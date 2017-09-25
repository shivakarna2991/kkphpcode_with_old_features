<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class IngestionPhaseRow
	{
		private $dbcon = NULL;

		private $id = NULL;
		private $videoid = NULL;
		private $phase = NULL;
		private $stardtime = NULL;
		private $endtime = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldphase, $fieldValue)
		{
			$this->changedFields[$fieldphase] = $fieldValue;
		}

		public function getId()
		{
			return $this->id;
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

		public function getPhase()
		{
			return $this->phase;
		}

		public function setPhase($value)
		{
			if ($this->phase != $value)
			{
				$this->phase = $value;
				$this->fieldUpdated('phase', $value);
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

		public function __construct(
			$dbcon,
			$id,
			$videoid,
			$phase,
			$starttime,
			$endtime
			)
		{
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "videoid=$videoid");

			$this->dbcon = $dbcon;

			$this->id = $id;
			$this->videoid = $videoid;
			$this->phase = $phase;
			$this->starttime = $starttime;
			$this->endtime = $endtime;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$videoid,
			$phase,
			$starttime,
			$endtime,
			&$object,
			&$sqlError
			)
		{
                    $endtime = isset($endtime) ? $endtime : '0000-00-00 00:00:00';
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__);

			DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Inserting row with videoid=$videoid, phase=$phase");

			$retval = FALSE;

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_ingestionphases (videoid, phase, starttime, endtime)
					VALUES ('$videoid', '$phase', '$starttime', '$endtime')"
					);

			if ($result)
			{
				$retval = TRUE;

				$id = mysqli_insert_id($dbcon);

				$object = new IngestionPhaseRow(
						$dbcon,
						$id,
						$videoid,
						$phase,
						$starttime,
						$endtime
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, $retval);
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

			$rows = IngestionPhaseRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "id";
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

			DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_ingestionphases $filterString $sortString");

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

						$rows[] = new IngestionPhaseRow(
								$dbcon,
								isset($row['id']) ? $row['id'] : NULL,
								isset($row['videoid']) ? $row['videoid'] : NULL,
								isset($row['phase']) ? $row['phase'] : NULL,
								isset($row['starttime']) ? $row['starttime'] : NULL,
								isset($row['endtime']) ? $row['endtime'] : NULL
							);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "videoid=$this->videoid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->id != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldphase = key($this->changedFields);
						$setString = "$setString $fieldphase='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Updating row with videoid=$this->videoid, phase=$this->phase. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_ingestionphases
							 SET $setString
							 WHERE id='$this->id'"
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
					DBG_WARN(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Must set id property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_ingestionphases SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_ingestionphases $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEOINGESTIONPHASEROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
