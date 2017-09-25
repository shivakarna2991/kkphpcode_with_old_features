<?php
	require_once '/home/core/LocalMethodCall.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Data\AccountRow;
	use \Idax\Common\Classes\IdaxManager;

	function DoGetVideoInfoTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$response = LocalMethodCall(
				"IdaxManager",
				"GetVideoInfo",
				"71.202.153.144",
				array(
						"_mhdr_token" => "59ae38091a63622cdd07e6b3665f012c", 
						"_mhdr_version" => "1.0",
						"_mhdr_build" => "100"
						),
				""
				);

		var_dump(json_encode($response));

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(DBGZ_METHODCALL | DBGZ_APP | DBGZ_IDAXMGR, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal, TRUE);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-v')
		{
			DoGetVideoInfoTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-v]\n";
	}
?>
