<?php

	use Core\Common\Classes\MethodCallContext;
	use Core\Common\Data\AccountRow;
	use Idax\Tube\Classes\ReportFormatManager;
	use Idax\Tube\Data\ReportFormatRow;

	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';

	$reportFormats = array(
			array(
					'name' => 'Standard',
					'fields' => array(
							)
					),
			array(
					'name' => 'Bellevue',
					'fields' => array(
							)
					),
			array(
					'name' => 'Seattle',
					'fields' => array(
							array(
									"name" => "highway",
									"friendlyname" => "Highway",
									"description" => "Highway",
									"type" => "bool",
									"required" => true,
									"defaultvalue" => false,
									"allowedvalues" => NULL
									),
							array(
									"name" => "trafficcontrol",
									"friendlyname" => "Traffic Control",
									"description" => "Traffic Control",
									"type" => "string",
									"required" => true,
									"defaultvalue" => "none",
									"allowedvalues" => array("Stop sign", "Red, yellow, green light", "Yellow light", "none")
									)
							)
					),
			array(
					'name' => 'Redmond',
					'fields' => array(
							array(
									"name" => "stationid",
									"friendlyname" => "Station ID",
									"description" => "Station ID",
									"type" => "string",
									"required" => true,
									"defaultvalue" => NULL,
									"allowedvalues" => NULL
									),
							array(
									"name" => "specificlocation",
									"friendlyname" => "Specific Location",
									"description" => "Specific Location",
									"type" => "string",
									"required" => true,
									"defaultvalue" => NULL,
									"allowedvalues" => NULL
									),
							array(
									"name" => "speedlimit",
									"friendlyname" => "Speed Limit",
									"description" => "Speed Limit",
									"type" => "number",
									"required" => true,
									"defaultvalue" => NULL,
									"allowedvalues" => NULL
									)
							)
					)
			);

	function CreateData(
		$con,
		$reportFormats
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$accountRow = AccountRow::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError);
		$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);

		$reportFormatManager = new ReportFormatManager($context);

		foreach ($reportFormats as &$reportFormat)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "Creating report format ".$reportFormat['name']);

			$result = $reportFormatManager->CreateReportFormat(
					$reportFormat['name'],
					$reportFormat['fields'],
					$resultString
					);

			if ($result)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Success!");
			}
			else
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Failed! resultString='$resultString'");
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function EraseData(
		$con,
		$reportFormats
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		foreach ($reportFormats as &$reportFormat)
		{
			ReportFormatRow::Delete($con, array("name='".$reportFormat["name"]."'"), $sqlError);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_ACCOUNTROW | DBGZ_APP | DBGZ_TUBE_REPORTFORMATMGR | DBGZ_TUBE_REPORTFORMATROW,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal,
			FALSE
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	// connect and verify connection
	$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-c')
		{
			CreateData($con, $reportFormats);
		}
		else if ($argv[1] == '-e')
		{
			EraseData($con, $reportFormats);
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-c | -e]\n";
	}

	mysqli_close($con);
?>
