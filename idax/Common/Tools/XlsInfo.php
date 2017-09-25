<?php

	require_once '/home/core/core.php';
	require_once '/home/idax/idax.php';
	require_once '/home/idax/PHPExcel/Classes/PHPExcel.php';
	require_once '/home/idax/classes/ExcelHelpers.php';

	function GetWorksheetInfo(
		$objPHPExcel,
		$worksheetName
		)
	{
		$worksheet = $objPHPExcel->getSheetByName($worksheetName);

		echo "Worksheet: ".$worksheet->getTitle().PHP_EOL;
		echo "\tState: ".$worksheet->getSheetState();

		$topMargin = $worksheet->getPageMargins()->getTop();
		$rightMargin = $worksheet->getPageMargins()->getRight();
		$leftMargin = $worksheet->getPageMargins()->getLeft();
		$bottomMargin = $worksheet->getPageMargins()->getBottom();

		echo "\tMargins: right $rightMargin, left $leftMargin, top $topMargin, bottom $bottomMargin\n";

		echo "\tDefault row dimension: ".$worksheet->getDefaultRowDimension()->getRowHeight().PHP_EOL;
		echo "\tDefault column dimension: ".$worksheet->getDefaultColumnDimension()->getWidth().PHP_EOL;

		echo "\tRow Dimensions".PHP_EOL;

		$dimensions = $worksheet->getRowDimensions();

		foreach ($dimensions as $dimension)
		{
			echo "\t\tRow ".$dimension->getRowIndex().": ".$dimension->getRowHeight().PHP_EOL;
		}

		echo "\tColumn Dimensions".PHP_EOL;

		$dimensions = $worksheet->getColumnDimensions();

		foreach ($dimensions as $dimension)
		{
			echo "\t\tColumn ".$dimension->getColumnIndex().": ".$dimension->getWidth().PHP_EOL;
		}

		"\tHighest data row: ".$worksheet->getHighestDataRow().PHP_EOL;
		"\tHighest data column: ".$worksheet->getHighestDataColumn().PHP_EOL;
		$pageSetup = $worksheet->getPageSetup();
		$pageMargins = $worksheet->getPageMargins();
		$headerFooter = $worksheet->getHeaderFooter();
		$sheetView = $worksheet->getSheetView();
		"\tProtection: ".$worksheet->getProtection()->isProtectionEnabled().PHP_EOL;
	}

	function GetSpreadsheetInfo(
		$objPHPExcel
		)
	{
		$worksheetNames = $objPHPExcel->getSheetNames();

		foreach ($worksheetNames as &$worksheetName)
		{
			echo "Getting info for worksheet $worksheetName\n";
			GetWorksheetInfo($objPHPExcel, $worksheetName);
		}
	}

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);

	// Set defaults for user options
	$filename = NULL;
	$getInfo = FALSE;
	$setProperties = FALSE;
	$setWidths = FALSE;
	$setPrintArea = FALSE;
	$setHeader = FALSE;
	$setFooter = FALSE;
	$setPaperSize = FALSE;
	$setOrientation = FALSE;
	$saveAsPDF = FALSE;

	// Retrieve user options from command line parameters
	$usage = FALSE;

	if (isset($argv[1]))
	{
		$filename = $argv[1];
	}
	else
	{
		$usage = TRUE;
	}

	for ($i=2; $i<$argc; $i++)
	{
		switch ($argv[$i])
		{
			case '-i':
				$getInfo = TRUE;
				break;
			case '-spr':
				break;
			case '-scol':
				break;
			case '-spa':
				break;
			case '-sh':
				break;
			case '-sf':
				break;
			case '-sps':
				break;
			case '-sor':
				break;
			default:
				$usage = TRUE;
				break;
		}
	}

	if ($usage || ($filename == NULL))
	{
		echo "Usage: {$argv[0]} xlsfile | -i | -spr name:value -scol col:width -spa col:row:col:row -sh header -sf footer -sps papersize -sor orientation -spdf\n";
		exit(0);
	}

	$objPHPExcel = PHPExcel_IOFactory::load($filename);

	if ($getInfo)
	{
		GetSpreadsheetInfo($objPHPExcel);
	}
?>
