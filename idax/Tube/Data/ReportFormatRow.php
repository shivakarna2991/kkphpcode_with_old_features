<?php

	namespace Idax\Tube\Data;

	require_once '/home/idax/idax.php';

	class ReportFormatRow
	{
		private $dbcon = NULL;

		private $name = NULL;
		private $displayorder = NULL;
		private $fields = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getName()
		{
			return $this->name;
		}

		public function getDisplayOrder()
		{
			return $this->displayorder;
		}

		public function setDisplayOrder($value)
		{
			if ($this->displayorder != $value)
			{
				$this->displayorder = $value;
				$this->fieldUpdated('displayorder', $value);
			}
		}

		public function getFields()
		{
			return $this->fields;
		}

		public function setFields($value)
		{
			if ($this->fields != $value)
			{
				$this->fields = $value;
				$this->fieldUpdated('fields', $value);
			}
		}
		public function __construct(
			$dbcon,
			$name,
			$displayorder,
			$fields
			)
		{
			$this->dbcon = $dbcon;

			$this->name = $name;
			$this->displayorder = $displayorder;
			$this->fields = $fields;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$name,
			$displayorder,
			$fields,
			&$reportFormatsRow,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATROW, __METHOD__);

			DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Inserting row with name=$name, displayorder=$displayorder, fields=$fields");

			$escapedName = mysqli_real_escape_string($dbcon, $name);
			$escapedFields = mysqli_real_escape_string($dbcon, $fields);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tube_reportformats (name, displayorder, fields)
					VALUES ('$escapedName', '$displayorder', '$escapedFields')"
					);

			if ($result)
			{
				$reportFormatsRow = new ReportFormatRow(
						$dbcon,
						$name,
						$displayorder,
						$fields
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, $result);
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

			$rows = ReportFormatRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATROW, __METHOD__);

			$rows = NULL;

			$selectFields = "name";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add reportid as it's already included by default.
					if ($field != "name")
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

			DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tube_reportformats $filterString $sortString");

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

						$rows[] = new ReportFormatRow(
								$dbcon,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['displayorder']) ? $row['displayorder'] : NULL,
								isset($row['fields']) ? $row['fields'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "reportid=$this->reportid");

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

					DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Updating row with reportid=$this->reportid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE idax_tube_reportformats
							 SET $setString
							 WHERE name='$this->name'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Must set reportid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tube_reportformats SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tube_reportformats $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATROW, __METHOD__, $result);
			return $result;
		}
	}
?>
