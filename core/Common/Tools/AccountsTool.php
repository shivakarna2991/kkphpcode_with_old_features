<?php

	require_once '/home/core/core.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/DBParams.php';

	use \core\Common\Data\AccountRow;

	function BackupAccounts(
		$dbcon,
		$filename
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "filename=$filename");

		$accountRows = AccountRow::Find($dbcon, NULL, NULL, NULL, ROW_ASSOCIATIVE, $sqlError);

		if ($accountRows)
		{
			file_put_contents($filename, json_encode($accountRows));
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function RestoreAccounts(
		$dbcon,
		$filename
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__, "filename=$filename");

		$accountRows = json_decode(file_get_contents($filename), TRUE);

		foreach ($accountRows as &$accountRow)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "Restoring account ".$accountRow["email"]);

			AccountRow::Create(
					$dbcon,
					$accountRow["email"],
					$accountRow["state"],
					$accountRow["creationtime"],
					$accountRow["registeredtime"],
					$accountRow["lastlogintime"],
					$accountRow["firstname"],
					$accountRow["lastname"],
					$accountRow["passwordhash"],
					$accountRow["failedloginattempts"],
					$accountRow["role"],
					$accountRow["rating"],
					$accountRow["developer"],
					$accountRow["accountObject"],
					$sqlError
					);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	$usage = FALSE;

	if ($argc != 3)
	{
		$usage = TRUE;
	}
	else
	{
		$dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		switch ($argv[1])
		{
			case "-b":
				BackupAccounts($dbcon, $argv[2]);
				break;

			case "-r":
				RestoreAccounts($dbcon, $argv[2]);
				break;
				break;

			default:
				$usage = TRUE;
				break;
		}

		mysqli_close($dbcon);
	}

	if ($usage)
	{
		echo "Usage: ".$argv[0]." -b | -r filename".PHP_EOL;
	}
?>
