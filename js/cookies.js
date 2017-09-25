function createCookie(name, value, minutes) {
    var expires;
    if (minutes) {
        var date = new Date();
        date.setTime(Date.now() + (minutes * 60000));
        expires = "; expires=" + date.toUTCString();
    } else {
        expires = "; expires=Fri, 31 Dec 9999 23:59:59 GMT";
    }
    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = escape(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1, c.length);
        }
        if (c.indexOf(nameEQ) === 0) {
            return unescape(c.substring(nameEQ.length, c.length));
        }
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

function cookiesEnabled() { 
    var cookieEnabled = (navigator.cookieEnabled) ? true : false;
    
    //if not IE4+ nor NS6+
    if (typeof navigator.cookieEnabled === "undefined" && !cookieEnabled){
        document.cookie = "testcookie";
        cookieEnabled = (document.cookie.indexOf("testcookie") != -1) ? true : false;
    }
    return cookieEnabled;
}
