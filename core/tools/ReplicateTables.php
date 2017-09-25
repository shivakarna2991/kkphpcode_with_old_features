<?php

	require_once '/home/core/core.php';

	$usage = FALSE;

	$sourceDatabase = NULL;
	$destinationDatabase = NULL;
	$allTables = TRUE;
	$tableName = NULL;
	$replicateData = FALSE;

	$databases = array(
			"idax" => array(
					"host" => "idax-data.c9iqbocaavpb.us-west-2.rds.amazonaws.com",
					"name" => "idaxdata",
					"username" => "idaxdbadmin",
					"password" => "IdaxDBMaster"
					),
			"kanopian" => array(
					"host" => "idax-data.c9iqbocaavpb.us-west-2.rds.amazonaws.com",
					"name" => "idaxdata",
					"username" => "idaxdbadmin",
					"password" => "IdaxDBMaster"
					),
			);

	// Retrieve command line parameters
	// s - source database
	// d - destination database
	// a = all tables
	// t - tableName
	// r - replicate data (default is false)
	$params = getopt("s:d:a:t:r");

	DBG_SET_PARAMS(DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN | DBGZ_APP, dbg_dest_terminal);
	//DBG_SET_PARAMS(0, dbg_dest_terminal);

	// Need user email or userid (not both)
	if (array_key_exists("s", $params))
	{
		$sourceDatabase = $params["s"];
	}
	else
	{
		$usage = TRUE;
	}

	if (array_key_exists("d", $params))
	{
		$destinationDatabase = $params["d"];
	}
	else
	{
		$usage = TRUE;
	}

	if (array_key_exists("a", $params))
	{
		if (array_key_exists("t", $params))
		{
			$usage = TRUE;
		}
		else
		{
			$allTables = TRUE;
		}
	}
	else if (array_key_exists("t", $params))
	{
		$tableName = $params["t"];
	}

	if (array_key_exists("r", $params))
	{
		$replicateData = TRUE;
	}

	if ($usage)
	{
		echo "Usage: {$argv[0]} -s source | -d dest [-a | -t tablename] [-r]\n";
		exit(0);
	}

	DBG_INFO(DBGZ_APP, "ReplicateTables", "sourceDatabase=$sourceDatabase, destinationDatabase=$destinationDatabase, allTables=$allTables, tableName=$tableName, replicateData=$replicateData");

	// Connect to source database
	$dbInfo = $databases[$sourceDatabase];

	$conSource = mysqli_connect($dbInfo["host"], $dbInfo["username"], $dbInfo["password"], $dbInfo["name"]);

	$sqlError = mysqli_connect_errno($conSource);

	if ($sqlError != 0)
	{
		DBG_ERR(DBGZ_APP, "ReplicateTables", "Failed to connect to $sourceDatabase database. Error=$sqlError");
		exit();
	}

	// Connect to destination database
	$dbInfo = $databases[$destinationDatabase];

	$conDestination = mysqli_connect($dbInfo["host"], $dbInfo["username"], $dbInfo["password"], $dbInfo["name"]);

	$sqlError = mysqli_connect_errno($conDestination);

	if ($sqlError != 0)
	{
		DBG_ERR(DBGZ_APP, "ReplicateTables", "Failed to connect to $destinationDatabase database. Error=$sqlError");

		mysqli_close($conSource);

		exit();
	}

	// Create tables array - an array of table names to replicate
	if ($tableName != NULL)
	{
		$tables = array($tableName);
	}
	else
	{
		$tables = array();

		$result = mysqli_query($conSource, "SHOW TABLES");

		if ($result)
		{
			$numRows = mysqli_num_rows($result);

			for ($i=0; $i<$numRows; $i++)
			{
				$row = mysqli_fetch_row($result);

				$tables[] = $row[0];
			}
		}
		else
		{
			$sqlError = mysqli_errno($dbcon);
			DBG_ERR(DBGZ_APP, "ReplicateTables", "Query failed with error=$sqlError");
		}
	}

	// Replicate the tables
	foreach ($tables as &$table)
	{
		DBG_INFO(DBGZ_APP, "ReplicateTables", "Replicating table $table");

		$result = mysqli_query($conSource, "SHOW CREATE TABLE $table");

		if ($result)
		{
			$row = mysqli_fetch_row($result);

			$createCommand = $row[1];

			DBG_INFO(DBGZ_APP, "ReplicateTables", "Create command for $table is $createCommand");
			$result = TRUE; //mysqli_query($conDestination, $createCommand);

			if ($result)
			{
				if ($replicateData)
				{
					DBG_INFO(DBGZ_APP, "ReplicateTables", "Replicating data for table $table");

					$result = mysqli_query($conSource, "SELECT * FROM $table");

					if ($result)
					{
						$numRows = mysqli_num_rows($result);

						for ($i=0; $i<$numRows; $i++)
						{
							$fieldsString = "";
							$valuesString = "";

							$row = mysqli_fetch_assoc($result);

							foreach ($row as $fieldName => &$fieldValue)
							{
								$fieldsString .= "$fieldName, ";
								$escapedFieldValue = mysqli_real_escape_string($conDestination, $fieldValue);
								$valuesString .= "'$escapedFieldValue', ";
							}

							$fieldsString = trim($fieldsString, ", ");
							$valuesString = trim($valuesString, ", ");

							DBG_INFO(DBGZ_APP, "ReplicateTables", "INSERT INTO $table ($fieldsString) VALUES ($valuesString)");
							//mysqli_query($conDestination, "INSERT INTO $table ($fieldsString) VALUES ($valuesString)");
						}
					}
					else
					{
						DBG_ERR(DBGZ_APP, "ReplicateTables", "SELECT * FROM $table failed with error=$sqlError");
					}
				}
			}
			else
			{
				DBG_ERR(DBGZ_APP, "ReplicateTables", "Create table $tableName failed with error=$sqlError");
				break;
			}
		}
		else
		{
			DBG_ERR(DBGZ_APP, "ReplicateTables", "SHOW CREATE TABLE command for table $tableName failed with error=$sqlError");
			break;
		}
	}

	mysqli_close($conSource);
	mysqli_close($conDestination);

	return;
?>
