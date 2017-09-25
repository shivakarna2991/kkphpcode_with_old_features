<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class TaskRow
	{
		private $dbcon = NULL;

		private $taskid = NULL;
		private $testdata = NULL;
		private $jobid = NULL;
		private $name = NULL;
		private $setupdate = NULL;
		private $devicetype = NULL;
		private $status = NULL;
		private $assignedto = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getTaskId()
		{
			return $this->taskid;
		}

		public function getTestData()
		{
			return $this->testdata;
		}

		public function setTestData($value)
		{
			if ($this->testdata != $value)
			{
				$this->testdata = $value;
				$this->fieldUpdated('testdata', $value);
			}
		}

		public function getJobId()
		{
			return $this->jobid;
		}

		public function setJobId($value)
		{
			if ($this->jobid != $value)
			{
				$this->jobid = $value;
				$this->fieldUpdated('jobid', $value);
			}
		}

		public function getName()
		{
			return $this->name;
		}

		public function setName($value)
		{
			if ($this->name != $value)
			{
				$this->name = $value;
				$this->fieldUpdated('name', $value);
			}
		}

		public function getSetupDate()
		{
			return $this->setupdate;
		}

		public function setSetupDate($value)
		{
			if ($this->setupdate != $value)
			{
				$this->setupdate = $value;
				$this->fieldUpdated('setupdate', $value);
			}
		}

		public function getDeviceType()
		{
			return $this->devicetype;
		}

		public function setDeviceType($value)
		{
			if ($this->devicetype != $value)
			{
				$this->devicetype = $value;
				$this->fieldUpdated('devicetype', $value);
			}
		}

		public function getStatus()
		{
			return $this->status;
		}

		public function setStatus($value)
		{
			if ($this->status != $value)
			{
				$this->status = $value;
				$this->fieldUpdated('status', $value);
			}
		}

		public function getAssignedTo()
		{
			return $this->assignedto;
		}

		public function setAssignedTo($value)
		{
			if ($this->assignedto != $value)
			{
				$this->assignedto = $value;
				$this->fieldUpdated('assignedto', $value);
			}
		}

		public function __construct(
			$dbcon,
			$taskid,
			$testdata,
			$jobid,
			$name,
			$setupdate,
			$devicetype,
			$status,
			$assignedto
			)
		{
			$this->dbcon = $dbcon;

			$this->taskid = $taskid;
			$this->testdata = $testdata;
			$this->jobid = $jobid;
			$this->name = $name;
			$this->setupdate = $setupdate;
			$this->devicetype = $devicetype;
			$this->status = $status;
			$this->assignedto = $assignedto;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$testdata,
			$jobid,
			$name,
			$setupdate,
			$devicetype,
			$status,
			$assignedto,
			&$taskObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TASKROW, __METHOD__);

			DBG_INFO(DBGZ_TASKROW, __METHOD__, "Inserting row with jobid=$jobid, devicetype=$devicetype");

			$escapedName = mysqli_real_escape_string($dbcon, $name);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tasks (testdata, jobid, name, setupdate, devicetype, status, assignedto)
					VALUES ('$testdata', '$jobid', '$escapedName', '$setupdate', '$devicetype', '$status', '$assignedto')"
					);

			if ($result)
			{
				$taskid = mysqli_insert_id($dbcon);

				$taskObject = new TaskRow(
						$dbcon,
						$taskid,
						$testdata,
						$jobid,
						$name,
						$setupdate,
						$devicetype,
						$status,
						$assignedto
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TASKROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TASKROW, __METHOD__, $result);
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

			$rows = TaskRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_TASKROW, __METHOD__);

			$rows = NULL;

			$selectFields = "taskid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add taskid as it's already included by default.
					if ($field != "taskid")
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

			DBG_INFO(DBGZ_TASKROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tasks $filterString $sortString");

			if ($result)
			{
				$numRows = mysqli_num_rows($result);

				for ($i=0; $i<$numRows; $i++)
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

						$rows[] = new TaskRow(
								$dbcon,
								isset($row['taskid']) ? $row['taskid'] : NULL,
								isset($row['testdata']) ? $row['testdata'] : NULL,
								isset($row['jobid']) ? $row['jobid'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['setupdate']) ? $row['setupdate'] : NULL,
								isset($row['devicetype']) ? $row['$devicetype'] : NULL,
								isset($row['status']) ? $row['status'] : NULL,
								isset($row['assignedto']) ? $row['assignedto'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TASKROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TASKROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TASKROW, __METHOD__, "taskid=$this->taskid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->taskid != NULL)
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

					DBG_INFO(DBGZ_TASKROW, __METHOD__, "Updating row with taskid=$this->taskid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_tasks
							SET $setString
							WHERE taskid='$this->taskid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_TASKROW, __METHOD__, "Update failed with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_TASKROW, __METHOD__, "Must set taskid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_TASKROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_TASKROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TASKROW, __METHOD__);

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

			DBG_INFO(DBGZ_TASKROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tasks SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TASKROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TASKROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TASKROW, __METHOD__);

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

			DBG_INFO(DBGZ_TASKROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tasks $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TASKROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TASKROW, __METHOD__, $result);
			return $result;
		}
	}
?>
