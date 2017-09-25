<?php

	require_once '/home/core/core.php';
	require_once '/home/idax/idax.php';
	require_once '/home/idax/PHPExcel/Classes/PHPExcel.php';
	require_once '/home/idax/classes/ExcelHelpers.php';

	$renderpaths = array(
			PHPExcel_Settings::PDF_RENDERER_IDAXPDF => '/home/idax/ipdf',
			);

	DBG_SET_PARAMS(DBGZ_APP | DBGZ_ACCOUNTMGR | DBGZ_ACCOUNTROW, DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN, FALSE, FALSE, dbg_dest_terminal);

	if ($argc < 3)
	{
		echo "Usage: {$argv[0]} xlsfile pdffile\ [-pdfclass] [-L leftmargin -R rightmargin -T topmargin -B bottommargin -O orientation -P papersize]n";
		exit(0);
	}

	$xlsfile = $argv[1];
	$pdffile = $argv[2];
	$pdfclass = PHPExcel_Settings::PDF_RENDERER_IDAXPDF;
	$orientation = PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT;
	$papersize = PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER;
	$topmargin = $bottommargin = $leftmargin = $rightmargin = 0.5;

	if ($argc == 4)
	{
		$pdfmethod = $argv[3];
	}

	$objPHPExcel = PHPExcel_IOFactory::load($xlsfile);

	// Now save it as a PDF file on the local machine.
	$rendererName = PHPExcel_Settings::PDF_RENDERER_IDAXPDF;
	$rendererLibraryPath = '/home/idax/ipdf';

?>
