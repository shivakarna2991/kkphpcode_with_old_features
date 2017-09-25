<?php

	namespace Core\Common\Data;

	require_once 'core/core.php';

	class JobQueueRow
	{
		private $dbcon = NULL;

		private $jobqueueid = NULL;
		private $serverinstanceid = NULL;
		private $addedtime = NULL;
		private $jobname = NULL;
		private $jobparams = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getJobQueueId()
		{
			return $this->jobqueueid;
		}

		public function getServerInstanceId()
		{
			return $this->serverinstanceid;
		}

		public function setServerInstanceId($value)
		{
			if ($this->serverinstanceid != $value)
			{
				$this->serverinstanceid = $value;
				$this->fieldUpdated('serverinstanceid', $value);
			}
		}

		public function getAddedTime()
		{
			return $this->addedtime;
		}

		public function setAddedTime($value)
		{
			if ($this->addedtime != $value)
			{
				$this->addedtime = $value;
				$this->fieldUpdated('addedtime', $value);
			}
		}

		public function getJobName()
		{
			return $this->jobname;
		}

		public function setJobName($value)
		{
			if ($this->jobname != $value)
			{
				$this->jobname = $value;
				$this->fieldUpdated('jobname', $value);
			}
		}

		public function getJobParams()
		{
			return $this->jobparams;
		}

		public function setJobParams($value)
		{
			if ($this->jobparams != $value)
			{
				$this->jobparams = $value;
				$this->fieldUpdated('jobparams', $value);
			}
		}

		public function __construct(
			$dbcon,
			$jobqueueid,
			$serverinstanceid,
			$addedtime,
			$jobname,
			$jobparams
			)
		{
			$this->dbcon = $dbcon;

			$this->jobqueueid = $jobqueueid;
			$this->serverinstanceid = $serverinstanceid;
			$this->addedtime = $addedtime;
			$this->jobname = $jobname;
			$this->jobparams = $jobparams;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$serverinstanceid,
			$addedtime,
			$jobname,
			$jobparams,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBQUEUEROW, __METHOD__);

			DBG_INFO(
					DBGZ_JOBQUEUEROW,
					__METHOD__,
					"Inserting row with serverinstanceid=$serverinstanceid, addedtime=$addedtime"
					);

			$escapedJobParams = mysqli_real_escape_string($dbcon, $jobparams);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_jobqueue (serverinstanceid, addedtime, jobname, jobparams)
					VALUES ('$serverinstanceid', '$addedtime', '$jobname', '$escapedJobParams')"
					);

			if ($result)
			{
				$jobqueueid = mysqli_insert_id($dbcon);

				$object = new JobQueueRow(
						$dbcon,
						$jobqueueid,
						$serverinstanceid,
						$addedtime,
						$jobname,
						$jobparams
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBQUEUEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBQUEUEROW, __METHOD__, $result);
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

			$rows = JobQueueRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_JOBQUEUEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "jobqueueid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add jobqueueid as it's already included by default.
					if ($field != "jobqueueid")
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

			DBG_INFO(DBGZ_JOBQUEUEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_jobqueue $filterString $sortString");

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

						$rows[] = new JobQueueRow(
								$dbcon,
								isset($row['jobqueueid']) ? $row['jobqueueid'] : NULL,
								isset($row['serverinstanceid']) ? $row['serverinstanceid'] : NULL,
								isset($row['addedtime']) ? $row['addedtime'] : NULL,
								isset($row['jobname']) ? $row['jobname'] : NULL,
								isset($row['jobparams']) ? $row['jobparams'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBQUEUEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_JOBQUEUEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBQUEUEROW, __METHOD__, "jobqueueid=$this->jobqueueid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->jobqueueid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_JOBQUEUEROW, __METHOD__, "Updating row with jobqueueid=$this->jobqueueid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_jobqueue
							 SET $setString
							 WHERE jobqueueid='$this->jobqueueid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->$dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_JOBQUEUEROW, __METHOD__, "Must set jobqueueid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_JOBQUEUEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_JOBQUEUEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBQUEUEROW, __METHOD__);

			$result = FALSE;

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

			DBG_INFO(DBGZ_JOBQUEUEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE core_jobqueue SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBQUEUEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBQUEUEROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBQUEUEROW, __METHOD__);

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

			DBG_INFO(DBGZ_JOBQUEUEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM core_jobqueue $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBQUEUEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBQUEUEROW, __METHOD__, $result);
			return $result;
		}
	}
?>
