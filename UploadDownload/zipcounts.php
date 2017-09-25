<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
    header('Content-Type: text/plain; charset=utf-8');
    /*require_once '/home/core/core.php';    
    require_once '/home/core/jsonutils.php';
    require_once '/home/core/ValidateToken.php';*/
    //require_once '/core/core.php';    
    require_once '../core/core.php';
    require_once '../core/jsonutils.php';
    require_once '../core/ValidateToken.php';
//    require_once __DIR__.'/../core/core.php';
//    require_once __DIR__.'/../core/jsonutils.php';
//    require_once __DIR__.'/../core/ValidateToken.php';

    DBG_ENTER(DBGZ_UPLOADFILE, "zipcounts: ".serialize($_GET));

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
            $new_filename = str_replace(array("*", "/", "'", ":", "\"", "&", " "), "", $filename);
            DBG_INFO(DBGZ_UPLOADFILE, __METHOD__, "retrieving filename: ".$new_filename);
            $filepath = TEMP_FILE_FOLDER."/".$new_filename;
            if (file_exists($filepath)) {
                $files[] = $filepath;
            }
        }
        $zipfilename='';
        // if we have valid files
        if (count($files)) {
            echo $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);exit;
            // create zipfilename
            //$zipfilename = str_replace(array("*", ".", "/", "'", ":", "\"", "&"), "_", $_GET['packagename'])."_".date("Y-m-d", time()).".zip";
            //$zipfilename = str_replace(array("*", ".", "/", "'", ":", "\"", "&"), "_", $_GET['packagename'])."_".$filename.".zip";
            $zipfilename = str_replace(array("*", ".", "/", "'", ":", "\"", "&", " "), "", $withoutExt).".zip";
            //echo $zipfilename;exit;
            
            //create the archive
            $zip = new ZipArchive();
            if($zip->open(TEMP_FILE_FOLDER."/$zipfilename", ZIPARCHIVE::OVERWRITE) !== true) {
                //echo 'sdsdss';exit;
                $response_str = array("results" => array('response'  => 'failed',
                                                          'responder' => "zipcounts",
                                                          'returnval' => array("resultstring" => "Create zip archive failed for $zipfilename")));
            } else {
                //add the files
                //echo 'saasasasas';exit;
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
                    //echo 'sss';exit;
                    $response_str = array("results" => array('response'  => 'success',
                                                            'responder' => "zipcounts",
                                                            'returnval' => array("zipfilename" => $zipfilename)));
                } else {
                    //echo '1111sss';exit;
                    $response_str = array("results" => array('response'  => 'failed',
                                                            'responder' => "zipcounts",
                                                            'returnval' => array("resultstring" => "Failed to zip files for download")));
                }
            }
        }
        else {
            //echo '333sss';exit;
            $response_str = array("results" => array('response'  => 'failed',
                                                      'responder' => "zipcounts",
                                                      'returnval' => array("resultstring" => "No counts to package found")));
        }
        echo json_encode(utf8json($response_str));
    } else {
        echo $response;
    }        
    DBG_RETURN(DBGZ_UPLOADFILE, "Successfully zipped to file: $zipfilename");
?>
