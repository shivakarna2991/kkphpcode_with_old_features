/* userAccountsManagement form
*/
function openManageAccounts() {
    // call server to get list of active jobs
    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie("authToken");
    $.ajax({
        type: "GET",
        url: "MethodCall.php/AccountManager::GetUserAccounts",
        data: paramsString,
        contentType: "application/json; charset=utf-8",
        dataType: "html",
        cache: false,
        success: function (result) {
            var jsonResponse = JSON.parse(result);
            if (jsonResponse['results']['response'] == "success") {
                // load selectprojectform
                $("#accountsTarget").load("htmlpages/accountmanagerform.htm", function() {                                            
                    // load project values from server into newly loaded form
                    var acctsArr = jsonResponse['results']['returnval']['accounts'];
                    if (acctsArr.length) {
                        var jobstable = document.getElementById('acctstablerows');
                        for (i=0; i<acctsArr.length; i++) {
                            var accountid = acctsArr[i]['accountid'];
                            var name = acctsArr[i]['firstname'] + " " + acctsArr[i]['lastname'];
                            var email = acctsArr[i]['email'];
                            var state = acctsArr[i]['state'];
                            var role = acctsArr[i]['role'];
                            var rating = acctsArr[i]['rating'];
                            var created = acctsArr[i]['creationtime'];
                            var registered = acctsArr[i]['registeredtime'];
                            var lastlogin = acctsArr[i]['lastlogintime'];
                            
                            // populate each column in jobs table
                            var row = jobstable.insertRow(0);
                            var cell = row.insertCell(0);
                            cell.innerHTML = name;
                            cell.setAttribute("id", accountid+";NO");
                            cell = row.insertCell(1);
                            cell.innerHTML = email;
                            cell = row.insertCell(2);
                            cell.innerHTML = role;
                            cell = row.insertCell(3);
                            cell.innerHTML = rating;
                            cell = row.insertCell(4);
                            cell.innerHTML = state;
                            cell = row.insertCell(5);
                            if (typeof lastlogin !== 'undefined' && lastlogin != "") {
                                var datePart = lastlogin.split(" ",1);
                                if (datePart != '0000-00-00') {
                                    cell.innerHTML = datePart;
                                } else {
                                    cell.innerHTML = "-";
                                }
                            } else {
                                cell.innerHTML = "-";
                            }
                            cell = row.insertCell(6);
                            if (typeof created !== 'undefined' && created != "") {
                                var datePart = created.split(" ",1);
                                if (datePart != '0000-00-00') {
                                    cell.innerHTML = datePart;
                                } else {
                                    cell.innerHTML = "-";
                                }
                            } else {
                                cell.innerHTML = "-";
                            }
                            cell = row.insertCell(7);
                            if (typeof registered !== 'undefined' && registered != "") {
                                var datePart = registered.split(" ",1);
                                if (datePart != '0000-00-00') {
                                    cell.innerHTML = datePart;
                                } else {
                                    cell.innerHTML = "-";
                                }
                            } else {
                                cell.innerHTML = "-";
                            }
                        }
                    }
                    // set activate/deactivate/edit buttons to disables until a row is selected
                    document.getElementById('deactivatebtn').disabled = true;
                    document.getElementById('reactivatebtn').disabled = true;
                    document.getElementById('editaccountbtn').disabled = true;

                    // hide currently open form
                    hideOpenForm();                    
                    // initalize accountManager form functions
                    initAccountManagerForm();                    
                    // set current navigation position
                    updateMenuLocation("accountmanagement");
                    // display accountmanager form
                    $(".accounts-content").animate({width:'toggle'},350);
                });                
            }
            // else handle error for user 
            else {
                if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                    // close this form, inform user and logout
                    loginRequired();
                } else {
                    showErrorDialog("GetAccounts failed with:  "+ jsonResponse['results']['returnval']['resultstring']);
                }
            }
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });
}
