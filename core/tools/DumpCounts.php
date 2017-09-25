<?php

    require_once '/home/idax/DBParams.php';
    
    $layoutids = array();
    // Retrieve command line parameters
    $params = getopt("v:l:");
    // layoutid or videoid required, if videoid provided layoutid is ignored and all layouts for the specified video are gathered
    if (array_key_exists("v", $params)) {
        $videoid = $params['v'];
    } else if (array_key_exists("l", $params)) {
        $layoutids[] = $params['l'];
    } else {
        echo "DumpCounts.php usage: -v<videoid> | -l<layoutid>\n";
        exit;
    }
    echo "\n\n";
    
    // check connection to database
    $con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);
    if (!mysqli_connect_errno($con)) {
        // if videoid is set, retrieve list of layoutids
        if (isset($videoid)) {
            $result0 = mysqli_query($con, "SELECT layoutid from idax_video_layouts WHERE videoid=$videoid AND status='COUNT_COMPLETED'");
            if ($result0) {
                $numRows = mysqli_num_rows($result0);
                for ($i=0; $i<$numRows; $i++) {
                    $row0 = mysqli_fetch_row($result0);
                    $layoutids[] = $row0[0];
                }
            }
        }
        
        // get video start time
        $result0 = mysqli_query($con, "SELECT capturestarttime FROM idax_video_files WHERE videoid=$videoid");
        if ($result0) {
            $row0 = mysqli_fetch_row($result0);
            $startdatetime = strtotime($row0[0]);
        }
        
        // for each layoutid, create a formatted dump of the counts
        foreach ($layoutids as $layoutid) {                
            // get countedby_user(s) for this layout
            $result1 = mysqli_query($con, "SELECT DISTINCT countedby_user FROM idax_video_counts WHERE layoutid=$layoutid AND rejected=0");
            if ($result1) {
                $numRows = mysqli_num_rows($result1);
                for ($j=0; $j<$numRows; $j++) {
                    $row1 = mysqli_fetch_row($result1);
                    $userid = $row1[0];
                    
                    $result2 = mysqli_query($con, "SELECT email, firstname, lastname FROM accounts WHERE accountid=$userid");
                    if ($result2) {
                        $row2 = mysqli_fetch_row($result2);
                    }
                    $result3 = mysqli_query($con, "SELECT count(*) FROM idax_video_counts WHERE layoutid=$layoutid AND counttype!='PED' AND rejected=0 AND countedby_user=$userid");
                    if ($result3) {
                        $row3 = mysqli_fetch_row($result3);
                    }
                    echo "Userid: ".$userid.", ".$row2[0].",,,,,,,, ".$row2[1].",,,, ".$row2[2].",,,,, Total counts:".$row3[0]."\n";
                }

                // add table header
                echo "Interval Start, ";
                // get layoutleg directions by legindex for this layout
                $result = mysqli_query($con, "SELECT direction FROM idax_video_layoutlegs WHERE layoutid=$layoutid order by legindex");
                if ($result) {
                    $numlegs = mysqli_num_rows($result);
                    for ($i=0; $i<$numlegs; $i++) {
                        $row = mysqli_fetch_row($result);
                        echo $row[0].",,,,";
                    }
                    echo "15-min Total, Rolling One Hour\n";
                }
                echo ", ";
                for ($i=0; $i<$numlegs; $i++) {
                    echo "UT, LT, TH, RT";
                    if ($i < $numlegs-1) {
                        echo ", ";
                    }
                }
                echo ", ,\n";

                $startpos = 0;
                $endpos = 0;
                $lasttimeslottotal = 0;
                $rollinghourtotal = 0;
                $totaltotal = 0;
                for ($timeslot=0; $timeslot<4; $timeslot++) {            
                    $timeslottotal = 0;                    
                    // initialize zeroed-out leg array
                    $legarray = array();
                    for ($leg=0; $leg<$numlegs; $leg++) {
                        $legarray[] = array("UTURN"=> 0, "LTURN"=> 0, "STRAIGHT"=> 0, "RTURN"=> 0);
                    }
                    // save first timeslot legarray as the totalarray
                    if ($timeslot == 0) {
                        $totalarray =  $legarray;
                    }
                    
                    $startpos = $endpos;
                    $endpos = $endpos + 900;
                    // get split of counts for the timeperiod
                    $result4 = mysqli_query($con, "SELECT legindex, counttype, COUNT(*) FROM idax_video_counts WHERE layoutid=$layoutid AND rejected=0 AND counttype!='PED' AND videoposition>=$startpos AND videoposition<$endpos GROUP BY legindex, counttype ORDER BY legindex");
                    if ($result4) {
                        $numRows = mysqli_num_rows($result4);
                        for ($k=0; $k<$numRows; $k++) {
                            $row4 = mysqli_fetch_row($result4);
                            $legarray[$row4[0]][$row4[1]] = $row4[2];
                            $timeslottotal += (int)$row4[2];
                            // add timeslot totals to $totalarray
                            $totalarray[$row4[0]][$row4[1]] += $row4[2];
                        }
                    }
                    $rollinghourtotal += $timeslottotal;
                    $totaltotal += $timeslottotal;
                    $interval = date("g:i", $startdatetime+($timeslot*900));
                    echo $interval.", ";
                    for ($leg=0; $leg<$numlegs; $leg++) {
                        echo $legarray[$leg]["UTURN"].", ".$legarray[$leg]["LTURN"].", ".$legarray[$leg]["STRAIGHT"].", ".$legarray[$leg]["RTURN"].", ";
                    }
                    if ($timeslot > 2) {
                        $rollinghourtotal = $rollinghourtotal - $lasttimeslottotal;
                        $lasttimeslottotal = $timeslottotal;
                        echo $timeslottotal.", ".$rollinghourtotal."\n";
                    } else {
                        echo $timeslottotal.", 0\n";
                    }                    
                }    
                // echo the totals
                echo "Count Total, ";
                for ($leg=0; $leg<$numlegs; $leg++) {
                    echo $totalarray[$leg]["UTURN"].", ".$totalarray[$leg]["LTURN"].", ".$totalarray[$leg]["STRAIGHT"].", ".$totalarray[$leg]["RTURN"].", ";
                }
                echo $totaltotal.", 0";
                echo "\n\n";
            }
        }
        mysqli_close($con);
    }
    else {
        echo "Failed to connect to USERs database. Error=".mysqli_connect_errno()."\n";
    }
?>
