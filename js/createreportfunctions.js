/* functions to help with the back and forth in upload/ingest/create reports/download reports process
 *  this is a sequential, recusrive processing triggered by asynchronous ajax callback functions
 *  the recursive function calls rely on parameters pushed onto a stack then popped by the function when needed
 */

/* global function params arrays */
var ingestionParams = [];
var reportParams = [];
var downloadParams = [];
var processStart;
var uploadtime;
var readtime;
var createtime;
var combinedfilesize;
var speedreports; 
var classreports; 
var volumereports; 
var numreporttypes;
var numdays;
var numreports;
var numfiles;
var updateinterval;
var progressTimer;
var totalsegments;
var currentsegment;

/* progressbar function advances the bar from whereever it is forward
 *  incrementally in a somewhat random step amount until it has reached
 *  the pb_endpoint variable 
 */
function advanceProgress() {
    var val = $("#progressbar").progressbar("value") || 0;

    // advance a small random increment at a time
    var newval = val + Math.floor(Math.random() * 3);
    $("#progressbar").progressbar("value", newval);

    // advance until we reach the current segment end
    if (newval <= Math.round((currentsegment/totalsegments)*99)) {
        progressTimer = setTimeout(advanceProgress, updateinterval);
    }
}

/* set the ux for report generation
 *  kick off the process
 */
function generateSelectedReports() {
    processStart = new Date();

    // initialize global params
    speedreports = 0; 
    classreports = 0; 
    volumereports = 0; 
    numreporttypes = 0;
    combinedfilesize = 0;
    speedreports = 0;
    volumereports = 0;
    classreports = 0;

    // determine the progress bar metrics
    numfiles = jobFiles.length;
    for (var i=0; i<numfiles; i++) {
        combinedfilesize += jobFiles[i].size;
    }
    if (document.getElementById('volume').checked) {
        volumereports = 35;
        numreporttypes ++;
    }
    if (document.getElementById('speed').checked) {
        speedreports = 110;
        numreporttypes ++;
    }
    if (document.getElementById('class').checked) {
        classreports = 94;
        numreporttypes ++;
    }
    numreports = numreporttypes * numfiles;
    var start = new Date($('#startdate').val());
    var end = new Date($('#enddate').val());
    numdays = 1 + Math.abs(end-start)/86400000; // divide by # of ms in a day to get days, + 1 because it is an inclusive range

    // estimate processing time (1 + to ensure fractions are at least 1s of progressbar)
    // assumes 1s/5MB of filesize
    uploadtime = 1 + Math.round(combinedfilesize/5000000); 
    // assumes 1s/0.2MB of filesize
    readtime = 1 + Math.round(combinedfilesize/200000); 
    // numdays in each report, report types inluded, input file size - tuning this formula iteratively, not scientifically - sigh
    createtime = 1 + Math.round((numdays + numdays/3)*((speedreports+classreports+volumereports)/23)*combinedfilesize/4000000); 

    // set values in processing dialog
    $("#numfiles").text(numfiles);
    $("#numreports").text(numreports);
    var totaltime = uploadtime + readtime + createtime;
    var seconds = Math.round(totaltime % 60);
    totaltime = Math.floor(totaltime / 60);
    var minutes = Math.round(totaltime % 60);
    $("#estimatedtime").text(str_pad_left(minutes,'0',2)+':'+str_pad_left(seconds,'0',2));

    // set progressbar segment count, for making adjustments along the way
    totalsegments = 1 + (2 * numfiles);

    // open the processingdata dialog to show user what's happening
    openProcessingDataDialog();
    // start the createReportProcess
    uploadFiles();
}

/* upload all files at once 
 *  return tmpfile names and add to ingestionParams
 *  call ingestDatafiles when done
 */
function uploadFiles() {
    // reset params queues
    while(ingestionParams.length > 0) {
        ingestionParams.pop();
    }
    while(reportParams.length > 0) {
        reportParams.pop();
    }
    while(downloadParams.length > 0) {
        downloadParams.pop();
    }

    // initialize and start progressbar
    $("#progressbar").progressbar("value", 0);
    currentsegment = 1;
    updateinterval = uploadtime * 50;
    advanceProgress();

    // call ajax function to upload all datafiles at once
    var dataparams = new FormData();
    for (var i=0; i<jobFiles.length; i++) {
        dataparams.append ('upfile[]', jobFiles[i]);
    }
    // append authorization token
    dataparams.append(METHODCALL_HEADER_PARAM_AUTHTOKEN, readCookie('authToken'));
    dataparams.append('numfiles', jobFiles.length);

    // post files to server
    $.ajax({
        type: "POST",
        url: "UploadDownload/uploaddatafiles.php",
        data: dataparams,
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        success: function (result) {
            // parse the return string (JSON not available for return with this multipart/form-data call)
            var successRes = result.split(":");
            if (successRes[0] == "success") {
                for (var i=1; i<successRes.length; i++) {
                    var filenames = successRes[i].split("*-*");
                    ingestionParams.push({tmpfile: filenames[1].trim(), filename: filenames[3].trim()});
                }
                // show completion for upload
                $("#uploadingfiles").text("..Completed");
                // clear timer on progressbar, let next function restart it
                clearTimeout(progressTimer);
                // start ingesting the datafiles
                ingestDatafile();
            }
            // else handle error for user 
            else {
                if (successRes[1] == "login required") {
                    // close this form
                    closeProcessingDataDialog();
                    // inform user and logout
                    loginRequired();
                } else {
                    $("#processingdata").text("Error, uploadFiles returned with:<br />"+successRes[1]);
                    $("#doneProcessingBtn").show();
                }
            }
        },
        error: function (request, status, error) {
            $("#processingdata").text("Server error: " + status);
            $("#doneProcessingBtn").show();
        }
    });
}

/* ingest a datafile for each ingestionParam set
 *  recursively call self until ingestionParams exhausted
 *  return ingestionIds, add to reportParams
 *  call createReports when done
 */
function ingestDatafile() {
    // pop ingestion params from stack, if any, otherwise just exit
    if (ingestionParams.length) {
        var ingestionParam = ingestionParams.pop();
        var tmpFile = ingestionParam.tmpfile;
        var fileName = ingestionParam.filename;

        // if first calling ingestDataFile
        if (!$("#reading").is(":visible")) {
            // show reading files 
            $("#reading").show();

            // set segment position, interval and currentsegment for progressbar
            $("#progressbar").progressbar("value", Math.round((currentsegment/totalsegments)*99));
            updateinterval = (readtime/numfiles) * 60;
            currentsegment ++;
        } else {
            // set segment position and currentsegment for progressbar
            $("#progressbar").progressbar("value", Math.round((currentsegment/totalsegments)*99));
            currentsegment ++;
        }
        // advance progressbar 
        advanceProgress();
        // update with current file
        $("#readingfiles").text(fileName);

        var authToken = readCookie('authToken');

        // get relevant params for the current ingestion by matching filename with selected rows
        var jobsitestable = document.getElementById('jobsitestablerows');
        for (var j = 0, row; row = jobsitestable.rows[j]; j++) {
            var cell = row.cells[0];
            var cellid = cell.getAttribute("id");
            var issel = cell.getAttribute("data-selected");
            var file = row.cells[5].innerText;
            // found a match, selected row with matching filename
            if (issel == "true" && fileName == file) {
                var jobsitestr = "&jobsiteid=" + cellid;
                var revstr;
                if (row.cells[4].getElementsByTagName('input')[0].checked === true) {
                    revstr =  "&reverseprimary=1";
                } else {
                    revstr =  "&reverseprimary=0";
                }
                break;
            }
        }

        var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken + "&tmpfile=" + tmpFile + "&filename=" + fileName + jobsitestr + revstr;

        // call ajax funtion to ingestdata
        $.ajax({
            type: "POST",
            url: "UploadDownload/ingestdatafile.php",
            data: paramsString,
            dataType: "html",
            cache: false,
            success: function (result) {
                jsonResponse = JSON.parse(result);
                var response = jsonResponse['results']['response'];
                if (response == "success") {
                    // get ingestionid returned and add to reportParams
                    reportParams.push({ingestionid: jsonResponse['results']['returnval']['ingestionid'], filename: fileName});

                    // if more ingestions to do
                    if (ingestionParams.length) {
                        ingestDatafile();
                    }
                    // else call createReports
                    else {
                        // show completion for reading files
                        $("#readingfiles").text("..Completed");
                        // clear timer on progressbar, let next function restart it
                        clearTimeout(progressTimer);
                        // start creating reports
                        createReports();
                    }
                }
                // else handle error for user 
                else {
                    if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                        // close this form
                        closeProcessingDataDialog();
                        // inform user and logout
                        loginRequired();
                    } else {
                        $("#processingdata").text("Error, ingestDatafile returned with:<br />"+jsonResponse['results']['returnval']['resultstring']);
                        $("#doneProcessingBtn").show();
                    }
                }
            },
            error: function (request, status, error) {
                $("#processingdata").text("Server error: " + status);
                $("#doneProcessingBtn").show();
            }
        });
    } else {
        // nothing to do, set error message and add done button on processingdata dialog
        $("#processingdata").text("Error during Ingestion, no datafiles to ingest");
        $("#doneProcessingBtn").show();
    }
}
/* create a report set for each reportParam set
 *  recursively call self until reportParams exhausted
 *  return report filenames, add to downloadParams
 *  call downloadReports when done
 */
function createReports() {
    // pop ingestion params from stack, if any, otherwise just exit
    if (reportParams.length) {
        var reportParam = reportParams.pop();

        // if creating first report
        if (!$("#creating").is(":visible")) {
            // show creating reports
            $("#creating").show();

            // set segment position, interval and currentsegment for progressbar 
            $("#progressbar").progressbar("value", Math.round((currentsegment/totalsegments)*99));
            updateinterval = (createtime/numfiles) * 80;
            currentsegment ++;
        } else {
            // set segment position and currentsegment for progressbar
            $("#progressbar").progressbar("value", Math.round((currentsegment/totalsegments)*99));
            currentsegment ++;
        }
        // advance progressbar 
        advanceProgress();
        // update with current file
        $("#creatingreports").text(reportParam.filename);

        var ingestionId = reportParam.ingestionid;
        var startDate = $('#startdate').val();
        var endDate = $('#enddate').val();
        var cnt = 0;

        var reportTypes = "";
        if (document.getElementById('volume').checked) {
            reportTypes = reportTypes + '&type'+cnt+'=volume';
            cnt ++;
        }
        if (document.getElementById('speed').checked) {
            reportTypes = reportTypes + '&type'+cnt+'=speed';
            cnt ++;
        }
        if (document.getElementById('class').checked) {
            reportTypes = reportTypes + '&type'+cnt+'=class';
        }

        var reportFormatDropdown = document.getElementById("reports-reportformats-dropdown");
        var reportFormat = "&reportformat=" + reportFormatDropdown.options[reportFormatDropdown.selectedIndex].innerHTML;

        reportParameters = GetReportParameters("reports");

        if (reportParameters !== "")
        {
            reportParameters = "&reportparameters=" + encodeURIComponent(reportParameters);
        }

        // call ajax function to createreports
        var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie('authToken') + "&ingestionid=" + ingestionId + "&startdate=" + startDate + "&enddate=" + endDate + reportTypes + reportFormat + reportParameters;
        $.ajax({
            type: "GET",
            url: "MethodCall.php/TubeJobSite::CreateReports",
            data: paramsString,
            dataType: "html",
            cache: false,
            success: function(result) {
                jsonResponse = JSON.parse(result);
                var response = jsonResponse['results']['response'];
                if (response == "success") {
                    // get report types & filenames and add to downloadParams
                    if ("volume" in jsonResponse['results']['returnval']['outputFiles']) {
                        if ("xls" in jsonResponse['results']['returnval']['outputFiles']['volume']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['volume']['xls']});
                        }
                        if ("pdf" in jsonResponse['results']['returnval']['outputFiles']['volume']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['volume']['pdf']});
                        }
                    }
                    if ("class" in jsonResponse['results']['returnval']['outputFiles']) {
                        if ("xls" in jsonResponse['results']['returnval']['outputFiles']['class']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['class']['xls']});
                        }
                        if ("pdf" in jsonResponse['results']['returnval']['outputFiles']['class']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['class']['pdf']});
                        }
                    }
                    if ("speed" in jsonResponse['results']['returnval']['outputFiles']) {
                        if ("xls" in jsonResponse['results']['returnval']['outputFiles']['speed']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['speed']['xls']});
                        }
                        if ("pdf" in jsonResponse['results']['returnval']['outputFiles']['speed']) {
                            downloadParams.push({filename: jsonResponse['results']['returnval']['outputFiles']['speed']['pdf']});
                        }
                    }
                    // if more reports to create
                    if (reportParams.length) {
                        // call createReports
                        createReports();
                    }
                    // else call downloadReports
                    else {
                        // show create completion
                        $("#creatingreports").text("..Completed");
                        // clear timer on progressbar, let next function restart it
                        clearTimeout(progressTimer);
                        // start downloading
                        packageReports();
                    }
                }
                // else handle error for user 
                else {
                    if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                        // close this form
                        closeProcessingDataDialog();
                        // inform user and logout
                        loginRequired();
                    } else {
                        $("#processingdata").text("Error, createReports returned with:<br />"+jsonResponse['results']['returnval']['resultstring']);
                        $("#doneProcessingBtn").show();
                    }
                }
            },
            error: function (request, status, error) {
                $("#processingdata").text("Server error: " + status);
                $("#doneProcessingBtn").show();
            }
        });
    } 
    else {
        // nothing to do, set error message and add done button on processingdata dialog
        $("#processingdata").text("Error in createReports, no ingestions available");
        $("#doneProcessingBtn").show();
    }
}
/* package up the reports into a zip file then call downloadReports
*/
function packageReports () {
    // show downloading reports
    $("#downloading").show();

    // set segment position, interval and currentsegment for progressbar 
    $("#progressbar").progressbar("value", Math.round((currentsegment/totalsegments)*99));
    updateinterval = 50;
    currentsegment ++;
    // advance progressbar 
    advanceProgress();

    // pop download param filenames into paramsString for packager
    if (downloadParams.length) {
        var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie('authToken') + "&numfiles=" + downloadParams.length + "&jobnumber=" + readCookie('currentjobnumber');
        for (var i=0; i<downloadParams.length; i++) {
            paramsString = paramsString + "&file_"+i+"=" + downloadParams[i].filename;
        }

        // call ajax function to zipreports
        $.ajax({
            type: "GET",
            url: "UploadDownload/zipreports.php",
            data: paramsString,
            dataType: "html",
            cache: false,
            success: function(result) {
                jsonResponse = JSON.parse(result);
                var response = jsonResponse['results']['response'];
                if (response == "success") {
                    // download reports
                    downloadReports(jsonResponse['results']['returnval']['zipfilename']);
                }
                // else handle error for user 
                else {
                    if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                        // close this form
                        closeProcessingDataDialog();
                        // inform user and logout
                        loginRequired();
                    } else {
                        $("#processingdata").text("Error, packageReports returned with:<br />"+jsonResponse['results']['returnval']['resultstring']);
                        $("#doneProcessingBtn").show();
                    }
                }
            },
            error: function (request, status, error) {
                // nothing to do, set error message and add done button on processingdata dialog
                $("#processingdata").text("Server error: " + status);
                $("#doneProcessingBtn").show();
            }
        }); 
    }
    else {
        // nothing to do, set error message and add done button on processingdata dialog
        $("#processingdata").text("Error preparing download, no reports to package");
        $("#doneProcessingBtn").show();
    }
}
/* download all the reportsets at one time
 */
function downloadReports(zipfile) {
    // call server to download the file
    window.location.href = 'UploadDownload/downloadreports.php?file='+zipfile + "&" + METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie('authToken');

    var processEnd = new Date();
    var elapsedTime = processEnd - processStart;
    // remove milliseconds
    elapsedTime /= 1000;
    // get seconds
    var seconds = Math.round(elapsedTime % 60);
    // remove seconds 
    elapsedTime = Math.floor(elapsedTime / 60);
    // get minutes
    var minutes = Math.round(elapsedTime % 60);
    
    $("#downloadingfiles").text("..Completed");
    $("#reportsduration").text("Finished downloading reports, actual duration: "+str_pad_left(minutes,'0',2)+':'+str_pad_left(seconds,'0',2));
    $("#completedduration").show();
    $("#doneProcessingBtn").show();
}
