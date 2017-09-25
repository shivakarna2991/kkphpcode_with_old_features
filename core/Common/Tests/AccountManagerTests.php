<?php

	require_once '/home/core/core.php';
	require_once '/home/core/LocalMethodCall.php';
	require_once '/home/core/classes/AccountManager.php';
	require_once '/home/core/classes/LoginManager.php';

	function DoCreateAccountTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mikesa_13@hotmail.com"'), NULL, ROW_OBJECT, $sqlError=0);

		$context = new MethodCallContext($con, $account, NULL);
		$accountManager = new AccountManager($context);

		$result = $accountManager->CreateAccount(
				"test@email.com",
				"USER",
				"TestFirstName",
				"TestLastName",
				$account,
				$resultString
				);

		if ($result)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "Succeeded to create account");
		}
		else
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "Failed to create account - resultString=$resultString");
		}

		mysqli_close($con);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoCreateAccountViaLMCTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$response = LocalMethodCall(
				"AccountManager",
				"CreateAccount",
				"127.0.0.1",
				array(
						"email" => "testuser@email.com",
						"role" => "USER",
						"firstname" => "TestFirstName",
						"lastname" => "TestLastName",
						"version" => "1.0",
						"build" => "100"
						),
				""   // user agent string
				);

		var_dump($response);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoGetAccountViaLMCTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$response = LocalMethodCall(
				"AccountManager",
				"GetAccount",
				"127.0.0.1",
				array(
						"token" => "b8750d6c7d53ebb3e0f0825046f6ab8e", 
						"version" => "1.0",
						"build" => "100"
						),
				""   // user agent string
				);

		var_dump($response);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoGetAccountsTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mikesa_13@hotmail.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$context = new MethodCallContext($con, $account, NULL);
			$accountManager = new AccountManager($context);

			$result = $accountManager->GetUserAccounts($accounts, $resultString);

			if ($result)
			{
				foreach ($accounts as &$account)
				{
					DBG_INFO(
							DBGZ_APP,
							__FUNCTION__,
							"Account email=".$account['email'].", firstname=".$account['firstname'].", lastname=".$account['lastname'].", role=".$account['role'].", state=".$account['state']
							);
				}
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Account::GetAccounts failed with ".serialize($result));
			}
		}

		mysqli_close($con);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoGetAccountsViaLMCTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$response = LocalMethodCall(
				"AccountManager",
				"GetUserAccounts",
				"127.0.0.1",
				array(
						"token" => "b8750d6c7d53ebb3e0f0825046f6ab8e", 
						"version" => "1.0",
						"build" => "100"
						),
				""   // user agent string
				);

		var_dump($response);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoUpdateUserAccountViaLMCTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$response = LocalMethodCall(
				"AccountManager",
				"UpdateUserAccount",
				"127.0.0.1",
				array(
						"token" => "b8750d6c7d53ebb3e0f0825046f6ab8e",
						"accountid" => "29",
						"firstname" => "Randy",
						"lastname" => "Kath",
						"version" => "1.0",
						"build" => "100"
						),
				""   // user agent string
				);

		var_dump($response);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function DoChangePasswordTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		// connect and verify connection
		$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		$account = Account::FindOne($con, NULL, array('email="mikesa_13@hotmail.com"'), NULL, ROW_OBJECT, $sqlError=0);

		if ($account != NULL)
		{
			$accountManager = new AccountManager($con, $account);

			$retval = $accountManager->ChangePassword("NewPassword1", "Bridges1");

			DBG_INFO(DBGZ_APP, __FUNCTION__, "Account::ChangePassword returned ".serialize($retval));
		}

		mysqli_close($con);

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}


	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTROW | DBGZ_LOGINMGR, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-c')
		{
			DoCreateAccountTest();
		}
		else if ($argv[1] == '-cvl')
		{
			DoCreateAccountViaLMCTest();
		}
		else if ($argv[1] == '-p')
		{
			DoChangePasswordTest();
		}
		else if ($argv[1] == '-gavl')
		{
			DoGetAccountViaLMCTest();
		}
		else if ($argv[1] == '-la')
		{
			DoGetAccountsTest();
		}
		else if ($argv[1] == '-lavl')
		{
			DoGetAccountsViaLMCTest();
		}
		else if ($argv[1] == '-uavl')
		{
			DoUpdateUserAccountViaLMCTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-c | | -cvl | -p | -gavl | -la | -lavl | -uavl]\n";
	}
?>
