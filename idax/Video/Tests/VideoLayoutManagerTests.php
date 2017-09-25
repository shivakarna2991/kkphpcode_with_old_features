<?php

	//use VideoJobSiteManager;
	//use Idax\Video\Classes\JobSiteManager as VideoJobSiteManager;

	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';
	// require_once '/home/core/classes/AccountManager.php';
	require_once '/home/core/classes/accountstable.php';
	// require_once '/home/idax/Video/Classes/JobSiteManager.php';
	// require_once '/home/idax/Video/Data/JobSite.php';
	// require_once '/home/idax/Video/Data/File.php';
	// require_once '/home/idax/Video/Data/Layout.php';
	// require_once '/home/idax/Video/Data/LayoutLeg.php';

	function DoCreateLayoutTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$legs = array(
					array(
							'index' => 0,
							"lturn_pos" => "1.0,1.0,1.0,1.0,1.0",
							"rturn_pos" => "2.0,2.0,2.0,2.0,2.0",
							"uturn_pos" => "3.0,3.0,3.0,3.0,3.0",
							"straight_pos" => "4.0,4.0,4.0,4.0,4.0"
							),
					array(
							'index' => 1,
							"lturn_pos" => "1.5,1.5,1.5,1.5,1.5",
							"rturn_pos" => "2.5,2.5,2.5,2.5,2.5",
							"uturn_pos" => "3.5,3.5,3.5,3.5,3.5",
							"straight_pos" => "4.5,4.5,4.5,4.5,4.5"
							)
					);

			$retval = $layoutManager->CreateLayout(3, "test layout", 4, $legs, $layout, $resultString);

			var_dump($layout);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoUpdateLayoutTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$legs = array(
					array(
							'index' => 0,
							"lturn_pos" => "1.0,1.0,1.0,1.0,1.0",
							"rturn_pos" => "2.0,2.0,2.0,2.0,2.0",
							"uturn_pos" => "3.0,3.0,3.0,3.0,3.0",
							"straight_pos" => "4.0,4.0,4.0,4.0,4.0"
							),
					array(
							'index' => 1,
							"lturn_pos" => "1.5,1.5,1.5,1.5,1.5",
							"rturn_pos" => "2.5,2.5,2.5,2.5,2.5",
							"uturn_pos" => "3.5,3.5,3.5,3.5,3.5",
							"straight_pos" => "4.5,4.5,4.5,4.5,4.5"
							),
					array(
							'index' => 2,
							"lturn_pos" => "2.5,2.5,2.5,2.5,2.5",
							"rturn_pos" => "3.5,3.5,3.5,3.5,3.5",
							"uturn_pos" => "4.5,4.5,4.5,4.5,4.5",
							"straight_pos" => "5.5,5.5,5.5,5.5,5.5"
							)
					);

			$retval = $layoutManager->UpdateLayout(13, "test layout revised", 2, $legs, $layout, $resultString);

			var_dump($layout);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoDeleteLayoutTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$retval = $layoutManager->DeleteLayout(9, $resultString);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoActionTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$retval = $layoutManager->StartAction(16, 'QC', $resultString);

			DBG_ENTER(DBGZ_APP, __FUNCTION__, "retval=$retval, resultString='$resultString'");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoAddMovementTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$retval = $layoutManager->AddMovement(16, 0, "uturn", "car", "12.345", $resultString);

			DBG_ENTER(DBGZ_APP, __FUNCTION__, "retval=$retval, resultString='$resultString'");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoRejectCountTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$layoutManager = new VideoLayoutManager($context);

			$retval = $layoutManager->RejectCount(16, $resultString);

			DBG_ENTER(DBGZ_APP, __FUNCTION__, "retval=$retval, resultString='$resultString'");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_VIDEO_JOBSITEMGR | DBGZ_VIDEO_LAYOUTMGR | DBGZ_VIDEOFILE | DBGZ_VIDEO_LAYOUT | DBGZ_VIDEO_LAYOUTLEG,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-c')
		{
			DoCreateLayoutTest();
		}
		else if ($argv[1] == '-u')
		{
			DoUpdateLayoutTest();
		}
		else if ($argv[1] == '-d')
		{
			DoDeleteLayoutTest();
		}
		else if ($argv[1] == '-a')
		{
			DoActionTest();
		}
		else if ($argv[1] == '-m')
		{
			DoAddMovementTest();
		}
		else if ($argv[1] == '-r')
		{
			DoRejectCountTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-c | -u | -d]\n";
	}
?>
