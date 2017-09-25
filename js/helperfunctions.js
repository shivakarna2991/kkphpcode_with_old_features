/*
* convert css RGB color to hex format #xxxxxx (isn't setup for colors with transparency
*/
function hexc(colorval) {
    alert("colorval: " + colorval);
    var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    delete(parts[0]);
    for (var i = 1; i <= 3; ++i) {
        parts[i] = parseInt(parts[i], 10).toString(16);
        if (parts[i].length == 1) parts[i] = '0' + parts[i];
    }
    color = '#' + parts.join('');
}

/* 
* table sort functions
*/
function comparer(index) {
    return function(a, b) {
        var valA = getCellValue(a, index);
        var valB = getCellValue(b, index);
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.localeCompare(valB);
    };
}
function getCellValue(row, index) { 
    return $(row).children('td').eq(index).html();
}

/* input validations
*/
function validEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function validPassword(password) {
    var re = /^(?=\d*)(?=[a-z]*)(?=[A-Z]*).{8,20}$/;
    return re.test(password);
}
function validTime(time) {
    var result = false, m;
    var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
    if ((m = time.match(re))) {
        result = (m[1].length === 2 ? "" : "0") + m[1] + ":" + m[2];
    }
    return result;
}
function validDate(date) {
    var d = new Date(date);
    if (Object.prototype.toString.call(d) !== "[object Date]" ) {
        return false;
    }
    return !isNaN(d.getTime());
}
var mod = function (n, m) {
    var remain = n % m;
    return Math.floor(remain >= 0 ? remain : remain + m);
};

function sqlToJsDate(sqlDate){ 
    //sqlDate in SQL DATETIME format ("yyyy-mm-dd hh:mm:ss") 
    var sqlDateArr1 = sqlDate.split("-"); 
    //format of sqlDateArr1[] = ['yyyy','mm','dd hh:mm:ss'] 
    var sYear = sqlDateArr1[0]; 
    var sMonth = (Number(sqlDateArr1[1]) - 1).toString(); 
    var sqlDateArr2 = sqlDateArr1[2].split(" "); 
    //format of sqlDateArr2[] = ['dd', 'hh:mm:ss'] 
    var sDay = sqlDateArr2[0]; 
    var sqlDateArr3 = sqlDateArr2[1].split(":"); 
    //format of sqlDateArr3[] = ['hh','mm','ss'] 
    var sHour = sqlDateArr3[0]; 
    var sMinute = sqlDateArr3[1]; 
    var sSecond = sqlDateArr3[2]; 
    return new Date(sYear,sMonth,sDay,sHour,sMinute,sSecond); 
} 


/* time format 0 padding */
function str_pad_left(string,pad,length) {
    return (new Array(length+1).join(pad)+string).slice(-length);
}

/* number formatting with commas
*/
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

/*
* error dialog functions
*/
function initErrorDialog() {
    $("#error-dialog").dialog({
        resizable: false,
        dialogClass: "no-close",
        autoOpen: false,
        modal: true
    });
}
function showErrorDialog(errormsg) {
    document.getElementById("errorlabel").innerHTML = errormsg;
    $("#error-dialog").dialog("open");
}
function cancelErrorDialog() {
    // close dialog
    $("#error-dialog").dialog("close");
    return true;
}
/*
* function validates a set of selected files as having the appropriate title format required for 
* multiple-file select, merge and upload operations, upon success it returns a sorted list of files
*/
function validVideoFileSet(files) {
    if (files.length) {
        var prefixname = "";
        var filenos = [];
        for (var i=0; i<files.length; i++) {
            var file = files[i];
            var fileparts = file.name.split(".");
            var fileno = parseInt(fileparts[fileparts.length-2], 10);
            var dateparts = fileparts[0].split("_");
            // create prefix portion
            var prefix = "";
            for (var j=0; j<dateparts.length-2; j++) {
                prefix = prefix + dateparts[j];
            }
            if (prefixname === "") {
                prefixname = prefix;
            } else if (prefix != prefixname) {
                // multiple prefixes not allowed in multi-select
                return false;
            }
            var datepart = dateparts[dateparts.length-2];
            var datepart1 = dateparts[dateparts.length-1];
            var timepart = datepart1.substring(0, 2) + ":" + datepart1.substring(2, 4);
            if (!validDate(datepart) || !validTime(timepart)) {
                // not a valid date & time suffix
                return false;
            }

            // Track the file indexes.  Fail if the index is already in the array.
            if (filenos.indexOf(fileno) == -1) {
                filenos.push(fileno);
            } else {
                return false;
            }
        }
    } else {
        // no valid files
        return false;
    }
    // make sure the file indexes are contiguous.  Fail if there are any gaps.
    filenos.sort(function(a, b){return a-b;});

    var index = filenos[0];
    for (i=1; i<filenos.length; i++) {
        if (filenos[i] != (index + 1)) {
            return false;
        }
        index = filenos[i];
    }

    // sort the files by title descending
    var sortedFiles = [].slice.call(files); // convert to sortable array
    sortedFiles.sort(function(a,b) {
        if ( a.name < b.name )
            return -1;
        if ( a.name > b.name )
            return 1;
        return 0;
    });
    return sortedFiles;
}
/* test routine for executing tests from the menu
*/
function testFunction() {
    $.ajax({
        type: "GET",
        url: "exectest.php",
        dataType: "html",
        cache: false,
        success: function(result) {
            showErrorDialog("success result: " + result);
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });

}