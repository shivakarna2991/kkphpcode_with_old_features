/* userAccountsManagement form
*/
/* current user settings */
var cuFname = "";
var cuLname = "";
var cuEmail = "";

function openUserSettings() {
    // call server to get current user settings
    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + readCookie("authToken");
    $.ajax({
        type: "GET",
        url: "MethodCall.php/AccountManager::GetAccount",
        data: paramsString,
        contentType: "application/json; charset=utf-8",
        dataType: "html",
        cache: false,
        success: function (result) {
            var jsonResponse = JSON.parse(result);
            if (jsonResponse['results']['response'] == "success") {
                // load selectprojectform
                $("#userTarget").load("htmlpages/usersettingsform.htm", function() {                                            
                    // load project values from server into newly loaded form
                    cuFname = jsonResponse['results']['returnval']['account']['firstname'];
                    cuLname = jsonResponse['results']['returnval']['account']['lastname'];
                    cuEmail = jsonResponse['results']['returnval']['account']['email'];

                    // load current values into form
                    document.getElementById('userfirstname').value = cuFname;
                    document.getElementById('userlastname').value = cuLname;
                    document.getElementById('useremail').value = cuEmail;
  
                    // set activate/deactivate/edit buttons to disables until a row is selected
                    document.getElementById('savesettingsbtn').disabled = true;

                    // set change event for input fields to enable/disable save button as appropriate
                    $("#userfirstname").on('input', setSaveSettingsState);
                    $("#userlastname").on('input', setSaveSettingsState);
                    $("#useremail").on('input', setSaveSettingsState);

                    // hide currently open form
                    hideOpenForm();                    
                    // initalize accountManager form functions
                    initUserSettingsForm();                    
                    // set current navigation position
                    updateMenuLocation("useraccount");
                    // display accountmanager form
                    $(".user-content").animate({width:'toggle'},350);
                });                
            }
            // else handle error for user 
            else {
                if (jsonResponse['results']['returnval']['resultstring'] == "login required") {
                    // close this form, inform user and logout
                    loginRequired();
                } else {
                    showErrorDialog("GetUserSettings failed with:  "+ jsonResponse['results']['returnval']['resultstring']);
                }
            }
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });
}
