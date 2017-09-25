<?php

	namespace Idax\Tube\Reports\Classes;

	require_once '/home/idax/idax.php';

	use Idax\Common\Classes\Direction;
	use Idax\Common\Classes\ExcelHelpers;
	use Idax\Tube\Data\IngestionData;

	require_once '/home/idax/PHPExcel/Classes/PHPExcel.php';

	class ClassReport
	{
		private $context = NULL;
		private $dataRows = NULL;
		private $objPHPExcel = NULL;
		private $multiDayHourlyWorksheet = NULL;
		private $worksheetTemplates = NULL;
		private $jobId = NULL;
		private $jobSiteId = NULL;
		private $siteCode = NULL;
		private $ingestinId = NULL;
		private $title = NULL;
		private $location = NULL;
		private $filterBeginTime = NULL;
		private $filterEndTime = NULL;
		private $startTime = NULL;
		private $endTime = NULL;
		private $primaryDirection = NULL;
		private $secondaryDirection = NULL;
		private $primaryDr = NULL;
		private $secondaryDr = NULL;
		private $dailyBuckets = NULL;
		private $directionTotals = NULL;

		private $hours = array(
				"00", "01", "02", "03", "04", "05",
				"06", "07", "08", "09", "10", "11",
				"12", "13", "14", "15", "16", "17",
				"18", "19", "20", "21", "22", "23"
				);

		private $vehicleClasses = array(1,  2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13);

		public function __construct(
			$context
			)
		{
			$this->context = $context;

			$this->dailyBuckets = array();
		}

		private function InitializeBuckets()
		{
			// Day bucket - keep 'YYYY-MM-DD' - 10 characters
			$time = strtotime($this->startTime);
			$filterBeginTime = strtotime($this->filterBeginTime);
			$filterEndTime = strtotime($this->filterEndTime);
			$startTime = strtotime($this->startTime);
			$endTime = strtotime($this->endTime);

			$dayBucket = date("Y-m-d", $time);
			$dayOfWeek = date("w", $time);

			while ($time <= $endTime)
			{
				$dayBeginTime = strtotime("$dayBucket 00:00:00");
				$dayEndTime = strtotime("$dayBucket 23:59:59");

				$this->dailyBuckets[$dayBucket]['fullday'] = (($dayBeginTime >= $filterBeginTime) && ($dayEndTime <= $filterEndTime));
				$this->dailyBuckets[$dayBucket]['midweekday'] = (($dayOfWeek >= 2) && ($dayOfWeek <= 4));

				foreach ($this->hours as &$hour)
				{
					foreach ($this->vehicleClasses as &$vehicleClass)
					{
						$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr][$hour][$vehicleClass] = 0;
						$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr]['dailytotal'][$vehicleClass] = 0;

						$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr][$hour][$vehicleClass] = 0;
						$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr]['dailytotal'][$vehicleClass] = 0;
					}
				}

				$time += 24 * 60 * 60;
				$dayBucket = date("Y-m-d", $time);
				$dayOfWeek = date("w", $time);
			}

			foreach ($this->vehicleClasses as &$vehicleClass)
			{
				$this->directionTotals[$this->primaryDr][$vehicleClass] = 0;
				$this->directionTotals[$this->secondaryDr][$vehicleClass] = 0;
			}
		}

		private function GetDataByDirection()
		{
			$filter = array(
					"jobid='$this->jobId'",
					"jobsiteid='$this->jobSiteId'",
					"occurred>='$this->startTime'",
					"occurred<='$this->endTime'"
					);

			if ($this->ingestionId != 0)
			{
				$filter[] = "ingestionid='$this->ingestionId'";
			}

			$this->dataRows = IngestionData::Find(
					$this->context->dbcon,
					array("ds", "occurred", "dr", "cl"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE
					);
		}

		private function PopulateOneDayOneDir(
			$date,
			$direction,
			$directionBucket,
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_CLASSREPORT, __METHOD__, "beginningRow=$beginningRow");

			$rowHeights = array(12, 13, 12, 13, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 13, 13, 13);

			$currentRow = $beginningRow;

				for ($i=0; $i<count($rowHeights); $i++)
				{
					$this->multiDayHourlyWorksheet->getRowDimension($currentRow+$i)->setRowHeight($rowHeights[$i]);
				}

				$replicateDestinationRow = $currentRow - 1;
				ExcelHelpers::ReplicateRange($this->worksheetTemplates, "B38:P68", $this->multiDayHourlyWorksheet, "A{$replicateDestinationRow}", TRUE);
				$this->multiDayHourlyWorksheet->setBreak("A{$replicateDestinationRow}" , \PHPExcel_Worksheet::BREAK_ROW);

				// Populate the date
				$dateObject = \DateTime::createFromFormat("Y-m-d", $date);
				$formattedDate = $dateObject->format("l, F d, Y");

				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", "{$formattedDate}");
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");
				$currentRow += 1;

				// Populate the direction
				$dirObj = new Direction($direction);
				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", "{$dirObj->GetFullName()}");
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");
				$currentRow += 1;

				// Merge the header row
				$this->multiDayHourlyWorksheet->mergeCells("B{$currentRow}:N{$currentRow}");

				// Skip over the column headers
				$currentRow += 2;

				foreach ($this->hours as &$hour)
				{
					$currentColumn = "B";
					$hourlyTotal = 0;

					foreach ($this->vehicleClasses as &$vehicleClass)
					{
						$vehicleClassCount = $directionBucket[$hour][$vehicleClass];

						$hourlyTotal += $vehicleClassCount;
						$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$vehicleClassCount}");
						$currentColumn++;
					}

					// Populate the hourly total
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$hourlyTotal}");

					$currentRow += 1;
				}

				// Populate the daily class totals
				$currentColumn = "B";

				$dayTotal = 0;

				foreach ($this->vehicleClasses as &$vehicleClass)
				{
					// Populate the daily totals
					$vehicleClassTotal = $directionBucket['dailytotal'][$vehicleClass];
					$dayTotal += $vehicleClassTotal;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$vehicleClassTotal}");
					$currentColumn++;
				}

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$dayTotal}");

				$currentRow += 1;

				// Populate the percentages
				if ($dayTotal > 0)
				{
					$currentColumn = "B";

					foreach ($this->vehicleClasses as &$vehicleClass)
					{
						// Populate the daily totals
						$percentage = $directionBucket['dailytotal'][$vehicleClass] / $dayTotal;
						$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$percentage}");
						$currentColumn++;
					}
				}

				// Skip the gap between directions
				$currentRow += 2;

			DBG_RETURN(DBGZ_TUBE_CLASSREPORT, __METHOD__);
		}

		private function PopulateReportData(
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_CLASSREPORT, __METHOD__, "beginningRow=$beginningRow");

			$currentRow = $beginningRow;

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, "B2:P36", $this->multiDayHourlyWorksheet, "A1", TRUE);

			// Merge a bunch of stuff
			$mergeRanges = array(
					"A1:G2",                              // Vehicle Classification Report Summary
					"B6:J6", "B7:J7", "B8:J8", "B9:J9",   // Location, Count Direction, Date Range, Site Code
					"B13:N13",                            // FMWA Vehicle Classification
					"A15:O15",                            // Study Total
					"A25:F25",                            // FHWA Vehicle Classification (legend)
					"A26:F26", "A27:G27", "A28:G28", "A29:G29", "A30:G30", "A31:G31", "A32:G32",  // Legend items
					"H26:N26", "H27:N27", "H28:N28", "H29:N29", "H30:N30", "H31:N31", "H32:N32"   // Legend items
					);

			foreach ($mergeRanges as &$mergeRange)
			{
				$this->multiDayHourlyWorksheet->mergeCells($mergeRange);
			}

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", $this->location);
			$currentRow += 1;

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", "{$this->primaryDirection->GetFullName()} / {$this->secondaryDirection->GetFullName()}");
			$currentRow += 1;

			$startTime = strtotime($this->startTime);
			$startDate = date("m/d/Y", $startTime);

			$endTime = strtotime($this->endTime);
			$endDate = date("m/d/Y", $endTime);

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", "{$startDate} to {$endDate}");
			$currentRow += 1;

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", $this->siteCode);
			$currentRow += 7;  // Skip over some blank lines and column headers

			// Populate the class totals
			$currentColumn = "B";

			$primaryRow = $currentRow;
			$primaryPercentageRow = $currentRow + 1;
			$secondaryRow = $currentRow + 2;
			$secondaryPercentageRow = $currentRow + 3;
			$totalRow = $currentRow + 4;
			$totalPercentageRow = $currentRow + 5;

			$this->multiDayHourlyWorksheet->setCellValue("A{$primaryRow}", "{$this->primaryDirection->GetFullName()}");
			$this->multiDayHourlyWorksheet->setCellValue("A{$secondaryRow}", "{$this->secondaryDirection->GetFullName()}");

			$primaryDrTotal = 0;
			$secondaryDrTotal = 0;
			$total = 0;

			foreach ($this->vehicleClasses as &$vehicleClass)
			{
				$primaryDrClassCount = $this->directionTotals[$this->primaryDr][$vehicleClass];
				$primaryDrTotal += $primaryDrClassCount;

				$secondaryDrClassCount = $this->directionTotals[$this->secondaryDr][$vehicleClass];
				$secondaryDrTotal += $secondaryDrClassCount;

				$totalClassCount = $primaryDrClassCount + $secondaryDrClassCount;
				$total += $totalClassCount;

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryRow}", "$primaryDrClassCount");
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryRow}", "$secondaryDrClassCount");
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalRow}", "$totalClassCount");

				$currentColumn++;
			}

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryRow}", "$primaryDrTotal");

			if ($primaryDrTotal > 0)
			{
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryPercentageRow}", "1");
			}

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryRow}", "$secondaryDrTotal");

			if ($secondaryDrTotal > 0)
			{
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryPercentageRow}", "1");
			}

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalRow}", "$total");

			if ($total > 0)
			{
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalPercentageRow}", "1");
			}

			// Populate the class percentages
			$currentColumn = "B";

			foreach ($this->vehicleClasses as &$vehicleClass)
			{
				$primaryDrClassCount = $this->directionTotals[$this->primaryDr][$vehicleClass];
				$secondaryDrClassCount = $this->directionTotals[$this->secondaryDr][$vehicleClass];
				$totalClassCount = $primaryDrClassCount + $secondaryDrClassCount;

				if ($primaryDrTotal > 0)
				{
					$pct = $primaryDrClassCount / $primaryDrTotal;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryPercentageRow}", "{$pct}");
				}

				if ($secondaryDrTotal > 0)
				{
					$pct = $secondaryDrClassCount / $secondaryDrTotal;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryPercentageRow}", "{$pct}");
				}

				if ($total > 0)
				{
					$pct = $totalClassCount / $total;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalPercentageRow}", "{$pct}");
				}

				$currentColumn++;
			}

			// The location, date range, and site code get duplicated after the vehicle classification legend.
			$currentRow += 17;

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", $this->location);
			$currentRow += 1;

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", "{$startDate} to {$endDate}");
			$currentRow += 1;

			$this->multiDayHourlyWorksheet->setCellValue("B{$currentRow}", $this->siteCode);

			DBG_RETURN(DBGZ_TUBE_CLASSREPORT, __METHOD__);
		}

		private function PopulateAverages(
			$type,
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_CLASSREPORT, __METHOD__, "beginningRow=$beginningRow, type=$type");

			//
			// Calculate the daily totals.
			//
			$directionHourlyTotals = array();
			$numberOfDays = 0;

			foreach ($this->dailyBuckets as $date => &$dailyBucket)
			{
				// Filter the days based on $type.
				if ((($type == 'fullday') && !$dailyBucket['fullday'])
						|| (($type == 'midweek') && !$dailyBucket['midweekday']))
				{
					DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Not including $date in the averaging because it's not $type");
					continue;
				}

				$numberOfDays += 1;

				foreach ($this->hours as &$hour)
				{
					foreach ($this->vehicleClasses as &$vehicleClass)
					{
						if (!isset($directionHourlyTotals[$this->primaryDr][$hour][$vehicleClass]))
						{
							$directionHourlyTotals[$this->primaryDr][$hour][$vehicleClass] = 0;
							$directionHourlyTotals[$this->secondaryDr][$hour][$vehicleClass] = 0;
						}

						$directionHourlyTotals[$this->primaryDr][$hour][$vehicleClass] += $dailyBucket['directions'][$this->primaryDr][$hour][$vehicleClass];
						$directionHourlyTotals[$this->secondaryDr][$hour][$vehicleClass] += $dailyBucket['directions'][$this->secondaryDr][$hour][$vehicleClass];
					}
				}
			}

			// Now populate the averages
			$rowHeights = array(12, 13, 12, 13, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 13, 13, 13);

			$currentRow = $beginningRow;

			foreach ($directionHourlyTotals as $direction => &$directionHourlyTotal)
			{
				for ($i=0; $i<count($rowHeights); $i++)
				{
					$this->multiDayHourlyWorksheet->getRowDimension($currentRow+$i)->setRowHeight($rowHeights[$i]);
				}

				if ($type == 'fullday')
				{
					$templateRange = "B38:P68";
					$header = "Total Study Average";
				}
				else if ($type == 'midweek')
				{
					$templateRange = "B38:P68";
					$header = "3-Day (Tuesday - Thursday) Average";
				}

				$replicateDestinationRow = $currentRow - 1;
				ExcelHelpers::ReplicateRange($this->worksheetTemplates, $templateRange, $this->multiDayHourlyWorksheet, "A{$replicateDestinationRow}", TRUE);
				$this->multiDayHourlyWorksheet->setBreak("A{$replicateDestinationRow}" , \PHPExcel_Worksheet::BREAK_ROW);

				// Write the the header
				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", $header);
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:E{$currentRow}");
				$currentRow += 1;

				// Populate the direction
				$dirObj = new Direction($direction);
				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", $dirObj->GetFullName());
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:E{$currentRow}");
				$currentRow += 1;

				// Merge the header row
				$this->multiDayHourlyWorksheet->mergeCells("B{$currentRow}:N{$currentRow}");

				// Skip over column headers
				$currentRow += 2;

				$totalOfHourlyAverages = array();
				$totalOfAverages = 0;

				foreach ($this->hours as &$hour)
				{
					$currentColumn = "B";

					$totalOfVehicleClassAverages = 0;

					foreach ($this->vehicleClasses as &$vehicleClass)
					{
						$avg = $directionHourlyTotal[$hour][$vehicleClass] / $numberOfDays;

						if (!isset($totalOfHourlyAverages[$vehicleClass]))
						{
							$totalOfHourlyAverages[$vehicleClass] = 0;
						}

						$totalOfHourlyAverages[$vehicleClass] += $avg;
						$totalOfVehicleClassAverages += $avg;
						$totalOfAverages += $avg;

						$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$avg");
						$currentColumn++;
					}

					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$totalOfVehicleClassAverages");

					$currentRow++;
				}

				// Populate the totals and percentages
				$currentColumn = "B";

				foreach ($this->vehicleClasses as &$vehicleClass)
				{
					$hourlyAverage = $totalOfHourlyAverages[$vehicleClass];

					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$hourlyAverage");

					$percentageRow = $currentRow + 1;

					if ($totalOfAverages > 0)
					{
						$pct = $hourlyAverage / $totalOfAverages;
						$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$percentageRow}", "$pct");
					}

					$currentColumn++;
				}

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$totalOfAverages");

				$currentRow += 1;

				// Skip over unused/empty rows to the next direction
				$currentRow += ($type == 'fullday') ? 3 : 2;
			}

			DBG_RETURN(DBGZ_TUBE_CLASSREPORT, __METHOD__);
		}

		private function PopulateMultiDayHourlyWorksheet(
			&$maxRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_CLASSREPORT, __METHOD__);

			// Set row heights and column widths
			$rowHeights = array(17, 17, 17, 17, 12, 12, 12, 12, 12, 12, 12, 13, 12, 13, 13, 12, 14, 12, 14, 12, 15, 12, 12, 13, 12, 12, 12, 12, 12, 12, 12, 13, 12, 12, 12);

			$row = 1;

			foreach ($rowHeights as &$rowHeight)
			{
				$this->multiDayHourlyWorksheet->getRowDimension($row)->setRowHeight($rowHeight);
				$row++;
			}

			$columnWidths = array(
					15.5703125, 7.7109375, 7.7109375, 7.7109375, 7.7109375,
					7.7109375, 7.7109375, 7.7109375, 7.7109375, 7.7109375,
					7.7109375, 7.7109375, 7.7109375, 7.7109375, 7.7109375
					);

			$column = 'A';

			foreach ($columnWidths as &$columnWidth)
			{
				$this->multiDayHourlyWorksheet->getColumnDimension($column)->setWidth($columnWidth);
				$column++;
			}

			$this->PopulateReportData(6);

			// Populate daily stats
			$beginningRow = 37;

      foreach ($this->dailyBuckets as $date => &$dailyBucket)
      {
        foreach ($dailyBucket['directions'] as $direction => &$directionBucket)
        {
          $this->PopulateOneDayOneDir($date, $direction, $directionBucket, $beginningRow);
          $beginningRow += 31;
        }
      }
			
      $this->PopulateAverages('fullday', $beginningRow);
        $beginningRow += 64;

      $this->PopulateAverages('midweek', $beginningRow);

      $maxRow = $beginningRow + 64;

      DBG_RETURN(DBGZ_TUBE_CLASSREPORT, __METHOD__);
    }
      

		public function Create(
			$jobId,
			$jobSiteId,
			$title,
			$ingestionId,
			$filterBeginTime,
			$filterEndTime,
			$startTime,
			$endTime,
			$siteCode,
			$location,
			$primaryDirection,
			$secondaryDirection,
			$outputFolder,
			&$outputFiles,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_TUBE_CLASSREPORT,
					__METHOD__,
					"jobId=$jobId, jobSiteId=$jobSiteId, ingestionId=$ingestionId, startTime=$startTime, endTime=$endTime, siteCode=SsiteCode, primaryDirection=$primaryDirection, secondaryDirection=$secondaryDirection"
					);

			$this->jobId = $jobId;
			$this->jobSiteId = $jobSiteId;
			$this->title = $title;
			$this->ingestionId = $ingestionId;
			$this->filterBeginTime = $filterBeginTime;
			$this->filterEndTime = $filterEndTime;
			$this->startTime = $startTime;
			$this->endTime = $endTime;
			$this->siteCode = $siteCode;
			$this->location = $location;
			$this->primaryDirection = new Direction($primaryDirection);
			$this->secondaryDirection = new Direction($secondaryDirection);
			$this->primaryDr = $this->primaryDirection->GetDr();
			$this->secondaryDr = $this->secondaryDirection->GetDr();

			$outputFiles = array();

			$this->GetDataByDirection();

			if ($this->dataRows == NULL)
			{
				$resultString = "WARNING: No data rows found";
				DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, $resultString);
			}
			else
			{
				DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Found ".count($this->dataRows)." rows.  Tallying results...");

				$this->InitializeBuckets();
        var_dump($this->dailyBuckets);
				// We need to tally the volumes by day and hour intervals
				DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Tallying...");

				foreach ($this->dataRows as &$dataRow)
				{
					$occurred = \DateTime::createFromFormat("Y-m-d H:i:s", $dataRow['occurred']);

					$dayBucket = $occurred->format("Y-m-d");
					$hourBucket = $occurred->format("H");

					$dr = $dataRow['dr'];
					$cl = $dataRow['cl'];

					$this->dailyBuckets[$dayBucket]['directions'][$dr][$hourBucket][$cl] += 1;
					$this->dailyBuckets[$dayBucket]['directions'][$dr]['dailytotal'][$cl] += 1;

					$this->directionTotals[$dr][$cl] += 1;
				}

				// Load the class template and populate the worksheets.
				$this->objPHPExcel = \PHPExcel_IOFactory::load(_IDAX_REPORTS_PATH."/templates/xlsx/Class_Template.xlsx");

				$this->multiDayHourlyWorksheet = new \PHPExcel_Worksheet($this->objPHPExcel, "Class_Multi-Day_Hourly");
				$this->objPHPExcel->addSheet($this->multiDayHourlyWorksheet, 0);

				$this->worksheetTemplates = $this->objPHPExcel->getSheetByName("WorksheetTemplates");

				$this->PopulateMultiDayHourlyWorksheet($maxRow);

				$sheetIndex = $this->objPHPExcel->getIndex($this->worksheetTemplates);
				$this->objPHPExcel->removeSheetByIndex($sheetIndex);

				$properties = $this->objPHPExcel->getProperties();
				$properties->setCreator("IDAX Data Solutions");

				// Save the spreadhsheet as an Excel file on the local machine.
				DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Saving as Excel worksheet...");

				$excelWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
				$excelWriter->save("$outputFolder/{$title}_Class.xlsx");
				$outputFiles["xls"] = "{$title}_Class.xlsx";

				// Now save it as a PDF file on the local machine.
				$rendererName = \PHPExcel_Settings::PDF_RENDERER_IDAXPDF;
				$rendererLibraryPath = '/home/idax/ipdf';

				if (!\PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath))
				{
				    DBG_ERR(DBGZ_TUBE_CLASSREPORT, __METHOD__, "PDF renderer (renderName=$rendererName, renderLibraryPath=$rendererLibraryPath) not found/supported");
				}
				else
				{
					DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Saving as PDF...");

					// Set the margins
					$pageMargins = $this->multiDayHourlyWorksheet->getPageMargins();

					$pageMargins->setTop(0.5);
					$pageMargins->setRight(0.5);
					$pageMargins->setLeft(0.5);
					$pageMargins->setBottom(0.5);

					$pageMargins->setPDFTop(28/25.4);
					$pageMargins->setPDFRight(10/25.4);
					$pageMargins->setPDFLeft(10/25.4);
					$pageMargins->setPDFBottom(17/25.4);

					// Set up HTML headers and footers.  Excel headers and footers aren't supported in PHPExcel, so we
					// made an extension to it to support headers in HTML, which then get rendered in PDF.
					$this->multiDayHourlyWorksheet->setShowGridLines(false);

					$htmlHeaderFooter = $this->multiDayHourlyWorksheet->getHTMLHeaderFooter();

					$startDate = date("m/d/Y", strtotime($startTime));
					$endDate = date("m/d/Y", strtotime($endTime));

					$searchStrings = array("@_IDAX_REPORTS_PATH@", "@reporttype@", "@direction@", "@location@", "@sitecode@", "@startdate@", "@enddate@");
					$replacements = array(_IDAX_REPORTS_PATH, "Class", "$primaryDirection / $secondaryDirection", $location, $siteCode, $startDate, $endDate);

					$htmlHeaderFooter->setDifferentFirst(true);
					$htmlHeaderFooter->setDifferentOddEven(false);

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/html_first_page_header.html")
							);

					$htmlHeaderFooter->setFirstHeader($html);

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/html_page_header.html")
							);

					$htmlHeaderFooter->setOddHeader($html);

					$pdfHeaderFooter = $this->multiDayHourlyWorksheet->getPDFHeaderFooter();

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/pdf_page_header.html")
							);

					$pdfHeaderFooter->setOddHeader($html);

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/pdf_page_footer.html")
							);

					$pdfHeaderFooter->setOddFooter($html);

					$pageSetup = $this->multiDayHourlyWorksheet->getPageSetup();
					$pageSetup->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$pageSetup->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

					// We don't need to print the first 10 row because the HTML header has the same content.
					// And we don't need to print rows 33-35 in the PDF file.  Set the print areas appropriately
					// to exclude these rows.
					$pageSetup->setPrintArea('A11:O32,A36:O'.$maxRow);

					// Resize the column widths and row heights so the tables appear better in the pdf file.
					$row = "11";

					while ($row <= $maxRow)
					{
						$height = $this->multiDayHourlyWorksheet->getRowDimension($row)->getRowHeight();
						$this->multiDayHourlyWorksheet->getRowDimension($row)->setRowHeight(12);
						$row++;
					}

					$pdfWriter = new \PHPExcel_Writer_PDF($this->objPHPExcel);
					$pdfWriter->save("$outputFolder/{$title}_Class.pdf");

					$outputFiles["pdf"] = "{$title}_Class.pdf";
					unset($pdfWriter);
				}
			}

			DBG_RETURN(DBGZ_TUBE_CLASSREPORT, __METHOD__);
			return TRUE;
		}
	}
?>
