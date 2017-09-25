<?php
    require_once 'core/core.php';
    require_once 'core/LocalMethodCall.php';

    // function redirects visitor to fullURL 
    function redirectTo($response, $urlkey) {
        $server = "http://52.42.214.56";

        // change this to your domain
        header("Referer: $server");

        switch ($response) {
            case "validate_email":
                // use a 301 redirect to your destination
                header("Location: $server/redirpages/validated.html", TRUE, 301);
            break;
        
            case "account_registration":
            case "reset_password":
                // use a 301 redirect to your destination
                header("Location: $server/redirpages/register.php?urlkey=$urlkey", TRUE, 301);
            break;

            default:
                // response not recognized, show 404 page
                show404();
            break;
        }
    }

    function show404() {
        // display/include your standard 404 page here
        echo "The url page does not exist";
    }

    function getResponseFromUrlkey($urlkey) {
        // get ipaddress location of caller
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
            $location = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $location = $_SERVER['REMOTE_ADDR'];
        }

        $response_str = null;
        // send file contents and params to ProjectManager::IngestData
        $response = LocalMethodCall(
            "AccountManager",
            "ExecuteURL",
            $location,
            array('urlkey' => $urlkey),
            $_SERVER['HTTP_USER_AGENT']
        );

        if ($response['results']['response'] == "success") {
            $response_str = $response['results']['returnval']['type'];
        }

        DBG_INFO(DBGZ_URLKEYDIRECT, "urlkeyredirect.php", "response from AccountManager::ExecuteURL: ".serialize($response));
        return $response_str;
    }

    define("dbg_zones",  DBGZ_CORE | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTLINKMGR | DBGZ_URLKEYDIRECT);
    define("dbg_levels", DBGL_TRACE | DBGL_INFO | DBGL_WARN | DBGL_ERR | DBGL_EXCEPTION);
    define("dbg_dest",   dbg_dest_log);

    DBG_SET_PARAMS(dbg_zones, dbg_levels, FALSE, FALSE, dbg_dest, dbg_file);

    DBG_ENTER(DBGZ_URLKEYDIRECT, "urlkeydirect: ".$_SERVER['REDIRECT_URL']);

    // extract urlkey
    $expectedUrlkey = trim($_SERVER['REDIRECT_URL']);
    // security: strip all but alphanumerics & dashes - the urlkey should conatin only valid hexadecimal chars
    $urlkey = preg_replace("/[^a-z0-9-]+/i", "", $expectedUrlkey);

    // retrieve fullURL from shortURL
    $response = getResponseFromUrlkey($urlkey);

    if (isset($response) && $response != null) {
        redirectTo($response, $urlkey);
        DBG_INFO(DBGZ_URLKEYDIRECT, "urlkeyredirect.php", "Response found, redirect successful");
    } else {
        show404();  // no shortURL found, display standard 404 page
        DBG_INFO(DBGZ_URLKEYDIRECT, "urlkeyredirect.php", "Redirecting to 404");
    }

    DBG_RETURN(DBGZ_URLKEYDIRECT, "Urlkey redirect completed.");
    exit;
?>