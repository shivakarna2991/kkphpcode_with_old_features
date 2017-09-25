<?php

	require_once '/home/idax/idax.php';
	require_once '/home/core/classes/AWSFileManager.php';
	require_once '/home/core/fileutils.php';

	function DoAWSFileManagerTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$awsFileManager = new AWSFileManager(IDAX_DATA_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

		$retval = $awsFileManager->UploadData(
				"idax.txt",
				"private",
				"idax test!",
				GetMimeTypeByFileExtension("txt"),
				FALSE,
				$resultString
				);

		if ($retval)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "UploadData returned TRUE.");
		}
		else
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "UploadData returned FALSE.");
		}

		if ($awsFileManager->FileExists("idax.txt"))
		{
			$awsFileManager->GetFile("idax.txt", $fileContents);
			var_dump($fileContents);

			$awsFileManager->DeleteFile("idax.txt");

			if ($awsFileManager->FileExists("idax.txt"))
			{
				DBG_ERR("aswFileManager->DeleteFile failed");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoGetListingTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

		$result = $awsFileManager->GetFileListings(NULL, $fileListing);

		if ($result)
		{
			var_dump($fileListing);
		}
		else
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "GetFileListings returned FALSE.");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoDeleteFilesTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$awsFileManager = new AWSFileManager(IDAX_ATTACHMENTS_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

		$result = $awsFileManager->DeleteFiles("Async");

		if (!$result)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "GetFileListings returned FALSE.");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_AWSFILEMGR,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-t')
		{
			DoAWSFileManagerTest();
		}
		else if ($argv[1] == '-l')
		{
			DoGetListingTest();
		}
		else if ($argv[1] == '-d')
		{
			DoDeleteFilesTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." -t\n";
	}
?>
