<?php

	require_once '/home/idax/idax.php';

	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Classes\AccountManager;
	use \Idax\Common\Classes\JobManager;

	function DoCreateJobTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);

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
				DBG_INFO(DBGZ_APP, __FUNCTION__, "New job id=".$jobObject->getJobId());

				$result = $jobManager->GetJob($jobObject->getJobId(), $job, $resultString);

				if ($result)
				{
					var_dump($job);

					$result = $jobManager->UpdateJob(
							$jobObject->GetJobId(),
							"TEST.0002",
							"test job2",
							"Renton2",
							"West Seattle2",
							"This is a test job2",
							"VideoData",
							"2017-04-15",
							"2017-04-30",
							$jobObject,
							$resultString
							);

					if ($result)
					{
						$result = $jobManager->GetJob($jobObject->getJobId(), $job, $resultString);

						if ($result)
						{
							var_dump($job);


							$result = $jobManager->CreateTask($jobObject->getJobId(), 1, 3, 84, $taskObject, $resultString);
							$result = $jobManager->CreateTask($jobObject->getJobId(), 2, 4, 29, $taskObject2, $resultString);

							$result = $jobManager->GetTasks($jobObject->getJobId(), $tasks, $resultString);
							var_dump($tasks);

							$result = $jobManager->CloseJob($jobObject->getJobId(), $resultString);

							if ($result)
							{
								$result = $jobManager->GetJob($jobObject->getJobId(), $job, $resultString);

								if ($result)
								{
									var_dump($job);

									$result = $jobManager->DeleteJob($jobObject->getJobId(), $resultString);

									if ($result)
									{
										$result = $jobManager->GetJob($jobObject->getJobId(), $job, $resultString);

										if ($result)
										{
											var_dump($job);
										}
										else
										{
											DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->GetJob() failed: resultString=$resultString");
										}
									}
									else
									{
										DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->DeleteJob() failed: resultString=$resultString");
									}
								}
								else
								{
									DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->GetJob() failed: resultString=$resultString");
								}
							}
							else
							{
								DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->CloseJob() failed: resultString=$resultString");
							}
						}
						else
						{
							DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->GetJob() failed: resultString=$resultString");
						}
					}
					else
					{
						DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->Update() failed: resultString=$resultString");
					}
				}
				else
				{
					DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->GetJob() failed: resultString=$resultString");
				}
			}
			else
			{
				DBG_ERR(DBGZ_APP, __FUNCTION__, "jobManager->CreateJob() failed: resultString=$resultString");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCloseJobTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);

			$retval = $jobManager->CloseJob(24);

			DBG_INFO(DBGZ_APP, __FUNCTION__, "jobManager::CloseJob returned ".serialize($retval));

			var_dump($jobManager->GetJobs(FALSE));
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

	function DoUpdateJobSiteTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);

			$retval = $jobManager->UpdateJobSite(
					"133",                 // job site id
					NULL,                  // latitude
					NULL,                  // longitude
					NULL,                  // description
					NULL,                  // direction
					NULL,                  // one-way
					1,                     // count priority
					$resultString
					);

			if ($retval)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "JobSite updated");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "jobManager::UpdateJobSite failed - resultString='$resultString'");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoDeleteJobSiteTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
			$jobManager = new JobManager($context);

			$retval = $jobManager->DeleteJobSite(
					"133",                 // job site id
					$resultString
					);

			if ($retval)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "JobSite updated");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "jobManager::UpdateJobSite failed - resultString='$resultString'");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}


	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTS | DBGZ_JOBMGR | DBGZ_JOBROW | DBGZ_JOBSITEROW | DBGZ_TASKROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-createjob')
		{
			DoCreateJobTest();
		}
		else if ($argv[1] == '-closejob')
		{
			DoCloseJobTest();
		}
		else if ($argv[1] == '-createjobsite')
		{
			DoCreateJobSiteTest();
		}
		else if ($argv[1] == '-updatejobsite')
		{
			DoUpdateJobSiteTest();
		}
		else if ($argv[1] == '-deletejobsite')
		{
			DoDeleteJobSiteTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-createjob | -closejob | -createjobsite | -updatejobsite | -deletejobsite]\n";
	}
?>
