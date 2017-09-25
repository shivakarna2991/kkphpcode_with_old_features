<?php
	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Data\AccountRow;
	use \Idax\Common\Classes\Device;

	function DoDeviceQueryStatusTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		if ($accountRow != NULL)
		{
			$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
			$device = new Device($context);

			$result = $device->QueryStatus(1, $status, $resultString);

			var_dump($status);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_DEVICE | DBGZ_DEVICEROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal, TRUE);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-qs')
		{
			DoDeviceQueryStatusTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-qs]\n";
	}
?>
