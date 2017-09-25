<?php

	require_once '/home/core/core.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Video\Classes\JobSite as VideoJobSite;

	function DeleteVideo(
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
		if ($argv[1] == '-d')
		{
			DeleteVideo($argv[2]);
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." -d videoid\n";
	}
?>
