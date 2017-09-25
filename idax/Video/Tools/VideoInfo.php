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

	function PrintVideoInfo(
		$fileRow,
		&$resultString
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$videoId = $fileRow["videoid"];

		if ($fileRow["testdata"] == "1")
		{
			$videoId .= "*";
		}

		$filePrefix = $fileRow["bucketfileprefix"];
		$captureDuration = gmdate("H:i:s", strtotime($fileRow["captureendtime"]) - strtotime($fileRow["capturestarttime"]));
		$ingestionDuration = gmdate("H:i:s", strtotime($fileRow["lastupdatetime"]) - strtotime($fileRow["addedtime"]));

		echo "Video Info\n";
		echo "----------\n";
		echo "ID:       $videoId\n";
		echo "Name:     ".$fileRow["name"]."\n";
		echo "Job Site: ".$fileRow["jobsiteid"]."\n";
		echo "Status:   ".$fileRow["status"]."\n";
		echo "Phase:    ".$fileRow["phase"]."\n";
		echo "Prefix:   ".$filePrefix."\n";
		echo "Size:     ".number_format($fileRow["filesize"])."\n";
		echo "Location: ".$fileRow["cameralocation"]."\n";
		echo "Ingestion:\n";
		echo "\tStart:    ".$fileRow["lastupdatetime"]."\n";
		echo "\tEnd:      ".$fileRow["addedtime"]."\n";
		echo "\tDuration: ".$ingestionDuration."\n";
		echo "Capture:\n";
		echo "\tStart:    ".$fileRow["capturestarttime"]."\n";
		echo "\tEnd:      ".$fileRow["captureendtime"]."\n";
		echo "\tDuration: ".$captureDuration."\n";
		echo "\n";

		DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, TRUE);
		return TRUE;
	}

	function GetVideoInfo(
		$videoIds
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

			$filter = NULL;

			if ($videoIds != NULL)
			{
				$filter = array("videoid IN (".implode(",", $videoIds).")");
			}

			$fileRows = FileRow::Find(
					$con,
					NULL,
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			foreach ($fileRows as &$fileRow)
			{
				PrintVideoInfo($fileRow, $resultString);
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	// DBG_SET_PARAMS(
	// 		DBGZ_APP | DBGZ_VIDEO_JOBSITE,
	// 		DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
	// 		FALSE,
	// 		FALSE,
	// 		dbg_dest_terminal,
	// 		NULL
	// 		);
	DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	$videoIds = NULL;

	if (isset($argv[1]))
	{
		$videoIds = explode(",", $argv[1]);
	}

	GetVideoInfo($videoIds);
?>
