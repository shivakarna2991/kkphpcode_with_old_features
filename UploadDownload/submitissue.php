<?php
    header('Content-Type: text/plain; charset=utf-8');
    ini_set('max_execution_time', 300);

    // include dbglog header detail
    require_once '/home/core/core.php';
    require_once '/home/core/jsonutils.php';
    require_once '/home/core/LocalMethodCall.php';
    require_once '/home/core/ValidateToken.php';
    
    DBG_ENTER(DBGZ_UPLOADFILE, "Started submitIssueReport: ".serialize($_POST));

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
    
        $paramsArr = array(
                    '_mhdr_token'  => $_POST['_mhdr_token'],
                    'title'        => $_POST['title'],
                    'description'  => $_POST['description'],
                    'reprosteps'   => $_POST['reprosteps'],
                    'priority'     => $_POST['priority'],
                    'type'         => $_POST['type']
                     );

        DBG_INFO(DBGZ_UPLOADFILE, "submitissue.php", "Calling LocalMethodCall.IssueManager::CreateIssue with params: ".serialize($paramsArr));
        // submit issue to IssueManager::CreateIssue
        $response = LocalMethodCall(
            "IssueManager",
            "CreateIssue",
            $location,
            $paramsArr,
            $_SERVER['HTTP_USER_AGENT']
        );

        if ($response['results']['response'] == "success") {
            $issueid = $response['results']['returnval']['issueid'];
        
            $index = 0;
            // loop thru all attachments indexed from 0..n
            while (isset($_POST['tmpfile_'.$index])) {
                $tmpfile = TEMP_FILE_FOLDER."/".$_POST['tmpfile_'.$index];
                $filename = $_POST['filename_'.$index];

                DBG_INFO(DBGZ_UPLOADFILE, "submitissue.php", "Getting tmpfile contents: $tmpfile");
                // get filedata contents from tmpfile
                if (file_exists($tmpfile)) {
                    $handle = fopen($tmpfile, "r");
                    $filecontents = fread($handle, filesize($tmpfile));
                    fclose($handle);
                    // remove file after reading for ingestion
                    unlink($tmpfile);

                    $paramsArr = array(
                                '_mhdr_token'      => $_POST['_mhdr_token'],
                                'issueid'          => $issueid,
                                'filecontents'     => base64_encode($filecontents),
                                'filename'         => $filename
                                );

                    DBG_INFO(DBGZ_UPLOADFILE, "submitissue.php", "Calling LocalMethodCall.IssueManager::AddAttachment with issueid=$issueid, filename=$filename");
                    // attach the filestream to the issue
                    $uploadResponse = LocalMethodCall(
                        "IssueManager",
                        "AddAttachment",
                        $location,
                        $paramsArr,
                        $_SERVER['HTTP_USER_AGENT']
                    );

                    // increment index to get next attachment
                    $index++;
                }
            }
        }
        // failed to createIssue 
        else {
            $response = array("results" => array('response'  => 'failed',
                                                  'responder' => "submitissue",
                                                  'returnval' => array("resultstring" => "Unable to CreateIssue")));
        }
    }
    echo json_encode(utf8json($response));
    DBG_RETURN(DBGZ_UPLOADFILE, "Completed submitIssueReport: ".serialize($response));
        
?>

