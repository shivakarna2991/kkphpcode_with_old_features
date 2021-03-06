<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class DeviceRow
	{
		private $dbcon = NULL;

		private $deviceid = NULL;
		private $type = NULL;
		private $manufacturer = NULL;
		private $model = NULL;
		private $serialnumber = NULL;
		private $latitude = NULL;
		private $longitude = NULL;
		private $config = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getDeviceId()
		{
			return $this->deviceid;
		}

		public function getType()
		{
			return $this->type;
		}

		public function setType($value)
		{
			if ($this->type != $value)
			{
				$this->type = $value;
				$this->fieldUpdated('type', $value);
			}
		}

		public function getManufacturer()
		{
			return $this->manufacturer;
		}

		public function setManufacturer($value)
		{
			if ($this->manufacturer != $value)
			{
				$this->manufacturer = $value;
				$this->fieldUpdated('manufacturer', $value);
			}
		}

		public function getModel()
		{
			return $this->model;
		}

		public function setModel($value)
		{
			if ($this->model != $value)
			{
				$this->model = $value;
				$this->fieldUpdated('model', $value);
			}
		}

		public function getSerialNumber()
		{
			return $this->serialnumber;
		}

		public function setSerialNumber($value)
		{
			if ($this->serialnumber != $value)
			{
				$this->serialnumber = $value;
				$this->fieldUpdated('serialnumber', $value);
			}
		}

		public function getLatitude()
		{
			return $this->latitude;
		}

		public function setLatitude($value)
		{
			if ($this->latitude != $value)
			{
				$this->latitude = $value;
				$this->fieldUpdated('latitude', $value);
			}
		}

		public function getLongitude()
		{
			return $this->longitude;
		}

		public function setLongitude($value)
		{
			if ($this->longitude != $value)
			{
				$this->longitude = $value;
				$this->fieldUpdated('longitude', $value);
			}
		}

		public function getConfig()
		{
			return $this->config;
		}

		public function setConfig($value)
		{
			if ($this->config != $value)
			{
				$this->config = $value;
				$this->fieldUpdated('config', $value);
			}
		}

		public function __construct(
			$dbcon,
			$deviceid,
			$type,
			$manufacturer,
			$model,
			$serialnumber,
			$latitude,
			$longitude,
			$config
			)
		{
			$this->dbcon = $dbcon;

			$this->deviceid = $deviceid;
			$this->type = $type;
			$this->manufacturer = $manufacturer;
			$this->model = $model;
			$this->serialnumber = $serialnumber;
			$this->latitude = $latitude;
			$this->longitude = $longitude;
			$this->config = $config;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$type,
			$manufacturer,
			$model,
			$serialnumber,
			$latitude,
			$longitude,
			$config,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICEROW, __METHOD__);

			DBG_INFO(
					DBGZ_DEVICEROW,
					__METHOD__,
					"Inserting row with type=$type, getManufacturer=$manufacturer, model=$model, serialnumber=$serialnumber"
					);

			$escapedManufacturer = mysqli_real_escape_string($dbcon, $manufacturer);
			$escapedModel = mysqli_real_escape_string($dbcon, $model);
			$escapedConfig = mysqli_real_escape_string($dbcon, $config);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_devices (type, manufacturer, model, serialnumber, latitude, longitude, config)
					VALUES ('$type', '$escapedManufacturer', '$escapedModel', '$serialnumber', '$latitude', '$longitude', '$escapedConfig')"
					);

			if ($result)
			{
				$deviceid = mysqli_insert_id($dbcon);

				$object = new DeviceRow(
						$dbcon,
						$deviceid,
						$type,
						$manufacturer,
						$model,
						$serialnumber,
						$latitude,
						$longitude,
						$config
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEROW, __METHOD__, $result);
			return $result;
		}

		static public function FindOne(
			$dbcon,
			$fields,
			$filters,
			$sortOrder,
			$returnType,
			&$sqlError
			)
		{
			$row = NULL;

			$rows = DeviceRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

			if ($rows)
			{
				$row = $rows[0];
			}

			return $row;
		}

		static public function Find(
			$dbcon,
			$fields,
			$filters,
			$sortOrder,
			$returnType,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "deviceid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add deviceid as it's already included by default.
					if ($field != "deviceid")
					{
						$selectFields = "$selectFields, $field";
					}
				}
			}
			else
			{
				$selectFields = "*";
			}

			$filterString = "";
			$numFilters = 0;

			if ($filters != NULL)
			{
				foreach ($filters as $filter)
				{
					if ($numFilters == 0)
					{
						$filterString = "WHERE ($filter)";
					}
					else
					{
						$filterString = "$filterString AND ($filter)";
					}

					$numFilters += 1;
				}
			}

			$sortString = "";

			if ($sortOrder != NULL)
			{
				$sortString = "ORDER BY $sortOrder";
			}

			DBG_INFO(DBGZ_DEVICEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_devices $filterString $sortString");

			if ($result)
			{
				$numrows = mysqli_num_rows($result);

				for ($i=0; $i<$numrows; $i++)
				{
					if ($returnType == ROW_NUMERIC)
					{
						$rows[] = mysqli_fetch_row($result);
					}
					else if ($returnType == ROW_ASSOCIATIVE)
					{
						$rows[] = mysqli_fetch_assoc($result);
					}
					else
					{
						$row = mysqli_fetch_array($result);

						$rows[] = new DeviceRow(
								$dbcon,
								isset($row['deviceid']) ? $row['deviceid'] : NULL,
								isset($row['type']) ? $row['type'] : NULL,
								isset($row['manufacturer']) ? $row['manufacturer'] : NULL,
								isset($row['model']) ? $row['model'] : NULL,
								isset($row['serialnumber']) ? $row['serialnumber'] : NULL,
								isset($row['latitude']) ? $row['latitude'] : NULL,
								isset($row['longitude']) ? $row['longitude'] : NULL,
								isset($row['config']) ? $row['config'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_DEVICEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICEROW, __METHOD__, "deviceid=$this->deviceid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->deviceid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$changedField = mysqli_real_escape_string($this->dbcon, $changedField);
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_DEVICEROW, __METHOD__, "Updating row with deviceid=$this->deviceid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_devices
							 SET $setString
							 WHERE deviceid='$this->deviceid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_DEVICEROW, __METHOD__, "Must set deviceid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_DEVICEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICEROW, __METHOD__);

			$retval = FALSE;

			$filterString = "";
			$numFilters = 0;

			if ($filters != NULL)
			{
				foreach ($filters as $filter)
				{
					if ($numFilters == 0)
					{
						$filterString = "WHERE ($filter)";
					}
					else
					{
						$filterString = "$filterString AND ($filter)";
					}

					$numFilters += 1;
				}
			}

			$setString = "";

			foreach ($fields as &$field)
			{
				$setString = "$setString $field, ";
			}

			$setString = trim($setString, ', ');

			DBG_INFO(DBGZ_DEVICEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_devices SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICEROW, __METHOD__);

			$retval = FALSE;

			$filterString = "";
			$numFilters = 0;

			if ($filters != NULL)
			{
				foreach ($filters as $filter)
				{
					if ($numFilters == 0)
					{
						$filterString = "WHERE ($filter)";
					}
					else
					{
						$filterString = "$filterString AND ($filter)";
					}

					$numFilters += 1;
				}
			}

			DBG_INFO(DBGZ_DEVICEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_devices $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICEROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
