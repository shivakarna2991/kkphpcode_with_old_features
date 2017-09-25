<?php

	require_once '/home/idax/idax.php';
	require_once '/home/core/classes/AccountManager.php';
	require_once '/home/idax/Common/Classes/JobManager.php';
	require_once '/home/idax/Common/Classes/TaskManager.php';
	require_once '/home/core/classes/MethodCallContext.php';

	function DoTaskTests()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);
			$taskManager = new TaskManager($context);

			$result = $jobManager->CreateJob(
					"TEST.0001",
					"test job",
					"Renton",
					"West Seattle",
					"This is a test job",
					"TubeData",
					"2016-04-15",
					"2016-04-30",
					$jobObject,
					$resultString
					);

			DBG_INFO(DBGZ_APP, __FUNCTION__, "jobManager::CreateJob returned ".serialize($result));

			if ($result)
			{
				readline("Job created - hit ENTER to continue");
				DBG_INFO(DBGZ_APP, __FUNCTION__, "New job id=".$jobObject->getJobId());

				$result = $jobManager->CreateTask($jobObject->getJobId(), 1, 3, 84, $taskObject, $resultString);
				readline("Task created - hit ENTER to continue");

				if ($result)
				{
					$result = $taskManager->UpdateTask($taskObject->getTaskId(), 5, 8, 24, $resultString);
					readline("Task updated - hit ENTER to continue");

					$result = $taskManager->CreateJobSite(
							$taskObject->getTaskId(),
							$jobObject->getJobId(),
							"sitecode",
							4,
							"TubeData",
							100.000,
							200.000,
							"description",
							"STANDARD",
							"Northbound/Southbound",
							TRUE,
							5,
							$jobSiteObject,
							$resultString
							);
					readline("JobSite created - hit ENTER to continue");

					$result = $taskManager->GetJobSites($taskObject->getTaskId(), $jobSites, $resultString);
					var_dump($jobSites);

					readline("Get job sites - hit ENTER to continue");

					$result = $taskManager->DeleteTask($taskObject->getTaskId(), $resultString);
					readline("Task deleted - hit ENTER to continue");
				}

				$result = $jobManager->DeleteJob($jobObject->getJobId(), $resultString);
			}
			else
			{
				DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->CreateJob() failed: resultString=$resultString");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCreateJobSiteTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);

			$retval = $jobManager->CreateJobSite(
					"124th Cam",           // site code
					"37",                  // job id
					"VideoData",
					-134.5,                // latitude
					47.5,                  // longitude
					"test jobsite",        // description
					"North/South",         // direction
					FALSE,                 // one-way
					3,                     // count priority
					$jobsiteExists,
					$jobsiteObject,
					$resultString
					);

			if ($retval)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "JobSite with id={$jobsiteObject->getJobSiteId()} created");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "jobManager::CreateJobSite failed - resultString='$resultString'");

				if ($jobsiteExists)
				{
					DBG_INFO(DBGZ_APP, __FUNCTION__, "Job site already exists");
				}
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}


	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTS | DBGZ_JOBMGR | DBGZ_JOBS | DBGZ_JOBSITES | DBGZ_TASKMGR | DBGZ_TASKS, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-t')
		{
			DoTaskTests();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [--t]\n";
	}
?>
