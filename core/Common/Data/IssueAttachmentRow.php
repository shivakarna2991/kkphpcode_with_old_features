<?php

	namespace Core\Common\Data;

	require_once 'core/core.php';

	class IssueAttachmentRow
	{
		private $dbcon = NULL;

		private $attachmentid = NULL;
		private $issueid = NULL;
		private $lastupdated = NULL;
		private $filename = NULL;
		private $bucketfilename = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getAttachmentId()
		{
			return $this->attachmentid;
		}

		public function getIssueId()
		{
			return $this->issueid;
		}

		public function setIssueId($value)
		{
			if ($this->issueid != $value)
			{
				$this->issueid = $value;
				$this->fieldUpdated('issueid', $value);
			}
		}

		public function getLastUpdated()
		{
			return $this->lastupdated;
		}

		public function setLastUpdated($value)
		{
			if ($this->lastupdated != $value)
			{
				$this->lastupdated = $value;
				$this->fieldUpdated('lastupdated', $value);
			}
		}

		public function getFilename()
		{
			return $this->filename;
		}

		public function setFilename($value)
		{
			if ($this->filename != $value)
			{
				$this->filename = $value;
				$this->fieldUpdated('filename', $value);
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

		public function __construct(
			$dbcon,
			$attachmentid,
			$issueid,
			$lastupdated,
			$filename,
			$bucketfilename
			)
		{
			$this->dbcon = $dbcon;

			$this->attachmentid = $attachmentid;
			$this->issueid = $issueid;
			$this->lastupdated = $lastupdated;
			$this->filename = $filename;
			$this->bucketfilename = $bucketfilename;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$issueid,
			$lastupdated,
			$filename,
			$bucketfilename,
			&$attachmentObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ISSUEATTACHMENTROW, __METHOD__);

			DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Inserting row with issueid=$issueid, filename=$filename");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_issueattachments  (issueid, lastupdated, filename, bucketfilename)
					VALUES ('$issueid', '$lastupdated', '$filename', '$bucketfilename')"
					);

			if ($result)
			{
				$attachmentid = mysqli_insert_id($dbcon);

				$attachmentObject = new IssueAttachmentRow(
						$dbcon,
						$attachmentid,
						$issueid,
						$lastupdated,
						$filename,
						$bucketfilename
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Failed to insert row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEATTACHMENTROW, __METHOD__, $result);
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

			$rows = IssueAttachmentRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_ISSUEATTACHMENTROW, __METHOD__);

			$rows = NULL;

			$selectFields = "attachmentid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add accountid as it's already included by default.
					if ($field != "attachmentid")
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

			DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_issueattachments $filterString $sortString");

			if ($result)
			{
				$numRows = mysqli_num_rows($result);

				for ($i=0; $i<$numRows; $i++)
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
						$row = mysqli_fetch_assoc($result);

						$rows[] = new IssueAttachmentRow(
								$dbcon,
								isset($row['attachmentid']) ? $row['attachmentid'] : NULL,
								isset($row['issueid']) ? $row['issueid'] : NULL,
								isset($row['lastupdated']) ? $row['lastupdated'] : NULL,
								isset($row['filename']) ? $row['filename'] : NULL,
								isset($row['bucketfilename']) ? $row['bucketfilename'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "found ".count($rows)." issue rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "attachmentid=$this->attachmentid");

			$numFieldsChanged = count($this->changedFields);

			$result = TRUE;

			if ($numFieldsChanged > 0)
			{
				if ($this->attachmentid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Updating row with attachmentid=$this->attachmentid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_issueattachments
							 SET $setString
							 WHERE attachmentid='$this->attachmentid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Must set attachmentid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "No fields were changed, nothing to update.");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEATTACHMENTROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ISSUEATTACHMENTROW, __METHOD__);

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

			DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE core_issueattachments SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEATTACHMENTROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ISSUEATTACHMENTROW, __METHOD__);

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

			DBG_INFO(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM core_issueattachments $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEATTACHMENTROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEATTACHMENTROW, __METHOD__, $result);
			return $result;
		}
	}
?>
