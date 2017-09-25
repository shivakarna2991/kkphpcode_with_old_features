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

    /* Find jobs assigned to this server to execute
     * look for a task, if not already busy
      check my status in the servers table by instance_id parameter passed in
        if status=AVAILABLE
          check for jobs assigned to me in the core_jobqueue table (serverinstanceid==my instance-id)
            if job for me exists
              start executing the task (execute php task with params from core_jobqueue table)
            else
              go back to sleep
        else
          go back to sleep
    */

    $params = getopt("i:");
    if (array_key_exists("i", $params)) {
        $instance_id = $params["i"];
    } else {
        // no instance_id, nothing to do
        exit;
    }

    // connect and verify connection
    $dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);
    if ($dbcon == NULL) {
        DBG_RETURN(DBGZ_APP, "FindTaskToExecute", 'Failed to connect to database');
        exit;
    }

    // retrieve status for server row having my instance_id
    $status = "Undefined";
    $result = mysqli_query($dbcon, "SELECT status FROM core_jobservers WHERE instanceid='$instance_id'");
    if ($result && mysqli_num_rows($result)) {
        $row = mysqli_fetch_row($result);
        $status = $row[0];
        
        // take this opportunity to set the public ip address of this server instance in the jobserver table
        $ipaddress = shell_exec("GET http://169.254.169.254/latest/meta-data/public-ipv4 2>&1");
        mysqli_query($dbcon, "UPDATE core_jobservers SET ipaddress='$ipaddress' WHERE instanceid='$instance_id'");
    }

    // if status != "AVAILABLE", we're done
    if ($status != "AVAILABLE") {
        exit;
    }

    // check for a job assigned to us, and get the job params to pass along for execution
    $result = mysqli_query($dbcon, "SELECT * FROM core_jobqueue WHERE serverinstanceid='$instance_id'");
    if ($result && mysqli_num_rows($result)) {
        $row = mysqli_fetch_array($result);
        $jobqueueid = $row['jobqueueid'];
        $jobname = $row['jobname'];
        $jobparams = $row['jobparams'];

        // set status=EXECUTING in core_jobservers table
        mysqli_query($dbcon, "UPDATE core_jobservers SET status='EXECUTING' WHERE instanceid='$instance_id'");
        DBG_INFO(DBGZ_APP,  "FindTaskToExecute", "Found a job...set jobserver status='EXECUTING' for instanceid: $instance_id");

        switch ($jobname) {
            case "IdaxIngestVideo":
                DBG_INFO(DBGZ_APP,  "FindTaskToExecute", "Executing the job ".$jobname.", with params: ".$jobparams);
                $returnval = shell_exec("php /home/idax/Video/Tools/IngestVideo.php -i ".$instance_id." -p ".addslashes($jobparams)." 2>&1");
                break;
            default:
                break;
        }
    }     
?>