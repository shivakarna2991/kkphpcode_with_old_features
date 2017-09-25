<?php

	namespace Core\Common\Classes;

	require_once 'core/core.php';
	require_once 'core/emailutils.php';

	use \Core\Common\Data\AccountRow;
	use \Core\Common\Data\AccountLinkRow;

	class AccountLinkManager
	{
		private $context = NULL;

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		function CreateLink(
			$accountId,
			$email,
			$type,
			$validityPeriod,
			&$accountLinkRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKMGR, __METHOD__, "accountId=$accountId, email=$email, type=$type, validityPeriod=$validityPeriod");

			// if ($this->context->account == NULL)
			// {
			// 	$resultString = "Login required";
			// 	DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
			//
			// 	DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKMGR, __METHOD__, FALSE);
			// 	return FALSE;
			// }

			$result = FALSE;
			$sqlError = 0;

			// Parameter validation
			$validParameters = TRUE;

			if ($accountId == NULL)
			{
				$resultString = "Missing parameter 'accountid'";
				$validParameters = FALSE;
			}
			else if ($email == NULL)
			{
				$resultString = "Missing parameter 'email'";
				$validParameters = FALSE;
			}
			else if ($type == NULL)
			{
				$resultString = "Missing parameter 'type'";
				$validParameters = FALSE;
			}
			else if ($validityPeriod == NULL)
			{
				$resultString = "Missing parameter 'validityperiod'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$now = time();
				$creationTime = date('Y-m-d H:i:s', $now);

				// Units for validity period is minutes
				$expirationTime = date('Y-m-d H:i:s', $now + ($validityPeriod * 60));

				$urlKey = GenerateUrlKey();

				//
				// Check if there is an existing link for this user.  If so, update it.  Otherwise
				// we'll create one.
				//
				$accountLinkRow = AccountLinkRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("accountid='$accountId'"),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($accountLinkRow != NULL)
				{
					$accountLinkRow->setUrlKey($urlKey);
					$accountLinkRow->setEmail($email);
					$accountLinkRow->setType($type);
					$accountLinkRow->setCreationTime($creationTime);
					$accountLinkRow->setExpirationTime($expirationTime);
					$accountLinkRow->setUsedTime("0000-00-00 00:00:00");
					$accountLinkRow->setState(ACCOUNTLINK_STATE_ACTIVE);
					$accountLinkRow->setUseCount(0);

					$result = $accountLinkRow->CommitChangedFields($sqlError);
				}
				else
				{
					$result = AccountLinkRow::Create(
							$this->context->dbcon,
							$accountId,
							$email,
							$urlKey,
							$type,
							$creationTime,
							$expirationTime,
							NULL,                 // usedtime
							0,                    // usecount
							ACCOUNTLINK_STATE_ACTIVE,
							$accountLinkRow,
							$sqlError
							);

					if (!$result)
					{
						$resultString = "SQL Error $sqlError.";
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKMGR, __METHOD__, $result);
			return $result;
		}

		function DeleteLink(
			$accountId,
			$urlKey,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKMGR, __METHOD__, "accountId=$accountId, urlKey=$urlKey");

			$result = FALSE;

			// Parameter validation
			$validParameters = TRUE;

			if ($accountId == NULL)
			{
				$resultString = "Missing parameter 'accountid'";
				$validParameters = FALSE;
			}
			else if ($urlKey == NULL)
			{
				$resultString = "Missing parameter 'urlkey'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$result = AccountLinkRow::Delete(
						$this->context->dbcon,
						array("accountid=$accountId",
						"urlkey='$urlKey'"),
						$sqlError
						);

				if (!$result)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKMGR, __METHOD__, $result);
			return $result;
		}

		public function GetAccountLinkFromUrl(
			$urlKey,
			&$accountLinkRow,
			&$accountRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKMGR, __METHOD__, "urlKey=$urlKey");

			$result = FALSE;
			$sqlError = 0;

			$accountLinkRow = AccountLinkRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("urlkey='$urlKey'"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if ($accountLinkRow != NULL)
			{
				// Make sure the link is active, not expired, and not exhausted it's maximum number of uses
				if ($accountLinkRow->getState() != ACCOUNTLINK_STATE_ACTIVE)
				{
					$resultString = "URL Key is not active";
					DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
				}
				else if (strtotime($accountLinkRow->getExpirationTime()) < time())
				{
					$resultString = "URL Key is expired";
					DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
				}
				else
				{
					// Increment the usage count
					$accountLinkRow->setUseCount($accountLinkRow->getUseCount() + 1);
					$accountLinkRow->CommitChangedFields($sqlError);

					$accountId = $accountLinkRow->getAccountId();

					// Ensure there is an account
					$accountRow = AccountRow::FindOne(
							$this->context->dbcon,
							NULL,
							array("accountid=$accountId"),
							NULL,
							ROW_OBJECT,
							$sqlError
							);

					if ($accountRow != NULL)
					{
						$result = TRUE;
					}
					else
					{
						$accountLinkRow = NULL;

						if ($sqlError != 0)
						{
							$resultString = "SQL error $sqlError";
							DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
						}
						else
						{
							$resultString = "Account not found";
							DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
						}
					}
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "AccountLink not found";
				DBG_ERR(DBGZ_ACCOUNTLINKMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
