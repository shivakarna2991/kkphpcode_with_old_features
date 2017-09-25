<?php

	namespace Core\Common\Classes;

	require_once 'core/core.php';
	require_once 'core/emailutils.php';

	use \Core\Common\Data\IssueRow;
	use \Core\Common\Data\IssueAttachmentRow;
	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\AWSFileManager;

	class IssueManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_ISSUEMGR, __METHOD__, "methodName=$methodName");

			$issueManager = new IssueManager($context);

			$response = "failed";
			$responder = "IssueManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateIssue':
					$result = $issueManager->CreateIssue(
							isset($parameters['app']) ? $parameters['app'] : NULL,
							isset($parameters['secret']) ? $parameters['secret'] : NULL,
							isset($parameters['type']) ? $parameters['type'] : NULL,
							isset($parameters['title']) ? $parameters['title'] : NULL,
							isset($parameters['description']) ? $parameters['description'] : NULL,
							isset($parameters['reprosteps']) ? $parameters['reprosteps'] : NULL,
							isset($parameters['priority']) ? $parameters['priority'] : NULL,
							$issue,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"issueid" => $issue->getIssueId(),
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'AddAttachment':
					$result = $issueManager->AddAttachment(
							isset($parameters['secret']) ? $parameters['secret'] : NULL,
							isset($parameters['issueid']) ? $parameters['issueid'] : NULL,
							isset($parameters['filename']) ? $parameters['filename'] : NULL,
							isset($parameters['filecontents']) ? base64_decode($parameters['filecontents']) : NULL,
							$issueAttachmentRow,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"attachmentid" => $issueAttachmentRow->getAttachmentId(),
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "IssueManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_ISSUEMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_ISSUEMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		private function SendIssueCreatedNotification(
			$openedBy,
			$issue
			)
		{
			DBG_ENTER(DBGZ_ISSUEMGR, __METHOD__);

			$body = file_get_contents("/home/core/EmailTemplates/IssueCreatedNotification.html");

			$body = str_replace(
					array("@ISSUEID@", "@OPENEDBY@", "@TITLE@", "@DESCRIPTION@", "@REPROSTEPS@"),
					array($issue->getIssueId(), $openedBy, $issue->getTitle(), $issue->getDescription(), $issue->getReproSteps()),
					$body
					);

			foreach ($sendTo as &$to)
			{
				$result = SendEmail(
						$to['email'],
						$to['firstname'],
						$to['lastname'],
						"no-reply@idaxdata.com",
						"New issue opened for ".$issue->getApp(),
						$body,
						NULL,          // attachments
						$resultString
						);
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, $result);
		}

		private function SendIssueAttachmentNotification(
			$attachedBy,
			$attachmentContent,
			$issue,
			$issueAttachmentRow
			)
		{
			DBG_ENTER(DBGZ_ISSUEMGR, __METHOD__);

			$accountManager = new AccountManager($this->context);
			$accountManager->GetUserAccount($issue->getAccountId(), $openedBy, $resultString);

			$sendTo = array(
					//array("email" => "user's email address'", "firstname" => "user's first name'" , "lastname" => "user's last name'"),
					);

			$body = file_get_contents("/home/core/EmailTemplates/IssueAttachmentNotification.html");

			$body = str_replace(
					array(
							"@ISSUEID@",
							"@OPENEDBY@",
							"@ATTACHMENTNAME@",
							"@ATTACHEDBY@",
							"@TITLE@",
							"@DESCRIPTION@",
							"@REPROSTEPS@"
							),
					array(
							$issue->getIssueId(),
							$openedBy["firstname"]." ".$openedBy["lastname"],
							$issueAttachmentRow->getFilename(),
							$attachedBy,
							$issue->getTitle(),
							$issue->getDescription(),
							$issue->getReproSteps()
							),
					$body
					);

			foreach ($sendTo as &$to)
			{
				$result = SendEmail(
						$to['email'],
						$to['firstname'],
						$to['lastname'],
						"no-reply@idaxdata.com",
						"Attachment added for issue ".$issue->getIssueId(),
						$body,
						array(
								array("type" => "string", "name" => $issueAttachmentRow->getFilename(), "string" => $attachmentContent)
								),
						$resultString
						);
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, $result);
		}

		public function CreateIssue(
			$app,
			$secret,
			$type,
			$title,
			$description,
			$reproSteps,
			$priority,
			&$issueRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ISSUEMGR, __METHOD__, "app=$app, type=$type, title=$title");

			$result = FALSE;

			// Make sure we have a valid user or secret
			if (($this->context->account == NULL) && ($secret != "f3416cf6d21830579afa6d8a047ddca6"))
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($validParameters)
			{
				$date = date("Y-m-d H:i:s");

				$result = IssueRow::Create(
						$this->context->dbcon,
						$type,
						($this->context->account != NULL) ? $this->context->account->getAccountId() : "",
						$date,                                    // opendate
						$app,
						$title,
						$description,
						$reproSteps,
						1,                                        // state
						$date,                                    // lastupdated
						$this->context->account->getAccountId(),  // lastupdatedby
						NULL,                                     // comments
						$priority,
						"support@kanopian.com",                   // assignedto
						$issueRow,
						$sqlError
						);

				if ($result)
				{
					//
					// Send email notification *if* the device is not device_simulator (which means it is from an app
					// under development as opposed to a release app).
					//
					if ($this->context->location != "device_simulator")
					{
						$openedBy = $this->context->account->getFirstName()." ".$this->context->account->getLastName();

						$this->SendIssueCreatedNotification($openedBy, $issueRow);
					}
				}
				else
				{
					$resultString = "SQL error $sqlError";
				}
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, $result);
			return $result;
		}

		public function AddAttachment(
			$secret,
			$issueid,
			$filename,
			&$fileContents,
			&$issueAttachmentRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_ISSUEMGR, __METHOD__, "issueid=$issueid, filename=$filename");

			$result = FALSE;

			// Make sure we have a valid user or secret
			if (($this->context->account == NULL) && ($secret != "f3416cf6d21830579afa6d8a047ddca6"))
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($issueid === NULL)
			{
				$resultString = "Missing parameter '$issueid'";

				DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);
				$validParamters = FALSE;
			}
			else if ($filename === NULL)
			{
				$resultString = "Missing parameter 'filename'";

				DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);
				$validParamters = FALSE;
			}
			else if ($fileContents === NULL)
			{
				$resultString = "Missing parameter 'filecontents'";

				DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);
				$validParamters = FALSE;
			}

			if ($validParameters)
			{
				// Verify this is a valid issueid
				$issueRow = IssueRow::FindOne($this->context->dbcon, NULL, array("issueid=$issueid"), NULL, ROW_OBJECT);

				if ($issueRow == NULL)
				{
					$resultString = "Issue not found";

					DBG_ERR(DBGZ_ISSUEMGR, __METHOD__, $resultString);
					$validParamters = FALSE;
				}
				else
				{
					// Upload the fileContents to the bucket.
					$awsFileManager = new AWSFileManager(IDAX_ATTACHMENTS_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

					$bucketFilename = "{$issueid}_{$filename}";

					$result = $awsFileManager->UploadData(
							$bucketFilename,
							"public-read",
							$fileContents,
							GetMimeTypeByFileExtension(basename($filename)),
							TRUE,                // Overwrite existing file.
							$resultString
							);

					if ($result)
					{
						$date = date("Y-m-d H:i:s");

						$result = IssueAttachmentRow::Create(
								$this->context->dbcon,
								$issueid,
								date("Y-m-d H:i:s"), // lastupdated
								$filename,
								$bucketFilename,
								$issueAttachmentRow,
								$sqlError
								);

						if ($result)
						{
							//
							// Send email notification *if* the device is not device_simulator (which means it is from an app
							// under development as opposed to a release app).
							//
							if ($this->context->location != "device_simulator")
							{
								$attachedBy = $this->context->account->getFirstName()." ".$this->context->account->getLastName();

								$this->SendIssueAttachmentNotification($attachedBy, $fileContents, $issueRow, $issueAttachmentRow);
							}
						}
						else
						{
							$resultString = "SQL error $sqlError";
						}
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
