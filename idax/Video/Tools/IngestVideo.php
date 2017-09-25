<?php

	require_once '/home/core/core.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\AWSFileManager;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Video\Classes\JobSite as VideoJobSite;

	function IngestVideo(
		$serverInstanceId,
		$videoId
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "serverInstanceId=$serverInstanceId, videoId=$videoId");

		// // Start an async task to ingest the video file.
		// $result = AsyncCall(
		// 		"/home/idax/Video/Classes/JobSite.php",
		// 		"\Idax\Video\Classes\JobSite::IngestVideo_Array",
		// 		array(
		// 				'email' => "mike@kanopian.com",
		// 				'serverinstanceid' => $serverInstanceId,
		// 				'videoid' => $videoId
		// 				)
		// 		);

		// if ($result === NULL)
		// {
		// 	$resultString = "AsyncCall(IngestVideo($videoId)) failed";
		// }
		// else
		// {
		// 	$pid = intval($result);
		// 	DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "PID for IngestVideo($videoId) is ".$pid);
		// }

		// connect and verify connection
		$dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($dbcon, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($dbcon, $accountRow, NULL, "127.0.0.1", NULL);
			$videoJobSite = new VideoJobSite($context);

			$result = $videoJobSite->IngestVideo(
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

			mysqli_query($dbcon, "UPDATE core_jobservers SET status='COMPLETED' WHERE instanceid='$serverInstanceId'");

			// Transfer idax.log to AWS bucket.
			$awsFileManager = new AWSFileManager(IDAX_JOBSERVERLOG_BUCKET, AWSREGION, AWSKEY, AWSSECRET);
			$awsFileManager->UploadFile("idax/logs/idax.log", "{$serverInstanceId}_idax.log", "public-read", true, $resultString);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_ASYNCCALL | DBGZ_ACCOUNTROW | DBGZ_JOBSITEROW | DBGZ_VIDEO_JOBSITE | DBGZ_VIDEO_FILEROW,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_log | dbg_dest_terminal,
			dbg_file
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	$params = getopt("i:p:");

	if (array_key_exists("i", $params) && array_key_exists("p", $params))
	{
		$serverInstanceId = $params["i"];

		$ingestionParams = json_decode(stripslashes($params["p"]), true);
		$videoId = $ingestionParams["videoid"];

		IngestVideo($serverInstanceId, $videoId);
	}
	else
	{
		echo "Usage: -i serverinstanceid -p [\"videoid=vid\"]\n";
	}
?>
