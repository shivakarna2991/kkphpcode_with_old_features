function showUserMenu() {
    var firstName = readCookie("firstName");
    var userName = readCookie("userName");
    var lastName = readCookie("lastName");
    var userName = readCookie("userName");
    var userrole = readCookie("userrole");
    var jobnumber = readCookie("jobidentifer");

    document.getElementById('fnamelname').innerHTML = firstName + " " + lastName;
    var navbanner = document.getElementById("masthead");
    navbanner.style.display = "block";
}
function updateMenuLocation(navloc) {
    if (navloc == "jobmanager") {
        createCookie("navloc", "jobmanager", 0);
        document.getElementById('navlocation').innerHTML = "Manage Jobs"
    } else if (navloc == "jobsitemanager") {
        createCookie("navloc", "jobsitemanager", 0);
        document.getElementById('navlocation').innerHTML = "Manage Jobs > Jobsites"
    } else if (navloc == "accountmanagement") {
        createCookie("navloc", "accountmanagement", 0);
        document.getElementById('navlocation').innerHTML = "Manage Users"
    } else if (navloc == "devicemanagement") {
        createCookie("navloc", "devicemanagement", 0);
        document.getElementById('navlocation').innerHTML = "Manage Devices"
    } else if (navloc == "useraccount") {
        createCookie("navloc", "useraccount", 0);
        document.getElementById('navlocation').innerHTML = "Account Settings"
    } else if (navloc == "issuereporting") {
        createCookie("navloc", "issuereporting", 0);
        document.getElementById('navlocation').innerHTML = "Report Issue"
    } else if (navloc == "homeviewnew") {
        createCookie("navloc", "homeviewnew", 0);
        document.getElementById('navlocation').innerHTML = "New Home View"
    } else {
        createCookie("navloc", "homeview", 0);
        document.getElementById('navlocation').innerHTML = "Main Menu"
    }
}
function hideUserMenu() {
    document.getElementById('fnamelname').innerHTML = "Username";
    var navbanner = document.getElementById("masthead");
    navbanner.style.display = "none";
}
/* hide the currently open form
*/
function hideOpenForm() {
    var navloc = readCookie("navloc");
    if (navloc == "jobmanager") {
        $(".jobs-content").hide();
    } else if (navloc == "jobsitemanager") {
        $(".jobsites-content").hide();
    } else if (navloc == "accountmanagement") {
        $(".accounts-content").hide();
    } else if (navloc == "devicemanagement") {
        $(".devices-content").hide();
    } else if (navloc == "useraccount") {
        $(".user-content").hide();
    } else if (navloc == "issuereporting") {
        $(".issue-content").hide();
    } else if (navloc == "homeviewnew") {
        // It just uses the old home view placeholder.
        $(".home-content").hide();
    } else if (navloc == "homeview") {
        $(".home-content").hide();
    }
}
// check cookies for valid session, if expired session found, erase it from cookie store
function validSession() {
    // see if we have session variables
    var authToken = readCookie("authToken");
    if (authToken !== 'undefined' && authToken != "" && authToken != null) {
        return true;
    }
    return false;
}

function startSessionTimer() {
    // retrieve cookie expiration 
    var expiration = Date.parse(readCookie("expiration")) - Date.now();
    
    // set to logout and session expiration
    timerObject = setTimeout("userLogout()", expiration);
}
 
function showLoginForm() {    
    // load login form
    $("#loginTarget").load("htmlpages/loginform.htm", function() {
        // init password reset dialog once page has loaded
        initResetPasswordDialog();
        initResetMessageDialog();
        $("#username").on('input', resetErrorField);
        $("#password").on('input', resetErrorField);

        // display login form
        $(".login-content").animate({width:'toggle'},350);
    });
}

function loginRequired() {
    showErrorDialog("User session timed out, please re-login");
    logoutCleanup();
}

function logoutCleanup() {
    // delete all cookies for this session
    eraseCookie("userName");
    eraseCookie("userrole");
    eraseCookie("firstName");
    eraseCookie("lastName");
    eraseCookie("authToken");
    eraseCookie("expiration");
    eraseCookie("currentjobid");
    eraseCookie("currentjobname");
    eraseCookie("currentjobnickname");
    eraseCookie("currentjoboffice");
    eraseCookie("currentjobstudytype");
    eraseCookie("currentjobnumber");

    // clear sessionTimout timer
    clearTimeout(timerObject);

    // remove usermenu
    hideUserMenu();
    
    // hide currently open form
    hideOpenForm();
    
    // show login form
    showLoginForm();
}

function userLogout() {
    // call into to server to let it know the user logged out of this session
    var authToken = readCookie("authToken");
    var paramsString = METHODCALL_HEADER_PARAM_AUTHTOKEN + "=" + authToken;
    $.ajax({
        type: "GET",
        url: "MethodCall.php/AccountManager::Logout",
        data: paramsString,
        contentType: "application/json; charset=utf-8",
        dataType: "html",
        cache: false,
        success: function (result) {
        }
    });

    logoutCleanup();
}

function InitializeLoginSession (jsonResponse, uname, navToNewHome) {
    var userName = uname;
    var firstName = jsonResponse['results']['returnval']['firstname'];
    var lastName = jsonResponse['results']['returnval']['lastname'];
    var authToken = jsonResponse['results']['returnval']['token'];
    var expiration = jsonResponse['results']['returnval']['tokenvalidityperiod'];
    if (jsonResponse['results']['returnval']['role'] == '1') {
        var userrole = "user";
    } else if (jsonResponse['results']['returnval']['role'] == '2') {
        var userrole = "qcer";
    } else if (jsonResponse['results']['returnval']['role'] == '3') {
        var userrole = "designer";
    } else if (jsonResponse['results']['returnval']['role'] == '4') {
        var userrole = "projectmanager";
    } else {
        var userrole = "admin";
    }

    // in case we have leftover session cookies, remove them now
    eraseCookie("currentjobid");
    eraseCookie("currentjobname");
    eraseCookie("currentjobnickname");
    eraseCookie("currentjoboffice");
    eraseCookie("currentjobstudytype");
    eraseCookie("currentjobnumber");

    // save off cookies for active session
    createCookie("userName", userName, parseInt(expiration, 10));
    createCookie("firstName", firstName, parseInt(expiration, 10));
    createCookie("lastName", lastName, parseInt(expiration, 10));
    createCookie("userrole", userrole, parseInt(expiration, 10));
    createCookie("authToken", authToken, parseInt(expiration, 10));

    // determine expiration date and save as cookie
    var date = new Date();
    date.setTime(Date.now() + (parseInt(expiration, 10) * 60000));
    createCookie("expiration", date.toUTCString(), parseInt(expiration, 10));

    // close login/register dialog overlay
    $(".login-overlay").hide("fast");

    // show user navigation banner
    showUserMenu();

    // if not self, hide openform (in case returning from timed out session)
    if (readCookie("navloc") != "homeview") {
        hideOpenForm();
    }

    if (navToNewHome) {
        // Open the new home view.
        openNewHomeView();
    }
    else {
        // open home view
        openHomeView();
    }

    // start session timeout logout timer
    startSessionTimer();
}
