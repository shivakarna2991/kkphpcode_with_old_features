<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class LayoutNotesRow
	{
		private $dbcon = NULL;

		private $layoutid = NULL;
		private $designer_notes = NULL;
		private $counter_notes = NULL;
		private $qc_notes = NULL;

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

		public function getDesignerNotes()
		{
			return $this->designer_notes;
		}

		public function setDesignerNotes($value)
		{
			if ($this->designer_notes != $value)
			{
				$this->designer_notes = $value;
				$this->fieldUpdated('designer_notes', $value);
			}
		}

		public function getCounterNotes()
		{
			return $this->counter_notes;
		}

		public function setCounterNotes($value)
		{
			if ($this->counter_notes != $value)
			{
				$this->counter_notes = $value;
				$this->fieldUpdated('counter_notes', $value);
			}
		}

		public function getQCNotes()
		{
			return $this->qc_notes;
		}

		public function setQCNotes($value)
		{
			if ($this->qc_notes != $value)
			{
				$this->qc_notes = $value;
				$this->fieldUpdated('qc_notes', $value);
			}
		}

		public function __construct(
			$dbcon,
			$layoutid,
			$designer_notes,
			$counter_notes,
			$qc_notes
			)
		{
			$this->dbcon = $dbcon;

			$this->layoutid = $layoutid;
			$this->designer_notes = $designer_notes;
			$this->counter_notes = $counter_notes;
			$this->qc_notes = $qc_notes;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$layoutid,
			$designer_notes,
			$counter_notes,
			$qc_notes,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__);

			DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Inserting row with layoutid=$layoutid");

			$retval = FALSE;

			$escapdedDesignerNotes = mysqli_real_escape_string($dbcon, $designer_notes);
			$escapdedCounterNotes = mysqli_real_escape_string($dbcon, $counter_notes);
			$escapdedQCNotes = mysqli_real_escape_string($dbcon, $qc_notes);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_layoutnotes (layoutid, designer_notes, counter_notes, qc_notes)
					VALUES ('$layoutid', '$escapdedDesignerNotes', '$escapdedCounterNotes',  '$escapdedQCNotes')"
					);

			if ($result)
			{
				$retval = TRUE;

				$object = new LayoutNotesRow(
						$dbcon,
						$layoutid,
						$designer_notes,
						$counter_notes,
						$qc_notes
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error());
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, $retval);
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

			$rows = LayoutNotesRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_layoutnotes $filterString $sortString");

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

						$rows[] = new LayoutNotesRow(
								$dbcon,
								isset($row['layoutid']) ? $row['layoutid'] : NULL,
								isset($row['designer_notes']) ? $row['designer_notes'] : NULL,
								isset($row['counter_notes']) ? $row['counter_notes'] : NULL,
								isset($row['qc_notes']) ? $row['qc_notes'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error());
			}

			DBG_RETURN(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "layoutid=$this->layoutid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->layoutid != NULL)
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

					DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Updating row with layoutid=$this->layoutid, Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_layoutnotes
							 SET $setString
							 WHERE layoutid='$this->layoutid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($dbcon);
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error());
					}
				}
				else
				{
					DBG_WARN(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Must set layoutid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_layoutnotes SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error());
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_layoutnotes $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error());
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTNOTEROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
