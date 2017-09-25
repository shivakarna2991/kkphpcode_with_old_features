<?php

	namespace Idax\Tube\Data;

	require_once '/home/idax/idax.php';

	class IngestionDataRow
	{
		private $dbcon = NULL;

		private $id = NULL;
		private $ingestionid = NULL;
		private $jobsiteid = NULL;
		private $ds = NULL;
		private $trignum = NULL;
		private $ht = NULL;
		private $occurred = NULL;
		private $dr = NULL;
		private $speed = NULL;
		private $wb = NULL;
		private $hdwy = NULL;
		private $gap = NULL;
		private $ax = NULL;
		private $gp = NULL;
		private $rho = NULL;
		private $cl = NULL;
		private $nm = NULL;
		private $vehicle = NULL;
		private $coercedsequence = NULL;
		private $other = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getId()
		{
			return $this->$id;
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

		public function getTrigNum()
		{
			return $this->trignum;
		}

		public function setTrigNum($value)
		{
			if ($this->trignum != $value)
			{
				$this->trignum = $value;
				$this->fieldUpdated('trignum', $value);
			}
		}

		public function getHt()
		{
			return $this->ht;
		}

		public function setHt($value)
		{
			if ($this->ht != $value)
			{
				$this->ht = $value;
				$this->fieldUpdated('ht', $value);
			}
		}

		public function getOccurred()
		{
			return $this->occurred;
		}

		public function setOccurred($value)
		{
			if ($this->occurred != $value)
			{
				$this->occurred = $value;
				$this->fieldUpdated('occurred', $value);
			}
		}

		public function getDr()
		{
			return $this->dr;
		}

		public function setDr($value)
		{
			if ($this->dr != $value)
			{
				$this->dr = $value;
				$this->fieldUpdated('dr', $value);
			}
		}

		public function getSpeed()
		{
			return $this->speed;
		}

		public function setSpeed($value)
		{
			if ($this->speed != $value)
			{
				$this->speed = $value;
				$this->fieldUpdated('speed', $value);
			}
		}

		public function getWb()
		{
			return $this->wb;
		}

		public function setWb($value)
		{
			if ($this->wb != $value)
			{
				$this->wb = $value;
				$this->fieldUpdated('wb', $value);
			}
		}

		public function getHdwy()
		{
			return $this->hdwy;
		}

		public function setHdwy($value)
		{
			if ($this->hdwy != $value)
			{
				$this->hdwy = $value;
				$this->fieldUpdated('hdwy', $value);
			}
		}

		public function getGap()
		{
			return $this->gap;
		}

		public function setGap($value)
		{
			if ($this->gap != $value)
			{
				$this->gap = $value;
				$this->fieldUpdated('gap', $value);
			}
		}

		public function getAx()
		{
			return $this->ax;
		}

		public function setAx($value)
		{
			if ($this->ax != $value)
			{
				$this->ax = $value;
				$this->fieldUpdated('ax', $value);
			}
		}

		public function getGp()
		{
			return $this->gp;
		}

		public function setGp($value)
		{
			if ($this->gp != $value)
			{
				$this->gp = $value;
				$this->fieldUpdated('gp', $value);
			}
		}

		public function getRho()
		{
			return $this->rho;
		}

		public function setRho($value)
		{
			if ($this->rho != $value)
			{
				$this->rho = $value;
				$this->fieldUpdated('rho', $value);
			}
		}

		public function getCl()
		{
			return $this->rho;
		}

		public function setCl($value)
		{
			if ($this->cl != $value)
			{
				$this->cl = $value;
				$this->fieldUpdated('cl', $value);
			}
		}

		public function getVm()
		{
			return $this->vm;
		}

		public function setVm($value)
		{
			if ($this->vm != $value)
			{
				$this->vm = $value;
				$this->fieldUpdated('vm', $value);
			}
		}

		public function getVehicle()
		{
			return $this->vehicle;
		}

		public function setVehicle($value)
		{
			if ($this->vehicle != $value)
			{
				$this->vehicle = $value;
				$this->fieldUpdated('vehicle', $value);
			}
		}

		public function getCoercedSequence()
		{
			return $this->coercedsequence;
		}

		public function setCoercedSequence($value)
		{
			if ($this->coercedsequence != $value)
			{
				$this->coercedsequence = $value;
				$this->fieldUpdated('coercedsequence', $value);
			}
		}

		public function getOther()
		{
			return $this->other;
		}

		public function setOther($value)
		{
			if ($this->other != $value)
			{
				$this->other = $value;
				$this->fieldUpdated('other', $value);
			}
		}

		public function __construct(
			$dbcon,
			$id,
			$ingestionid,
			$jobsiteid,
			$ds,
			$trignum,
			$ht,
			$occurred,
			$dr,
			$speed,
			$wb,
			$hdwy,
			$gap,
			$ax,
			$gp,
			$rho,
			$cl,
			$nm,
			$vehicle,
			$coercedsequence,
			$other
			)
		{
			$this->dbcon = $dbcon;

			$this->id = $id;
			$this->ingestionid = $ingestionid;
			$this->jobsiteid = $jobsiteid;
			$this->ds = $ds;
			$this->trignum = $trignum;
			$this->ht = $ht;
			$this->occurred = $occurred;
			$this->dr = $dr;
			$this->speed = $speed;
			$this->wb = $wb;
			$this->hdwy = $hdwy;
			$this->gap = $gap;
			$this->ax = $ax;
			$this->gp = $gp;
			$this->rho = $rho;
			$this->cl = $cl;
			$this->nm = $nm;
			$this->vehicle = $vehicle;
			$this->coercedsequence = $coercedsequence;
			$this->other = $other;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$ingestionid,
			$jobsiteid,
			$ds,
			$trignum,
			$ht,
			$occurred,
			$dr,
			$speed,
			$wb,
			$hdwy,
			$gap,
			$ax,
			$gp,
			$rho,
			$cl,
			$nm,
			$vehicle,
			$coercedsequence,
			$other,
			&$ingestionDataRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__);

			DBG_INFO(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "Inserting row with ingestionid=$ingestionid, jobsiteid=$$jobsiteid");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO idax_tube_ingestiondata (ingestionid, jobsiteid, ds, trignum, ht, occurred,
							dr, speed, wb, hdwy, gap, ax, gp, rho, cl, nm, vehicle, coercedsequence, other)
					VALUES ('$ingestionid', '$jobsiteid', '$ds', '$trignum', '$ht', '$occurred',
							'$dr', '$speed', '$wb', '$hdwy', '$gap', '$ax', '$gp', '$rho', '$cl', '$nm', '$vehicle', '$coercedsequence', '$other')"
					);

			if ($result)
			{
				$id = mysqli_insert_id($dbcon);

				$ingestionrDataRow = new IngestionDataRow(
						$dbcon,
						$id,
						$ingestionid,
						$jobsiteid,
						$ds,
						$trignum,
						$ht,
						$occurred,
						$dr,
						$speed,
						$wb,
						$hdwy,
						$gap,
						$ax,
						$gp,
						$rho,
						$cl,
						$nm,
						$vehicle,
						$coercedsequence,
						$other
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, $result);
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

			$rows = IngestionDataRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__);

			$rows = NULL;

			$selectFields = "id";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add ingestid as it's already included by default.
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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM idax_tube_ingestiondata $filterString $sortString");

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

						$rows[] = new IngestionDataRow(
								$dbcon,
								isset($row['id']) ? $row['id'] : NULL,
								isset($row['ingestionid']) ? $row['ingestionid'] : NULL,
								isset($row['jobsiteid']) ? $row['jobsiteid'] : NULL,
								isset($row['ds']) ? $row['ds'] : NULL,
								isset($row['trignum']) ? $row['trignum'] : NULL,
								isset($row['ht']) ? $row['ht'] : NULL,
								isset($row['occurred']) ? $row['occurred'] : NULL,
								isset($row['dr']) ? $row['dr'] : NULL,
								isset($row['speed']) ? $row['speed'] : NULL,
								isset($row['wb']) ? $row['wb'] : NULL,
								isset($row['hdwy']) ? $row['hdwy'] : NULL,
								isset($row['gap']) ? $row['gap'] : NULL,
								isset($row['ax']) ? $row['ax'] : NULL,
								isset($row['gp']) ? $row['gp'] : NULL,
								isset($row['rho']) ? $row['rho'] : NULL,
								isset($row['cl']) ? $row['cl'] : NULL,
								isset($row['nm']) ? $row['nm'] : NULL,
								isset($row['vehicle']) ? $row['vehicle'] : NULL,
								isset($row['coercedsequence']) ? $row['coercedsequence'] : NULL,
								isset($row['other']) ? $row['other'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__);

			$result = FALSE;

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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE idax_tube_ingestiondata SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters
			)
		{
			DBG_ENTER(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__);

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

			DBG_INFO(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM idax_tube_ingestiondata $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_INGESTIONDATAROW, __METHOD__, $result);
			return $result;
		}
	}
?>
