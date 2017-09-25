<?php

	namespace Idax\Tube\Reports\Classes;

	require_once '/home/idax/idax.php';
	require_once '/home/idax/PHPExcel/Classes/PHPExcel.php';
 
	use Idax\Common\Classes\Direction;
	use Idax\Common\Classes\ExcelHelpers;
	use Idax\Tube\Data\IngestionDataRow;

	class VolumeReport
	{
		private $context = NULL;
		private $dataRows = NULL;
		private $objPHPExcel = NULL;
		private $multiDayHourlyWorksheet = NULL;
		private $oneDay1hourWorksheet = NULL;
		private $oneDay15MinutesWorksheet = NULL;
		private $worksheetTemplates = NULL;
		private $jobId = NULL;
		private $jobSiteId = NULL;
		private $siteCode = NULL;
		private $ingestionId = NULL;
		private $reportFormat = NULL;
		private $title = NULL;
		private $location = NULL;
		private $filterBeginTime = NULL;
		private $filterEndTime = NULL;
		private $reportStartTime = NULL;
		private $reportEndTime = NULL;
		private $studyStartTime = NULL;
		private $studyEndTime = NULL;
		private $primaryDirection = NULL;
		private $secondaryDirection = NULL;
		private $primaryDr = NULL;
		private $secondaryDr = NULL;
		private $dailyBuckets = NULL;
		private $hourlyBuckets = NULL;
		private $quarterHourBuckets = NULL;
		private $directionTotals = NULL;
		private $numMidWeekDays = NULL;
		private $midWeekTotals = NULL;
		private $stationId = NULL;                    // Redmond Use
		private $specificLocation = NULL;             // Redmond Use
		private $speedLimit = NULL;                   // Redmond Use

		private $hours = array(
				"00", "01", "02", "03", "04", "05",
				"06", "07", "08", "09", "10", "11",
				"12", "13", "14", "15", "16", "17",
				"18", "19", "20", "21", "22", "23"
				);

		private $fifteenminutes = array("00", "15", "30", "45");


		public function __construct(
			$context
			)
		{
			$this->context = $context;

			$this->dailyBuckets = array();

			// Create the buckets
			$this->hourlyBuckets = array();
			$this->quarterHourBuckets = array();
			$this->midWeekHourlyBuckets = array();
			$this->numMidWeekDays = 0;
		}

		private function InitializeBuckets()
		{
			// Day bucket - keep 'YYYY-MM-DD' - 10 characters
			$time = strtotime($this->reportStartTime);
			$endTime = strtotime($this->reportEndTime);

			$dayBucket = date("Y-m-d", $time);
			$dayOfWeek = date("w", $time);

			while ($time <= $endTime)
			{
				$this->dailyBuckets[$dayBucket][$this->primaryDr] = 0;
				$this->dailyBuckets[$dayBucket][$this->secondaryDr] = 0;

				foreach ($this->hours as &$hour)
				{
					$hourBucket = "$dayBucket $hour:00";
					$this->hourlyBuckets[$hourBucket][$this->primaryDr] = 0;
					$this->hourlyBuckets[$hourBucket][$this->secondaryDr] = 0;

					foreach ($this->fifteenminutes as &$quarter)
					{
						$quarterBucket = $dayBucket." $hour:".$quarter;
						$this->quarterHourBuckets[$quarterBucket][$this->primaryDr] = 0;
						$this->quarterHourBuckets[$quarterBucket][$this->secondaryDr] = 0;
					}
				}

				if (($dayOfWeek >= 2) && ($dayOfWeek <= 4))
				{
					$this->numMidWeekDays += 1;
				}

				$time += 24 * 60 * 60;
				$dayBucket = date("Y-m-d", $time);
				$dayOfWeek = date("w", $time);
			}

			foreach ($this->hours as &$hour)
			{
				$hourBucket = "$hour:00";
				$this->midWeekHourlyBuckets[$hourBucket][$this->primaryDr] = 0;
				$this->midWeekHourlyBuckets[$hourBucket][$this->secondaryDr] = 0;
			}

			$this->directionTotals[$this->primaryDr] = 0;
			$this->directionTotals[$this->secondaryDr] = 0;
			$this->midWeekTotals[$this->primaryDr] = 0;
			$this->midWeekTotals[$this->secondaryDr] = 0;
		}

		private function GetDataByDirection()
		{
			$filter = array(
					"jobid='$this->jobId'",
					"jobsiteid='$this->jobSiteId'",
					"occurred>='$this->studyStartTime'",
					"occurred<='$this->studyEndTime'"
					);

			if ($this->ingestionId != 0)
			{
				$filter[] = "ingestionid='$this->ingestionId'";
			}

			$this->dataRows = IngestionDataRow::Find(
					$this->context->dbcon,
					array("ds", "occurred", "dr", "cl"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE
					);
		}

		private function PopulateMultiDayHourlyWorksheet(
			&$maxRow,
			&$maxColumn
			)
		{
			DBG_ENTER(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);

			// Set row heights and column widths
			/*$rowHeights = array(
					12.00, 12.00, 12.00, 12.00, 12.00, 19.50, 19.50,
					19.50, 19.50, 18.00, 18.00, 18.00, 18.00, 18.00,
					18.00, 18.00, 18.00, 18.00, 18.00, 18.00, 18.00,
					18.00, 18.00, 18.00, 18.00, 18.00, 18.00, 18.00,
					18.00, 18.00, 18.00, 18.00, 18.00, 13.50, 13.50, 13.50
					);*/

			$rowHeights = array(
					12, 12, 17, 17, 12, 13, 12, 12, 13, 12, 12, 12,
					13, 12, 12, 12, 12, 13, 12, 12, 12, 12, 12, 12,
					12, 12, 12, 12, 12, 12, 12, 12, 13, 12, 13, 12
					);
					
			$row = 1;

			foreach ($rowHeights as &$rowHeight)
			{
				$this->multiDayHourlyWorksheet->getRowDimension($row)->setRowHeight($rowHeight);
				$row++;
			}

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, "G43:AB78", $this->multiDayHourlyWorksheet, "A1", TRUE);

			// Set the column width of first colum
			$this->multiDayHourlyWorksheet->getColumnDimension("A")->setWidth(11.7109375);

			// Add report header info (location, date range, and site code)
			$studyStartTime = strtotime($this->studyStartTime);
			$studyStartDate = date("m/d/Y", $studyStartTime);

			$studyEndTime = strtotime($this->studyEndTime);
			$studyEndDate = date("m/d/Y", $studyEndTime);

			$this->multiDayHourlyWorksheet->setCellValue("C2", $this->location);
			//$this->multiDayHourlyWorksheet->mergeCells("B2:G2");
			//$this->multiDayHourlyWorksheet->getStyle('B3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$this->multiDayHourlyWorksheet->setCellValue("C3", "$studyStartDate - $studyEndDate");
			//$this->multiDayHourlyWorksheet->mergeCells("B3:G3");
			//$this->multiDayHourlyWorksheet->getStyle('B3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$this->multiDayHourlyWorksheet->setCellValue("C4", "$this->siteCode");
			//$this->multiDayHourlyWorksheet->mergeCells("B4:G4");
			//$this->multiDayHourlyWorksheet->getStyle('B4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$dayColumn = 'A';

			$currentDayOfWeek = "";
			$previousDayOfWeek = "";

			foreach ($this->hourlyBuckets as $key => &$hourlyBucket)
			{
				$time = strtotime($key);
				$currentDayOfWeek = date("l", $time);
				$currentDate = date("m/d/y", $time);
				$dayBucket = date("Y-m-d", $time);
				$currentHour = date("h:i A", $time);

				if ($currentDayOfWeek != $previousDayOfWeek)
				{
					$dayColumn++;

					ExcelHelpers::ReplicateRange($this->worksheetTemplates, "B4:D32", $this->multiDayHourlyWorksheet, "{$dayColumn}7", TRUE);

					$this->multiDayHourlyWorksheet->setCellValue("{$dayColumn}7", $currentDayOfWeek);
					$this->multiDayHourlyWorksheet->setCellValue("{$dayColumn}8", $currentDate);

					$pColumn = $dayColumn;
					$sColumn = ++$dayColumn;
					$tColumn = ++$dayColumn;

					// Set column widths
					$this->multiDayHourlyWorksheet->getColumnDimension($pColumn)->setWidth(5.7109375);
					$this->multiDayHourlyWorksheet->getColumnDimension($sColumn)->setWidth(5.7109375);
					$this->multiDayHourlyWorksheet->getColumnDimension($tColumn)->setWidth(5.7109375);

					// Merge the header rows
					$this->multiDayHourlyWorksheet->mergeCells("{$pColumn}7:{$tColumn}7");
					$this->multiDayHourlyWorksheet->mergeCells("{$pColumn}8:{$tColumn}8");

					$currentDataRow = 9;

					$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->primaryDirection->GetAbbreviation());
					$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->secondaryDirection->GetAbbreviation());

					$currentDataRow += 1;

					$previousDayOfWeek = $currentDayOfWeek;
				}

				if (strtotime($dayBucket) > strtotime($this->studyEndTime))
				{
					continue;
				}

				$primaryDrHourlyTotal = $hourlyBucket[$this->primaryDr];
				$secondaryDrHourlyTotal = $hourlyBucket[$this->secondaryDr];

				$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $primaryDrHourlyTotal);
				$this->multiDayHourlyWorksheet->getStyle("{$pColumn}{$currentDataRow}")->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_BLACK);

				$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $secondaryDrHourlyTotal);
				$this->multiDayHourlyWorksheet->getStyle("{$sColumn}{$currentDataRow}")->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_BLACK);

				$this->multiDayHourlyWorksheet->setCellValue("{$tColumn}{$currentDataRow}", $primaryDrHourlyTotal + $secondaryDrHourlyTotal);
				$this->multiDayHourlyWorksheet->getStyle("{$tColumn}{$currentDataRow}")->getFont()->getColor()->setARGB(\PHPExcel_Style_Color::COLOR_BLACK);

				$currentDataRow += 1;

				if ($currentHour == "11:00 PM")
				{
					// 11:00PM (-12:00AM) is the last hour of the day.  Put daily totals (by direction) and
					// percentages (by direction).
					$dayTotal = $this->dailyBuckets[$dayBucket][$this->primaryDr] + $this->dailyBuckets[$dayBucket][$this->secondaryDr];

					$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->dailyBuckets[$dayBucket][$this->primaryDr]);
					$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->dailyBuckets[$dayBucket][$this->secondaryDr]);
					$this->multiDayHourlyWorksheet->setCellValue("{$tColumn}{$currentDataRow}", $dayTotal);

					$currentDataRow += 1;

					if ($dayTotal > 0)
					{
						$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->dailyBuckets[$dayBucket][$this->primaryDr] / $dayTotal);
						$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->dailyBuckets[$dayBucket][$this->secondaryDr] / $dayTotal);
					}
				}
			}

			// Add the midweek hourly totals and averages
			$dayColumn++;

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, "F1:H32", $this->multiDayHourlyWorksheet, "{$dayColumn}4", TRUE);

			$pColumn = $dayColumn;
			$sColumn = ++$dayColumn;
			$tColumn = ++$dayColumn;

			// Set column widths
			$this->multiDayHourlyWorksheet->getColumnDimension($pColumn)->setWidth(5.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($sColumn)->setWidth(5.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($tColumn)->setWidth(5.7109375);

			// Merge the header rows
			$this->multiDayHourlyWorksheet->mergeCells("{$pColumn}7:{$tColumn}8");

			if ($this->numMidWeekDays > 0)
			{
				$currentDataRow = 9;

				$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->primaryDirection->GetAbbreviation());
				$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->secondaryDirection->GetAbbreviation());

				$currentDataRow += 1;

				foreach ($this->hours as &$hour)
				{
					$hourBucket = $hour.":00";

					$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->midWeekHourlyBuckets[$hourBucket][$this->primaryDr] / $this->numMidWeekDays);
					$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->midWeekHourlyBuckets[$hourBucket][$this->secondaryDr] / $this->numMidWeekDays);
					$this->multiDayHourlyWorksheet->setCellValue("{$tColumn}{$currentDataRow}", ($this->midWeekHourlyBuckets[$hourBucket][$this->primaryDr] + $this->midWeekHourlyBuckets[$hourBucket][$this->secondaryDr]) / $this->numMidWeekDays);

					$currentDataRow += 1;
				}

				$midWeekTotal = $this->midWeekTotals[$this->primaryDr] + $this->midWeekTotals[$this->secondaryDr];

				$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->midWeekTotals[$this->primaryDr] / $this->numMidWeekDays);
				$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->midWeekTotals[$this->secondaryDr] / $this->numMidWeekDays);
				$this->multiDayHourlyWorksheet->setCellValue("{$tColumn}{$currentDataRow}", ($this->midWeekTotals[$this->primaryDr] + $this->midWeekTotals[$this->secondaryDr]) / $this->numMidWeekDays);

				$currentDataRow += 1;

				if ($midWeekTotal > 0)
				{
					$this->multiDayHourlyWorksheet->setCellValue("{$pColumn}{$currentDataRow}", $this->midWeekTotals[$this->primaryDr] / $midWeekTotal);
					$this->multiDayHourlyWorksheet->setCellValue("{$sColumn}{$currentDataRow}", $this->midWeekTotals[$this->secondaryDr] / $midWeekTotal);
				}
			}

			$footNoteRow = $currentDataRow + 1;
			$this->multiDayHourlyWorksheet->mergeCells("A{$footNoteRow}:M{$footNoteRow}");

			$maxRow = $footNoteRow;
			$maxColumn = $dayColumn;

			// Add the daily averages
			$dayColumn++;

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, "J4:P21", $this->multiDayHourlyWorksheet, "{$dayColumn}1", TRUE);

			$col1 = $dayColumn;
			$col2 = ++$dayColumn;
			$col3 = ++$dayColumn;
			$col4 = ++$dayColumn;
			$col5 = ++$dayColumn;
			$col6 = ++$dayColumn;
			$col7 = ++$dayColumn;

			$this->multiDayHourlyWorksheet->getColumnDimension($col1)->setWidth(16.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($col2)->setWidth(12.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($col3)->setWidth(12.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($col4)->setWidth(12.7109375);
			$this->multiDayHourlyWorksheet->getColumnDimension($col5)->setWidth(9.140625);
			$this->multiDayHourlyWorksheet->getColumnDimension($col6)->setWidth(9.140625);
			$this->multiDayHourlyWorksheet->getColumnDimension($col7)->setWidth(9.140625);

			// Merge the header rows
			$this->multiDayHourlyWorksheet->mergeCells("{$col1}1:{$col7}2");

			// Add report header info (location, date range, direction, and site code)
			$this->multiDayHourlyWorksheet->setCellValue("{$col2}6", $this->location);
			$this->multiDayHourlyWorksheet->mergeCells("{$col2}6:{$col4}6");

			$this->multiDayHourlyWorksheet->setCellValue("{$col2}7", "{$this->primaryDirection->GetFullName()} / {$this->secondaryDirection->GetFullName()}");
			$this->multiDayHourlyWorksheet->mergeCells("{$col2}7:{$col4}7");

			$this->multiDayHourlyWorksheet->setCellValue("{$col2}8", "$studyStartDate - $studyEndDate");
			$this->multiDayHourlyWorksheet->mergeCells("{$col2}8:{$col4}8");

			$this->multiDayHourlyWorksheet->setCellValue("{$col2}9", "$this->siteCode");
			$this->multiDayHourlyWorksheet->mergeCells("{$col2}9:{$col4}9");

			$this->multiDayHourlyWorksheet->setCellValue("{$col2}14", $this->primaryDirection->GetAbbreviation());
			$this->multiDayHourlyWorksheet->setCellValue("{$col3}14", $this->secondaryDirection->GetAbbreviation());

			$this->multiDayHourlyWorksheet->setCellValue("{$col2}15", $this->directionTotals[$this->primaryDr] / count($this->dailyBuckets));
			$this->multiDayHourlyWorksheet->setCellValue("{$col3}15", $this->directionTotals[$this->secondaryDr] / count($this->dailyBuckets));
			$this->multiDayHourlyWorksheet->setCellValue("{$col4}15", ($this->directionTotals[$this->primaryDr] + $this->directionTotals[$this->secondaryDr]) / count($this->dailyBuckets));

			// Add the mid-week averages
			if ($this->numMidWeekDays > 0)
			{
				$this->multiDayHourlyWorksheet->setCellValue("{$col2}16", $this->midWeekTotals[$this->primaryDr] / $this->numMidWeekDays);
				$this->multiDayHourlyWorksheet->setCellValue("{$col3}16", $this->midWeekTotals[$this->secondaryDr] / $this->numMidWeekDays);
				$this->multiDayHourlyWorksheet->setCellValue("{$col4}16", ($this->midWeekTotals[$this->primaryDr] + $this->midWeekTotals[$this->secondaryDr]) / $this->numMidWeekDays);
			}

			DBG_RETURN(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);
		}

		private function PopulateOneDayByHour(
			$date,
			$dayBucket,
			$startingRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);

			// Set the column widths
			$columnWidths = array(30.42578125, 22.7109375, 22.7109375, 22.7109375, 9.140625);

			$column = "A";

			foreach ($columnWidths as &$columnWidth)
			{
				$this->oneDay1hourWorksheet->getColumnDimension($column)->setWidth($columnWidth);
				$column++;
			}

			$currentRow = $startingRow;

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, "B43:E78", $this->oneDay1hourWorksheet, "A{$currentRow}", TRUE);

			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->location);
			$currentRow++;

			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $date);
			$currentRow++;

			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->siteCode);
			$currentRow += 2;

			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->primaryDirection->GetAbbreviation());
			$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->secondaryDirection->GetAbbreviation());
			$currentRow++;

			$peakHourData = array();

			$peakHourData['AM']['count'] = -1;
			$peakHourData['AM']['bucket'] = "";
			$peakHourData['AM']['row'] = 0;
			$peakHourData['AM'][$this->primaryDr]['count'] = -1;
			$peakHourData['AM'][$this->primaryDr]['cell'] = NULL;
			$peakHourData['AM'][$this->secondaryDr]['count'] = -1;
			$peakHourData['AM'][$this->secondaryDr]['cell'] = NULL;

			$peakHourData['PM']['count'] = -1;
			$peakHourData['PM']['bucket'] = "";
			$peakHourData['PM']['row'] = 0;
			$peakHourData['PM'][$this->primaryDr]['count'] = -1;
			$peakHourData['PM'][$this->primaryDr]['cell'] = NULL;
			$peakHourData['PM'][$this->secondaryDr]['count'] = -1;
			$peakHourData['PM'][$this->secondaryDr]['cell'] = NULL;

			foreach ($this->hours as $hour)
			{
				$hourBucketKey = "$date $hour:00";

				$hourlyTotal = $this->hourlyBuckets[$hourBucketKey][$this->primaryDr] + $this->hourlyBuckets[$hourBucketKey][$this->secondaryDr];

				if (intval($hour) < 12)
				{
					$ampm = 'AM';
				}
				else
				{
					$ampm = 'PM';
				}

				if ($this->hourlyBuckets[$hourBucketKey][$this->primaryDr] > $peakHourData[$ampm][$this->primaryDr]['count'])
				{
					$peakHourData[$ampm][$this->primaryDr]['count'] = $this->hourlyBuckets[$hourBucketKey][$this->primaryDr];
					$peakHourData[$ampm][$this->primaryDr]['cell'] = "B{$currentRow}";
				}

				if ($this->hourlyBuckets[$hourBucketKey][$this->secondaryDr] > $peakHourData[$ampm][$this->secondaryDr]['count'])
				{
					$peakHourData[$ampm][$this->secondaryDr]['count'] = $this->hourlyBuckets[$hourBucketKey][$this->secondaryDr];
					$peakHourData[$ampm][$this->secondaryDr]['cell'] = "C{$currentRow}";
				}

				if ($hourlyTotal > $peakHourData[$ampm]['count'])
				{
					$peakHourData[$ampm]['count'] = $hourlyTotal;
					$peakHourData[$ampm]['bucket'] = $hourBucketKey;
					$peakHourData[$ampm]['row'] = $currentRow;
				}

				$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->hourlyBuckets[$hourBucketKey][$this->primaryDr]);
				$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->hourlyBuckets[$hourBucketKey][$this->secondaryDr]);
				$this->oneDay1hourWorksheet->setCellValue("D{$currentRow}", $hourlyTotal);

				$currentRow++;
			}

			// Add the daily totals
			$dailyTotal = $dayBucket[$this->primaryDr] + $dayBucket[$this->secondaryDr];

			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $dayBucket[$this->primaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $dayBucket[$this->secondaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("D{$currentRow}", $dailyTotal);

			$currentRow++;

			// Add the percentages by direction
			if ($dailyTotal > 0)
			{
				$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $dayBucket[$this->primaryDr] / $dailyTotal);
				$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $dayBucket[$this->secondaryDr] / $dailyTotal);
			}

			$currentRow += 2;

			// Add the AM peak hour info
			$styleArray = array('font' => array('bold' => true));

			$displayDate = \DateTime::createFromFormat("Y-m-d H:i", $peakHourData['AM']['bucket']);
			$timeRangeLow = $displayDate->format("h:i A");

			$displayDate->add(new \DateInterval("PT1H"));
			$timeRangeHigh = $displayDate->format("h:i A");

			$this->oneDay1hourWorksheet->setCellValue("A{$currentRow}", "AM Peak Hour ($timeRangeLow - $timeRangeHigh)");
			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->primaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->secondaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("D{$currentRow}", $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->primaryDr] + $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->secondaryDr]);

			$currentRow++;

			if ($peakHourData['AM']['count'] > 0)
			{
				$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->primaryDr] / $peakHourData['AM']['count']);
				$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->hourlyBuckets[$peakHourData['AM']['bucket']][$this->secondaryDr] / $peakHourData['AM']['count']);

				$peakRow = $peakHourData['AM']['row'];
				$this->oneDay1hourWorksheet->getStyle($peakHourData['AM'][$this->primaryDr]['cell'])->applyFromArray($styleArray);
				$this->oneDay1hourWorksheet->getStyle($peakHourData['AM'][$this->secondaryDr]['cell'])->applyFromArray($styleArray);
				$this->oneDay1hourWorksheet->getStyle("D{$peakRow}")->applyFromArray($styleArray);
			}

			$currentRow++;

			// Add the PM peak hour info
			$displayDate = \DateTime::createFromFormat("Y-m-d H:i", $peakHourData['PM']['bucket']);
			$timeRangeLow = $displayDate->format("h:i A");

			$displayDate->add(new \DateInterval("PT1H"));
			$timeRangeHigh = $displayDate->format("h:i A");

			$this->oneDay1hourWorksheet->setCellValue("A{$currentRow}", "PM Peak Hour ($timeRangeLow - $timeRangeHigh)");
			$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->primaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->secondaryDr]);
			$this->oneDay1hourWorksheet->setCellValue("D{$currentRow}", $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->primaryDr] + $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->secondaryDr]);

			$currentRow++;

			if ($peakHourData['PM']['count'] > 0)
			{
				$this->oneDay1hourWorksheet->setCellValue("B{$currentRow}", $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->primaryDr] / $peakHourData['PM']['count']);
				$this->oneDay1hourWorksheet->setCellValue("C{$currentRow}", $this->hourlyBuckets[$peakHourData['PM']['bucket']][$this->secondaryDr] / $peakHourData['PM']['count']);

				$peakRow = $peakHourData['PM']['row'];
				$this->oneDay1hourWorksheet->getStyle($peakHourData['PM'][$this->primaryDr]['cell'])->applyFromArray($styleArray);
				$this->oneDay1hourWorksheet->getStyle($peakHourData['PM'][$this->secondaryDr]['cell'])->applyFromArray($styleArray);
				$this->oneDay1hourWorksheet->getStyle("D{$peakRow}")->applyFromArray($styleArray);
			}

			DBG_RETURN(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);
		}

		private function PopulateOneDayByHourWorksheet()
		{
			DBG_ENTER(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);

			$startingRow = 2;

			foreach ($this->dailyBuckets as $key => &$dailyBucket)
			{
				$this->PopulateOneDayByHour($key, $dailyBucket, $startingRow);
				$startingRow += 39;
			}

			DBG_RETURN(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);
		}

		private function PopulateQuarterHourWorksheet()
		{
			DBG_ENTER(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);

			// Set the column widths
			$columnWidths = array(24.140625, 12.140625, 22.7109375, 22.7109375, 9.140625);

			$column = "A";

			foreach ($columnWidths as &$columnWidth)
			{
				$this->oneDay15MinutesWorksheet->getColumnDimension($column)->setWidth($columnWidth);
				$column++;
			}

			$row = 1;

			$this->oneDay15MinutesWorksheet->setCellValue("C{$row}", $this->primaryDirection->GetAbbreviation());
			$this->oneDay15MinutesWorksheet->setCellValue("D{$row}", $this->secondaryDirection->GetAbbreviation());

			$row++;

			foreach ($this->quarterHourBuckets as $key => &$quarterlyBucket)
			{
				$dateTime = \DateTime::createFromFormat("Y-m-d H:i", $key);

				$date = $dateTime->format("m/d/y");
				$quarterHour = $dateTime->format("h:i A");

				$this->oneDay15MinutesWorksheet->setCellValue("A{$row}", $date);
				$this->oneDay15MinutesWorksheet->setCellValue("B{$row}", $quarterHour);

				$this->oneDay15MinutesWorksheet->setCellValue("C{$row}", $quarterlyBucket[$this->primaryDr]);
				$this->oneDay15MinutesWorksheet->setCellValue("D{$row}", $quarterlyBucket[$this->secondaryDr]);

				$row++;
			}

			DBG_RETURN(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);
		}

		public function Create(
			$jobId,
			$jobSiteId,
			$title,
			$ingestionId,
			$reportFormat,
			$filterBeginTime,
			$filterEndTime,
			$studyStartTime,
			$studyEndTime,
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
					DBGZ_TUBE_VOLUMEREPORT,
					__METHOD__,
					"jobId=$jobId, jobSiteId=$jobSiteId, ingestionId=$ingestionId, reportFormat=$reportFormat, studyStartTime=$studyStartTime, studyEndTime=$studyEndTime, siteCode=SsiteCode, primaryDirection=$primaryDirection, secondaryDirection=$secondaryDirection"
					);

			$this->jobId = $jobId;
			$this->jobSiteId = $jobSiteId;
			$this->title = $title;
			$this->ingestionId = $ingestionId;
			$this->reportFormat = $reportFormat;
			$this->filterBeginTime = $filterBeginTime;
			$this->filterEndTime = $filterEndTime;
			$this->studyStartTime = $studyStartTime;
			$this->studyEndTime = $studyEndTime;
			$this->siteCode = $siteCode;
			$this->location = $location;
			$this->primaryDirection = new Direction($primaryDirection);
			$this->secondaryDirection = new Direction($secondaryDirection);
			$this->primaryDr = $this->primaryDirection->GetDr();
			$this->secondaryDr = $this->secondaryDirection->GetDr();

      //++++++++++++++++ quary need confirm by Mike +++++++++++++++++
      //
      //$jobSite = JobSite::FindOne(
      //  $this->context->dbcon,
      //  $jobSiteId,
      //  "ingestionid='$this->ingestionId'",
      //  NULL,
      //  $sqlError
      //);
      //
      //$reportParameters = explode(";", $jobSite['report parameters']);
      //foreach ($reportParameters as &$reportParameter)
      //{
      //  $keyValue = explode("=", $reportParameter);
      //  switch ($keyValue[0])
      //  {
      //    case "stationid":
      //      $stationId = $keyValue[1];
      //      break;
      //    case "specificlocation":
      //      $specificLocation = $keyValue[1];
      //      break;
      //    case "speedlimit":
      //      $speedLimit = $keyValue[1];
      //      break;
      //  }
      //}
      //
      //++++++++++++++++ quary need confirm by Mike +++++++++++++++++
      
      //temporary solution:
      $this->stationId = "10";
      $this->specificLocation = "50 Feet E/O W MERCER WAY";
      $this->speedLimit = "30";
      
			$outputFiles = array();

			$this->GetDataByDirection();

			if ($this->dataRows == NULL)
			{
				$resultString = "WARNING: No data rows found";
				DBG_INFO(DBGZ_TUBE_VOLUMEREPORT, __METHOD__, $resultString);
			}
			else
			{
				// The report will contain 7 days.  Adjust the endTime as needed.
				$numberOfDaysInStudy = (strtotime($studyEndTime) - strtotime($studyStartTime)) / 86400;

				$this->reportStartTime = $studyStartTime;

				if ($numberOfDaysInStudy < 7)
				{
					$this->reportEndTime = date("Y-m-d H:i:s", strtotime($studyStartTime) + (86400 * 7) - 1);
				}
				else
				{
					$this->reportEndTime = $studyEndTime;
				}

				DBG_INFO(DBGZ_TUBE_VOLUMEREPORT, __METHOD__, "numberOfDaysInStudy=$numberOfDaysInStudy, reportStartTime=$this->reportStartTime, reportEndTime=$this->reportEndTime, found ".count($this->dataRows)." rows.  Tallying results...");

				$this->InitializeBuckets();

				// We need to tally the volumes by day, hour, and quarter hour intervals
				// We also need a daily average and midweek (Tue-Thu) average.
				// And we need hourly averages (per day and midweek).

				foreach ($this->dataRows as &$dataRow)
				{
					$occurred = \DateTime::createFromFormat("Y-m-d H:i:s", $dataRow['occurred']);

					$dayBucket = $occurred->format("Y-m-d");

					$hourBucket = $occurred->format("Y-m-d H").":00";

					// Quarter hour bucket
					$minutes = $occurred->format("i");
					$quarterHourBucket = $occurred->format("Y-m-d H").':'.$this->fifteenminutes[intval($minutes/15)];

					$dayOfWeek = intval($occurred->format("w"));

					$dr = $dataRow['dr'];

					$this->dailyBuckets[$dayBucket][$dr] += 1;
					$this->hourlyBuckets[$hourBucket][$dr] += 1;
					$this->quarterHourBuckets[$quarterHourBucket][$dr] += 1;

					$this->directionTotals[$dr] += 1;

					if (($dayOfWeek >= 2) && ($dayOfWeek <= 4))
					{
						$this->midWeekTotals[$dr] += 1;
						$this->midWeekHourlyBuckets[$occurred->format("H").":00"][$dr] += 1;
					}
				}

				// Load the volume template and populate each of the worksheets.
				$this->objPHPExcel = \PHPExcel_IOFactory::load(_IDAX_REPORTS_PATH."/templates/xlsx/Volume_Template.xlsx");

				$this->worksheetTemplates = $this->objPHPExcel->getSheetByName("WorksheetTemplates");

				$this->multiDayHourlyWorksheet = new \PHPExcel_Worksheet($this->objPHPExcel, "Vol_Multi-Day_Hourly");
				$this->objPHPExcel->addSheet($this->multiDayHourlyWorksheet, 0);

				$this->oneDay1hourWorksheet = new \PHPExcel_Worksheet($this->objPHPExcel, "Vol_One Day_1hr");
				$this->objPHPExcel->addSheet($this->oneDay1hourWorksheet, 1);

				$this->oneDay15MinutesWorksheet = new \PHPExcel_Worksheet($this->objPHPExcel, "Vol_One Day_15min");
				$this->objPHPExcel->addSheet($this->oneDay15MinutesWorksheet, 2);

				$this->PopulateMultiDayHourlyWorksheet($maxRow, $maxColumn);
				$this->PopulateOneDayByHourWorksheet();
				$this->PopulateQuarterHourWorksheet();

				$sheetIndex = $this->objPHPExcel->getIndex($this->worksheetTemplates);
				$this->objPHPExcel->removeSheetByIndex($sheetIndex);

				$properties = $this->objPHPExcel->getProperties();
				$properties->setCreator("IDAX Data Solutions");

				DBG_INFO(DBGZ_TUBE_CLASSREPORT, __METHOD__, "Saving as Excel worksheet...");

				$excelWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
				$excelWriter->save("$outputFolder/{$title}_Volume.xlsx");
				$outputFiles["xls"] = "{$title}_Volume.xlsx";

				// Now save it as a PDF file on the local machine.
				$rendererName = \PHPExcel_Settings::PDF_RENDERER_IDAXPDF;
				$rendererLibraryPath = '/home/idax/ipdf';

				if (!\PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath))
				{
				    DBG_ERR(DBGZ_TUBE_VOLUMEREPORT, __METHOD__, "PDF renderer (renderName=$rendererName, renderLibraryPath=$rendererLibraryPath) not found/supported");
				}
				else
				{
					DBG_INFO(DBGZ_TUBE_VOLUMEREPORT, __METHOD__, "Saving as PDF...");

					$this->multiDayHourlyWorksheet->setShowGridLines(false);

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
					$htmlHeaderFooter = $this->multiDayHourlyWorksheet->getHTMLHeaderFooter();

					$studyStartDate = date("m/d/Y", strtotime($studyStartTime));
					$studyEndDate = date("m/d/Y", strtotime($studyEndTime));

					$searchStrings = array("@_IDAX_REPORTS_PATH@", "@reporttype@", "@direction@", "@location@", "@sitecode@", "@startdate@", "@enddate@");
					$replacements = array(_IDAX_REPORTS_PATH, "Volume", "$primaryDirection / $secondaryDirection", $location, $siteCode, $studyStartDate, $studyEndDate);

					$htmlHeaderFooter->setDifferentFirst(true);
					$htmlHeaderFooter->setDifferentOddEven(false);

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/html_page_header.html")
							);

					$htmlHeaderFooter->setFirstHeader($html);

					$pdfHeaderFooter = $this->multiDayHourlyWorksheet->getPDFHeaderFooter();

					$html = str_replace(
							$searchStrings,
							$replacements,
							file_get_contents(_IDAX_REPORTS_PATH."/templates/html/pdf_volume_page_header.html")
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

					// Set the print area to exclude the first 4 rows.  They aren't necessary because the HTML header has the same content.
					$pageSetup->setPrintArea('A5:'.$maxColumn.$maxRow);

					// Save the spreadhsheet as a PDF file on the local machine.
					$pdfWriter = new \PHPExcel_Writer_PDF($this->objPHPExcel);
					$pdfWriter->save("$outputFolder/{$title}_Volume.pdf");

					$outputFiles["pdf"] = "{$title}_Volume.pdf";
					unset($pdfWriter);
				}
			}

			DBG_RETURN(DBGZ_TUBE_VOLUMEREPORT, __METHOD__);
			return TRUE;
		}
	}
?>
