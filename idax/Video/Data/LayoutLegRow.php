<?php

	namespace Idax\Video\Data;

	require_once 'idax/idax.php';

	class LayoutLegRow
	{
		private $dbcon = NULL;

		private $layoutid = NULL;
		private $legindex = NULL;
		private $type = NULL;
		private $direction = NULL;
		private $leg_pos = NULL;
		private $button1_pos = NULL;
		private $button2_pos = NULL;
		private $button3_pos = NULL;
		private $button4_pos = NULL;
		private $button5_pos = NULL;
		private $button1_def = NULL;
		private $button2_def = NULL;
		private $button3_def = NULL;
		private $button4_def = NULL;
		private $button5_def = NULL;

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

		public function getLeg_Pos()
		{
			return $this->leg_pos;
		}

		public function setLeg_Pos($value)
		{
			if ($this->leg_pos != $value)
			{
				$this->leg_pos = $value;
				$this->fieldUpdated('leg_pos', $value);
			}
		}

		public function getButton1_Pos()
		{
			return $this->button1_pos;
		}

		public function setButton1_Pos($value)
		{
			if ($this->button1_pos != $value)
			{
				$this->button1_pos = $value;
				$this->fieldUpdated('button1_pos', $value);
			}
		}

		public function getButton2_Pos()
		{
			return $this->button2_pos;
		}

		public function setButton2_Pos($value)
		{
			if ($this->button2_pos != $value)
			{
				$this->button2_pos = $value;
				$this->fieldUpdated('button2_pos', $value);
			}
		}

		public function getButton3_Pos()
		{
			return $this->button3_pos;
		}

		public function setButton3_Pos($value)
		{
			if ($this->button3_pos != $value)
			{
				$this->button3_pos = $value;
				$this->fieldUpdated('button3_pos', $value);
			}
		}

		public function getButton4_Pos()
		{
			return $this->button4_pos;
		}

		public function setButton4_Pos($value)
		{
			if ($this->button4_pos != $value)
			{
				$this->button4_pos = $value;
				$this->fieldUpdated('button4_pos', $value);
			}
		}

		public function getButton5_Pos()
		{
			return $this->button5_pos;
		}

		public function setButton5_Pos($value)
		{
			if ($this->button5_pos != $value)
			{
				$this->button5_pos = $value;
				$this->fieldUpdated('button5_pos', $value);
			}
		}

		public function getButton1_Def()
		{
			return $this->button1_def;
		}

		public function setButton1_Def($value)
		{
			if ($this->button1_def != $value)
			{
				$this->button1_def = $value;
				$this->fieldUpdated('button1_def', $value);
			}
		}

		public function getButton2_Def()
		{
			return $this->button2_def;
		}

		public function setButton2_Def($value)
		{
			if ($this->button2_def != $value)
			{
				$this->button2_def = $value;
				$this->fieldUpdated('button2_def', $value);
			}
		}

		public function getButton3_Def()
		{
			return $this->button3_def;
		}

		public function setButton3_Def($value)
		{
			if ($this->button3_def != $value)
			{
				$this->button3_def = $value;
				$this->fieldUpdated('button3_def', $value);
			}
		}

		public function getButton4_Def()
		{
			return $this->button4_def;
		}

		public function setButton4_Def($value)
		{
			if ($this->button4_def != $value)
			{
				$this->button4_def = $value;
				$this->fieldUpdated('button4_def', $value);
			}
		}

		public function getButton5_Def()
		{
			return $this->button5_pos;
		}

		public function setButton5_Def($value)
		{
			if ($this->button5_def != $value)
			{
				$this->button5_def = $value;
				$this->fieldUpdated('button5_def', $value);
			}
		}

		public function __construct(
			$dbcon,
			$layoutid,
			$legindex,
			$type,
			$direction,
			$leg_pos,
			$button1_pos,
			$button2_pos,
			$button3_pos,
			$button4_pos,
			$button5_pos,
			$button1_def,
			$button2_def,
			$button3_def,
			$button4_def,
			$button5_def
			)
		{
			$this->dbcon = $dbcon;

			$this->layoutid = $layoutid;
			$this->legindex = $legindex;
			$this->type = $type;
			$this->direction = $direction;
			$this->leg_pos = $leg_pos;
			$this->button1_pos = $button1_pos;
			$this->button2_pos = $button2_pos;
			$this->button3_pos = $button3_pos;
			$this->button4_pos = $button4_pos;
			$this->button5_pos = $button5_pos;
			$this->button1_def = $button1_def;
			$this->button2_def = $button2_def;
			$this->button3_def = $button3_def;
			$this->button4_def = $button4_def;
			$this->button5_def = $button5_def;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$layoutid,
			$legindex,
			$type,
			$direction,
			$leg_pos,
			$button1_pos,
			$button2_pos,
			$button3_pos,
			$button4_pos,
			$button5_pos,
			$button1_def,
			$button2_def,
			$button3_def,
			$button4_def,
			$button5_def,
			&$object,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__);

			DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Inserting row with layoutid=$layoutid, legindex=$legindex");

			$escapedButton1_Def = mysqli_real_escape_string($dbcon, $button1_def);
			$escapedButton2_Def = mysqli_real_escape_string($dbcon, $button2_def);
			$escapedButton3_Def = mysqli_real_escape_string($dbcon, $button3_def);
			$escapedButton4_Def = mysqli_real_escape_string($dbcon, $button4_def);
			$escapedButton5_Def = mysqli_real_escape_string($dbcon, $button5_def);

			DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "str=\"INSERT INTO idax_video_layoutlegs (layoutid, legindex, type, direction, leg_pos
					button1_pos, button2_pos, button3_pos, button4_pos, button5_pos,
					button1_def, button2_def, button3_def, button4_def, button5_def)
					VALUES ('$layoutid', '$legindex', '$type', '$direction', '$leg_pos',
					'$button1_pos', '$button2_pos', '$button3_pos', '$button4_pos', '$button5_pos',
					'$escapedButton1_Def', '$escapedButton2_Def', '$escapedButton3_Def', '$escapedButton4_Def', '$escapedButton5_Def')\"");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_video_layoutlegs (layoutid, legindex, type, direction, leg_pos,
					button1_pos, button2_pos, button3_pos, button4_pos, button5_pos,
					button1_def, button2_def, button3_def, button4_def, button5_def)
					VALUES ('$layoutid', '$legindex', '$type', '$direction', '$leg_pos',
					'$button1_pos', '$button2_pos', '$button3_pos', '$button4_pos', '$button5_pos',
					'$escapedButton1_Def', '$escapedButton2_Def', '$escapedButton3_Def', '$escapedButton4_Def', '$escapedButton5_Def')"
					);

			if ($result)
			{
				$object = new LayoutLegRow(
						$dbcon,
						$layoutid,
						$legindex,
						$type,
						$direction,
						$leg_pos,
						$button1_pos,
						$button2_pos,
						$button3_pos,
						$button4_pos,
						$button5_pos,
						$button1_def,
						$button2_def,
						$button3_def,
						$button4_def,
						$button5_def
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, $result);
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

			$rows = LayoutLegRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_video_layoutlegs $filterString $sortString");

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

						$rows[] = new LayoutLegRow(
								$dbcon,
								isset($row['layoutid']) ? $row['layoutid'] : NULL,
								isset($row['legindex']) ? $row['legindex'] : NULL,
								isset($row['type']) ? $row['type'] : NULL,
								isset($row['direction']) ? $row['direction'] : NULL,
								isset($row['leg_pos']) ? $row['leg_pos'] : NULL,
								isset($row['button1_pos']) ? $row['button1_pos'] : NULL,
								isset($row['button2_pos']) ? $row['button2_pos'] : NULL,
								isset($row['button3_pos']) ? $row['button3_pos'] : NULL,
								isset($row['button4_pos']) ? $row['button4_pos'] : NULL,
								isset($row['button5_pos']) ? $row['button5_pos'] : NULL,
								isset($row['button1_def']) ? $row['button1_pos'] : NULL,
								isset($row['button2_def']) ? $row['button2_pos'] : NULL,
								isset($row['button3_def']) ? $row['button3_pos'] : NULL,
								isset($row['button4_def']) ? $row['button4_pos'] : NULL,
								isset($row['button5_def']) ? $row['button5_pos'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "layoutid=$this->layoutid");

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

					DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Updating row with layoutid=$this->layoutid, legindex=$this->legindex. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_video_layoutlegs
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
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Must set layoutid and legindex properties before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_video_layoutlegs SET $setString $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, $retval);
			return $retval;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__);

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

			DBG_INFO(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_video_layoutlegs $filterString");

			if ($result)
			{
				$retval = TRUE;
			}
			else
			{
				$retval = FALSE;

				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTLEGROW, __METHOD__, $retval);
			return $retval;
		}
	}
?>
