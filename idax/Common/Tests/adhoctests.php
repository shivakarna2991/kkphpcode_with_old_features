<?php

	require_once '/home/core/core.php';

	if (0 === NULL)
	{
		echo "(0 === NULL) evaluates to TRUE\n";
	}
	else if (0 == NULL)
	{
		echo "(0 === NULL) evaluates to FALSE\n";
		echo "(0 == NULL) evaluates to TRUE\n";
	}
	else
	{
		echo "(0 === NULL) evaluates to FALSE\n";
		echo "(0 == NULL) evaluates to FALSE\n";
	}

	die();

	require_once 'idax.php';
	require_once PROJECT_ROOT.'/vendor/fpdf/fpdf.php';

	function DoStrtokTest()
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$token = strtok("Site:	[BOX 146] MetroCount Factory Test Setup", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Attribute: ", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Direction:	1 - North bound, A trigger first. Lane: 0", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Survey Duration:	13:47 Sunday, October 18, 2015 => 23:19 Saturday, October 24, 2015,", ":");
		$value1 = trim(strtok("="));
		$value2 = trim(strtok(""), " \t\n\r,>");

		echo "$token: $value1 - $value2";

		$token = strtok("Zone:	", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("File:	BOX 149 SB.EC0 (Plus )", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Identifier:	KD687E43 MC56-L5 [MC55] (c)Microcom 19Oct04", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Algorithm:	Factory default axle (v4.08)", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		$token = strtok("Data type:	Axle sensors - Paired (Class/Speed/Count)", ":");
		$value = trim(strtok(""));

		echo "$token: $value\n";

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-t')
		{
			echo "Calling DoStrtokTest()\n";
			DoStrtokTest();
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." -t\n";
	}
?>
