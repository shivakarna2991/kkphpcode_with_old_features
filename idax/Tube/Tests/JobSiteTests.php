<?php

	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Idax\Tube\Classes\JobSite;

	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	function DoIngestionTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$jobSite = new JobSite($context);

			$fileContents = file_get_contents("/tmp/58942_96.txt");

			$alreadyExists = FALSE;
			$result = $jobSite->IngestData(
					"374",                // jobsite id
					"96.txt",             // ingestion key
					FALSE,                // reverse
					$fileContents,
					TRUE,                 // replace existing
					$ingestionId,
					$resultString
					);

			DBG_INFO(
					DBGZ_APP,
					__FUNCTION__,
					"JobSiteManager::CreateIngestion returned ".serialize($retval).", alreadyExists=".serialize($alreadyExists).", resultString=$resultString"
					);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}


	DBG_SET_PARAMS(DBGZ_APP | DBGZ_TUBE_JOBSITE | DBGZ_TUBEJOBSITEROW | DBGZ_ACCOUNTROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, TRUE, FALSE, dbg_dest_terminal, TRUE);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-i')
		{
			DoIngestionTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-i]\n";
	}
?>
