<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class FileRow
	{
		private $dbcon = NULL;

		private $videoid = NULL;
		private $testdata = NULL;
		private $jobsiteid = NULL;
		private $name = NULL;
		private $cameralocation = NULL;
		private $filesize = NULL;
		private $uploadtime = NULL;
		private $addedtime = NULL;
		private $capturestarttime = NULL;
		private $captureendtime = NULL;
		private $bucketfileprefix  = NULL;
		private $status = NULL;
		private $lastupdatetime = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getVideoId()
		{
			return $this->videoid;
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

		public function getCameraLocation()
		{
			return $this->cameralocation;
		}

		public function setCameraLocation($value)
		{
			if ($this->cameralocation != $value)
			{
				$this->cameralocation = $value;
				$this->fieldUpdated('cameralocation', $value);
			}
		}

		public function getFileSize()
		{
			return $this->filesize;
		}

		public function setFileSize($value)
		{
			if ($this->filesize != $value)
			{
				$this->filesize = $value;
				$this->fieldUpdated('filesize', $value);
			}
		}

		public function getUploadTime()
		{
			return $this->uploadtime;
		}

		public function setUploadTime($value)
		{
			if ($this->uploadtime != $value)
			{
				$this->uploadtime = $value;
				$this->fieldUpdated('uploadtime', $value);
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

		public function getCaptureStartTime()
		{
			return $this->capturestarttime;
		}

		public function setCaptureStartTime($value)
		{
			if ($this->capturestarttime != $value)
			{
				$this->capturestarttime = $value;
				$this->fieldUpdated('capturestarttime', $value);
			}
		}

		public function getCaptureEndTime()
		{
			return $this->captureendtime;
		}

		public function setCaptureEndTime($value)
		{
			if ($this->captureendtime != $value)
			{
				$this->captureendtime = $value;
				$this->fieldUpdated('captureendtime', $value);
			}
		}

		public function getBucketFilePrefix()
		{
			return $this->bucketfileprefix;
		}

		public function setBucketFilePrefix($value)
		{
			if ($this->bucketfileprefix != $value)
			{
				$this->bucketfileprefix = $value;
				$this->fieldUpdated('bucketfileprefix', $value);
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
			$videoid,
			$testdata,
			$jobsiteid,
			$name,
			$cameralocation,
			$filesize,
			$uploadtime,
			$addedtime,
			$capturestarttime,
			$captureendtime,
			$bucketfileprefix,
			$status,
			$lastupdatetime
			)
		{
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__, "videoid=$videoid");

			$this->dbcon = $dbcon;

			$this->videoid = $videoid;
			$this->testdata = $testdata;
			$this->jobsiteid = $jobsiteid;
			$this->name = $name;
			$this->cameralocation = $cameralocation;
			$this->filesize = $filesize;
			$this->uploadtime = $uploadtime;
			$this->addedtime = $addedtime;
			$this->capturestarttime = $capturestarttime;
			$this->captureendtime = $captureendtime;
			$this->bucketfileprefix = $bucketfileprefix;
			$this->status = $status;
			$this->lastupdatetime = $lastupdatetime;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$testdata,
			$jobsiteid,
			$name,
			$cameralocation,
			$filesize,
			$uploadtime,
			$addedtime,
			$capturestarttime,
			$captureendtime,
			$bucketfileprefix,
			$status,
			$lastupdatetime,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__);

			DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "Inserting row with jobsiteid=$jobsiteid, name=$name");

			$escapedName = mysqli_real_escape_string($dbcon, $name);
			$escapedCameralocation = mysqli_real_escape_string($dbcon, $cameralocation);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_files (testdata, jobsiteid, name, cameralocation, filesize, uploadtime, addedtime, capturestarttime, captureendtime,
					 		bucketfileprefix, status, lastupdatetime)
					VALUES ('$testdata', '$jobsiteid', '$escapedName', '$escapedCameralocation', '$filesize', '$uploadtime', '$addedtime', '$capturestarttime', '$captureendtime',
							'$bucketfileprefix', '$status', '$lastupdatetime')"
					);

			if ($result)
			{
				$videoid = mysqli_insert_id($dbcon);

				$object = new FileRow(
						$dbcon,
						$videoid,
						$testdata,
						$jobsiteid,
						$name,
						$cameralocation,
						$filesize,
						$uploadtime,
						$addedtime,
						$capturestarttime,
						$captureendtime,
						$bucketfileprefix,
						$status,
						$lastupdatetime
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_FILEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_FILEROW, __METHOD__, $result);
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

			$rows = FileRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "videoid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add videoid as it's already included by default.
					if ($field != "videoid")
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

			DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_files $filterString $sortString");

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

						$rows[] = new FileRow(
								$dbcon,
								isset($row['videoid']) ? $row['videoid'] : NULL,
								isset($row['testdata']) ? $row['testdata'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['cameralocation']) ? $row['cameralocation'] : NULL,
								isset($row['filesize']) ? $row['filesize'] : NULL,
								isset($row['uploadtime']) ? $row['uploadtime'] : NULL,
								isset($row['addedtime']) ? $row['addedtime'] : NULL,
								isset($row['capturestarttime']) ? $row['capturestarttime'] : NULL,
								isset($row['captureendtime']) ? $row['captureendtime'] : NULL,
								isset($row['bucketfileprefix']) ? $row['bucketfileprefix'] : NULL,
								isset($row['status']) ? $row['status'] : NULL,
								isset($row['lastupdatetime']) ? $row['lastupdatetime'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_FILEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_VIDEO_FILEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__, "videoid=$this->videoid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->videoid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$changedField = mysqli_real_escape_string($this->dbcon, $changedField);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "Updating row with videoid=$this->videoid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_files
							 SET $setString
							 WHERE videoid='$this->videoid'"
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
					DBG_WARN(DBGZ_VIDEO_FILEROW, __METHOD__, "Must set videoid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_FILEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_files SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_FILEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_FILEROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_FILEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_FILEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_files $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_FILEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_FILEROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
