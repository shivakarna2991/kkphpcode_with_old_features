<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class DeviceLogRow
	{
		private $dbcon = NULL;

		private $deviceid = NULL;
		private $jobsiteid = NULL;
		private $timestamp = NULL;
		private $datatype = NULL;
		private $data = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getDeviceId()
		{
			return $this->deviceid;
		}

		public function setDeviceId($value)
		{
			if ($this->deviceid != $value)
			{
				$this->deviceid = $value;
				$this->fieldUpdated('deviceid', $value);
			}
		}

		public function getJobSiteId()
		{
			return $this->jobsiteid;
		}

		public function setJobSiteId($value)
		{
			if ($this->jobsiteid != $value)
			{
				$this->jobsiteid = $value;
				$this->fieldUpdated('jobsiteid', $value);
			}
		}

		public function getTimeStamp()
		{
			return $this->timestamp;
		}

		public function setTimeStamp($value)
		{
			if ($this->timestamp != $value)
			{
				$this->timestamp = $value;
				$this->fieldUpdated('timestamp', $value);
			}
		}

		public function getDataType()
		{
			return $this->datatype;
		}

		public function setDataType($value)
		{
			if ($this->datatype != $value)
			{
				$this->datatype = $value;
				$this->fieldUpdated('datatype', $value);
			}
		}

		public function getData()
		{
			return $this->data;
		}

		public function setData($value)
		{
			if ($this->data != $value)
			{
				$this->data = $value;
				$this->fieldUpdated('data', $value);
			}
		}

		public function __construct(
			$dbcon,
			$deviceid,
			$jobsiteid,
			$timestamp,
			$datatype,
			$data
			)
		{
			$this->dbcon = $dbcon;

			$this->deviceid = $deviceid;
			$this->jobsiteid = $jobsiteid;
			$this->timestamp = $timestamp;
			$this->datatype = $datatype;
			$this->data = $data;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$deviceid,
			$jobsiteid,
			$timestamp,
			$datatype,
			$data,
			&$row,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICELOGROW, __METHOD__);

			DBG_INFO(
					DBGZ_DEVICELOGROW,
					__METHOD__,
					"Inserting row with deviceid=$deviceid, jobsiteid=$jobsiteid, timestamp=$timestamp, datatype=$datatype"
					);

			$escapedData = mysqli_real_escape_string($dbcon, $data);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_devices_log (deviceid, jobsiteid, timestamp, datatype, data)
					VALUES ('$deviceid', '$jobsiteid', '$timestamp', '$datatype', '$escapedData')"
					);

			if ($result)
			{
				$row = new DeviceLogRow(
						$dbcon,
						$deviceid,
						$jobsiteid,
						$timestamp,
						$datatype,
						$data
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICELOGROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICELOGROW, __METHOD__, $result);
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

			$rows = DeviceLogRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_DEVICELOGROW, __METHOD__);

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

			DBG_INFO(DBGZ_DEVICELOGROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_devices_log $filterString $sortString");

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

						$rows[] = new DeviceLogRow(
								$dbcon,
								isset($row['deviceid']) ? $row['deviceid'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['timestamp']) ? $row['timestamp'] : NULL,
								isset($row['datatype']) ? $row['datatype'] : NULL,
								isset($row['data']) ? $row['data'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICELOGROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_DEVICELOGROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICELOGROW, __METHOD__);

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

			DBG_INFO(DBGZ_DEVICELOGROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_devices_log SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICELOGROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICELOGROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_DEVICELOGROW, __METHOD__);

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

			DBG_INFO(DBGZ_DEVICELOGROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_devices_log $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_DEVICELOGROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_DEVICELOGROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
