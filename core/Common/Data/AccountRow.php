<?php

	namespace Core\Common\Data;

	require_once 'core/core.php';

	class AccountRow
	{
		private $dbcon = NULL;

		private $accountid = NULL;
		private $email = NULL;
		private $state = NULL;
		private $creationtime = NULL;
		private $registeredtime = NULL;
		private $lastlogintime = NULL;
		private $firstname = NULL;
		private $lastname = NULL;
		private $passwordhash = NULL;
		private $failedloginattempts = NULL;
		private $role = NULL;
		private $rating = NULL;
		private $developer = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getAccountId()
		{
			return $this->accountid;
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

		public function getRegisteredTime()
		{
			return $this->registeredtime;
		}

		public function setRegisteredTime($value)
		{
			if ($this->registeredtime != $value)
			{
				$this->registeredtime = $value;
				$this->fieldUpdated('registeredtime', $value);
			}
		}

		public function getLastLoginTime()
		{
			return $this->lastlogintime;
		}

		public function setLastLoginTime($value)
		{
			if ($this->lastlogintime != $value)
			{
				$this->lastlogintime = $value;
				$this->fieldUpdated('lastlogintime', $value);
			}
		}

		public function getFirstName()
		{
			return $this->firstname;
		}

		public function setFirstName($value)
		{
			if ($this->firstname != $value)
			{
				$this->firstname = $value;
				$this->fieldUpdated('firstname', $value);
			}
		}

		public function getLastName()
		{
			return $this->lastname;
		}

		public function setLastName($value)
		{
			if ($this->lastname != $value)
			{
				$this->lastname = $value;
				$this->fieldUpdated('lastname', $value);
			}
		}

		public function getPasswordHash()
		{
			return $this->passwordhash;
		}

		public function setPasswordHash($value)
		{
			if ($this->passwordhash != $value)
			{
				$this->passwordhash = $value;
				$this->fieldUpdated('passwordhash', $value);
			}
		}

		public function getFailedLoginAttempts()
		{
			return $this->failedloginattempts;
		}

		public function setFailedLoginAttempts($value)
		{
			if ($this->failedloginattempts != $value)
			{
				$this->failedloginattempts = $value;
				$this->fieldUpdated('failedloginattempts', $value);
			}
		}

		public function getRole()
		{
			return $this->role;
		}

		public function setRole($value)
		{
			if ($this->role != $value)
			{
				$this->role = $value;
				$this->fieldUpdated('role', $value);
			}
		}

		public function getRating()
		{
			return $this->rating;
		}

		public function setRating($value)
		{
			if ($this->rating != $value)
			{
				$this->rating = $value;
				$this->fieldUpdated('rating', $value);
			}
		}

		public function getDeveloper()
		{
			return $this->developer;
		}

		public function setDeveloper($value)
		{
			if ($this->developer != $value)
			{
				$this->developer = $value;
				$this->fieldUpdated('developer', $value);
			}
		}

		public function __construct(
			$dbcon,
			$accountid,
			$email,
			$state,
			$creationtime,
			$registeredtime,
			$lastlogintime,
			$firstname,
			$lastname,
			$passwordhash,
			$failedloginattempts,
			$role,
			$rating,
			$developer
			)
		{
			$this->dbcon = $dbcon;

			$this->accountid = $accountid;
			$this->email = $email;
			$this->state = $state;
			$this->creationtime = $creationtime;
			$this->registeredtime = $registeredtime;
			$this->lastlogintime = $lastlogintime;
			$this->firstname = $firstname;
			$this->lastname = $lastname;
			$this->passwordhash = $passwordhash;
			$this->failedloginattempts = $failedloginattempts;
			$this->role = $role;
			$this->rating = $rating;
			$this->developer = $developer;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$email,
			$state,
			$creationtime,
			$registeredtime,
			$lastlogintime,
			$firstname,
			$lastname,
			$passwordhash,
			$failedloginattempts,
			$role,
			$rating,
			$developer,
			&$accountObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTROW, __METHOD__);

			DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "Inserting row with email=$email");

			$escapedFirstName = mysqli_real_escape_string($dbcon, $firstname);
			$escapedLastName = mysqli_real_escape_string($dbcon, $lastname);

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_accounts (email, state, creationtime, registeredtime, lastlogintime, firstname, lastname,
							passwordhash, failedloginattempts, role, rating, developer)
					VALUES ('$email', '$state', '$creationtime', '$registeredtime', '$lastlogintime', '$escapedFirstName', '$escapedLastName',
							'$passwordhash', '$failedloginattempts', '$role', '$rating', '$developer')"
					);

			if ($result)
			{
				$accountid = mysqli_insert_id($dbcon);

				$accountObject = new AccountRow(
						$dbcon,
						$accountid,
						$email,
						$state,
						$creationtime,
						$registeredtime,
						$lastlogintime,
						$firstname,
						$lastname,
						$passwordhash,
						$failedloginattempts,
						$role,
						$rating,
						$developer
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTROW, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTROW, __METHOD__, $result);
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

			$rows = AccountRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

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
			DBG_ENTER(DBGZ_ACCOUNTROW, __METHOD__);

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

			DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_accounts $filterString $sortString");

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

						$rows[] = new AccountRow(
								$dbcon,
								isset($row['accountid']) ? $row['accountid'] : NULL,
								isset($row['email']) ? $row['email'] : NULL,
								isset($row['state']) ? $row['state'] : NULL,
								isset($row['creationtime']) ? $row['creationtime'] : NULL,
								isset($row['registeredtime']) ? $row['registeredtime'] : NULL,
								isset($row['lastlogintime']) ? $row['lastlogintime'] : NULL,
								isset($row['firstname']) ? $row['firstname'] : NULL,
								isset($row['lastname']) ? $row['lastname'] : NULL,
								isset($row['passwordhash']) ? $row['passwordhash'] : NULL,
								isset($row['failedloginattempts']) ? $row['failedloginattempts'] : NULL,
								isset($row['role']) ? $row['role'] : NULL,
								isset($row['rating']) ? $row['rating'] : NULL,
								isset($row['developer']) ? $row['developer'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_ACCOUNTROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTROW, __METHOD__, "accountid=$this->accountid, email=$this->email");

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
						$escapedChangedField = mysqli_real_escape_string($this->dbcon, $changedField);

						$setString = "$setString $fieldName='$escapedChangedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "Updating row with accountid=$this->accountid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_accounts
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
						DBG_ERR(DBGZ_ACCOUNTROW, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_ACCOUNTROW, __METHOD__, "Must set accountid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "No fields were changed, nothing to update.");
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTROW, __METHOD__);

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

			DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE core_accounts SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTROW, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters
			)
		{
			DBG_ENTER(DBGZ_ACCOUNTROW, __METHOD__);

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

			DBG_INFO(DBGZ_ACCOUNTROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM core_accounts $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_ACCOUNTROW, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_ACCOUNTROW, __METHOD__, $result);
			return $result;
		}
	}
?>
