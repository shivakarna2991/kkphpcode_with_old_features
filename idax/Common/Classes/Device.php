<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Data\DeviceRow;

	class Device
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_DEVICE, __METHOD__, "methodName=$methodName");

			$device = new Device($context);

			$response = "failed";
			$responder = "Device::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'Update':
					$result = $device->Update(
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							isset($parameters['type']) ? $parameters['type'] : NULL,
							isset($parameters['manufacturer']) ? $parameters['manufacturer'] : NULL,
							isset($parameters['model']) ? $parameters['model'] :  NULL,
							isset($parameters['serialnumber']) ? $parameters['serialnumber'] : NULL,
							isset($parameters['latitude']) ? $parameters['latitude'] : NULL,
							isset($parameters['longitude']) ? $parameters['longitude'] : NULL,
							isset($parameters['ipv4address']) ? $parameters['ipv4address'] : NULL,
							isset($parameters['port']) ? $parameters['port'] : NULL,
							isset($parameters['secret']) ? $parameters['secret'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'GetInfo':
					$result = $device->GetInfo(
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							$deviceRow,
							$resultString
							);

					if ($result) 
					{
						$response = "success";
						$returnval = array("device" => $deviceRow, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'GetConfiguration':
					$result = $device->GetInfo(
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							$deviceRow,
							$resultString
							);

					if ($result) 
					{
						$response = "success";
						$returnval = array("config" => $deviceRow["config"], "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'Delete':
					$result = $device->Delete(
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							$resultString
							);

					if ($result) 
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				default:
					$response = "failed";
					$responder = "Device";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_DEVICE, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_DEVICE, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function Update(
			$deviceId,
			$type,
			$manufacturer,
			$model,
			$serialNumber,
			$latitude,
			$longitude,
			$ipv4Address,
			$port,
			$secret,
			$config,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICE, __METHOD__, "deviceid=$deviceId, type=$type, manufacturer=$manufacturer, model=$model, ipv4address=$ipv4Address, port=$port");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;

			$deviceRow = DeviceRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("deviceid=$deviceId"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			$updateRow = FALSE;

			if ($type !== NULL)
			{
				$deviceRow->setType($type);
				$updateRow = TRUE;
			}

			if ($manufacturer !== NULL)
			{
				$deviceRow->setManufacturer($manufacturer);
				$updateRow = TRUE;
			}

			if ($model !== NULL)
			{
				$deviceRow->setSerialNumber($model);
				$updateRow = TRUE;
			}

			if ($serialNumber !== NULL)
			{
				$deviceRow->setSerialNumber($serialNumber);
				$updateRow = TRUE;
			}

			if ($latitude !== NULL)
			{
				$deviceRow->setLatitude($latitude);
				$updateRow = TRUE;
			}

			if ($longitude !== NULL)
			{
				$deviceRow->setLongitude($longitude);
				$updateRow = TRUE;
			}

			if ($ipv4Address !== NULL)
			{
				$deviceRow->setIPV4Address($ipv4Address);
				$updateRow = TRUE;
			}

			if ($port !== NULL)
			{
				$deviceRow->setPort($port);
				$updateRow = TRUE;
			}

			if ($secret !== NULL)
			{
				$deviceRow->setSecret($secret);
				$updateRow = TRUE;
			}

			if ($config !== NULL)
			{
				$deviceRow->setConfig($config);
				$updateRow = TRUE;
			}

			if ($updateRow)
			{
				$result = $deviceRow->CommitChangedFields($sqlError);

				if (!$result)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, $result);
			return $result;
		}

		public function GetInfo(
			$deviceId,
			&$deviceRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICE, __METHOD__, "deviceid=$deviceId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}

			$validParameters = TRUE;

			if ($deviceId === NULL)
			{
				$resultString = "Missing parameter 'deviceid'";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				// Check if the device is deployed.  Don't delete it if it is.
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						array("deviceid", "type", "manufacturer", "model", "serialnumber", "deployed", "jobsiteid", "latitude", "longitude", "ipv4address", "port"),
						array("deviceid=$deviceId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$result = TRUE;
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Device not found";
					DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, $result);
			return $result;
		}

		public function GetConfiguration(
			$deviceId,
			&$deviceRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICE, __METHOD__, "deviceid=$deviceId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}

			$validParameters = TRUE;

			if ($deviceId === NULL)
			{
				$resultString = "Missing parameter 'deviceid'";
				DBG_ERR(DBGZ_DEVICEMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				// Check if the device is deployed.  Don't delete it if it is.
				$deviceRow = DeviceRow::FindOne(
						$this->context->dbcon,
						array("deviceid", "config"),
						array("deviceid=$deviceId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($deviceRow != NULL)
				{
					$result = TRUE;
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Device not found";
					DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$deviceId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_DEVICE, __METHOD__, "deviceid=$deviceId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_DEVICE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;

			// Check if the device is deployed.  Don't delete it if it is.
			$deviceRow = DeviceRow::FindOne(
					$this->context->dbcon,
					array("deployed", "jobsiteid"),
					array("deviceid=$deviceId"),
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($deviceRow != NULL)
			{
				if ($deviceRow["deployed"])
				{
					$resultString = "Device is deployed to job site ".$deviceRow["jobsiteid"];
					DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
				}
				else
				{
					$result = DeviceRow::Delete(
							$this->context->dbcon,
							array("deviceid=$deviceId"),
							$sqlError
							);

					if (!$result)
					{
						$resultString = "SQL error $sqlError";
						DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
					}
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Device not found";
				DBG_WARN(DBGZ_DEVICE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_DEVICE, __METHOD__, $result);
			return $result;
		}
	}
?>
