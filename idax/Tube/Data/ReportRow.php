<?php

	namespace Idax\Tube\Data;

	require_once '/home/idax/idax.php';

	class ReportRow
	{
		private $dbcon = NULL;

		private $reportid = NULL;
		private $creationdate = NULL;
		private $ingestionid = NULL;
		private $reportformat = NULL;
		private $reportparameters = NULL;
		private $jobsiteid = NULL;
		private $sitecode = NULL;
		private $starttime = NULL;
		private $endtime = NULL;
		private $type = NULL;
		private $bucketfilename = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getReportId()
		{
			return $this->reportid;
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

		public function getIngestionId()
		{
			return $this->ingestionid;
		}

		public function setIngestionId($value)
		{
			if ($this->ingestionid != $value)
			{
				$this->ingestionid = $value;
				$this->fieldUpdated('ingestionid', $value);
			}
		}

		public function getReportFormat()
		{
			return $this->reportformat;
		}

		public function setReportFormat($value)
		{
			if ($this->reportformat != $value)
			{
				$this->reportformat = $value;
				$this->fieldUpdated('reportformat', $value);
			}
		}

		public function getReportParameters()
		{
			return $this->reportparameters;
		}

		public function setReportParameters($value)
		{
			if ($this->reportparameters != $value)
			{
				$this->reportparameters = $value;
				$this->fieldUpdated('reportparameters', $value);
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

		public function getSiteCode()
		{
			return $this->sitecode;
		}

		public function setSiteCode($value)
		{
			if ($this->sitecode != $value)
			{
				$this->sitecode = $value;
				$this->fieldUpdated('sitecode', $value);
			}
		}

		public function getStartTime()
		{
			return $this->starttime;
		}

		public function setStartTime($value)
		{
			if ($this->starttime != $value)
			{
				$this->starttime = $value;
				$this->fieldUpdated('starttime', $value);
			}
		}

		public function getEndTime()
		{
			return $this->endtime;
		}

		public function setEndTime($value)
		{
			if ($this->endtime != $value)
			{
				$this->endtime = $value;
				$this->fieldUpdated('endtime', $value);
			}
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

		public function getBucketFilename()
		{
			return $this->bucketfilename;
		}

		public function setBucketFilename($value)
		{
			if ($this->bucketfilename != $value)
			{
				$this->bucketfilename = $value;
				$this->fieldUpdated('bucketfilename', $value);
			}
		}

		public function __construct(
			$dbcon,
			$reportid,
			$creationdate,
			$ingestionid,
			$reportformat,
			$reportparameters,
			$jobsiteid,
			$sitecode,
			$starttime,
			$endtime,
			$type,
			$bucketfilename
			)
		{
			$this->dbcon = $dbcon;

			$this->reportid = $reportid;
			$this->creationdate = $creationdate;
			$this->ingestionid = $ingestionid;
			$this->reportformat = $reportformat;
			$this->reportparameters = $reportparameters;
			$this->jobsiteid = $jobsiteid;
			$this->sitecode = $sitecode;
			$this->starttime = $starttime;
			$this->endtime = $endtime;
			$this->type = $type;
			$this->bucketfilename = $bucketfilename;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$creationdate,
			$ingestionid,
			$reportformat,
			$reportparameters,
			$jobsiteid,
			$sitecode,
			$starttime,
			$endtime,
			$type,
			$bucketfilename,
			&$reportObject = NULL,
			&$sqlError = NULL
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTROW, __METHOD__, "Inserting row with ingestionid=$ingestionid, jobsiteid=$jobsiteid");

			$escapedReportParameters = mysqli_real_escape_string($dbcon, $reportparameters);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tube_reports (creationdate, ingestionid, reportformat, reportparameters, jobsiteid, sitecode, starttime, endtime, type, bucketfilename)
					VALUES ('$creationdate', '$ingestionid', '$reportformat', '$escapedReportParameters', '$jobsiteid', '$sitecode', '$starttime', '$endtime', '$type', '$bucketfilename')"
					);

			if ($result)
			{
				$reportid = mysqli_insert_id($dbcon);

				$reportObject = new ReportRow(
						$dbcon,
						$reportid,
						$creationdate,
						$ingestionid,
						$reportformat,
						$reportparameters,
						$jobsiteid,
						$sitecode,
						$starttime,
						$endtime,
						$type,
						$bucketfilename
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTROW, __METHOD__, $result);
			return $result;
		}

		static public function FindOne(
			$dbcon,
			$fields,
			$filters,
			$sortOrder,
			$returnType
			)
		{
			$row = NULL;

			$rows = ReportRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			$returnType
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTROW, __METHOD__);

			$rows = NULL;

			$selectFields = "reportid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add reportid as it's already included by default.
					if ($field != "reportid")
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

			DBG_INFO(DBGZ_TUBE_REPORTROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tube_reports $filterString $sortString");

			if ($result)
			{
				$numRows = mysqli_num_rows($result);

				for ($i=0; $i<$numRows; $i++)
				{
					if ($returnType == ROW_NUMERIC)
					{
						$rows[] = mysqli_fetch_array($result);
					}
					else if ($returnType == ROW_ASSOCIATIVE)
					{
						$rows[] = mysqli_fetch_assoc($result);
					}
					else
					{
						$row = mysqli_fetch_array($result);

						$rows[] = new ReportRow(
								$dbcon,
								isset($row['reportid']) ? $row['reportid'] : NULL,
								isset($row['creationdate']) ? $row['creationdate'] : NULL,
								isset($row['ingestionid']) ? $row['ingestionid'] : NULL,
								isset($row['reportformat']) ? $row['reportformat'] : NULL,
								isset($row['reportparameters']) ? $row['reportparameters'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['sitecode']) ? $row['sitecode'] : NULL,
								isset($row['starttime']) ? $row['starttime'] : NULL,
								isset($row['endtime']) ? $row['endtime'] : NULL,
								isset($row['type']) ? $row['type'] : NULL,
								isset($row['bucketfilename']) ? $row['bucketfilename'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TUBE_REPORTROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields()
		{
			DBG_ENTER(DBGZ_TUBE_REPORTROW, __METHOD__, "reportid=$this->reportid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->reportid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_TUBE_REPORTROW, __METHOD__, "Updating row with reportid=$this->reportid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_tube_reports
							 SET $setString
							 WHERE reportid='$this->reportid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_TUBE_REPORTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_TUBE_REPORTROW, __METHOD__, "Must set reportid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_TUBE_REPORTROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_REPORTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tube_reports SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_REPORTROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tube_reports $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTROW, __METHOD__, $result);
			return $result;
		}
	}
?>
