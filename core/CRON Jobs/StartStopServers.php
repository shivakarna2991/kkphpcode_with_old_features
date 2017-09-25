<?php
    require_once '/home/idax/idax.php';

    DBG_SET_PARAMS(
            DBGZ_APP | DBGZ_ACCOUNTROW | DBGZ_JOBSITEROW | DBGZ_VIDEO_JOBSITE | DBGZ_VIDEO_FILEROW,
            DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
            FALSE,
            FALSE,
            dbg_dest_log,
            dbg_file
            );

    /* Master Server Script - starts and stops slave servers, assigns tasks to newly started servers
     * find tasks that need assigning
      look for jobs needing tasking in the core_jobqueue table (defined as serverinstanceid==unassigned)
        if any jobs to task
          generate unique client token for the server instance (AWS requirement, not used by us, but should not be reused)
          launch a new server - get it's instanceid in return
          create a new server entry in the servers table with serverid=instance-id and status=AVAILABLE
          assign job to the server by setting the serverinstanceid=instanceid to the job in the core_jobqueue table

     * clean up idle servers
      search for idle servers (defined as those with status==COMPLETED) 
        if idle server exist
          terminate the server
          update servers table with status=TERMINATED
    */

        // connect and verify connection
    $dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);
    if ($dbcon == NULL) {
        DBG_RETURN(DBGZ_APP, "StartStopServers", 'Failed to connect to database');
        echo "Failed to connect to Database.\n";
        exit;
    }

    // retrieve list of jobs needing assignment
    $result = mysqli_query($dbcon, "SELECT jobqueueid FROM core_jobqueue WHERE serverinstanceid='unassigned'");
    if ($result && mysqli_num_rows($result)) {
        for ($i=0; $i<mysqli_num_rows($result); $i++) {
            $row = mysqli_fetch_row($result);
            $jobqueueid = $row[0];

            DBG_INFO(DBGZ_APP, "StartStopServers", "Found an unassigned job with id: $jobqueueid");

            // get image-id value from current server instance, use it for starting new servers
            $imageid = "ami-889247e8";

            // generate a unique server client token
            $clientToken = md5(microtime().rand())."-".time();
            
            // start new server instance - test server is t2.micro, slightly different required parameters
            // $returnval = shell_exec("aws ec2 run-instances --image-id ami-3a2fd55a --count 1 --key-name IDAXkey --security-groups Tubeworld-security-group --instance-type t2.micro --instance-initiated-shutdown-behavior terminate --client-token $clientToken 2>&1"); 
            // real server is a c4.4xlarge server with the real parameters below           
            $returnval = shell_exec("sudo aws ec2 run-instances --image-id $imageid --count 1 --key-name VAKeyPair --security-groups VA-Development --instance-type c4.4xlarge --instance-initiated-shutdown-behavior terminate --client-token $clientToken --ebs-optimized 2>&1");
            $jsonOutput = json_decode($returnval, true);
            if (isset($jsonOutput)) {
                $instanceid = $jsonOutput["Instances"][0]["InstanceId"];
            } else {
                DBG_RETURN(DBGZ_APP, "StartStopServers", "Failed to start server instance for image-id: $imageid");
                break;
            }
            
            // retrieve the instance-id value from the return result
            if (isset($instanceid)) {
                // create new slave server record and set status=AVAILABLE
                $result2 = mysqli_query($dbcon, "INSERT INTO core_jobservers (instanceid, status) VALUES ('$instanceid', 'AVAILABLE')");
                DBG_INFO(DBGZ_APP, "StartStopServers", "Launched new server instance with ami-id: $imageid, returned with instanceid: $instanceid, set status='AVAILABLE'");

                // assign this job to the server by adding it's instanceid to the serverinstanceid parameter in the jobqueue table
                mysqli_query($dbcon, "UPDATE core_jobqueue SET serverinstanceid='$instanceid' WHERE jobqueueid='$jobqueueid'");
                DBG_INFO(DBGZ_APP, "StartStopServers", "Assigned server with instanceid: $instanceid to job with id: $jobqueueid");
            } else {
                DBG_RETURN(DBGZ_APP, "StartStopServers", "Failed to retrieve instanceid from newly launched server with imageid: $imageid");
                break;
            }
        }
    }

    // search for idle slave servers, terminate them
    $result = mysqli_query($dbcon, "SELECT instanceid, manualtakedown FROM core_jobservers WHERE status='COMPLETED'");
    if ($result && mysqli_num_rows($result)) {
        for ($i=0; $i<mysqli_num_rows($result); $i++) {
            $row = mysqli_fetch_row($result);
            $instanceid = $row[0];
            $manualtakedown = $row[1];
            DBG_INFO(DBGZ_APP, "StartStopServers", "Found server with instance: $instanceid, manualtakedown=$manualtakedown, status='COMPLETED'");
            if ($manualtakedown == '0') {
                $returnval = shell_exec("sudo aws ec2 terminate-instances --instance-ids $instanceid 2>&1");
                // update slave server record with status=TERMINATED
                mysqli_query($dbcon, "UPDATE core_jobservers SET status='TERMINATED' WHERE instanceid='$instanceid'");
                DBG_INFO(DBGZ_APP, "StartStopServers", "Server instance: $instanceid TERMINATED");
            }
        }
    }
?>
