<?php

	namespace Core\Common\Classes;

	require_once 'core/core.php';
	require_once 'core/emailutils.php';

	use \Core\Common\Data\AccountRow;
	use \Core\Common\Classes\LoginManager;
	use \Core\Common\Classes\AccountLinkManager;

	class AccountManager
	{
		private $context = NULL;
		private static $accountStateStrings = array(
				ACCOUNT_STATE_REGISTERING          => "REGISTERING",
				ACCOUNT_STATE_INACTIVE             => "INACTIVE",
				ACCOUNT_STATE_ACTIVE               => "ACTIVE",
				ACCOUNT_STATE_LOCKED               => "LOCKED",
				ACCOUNT_STATE_VALIDATING_EMAIL     => "VALIDATING",
				ACCOUNT_STATE_SETPASSWORD_REQUIRED => "SETPASSWORD"
				);

		private static $accountRoleStrings = array(
				ACCOUNT_ROLE_ANONYMOUS        => "ANONYMOUS",
				ACCOUNT_ROLE_USER             => "USER",
				ACCOUNT_ROLE_QUALITYCONTROL   => "QC",
				ACCOUNT_ROLE_DESIGNER         => "DESIGNER",
				ACCOUNT_ROLE_PROJECTMANAGER   => "PROJECT MANAGER",
				ACCOUNT_ROLE_ADMIN            => "ADMIN"
				);

		private static $accountRoles = array(
				"ANONYMOUS"       => ACCOUNT_ROLE_ANONYMOUS,
				"USER"            => ACCOUNT_ROLE_USER,
				"QC"              => ACCOUNT_ROLE_QUALITYCONTROL,
				"DESIGNER"        => ACCOUNT_ROLE_DESIGNER,
				"PROJECT MANAGER" => ACCOUNT_ROLE_PROJECTMANAGER,
				"ADMIN"           => ACCOUNT_ROLE_ADMIN
				);

		private static $emailTypes = array(
				"validateemail" => array("subject" => "IDAX: Confirm Email Address", "body" => "core/EmailTemplates/ValidateEmail.html"),
				"registeruser" => array("subject" => "IDAX: Account Registration", "body" => "core/EmailTemplates/RegisterUser.html"),
				"resetpassword" => array("subject" => "IDAX: Reset Password", "body" => "core/EmailTemplates/ResetPassword.html")
				);

		private static $emailTypesByAccountState = array(
				ACCOUNT_STATE_REGISTERING => "registering",
				ACCOUNT_STATE_SETPASSWORD_REQUIRED => "forgotpassword"
				);

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "methodName=$methodName");

			$accountManager = new AccountManager($context);

			$response = "failed";
			$responder = "AccountManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateAccount':
					$result = $accountManager->CreateAccount(
							isset($parameters['email']) ? $parameters['email'] : NULL,
							isset($parameters['role']) ? $parameters['role'] : NULL,
							isset($parameters['rating']) ? $parameters['rating'] : NULL,
							isset($parameters['firstname']) ? $parameters['firstname'] : NULL,
							isset($parameters['lastname']) ? $parameters['lastname'] : NULL,
							$accountRow,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"accountid" => $accountRow->getAccountId(),
								"resultstring" => $resultString
								);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'SendPasswordResetLink':
					$result = $accountManager->SendPasswordResetLink(
							isset($parameters['email']) ? $parameters['email'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'ChangePassword':
					$result = $accountManager->ChangePassword(
							isset($parameters['oldpassword']) ? $parameters['oldpassword'] : NULL,
							isset($parameters['newpassword']) ? $parameters['newpassword'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'UpdateAccount':
					$result = $accountManager->UpdateAccount(
							isset($parameters['firstname']) ? $parameters['firstname'] : NULL,
							isset($parameters['lastname']) ? $parameters['lastname'] : NULL,
							isset($parameters['email']) ? $parameters['email'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'UpdateUserAccount':
					$result = $accountManager->UpdateUserAccount(
							isset($parameters['accountid']) ? $parameters['accountid'] : NULL,
							isset($parameters['email']) ? $parameters['email'] : NULL,
							isset($parameters['firstname']) ? $parameters['firstname'] : NULL,
							isset($parameters['lastname']) ? $parameters['lastname'] : NULL,
							isset($parameters['role']) ? $parameters['role'] : NULL,
							isset($parameters['rating']) ? $parameters['rating'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'GetAccount':
					$result = $accountManager->GetAccount(
							$account,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"account" => $account,
								"resultstring" => $resultString
								);
					}
					else
					{
						$returnval = array(
								"resultstring" => $resultString
								);
					}

					break;

				case 'GetUserAccount':
					$result = $accountManager->GetUserAccount(
							isset($parameters['accountid']) ? $parameters['accountid'] : NULL,
							$accountRow,
							$resultString
							);

					if ($result)
					{
						// Need to map account and role to a string
						$accountRow['role'] = AccountManager::$accountRoleStrings[$accountRow['role']];

						$response = "success";
						$returnval = array(
								"account" => $accountRow,
								"resultstring" => $resultString
								);
					}
					else
					{
						$returnval = array(
								"resultstring" => $resultString
								);
					}

					break;

				case 'GetUserAccounts':
					$result = $accountManager->GetUserAccounts(
							$accountRows,
							$resultString
							);

					if ($result)
					{
						// Need to map account states and roles to strings
						foreach ($accountRows as &$accountRow)
						{
							$accountRow['state'] = AccountManager::$accountStateStrings[$accountRow['state']];
							$accountRow['role'] = AccountManager::$accountRoleStrings[$accountRow['role']];
						}

						$response = "success";
						$returnval = array(
								"accounts" => $accountRows,
								"resultstring" => $resultString
								);
					}
					else
					{
						$returnval = array(
								"resultstring" => $resultString
								);
					}

					break;

				case 'ActivateUserAccount':
					$result = $accountManager->ActivateUserAccount(
							isset($parameters['accountid']) ? $parameters['accountid'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'DeactivateUserAccount':
					$result = $accountManager->DeactivateUserAccount(
							isset($parameters['accountid']) ? $parameters['accountid'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'Login':
					// Validate parameters
					if (!isset($parameters['email']))
					{
						$returnval = array("resultstring" => "Missing parameter 'email'");
					}
					else if (!isset($parameters['password']))
					{
						$returnval = array("resultstring" => "Missing parameter 'password'");
					}
					else
					{
						$result = $accountManager->Login(
								$parameters['email'],
								$parameters['password'],
								$context->location,
								$accountRow,
								$token,
								$tokenValidityPeriod,
								$resultString
								);

						if ($result)
						{
							$response = "success";
							$returnval = array(
									"firstname" => $accountRow->getFirstName(),
									"lastname" => $accountRow->getLastName(),
									"role" => $accountRow->getRole(),
									"rating" => $accountRow->getRating(),
									"token" => $token,
									"tokenvalidityperiod" => $tokenValidityPeriod
									);
						}
						else
						{
							$returnval = array("resultstring" => $resultString);
						}
					}

					break;

				case 'Logout':
					$result = $accountManager->Logout($context->location);

					if ($result)
					{
						$response = "success";
						$returnval = array();
					}

					break;

				case 'ExecuteURL':
					$result = $accountManager->ExecuteURL(
							isset($parameters['urlkey']) ? $parameters['urlkey'] : NULL,
							$type,
							$nextAction,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"type" => $type,
								"nextaction" => $nextAction,
								"resultstring" => $resultString
								);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'ExecuteURLSetPassword':
					$result = $accountManager->ExecuteURLSetPassword(
							isset($parameters['urlkey']) ? $parameters['urlkey'] : NULL,
							isset($parameters['password']) ? $parameters['password'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				default:
					$response = "failed";
					$responder = "AccountManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_ACCOUNTMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
			  				'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_ACCOUNTMGR, __METHOD__);
			return $response_str;
		}

		public static function IsValidEmailAddress(
			$email
			)
		{
			return TRUE;
		}

		public static function IsValidPassword(
			$email
			)
		{
			return TRUE;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		private function SendUserEmailWithUrl(
			$accountRow,
			$email,
			$urlKey,
			$subject,
			$bodyFilename,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "urlKey=$urlKey, subject=$subject");

			$year = date("Y");
			#$webPortalURL = "https://collect.idaxdata.com";
			#$webPortalURL = "http://52.42.214.56";
			$webPortalURL = "http://localhost";

			$body = file_get_contents($bodyFilename);

			$body = str_replace(
					array("@FIRSTNAME@", "@LASTNAME@", "@URLKEY@", "@WEBPORTALURL@", "@YEAR@"),
					array($accountRow->getFirstName(), $accountRow->getLastName(), $urlKey, $webPortalURL, $year),
					$body
					);

			$result = SendEmail(
					($email != NULL) ? $email : $accountRow->getEmail(),
					$accountRow->getFirstName(),
					$accountRow->getLastName(),
					"no-reply@idaxdata.com",
					$subject,
					$body,
					NULL,           // Attachments
					$resultString
					);

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
		}

		public function CreateAccount(
			$email,
			$role,
			$rating,
			$firstName,
			$lastName,
			&$accountRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "email=$email, role=$role, rating=$rating, firstName=$firstName, lastName=$lastName");

			// User must be ADMIN or higher to create an account.
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

			$accountid = NULL;

			// Parameter validation
			$validParameters = TRUE;

			if ($email == NULL)
			{
				$resultString = "Missing required parameter 'email'";
				$validParameters = FALSE;
			}
			else if (!AccountManager::IsValidEmailAddress($email))
			{
				$resultString = "Invalid value specified for 'email' parameter";
				$validParameters = FALSE;
			}
			else
			{
				// Map the caller-specified role string to appropriate role value.
				if ($role === NULL)
				{
					$role = "USER";
				}

				if (isset(AccountManager::$accountRoles[$role]))
				{
					$role = AccountManager::$accountRoles[$role];

					if ($role == ACCOUNT_ROLE_ANONYMOUS)
					{
						$validParameters = FALSE;
						$resultString = "Invalid value specified for 'role' parameter";
					}
				}
				else
				{
					$validParameters = FALSE;
					$resultString = "Invalid value specified for 'role' parameter";
				}
			}

			if ($validParameters)
			{
				if ($rating === NULL)
				{
					$rating = "1";
				}

				$result = AccountRow::Create(
						$this->context->dbcon,
						$email,
						ACCOUNT_STATE_REGISTERING,   // state
						date('Y-m-d H:i:s'),         // creationtime
						"",                          // registeredtime
						"",                          // lastlogintime
						$firstName,
						$lastName,
						NULL,                        // passwordhash
						0,                           // Number of failed login attempts
						$role,
						$rating,
						FALSE,                       // developer
						$accountRow,
						$sqlError
						);

				if ($result)
				{
					$accountLinkManager = new AccountLinkManager($this->context);

					// Create a URL the user can click to validate the email address is correct.
					$result = $accountLinkManager->CreateLink(
							$accountRow->getAccountId(),
							$accountRow->getEmail(),
							"account_registration",   // type
							24 * 60,                  // validity period in minutes
							$accountLinkRow,
							$resultString
							);

					// Now send email to the user with the URL.
					$this->SendUserEmailWithUrl(
							$accountRow,
							NULL,
							$accountLinkRow->getUrlKey(),
							AccountManager::$emailTypes["registeruser"]["subject"],
							AccountManager::$emailTypes["registeruser"]["body"],
							$resultString
							);
				}
				else
				{
					$resultString = "SQL Error $sqlError.";
				}
			}

			DBG_RETURN_RESULT(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function DeleteAccountByAccountId(
			$accountid,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "accountid=$accountid");

			$retval = FALSE;

			// User must be ADMIN or higher to delete an account.
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

			$retval = AccountRow::Delete($this->context->dbcon, "accountid==$accountid");

			DBG_RETURN_RESULT(DBGZ_ACCOUNTMGR, __METHOD__, $retval);
			return $retval;
		}

		public function ExecuteURL(
			$urlKey,
			&$type,
			&$nextAction,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "urlKey=$urlKey");

			$result = FALSE;

			// Validate parameters
			// Validate parameters
			$validParameters = TRUE;

			if ($urlKey == NULL)
			{
				$resultString = "Missing parameter 'urlkey'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$accountLinkManager = new AccountLinkManager($this->context);

				$result = $accountLinkManager->GetAccountLinkFromUrl($urlKey, $accountLinkRow, $accountRow, $resultString);

				if ($result)
				{
					$type = $accountLinkRow->getType();

					switch ($type)
					{
						case "account_registration":
							$accountRow->setState(ACCOUNT_STATE_SETPASSWORD_REQUIRED);
							$accountRow->CommitChangedFields($sqlError);
							$nextAction = "set_password";
							break;

						case "validate_email":
							$accountRow->setState(ACCOUNT_STATE_ACTIVE);
							$accountRow->setEmail($accountLinkRow->getEmail());
							$accountRow->CommitChangedFields($sqlError);
							$nextAction = "none";

							$accountLinkManager->DeleteLink($accountRow->getAccountId(), $urlKey, $resultString);
							break;

						case "reset_password":
							$nextAction = "set_password";
							break;
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function ExecuteURLSetPassword(
			$urlKey,
			$password,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "urlkey=$urlKey");

			// Validate parameters
			$validParameters = TRUE;

			if ($urlKey == NULL)
			{
				$resultString = "Missing parameter 'urlkey'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}
			else if ($password == NULL)
			{
				$resultString = "Missing parameter 'password'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$accountLinkManager = new AccountLinkManager($this->context);

				$result = $accountLinkManager->GetAccountLinkFromUrl($urlKey, $accountLinkRow, $accountRow, $resultString);

				if ($result)
				{
					$accountRow->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));
					$accountRow->setFailedLoginAttempts(0);

					if ($accountRow->getState() == ACCOUNT_STATE_SETPASSWORD_REQUIRED)
					{
						$accountRow->setState(ACCOUNT_STATE_ACTIVE);
						$accountRow->setRegisteredTime(date("Y-m-d H:i:s"));
					}

					$accountRow->CommitChangedFields($sqlError);

					$accountLinkManager->DeleteLink($accountRow->getAccountId(), $urlKey, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function SendPasswordResetLink(
			$email,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "email=$email");

			$result = FALSE;
			$sqlError = 0;

			// Validate parameters
			$validParameters = TRUE;

			if ($email == NULL)
			{
				$resultString = "Missing parameter 'email'";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			//
			// This is an unsecured API (no auth token / account needed).
			//
			// However we do need to validate that the email address is in our database.
			//
			$accountRow = AccountRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("email='$email'"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if (($accountRow != NULL) && ($accountRow->getState() == ACCOUNT_STATE_ACTIVE))
			{
				$accountLinkManager = new AccountLinkManager($this->context);

				// Create a URL the user can click to validate the email address is correct.
				$result = $accountLinkManager->CreateLink(
						$accountRow->getAccountId(),
						$email,
						"reset_password",   // type
						24 * 60,            // validity period in minutes
						$accountLinkRow,
						$resultString
						);

				// Now send email to the user with the URL.
				$this->SendUserEmailWithUrl(
						$accountRow,
						NULL,
						$accountLinkRow->getUrlKey(),
						AccountManager::$emailTypes["resetpassword"]["subject"],
						AccountManager::$emailTypes["resetpassword"]["body"],
						$resultString
						);
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
			}
			else
			{
				$resultString = "Account not found";
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function ChangePassword(
			$oldPassword,
			$newPassword,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__);

			$retval = FALSE;

			//
			// To change the password, we must have an account that is active.  And the oldPassword
			// parameter must match the current password.
			//
			// Validate parameters
			$validParameters = TRUE;

			if ($oldPassword == NULL)
			{
				$resultString = "Missing parameter 'oldpasword'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}
			else if ($newPassword == NULL)
			{
				$resultString = "Missing parameter 'newpasword'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				if ($this->context->account != NULL)
				{
					if ($this->context->account->getState() == ACCOUNT_STATE_ACTIVE)
					{
						if (password_verify($oldPassword, $this->context->account->getPasswordHash()))
						{
							$this->context->account->setPasswordHash(password_hash($newPassword, PASSWORD_DEFAULT));
							$this->context->account->setFailedLoginAttempts(0);

							$retval = $this->context->account->CommitChangedFields($sqlError);
						}
						else
						{
							$resultString = "Old password is inccorrect";
							DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
						}
					}
					else
					{
						$resultString = "Account is not active";
						DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
					}
				}
				else
				{
					$resultString = "Login required";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_RESULT(DBGZ_ACCOUNTMGR, __METHOD__, $retval);
			return $retval;
		}

		public function UpdateAccount(
			$firstName,
			$lastName,
			$email,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__);

			$result = TRUE;

			//
			// To change the password, we must have an account that is active.  And the oldPassword
			// parameter must match the current password.
			//
			if ($this->context->account != NULL)
			{
				if ($this->context->account->getState() == ACCOUNT_STATE_ACTIVE)
				{
					if ($firstName != NULL)
					{
						$this->context->account->setFirstName($firstName);
					}

					if ($lastName != NULL)
					{
						$this->context->account->setLastName($lastName);
					}

					if ($email != NULL && ($email != $this->context->account->getEmail()))
					{
						$accountLinkManager = new AccountLinkManager($this->context);

						// Create a URL the user can click to validate the email address is correct.
						$result = $accountLinkManager->CreateLink(
								$this->context->account->getAccountId(),
								$email,
								"validate_email",   // type
								24 * 60,            // validity period in minutes
								$accountLinkRow,
								$resultString
								);

						// Now send email to the user with the URL.
						$this->SendUserEmailWithUrl(
								$this->context->account,
								$email,
								$accountLinkRow->getUrlKey(),
								AccountManager::$emailTypes["validateemail"]["subject"],
								AccountManager::$emailTypes["validateemail"]["body"],
								$resultString
								);
					}

					$this->context->account->CommitChangedFields($sqlError);
				}
				else
				{
					$result = FALSE;

					$resultString = "Account is not active";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
			}
			else
			{
				$result = FALSE;

				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function UpdateUserAccount(
			$accountId,
			$email,
			$firstName,
			$lastName,
			$role,
			$rating,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "accountId=$accountId, email=$email, firstName=$firstName, lastName=$lastName, role=$role");

			$result = FALSE;
			$sqlError = 0;

			// User must be ADMIN or higher to update an account
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($accountId == NULL)
			{
				$resultString = "Missing parameter 'accountid'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}

			//
			// If role is set, check that it's a valid value and, if so, map it from
			// the string value to the appropriate integer value.
			//
			if ($role != NULL)
			{
				if (isset(AccountManager::$accountRoles[$role]))
				{
					$role = AccountManager::$accountRoles[$role];

					if ($role == ACCOUNT_ROLE_ANONYMOUS)
					{
						$validParameters = FALSE;
						$resultString = "Invalid value specified for 'role' parameter";
					}
				}
				else
				{
					$validParameters = FALSE;
					$resultString = "Invalid value specified for 'role' parameter";
				}
			}

			if ($validParameters)
			{
				$accountRow = AccountRow::FindOne(
						$this->context->dbcon,
						array("accountid", "email", "firstname", "lastname", "role"),
						array("accountid=$accountId"),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($accountRow)
				{
					if ($firstName != NULL)
					{
						$accountRow->setFirstName($firstName);
					}

					if ($lastName != NULL)
					{
						$accountRow->setLastName($lastName);
					}

					if ($role != NULL)
					{
						$accountRow->setRole($role);
					}

					if ($rating != NULL)
					{
						$accountRow->setRating($rating);
					}

					if ($email != NULL)
					{
						// Before setting, check if the email value is different than the current value.
						if ($email != $accountRow->getEmail())
						{
							$accountRow->setEmail($email);

							// In addition to changing the email address, we also need to update the account state
							// and create an "email validation" url and send it to the new email address.
							if ($accountRow->getState() != ACCOUNT_STATE_REGISTERING)
							{
								$accountRow->setState(ACCOUNT_STATE_VALIDATING_EMAIL);

								// Create email vaildation url and send it to the user.
							}

							$accountLinkManager = new AccountLinkManager($this->context);

							// Create a URL the user can click to validate the email address is correct.
							$result = $accountLinkManager->CreateLink(
									$accountRow->getAccountId(),
									$email,
									"validate_email",         // type
									24 * 60,                  // validity period in minutes
									$accountLinkRow,
									$resultString
									);

							// Now send email to the user with the URL.
							$this->SendUserEmailWithUrl(
									$accountRow,
									NULL,
									$accountLinkRow->getUrlKey(),
									AccountManager::$emailTypes["validateemail"]["subject"],
									AccountManager::$emailTypes["validateemail"]["body"],
									$resultString
									);

							// Last step - invalidate any existing login sessions for this account
							$loginManager = new LoginManager($this->context->dbcon, $accountRow);
							$loginManager->ForceLogout($accountRow->getAccountId(), FALSE);
						}
					}

					$accountRow->CommitChangedFields($sqlError);

					$result = TRUE;
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError'";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Account not found";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function GetAccount(
			&$account,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__);

			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$account = array(
					"email" => $this->context->account->getEmail(),
					"firstname" => $this->context->account->getFirstName(),
					"lastname" => $this->context->account->getLastName()
					);

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, TRUE);
			return TRUE;
		}

		public function GetUserAccount(
			$accountId,
			&$account,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "accountId=$accountId");

			$result = FALSE;
			$account = NULL;
			$sqlError = 0;

			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($accountId == NULL)
			{
				$resultstring = "Missing parameter 'accountid'";

				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$account = AccountRow::FindOne(
						$this->context->dbcon,
						array("accountid", "email", "firstname", "lastname", "role", "rating"),
						array("accountid=$accountId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($account)
				{
					$result = TRUE;
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Account not found";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function GetUserAccounts(
			&$accountRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__);

			$result = FALSE;
			$sqlError = 0;

			// User must be ADMIN or higher to list accounts.
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

			$accountRows = AccountRow::Find(
					$this->context->dbcon,
					array("accountid", "email", "state", "creationtime", "registeredtime", "lastlogintime", "firstname", "lastname", "role", "rating"),
					NULL,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($accountRows != NULL)
			{
				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "No accounts found";
				DBG_WARN(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function ActivateUserAccount(
			$accountId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "accountid=$accountId");
			
			$retval = FALSE;
			$sqlError = 0;

			// User must be ADMIN or higher to activate an account.
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

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
				//
				// Activating an account goes back to "registering" state, same as when the
				// account was created.  The user will have to re-validate the email account
				// and set a new password.
				//
				$accountRow->setState(ACCOUNT_STATE_REGISTERING);
				$accountRow->setFailedLoginAttempts(0);
				$accountRow->CommitChangedFields($sqlError);

				$accountLinkManager = new AccountLinkManager($this->context);

				// Create a URL the user can click to validate the email address is correct.
				$result = $accountLinkManager->CreateLink(
						$accountRow->getAccountId(),
						$accountRow->getEmail(),
						"account_registration",   // type
						24 * 60,                  // validity period in minutes
						$accountLinkRow,
						$resultString
						);

				// Now send email to the user with the URL.
				$this->SendUserEmailWithUrl(
						$accountRow,
						NULL,
						$accountLinkRow->getUrlKey(),
						AccountManager::$emailTypes["registeruser"]["subject"],
						AccountManager::$emailTypes["registeruser"]["body"],
						$resultString
						);

				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Account not found";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function DeactivateUserAccount(
			$accountId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "accountid=$accountId");

			$retval = FALSE;
			$sqlError = 0;

			// User must be ADMIN or higher role to deactivate an account.
			if ($this->context->account == NULL)
			{
				$resultString = "Login required";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else
			{
				if ($this->context->account->getRole() < ACCOUNT_ROLE_ADMIN)
				{
					$resultString = "Access denied";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

					DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, FALSE);
					return FALSE;
				}
			}

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
				$accountRow->setState(ACCOUNT_STATE_INACTIVE);
				$accountRow->setFailedLoginAttempts(0);
				$accountRow->CommitChangedFields($sqlError);

				// Force logout of any existing login sessions for this user
				$loginManager = new LoginManager($this->context->dbcon, $accountRow);
				$loginManager->ForceLogout($accountId, FALSE);
				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Account not found";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public static function ValidateToken(
			$dbcon,
			$token,
			$location,
			&$accountRow,
			&$expired
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__);

			$result = LoginManager::ValidateToken($dbcon, $token, $location, $accountid, $expired);

			if ($result)
			{
				if (!$expired)
				{
					$sqlError = 0;

					$accountRow = AccountRow::FindOne(
							$dbcon,
							NULL,
							array("accountid=$accountid"),
							NULL,
							ROW_OBJECT,
							$sqlError
							);

					if ($accountRow != NULL)
					{
						$result = TRUE;
					}
					else if ($sqlError != 0)
					{
						DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, "SQL error $sqlError");
					}
					else
					{
						DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, "Account not found");
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTMGR, __METHOD__, $result);
			return $result;
		}

		public function Login(
			$email,
			$password,
			$location,
			&$accountRow,
			&$token,
			&$tokenValidityPeriod,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "email=$email, location=$location");

			$retval = FALSE;
			$sqlError = 0;

			$accountRow = AccountRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("email='$email'"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if ($accountRow != NULL)
			{
				if ($accountRow->getState() == ACCOUNT_STATE_ACTIVE)
				{
					if (password_verify($password, $accountRow->getPasswordHash()))
					{
						//
						// Found the account and verified it is activated and the password is correct.
						// Now create an entry in the logins table.
						//
						$loginManager = new LoginManager($this->context->dbcon, $accountRow);

						$retval = $loginManager->Login($location, $loginTime, $token, $tokenValidityPeriod, $resultString);
                                                 //echo 'aaaa1'; print_r($retval); exit;
						if ($retval)
						{
							$firstname = $accountRow->getFirstName();
							$lastname = $accountRow->getLastName();

							$accountRow->setLastLoginTime($loginTime);
							$accountRow->setFailedLoginAttempts(0);
                          //  echo "<pre>";print_r($accountRow);echo"</pre>";
							DBG_INFO(DBGZ_ACCOUNTMGR, __METHOD__, "Successful login for email=$email from location=$location");
						}
					}
					else
					{
						$resultString = "Invalid email/password combination.";
						DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);

						$failedLoginAttempts = $accountRow->getFailedLoginAttempts() + 1;

						if ($failedLoginAttempts >= LOGIN_OPTION_ALLOWED_FAILED_LOGIN_ATTEMPTS)
						{
							$accountRow->setState(ACCOUNT_STATE_LOCKED);
						}

						$accountRow->setFailedLoginAttempts($failedLoginAttempts);
					}
				}
				else
				{
					$resultString = "Account not active for $email.";
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
				}

				$accountRow->CommitChangedFields($sqlError);
			}
			else if ($sqlError != 0)
			{
                                                                    //echo '111dsdsdsadas';exit;
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Account not found.";
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, $resultString);
			}
                                                               // echo '123dsdsdsadas';exit;
			DBG_RETURN(DBGZ_ACCOUNTMGR, __METHOD__);
			return $retval;	
		}

		public function Logout(
			$location
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTMGR, __METHOD__, "location=$location");

			$result = FALSE;

			if ($this->context->account != NULL)
			{
				$loginManager = new LoginManager($this->context->dbcon, $this->context->account);

				$result = $loginManager->Logout($this->context->token, $location);

				if ($result)
				{
					DBG_INFO(DBGZ_ACCOUNTMGR, __METHOD__, "Successful logout for email={$this->context->account->getEmail()} from location=$location");
				}
				else
				{
					DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, "Logout failed for email={$this->context->account->getEmail()} from location=$location");
				}
			}
			else
			{
				DBG_ERR(DBGZ_ACCOUNTMGR, __METHOD__, "No account!");
			}

			DBG_RETURN(DBGZ_ACCOUNTMGR, __METHOD__);
			return $result;	
		}
	}
?>
