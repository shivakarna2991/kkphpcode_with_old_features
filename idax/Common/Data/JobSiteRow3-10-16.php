<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class JobSiteRow
	{
		private $dbcon = NULL;

		private $jobsiteid = NULL;
		private $testdata = NULL;
		private $sitecode = NULL;
		private $taskid = NULL;
		private $jobid = NULL;
		private $latitude = NULL;
		private $longitude = NULL;
		private $creationtime = NULL;
		private $setupdate = NULL;
		private $durations = NULL;
		private $timeblocks = NULL;
		private $state = NULL;
		private $status = NULL;
		private $description = NULL;
		private $notes = NULL;
		private $n_street = NULL;
		private $s_street = NULL;
		private $e_street = NULL;
		private $w_street = NULL;
		private $ne_street = NULL;
		private $nw_street = NULL;
		private $se_street = NULL;
		private $sw_street = NULL;
		private $direction = NULL;
		private $oneway = NULL;
		private $countpriority = NULL;
		private $reportformat = NULL;
		private $reportparameters = NULL;
		private $lastupdatetime = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getJobSiteId()
		{
			return $this->jobsiteid;
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

		public function getTaskId()
		{
			return $this->taskid;
		}

		public function setTaskId($value)
		{
			if ($this->taskid != $value)
			{
				$this->taskid = $value;
				$this->fieldUpdated('taskid', $value);
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

		public function getCreationTime()
		{
			return $this->creationtime;
		}

		public function setCreationTime($value)
		{
			if ($this->creationtime != $value)
			{
				$this->creationtime = $value;
				$this->fieldUpdated('creationtime', $value);
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

		public function getDurations()
		{
			return $this->durations;
		}

		public function setDurations($value)
		{
			if ($this->durations != $value)
			{
				$this->durations = $value;
				$this->fieldUpdated('durations', $value);
			}
		}

		public function getTimeBlocks()
		{
			return $this->timeblocks;
		}

		public function setTimeBlocks($value)
		{
			if ($this->timeblocks != $value)
			{
				$this->timeblocks = $value;
				$this->fieldUpdated('timeblocks', $value);
			}
		}

		public function getState()
		{
			return $this->state;
		}

		public function setState($value)
		{
			if ($this->state != $value)
			{
				$this->state = $value;
				$this->fieldUpdated('state', $value);
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

		public function getDescription()
		{
			return $this->description;
		}

		public function setDescription($value)
		{
			if ($this->description != $value)
			{
				$this->description = $value;
				$this->fieldUpdated('description', $value);
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

		public function getNStreet()
		{
			return $this->n_street;
		}

		public function setNStreet($value)
		{
			if ($this->n_street != $value)
			{
				$this->n_street = $value;
				$this->fieldUpdated('n_street', $value);
			}
		}

		public function getSStreet()
		{
			return $this->s_street;
		}

		public function setSStreet($value)
		{
			if ($this->s_street != $value)
			{
				$this->s_street = $value;
				$this->fieldUpdated('s_street', $value);
			}
		}

		public function getEStreet()
		{
			return $this->e_street;
		}

		public function setEStreet($value)
		{
			if ($this->e_street != $value)
			{
				$this->e_street = $value;
				$this->fieldUpdated('e_street', $value);
			}
		}

		public function getWStreet()
		{
			return $this->w_street;
		}

		public function setWStreet($value)
		{
			if ($this->w_street != $value)
			{
				$this->w_street = $value;
				$this->fieldUpdated('w_street', $value);
			}
		}

		public function getNEStreet()
		{
			return $this->ne_street;
		}

		public function setNEStreet($value)
		{
			if ($this->ne_street != $value)
			{
				$this->ne_street = $value;
				$this->fieldUpdated('ne_street', $value);
			}
		}

		public function getNWStreet()
		{
			return $this->nw_street;
		}

		public function setNWStreet($value)
		{
			if ($this->nw_street != $value)
			{
				$this->nw_street = $value;
				$this->fieldUpdated('nw_street', $value);
			}
		}

		public function getSEStreet()
		{
			return $this->se_street;
		}

		public function setSEStreet($value)
		{
			if ($this->se_street != $value)
			{
				$this->se_street = $value;
				$this->fieldUpdated('se_street', $value);
			}
		}

		public function getSWStreet()
		{
			return $this->sw_street;
		}

		public function setSWStreet($value)
		{
			if ($this->sw_street != $value)
			{
				$this->sw_street = $value;
				$this->fieldUpdated('sw_street', $value);
			}
		}

		public function getDirection()
		{
			return $this->direction;
		}

		public function setDirection($value)
		{
			if ($this->direction != $value)
			{
				$this->direction = $value;
				$this->fieldUpdated('direction', $value);
			}
		}

		public function getOneWay()
		{
			return $this->oneway;
		}

		public function setOneWay($value)
		{
			if ($this->oneway != $value)
			{
				$this->oneway = $value;
				$this->fieldUpdated('oneway', $value);
			}
		}

		public function getCountPriority()
		{
			return $this->countpriority;
		}

		public function setCountPriority($value)
		{
			if ($this->countpriority != $value)
			{
				$this->countpriority = $value;
				$this->fieldUpdated('countpriority', $value);
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
			$jobsiteid,
			$testdata,
			$sitecode,
			$taskid,
			$jobid,
			$latitude,
			$longitude,
			$creationtime,
			$setupdate,
			$durations,
			$timeblocks,
			$state,
			$status,
			$description,
			$notes,
			$n_street,
			$s_street,
			$e_street,
			$w_street,
			$ne_street,
			$nw_street,
			$se_street,
			$sw_street,
			$direction,
			$oneway,
			$countpriority,
			$reportformat,
			$reportparameters,
			$lastupdatetime
			)
		{
			$this->dbcon = $dbcon;

			$this->jobsiteid = $jobsiteid;
			$this->testdata = $testdata;
			$this->sitecode = $sitecode;
			$this->taskid = $taskid;
			$this->jobid = $jobid;
			$this->latitude = $latitude;
			$this->longitude = $longitude;
			$this->creationtime = $creationtime;
			$this->setupdate = $setupdate;
			$this->durations = $durations;
			$this->timeblocks = $timeblocks;
			$this->state = $state;
			$this->status = $status;
			$this->description = $description;
			$this->notes = $notes;
			$this->n_street = $n_street;
			$this->s_street = $s_street;
			$this->e_street = $e_street;
			$this->w_street = $w_street;
			$this->ne_street = $ne_street;
			$this->nw_street = $nw_street;
			$this->se_street = $se_street;
			$this->sw_street = $sw_street;
			$this->direction = $direction;
			$this->oneway = $oneway;
			$this->countpriority = $countpriority;
			$this->reportformat = $reportformat;
			$this->reportparameters = $reportparameters;
			$this->lastupdatetime = $lastupdatetime;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$testdata,
			$sitecode,
			$taskid,
			$jobid,
			$latitude,
			$longitude,
			$creationtime,
			$setupdate,
			$durations,
			$timeblocks,
			$state,
			$status,
			$description,
			$notes,
			$n_street,
			$s_street,
			$e_street,
			$w_street,
			$ne_street,
			$nw_street,
			$se_street,
			$sw_street,
			$direction,
			$oneway,
			$countpriority,
			$reportformat,
			$reportparameters,
			$lastupdatetime,
			&$jobsiteRow,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBSITEROW, __METHOD__);

			DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "Inserting row with sitecode=$sitecode, taskid=$taskid, jobid=$jobid, timeblocks=$timeblocks");

			$retval = FALSE;

			$escapedSiteCode = mysqli_real_escape_string($dbcon, $sitecode);
			$escapedDescription = mysqli_real_escape_string($dbcon, $description);
			$escapedNotes = mysqli_real_escape_string($dbcon, $notes);
			$escapedNStreet = mysqli_real_escape_string($dbcon, $n_street);
			$escapedSStreet = mysqli_real_escape_string($dbcon, $s_street);
			$escapedEStreet = mysqli_real_escape_string($dbcon, $e_street);
			$escapedWStreet = mysqli_real_escape_string($dbcon, $w_street);
			$escapedNEStreet = mysqli_real_escape_string($dbcon, $ne_street);
			$escapedNWStreet = mysqli_real_escape_string($dbcon, $nw_street);
			$escapedSEStreet = mysqli_real_escape_string($dbcon, $se_street);
			$escapedSWStreet = mysqli_real_escape_string($dbcon, $sw_street);
			$escapedReportParameters = mysqli_real_escape_string($dbcon, $reportparameters);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_jobsites (testdata, sitecode, taskid, jobid,
							latitude, longitude, creationtime, setupdate, durations, timeblocks, state, status,
							description, notes, n_street, s_street, e_street, w_street,
							ne_street, nw_street, se_street, sw_street, direction, oneway, countpriority,
							reportformat, reportparameters, lastupdatetime)
					VALUES ('$testdata', '$escapedSiteCode', '$taskid', '$jobid',
							'$latitude', '$longitude', '$creationtime', '$setupdate', '$durations', '$timeblocks', '$state', '$status',
							'$escapedDescription', '$escapedNotes', '$escapedNStreet', '$escapedSStreet', '$escapedEStreet', '$escapedWStreet',
							'$escapedNEStreet', '$escapedNWStreet', '$escapedSEStreet', '$escapedSWStreet', '$direction', '$oneway', '$countpriority',
							'$reportformat', '$escapedReportParameters', '$lastupdatetime')"
					);

			if ($result)
			{
				$jobsiteid = mysqli_insert_id($dbcon);

				$jobsiteRow = new JobSiteRow(
						$dbcon,
						$jobsiteid,
						$testdata,
						$sitecode,
						$taskid,
						$jobid,
						$latitude,
						$longitude,
						$creationtime,
						$setupdate,
						$durations,
						$timeblocks,
						$state,
						$status,
						$description,
						$notes,
						$n_street,
						$s_street,
						$e_street,
						$w_street,
						$ne_street,
						$nw_street,
						$se_street,
						$sw_street,
						$direction,
						$oneway,
						$countpriority,
						$reportformat,
						$reportparameters,
						$lastupdatetime
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBSITEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITEROW, __METHOD__, $result);
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

			$rows = JobSiteRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_JOBSITEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "jobsiteid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add jobsiteid as it's already included by default.
					if ($field != "jobsiteid")
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

			DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_jobsites $filterString $sortString");

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
						$jobsiteidinqry=$rows[$i]['jobsiteid'];
						// echo "<pre>";print_r($rows[$i]['jobsiteid']);echo"</pre>";
						$joinresult = mysqli_query($dbcon, "SELECT lay.status FROM idax_jobsites jb inner join idax_video_files vf on jb.jobsiteid=vf.jobsiteid inner join idax_video_layouts lay on lay.videoid=vf.videoid where vf.jobsiteid=".$jobsiteidinqry);
					while ($layoutarray = mysqli_fetch_array($joinresult)) {
					$new_array[] = $layoutarray['status'];
					if (in_array("QC_STARTED", $new_array))
				  {
				  $rows[$i]['status']="UPLOADED";
				  }
				else if (in_array("COUNT_PAUSED", $new_array))
				  {
				  $rows[$i]['status']="PROCESSING";
				  }
				  else if (in_array("QC_PAUSED", $new_array))
				  {
					$rows[$i]['status']="PROCESSING";  
				  }
				  else if (in_array("QC_COMPLETED", $new_array))
				  {
					$rows[$i]['status']="COMPLETE";  
				  }
				  else if (in_array("COUNT_COMPLETED", $new_array))
				  {
				  $rows[$i]['status']="COMPLETE";
				  }
				  
					//echo "<pre>";print_r($rows[$i]['status']);echo"</pre>";
				}					
					}
					else
					{
						$row = mysqli_fetch_array($result);

						$rows[] = new JobSiteRow(
								$dbcon,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['testdata']) ? $row['testdata'] : NULL,
								isset($row['sitecode']) ? $row['sitecode'] : NULL,
								isset($row['taskid']) ? $row['taskid'] : NULL,
								isset($row['jobid']) ? $row['jobid'] : NULL,
								isset($row['latitude']) ? $row['latitude'] : NULL,
								isset($row['longitude']) ? $row['longitude'] : NULL,
								isset($row['creationtime']) ? $row['creationtime'] : NULL,
								isset($row['setupdate']) ? $row['setupdate'] : NULL,
								isset($row['durations']) ? $row['durations'] : NULL,
								isset($row['timeblocks']) ? $row['timeblocks'] : NULL,
								isset($row['state']) ? $row['state'] : NULL,
								isset($row['status']) ? $row['status'] : NULL,
								isset($row['description']) ? $row['description'] : NULL,
								isset($row['notes']) ? $row['notes'] : NULL,
								isset($row['n_street']) ? $row['n_street'] : NULL,
								isset($row['s_street']) ? $row['s_street'] : NULL,
								isset($row['e_street']) ? $row['e_street'] : NULL,
								isset($row['w_street']) ? $row['w_street'] : NULL,
								isset($row['ne_street']) ? $row['ne_street'] : NULL,
								isset($row['nw_street']) ? $row['nw_street'] : NULL,
								isset($row['se_street']) ? $row['se_street'] : NULL,
								isset($row['sw_street']) ? $row['sw_street'] : NULL,
								isset($row['direction']) ? $row['direction'] : NULL,
								isset($row['oneway']) ? $row['oneway'] : NULL,
								isset($row['countpriority']) ? $row['countpriority'] : NULL,
								isset($row['reportformat']) ? $row['reportformat'] : NULL,
								isset($row['reportparameters']) ? $row['reportparameters'] : NULL,
								isset($row['lastupdatetime']) ? $row['lastupdatetime'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBSITEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_JOBSITEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBSITEROW, __METHOD__, "jobsiteid=$this->jobsiteid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->jobsiteid != NULL)
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

					DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "Updating row with jobsiteid=$this->jobsiteid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_jobsites
							 SET $setString
							 WHERE jobsiteid='$this->jobsiteid'"
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
					DBG_WARN(DBGZ_JOBSITEROW, __METHOD__, "Must set jobsiteid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBSITEROW, __METHOD__);

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

			DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_jobsites SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBSITEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITEROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_JOBSITEROW, __METHOD__);

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

			DBG_INFO(DBGZ_JOBSITEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_jobsites $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_JOBSITEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITEROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
