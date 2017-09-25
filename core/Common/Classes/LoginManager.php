<?php

	namespace Core\Common\Classes;

	//require_once 'core/core.php';
	require_once __DIR__.'/../../../core/core.php';
	//require_once '../core/core.php';

	use \Core\Common\Data\LoginRow;

	class LoginManager
	{
		private $dbcon = NULL;
		private $accountRow = NULL;

		public function __construct(
			$dbcon,
			$accountRow
			)
		{
			$this->dbcon = $dbcon;
			$this->accountRow = $accountRow;
		}

		private function CheckForExistingLogin(
			$location
			)
		{
			
		}

		public function Login(
			$location,
			&$loginTime,
			&$token,
			&$tokenValidityPeriod,
			&$resultString
			)
		{        
                            //echo 'params:--location-'.$location.'--$loginTime-'.$loginTime.'--$token-'.$token.'--$tokenValidityPeriod-'.$tokenValidityPeriod.'--$resultString-'.$resultString;
                    //echo 'dddd';exit;
			DBG_ENTER(DBGZ_LOGINMGR, __METHOD__, "location=$location");

			$result = FALSE;

			if (!LOGIN_OPTION_ALLOW_MULTIPLE_LOGINS)
			{
				// Force logout of any existing sessions
				$this->ForceLogout($this->accountRow->getAccountId(), FALSE);
			}

			$date = date('Y-m-d H:i:s');
			$now = time();

			// Create a token good for 20 minutes
			$token = md5(microtime().rand());
			$tokenValidityPeriod = LOGIN_OPTION_TOKEN_VALIDITY_PERIOD;
			$tokenExpiration = $now + $tokenValidityPeriod;
                       // echo 'params:--location-'.$location.'--$loginTime-'.$loginTime.'--$token-'.$token.'--$tokenValidityPeriod-'.$tokenValidityPeriod.'--$resultString-'.$resultString;
                        //exit;
			$result = LoginRow::Create(
					$this->dbcon,
					$this->accountRow->getAccountId(),
					$date,
					//0,
					'0000-00-00 00:00:00',
					$token,
					$tokenExpiration,
					$location,
					$loginRow,
					$sqlError
					);
                                       // echo '<pre>';print_r($result);exit;
			if ($loginRow != NULL)
			{
				$result = TRUE;
				$loginTime = $date;
			}
			else
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_LOGINMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_LOGINMGR, __METHOD__, $result);
			return $result;
		}

		public function Logout(
			$token,
			$location
			)
		{
			DBG_ENTER(DBGZ_LOGINMGR, __METHOD__, "location=$location");

			if (LOGIN_OPTION_RETAIN_LOGINS_FOR_AUDITING)
			{
				$date = date('Y-m-d H:i:s');
				LoginRow::Update($this->dbcon, array("loggedouttime='$date'", "token=''"), array("token='$token'"));
			}
			else
			{
				LoginRow::Delete($this->dbcon, array("token='$token'"));
			}

			DBG_RETURN_BOOL(DBGZ_LOGINMGR, __METHOD__, TRUE);
			return TRUE;
		}

		public function ForceLogout(
			$accountId,
			$excludeThisSession
			)
		{
			DBG_ENTER(DBGZ_LOGINMGR, __METHOD__, "accountId=$accountId");
			
			if (LOGIN_OPTION_RETAIN_LOGINS_FOR_AUDITING)
			{
				$date = date('Y-m-d H:i:s');
				LoginRow::Update($this->dbcon, array("loggedouttime='$date'", "token=''"), array("accountid=$accountId"));
			}
			else
			{
				LoginRow::Delete($this->dbcon, array("accountid=$accountId"));
			}

			DBG_RETURN(DBGZ_LOGINMGR, __METHOD__);
		}

		public static function ValidateToken(
			$dbcon,
			$token,
			$location,
			&$accountid,
			&$tokenExpired
			)
		{
                            //echo 'dsaddsa';exit;
			DBG_ENTER(DBGZ_LOGINMGR, __METHOD__);

			$accountid = NULL;
			$tokenExpired = NULL;

			$retval = FALSE;

			// Try to find the token in the logins table.
			$loginRow = LoginRow::FindOne(
					$dbcon,
					NULL,
					array("token='$token'"),
					NULL,
					ROW_OBJECT
					);

			if ($loginRow != NULL)
			{
				$retval = TRUE;

				// Get the user account associated with this account.
				$accountid = $loginRow->getAccountId();

				$now = time();

				// Check if the session has not been logged out
				if ($loginRow->getLoggedOutTime() != "0000-00-00 00:00:00")
				{
					$tokenExpired = TRUE;

					DBG_INFO(DBGZ_LOGINMGR, __METHOD__, "Session was logged out!");
				}
				else if ($location != $loginRow->getLoggedInLocation())
				{
					$tokenExpired = TRUE;

					DBG_INFO(DBGZ_LOGINMGR, __METHOD__, "Token found but location is different!");
				}
				else if ($now > $loginRow->getTokenExpirationTime())
				{
					$tokenExpired = TRUE;

					DBG_INFO(DBGZ_LOGINMGR, __METHOD__, "Token expired");
				}
				else
				{
					$tokenExpired = FALSE;

					if (LOGIN_OPTION_EXTEND_TOKEN_VALIDITY_PERIOD)
					{
						// Extend the token validity period.
						$loginRow->setTokenExpirationTime($now + LOGIN_OPTION_TOKEN_VALIDITY_PERIOD);
						$loginRow->CommitChangedFields();
					}
				}
			}
			else
			{
				DBG_INFO(DBGZ_LOGINMGR, __METHOD__, "Token not found");
			}

			DBG_RETURN_BOOL(DBGZ_LOGINMGR, __METHOD__, $retval);
			return $retval;
		}
	}
?>
