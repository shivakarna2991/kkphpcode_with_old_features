<?php

	require_once '/home/core/core.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\AWSFileManager;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Video\Data\FileRow;
	use \Idax\Video\Classes\JobSite as VideoJobSite;

	function RetryVideo(
		$con,
		$videoJobSite,
		$awsFileManager,
		$videoId
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "videoId=$videoId");

		$fileRow = FileRow::FindOne(
				$con,
				NULL,
				array("videoid=$videoId"),
				NULL,
				ROW_ASSOCIATIVE,
				$sqlError
				);

		if ($fileRow !== NULL)
		{
			//
			// Download the file from the AWS bucket to the temp_file_upload folder where the
			// UploadVideo API looks for files.
			//
			$filePrefix = $fileRow["bucketfileprefix"];
			$mp4Filename = $filePrefix.".mp4";

			DBG_INFO(DBGZ_APP, __FUNCTION__, "Downloading file $mp4Filename from AWS bucket");

			$awsFileManager->getFile($mp4Filename, $mp4FileContents);
			file_put_contents("/home/idax/temp_file_upload/$mp4Filename", $mp4FileContents);
			$mp4FileContents = NULL;

			$result = $videoJobSite->UploadVideo(
					$fileRow["jobsiteid"],
					$fileRow["name"],
					$fileRow["cameralocation"],
					$fileRow["capturestarttime"],
					array($mp4Filename),
					1,
					$videoIds,
					$resultString
					);

			if ($result)
			{
				// Set the name of the new video to the name of the original video
				$newFileRow = FileRow::FindOne(
						$con,
						NULL,
						array("videoid=".$videoIds[0]),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($newFileRow !== NULL)
				{
					$newFileRow->setName($fileRow["name"]);
					$newFileRow->setCaptureStartTime($fileRow["capturestarttime"]);
					$newFileRow->setCaptureEndTime($fileRow["capturestarttime"]);
					$newFileRow->CommitChangedFields($sqlError);
				}
			}
		}

		DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, TRUE);
		return TRUE;
	}

	function RetryVideos()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mark.skaggs@idaxdata.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSite = new VideoJobSite($context);
			$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

			//$videoIds = array(342);//, 343, 344, 345, 346, 347, 348, 349);
			$videoIds = array(346);//, 343, 344, 345, 346, 347, 348, 349);

			foreach ($videoIds as &$videoId)
			{
				RetryVideo($con, $videoJobSite, $awsFileManager, $videoId);
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function VerifyVideo(
		$videoId
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mark.skaggs@idaxdata.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSite = new VideoJobSite($context);
			$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

			$fileRow = FileRow::FindOne(
					$con,
					NULL,
					array("videoid=$videoId"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if ($fileRow != NULL)
			{
				$result = $videoJobSite->VerifyVideo($awsFileManager, $fileRow, 10, $resultString);

				if (!$result)
				{
					DBG_ERR(DBGZ_APP, __FUNCTION__, "VerifyFile failed for video ".$fileRow["videoid"]." - resultString='$resultString'");
				}
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_VIDEO_JOBSITE,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	VerifyVideo($argv[1]);
?>
