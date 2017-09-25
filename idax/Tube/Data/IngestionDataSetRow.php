<?php

	namespace Idax\Tube\Data;

	require_once '/home/idax/idax.php';

	class IngestionDataSetRow
	{
		private $dbcon = NULL;

		private $datasetid = NULL;
		private $ingestid = NULL;
		private $jobsiteid = NULL;
		private $ds = NULL;
		private $name = NULL;
		private $attribute = NULL;
		private $direction = NULL;
		private $surveybegintime = NULL;
		private $surveyendtime = NULL;
		private $zone = NULL;
		private $file = NULL;
		private $identifier = NULL;
		private $algorithm = NULL;
		private $datatype = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getDataSetId()
		{
			return $this->datasetid;
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

		public function getDS()
		{
			return $this->ds;
		}

		public function setDS($value)
		{
			if ($this->ds != $value)
			{
				$this->ds = $value;
				$this->fieldUpdated('ds', $value);
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

		public function getAttribute()
		{
			return $this->attribute;
		}

		public function setAttribute($value)
		{
			if ($this->attribute != $value)
			{
				$this->attribute = $value;
				$this->fieldUpdated('attribute', $value);
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

		public function getSurveyBeginTime()
		{
			return $this->surveybegintime;
		}

		public function setSurveyBeginTime($value)
		{
			if ($this->surveybegintime != $value)
			{
				$this->surveybegintime = $value;
				$this->fieldUpdated('surveybegintime', $value);
			}
		}

		public function getSurveyBeginEnd()
		{
			return $this->surveyendtime;
		}

		public function setSurveyBeginEnd($value)
		{
			if ($this->surveyendtime != $value)
			{
				$this->surveyendtime = $value;
				$this->fieldUpdated('surveyendtime', $value);
			}
		}

		public function getZone()
		{
			return $this->zone;
		}

		public function setZone($value)
		{
			if ($this->zone != $value)
			{
				$this->zone = $value;
				$this->fieldUpdated('zone', $value);
			}
		}

		public function getFile()
		{
			return $this->file;
		}

		public function setFile($value)
		{
			if ($this->file != $value)
			{
				$this->file = $value;
				$this->fieldUpdated('file', $value);
			}
		}

		public function getIdentifier()
		{
			return $this->identifier;
		}

		public function setIdentifier($value)
		{
			if ($this->identifier != $value)
			{
				$this->identifier = $value;
				$this->fieldUpdated('identifier', $value);
			}
		}

		public function getAlgorithm()
		{
			return $this->algorithm;
		}

		public function setAlgorithm($value)
		{
			if ($this->algorithm != $value)
			{
				$this->algorithm = $value;
				$this->fieldUpdated('algorithm', $value);
			}
		}

		public function getDataType()
		{
			return $this->datatype;
		}

		public function setDataType($value)
		{
			if ($this->datatype != $value)
			{
				$this->datatype = $value;
				$this->fieldUpdated('datatype', $value);
			}
		}

		public function __construct(
			$dbcon,
			$datasetid,
			$ingestionid,
			$jobsiteid,
			$ds,
			$name,
			$attribute,
			$direction,
			$surveybegintime,
			$surveyendtime,
			$zone,
			$file,
			$identifier,
			$algorithm,
			$datatype
			)
		{
			$this->dbcon = $dbcon;

			$this->id = $datasetid;
			$this->jobsiteid = $ingestionid;
			$this->ingestionid = $jobsiteid;
			$this->ds = $ds;
			$this->name = $name;
			$this->attribute = $attribute;
			$this->direction = $direction;
			$this->surveybegintime = $surveybegintime;
			$this->surveyendtime = $surveyendtime;
			$this->zone = $zone;
			$this->file = $file;
			$this->identifier = $identifier;
			$this->algorithm = $algorithm;
			$this->datatype = $datatype;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$ingestionid,
			$jobsiteid,
			$ds,
			$name,
			$attribute,
			$direction,
			$surveybegintime,
			$surveyendtime,
			$zone,
			$file,
			$identifier,
			$algorithm,
			$datatype,
			&$ingestiondatasetObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__);

			DBG_INFO(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "Inserting row with ingestionid=$ingestionid, jobsiteid=$jobsiteid");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tube_ingestiondatasets (ingestionid, jobsiteid, ds, name, attribute, direction,
							surveybegintime, surveyendtime, zone, file, identifier, algorithm, datatype)
					VALUES ('$ingestionid', '$jobsiteid', '$ds', '$name', '$attribute', '$direction',
							'$surveybegintime', '$surveyendtime', '$zone', '$file', '$identifier', '$algorithm', '$datatype')"
					);

			if ($result)
			{
				$datasetid = mysqli_insert_id($dbcon);

				$ingestiondatasetObject = new IngestionDataSetRow(
						$dbcon,
						$datasetid,
						$ingestionid,
						$jobsiteid,
						$ds,
						$name,
						$attribute,
						$direction,
						$surveybegintime,
						$surveyendtime,
						$zone,
						$file,
						$identifier,
						$algorithm,
						$datatype
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, $result);
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

			$rows = IngestionDataSetRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__);

			$rows = NULL;

			$selectFields = "id";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add id as it's already included by default.
					if ($field != "id")
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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tube_ingestiondatasets $filterString $sortString");

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

						$rows[] = new IngestionDataSetRow(
								$dbcon,
								isset($row['datasetid']) ? $row['datasetid'] : NULL,
								isset($row['ingestionid']) ? $row['ingestionid'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['ds']) ? $row['ds'] : NULL,
								isset($row['name']) ? $row['name'] : NULL,
								isset($row['attribute']) ? $row['attribute'] : NULL,
								isset($row['direction']) ? $row['direction'] : NULL,
								isset($row['surveybegintime']) ? $row['surveybegintime'] : NULL,
								isset($row['surveyendtime']) ? $row['surveyendtime'] : NULL,
								isset($row['zone']) ? $row['zone'] : NULL,
								isset($row['file']) ? $row['file'] : NULL,
								isset($row['identifier']) ? $row['identifier'] : NULL,
								isset($row['algorithm']) ? $row['algorithm'] : NULL,
								isset($row['datatype']) ? $row['datatype'] : NULL
								);
						}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tube_ingestiondatasets SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tube_ingestiondatasets $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATASETROW, __METHOD__, $result);
			return $result;
		}
	}
?>
