<?php

	namespace Idax\Tube\Data;

	require_once '/home/idax/idax.php';

	class IngestionRow
	{
		private $dbcon = NULL;

		private $ingestionid = NULL;
		private $jobsiteid = NULL;
		private $accountid = NULL;
		private $ingestdate = NULL;
		private $ingestionkey = NULL;
		private $reversed = NULL;
		private $bucketfilename = NULL;
		private $title = NULL;
		private $subtitle = NULL;
		private $description = NULL;
		private $filterbegintime = NULL;
		private $filterendtime = NULL;
		private $includedclasses = NULL;
		private $speedrangelow = NULL;
		private $speedrangehigh = NULL;
		private $direction = NULL;
		private $separation = NULL;
		private $name = NULL;
		private $scheme = NULL;
		private $units = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getIngestionId()
		{
			return $this->ingestionid;
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

		public function getAccountId()
		{
			return $this->accountid;
		}

		public function setAccountId($value)
		{
			if ($this->accountid != $value)
			{
				$this->accountid = $value;
				$this->fieldUpdated('accountid', $value);
			}
		}

		public function getIngestDate()
		{
			return $this->ingestdate;
		}

		public function setIngestDate($value)
		{
			if ($this->ingestdate != $value)
			{
				$this->ingestdate = $value;
				$this->fieldUpdated('ingestdate', $value);
			}
		}

		public function getIngestionKey()
		{
			return $this->ingestionkey;
		}

		public function setIngestionKey($value)
		{
			if ($this->ingestionkey != $value)
			{
				$this->ingestionkey = $value;
				$this->fieldUpdated('ingestionkey', $value);
			}
		}

		public function getReversed()
		{
			return $this->reversed;
		}

		public function setReversed($value)
		{
			if ($this->reversed != $value)
			{
				$this->reversed = $value;
				$this->fieldUpdated('reversed', $value);
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

		public function getTitle()
		{
			return $this->title;
		}

		public function setTitle($value)
		{
			if ($this->title != $value)
			{
				$this->title = $value;
				$this->fieldUpdated('title', $value);
			}
		}

		public function getSubTitle()
		{
			return $this->subtitle;
		}

		public function setSubTitle($value)
		{
			if ($this->subtitle != $value)
			{
				$this->subtitle = $value;
				$this->fieldUpdated('subtitle', $value);
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

		public function getFilterBeginTime()
		{
			return $this->filterbegintime;
		}

		public function setFilterBeginTime($value)
		{
			if ($this->filterbegintime != $value)
			{
				$this->filterbegintime = $value;
				$this->fieldUpdated('filterbegintime', $value);
			}
		}

		public function getFilterEndTime()
		{
			return $this->filterendtime;
		}

		public function setFilterEndTime($value)
		{
			if ($this->filterendtime != $value)
			{
				$this->filterendtime = $value;
				$this->fieldUpdated('filterendtime', $value);
			}
		}

		public function getIncludedClasses()
		{
			return $this->includedclasses;
		}

		public function setIncludedClasses($value)
		{
			if ($this->includedclasses != $value)
			{
				$this->includedclasses = $value;
				$this->fieldUpdated('includedclasses', $value);
			}
		}

		public function getSpeedRangeHigh()
		{
			return $this->speedrangehigh;
		}

		public function setSpeedRangeHigh($value)
		{
			if ($this->speedrangehigh != $value)
			{
				$this->speedrangehigh = $value;
				$this->fieldUpdated('speedrangehigh', $value);
			}
		}

		public function getSpeedRangeLow()
		{
			return $this->speedrangelow;
		}

		public function setSpeedRangeLow($value)
		{
			if ($this->speedrangelow != $value)
			{
				$this->speedrangelow = $value;
				$this->fieldUpdated('speedrangelow', $value);
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

		public function getSeparation()
		{
			return $this->separation;
		}

		public function setSeparation($value)
		{
			if ($this->separation != $value)
			{
				$this->separation = $value;
				$this->fieldUpdated('separation', $value);
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

		public function getScheme()
		{
			return $this->scheme;
		}

		public function setScheme($value)
		{
			if ($this->scheme != $value)
			{
				$this->scheme = $value;
				$this->fieldUpdated('scheme', $value);
			}
		}

		public function getUnits()
		{
			return $this->units;
		}

		public function setUnits($value)
		{
			if ($this->units != $value)
			{
				$this->units = $value;
				$this->fieldUpdated('units', $value);
			}
		}

		public function __construct(
			$dbcon,
			$ingestionid,
			$jobsiteid,
			$accountid,
			$ingestdate,
			$ingestionkey,
			$reversed,
			$bucketfilename,
			$title,
			$subtitle,
			$description,
			$filterbegintime,
			$filterendtime,
			$includedclasses,
			$speedrangehigh,
			$speedrangelow,
			$direction,
			$separation,
			$name,
			$scheme,
			$units
			)
		{
			$this->dbcon = $dbcon;

			$this->ingestionid = $ingestionid;
			$this->jobsiteid = $jobsiteid;
			$this->accountid = $accountid;
			$this->ingestdate = $ingestdate;
			$this->ingestionkey = $ingestionkey;
			$this->reversed = $reversed;
			$this->bucketfilename = $bucketfilename;
			$this->title = $title;
			$this->subtitle = $subtitle;
			$this->description = $description;
			$this->filterbegintime = $filterbegintime;
			$this->filterendtime = $filterendtime;
			$this->includedclasses = $includedclasses;
			$this->speedrangehigh = $speedrangehigh;
			$this->speedrangelow = $speedrangelow;
			$this->direction = $direction;
			$this->separation = $separation;
			$this->name = $name;
			$this->scheme = $scheme;
			$this->units = $units;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$jobsiteid,
			$accountid,
			$ingestdate,
			$ingestionkey,
			$reversed,
			$bucketfilename,
			$title,
			$subtitle,
			$description,
			$filterbegintime,
			$filterendtime,
			$includedclasses,
			$speedrangehigh,
			$speedrangelow,
			$direction,
			$separation,
			$name,
			$scheme,
			$units,
			&$ingestionObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONROW, __METHOD__);

			DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Inserting row with jobsiteid=$jobsiteid, accountid=$accountid");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tube_ingestions (jobsiteid, accountid, ingestdate, ingestionkey, reversed, bucketfilename,
								title, subtitle, description, filterbegintime, filterendtime, includedclasses,
								speedrangehigh, speedrangelow, direction, separation, name, scheme, units)
					VALUES ('$jobsiteid', '$accountid', '$ingestdate', '$ingestionkey', '$reversed', '$bucketfilename',
							'$title', '$subtitle', '$description', '$filterbegintime', '$filterendtime', '$includedclasses',
							'$speedrangehigh', '$speedrangelow', '$direction', '$separation', '$name', '$scheme', '$units')"
					);

			if ($result)
			{
				$ingestionid = mysqli_insert_id($dbcon);

				$ingestionObject = new IngestionRow(
						$dbcon,
						$ingestionid,
						$jobsiteid,
						$accountid,
						$ingestdate,
						$ingestionkey,
						$reversed,
						$bucketfilename,
						$title,
						$subtitle,
						$description,
						$filterbegintime,
						$filterendtime,
						$includedclasses,
						$speedrangehigh,
						$speedrangelow,
						$direction,
						$separation,
						$name,
						$scheme,
						$units
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONROW, __METHOD__, $result);
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

			$rows = IngestionRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_TUBE_INGESTIONROW, __METHOD__);

			$rows = NULL;

			$selectFields = "ingestionid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add ingestionid as it's already included by default.
					if ($field != "ingestionid")
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

			DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tube_ingestions $filterString $sortString");

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

						$rows[] = new IngestionRow(
								$dbcon,
								isset($row['ingestionid']) ? $row['ingestionid'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['accountid']) ? $row['accountid'] : NULL,
								isset($row['ingestdate']) ? $row['ingestdate'] : NULL,
								isset($row['ingestionkey']) ? $row['ingestionkey'] : NULL,
								isset($row['reversed']) ? $row['reversed'] : NULL,
								isset($row['bucketfilename']) ? $row['bucketfilename'] : NULL,
								isset($row['title']) ? $row['title'] : NULL,
								isset($row['subtitle']) ? $row['subtitle'] : NULL,
								isset($row['description']) ? $row['description'] : NULL,
								isset($row['filterbegintime']) ? $row['filterbegintime'] : NULL,
								isset($row['filterendtime']) ? $row['filterendtime'] : NULL,
								isset($row['includedclasses']) ? $row['includedclasses'] : NULL,
								isset($row['speedrangehigh']) ? $row['speedrangehigh'] : NULL,
								isset($row['speedrangelow']) ? $row['speedrangelow'] : NULL,
								isset($row['direction']) ? $row['direction'] : NULL,
								isset($row['separation']) ? $row['separation'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['units']) ? $row['units'] : NULL,
								isset($row['scheme']) ? $row['scheme'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TUBE_INGESTIONROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields()
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONROW, __METHOD__, "ingestionid=$this->ingestionid");

			$result = FALSE;

			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->ingestionid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Updating row with ingestionid=$this->ingestionid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_tube_ingestions
							 SET $setString
							 WHERE ingestionid='$this->ingestionid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
				}
				else
				{
					DBG_WARN(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Must set ingestionid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tube_ingestions SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_INGESTIONROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tube_ingestions $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONROW, __METHOD__, $result);
			return $result;
		}
	}
?>
