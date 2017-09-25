<?php
    header('Content-Type: text/plain; charset=utf-8');

    // include dbglog header detail
    require_once '/home/core/core.php';
    require_once '/home/core/jsonutils.php';
    require_once '/home/core/LocalMethodCall.php';
    require_once '/home/core/ValidateToken.php';
    
    DBG_ENTER(DBGZ_UPLOADFILE, "ingestDatafile: ".serialize($_POST));

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
  
    // validate the token, if not valid return error
    if ($response['results']['response'] == "success") {
        // get filedata contents from tmpfile
        $tmpfile = TEMP_FILE_FOLDER."/".$_POST['tmpfile'];
        if (file_exists($tmpfile)) {
            $handle = fopen($tmpfile, "r");
            $filecontents = fread($handle, filesize($tmpfile));
            fclose($handle);
            // remove file after reading for ingestion
            unlink($tmpfile);

            // build params array for debug logging
            $paramsArr = array('_mhdr_token' => $_POST['_mhdr_token'],
                                'jobid' => $_POST['jobid'],
                                'jobsiteid' => $_POST['jobsiteid'],
                                'replaceexisting' => 1,
                                'reverseprimary' => $_POST['reverseprimary'],
                                'ingestionkey' => $_POST['filename']
                             );
            DBG_INFO(DBGZ_UPLOADFILE, __METHOD__, "Calling LocalMethodCall.TubeJobSite::IngestData with params: ".serialize($paramsArr));

            // send file contents and params to ProjectManager::IngestData
            $response = LocalMethodCall(
                "TubeJobSite",
                "IngestData",
                $location,
                array_merge($paramsArr, array("filecontents" => $filecontents)),
                $_SERVER['HTTP_USER_AGENT']
            );
          
            if ($response['results']['response'] != "success") {
                $response_str = array("results" => array('response'  => 'failed',
                                                          'responder' => "uploadDatafiles",
                                                          'returnval' => array("resultstring" => "Unable to ingest ".$_POST['filename'])));
            } else {
                $response_str = array("results" => array('response'  => 'success',
                                                          'responder' => 'uploadDatafiles',
                                                          'returnval' => array("ingestionid" => $response['results']['returnval']['ingestionid'])));
            }
        } else {
                $response_str = array("results" => array('response'  => 'failed',
                                                          'responder' => "uploadDatafiles",
                                                          'returnval' => array("resultstring" => "File not found ".$tmpfile)));
        }

    } else {
        $response_str = $response;
    }

    echo json_encode(utf8json($response_str));
    DBG_RETURN(DBGZ_UPLOADFILE, "Completed ingestDatafile: ".serialize($response_str));
        
?>

