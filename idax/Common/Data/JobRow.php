<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class JobRow
	{
		private $dbcon = NULL;

		private $jobid = NULL;
		private $testdata = NULL;
		private $number = NULL;
		private $name = NULL;
		private $nickname = NULL;
		private $office = NULL;
		private $area = NULL;
		private $notes = NULL;
		private $active = NULL;
		private $status = NULL;
		private $creationdate = NULL;
		private $orderdate = NULL;
		private $deliverydate = NULL;
		private $studytype = NULL;
		private $lastupdatetime = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getJobId()
		{
			return $this->jobid;
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

		public function getNumber()
		{
			return $this->number;
		}

		public function setNumber($value)
		{
			if ($this->number != $value)
			{
				$this->number = $value;
				$this->fieldUpdated('number', $value);
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

		public function getNickname()
		{
			return $this->nickname;
		}

		public function setNickname($value)
		{
			if ($this->nickname != $value)
			{
				$this->nickname = $value;
				$this->fieldUpdated('nickname', $value);
			}
		}

		public function getOffice()
		{
			return $this->office;
		}

		public function setOffice($value)
		{
			if ($this->office != $value)
			{
				$this->office = $value;
				$this->fieldUpdated('office', $value);
			}
		}

		public function getArea()
		{
			return $this->area;
		}

		public function setArea($value)
		{
			if ($this->area != $value)
			{
				$this->area = $value;
				$this->fieldUpdated('area', $value);
			}
		}

		public function getNotes()
		{
			return $this->notes;
		}

		public function setNotes($value)
		{
			if ($this->notes != $value)
			{
				$this->notes = $value;
				$this->fieldUpdated('notes', $value);
			}
		}

		public function getActive()
		{
			return $this->active;
		}

		public function setActive($value)
		{
			if ($this->active != $value)
			{
				$this->active = $value;
				$this->fieldUpdated('active', $value);
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

		public function getCreationDate()
		{
			return $this->creationdate;
		}

		public function setCreationDate($value)
		{
			if ($this->creationdate != $value)
			{
				$this->creationdate = $value;
				$this->fieldUpdated('creationdate', $value);
			}
		}

		public function getOrderDate()
		{
			return $this->orderdate;
		}

		public function setOrderDate($value)
		{
			if ($this->orderdate != $value)
			{
				$this->orderdate = $value;
				$this->fieldUpdated('orderdate', $value);
			}
		}

		public function getDeliveryDate()
		{
			return $this->deliverydate;
		}

		public function setDeliveryDate($value)
		{
			if ($this->deliverydate != $value)
			{
				$this->deliverydate = $value;
				$this->fieldUpdated('deliverydate', $value);
			}
		}

		public function getStudyType()
		{
			return $this->studytype;
		}

		public function setStudyType($value)
		{
			if ($this->studytype != $value)
			{
				$this->studytype = $value;
				$this->fieldUpdated('studytype', $value);
			}
		}

		public function getLastUpdateTime()
		{
			return $this->lastupdatetime;
		}

		public function setLastUpdateTime($value)
		{
			if ($this->lastupdatetime != $value)
			{
				$this->lastupdatetime = $value;
				$this->fieldUpdated('lastupdatetime', $value);
			}
		}

		public function __construct(
			$dbcon,
			$jobid,
			$testdata,
			$number,
			$name,
			$nickname,
			$studytype,
			$office,
			$area,
			$notes,
			$active,
			$status,
			$creationdate,
			$orderdate,
			$deliverydate,
			$lastupdatetime
			)
		{
			$this->dbcon = $dbcon;

			$this->jobid = $jobid;
			$this->testdata = $testdata;
			$this->number = $number;
			$this->name = $name;
			$this->nickname = $nickname;
			$this->studytype = $studytype;
			$this->office = $office;
			$this->area = $area;
			$this->notes = $notes;
			$this->active = $active;
			$this->status = $status;
			$this->creationdate = $creationdate;
			$this->orderdate = $orderdate;
			$this->deliverydate = $deliverydate;
			$this->lastupdatetime = $lastupdatetime;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$testdata,
			$number,
			$name,
			$nickname,
			$studytype,
			$office,
			$area,
			$notes,
			$active,
			$status,
			$creationdate,
			$orderdate,
			$deliverydate,
			$lastupdatetime,
			&$jobRow,
			&$sqlError
			)
		{
                    $orderdate = (isset($orderdate) && $orderdate!='') ? $orderdate : '0000-00-00';
                    $deliverydate = (isset($deliverydate) && $deliverydate!='') ? $deliverydate : '0000-00-00';
                    //echo '$orderDate---'.$orderdate.'---$deliveryDate'.$deliverydate.'$creationdate---'.$creationdate.'---$lastupdatetime'.$lastupdatetime;exit;
			DBG_ENTER(DBGZ_JOBROW, __METHOD__);

			DBG_INFO(DBGZ_JOBROW, __METHOD__, "Inserting row with number=$number, name=$name");

			$result = FALSE;

			$escapedName = mysqli_real_escape_string($dbcon, $name);
			$escapedNickname = mysqli_real_escape_string($dbcon, $nickname);
			$escapedNotes = mysqli_real_escape_string($dbcon, $notes);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_jobs (testdata, number, name, nickname, studytype, office, area, notes, active, status,
							creationdate, orderdate, deliverydate, lastupdatetime)
					VALUES ('$testdata', '$number', '$escapedName', '$escapedNickname', '$studytype', '$office', '$area', '$escapedNotes', '$active', '$status',
							'$creationdate', '$orderdate', '$deliverydate', '$lastupdatetime')"
					);

			if ($result)
			{
				$jobid = mysqli_insert_id($dbcon);

				$jobRow = new JobRow(
						$dbcon,
						$jobid,
						$testdata,
						$number,
						$name,
						$nickname,
						$studytype,
						$office,
						$area,
						$notes,
						$active,
						$status,
						$creationdate,
						$orderdate,
						$deliverydate,
						$lastupdatetime
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBROW, __METHOD__, $result);
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

			$rows = JobRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_JOBROW, __METHOD__);

			$rows = NULL;

			$selectFields = "jobid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add jobid as it's already included by default.
					if ($field != "jobid")
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

			DBG_INFO(DBGZ_JOBROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_jobs $filterString $sortString");

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

						$rows[] = new JobRow(
								$dbcon,
								isset($row['jobid']) ? $row['jobid'] : NULL,
								isset($row['testdata']) ? $row['testdata'] : NULL,
								isset($row['number']) ? $row['number'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['nickname']) ? $row['nickname'] : NULL,
								isset($row['studytype']) ? $row['studytype'] : NULL,
								isset($row['office']) ? $row['office'] : NULL,
								isset($row['area']) ? $row['area'] : NULL,
								isset($row['notes']) ? $row['notes'] : NULL,
								isset($row['active']) ? $row['active'] : NULL,
								isset($row['status']) ? $row['status'] : NULL,
								isset($row['creationdate']) ? $row['creationdate'] : NULL,
								isset($row['orderdate']) ? $row['orderdate'] : NULL,
								isset($row['deliverydate']) ? $row['deliverydate'] : NULL,
								isset($row['lastupdatetime']) ? $row['lastupdatetime'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_JOBROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBROW, __METHOD__, "jobid=$this->jobid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->jobid != NULL)
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

					DBG_INFO(DBGZ_JOBROW, __METHOD__, "Updating row with jobid=$this->jobid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_jobs
							 SET $setString
							 WHERE jobid='$this->jobid'"
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
					DBG_WARN(DBGZ_JOBROW, __METHOD__, "Must set jobid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_JOBROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_JOBROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBROW, __METHOD__);

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

			DBG_INFO(DBGZ_JOBROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_jobs SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBROW, __METHOD__);

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

			DBG_INFO(DBGZ_JOBROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_jobs $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
