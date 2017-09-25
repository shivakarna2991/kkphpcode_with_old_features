<?php

	namespace Idax\Common\Data;

	require_once 'idax/idax.php';

	class CustomerRow
	{
		private $dbcon = NULL;

		private $customerid = NULL;
		private $companyname = NULL;
		private $street1 = NULL;
		private $street2 = NULL;
		private $city = NULL;
		private $state = NULL;
		private $zipcode = NULL;
		private $phone = NULL;

		private $changedFields = array();

		private function fieldUpdated($fieldName, $fieldValue)
		{
			$this->changedFields[$fieldName] = $fieldValue;
		}

		public function getCustomerId()
		{
			return $this->customerid;
		}

		public function getCompanyName()
		{
			return $this->companyname;
		}

		public function setCompanyName($value)
		{
			if ($this->companyname != $value)
			{
				$this->companyname = $value;
				$this->fieldUpdated('companyname', $value);
			}
		}

		public function getStreet1()
		{
			return $this->street1;
		}

		public function setStreet1($value)
		{
			if ($this->street1 != $value)
			{
				$this->street1 = $value;
				$this->fieldUpdated('street1', $value);
			}
		}

		public function getStreet2()
		{
			return $this->street2;
		}

		public function setStreet2($value)
		{
			if ($this->street2 != $value)
			{
				$this->street2 = $value;
				$this->fieldUpdated('street1', $value);
			}
		}

		public function getCity()
		{
			return $this->city;
		}

		public function setCity($value)
		{
			if ($this->city != $value)
			{
				$this->city = $value;
				$this->fieldUpdated('city', $value);
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

		public function getZipCode()
		{
			return $this->zipcode;
		}

		public function setZipCode($value)
		{
			if ($this->zipcode != $value)
			{
				$this->zipcode = $value;
				$this->fieldUpdated('zipcode', $value);
			}
		}

		public function getPhone()
		{
			return $this->phone;
		}

		public function setPhone($value)
		{
			if ($this->phone != $value)
			{
				$this->phone = $value;
				$this->fieldUpdated('phone', $value);
			}
		}

		public function __construct(
			$dbcon,
			$customerid,
			$companyname,
			$street1,
			$street2,
			$city,
			$state,
			$zipcode,
			$phone
			)
		{
			$this->dbcon = $dbcon;

			$this->customerid = $customerid;
			$this->companyname = $companyname;
			$this->street1 = $street1;
			$this->street2 = $street2;
			$this->city = $city;
			$this->state = $state;
			$this->zipcode = $zipcode;
			$this->phone = $phone;
		}

		public function __destruct()
		{
		}

		static public function Create(
			$dbcon,
			$companyname,
			$street1,
			$street2,
			$city,
			$state,
			$zipcode,
			$phone,
			&$customerObject,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_CUSTOMERS, __METHOD__);

			DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "Inserting row with companyname=$companyname");

			$result = mysqli_query(
					$dbcon,
				 	"INSERT INTO customers (companyname, street1, street2, city, state, zipcode, phone)
					VALUES ('$companyname', '$street1', '$street2', '$city', '$state', '$zipcode', '$phone')"
					);

			if ($result)
			{
				$customerid = mysqli_insert_id($dbcon);

				$customerObject = new CustomerRow(
						$dbcon,
						$customerid,
						$companyname,
						$street1,
						$street2,
						$city,
						$state,
						$zipcode,
						$phone
						);
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_CUSTOMERS, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_CUSTOMERS, __METHOD__, $result);
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
			$customer = NULL;

			$customers = CustomerRow::Find($dbcon, $fields, $filters, $sortOrder, $returnType, $sqlError);

			if ($customers)
			{
				$customer = $customers[0];
			}

			return $customer;
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
			DBG_ENTER(DBGZ_CUSTOMERS, __METHOD__);

			$customers = NULL;

			$selectFields = "customerid";
			$numSelectFields = 0;

			if ($fields != NULL)
			{
				foreach ($fields as $field)
				{
					// Don't need to add customerid as it's already included by default.
					if ($field != "customerid")
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

			DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "selectFields='$selectFields', sortString='$sortString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "SELECT $selectFields FROM customers $filterString $sortString");

			if ($result)
			{
				$numRows = mysqli_num_rows($result);

				for ($i=0; $i<$numRows; $i++)
				{
					if ($returnType == ROW_NUMERIC)
					{
						$customers[] = mysqli_fetch_row($result);
					}
					else if ($returnType == ROW_ASSOCIATIVE)
					{
						$customers[] = mysqli_fetch_assoc($result);
					}
					else
					{
						$row = mysqli_fetch_array($result);

						$customers[] = new CustomerRow(
								$dbcon,
								isset($row['customerid']) ? $row['customerid'] : NULL,
								isset($row['companyname']) ? $row['companyname'] : NULL,
								isset($row['street1']) ? $row['$street1'] : NULL,
								isset($row['street2']) ? $row['street2'] : NULL,
								isset($row['city']) ? $row['city'] : NULL,
								isset($row['state']) ? $row['state'] : NULL,
								isset($row['zipcode']) ? $row['zipcode'] : NULL,
								isset($row['phone']) ? $row['phone'] : NULL
								);
					}
				}
			}
			else
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_CUSTOMERS, __METHOD__, "Select failed with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN(DBGZ_CUSTOMERS, __METHOD__, "found ".count($customers)." customers");
			return $customers;
		}

		public function CommitChangedFields(
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_CUSTOMERS, __METHOD__, "customerid=$this->customerid");

			$result = FALSE;
			$numFieldsChanged = count($this->changedFields);

			if ($numFieldsChanged > 0)
			{
				if ($this->customerid != NULL)
				{
					$setString = "";

					while (($changedField = current($this->changedFields)) !== FALSE)
					{
						$fieldName = key($this->changedFields);
						$setString = "$setString $fieldName='$changedField', ";

						next($this->changedFields);
					}

					$setString = trim($setString, ', ');

					DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "Updating row with customerid=$this->customerid. Number of fields changed: $numFieldsChanged. setString='$setString'");

					$result = mysqli_query(
							$this->dbcon,
							"UPDATE customers
							SET $setString
							WHERE customerid='$this->customerid'"
							);

					if ($result)
					{
						// Fields were written.  Reset the changedFields array.
						$this->changedFields = array();
					}
					else
					{
						$sqlError = mysqli_errno($this->dbcon);
						DBG_ERR(DBGZ_CUSTOMERS, __METHOD__, "Failed to insert row with error=$sqlError, ".mysqli_error($this->dbcon));
					}
				}
				else
				{
					DBG_WARN(DBGZ_CUSTOMERS, __METHOD__, "Must set customerid property before calling this method.");
				}
			}
			else
			{
				DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "No fields were changed, nothing to update.");

				// Nothing to change but we should return true.
				$result = TRUE;
			}

			DBG_RETURN_BOOL(DBGZ_CUSTOMERS, __METHOD__, $result);
			return $result;
		}

		public static function Update(
			$dbcon,
			$fields,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_CUSTOMERS, __METHOD__);

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

			DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "setString='$setString', filterString='$filterString'");

			$result = mysqli_query($dbcon, "UPDATE customers SET $setString $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_CUSTOMERS, __METHOD__, "Failed to update row with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_CUSTOMERS, __METHOD__, $result);
			return $result;
		}

		public static function Delete(
			$dbcon,
			$filters,
			&$sqlError
			)
		{
			DBG_ENTER(DBGZ_CUSTOMERS, __METHOD__);

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

			DBG_INFO(DBGZ_CUSTOMERS, __METHOD__, "filterString='$filterString'");

			$result = mysqli_query($dbcon, "DELETE FROM customers $filterString");

			if (!$result)
			{
				$sqlError = mysqli_errno($dbcon);
				DBG_ERR(DBGZ_CUSTOMERS, __METHOD__, "Failed to delete rows with error=$sqlError, ".mysqli_error($dbcon));
			}

			DBG_RETURN_BOOL(DBGZ_CUSTOMERS, __METHOD__, $result);
			return $result;
		}
	}
?>
