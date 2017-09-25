<?php 
    header('Content-Type: text/plain; charset=utf-8');
    require_once '/home/core/core.php';
    require_once '/home/core/jsonutils.php';
    require_once '/home/core/ValidateToken.php';

    DBG_ENTER(DBGZ_UPLOADFILE, "zipreports: ".serialize($_GET));

    // get ipaddress location of caller
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
        $location = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $location = $_SERVER['REMOTE_ADDR'];
    }

    $response = ValidateToken(
        $_GET['_mhdr_token'],
        $location
    );

    // validate the token, if not valid return error
    if ($response['results']['response'] == "success") {
        // get filenames
        $files = array();
        for ($i=0; $i<$_GET['numfiles']; $i++) {
            $filename = $_GET['file_'.$i];
            DBG_INFO(DBGZ_UPLOADFILE, __METHOD__, "extracting filename: ".$filename);
            $filepath = TEMP_FILE_FOLDER."/".$filename;
            if (file_exists($filepath)) {
                $files[] = $filepath;
            }
        }

        // if we have valid files
        if (count($files)) {

            // create zipfilename
            $zipfilename = $_GET['jobnumber']."_Reports_".date("Y-m-d", time()).".zip";

            //create the archive
            $zip = new ZipArchive();
            if($zip->open(TEMP_FILE_FOLDER."/$zipfilename", ZIPARCHIVE::CREATE) !== true) {
                $response_str = array("results" => array('response'  => 'failed',
                                                          'responder' => "zipreports",
                                                          'returnval' => array("resultstring" => "Unable to create zip archive: $zipfilename")));
            }
            //add the files
            foreach($files as $file) {
                $zip->addFile($file, pathinfo($file, PATHINFO_BASENAME));
            }
            //close the zip -- done!
            $zip->close();

            // remove original files !! important - only after closing the zipfile archive !!
            foreach($files as $file) {
                unlink($file);
            }
            
            //check to make sure the file exists
            if (file_exists(TEMP_FILE_FOLDER."/$zipfilename")) {
                $response_str = array("results" => array('response'  => 'success',
                                                          'responder' => "zipreports",
                                                          'returnval' => array("zipfilename" => $zipfilename)));
            }
        }
        else {
            $response_str = array("results" => array('response'  => 'failed',
                                                      'responder' => "zipreports",
                                                      'returnval' => array("resultstring" => "No reports to package found")));
        }
        echo json_encode(utf8json($response_str));
    } else {
        echo $response;
    }
    DBG_RETURN(DBGZ_UPLOADFILE, "Successfully zipped to file: $zipfilename");
?>


