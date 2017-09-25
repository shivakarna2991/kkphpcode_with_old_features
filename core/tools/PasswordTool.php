<?php

	require_once '/home/core/core.php';

	use \Core\Common\Data\AccountRow;

	//require_once PROJECT_ROOT.'/utils/EmailScripts/PHPMailer/PHPMailerAutoload.php';

	$usage = FALSE;
	$accountid = NULL;
	$email = NULL;
	$setPassword = FALSE;
	$resetPassword = FALSE;

	// Retrieve command line parameters
	$params = getopt("i:e:s:r");

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	// Need user email or userid (not both)
	if (array_key_exists("i", $params))
	{
		if (array_key_exists("n", $params))
		{
			$usage = TRUE;
		}
		else
		{
			$accountid = $params["i"];
			$filter = array("accountid=$accountid");

			DBG_INFO(DBGZ_APP, "PasswordTool", "accountid=$accountid");
		}
	}
	else if (array_key_exists("e", $params))
	{
		$email = $params["e"];
		$filter = array("email='$email'");

		DBG_INFO(DBGZ_APP, "PasswordTool", "email=$email");
	}
	else
	{
		$usage = TRUE;
	}

	// Check if for "set" or "reset" flag (not both).
	if (array_key_exists("s", $params))
	{
		if (array_key_exists("r", $params))
		{
			$usage = TRUE;
		}
		else
		{
			$setPassword = TRUE;
			$newPassword = $params["s"];
		}
	}
	else if (array_key_exists("r", $params))
	{
		$resetPassword = TRUE;
	}

	if ($usage)
	{
		echo "Usage: {$argv[0]} [-i accountid | -e email] [-r | -s newpassword]\n";
		exit(0);
	}

	DBG_INFO(DBGZ_APP, "PasswordTool", "Connecting to database...");

	$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

	if (!mysqli_connect_errno($con))
	{
		DBG_INFO(DBGZ_APP, "PasswordTool", "Searching for account with filter=".serialize($filter));

		$accountRow = AccountRow::FindOne(
				$con,
				NULL,
				$filter,
				NULL,
				ROW_OBJECT,
				$sqlError = 0
				);
			
		if ($accountRow == NULL)
		{
			if ($sqlError != 0)
			{
				echo "SQL error $sqlError.\n";
			}
			else
			{
				echo "Account not found.\n";
			}
		}
		else if ($resetPassword)
		{
			$result = ResetUserPassword($accountRow);

			if ($result)
			{
				echo "Password for @".$accountRow->getEmail()." (".$accountRow->getFirstName()." ".$accountRow->getLastName().") was reset to '".$accountRow->getPassword()."'\n";
			}
			else
			{
				echo "Failed to reset password for @".$accountRow->getEmail()." (".$accountRow->getFirstName()." ".$accountRow->getLastName().")\n";
			}
		}
		else if ($setPassword)
		{
			$accountRow->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));

			if ($accountRow->CommitChangedFields())
			{
				echo "New password set for @".$accountRow->getEmail()." (".$accountRow->getFirstName()." ".$accountRow->getLastName().")\n";
			}
			else
			{
				echo "Failed to set password for @".$accountRow->getEmail()." (".$accountRow->getFirstName()." ".$accountRow->getLastName().")\n";
			}
		}

		mysqli_close($con);
	}
	else
	{
		echo "Failed to connect to USERs database. Error=".mysqli_connect_errno()."\n";
	}

	return;
?>
