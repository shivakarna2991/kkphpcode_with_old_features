<?php
	namespace Idax\Video\Classes;

	use \Core\Common\Classes\AWSFileManager;
	use \Idax\Common\Data\DeviceRow;

	require_once 'idax/idax.php';

	class KapturrKam
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "methodName=$methodName");

			$kaptureKam = new KapturrKam($context);

			$response = "failed";
			$responder = "KapturrKam::$methodName";
			$returnval = "failed";

			$deviceResponded = FALSE;

			switch ($methodName)
			{
				case 'QueryStatus':
					$result = $kaptureKam->QueryStatus(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$status,
							$deviceResponded,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"status" => $status,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"resultstring" => $resultString
								);
					}

					break;

				case 'GetSessions':
					$result = $kaptureKam->GetSessions(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$sessions,
							$deviceResponded,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"sessions" => $sessions,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'GetCachedSessions':
					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "parameters=".serialize($parameters));

					$result = $kaptureKam->GetCachedSessions(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$sessions,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"sessions" => $sessions,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'AddScheduledCaptureSession':
					$result = $kaptureKam->AddScheduledCaptureSession(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							isset($parameters['starttime']) ? $parameters['starttime'] : NULL,
							isset($parameters['endtime']) ? $parameters['endtime'] : NULL,
							$session,
							$deviceResponded,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"session" => $session,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'UpdateScheduledCaptureSession':
					$result = $kaptureKam->UpdateScheduledCaptureSession(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							isset($parameters['sessionid']) ? $parameters['sessionid'] : NULL,
							isset($parameters['starttime']) ? $parameters['starttime'] : NULL,
							isset($parameters['endtime']) ? $parameters['endtime'] : NULL,
							$session,
							$deviceResponded,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"session" => $session,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'DeleteSessions':
					$sessionids = array();
					$i = 0;

					while (isset($parameters["sessionid_{$i}"]))
					{
						$sessionids[] = $parameters["sessionid_{$i}"];
						$i += 1;
					}

					$result = $kaptureKam->DeleteSessions(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$sessionids,
							$sessions,
							$deviceResponded,
							$time,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"sessions" => $sessions,
								"time" => $time,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'UploadVideos':
					$sessionids = array();
					$i = 0;

					while (isset($parameters["sessionid_{$i}"]))
					{
						$sessionids[] = $parameters["sessionid_{$i}"];
						$i += 1;
					}

					$result = $kaptureKam->UploadVideos(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$sessionids,
							$sessions,
							$deviceResponded,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("deviceresponded" => $deviceResponded, "sessions" => $sessions, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'ControlLiveStreaming':
					$result = $kaptureKam->ControlLiveStreaming(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							isset($parameters['startstream']) ? boolval($parameters['startstream']) : NULL,
							$deviceResponded,
							$active,
							$url,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"deviceresponded" => $deviceResponded,
								"active" => $active,
								"url" => $url,	
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'CaptureImage':
					$result = $kaptureKam->CaptureImage(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$deviceResponded,
							$image,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;

				case 'SendNotification':
					$result = $kaptureKam->SendNotification(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							isset($parameters['type']) ? $parameters['type'] : NULL,
							isset($parameters['info']) ? $parameters['info'] : NULL,
							$deviceResponded,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
						$returnval = array("deviceresponded" => $deviceResponded, "resultstring" => $resultString);
					}

					break;
				
				case 'GetDeviceLog':
					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "parameters=".serialize($parameters));

					$result = $kaptureKam->GetDeviceLog(
							isset($parameters['kamid']) ? $parameters['kamid'] : NULL,
							$deviceResponded,
							$log,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("deviceresponded" => $deviceResponded, "log" => $log);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case "GetSchedule":
					break;

				case "GetConfiguration":
					break;

				case "UpdateStatus":
					break;

				case "UpdateLocation":
					break;

				case "UploadImage":
					break;

				case "UploadVideo":
					break;

				default:
					$response = "failed";
					$responder = "KapturrKam";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_KAPTURRKAM, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function QueryStatus(
			$kamId,
			&$status,
			&$deviceResponded,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$url = "http://$ipv4address/v1/camera/status";

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$response = @file_get_contents($url);

					if ($response !== FALSE)
					{
						$deviceResponded = TRUE;

						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$status = $jsonResponse['results']['returnval'];
							$time = $jsonResponse['results']['time'];
							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function GetSessions(
			$kamId,
			&$sessions,
			&$deviceResponded,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$url = "http://$ipv4address/v1/camera/captureSchedule";

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$response = @file_get_contents($url);

					if ($response !== FALSE)
					{
						$deviceResponded = TRUE;

						// Save the sessions so they can be retrieved later if the device goes down.
						$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

						$awsFileManager->UploadData(
								"kapturrkamtestsessions.json",
								"public-read",
								$response,
								GetMimeTypeByFileExtension("json"),
								TRUE,
								$resultString
								);

						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$sessions = $jsonResponse['results']['returnval']['sessions'];
							$time = $jsonResponse['results']['time'];
							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function GetCachedSessions(
			$kamId,
			&$sessions,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

					$result = $awsFileManager->GetFile("kapturrkamtestsessions.json", $response);

					if ($result !== FALSE)
					{
						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$sessions = $jsonResponse['results']['returnval']['sessions'];
							$time = $jsonResponse['results']['time'];
							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function AddScheduledCaptureSession(
			$kamId,
			$startTime,
			$endTime,
			&$session,
			&$deviceResponded,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId, startTime=$startTime, endTime=$endTime");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			if ($startTime === NULL)
			{
				$resultString = "Missing parameter 'starttime'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			if ($endTime === NULL)
			{
				$resultString = "Missing parameter 'endtime'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$url = "http://$ipv4address/v1/camera/captureSchedule/session?start=$startTime&end=$endTime";

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$opts = array(
							'http' => array(
									'method'  => 'POST',
									'header'  => 'Content-type: application/x-www-form-urlencoded'
								)
							);

					$context  = stream_context_create($opts);

					$response = @file_get_contents($url, false, $context);

					if ($response !== FALSE)
					{
						$deviceResponded = TRUE;

						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$session = array(
									'id' => $jsonResponse['results']['returnval']['id'],
									'state' => $jsonResponse['results']['returnval']['state'],
									'startDate' => $jsonResponse['results']['returnval']['startDate'],
									'endDate' => $jsonResponse['results']['returnval']['endDate']
									);

							$time = $jsonResponse['results']['time'];
							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function UpdateScheduledCaptureSession(
			$kamId,
			$sessionId,
			$startTime,
			$endTime,
			&$session,
			&$deviceResponded,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId, sessionId=sessionId, startTime=$startTime, endTime=$endTime");

			$result = FALSE;

			// Currently there is no API to update a session.  So we'll delete the session and then add a new
			// one with the updated start and end times.
			$result = $this->DeleteSessions(
					$kamId,
					array($sessionId),
					$sessions,
					$deviceResponded,
					$time,
					$resultString
					);

			if ($result)
			{
				$result = $this->AddScheduledCaptureSession(
						$kamId,
						$startTime,
						$endTime,
						$session,
						$deviceResponded,
						$time,
						$resultString
						);

			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function DeleteSessions(
			$kamId,
			$sessionIds,
			&$sessions,
			&$deviceResponded,
			&$time,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($sessionIds === NULL)
			{
				$resultString = "Missing parameter 'sessionids'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if (count($sessionIds) === 0)
			{
				$resultString = "No sessionids provided";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$baseurl = "http://$ipv4address/v1/camera/captureSchedule/session";

					$opts = array(
							'http' => array(
									'method' => 'DELETE',
									'header' => 'Content-type: application/x-www-form-urlencoded'
								)
							);

					$context  = stream_context_create($opts);

					foreach ($sessionIds as &$sessionId)
					{
						$url = "$baseurl/$sessionId";
						DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

						$response = @file_get_contents($url, false, $context);

						if ($response !== FALSE)
						{
							$deviceResponded = TRUE;

							$jsonResponse = json_decode($response, true);

							if ($jsonResponse['results']['response'] == "success")
							{
								$sessions = $jsonResponse['results']['returnval']['sessions'];
								$time = $jsonResponse['results']['time'];
								$result = TRUE;
							}
							else
							{
								$resultString = $jsonResponse['results']['returnval']['resultstring'];
								DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

								break;
							}
						}
						else
						{
							$deviceResponded = FALSE;

							$error = error_get_last();
							$resultString = $error['message'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

							break;
						}
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function UploadVideos(
			$kamId,
			$sessionIds,
			&$sessions,
			&$deviceResponded,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($sessionIds === NULL)
			{
				$resultString = "Missing parameter 'sessionids'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if (count($sessionIds) === 0)
			{
				$resultString = "No sessionids provided";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$baseurl = "http://$ipv4address/v1/camera/captureSchedule/session";

					$opts = array(
							'http' => array(
									'method' => 'POST',
									'header' => 'Content-type: application/x-www-form-urlencoded'
								)
							);

					$context  = stream_context_create($opts);

					foreach ($sessionIds as &$sessionId)
					{
						$url = "$baseurl/$sessionId?action=upload";
						DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

						$response = @file_get_contents($url, false, $context);

						if ($response !== FALSE)
						{
							$deviceResponded = TRUE;

							$jsonResponse = json_decode($response, true);

							if ($jsonResponse['results']['response'] == "success")
							{
								$sessions = $jsonResponse['results']['returnval']['sessions'];
								$result = TRUE;
							}
							else
							{
								$resultString = $jsonResponse['results']['returnval']['resultstring'];
								DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

								break;
							}
						}
						else
						{
							$deviceResponded = FALSE;

							$error = error_get_last();
							$resultString = $error['message'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

							break;
						}
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function ControlLiveStreaming(
			$kamId,
			$startStream,
			&$deviceResponded,
			&$active,
			&$url,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($startStream === NULL)
			{
				$resultString = "Missing parameter 'startstream'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					if ($startStream)
					{
						$url = "http://$ipv4address/v1/camera/liveStream/start";
					}
					else
					{
						$url = "http://$ipv4address/v1/camera/liveStream/stop";
					}

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$opts = array(
							'http' => array(
									'method'  => 'POST',
									'header'  => 'Content-type: application/x-www-form-urlencoded'
								)
							);

					$context  = stream_context_create($opts);

					$response = @file_get_contents($url, false, $context);

					if ($response !== FALSE)
					{
						$deviceResponded = TRUE;

						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$active = $jsonResponse['results']['returnval']['active'];

							if ($active)
							{
								$url = $jsonResponse['results']['returnval']['url'];
							}
							else
							{
								$url = "";
							}

							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function CaptureImage(
			$kamid,
			$deviceResponded,
			$image,
			$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					if ($startStream)
					{
						$url = "http://$ipv4address/v1/camera/liveStream/start";
					}
					else
					{
						$url = "http://$ipv4address/v1/camera/liveStream/stop";
					}

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$opts = array(
							'http' => array(
									'method'  => 'POST',
									'header'  => 'Content-type: application/x-www-form-urlencoded'
								)
							);

					$context  = stream_context_create($opts);

					$response = @file_get_contents($url, false, $context);

					if ($response !== FALSE)
					{
						$deviceResponded = TRUE;

						$jsonResponse = json_decode($response, true);

						if ($jsonResponse['results']['response'] == "success")
						{
							$active = $jsonResponse['results']['returnval']['active'];

							if ($active)
							{
								$url = $jsonResponse['results']['returnval']['url'];
							}
							else
							{
								$url = "";
							}

							$result = TRUE;
						}
						else
						{
							$resultString = $jsonResponse['results']['returnval']['resultstring'];
							DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
						}
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}

		public function GetDeviceLog(
			$kamId,
			&$deviceResponded,
			&$log,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_KAPTURRKAM, __METHOD__, "kamId=$kamId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($kamId === NULL)
			{
				$resultString = "Missing parameter 'kamid'";
				DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("deviceid=$kamId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$ipv4address = $deviceRow['ipv4address'];
					$port = $deviceRow['port'];

					if ($port != 0)
					{
						$ipv4address .= ":$port";
					}

					$url = "http://$ipv4address/v1/camera/log";

					DBG_INFO(DBGZ_KAPTURRKAM, __METHOD__, "url=$url");

					$response = @file_get_contents($url);

					if ($response !== FALSE)
					{
						$result = TRUE;
						$deviceResponded = TRUE;

						$log = $response;
					}
					else
					{
						$deviceResponded = FALSE;

						$error = error_get_last();
						$resultString = $error['message'];
						DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "KapturrKam not found";
					DBG_ERR(DBGZ_KAPTURRKAM, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_KAPTURRKAM, __METHOD__, $result);
			return $result;
		}
	}
?>
