<?php

	require_once 'idax.php';
	require_once '/home/core/classes/AccountManager.php';
	require_once '/home/core/classes/LoginManager.php';

	function DoLoginTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$accountManager = new AccountManager($con, NULL);

		$retval = $accountManager->CreateAccount(
				"testusername",
				"testpassword",
				"test@email.com",
				"TestFirstName",
				"TestLastName",
				$account
				);

		if ($retval)
		{
			$localHost = gethostbyname("localhost");

			$retval = $accountManager->Login(
					$account->getUsername(),
					$account->getPassword(),
					$localHost,
					$token
					);

			if ($retval)
			{
				$retval = $accountManager->ValidateToken($token, $localHost, $expired);

				if ($expired)
				{
					DBG_ERR(DBGZ_APP, __FUNCTION__, "Token expired");
				}
				else
				{
					DBG_INFO(DBGZ_APP, __FUNCTION__, "Token validated!");
				}
			}
			else
			{
				DBG_ERR(DBGZ_APP, __FUNCTION__, "accountManager->ValidateToken failed.");
			}
		}
		else
		{
			DBG_ERR(DBGZ_APP, __FUNCTION__, "accountManager->Login failed.");
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTROW | DBGZ_LOGINMGR | DBGZ_LOGINROW,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		echo "argv[1]='".$argv[1]."'\n";

		if ($argv[1] == '-l')
		{
			echo "Calling DoLoginTest()\n";
			DoLoginTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." -l\n";
	}
?>
