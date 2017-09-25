<?php

	require_once '/home/core/core.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Video\Classes\JobSite as VideoJobSite;

	function DoGetAllJobSiteVideosTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSiteManager = new VideoJobSiteManager($context);

			$retval = $videoJobSiteManager->GetAllJobSiteVideos(true, "0000-00-00 00:00:00", $jobSiteVideos, $resultString);

			var_dump($jobSiteVideos);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function Retry()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$files = array(
				// array("jobsiteid" => "710", "name" => "3", "cameralocation" => "northwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "d0f94ad2a05df25ea3b4064cba08c301-1465428587.mp4")
				//array("jobsiteid" => "722", "name" => "6", "cameralocation" => "northwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "d235cbefa1af720f4f47697789a6fcee-1465428624.mp4"),
				array("jobsiteid" => "707", "name" => "1", "cameralocation" => "southwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "ac8bfe60bfbe569a543aa4aff8c1a115-1465428641.mp4"),
				//array("jobsiteid" => "708", "name" => "2", "cameralocation" => "northwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "84641e659ad3e27671a036a90febd9f6-1465428738.mp4"),
				array("jobsiteid" => "713", "name" => "4", "cameralocation" => "northwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "8098b9db4f7f2ab20da5d5bf2f44e636-1465428768.mp4"),
				array("jobsiteid" => "717", "name" => "5", "cameralocation" => "southwest", "capturestarttime" => "2016-06-07 16:00:00", "capturestoptime" => "2016-06-07 18:00:00", "videofile" => "77c27c71be0060a37c4787af2f9bfb94-1465429030.mp4")
		);

		$dir = "/home/idax/temp_file_upload/";

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mark.skaggs@idaxdata.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSite = new VideoJobSite($context);

			foreach ($files as &$file)
			{
				$result = $videoJobSite->UploadVideo(
						$file['jobsiteid'],
						$file['name'],
						$file['cameralocation'],
						$file['capturestarttime'],
						array($file['videofile']),
						1, // files per segment,
						$videoIds,
						$resultString
						);

				if ($result)
				{
					DBG_INFO(DBGZ_APP, __FUNCTION__, "Succeeded.  $videoIds=".serialize($videoIds));
				}
				else
				{
					DBG_INFO(DBGZ_APP, __FUNCTION__, "Failed.  resultString=$resultString");
				}
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoUploadVideoTest(
		$jobSiteId,
		$name,
		$videoFiles
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "jobSiteId=$jobSiteId, name=$name");

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSiteManager = new VideoJobSiteManager($context);

			$result = $videoJobSiteManager->IngestVideoFile(
					$jobSiteId,
					126,
					$name,
					$videoFiles,
					$resultString
					);
			// $result = $videoJobSiteManager->UploadVideo(
			// 		$jobSiteId,
			// 		$name,
			// 		"2016-01-01 00:00:00",
			// 		$videoFiles,
			// 		1,
			// 		$videoIds,
			// 		$resultString
			// 		);

			if ($result)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Succeeded.  $videoIds=".serialize($videoIds));
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Failed.  resultString=$resultString");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoDeleteVideoTest(
		$videoId
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "videoId=$videoId");

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSite = new VideoJobSite($context);

			$result = $videoJobSite->DeleteVideo(
					$videoId,
					$resultString
					);

			if ($result)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Succeeded.");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Failed.  resultString=$resultString");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}


	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_ACCOUNTROW | DBGZ_AWSFILEMGR | DBGZ_JOBSITEROW | DBGZ_VIDEO_JOBSITE | DBGZ_VIDEO_FILEROW | DBGZ_VIDEO_LAYOUTROW | DBGZ_VIDEO_LAYOUTLEGROW,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-v')
		{
			DoGetAllJobSiteVideosTest();
		}
		else if ($argv[1] == '-r')
		{
			Retry();
		}
		else if ($argv[1] == '-u')
		{
			$videoFiles = array();

			$i = 4;

			while (isset($argv[$i]))
			{
				$videoFiles[] = $argv[$i];
				$i += 1;
			}

			DoUploadVideoTest($argv[2], $argv[3], $videoFiles);
		}
		else if ($argv[1] == '-d')
		{
			DoDeleteVideoTest($argv[2]);
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-v] | [-u] | [-d]\n";
	}
?>
