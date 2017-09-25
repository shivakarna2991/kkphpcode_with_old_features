<?php

	define ('_IDAX_REPORTS_PATH', '/home/idax/Tube/Reports');

	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Tube\Classes\JobSite as TubeJobSite;

	function DoCreateVolumeReportTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$jobSite = new TubeJobSite($context);

			$retval = $jobSite->CreateVolumeReport(
					"130",                 // jobsite id
					"99",                  // title (site code)
					"141",                 // ingestion id
					"Redmond",            // Redmond template
					NULL,                 // report parameters
					"2015-10-18 13:48:00", // filter begin time
					"2015-10-24 23:19:00", // filter end time
					"2015-10-19 00:00:00", // start date
					"2015-10-24 00:00:00",
					"99",                  // site code
					"8TH AVE N/O SPRING ST",  // location
					"Northbound",           // primary direction
					"Southbound",           // secondary direction
					"/home/idax/test_file_upload",
					$outputFiles,
					$resultString
					);

			if ($retval == FALSE)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateVolumeReport failed");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateVolumeReport succeeded");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCreateSpeedReportTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$jobSite = new TubeJobSite($context);

			$retval = $jobSite->CreateSpeedReport(
					"130",                 // jobsite id
					"99",                  // title (site code)
					"141",                 // ingestion id
					"Redmond",            // Redmond template
					"stationid=123;specificlocation=456;speedlimit=30",                 // report parameters
					"2015-10-18 13:48:00", // filter begin time
					"2015-10-24 23:19:00", // filter end time
					"2015-10-19 00:00:00", // start date
					"2015-10-24 23:59:59",
					"99",                           // site code
					"8TH AVE N/O SPRING ST",  // location
					"Northbound",           // primary direction
					"Southbound",           // secondary direction
					"/home/idax/test_file_upload",  // output folder path
					$outputFiles,
					$resultString
					);

			if ($retval == FALSE)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateSpeedReport failed");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateSpeedReport succeeded");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCreateClassReportTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$jobSite = new TubeJobSite($context);

			$retval = $jobSite->CreateClassReport(
					"130",                 // jobsite id
					"99",                  // title (site code)
					"141",                 // ingestion id
					"Redmond",            // Redmond template
					"stationid=123;specificlocation=456;speedlimit=30",                 // report parameters
					"2015-10-18 13:48:00", // filter begin time
					"2015-10-24 23:19:00", // filter end time
					"2015-10-19 00:00:00", // start date
					"2015-10-24 00:00:00",
					"99",                  // site code
					"8TH AVE N/O SPRING ST",  // location
					"Northbound",           // primary direction
					"Southbound",           // secondary direction
					"/home/idax/test_file_upload",
					$outputFiles,
					$resultString
					);

			if ($retval == FALSE)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateClassReport failed");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateClassReport succeeded");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCreateReportsTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$jobSite = new TubeJobSite($context);

			$retval = $jobSite->CreateReports(
					"141",                  // ingestion id
					"2015-10-19 00:00:00", // start date
					"2015-10-24 00:00:00", // end date
					array("volume", "class", "speed"),
					NULL,                  // report format
					NULL,                  // report parameters
					// array("volume"),
					// array("class"),
					// array("speed"),
					"/home/idax/test_file_upload",
					$reportInfo,
					$resultString
					);

			if ($retval == FALSE)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateReports failed");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "CreateReports succeeded - reportInfo=".serialize($reportInfo));
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_ACCOUNTROW | DBGZ_JOBROW | DBGZ_JOBSITEROW | DBGZ_TUBE_REPORTROW | DBGZ_TUBE_VOLUMEREPORT | DBGZ_TUBE_CLASSREPORT | DBGZ_TUBE_SPEEDREPORT,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-v')
		{
			DoCreateVolumeReportTest();
		}
		else if ($argv[1] == '-s')
		{
			DoCreateSpeedReportTest();
		}
		else if ($argv[1] == '-c')
		{
			DoCreateClassReportTest();
		}
		else if ($argv[1] == '-a')
		{
			DoCreateReportsTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-v | -s | -c | -a]\n";
	}
?>
