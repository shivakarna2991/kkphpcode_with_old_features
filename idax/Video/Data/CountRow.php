<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class CountRow
	{
		private $dbcon = NULL;

		private $layoutid = NULL;
		private $legindex = NULL;
		private $videoposition = NULL;
		private $videospeed = NULL;
		private $counttype = NULL;
		private $objecttye = NULL;
		private $countedtime = NULL;
		private $countedby_user = NULL;
		private $rejected = NULL;
		private $rejectedtime = NULL;
		private $rejectedby_user = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getLayoutId()
		{
			return $this->layoutid;
		}

		public function setLayoutId($value)
		{
			if ($this->layoutid != $value)
			{
				$this->layoutid = $value;
				$this->fieldUpdated('layoutid', $value);
			}
		}

		public function getLegIndex()
		{
			return $this->legindex;
		}

		public function setLegIndex($value)
		{
			if ($this->legindex != $value)
			{
				$this->legindex = $value;
				$this->fieldUpdated('legindex', $value);
			}
		}

		public function getVideoPosition()
		{
			return $this->videoposition;
		}

		public function setVideoPosition($value)
		{
			if ($this->videoposition != $value)
			{
				$this->videoposition = $value;
				$this->fieldUpdated('videoposition', $value);
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

		public function getCountType()
		{
			return $this->counttype;
		}

		public function setCountType($value)
		{
			if ($this->counttype != $value)
			{
				$this->counttype = $value;
				$this->fieldUpdated('counttype', $value);
			}
		}

		public function getObjectType()
		{
			return $this->objecttye;
		}

		public function setObjectType($value)
		{
			if ($this->objecttype != $value)
			{
				$this->objecttype = $value;
				$this->fieldUpdated('objecttype', $value);
			}
		}

		public function getCountedTime()
		{
			return $this->countedtime;
		}

		public function setCountedTime($value)
		{
			if ($this->countedtime != $value)
			{
				$this->countedtime = $value;
				$this->fieldUpdated('countedtime', $value);
			}
		}

		public function getConfigureddBy_User()
		{
			return $this->countedby_user;
		}

		public function setConfigureddBy_User($value)
		{
			if ($this->countedby_user != $value)
			{
				$this->countedby_user = $value;
				$this->fieldUpdated('countedby_user', $value);
			}
		}

		public function getRejected()
		{
			return $this->rejected;
		}

		public function setRejected($value)
		{
			if ($this->rejected != $value)
			{
				$this->rejected = $value;
				$this->fieldUpdated('rejected', $value);
			}
		}

		public function getRejectedTime()
		{
			return $this->rejectedtime;
		}

		public function setRejectedTime($value)
		{
			if ($this->rejectedtime != $value)
			{
				$this->rejectedtime = $value;
				$this->fieldUpdated('rejectedtime', $value);
			}
		}

		public function getRejecteddBy_User()
		{
			return $this->rejectedby_user;
		}

		public function setRejecteddBy_User($value)
		{
			if ($this->rejectedby_user != $value)
			{
				$this->rejectedby_user = $value;
				$this->fieldUpdated('rejectedby_user', $value);
			}
		}

		public function __construct(
			$dbcon,
			$layoutid,
			$legindex,
			$videoposition,
			$videospeed,
			$counttype,
			$objecttype,
			$countedtime,
			$countedby_user,
			$rejected,
			$rejectedtime,
			$rejectedby_user
			)
		{
			$this->dbcon = $dbcon;

			$this->layoutid = $layoutid;
			$this->legindex = $legindex;
			$this->videoposition = $videoposition;
			$this->videospeed = $videospeed;
			$this->counttype = $counttype;
			$this->objecttype = $objecttype;
			$this->countedtime = $countedtime;
			$this->countedby_user = $countedby_user;
			$this->rejected = $rejected;
			$this->rejectedtime = $rejectedtime;
			$this->rejectedby_user = $rejectedby_user;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$layoutid,
			$legindex,
			$videoposition,
			$videospeed,
			$counttype,
			$objecttype,
			$countedtime,
			$countedby_user,
			$rejected,
			$rejectedtime,
			$rejectedby_user,
			&$object,
			&$sqlError
			)
		{
                           // echo '$rejectedtime'.$rejectedtime;exit;
                    $rejected = (isset($rejected) && $rejected!='') ? $rejected = $rejected : 0;
                    $rejectedtime = (isset($rejectedtime) && $rejectedtime!='') ? $rejectedtime = $rejectedtime : '0000-00-00 00:00:00';
                    $rejectedby_user = (isset($rejectedby_user) && $rejectedby_user!='') ? $rejectedby_user = $rejectedby_user : 0;
			DBG_ENTER(DBGZ_VIDEO_COUNTROW, __METHOD__);

			DBG_INFO(
					DBGZ_VIDEO_COUNTROW,
					__METHOD__,
					"Inserting row with layoutid=$layoutid, legindex=$legindex, counttype=$counttype, objecttype=$objecttype"
					);

			$retval = FALSE;

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_counts (layoutid, legindex, videoposition, videospeed, counttype, objecttype, countedtime, countedby_user,
							rejected, rejectedtime, rejectedby_user)
					VALUES ('$layoutid', '$legindex', '$videoposition', '$videospeed', '$counttype', '$objecttype', '$countedtime', '$countedby_user',
							'$rejected', '$rejectedtime', '$rejectedby_user')"
					);

			if ($result)
			{
				$retval = TRUE;

				$object = new CountRow(
						$dbcon,
						$layoutid,
						$legindex,
						$videoposition,
						$videospeed,
						$counttype,
						$objecttype,
						$countedtime,
						$countedby_user,
						$rejected,
						$rejectedtime,
						$rejectedby_user
					);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				$sqlError1 = mysqli_error($dbcon);
                                //echo '<pre>';print_r($sqlError1);exit;
				DBG_ERR(DBGZ_VIDEO_COUNTROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error());
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_COUNTROW, __METHOD__, $retval);
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

			$rows = CountRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEO_COUNTROW, __METHOD__);

			$rows = NULL;

			$selectFields = "layoutid, legindex";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add layoutid as it's already included by default.
					if (($field != "layoutid") && ($field != 'legindex'))
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

			DBG_INFO(DBGZ_VIDEO_COUNTROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_counts $filterString $sortString");

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

						$rows[] = new CountRow(
								$dbcon,
								isset($row['layoutid']) ? $row['layoutid'] : NULL,
								isset($row['legindex']) ? $row['legindex'] : NULL,
								isset($row['videoposition']) ? $row['videoposition'] : NULL,
								isset($row['videospeed']) ? $row['videospeed'] : NULL,
								isset($row['counttype']) ? $row['counttype'] : NULL,
								isset($row['objecttype']) ? $row['objecttype'] : NULL,
								isset($row['countedtime']) ? $row['countedtime'] : NULL,
								isset($row['countedby_user']) ? $row['countedby_user'] : NULL,
								isset($row['rejected']) ? $row['rejected'] : NULL,
								isset($row['rejectedtime']) ? $row['rejectedtime'] : NULL,
								isset($row['rejectedby_user']) ? $row['rejectedby_user'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_COUNTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_VIDEO_COUNTROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_COUNTROW, __METHOD__, "layoutid=$this->layoutid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if (($this->layoutid != NULL) && ($legindex != NULL))
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_VIDEO_COUNTROW, __METHOD__, "Updating row with layoutid=$this->layoutid, legindex=$this->legindex. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_counts
							 SET $setString
							 WHERE layoutid='$this->layoutid' AND legindex=$this->legindex"
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
					DBG_WARN(DBGZ_VIDEO_COUNTROW, __METHOD__, "Must set layoutid and legindex properties before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEO_COUNTROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_COUNTROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_COUNTROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_COUNTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_counts SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_COUNTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_COUNTROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_COUNTROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_COUNTROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_counts $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_COUNTROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_COUNTROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
