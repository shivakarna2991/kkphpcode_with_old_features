<?php

	namespace Core\Common\Data;

	//require_once 'core/core.php';
	require_once __DIR__.'/../../../core/core.php';

	class LoginRow
	{
		private $dbcon = NULL;

		private $loginid = NULL;
		private $accountid = NULL;
		private $loggedintime = NULL;
		private $loggedouttime = NULL;
		private $token = NULL;
		private $tokenexpirationtime = NULL;
		private $loggedinlocation = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getLoginId()
		{
			return $this->loginid;
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

		public function getLoggedInTime()
		{
			return $this->loggedintime;
		}

		public function setLoggedInTime($value)
		{
			if ($this->loggedintime != $value)
			{
				$this->loggedintime = $value;
				$this->fieldUpdated('loggedintime', $value);
			}
		}

		public function getLoggedOutTime()
		{
			return $this->loggedouttime;
		}

		public function setLoggedOutTime($value)
		{
			if ($this->loggedouttime != $value)
			{
				$this->loggedouttime = $value;
				$this->fieldUpdated('loggedouttime', $value);
			}
		}

		public function getToken()
		{
			return $this->token;
		}

		public function setToken($value)
		{
			if ($this->token != $value)
			{
				$this->token = $value;
				$this->fieldUpdated('token', $value);
			}
		}

		public function getTokenExpirationTime()
		{
			return $this->tokenexpirationtime;
		}

		public function setTokenExpirationTime($value)
		{
			if ($this->tokenexpirationtime != $value)
			{
				$this->tokenexpirationtime = $value;
				$this->fieldUpdated('tokenexpirationtime', $value);
			}
		}

		public function getLoggedInLocation()
		{
			return $this->loggedinlocation;
		}

		public function setLoggedInLocation($value)
		{
			if ($this->loggedinlocation != $value)
			{
				$this->loggedinlocation = $value;
				$this->fieldUpdated('loggedinlocation', $value);
			}
		}

		public function __construct(
			$dbcon,
			$loginid,
			$accountid,
			$loggedintime,
			$loggedouttime,
			$token,
			$tokenexpirationtime,
			$loggedinlocation
			)
		{
			$this->dbcon = $dbcon;

			$this->loginid = $loginid;
			$this->accountid = $accountid;
			$this->loggedintime = $loggedintime;
			$this->loggedouttime = $loggedouttime;
			$this->token = $token;
			$this->tokenexpirationtime = $tokenexpirationtime;
			$this->loggedinlocation = $loggedinlocation;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$accountid,
			$loggedintime,
			$loggedouttime,
			$token,
			$tokenexpirationtime,
			$loggedinlocation,
			&$loginObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_LOGINROW, __METHOD__);

			DBG_INFO(DBGZ_LOGINROW, __METHOD__, "Inserting row with accountid=$accountid");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO core_logins (accountid, loggedintime, loggedouttime, token, tokenexpirationtime, loggedinlocation)
					VALUES ('$accountid', '$loggedintime', '$loggedouttime', '$token', '$tokenexpirationtime', '$loggedinlocation')"
					);
                                       // echo $loggedouttime;exit;
			if ($result)
			{                            //echo 'succ';exit;
				$loginid = mysqli_insert_id($dbcon);

				$loginObject = new LoginRow(
						$dbcon,
						$loginid,
						$accountid,
						$loggedintime,
						$loggedouttime,
						$token,
						$tokenexpirationtime,
						$loggedinlocation
						);
			}
			else
			{
                            
				$sqlError = mysqli_errno($dbcon);
                                echo 'eses'; print_r($dbcon);//exit;
                                echo 'eses'; print_r($sqlError);exit;
				DBG_ERR(DBGZ_LOGINROW, __METHOD__, "Failed to insert row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_LOGINROW, __METHOD__, $result);
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

			$rows = LoginRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType);

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
			DBG_ENTER(DBGZ_LOGINROW, __METHOD__);

			$rows = NULL;

			$selectFields = "loginid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add loginid as it's already included by default.
					if ($field != "loginid")
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

			DBG_INFO(DBGZ_LOGINROW, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM core_logins $filterString $sortString");

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

						$rows[] = new LoginRow(
								$dbcon,
								isset($row['loginid']) ? $row['loginid'] : NULL,
								isset($row['accountid']) ? $row['accountid'] : NULL,
								isset($row['loggedintime']) ? $row['loggedintime'] : NULL,
								isset($row['loggedouttime']) ? $row['loggedouttime'] : NULL,
								isset($row['token']) ? $row['token'] : NULL,
								isset($row['tokenexpirationtime']) ? $row['tokenexpirationtime'] : NULL,
								isset($row['loggedinlocation']) ? $row['loggedinlocation'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_LOGINROW, __METHOD__, "Select failed with error=$sqlError");
			}

			DBG_RETURN(DBGZ_LOGINROW, __METHOD__, "found ".count($rows)." rows");
			return $rows;
		}

		public function CommitChangedFields()
		{
			DBG_ENTER(DBGZ_LOGINROW, __METHOD__, "loginid=$this->loginid");

			$result = TRUE;

			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->loginid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_LOGINROW, __METHOD__, "Updating row with loginid=$this->loginid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE core_logins
							 SET $setString
							 WHERE loginid='$this->loginid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_LOGINROW, __METHOD__, "Select failed with error=$sqlError");
					}
				}
				else
				{
					DBG_WARN(DBGZ_LOGINROW, __METHOD__, "Must set loginid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_LOGINROW, __METHOD__, "No fields were changed, nothing to update.");
			}

			DBG_RETURN_BOOL(DBGZ_LOGINROW, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$con_user,
			$fields,
			$filters
			)
		{
			DBG_ENTER(DBGZ_LOGINROW, __METHOD__);

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

			DBG_INFO(DBGZ_LOGINROW, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($con_user, "UPDATE core_logins SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($con_user);
				DBG_ERR(DBGZ_LOGINROW, __METHOD__, "Failed to update row with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_LOGINROW, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$con_user,
			$filters
			)
		{
			DBG_ENTER(DBGZ_LOGINROW, __METHOD__);

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

			DBG_INFO(DBGZ_LOGINROW, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($con_user, "DELETE FROM core_logins $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($con_user);
				DBG_ERR(DBGZ_LOGINROW, __METHOD__, "Failed to delete rows with error=$sqlError");
			}

			DBG_RETURN_BOOL(DBGZ_LOGINROW, __METHOD__, $result);
			return $result;
		}
	}
?>
