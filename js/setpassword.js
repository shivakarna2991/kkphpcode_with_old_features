function validateChangePassword() {
    var errorfield = document.getElementById('setpassword-errorfield');
    var newPword = document.getElementById('newpassword').value;
    var newPword2 = document.getElementById('newpassword2').value;
    
    // validate newpasswords match
    if (newPword != newPword2) {
        errorfield.style.color = "#b5535f";
        errorfield.innerHTML = "New passwords entered do not match";
        return false;
    }
    
    // validate new password formats
    if (!validPassword(newPword) || !validPassword(newPword2)) {
        errorfield.style.color = "#b5535f";
        errorfield.innerHTML = "Invalid format, 8+ chars, 1 number and 1 uppercase requried";
        return false;
    }
    
    // register new password
    // valid inputs, proceed with call to server
    var paramsString = "&urlkey=" + urlkey + "&password=" + newPword;
    $.ajax({
        type: "GET",
        url: "../MethodCall.php/AccountManager::ExecuteURLSetPassword",
        data: paramsString,
        dataType: "html",
        cache: false,
        success: function(result) {
            var jsonResponse = JSON.parse(result);
            if (jsonResponse['results']['response'] == "success") {
                // hide this form and put up usermessage dialog
                $("#setpass-setpassword").hide();
                openUserMessageDialog();
            }
            // else handle error for user 
            else {
                errorfield.style.color = "#b5535f";
                errorfield.innerHTML = "Error changing password: " + jsonResponse['results']['returnval']['resultstring'];
            }
        },
        error: function (request, status, error) {
            showErrorDialog("Server error: " + status);
        }
    });            
}
/* initialize placeholder jquery dialog 
*/
function initUserMessageDialog() {
    $("#dialog-usermessage").dialog({
        resizable: false,
        dialogClass: "no-close",
        autoOpen: false,
        show: 'fade',
        modal: true
    });
}
/* open changePassword dialog
*/
function openUserMessageDialog() {        
    // show message dialog
    $("#dialog-usermessage").dialog("open");
}
/* cancel changePassword dialog
*/
function cancelUserMessageDialog() {
    // close dialog
    $("#dialog-usermessage").dialog("close");
    window.location.href = "http://localhost";
}

