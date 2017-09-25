<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class LayoutRow
	{
		private $dbcon = NULL;

		private $layoutid = NULL;
		private $testdata = NULL;
		private $videoid = NULL;
		private $name = NULL;
		private $status = NULL;
		private $videospeed = NULL;
		private $lastvideoposition = NULL;
		private $designedby_user = NULL;
		private $countedby_user = NULL;
		private $qcedby_user = NULL;
		private $rating  = NULL;
		private $lastupdatetime = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getLayoutId()
		{
			return $this->layoutid;
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

		public function getVideoId()
		{
			return $this->videoid;
		}

		public function setVideoId($value)
		{
			if ($this->videoid != $value)
			{
				$this->videoid = $value;
				$this->fieldUpdated('videoid', $value);
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

		public function getVideoSpeed()
		{
			return $this->videospeed;
		}

		public function setVideoSpeed($value)
		{
			if ($this->videospeed != $value)
			{
				$this->videospeed = $value;
				$this->fieldUpdated('videospeed', $value);
			}
		}

		public function getLastVideoPosition()
		{
			return $this->lastvideoposition;
		}

		public function setLastVideoPosition($value)
		{
			if ($this->lastvideoposition != $value)
			{
				$this->lastvideoposition = $value;
				$this->fieldUpdated('lastvideoposition', $value);
			}
		}

		public function getDesignedBy_User()
		{
			return $this->designedby_user;
		}

		public function setDesignedBy_User($value)
		{
			if ($this->designedby_user != $value)
			{
				$this->designedby_user = $value;
				$this->fieldUpdated('designedby_user', $value);
			}
		}

		public function getCountedBy_User()
		{
			return $this->countedby_user;
		}

		public function setCountedBy_User($value)
		{
			if ($this->countedby_user != $value)
			{
				$this->countedby_user = $value;
				$this->fieldUpdated('countedby_user', $value);
			}
		}

		public function getQCedBy_User()
		{
			return $this->qcedby_user;
		}

		public function setQCedBy_User($value)
		{
			if ($this->qcedby_user != $value)
			{
				$this->qcedby_user = $value;
				$this->fieldUpdated('qcedby_user', $value);
			}
		}

		public function getRating()
		{
			return $this->rating;
		}

		public function setRating($value)
		{
			if ($this->rating != $value)
			{
				$this->rating = $value;
				$this->fieldUpdated('rating', $value);
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
			$layoutid,
			$testdata,
			$videoid,
			$name,
			$status,
			$videospeed,
			$lastvideoposition,
			$designedby_user,
			$countedby_user,
			$qcedby_user,
			$rating,
			$lastupdatetime
			)
		{
			$this->dbcon = $dbcon;

			$this->layoutid = $layoutid;
			$this->testdata = $testdata;
			$this->videoid = $videoid;
			$this->name = $name;
			$this->status = $status;
			$this->videospeed = $videospeed;
			$this->lastvideoposition = $lastvideoposition;
			$this->designedby_user = $designedby_user;
			$this->countedby_user = $countedby_user;
			$this->qcedby_user = $qcedby_user;
			$this->rating = $rating;
			$this->lastupdatetime = $lastupdatetime;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$testdata,
			$videoid,
			$name,
			$status,
			$videospeed,
			$lastvideoposition,
			$designedby_user,
			$countedby_user,
			$qcedby_user,
			$rating,
			$lastupdatetime,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTROW, __METHOD__);

			DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Inserting row with videoid=$videoid, name=$name, status=$status");

			$retval = FALSE;

			$escapedName = mysqli_real_escape_string($dbcon, $name);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_layouts (testdata, videoid, name, status, videospeed, lastvideoposition,
							designedby_user, countedby_user, qcedby_user, rating, lastupdatetime)
					VALUES ('$testdata', '$videoid', '$escapedName', '$status', '$videospeed', '$lastvideoposition',
							'$designedby_user', '$countedby_user', '$qcedby_user', '$rating', '$lastupdatetime')"
					);

			if ($result)
			{
				$retval = TRUE;

				$layoutid = mysqli_insert_id($dbcon);

				$object = new LayoutRow(
						$dbcon,
						$layoutid,
						$testdata,
						$videoid,
						$name,
						$status,
						$videospeed,
						$lastvideoposition,
						$designedby_user,
						$countedby_user,
						$qcedby_user,
						$rating,
						$lastupdatetime
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTROW, __METHOD__, $retval);
			return $retval;
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

			$rows = LayoutRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEO_LAYOUTROW, __METHOD__);

			$rows = NULL;

			$selectFields = "layoutid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add layoutid as it's already included by default.
					if ($field != "layoutid")
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

			DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_layouts $filterString $sortString");

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

						$rows[] = new LayoutRow(
								$dbcon,
								isset($row['layoutid']) ? $row['layoutid'] : NULL,
								isset($row['testdata']) ? $row['testdata'] : NULL,
								isset($row['videoid']) ? $row['videoid'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['status']) ? $row['status'] : NULL,
								isset($row['videospeed']) ? $row['videospeed'] : NULL,
								isset($row['lastvideoposition']) ? $row['lastvideoposition'] : NULL,
								isset($row['designedby_user']) ? $row['designedby_user'] : NULL,
								isset($row['countedby_user']) ? $row['countedby_user'] : NULL,
								isset($row['qcedby_user']) ? $row['qcedby_user'] : NULL,
								isset($row['rating']) ? $row['rating'] : NULL,
								isset($row['lastupdatetime']) ? $row['lastupdatetime'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "layoutid=$this->layoutid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->layoutid != NULL)
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

					DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Updating row with layoutid=$this->layoutid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_layouts
							 SET $setString
							 WHERE layoutid='$this->layoutid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();

						$retval = TRUE;
					}
					else
					{
						$sqlError = mysqli_errno($dbcon);
						DBG_ERR(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Must set layoutid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_layouts SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_layouts $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
