<?php

	namespace Core\Common\Data;

	require_once 'core/core.php';

	class AccountLinkRow
	{
		private $dbcon = NULL;

		private $accountid = NULL;
		private $email = NULL;
		private $urlkey = NULL;
		private $type = NULL;
		private $creationtime = NULL;
		private $expirationtime = NULL;
		private $usedtime = NULL;
		private $usecount = NULL;
		private $state = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
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

		public function getEmail()
		{
			return $this->email;
		}

		public function setEmail($value)
		{
			if ($this->email != $value)
			{
				$this->email = $value;
				$this->fieldUpdated('email', $value);
			}
		}

		public function getUrlKey()
		{
			return $this->urlkey;
		}

		public function setUrlKey($value)
		{
			if ($this->urlkey != $value)
			{
				$this->urlkey = $value;
				$this->fieldUpdated('urlkey', $value);
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

		public function getCreationTime()
		{
			return $this->creationtime;
		}

		public function setCreationTime($value)
		{
			if ($this->creationtime != $value)
			{
				$this->creationtime = $value;
				$this->fieldUpdated('creationtime', $value);
			}
		}

		public function getExpirationTime()
		{
			return $this->expirationtime;
		}

		public function setExpirationTime($value)
		{
			if ($this->expirationtime != $value)
			{
				$this->expirationtime = $value;
				$this->fieldUpdated('expirationtime', $value);
			}
		}

		public function getUsedTime()
		{
			return $this->usedtime;
		}

		public function setUsedTime($value)
		{
			if ($this->usedtime != $value)
			{
				$this->usedtime = $value;
				$this->fieldUpdated('usedtime', $value);
			}
		}

		public function getUseCount()
		{
			return $this->usecount;
		}

		public function setUseCount($value)
		{
			if ($this->usecount != $value)
			{
				$this->usecount = $value;
				$this->fieldUpdated('usecount', $value);
			}
		}

		public function getState()
		{
			return $this->state;
		}

		public function setState($value)
		{
			if ($this->state != $value)
			{
				$this->state = $value;
				$this->fieldUpdated('state', $value);
			}
		}

		public function __construct(
			$dbcon,
			$accountid,
			$email,
			$urlkey,
			$type,
			$creationtime,
			$expirationtime,
			$usedtime,
			$usecount,
			$state
			)
		{
			$this->dbcon = $dbcon;

			$this->accountid = $accountid;
			$this->email = $email;
			$this->urlkey = $urlkey;
			$this->type = $type;
			$this->creationtime = $creationtime;
			$this->expirationtime = $expirationtime;
			$this->usedtime = $usedtime;
			$this->usecount = $usecount;
			$this->state = $state;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$accountid,
			$email,
			$urlkey,
			$type,
			$creationtime,
			$expirationtime,
			$usedtime,
			$usecount,
			$state,
			&$accountLinkObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKROW, __METHOD__);

			DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "Inserting row with accountid=$accountid, email=$email, urlkey=$urlkey, type=$type");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_accountlinks  (accountid, email, urlkey, type, creationtime, expirationtime, usedtime, usecount, state)
					VALUES ('$accountid', '$email', '$urlkey', '$type', '$creationtime', '$expirationtime', '$usedtime', '$usecount', '$state')"
					);

			if ($result)
			{
				$accountLinkObject = new AccountLinkRow(
						$dbcon,
						$accountid,
						$email,
						$urlkey,
						$type,
						$creationtime,
						$expirationtime,
						$usedtime,
						$usecount,
						$state
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTLINKROW, __METHOD__, "Failed to insert row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKROW, __METHOD__, $result);
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

			$rows = AccountLinkRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_ACCOUNTLINKROW, __METHOD__);

			$rows = NULL;

			$selectFields = "accountid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add accountid as it's already included by default.
					if ($field != "accountid")
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

			DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_accountlinks $filterString $sortString");

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

						$rows[] = new AccountLinkRow(
								$dbcon,
								isset($row['accountid']) ? $row['accountid'] : NULL,
								isset($row['email']) ? $row['email'] : NULL,
								isset($row['urlkey']) ? $row['urlkey'] : NULL,
								isset($row['type']) ? $row['type'] : NULL,
								isset($row['creationtime']) ? $row['creationtime'] : NULL,
								isset($row['expirationtime']) ? $row['expirationtime'] : NULL,
								isset($row['usedtime']) ? $row['usedtime'] : NULL,
								isset($row['usecount']) ? $row['usecount'] : NULL,
								isset($row['state']) ? $row['state'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTLINKROW, __METHOD__, "Select failed with error=$sqlError");
			}

			DBG_RETURN(DBGZ_ACCOUNTLINKROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKROW, __METHOD__, "accountid=$this->accountid, email=$this->email");

			$result = TRUE;

			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->accountid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "Updating row with accountid=$this->accountid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_accountlinks
							 SET $setString
							 WHERE accountid='$this->accountid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_ACCOUNTLINKROW, __METHOD__, "Select failed with error=$sqlError");
					}
				}
				else
				{
					DBG_WARN(DBGZ_ACCOUNTLINKROW, __METHOD__, "Must set accountid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "No fields were changed, nothing to update.");
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKROW, __METHOD__);

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

			DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE core_accountlinks SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTLINKROW, __METHOD__, "Failed to update row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTLINKROW, __METHOD__);

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

			DBG_INFO(DBGZ_ACCOUNTLINKROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM core_accountlinks $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTLINKROW, __METHOD__, "Failed to delete rows with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTLINKROW, __METHOD__, $result);
			return $result;
		}
	}
?>
