<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Data\DeviceRow;

	class DeviceManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_DEVICEMGR, __METHOD__, "methodName=$methodName");

			$deviceManager = new DeviceManager($context);

			$response = "failed";
			$responder = "DeviceManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateDevice':
					$result = $deviceManager->CreateDevice(
							isset($parameters['type']) ? $parameters['type'] : NULL,
							isset($parameters['manufacturer']) ? $parameters['manufacturer'] : NULL,
							isset($parameters['model']) ? $parameters['model'] :  NULL,
							isset($parameters['serialnumber']) ? $parameters['serialnumber'] : NULL,
							isset($parameters['deployed']) ? $parameters['deployed'] : NULL,
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['latitude']) ? $parameters['latitude'] : NULL,
							isset($parameters['longitude']) ? $parameters['longitude'] : NULL,
							isset($parameters['ipv4address']) ? $parameters['ipv4address'] : NULL,
							isset($parameters['port']) ? $parameters['port'] : NULL,
							isset($parameters['secret']) ? $parameters['secret'] : NULL,
							isset($parameters['config']) ? $parameters['config'] : NULL,
							$deviceRow,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("deviceid" => $deviceRow->getDeviceId(), "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'GetDevices':
					$response = "success";

					$result = $deviceManager->GetDevices(
							isset($parameters['type']) ? $parameters['type'] : NULL,
							$devices,
							$resultString
							);

					if ($result) 
					{
						$returnval = array("devices" => $devices, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "DeviceManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_DEVICEMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_DEVICEMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function CreateDevice(
			$type,
			$manufacturer,
			$model,
			$deployed,
			$jobSiteId,
			$latitude,
			$longitude,
			$ipv4Address,
			$port,
			$secret,
			$config,
			&$deviceRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICEMGR, __METHOD__, "type=$type, manufacturer=$manufacturer, model=$model");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($type === NULL)
			{
				$resultString = "Missing parameter 'type'";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($manufacturer === NULL)
			{
				$resultString = "Missing parameter 'manufacturer'";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($serialNumber === NULL)
			{
				$resultString = "Missing parameter 'serialnumber'";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$creationTime = date('Y-m-d H:i:s');

				// Set defaults for unspecifed parameters
				$result = DeviceRow::Create(
						$this->context->dbcon,
						$type,
						$manufacturer,
						$model,
						$serialNumber,
						$deployed,
						$jobSiteId,
						$latitude,
						$longitude,
						$ipv4Address,
						$port,
						$secret,
						$config,
						$deviceRow,
						$sqlError
						);

				if (!$result)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, $result);
			return $result;
		}

		public function GetDevices(
			$type,
			&$deviceRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICEMGR, __METHOD__);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$filter = array();

			$sqlError = 0;

			$deviceRows = DeviceRow::Find(
					$this->context->dbcon,
					array("deviceid", "type", "manufacturer", "model", "serialnumber", "deployed", "jobsiteid", "latitude", "longitude", "ipv4address", "port"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($deviceRows != NULL)
			{
				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_WARN(DBGZ_DEVICEMGR, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: no devices found";
				DBG_WARN(DBGZ_DEVICEMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
