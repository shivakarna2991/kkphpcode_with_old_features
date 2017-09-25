<?php

	namespace Core\Common\Data;

	require_once 'core/core.php';

	class IssueRow
	{
		private $dbcon = NULL;

		private $issueid = NULL;
		private $type = NULL;
		private $accountid = NULL;
		private $opendate = NULL;
		private $app = NULL;
		private $title = NULL;
		private $description = NULL;
		private $reprosteps = NULL;
		private $state = NULL;
		private $lastupdated = NULL;
		private $lastupdatedby = NULL;
		private $comments = NULL;
		private $priority = NULL;
		private $assignedto = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getIssueId()
		{
			return $this->issueid;
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

		public function getAccountId()
		{
			return $this->accountid;
		}

		public function setAccountId($value)
		{
			if ($this->accountid != $value)
			{
				$this->accountid = $value;
				$this->fieldUpdated('accountid', $value);
			}
		}

		public function getOpenDate()
		{
			return $this->opendate;
		}

		public function setOpenDate($value)
		{
			if ($this->opendate != $value)
			{
				$this->opendate = $value;
				$this->fieldUpdated('opendate', $value);
			}
		}

		public function getApp()
		{
			return $this->app;
		}

		public function setApp($value)
		{
			if ($this->app != $value)
			{
				$this->app = $value;
				$this->fieldUpdated('app', $value);
			}
		}

		public function getTitle()
		{
			return $this->title;
		}

		public function setTitle($value)
		{
			if ($this->title != $value)
			{
				$this->title = $value;
				$this->fieldUpdated('title', $value);
			}
		}

		public function getDescription()
		{
			return $this->description;
		}

		public function setDescription($value)
		{
			if ($this->description != $value)
			{
				$this->description = $value;
				$this->fieldUpdated('description', $value);
			}
		}

		public function getReproSteps()
		{
			return $this->reprosteps;
		}

		public function setReproSteps($value)
		{
			if ($this->reprosteps != $value)
			{
				$this->reprosteps = $value;
				$this->fieldUpdated('reprosteps', $value);
			}
		}

		public function getState()
		{
			return $this->state;
		}

		public function setState()
		{
			if ($this->state != $value)
			{
				$this->state = $value;
				$this->fieldUpdated('state', $value);
			}
		}

		public function getLastUpdated()
		{
			return $this->lastupdated;
		}

		public function setLastUpdated()
		{
			if ($this->lastupdated != $value)
			{
				$this->lastupdated = $value;
				$this->fieldUpdated('lastupdated', $value);
			}
		}

		public function getLastUpdatedBy()
		{
			return $this->lastupdatedby;
		}

		public function setLastUpdatedBy()
		{
			if ($this->lastupdatedby != $value)
			{
				$this->lastupdatedby = $value;
				$this->fieldUpdated('lastupdatedby', $value);
			}
		}

		public function getComments()
		{
			return $this->comments;
		}

		public function setComments()
		{
			if ($this->comments != $value)
			{
				$this->comments = $value;
				$this->fieldUpdated('comments', $value);
			}
		}

		public function getPriority()
		{
			return $this->priority;
		}

		public function setPriority()
		{
			if ($this->priority != $value)
			{
				$this->priority = $value;
				$this->fieldUpdated('priority', $value);
			}
		}

		public function getAssignedTo()
		{
			return $this->assignedto;
		}

		public function setAssignedTo()
		{
			if ($this->assignedto != $value)
			{
				$this->assignedto = $value;
				$this->fieldUpdated('assignedto', $value);
			}
		}

		public function __construct(
			$dbcon,
			$issueid,
			$type,
			$accountid,
			$opendate,
			$app,
			$title,
			$description,
			$reprosteps,
			$state,
			$lastupdated,
			$lastupdatedby,
			$comments,
			$priority,
			$assignedto
			)
		{
			$this->dbcon = $dbcon;

			$this->issueid = $issueid;
			$this->type = $type;
			$this->accountid = $accountid;
			$this->opendate = $opendate;
			$this->app = $app;
			$this->title = $title;
			$this->description = $description;
			$this->reprosteps = $reprosteps;
			$this->state = $state;
			$this->lastupdated = $lastupdated;
			$this->lastupdatedby = $lastupdatedby;
			$this->comments = $comments;
			$this->priority = $priority;
			$this->assignedto = $assignedto;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$type,
			$accountid,
			$opendate,
			$app,
			$title,
			$description,
			$reprosteps,
			$state,
			$lastupdated,
			$lastupdatedby,
			$comments,
			$priority,
			$assignedto,
			&$issueObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ISSUEROW, __METHOD__);

			DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "Inserting row with type=$type, accoountid=$accountid, title=$title");

			$escapedApp = mysqli_real_escape_string($dbcon, $app);
			$escapedTitle = mysqli_real_escape_string($dbcon, $title);
			$escapedDescription = mysqli_real_escape_string($dbcon, $description);
			$escapedReproSteps = mysqli_real_escape_string($dbcon, $reprosteps);
			$escapedComments = mysqli_real_escape_string($dbcon, $comments);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_issues (type, accountid, opendate, app, title, description, reprosteps,
							state, lastupdated, lastupdatedby, comments, priority, assignedto)
					VALUES ('$type', '$accountid', '$opendate', '$escapedApp', '$escapedTitle', '$escapedDescription', '$escapedReproSteps',
							'$state', '$lastupdated', '$lastupdatedby', '$escapedComments', '$priority', '$assignedto')"
					);

			if ($result)
			{
				$issueid = mysqli_insert_id($dbcon);

				$issueObject = new IssueRow(
						$dbcon,
						$issueid,
						$type,
						$accountid,
						$opendate,
						$app,
						$title,
						$description,
						$reprosteps,
						$state,
						$lastupdated,
						$lastupdatedby,
						$comments,
						$priority,
						$assignedto
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEROW, __METHOD__, "Failed to insert row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEROW, __METHOD__, $result);
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

			$rows = IssueRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_ISSUEROW, __METHOD__);

			$rows = NULL;

			$selectFields = "issueid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add issueid as it's already included by default.
					if ($field != "issueid")
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

			DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_issues $filterString $sortString");

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

						$rows[] = new IssueRow(
								$dbcon,
								isset($row['issueid']) ? $row['issueid'] : NULL,
								isset($row['type']) ? $row['type'] : NULL,
								isset($row['accountid']) ? $row['accountid'] : NULL,
								isset($row['opendate']) ? $row['opendate'] : NULL,
								isset($row['app']) ? $row['app'] : NULL,
								isset($row['title']) ? $row['title'] : NULL,
								isset($row['description']) ? $row['description'] : NULL,
								isset($row['reprosteps']) ? $row['reprosteps'] : NULL,
								isset($row['state']) ? $row['state'] : NULL,
								isset($row['lastupdated']) ? $row['lastupdated'] : NULL,
								isset($row['lastupdatedby']) ? $row['lastupdatedby'] : NULL,
								isset($row['comments']) ? $row['comments'] : NULL,
								isset($row['priority']) ? $row['priority'] : NULL,
								isset($row['assignedto']) ? $row['assignedto'] : NULL
								);
					}
				}
			}
			else
			{
				$errno = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ISSUEROW, __METHOD__, "Select failed with error=$errno");
			}

			DBG_RETURN(DBGZ_ISSUEROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields()
		{
			DBG_ENTER(DBGZ_ISSUEROW, __METHOD__, "issueidid=$this->issueid");

			$result = TRUE;

			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->issueid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "Updating row with issueid=$this->issueid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_issues
							 SET $setString
							 WHERE issueid='$this->issueid'"
							);


					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_ISSUEROW, __METHOD__, "Select failed with error=$sqlError");
					}
				}
				else
				{
					DBG_WARN(DBGZ_ISSUEROW, __METHOD__, "Must set issueid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "No fields were changed, nothing to update.");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$con_user,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_ISSUEROW, __METHOD__);

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

			DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($con_user, "UPDATE core_issues SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($con_user);
				DBG_ERR(DBGZ_ISSUEROW, __METHOD__, "Failed to update row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$con_user,
			$filters
			)
		{
			DBG_ENTER(DBGZ_ISSUEROW, __METHOD__);

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

			$setString = trim($setString, ', ');

			DBG_INFO(DBGZ_ISSUEROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($con_user, "DELETE FROM core_issues $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($con_user);
				DBG_ERR(DBGZ_ISSUEROW, __METHOD__, "Failed to delete rows with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ISSUEROW, __METHOD__, $result);
			return $result;
		}
	}
?>
