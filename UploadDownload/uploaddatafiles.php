<?php
    /* function upload one or more file to the temporary upload folder assigning the files temporary names
     * the function returns with a list of original & temporary filename pairs
     */
    header('Content-Type: text/plain; charset=utf-8');

    // include dbglog header detail
    require_once '/home/core/core.php';
    require_once '/home/core/jsonutils.php';
    require_once '/home/core/ValidateToken.php';

    // define("dbg_zones",  DBGZ_UPLOADFILE);
    // define("dbg_levels", DBGL_TRACE | DBGL_INFO | DBGL_WARN | DBGL_ERR | DBGL_EXCEPTION);
    // define("dbg_dest",   dbg_dest_log);

	// DBG_SET_PARAMS(dbg_zones, dbg_levels, FALSE, FALSE, dbg_dest, dbg_file);

    DBG_ENTER(DBGZ_UPLOADFILE, "uploaddatafiles", serialize($_POST));

    // get ipaddress location of caller
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
        $location = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $location = $_SERVER['REMOTE_ADDR'];
    }

    $response = ValidateToken(
        $_POST['_mhdr_token'],
        $location
    );
  
    $tmpfileslist = "";

    // validate the token, if not valid return error
    if ($response['results']['response'] == "success") {
        foreach ($_FILES["upfile"]["error"] as $key => $error) {
            try {
                // Check $_FILES['upfile']['error'] value.
                switch ($error) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        throw new RuntimeException('No valid files specified.');
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new RuntimeException('Exceeded 20MB filesize limit.');
                    default:
                        throw new RuntimeException('Unknown errors.');
                }

                // setup temporary upload filename
                $tmpfilename = basename($_FILES['upfile']['tmp_name'][$key]).'.txt';
                $tmpfile = TEMP_FILE_FOLDER.'/'.$tmpfilename;
                $filesize = $_FILES['upfile']['size'][$key];
                
                DBG_INFO(DBGZ_UPLOADFILE, __METHOD__, "uploading temp file: $tmpfile, with size: $filesize bytes");
                
                if (!move_uploaded_file($_FILES['upfile']['tmp_name'][$key], $tmpfile)) {
                    throw new RuntimeException('Failed to retrieve file: '.$tmpfilename.'.');
                }
                // add tmpfilename & original filename to returned array
                $filename = basename($_FILES['upfile']['name'][$key]);
                $tmpfileslist = $tmpfileslist . ':tmpfile*-*' . $tmpfilename . '*-*filename*-*' . $filename;

            } catch (RuntimeException $e) {
                echo 'failed with error: '.$e->getMessage();
                DBG_RETURN(DBGZ_UPLOADFILE, 'failed with error: '.$e->getMessage());
                exit;
            }        
        }

        // particularly formatted return string because this is a return from an ajax call formatted with enctype: multipart/form-data
        //   yields a string "success:tmpfile*-*value*-*filename*-*value:tmpfile*-*value*-*filename*-*value:...
        echo "success$tmpfileslist";
    } else {
        echo "failed:".$response['results']['returnval'];
    }

    DBG_RETURN(DBGZ_UPLOADFILE, "Success: $tmpfileslist");
?>

