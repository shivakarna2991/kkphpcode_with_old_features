<?php

	require_once '/home/core/core.php';
	require_once '/home/core/LocalMethodCall.php';

	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Data\AccountRow;
	use \Core\Common\Classes\IssueManager;

	function DoCreateIssueTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = AccountRow::FindOne($con, NULL, array('email="mike@kanopian.com"'), NULL, ROW_OBJECT, $sqlError=0);

		$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
		$issueManager = new IssueManager($context);

		$result = $issueManager->CreateIssue(
				1,     // app
				NULL,  // secret
				"PDF Reports suck",
				"PDF reports for Speed, Volume, and Class are all messed up.",
				"1. Run the reports. 2. Look at the reports.  3. Notice that they're all messed up.",
				"The PDF rendering libraries we use suck.  There is no good workaround or alternative.",
				"1",
				$issueRow,
				$resultString
				);

		DBG_INFO(DBGZ_APP, __FUNCTION__, "result=$result, resultString='$resultString'");

		if ($result)
		{
			$response_str = LocalMethodCall(
					"IssueManager",
					"AddAttachment",
					"71.202.153.144",
					array("_mhdr_token" => "70bda7a05c374d5b82e83cc6579779cd", "issueid" => $issueRow->getIssueId(), "filename" => "attachment.txt", "filecontents" => file_get_contents("/home/core/core.php")),
					""
					);
			// $result = $issueManager->AddAttachment(
			// 		NULL,   // secret
			// 		$issueRow->getIssueId(),
			// 		"attachment.txt",
			// 		file_get_contents("/home/core/core.php"),
			// 		$issueAttachmentRow,
			// 		$resultString
			// 		);

			var_dump($response_str);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ISSUEMGR | DBGZ_ISSUEROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-c')
		{
			DoCreateIssueTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-c | | -cvl | -p]\n";
	}
?>
