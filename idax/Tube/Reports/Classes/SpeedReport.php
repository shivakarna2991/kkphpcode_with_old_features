<?php

	namespace Idax\Tube\Reports\Classes;

	require_once '/home/idax/idax.php';

	use \Idax\Common\Classes\Direction;
	use \Idax\Common\Classes\ExcelHelpers;
	use \Idax\Tube\Data\IngestionDataRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Common\Data\JobRow;

	require_once '/home/idax/PHPExcel/Classes/PHPExcel.php';
 
	class SpeedReport
	{
		private $context = NULL;
		private $dataRows = NULL;
		private $objPHPExcel = NULL;
		private $multiDayHourlyWorksheet = NULL;
		private $worksheetTemplates = NULL;
		private $jobSiteId = NULL;
		private $siteCode = NULL;
		private $title = NULL;
		private $ingestionId = NULL;
		private $reportFormat = NULL;
		private $location = NULL;
		private $filterBeginTime = NULL;
		private $filterEndTime = NULL;
		private $startTime = NULL;
		private $endTime = NULL;
		private $primaryDirection = NULL;
		private $secondaryDirection = NULL;
		private $allDirection = NULL;                   // Redmond Use
		private $primaryDr = NULL;
		private $secondaryDr = NULL;
		private $allDr = NULL;                          // Redmond Use
		private $dailyBuckets = NULL;
		private $directionalBuckets = NULL;
		private $stationId = NULL;                      // Redmond Use
		private $specificLocation = NULL;               // Redmond Use
		private $postSpeedLimit = NULL;                 // Redmond Use
		private $templateRangeMultiDayHourly = NULL;
		private $templateRangeOneDayOneDir = NULL;
		private $templateRangeAverageOneDir = NULL;
		private $templateRangeOneDaySpeedStat = NULL;
		private $templateRangeTotalStudySpeedStat = NULL;

		private $hours = array(
				"00", "01", "02", "03", "04", "05",
				"06", "07", "08", "09", "10", "11",
				"12", "13", "14", "15", "16", "17",
				"18", "19", "20", "21", "22", "23"
				);
				
		private $mergeRangesForPdf = array();

		private $speedRanges = array(
				"0 - 10",    // 0   0-10
				"10 - 15",   // 1   10-15
				"15 - 20",   // 2   15-20
				"20 - 25",   // 3   20-25
				"25 - 30",   // 4   25-30
				"30 - 35",   // 5   30-35
				"35 - 40",   // 6   35-40
				"40 - 45",   // 7   40-45
				"45 - 50",   // 8   45-50
				"50 - 55",   // 9   50-55
				"55 - 60",   // 10  55-60
				"60 - 65",   // 11  60-65
				"65 - 70",   // 12  65-70
				"70 - 75",   // 13  70-75
				"75 - 80",   // 14  75-80
				"80 - 85",   // 15  80-85
				"85 +"       // 16  85+
				);

		private function GetSpeedRangeIndex(
			$speed
			)
		{
			$index = NULL;

			if ($speed <= 10.00)
			{
				$index = 0;
			}
			else if ($speed >= 85.00)
			{
				$index = 16;
			}
			else
			{
				$index = intval($speed / 5.0) - 1;
			}

			return $index;
		}

		private function CalculateSpeedingFact(
			$dataPoints,
			$postSpeedLimit,
			&$speedingVehicles,
			&$speedingPercentage,
			&$speedingSpeedMean
			)
		{
			$countDataPoints = count($dataPoints);
			$speedLimit = $postSpeedLimit;

			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "countDataPoints=$countDataPoints, speedLimit=$speedLimit");

			$speedingVehicles = 0;
			$speedingPercentage = 0.0;
			$speedingSpeedSum = 0.0;
			$speedingSpeedMean = 0.0;

			for ($i=0; $i<$countDataPoints; $i++)
			{
				if ($dataPoints[$i] > $speedLimit)
				{
					$speedingVehicles += 1;
					$speedingSpeedSum += $dataPoints[$i];
				}
			}

			if ($speedingVehicles == 0)
			{
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "0 speeding vehicle found");
			}
			else
			{
				$speedingPercentage = $speedingVehicles / $countDataPoints;
				$speedingSpeedMean = $speedingSpeedSum / $speedingVehicles;
			}

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "speedingVehicles=$speedingVehicles, speedingPercentage=$speedingPercentage, speedingSpeedMean=$speedingSpeedMean");
			return;
		}

		private function Calculate10MphPace(
			$dataPoints,
			&$rangeStart,
			&$rangeEnd,
			&$countInRange,
			&$percentInRange
			)
		{
			$countDataPoints = count($dataPoints);
			$percentInRange = 0;

			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "count(dataPoints)=$countDataPoints");

			$tenMphRangeStart = 0.0;
			$tenMphRangeEnd = $tenMphRangeStart + 10.0;
			$tenMphCountInRange = 0;
			$tenMphPercentInRange = 0.0;

			$rangeStart = $tenMphRangeStart;
			$rangeEnd = $tenMphRangeEnd;
			$countInRange = $tenMphCountInRange;
			$percentInRange = $tenMphPercentInRange;

			DBG_INFO_LOWPRIO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Checking range $tenMphRangeStart-$tenMphRangeEnd");

			for ($i=0; $i<$countDataPoints; $i++)
			{
				if (($dataPoints[$i] >= $tenMphRangeStart)
						&& ($dataPoints[$i] < $tenMphRangeEnd))
				{
					$tenMphCountInRange += 1;
				}
				else if ($dataPoints[$i] >= $tenMphRangeEnd)
				{
					DBG_INFO_LOWPRIO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Finished range i=$i, $tenMphRangeStart-$tenMphRangeEnd, tenMphCountInRange=$tenMphCountInRange (current high range is $rangeStart-$rangeEnd with $countInRange)");

					//
					// Check if the count in the current range is
					// larger than the existing range.
					//
					if ($tenMphCountInRange > $countInRange)
					{
						$rangeStart = $tenMphRangeStart;
						$rangeEnd = $tenMphRangeEnd;
						$countInRange = $tenMphCountInRange;
						$percentInRange = $tenMphCountInRange / $countDataPoints;
					}

					//
					// Start a new range if the number of remaining data points is
					// greater than the existing count in range.  If it's less, then
					// there isn't any remaining range that can be greater than the
					// existing one.
					//

					//
					// First reset $i to the beginning of the current range.  That's where we
					// we need to start checking data points for the new ten mph range.
					//
					$i = $i - $tenMphCountInRange - 1;

					if ($countDataPoints - $i + 1 > $countInRange)
					{
						$tenMphRangeStart += 0.1;
						$tenMphRangeEnd += 0.1;
						$tenMphCountInRange = 0;
						$tenMphPercentInRange = 0;

						DBG_INFO_LOWPRIO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Starting new range at i=".strval($i-1).", $tenMphRangeStart-$tenMphRangeEnd");
					}
					else
					{
						break;
					}
				}
			}

			$rangeStart = ($rangeStart == 0) ? ".0" : number_format($rangeStart, 1);
			$rangeEnd = number_format($rangeEnd, 1);

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "rangeStart=$rangeStart, rangeEnd=$rangeEnd, countInRange=$countInRange, percentInRange=$percentInRange");
			return;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;

			$this->dailyBuckets = array();
		}

		private function InitializeBuckets()
		{
			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__);

			// Day bucket - keep 'YYYY-MM-DD' - 10 characters
			$filterBeginTime = strtotime($this->filterBeginTime);
			$filterEndTime = strtotime($this->filterEndTime);
			$startTime = strtotime($this->startTime);
			$endTime = strtotime($this->endTime);

			$time = $startTime;
			$dayBucket = date("Y-m-d", $time);
			$dayOfWeek = date("w", $time);

			while ($time <= $endTime)
			{
				$dayBeginTime = strtotime("$dayBucket 00:00:00");
				$dayEndTime = strtotime("$dayBucket 23:59:59");

				$this->dailyBuckets[$dayBucket]['fullday'] = (($dayBeginTime >= $filterBeginTime) && ($dayEndTime <= $filterEndTime));
				$this->dailyBuckets[$dayBucket]['midweekday'] = (($dayOfWeek >= 2) && ($dayOfWeek <= 4));

				$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr]['totalspeed'] = 0;
				$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr]['datapoints'] = array();

				$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr]['totalspeed'] = 0;
				$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr]['datapoints'] = array();

				$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['totalspeed'] = 0;
				$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['datapoints'] = array();
				
				foreach ($this->speedRanges as &$speedRange)
				{
					$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr]['total'][$speedRange] = 0;
					$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr]['total'][$speedRange] = 0;
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['total'][$speedRange] = 0;
				}

				foreach ($this->hours as &$hour)
				{
					$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr]['total'][$speedRange] = 0;
					$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr]['total'][$speedRange] = 0;
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['total'][$speedRange] = 0;

					foreach ($this->speedRanges as &$speedRange)
					{
						$this->dailyBuckets[$dayBucket]['directions'][$this->primaryDr][$hour][$speedRange] = 0;
						$this->dailyBuckets[$dayBucket]['directions'][$this->secondaryDr][$hour][$speedRange] = 0;
						$this->dailyBuckets[$dayBucket]['directions'][$this->allDr][$hour][$speedRange] = 0;
					}
				}

				$time += 24 * 60 * 60;
				$dayBucket = date("Y-m-d", $time);
				$dayOfWeek = date("w", $time);
			}

			$this->directionalBuckets[$this->primaryDr]['fullname'] = $this->primaryDirection->GetFullName();
			$this->directionalBuckets[$this->primaryDr]['totalspeed'] = 0.0;
			$this->directionalBuckets[$this->primaryDr]['datapoints'] = array();

			$this->directionalBuckets[$this->secondaryDr]['fullname'] = $this->secondaryDirection->GetFullName();
			$this->directionalBuckets[$this->secondaryDr]['totalspeed'] = 0.0;
			$this->directionalBuckets[$this->secondaryDr]['datapoints'] = array();
			
            $this->directionalBuckets[$this->allDr]['fullname'] = $this->allDirection;
			$this->directionalBuckets[$this->allDr]['totalspeed'] = 0.0;
			$this->directionalBuckets[$this->allDr]['datapoints'] = array();

			foreach ($this->speedRanges as &$speedRange)
			{
				$this->directionalBuckets[$this->primaryDr][$speedRange] = 0;
				$this->directionalBuckets[$this->secondaryDr][$speedRange] = 0;
				$this->directionalBuckets[$this->allDr][$speedRange] = 0;	
			}

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
		}

		private function GetDataByDirection()
		{
			$filter = array(
					"jobsiteid='$this->jobSiteId'",
					"occurred>='$this->startTime'",
					"occurred<='$this->endTime'"
					);

			if ($this->ingestionId != 0)
			{
				$filter[] = "ingestionid='$this->ingestionId'";
			}

			$this->dataRows = IngestionDataRow::Find(
					$this->context->dbcon,
					array("ds", "occurred", "dr", "speed"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE
					);
		}

		private function PopulateAveragesOneDir(
			$type,
			$dr,
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "beginningRow=$beginningRow, type=$type");

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
					DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Not including $date in the averaging because it's not $type");
					continue;
				}

				$numberOfDays += 1;

				foreach ($this->hours as &$hour)
				{
					foreach ($this->speedRanges as &$speedRange)
					{
						if (!isset($directionHourlyTotals[$dr][$hour][$speedRange]))
						{
							$directionHourlyTotals[$dr][$hour][$speedRange] = 0;
						}

						$directionHourlyTotals[$dr][$hour][$speedRange] += $dailyBucket['directions'][$dr][$hour][$speedRange];
					}
				}
			}

			// Now populate the averages
			$currentRow = $beginningRow;

			$rowHeights = array(12, 13, 12, 13, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 13, 13, 13, 12, 13, 13, 12, 12, 13);

			foreach ($directionHourlyTotals as $direction => &$directionHourlyTotal)
			{
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "direction=$direction, dr=$dr, currentrow=$currentRow");

				for ($i=0; $i<count($rowHeights); $i++)
				{
					$this->multiDayHourlyWorksheet->getRowDimension($currentRow+$i)->setRowHeight($rowHeights[$i]);
				}

				$replicateDestinationRow = $currentRow - 1;
				ExcelHelpers::ReplicateRange($this->worksheetTemplates, $this->templateRangeAverageOneDir, $this->multiDayHourlyWorksheet, "A{$replicateDestinationRow}", TRUE);
				$this->multiDayHourlyWorksheet->setBreak("A{$replicateDestinationRow}" , \PHPExcel_Worksheet::BREAK_ROW);

				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", "Total Study Average");
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");
				$currentRow += 1;

				// Populate the direction
				$dirObj = new Direction($direction);
				$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", $dirObj->GetFullName());
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");

				$currentRow += 1;

				// Merge the header row
				$this->multiDayHourlyWorksheet->mergeCells("B{$currentRow}:R{$currentRow}");

				// Skip over column headers
				$currentRow += 2;

				$totalOfHourlyAverages = array();
				$totalOfAverages = 0;

				foreach ($this->hours as &$hour)
				{
					$currentColumn = "B";

					$totalOfSpeedRangeAverages = 0;

					foreach ($this->speedRanges as &$speedRange)
					{
						$avg = $directionHourlyTotal[$hour][$speedRange] / $numberOfDays;

						if (!isset($totalOfHourlyAverages[$speedRange]))
						{
							$totalOfHourlyAverages[$speedRange] = 0;
						}

						$totalOfHourlyAverages[$speedRange] += $avg;
						$totalOfSpeedRangeAverages += $avg;
						$totalOfAverages += $avg;

						$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$avg");
						$currentColumn++;
					}

					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$totalOfSpeedRangeAverages");

					$currentRow++;
				}

				// Populate the totals and percentages
				$currentColumn = "B";

				$totalsRow = $currentRow;
				$percentagesRow = $currentRow + 1;

				foreach ($this->speedRanges as &$speedRange)
				{
					$hourlyAverage = $totalOfHourlyAverages[$speedRange];

					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalsRow}", "$hourlyAverage");

					$pct = 0;

					if ($totalOfAverages > 0)
					{
						$pct = $hourlyAverage / $totalOfAverages;
					}

					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$percentagesRow}", "$pct");

					$currentColumn++;
				}

				$footNoteRow = $percentagesRow + 1;
				$this->multiDayHourlyWorksheet->mergeCells("A{$footNoteRow}:G{$footNoteRow}");

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "$totalOfAverages");

				// Skip over unused/empty rows to the next direction
				$currentRow += 4;

				$replicationRow = $currentRow - 1;
				ExcelHelpers::ReplicateRange($this->worksheetTemplates, $this->templateRangeTotalStudySpeedStat, $this->multiDayHourlyWorksheet, "A{$replicationRow}", TRUE);

				// Merge the header row
				$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:E{$currentRow}");
				$this->multiDayHourlyWorksheet->mergeCells("F{$currentRow}:K{$currentRow}");

				$fiftiethPercentileRow = $currentRow + 1;
				$eightyfifthPercentileRow = $currentRow + 2;
				$ninetyfifthPercentileRow = $currentRow + 3;
				
				$this->multiDayHourlyWorksheet->mergeCells("I{$eightyfifthPercentileRow}:J{$eightyfifthPercentileRow}");

				$directionalBucket = $this->directionalBuckets[$direction];

				$numDataPoints = count($directionalBucket['datapoints']);

				$fiftiethPercentile = intval($numDataPoints * 0.5);
				$eightyfifthPercentile = intval($numDataPoints * 0.85);
				$nintyfifthPercentile = intval($numDataPoints * 0.95);

				$this->multiDayHourlyWorksheet->setCellValue("D{$fiftiethPercentileRow}", number_format($directionalBucket['datapoints'][$fiftiethPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("D{$eightyfifthPercentileRow}", number_format($directionalBucket['datapoints'][$eightyfifthPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("D{$ninetyfifthPercentileRow}", number_format($directionalBucket['datapoints'][$nintyfifthPercentile], 1));

				$this->Calculate10MphPace($directionalBucket['datapoints'], $rangeStart, $rangeEnd, $countInRange, $percentInRange);

				$this->multiDayHourlyWorksheet->setCellValue("J{$fiftiethPercentileRow}", number_format($directionalBucket['totalspeed'] / $numDataPoints, 1));
				$this->multiDayHourlyWorksheet->setCellValue("I{$eightyfifthPercentileRow}", "$rangeStart - $rangeEnd");
				$this->multiDayHourlyWorksheet->setCellValue("J{$ninetyfifthPercentileRow}", number_format($percentInRange * 100, 1));

				$currentRow += 5;
			}

			// Record merge range for PDF report
			array_push(
					$this->mergeRangesForPdf,
					"A{$fiftiethPercentileRow}:C{$fiftiethPercentileRow}",
					"F{$fiftiethPercentileRow}:I{$fiftiethPercentileRow}",
					"A{$eightyfifthPercentileRow}:C{$eightyfifthPercentileRow}",
					"F{$eightyfifthPercentileRow}:H{$eightyfifthPercentileRow}",
					"A{$ninetyfifthPercentileRow}:C{$ninetyfifthPercentileRow}",
					"F{$ninetyfifthPercentileRow}:I{$ninetyfifthPercentileRow}"
					);

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
		}

		private function PopulateOneDayOneDir(
			$date,
			$direction,
			$directionBucket,
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "beginningRow=$beginningRow");

			$currentRow = $beginningRow;

			$rowHeights = array(12, 13, 12, 13, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 13, 13, 13, 13, 13, 12, 12, 13);

			for ($i=0; $i<count($rowHeights); $i++)
			{
				$this->multiDayHourlyWorksheet->getRowDimension($currentRow+$i)->setRowHeight($rowHeights[$i]);
			}

			$replicateDestinationRow = $currentRow - 1;
			ExcelHelpers::ReplicateRange($this->worksheetTemplates, $this->templateRangeOneDayOneDir, $this->multiDayHourlyWorksheet, "A{$replicateDestinationRow}", TRUE);
			$this->multiDayHourlyWorksheet->setBreak("A{$replicateDestinationRow}" , \PHPExcel_Worksheet::BREAK_ROW);

			// Populate the date
			$dateObject = \DateTime::createFromFormat("Y-m-d", $date);
			$formattedDate = $dateObject->format("l, F d, Y");

			$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", "{$formattedDate}");
			$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");

			$currentRow += 1;

			// Populate the direction
			$dirObj = new Direction($direction);
			$this->multiDayHourlyWorksheet->setCellValue("A{$currentRow}", $dirObj->GetFullName());
			$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:D{$currentRow}");

			$currentRow += 1;

			// Merge the header row
			$this->multiDayHourlyWorksheet->mergeCells("B{$currentRow}:R{$currentRow}");

			// Skip over the column headers
			$currentRow += 2;

			foreach ($this->hours as &$hour)
			{
				$currentColumn = "B";
				$hourlyTotal = 0;

				foreach ($this->speedRanges as &$speedRange)
				{
					$speedRangeCount = $directionBucket[$hour][$speedRange];

					$hourlyTotal += $speedRangeCount;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$speedRangeCount}");
					$currentColumn++;
				}

				// Populate the hourly total
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$hourlyTotal}");

				$currentRow += 1;
			}

			// Populate the speed range totals
			$currentColumn = "B";

			$dayTotal = 0;

			foreach ($this->speedRanges as &$speedRange)
			{
				// Populate the daily totals
				$speedRangeTotal = $directionBucket['total'][$speedRange];
				$dayTotal += $speedRangeTotal;
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$speedRangeTotal}");
				$currentColumn++;
			}

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$dayTotal}");

			$currentRow += 1;

			// Populate the percentages
			if ($dayTotal > 0)
			{
				$currentColumn = "B";

				foreach ($this->speedRanges as &$speedRanges)
				{
					// Populate the daily totals
					$percentage = $directionBucket['total'][$speedRanges] / $dayTotal;
					$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$currentRow}", "{$percentage}");
					$currentColumn++;
				}
			}

			// Populate the percentiles for each direction and other stats
			$currentRow += 2;

			$replicationRow = $currentRow - 1;
			ExcelHelpers::ReplicateRange($this->worksheetTemplates, $this->templateRangeOneDaySpeedStat, $this->multiDayHourlyWorksheet, "A{$replicationRow}", TRUE);

			// Merge the header row
			$this->multiDayHourlyWorksheet->mergeCells("A{$currentRow}:E{$currentRow}");
			$this->multiDayHourlyWorksheet->mergeCells("F{$currentRow}:K{$currentRow}");

			$currentRow += 1;

			$numDataPoints = count($directionBucket['datapoints']);

			if ($numDataPoints > 0)
			{
				$fiftiethPercentile = intval($numDataPoints * 0.5);
				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", number_format($directionBucket['datapoints'][$fiftiethPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("J{$currentRow}", number_format($directionBucket['totalspeed'] / $numDataPoints, 1));

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:I{$currentRow}"
						);

				$currentRow += 1;

				$this->multiDayHourlyWorksheet->mergeCells("I{$currentRow}:J{$currentRow}");

				$eightyfifthPercentile = intval($numDataPoints * 0.85);
				$this->Calculate10MphPace($directionBucket['datapoints'], $rangeStart, $rangeEnd, $countInRange, $percentInRange);
				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", number_format($directionBucket['datapoints'][$eightyfifthPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("I{$currentRow}", "$rangeStart - $rangeEnd");

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:H{$currentRow}"
						);

				$currentRow += 1;

				$nintyfifthPercentile = intval($numDataPoints * 0.95);
				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", number_format($directionBucket['datapoints'][$nintyfifthPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("J{$currentRow}", number_format($percentInRange * 100, 1));

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:I{$currentRow}"
						);

				$currentRow += 1;

				// Skip the gap between directions
				$currentRow += 1;
			}
			else
			{
				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", "0.0");
				$this->multiDayHourlyWorksheet->setCellValue("J{$currentRow}", "0.0");

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:I{$currentRow}"
						);

				$currentRow += 1;

				$this->multiDayHourlyWorksheet->mergeCells("I{$currentRow}:J{$currentRow}");

				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", "0.0");
				$this->multiDayHourlyWorksheet->setCellValue("J{$currentRow}", "0.0-10.0");

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:H{$currentRow}"
						);

				$currentRow += 1;

				$this->multiDayHourlyWorksheet->setCellValue("D{$currentRow}", "0.0");
				$this->multiDayHourlyWorksheet->setCellValue("J{$currentRow}", 0);

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$currentRow}:C{$currentRow}",
						"F{$currentRow}:I{$currentRow}"
						);

				$currentRow += 1;

				// Skip the gap between directions
				$currentRow += 1;
			}

		DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
		}

		private function PopulateReportData(
			$beginningRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "beginningRow=$beginningRow");
			
			$currentRow = $beginningRow;

			ExcelHelpers::ReplicateRange($this->worksheetTemplates, $this->templateRangeMultiDayHourly, $this->multiDayHourlyWorksheet, "A1", TRUE);

			// Merge a bunch of stuff
			$mergeRanges = array(
					"A1:G2",
					"B13:R13",
					"A15:S15",
					"A23:E23", 
					"F23:K23"
					);

			foreach ($mergeRanges as &$mergeRange)
			{
				$this->multiDayHourlyWorksheet->mergeCells($mergeRange);
			}

			// Set the template range & headers
			switch ($this->reportFormat)
			{
				case "standard":
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$this->primaryDirection->GetFullName()} / {$this->secondaryDirection->GetFullName()}");
					$currentRow += 1;

					$startTime = strtotime($this->startTime);
					$startDate = date("m/d/Y", $startTime);

					$endTime = strtotime($this->endTime);
					$endDate = date("m/d/Y", $endTime);

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					$currentRow += 7;  // Skip over some blank lines and column headers
					
					break;
					
				case "bellevue":
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$this->primaryDirection->GetFullName()} / {$this->secondaryDirection->GetFullName()}");
					$currentRow += 1;

					$startTime = strtotime($this->startTime);
					$startDate = date("m/d/Y", $startTime);

					$endTime = strtotime($this->endTime);
					$endDate = date("m/d/Y", $endTime);

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					$currentRow += 7;  // Skip over some blank lines and column headers
					
					break;
					
				case "redmond":
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->specificLocation);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->stationId);
					$currentRow += 1;
					
					$startTime = strtotime($this->startTime);
					$startDate = date("m/d/Y", $startTime);

					$endTime = strtotime($this->endTime);
					$endDate = date("m/d/Y", $endTime);

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					$currentRow += 6;  // Skip over some blank lines and column headers
					
					break;
			}

			// Populate the speed range totals
			$primaryCountsRow = $currentRow;
			$primaryPercentagesRow = $primaryCountsRow + 1;
			$secondaryCountsRow = $primaryPercentagesRow + 1;
			$secondaryPercentagesRow = $secondaryCountsRow + 1;
			$totalCountsRow = $secondaryPercentagesRow + 1;
			$totalsPercentagesRow = $totalCountsRow + 1;

			$this->multiDayHourlyWorksheet->setCellValue("A{$primaryCountsRow}", $this->primaryDirection->GetFullName());
			$this->multiDayHourlyWorksheet->setCellValue("A{$secondaryCountsRow}", $this->secondaryDirection->GetFullName());

			$numPrimaryDataPoints = count($this->directionalBuckets[$this->primaryDr]['datapoints']);
			$numSecondaryDataPoints = count($this->directionalBuckets[$this->secondaryDr]['datapoints']);
			$numTotalDataPoints = $numPrimaryDataPoints + $numSecondaryDataPoints;

			$currentColumn = "B";

			foreach ($this->speedRanges as &$speedRange)
			{
				// Populate the primary direction speed bucket total and pct
				$primaryDrCount = $this->directionalBuckets[$this->primaryDr][$speedRange];
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryCountsRow}", $primaryDrCount);

				$pct = 0;

				if ($primaryDrCount > 0)
				{
					$pct = $primaryDrCount / $numPrimaryDataPoints;
				}

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryPercentagesRow}", $pct);

				// Populate the secondary direction speed bucket total and pct
				$secondaryDrCount = $this->directionalBuckets[$this->secondaryDr][$speedRange];
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryCountsRow}", $secondaryDrCount);

				$pct = 0;

				if ($secondaryDrCount > 0)
				{
					$pct = $secondaryDrCount / $numSecondaryDataPoints;
				}

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryPercentagesRow}", $pct);

				// Populate the combined total and pct
				$totalCount = $primaryDrCount + $secondaryDrCount;
				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalCountsRow}", $totalCount);

				$pct = 0;

				if ($totalCount > 0)
				{
					$pct = $totalCount / $numTotalDataPoints;
				}

				$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalsPercentagesRow}", $pct);

				$currentColumn++;
			}

			// Populate the totals (primary, secondary, and combined)
			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryCountsRow}", $numPrimaryDataPoints);
			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$primaryPercentagesRow}", "100%");

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryCountsRow}", $numSecondaryDataPoints);
			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$secondaryPercentagesRow}", "100%");

			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalCountsRow}", $numTotalDataPoints);
			$this->multiDayHourlyWorksheet->setCellValue("{$currentColumn}{$totalsPercentagesRow}", "100%");

			// Populate speeding fact info for Redmond Format
			switch ($this->reportFormat)
			{
				case "redmond":
				
					$currentRow = $beginningRow + 17;
					$speedingFactDirectionRow = $currentRow + 1;
					$postSpeedLimitRow = $currentRow + 2;
					$speedingVehicleRow = $currentRow + 3;
					$speedingPercentageRow = $currentRow + 4;
					$speedingSpeedRow = $currentRow + 5;
						
					$this->CalculateSpeedingFact($this->directionalBuckets[$this->allDr]['datapoints'], $this->postSpeedLimit, $speedingVehicles, $speedingPercentage, $speedingSpeed);
						
					$this->multiDayHourlyWorksheet->setCellValue("R{$postSpeedLimitRow}", number_format($this->postSpeedLimit, 1));
					$this->multiDayHourlyWorksheet->setCellValue("R{$speedingVehicleRow}", $speedingVehicles);
					$this->multiDayHourlyWorksheet->setCellValue("R{$speedingPercentageRow}", number_format($speedingPercentage * 100, 1));
					$this->multiDayHourlyWorksheet->setCellValue("R{$speedingSpeedRow}", $speedingSpeed);
					
					//merge cells
					$this->multiDayHourlyWorksheet->mergeCells("M{$currentRow}:S{$currentRow}");
					$this->multiDayHourlyWorksheet->mergeCells("M{$speedingFactDirectionRow}:P{$speedingFactDirectionRow}");
					$this->multiDayHourlyWorksheet->mergeCells("M{$postSpeedLimitRow}:P{$postSpeedLimitRow}");
					$this->multiDayHourlyWorksheet->mergeCells("M{$speedingVehicleRow}:P{$speedingVehicleRow}");
					$this->multiDayHourlyWorksheet->mergeCells("M{$speedingPercentageRow}:P{$speedingPercentageRow}");
					$this->multiDayHourlyWorksheet->mergeCells("M{$speedingSpeedRow}:P{$speedingSpeedRow}");
					
				break;
			}

			// Populate the percentiles for each direction
			$currentRow = $beginningRow + 18;

			foreach ($this->directionalBuckets as &$directionalBucket)
			{
				if ($directionalBucket['fullname'] == "Allbound")
				{
					if ($this->reportFormat != "redmond")
					{
						$currentRow += 4;
						break;
					}
				}

				$nameRow = $currentRow;
				$fiftiethPercentileRow = $nameRow + 1;
				$eightyfifthPercentileRow = $nameRow + 2;
				$ninetyfifthPercentileRow = $nameRow + 3;

				$this->multiDayHourlyWorksheet->mergeCells("I{$eightyfifthPercentileRow}:J{$eightyfifthPercentileRow}");

				$numDataPoints = count($directionalBucket['datapoints']);

				$fiftiethPercentile = intval($numDataPoints * 0.5);
				$eightyfifthPercentile = intval($numDataPoints * 0.85);
				$nintyfifthPercentile = intval($numDataPoints * 0.95);

				$this->multiDayHourlyWorksheet->setCellValue("A{$nameRow}", $directionalBucket['fullname']);
				$this->multiDayHourlyWorksheet->setCellValue("F{$nameRow}", $directionalBucket['fullname']);

				$this->multiDayHourlyWorksheet->setCellValue("D{$fiftiethPercentileRow}", number_format($directionalBucket['datapoints'][$fiftiethPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("D{$eightyfifthPercentileRow}", number_format($directionalBucket['datapoints'][$eightyfifthPercentile], 1));
				$this->multiDayHourlyWorksheet->setCellValue("D{$ninetyfifthPercentileRow}", number_format($directionalBucket['datapoints'][$nintyfifthPercentile], 1));

				$this->Calculate10MphPace($directionalBucket['datapoints'], $rangeStart, $rangeEnd, $countInRange, $percentInRange);

				$this->multiDayHourlyWorksheet->setCellValue("J{$fiftiethPercentileRow}", number_format($directionalBucket['totalspeed'] / $numDataPoints, 1));
				$this->multiDayHourlyWorksheet->setCellValue("I{$eightyfifthPercentileRow}", "$rangeStart - $rangeEnd");
				$this->multiDayHourlyWorksheet->setCellValue("J{$ninetyfifthPercentileRow}", number_format($percentInRange * 100, 1));

				// Record merge range for PDF report
				array_push(
						$this->mergeRangesForPdf,
						"A{$nameRow}:C{$nameRow}",
						"F{$nameRow}:I{$nameRow}",
						"A{$fiftiethPercentileRow}:C{$fiftiethPercentileRow}",
						"F{$fiftiethPercentileRow}:I{$fiftiethPercentileRow}",
						"A{$eightyfifthPercentileRow}:C{$eightyfifthPercentileRow}",
						"F{$eightyfifthPercentileRow}:H{$eightyfifthPercentileRow}",
						"A{$ninetyfifthPercentileRow}:C{$ninetyfifthPercentileRow}",
						"F{$ninetyfifthPercentileRow}:I{$ninetyfifthPercentileRow}"
						);

				$currentRow += 4;
			}

			// Populate the location, date range, and site code (again).
			switch ($this->reportFormat)
			{
				case "standard":
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					
					break;
				
				case "bellevue":
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					$currentRow += 1;

					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					
					break;
					
				case "redmond":
					$currentRow += 1;
					
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->location);
					$currentRow += 1;
					
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->specificLocation);
					$currentRow += 1;
					
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->siteCode);
					$currentRow += 1;
					
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", $this->stationId);
					$currentRow += 1;
					
					$this->multiDayHourlyWorksheet->setCellValue("C{$currentRow}", "{$startDate} to {$endDate}");
					
					break;
			}

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
		}

		private function PopulateMultiDayHourlyWorksheet(
			&$maxRow
			)
		{
			DBG_ENTER(DBGZ_TUBE_SPEEDREPORT, __METHOD__);

			// Set template range
			switch ($this->reportFormat)
			{
				case "standard":
					$this->templateRangeMultiDayHourly = "E5:W45";
					$this->templateRangeOneDayOneDir = "E48:W78";
					$this->templateRangeAverageOneDir = "E48:W79";
					$this->templateRangeOneDaySpeedStat = "E80:W84";
					$this->templateRangeTotalStudySpeedStat = "E87:W91";
					break;
					
				case "bellevue":
					$this->templateRangeMultiDayHourly = "E5:W45";
					$this->templateRangeOneDayOneDir = "E48:W78";
					$this->templateRangeAverageOneDir = "E48:W79";
					$this->templateRangeOneDaySpeedStat = "E80:W84";
					$this->templateRangeTotalStudySpeedStat = "E87:W91";
					break;
					
				case "redmond":
					$this->templateRangeMultiDayHourly = "Z5:AR45";
					$this->templateRangeOneDayOneDir = "Z48:AR78";
					$this->templateRangeAverageOneDir = "Z48:AR79";
					$this->templateRangeOneDaySpeedStat = "Z80:AR84";
					$this->templateRangeTotalStudySpeedStat = "Z87:AR91";
					break;
			}

			// Set row heights and column widths
			$rowHeights = array(17, 17, 17, 17, 12, 12, 12, 12, 12, 12, 12, 13, 12, 13, 13, 12, 14, 12, 14, 12, 15, 13, 13, 12, 12, 12, 12, 12, 12, 12, 13, 12, 12, 12, 12);

			$row = 1;

			foreach ($rowHeights as &$rowHeight)
			{
				$this->multiDayHourlyWorksheet->getRowDimension($row)->setRowHeight($rowHeight);
				$row++;
			}

			$columnWidths = array(14.7109375, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 7, 9);

			$column = 'A';

			foreach ($columnWidths as &$columnWidth)
			{
				$this->multiDayHourlyWorksheet->getColumnDimension($column)->setWidth($columnWidth);
				$column++;
			}

			$this->PopulateReportData(6);

			// Populate daily stats
			$beginningRow = 43;

			switch ($this->reportFormat)
			{
			case "standard":
			
				foreach ($this->dailyBuckets as $date => &$dailyBucket)
				{
					$this->PopulateOneDayOneDir($date, $this->primaryDirection->GetFullName(), $dailyBucket['directions'][$this->primaryDr], $beginningRow);
					$beginningRow += 36;
					
					$this->PopulateOneDayOneDir($date, $this->secondaryDirection->GetFullName(), $dailyBucket['directions'][$this->secondaryDr], $beginningRow);
					$beginningRow += 36;
				}

				$this->PopulateAveragesOneDir('fullday',$this->primaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
				
				$this->PopulateAveragesOneDir('fullday',$this->secondaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
					
				break;
				
			case "bellevue":
			
				foreach ($this->dailyBuckets as $date => &$dailyBucket)
				{
					$this->PopulateOneDayOneDir($date, $this->primaryDirection->GetFullName(), $dailyBucket['directions'][$this->primaryDr], $beginningRow);
					$beginningRow += 36;
				}

				$this->PopulateAveragesOneDir('fullday',$this->primaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
				
				foreach ($this->dailyBuckets as $date => &$dailyBucket)
				{
					$this->PopulateOneDayOneDir($date, $this->secondaryDirection->GetFullName(), $dailyBucket['directions'][$this->secondaryDr], $beginningRow);
					$beginningRow += 36;
				}

				$this->PopulateAveragesOneDir('fullday',$this->secondaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
				
				break;
				
			case "redmond":
			
				foreach ($this->dailyBuckets as $date => &$dailyBucket)
				{
					$this->PopulateOneDayOneDir($date, $this->primaryDirection->GetFullName(), $dailyBucket['directions'][$this->primaryDr], $beginningRow);
					$beginningRow += 36;
					
					$this->PopulateOneDayOneDir($date, $this->secondaryDirection->GetFullName(), $dailyBucket['directions'][$this->secondaryDr], $beginningRow);
					$beginningRow += 36;
				}

				$this->PopulateAveragesOneDir('fullday',$this->primaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
				
				$this->PopulateAveragesOneDir('fullday',$this->secondaryDirection->GetDr(), $beginningRow);
				$beginningRow += 37;
					
				break;
			}
			
			$maxRow = $beginningRow;

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
		}

		public function Create(
			$jobSiteId,
			$title,
			$ingestionId,
			$reportFormat,
			$reportParameters,
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
					DBGZ_TUBE_SPEEDREPORT,
					__METHOD__,
					"jobSiteId=$jobSiteId, ingestionId=$ingestionId, reportFormat=$reportFormat, reportParameters=$reportParameters, efilterBeginTime=$filterBeginTime, filterEndTime=$filterEndTime startTime=$startTime, endTime=$endTime, siteCode=SsiteCode, primaryDirection=$primaryDirection, secondaryDirection=$secondaryDirection"
					);

			$this->jobSiteId = $jobSiteId;
			$this->title = $title;
			$this->ingestionId = $ingestionId;
			$this->reportFormat = strtolower($reportFormat);
			$this->filterBeginTime = $filterBeginTime;
			$this->filterEndTime = $filterEndTime;
			$this->startTime = $startTime;
			$this->endTime = $endTime;
			$this->siteCode = $siteCode;
			$this->location = $location;
			$this->primaryDirection = new Direction($primaryDirection);
			$this->secondaryDirection = new Direction($secondaryDirection);
			$this->allDirection = "Allbound";
			$this->primaryDr = $this->primaryDirection->GetDr();
			$this->secondaryDr = $this->secondaryDirection->GetDr();
			$this->allDr = "AB";

			//query for IDAX Office input for this JobSite
			$jobSiteRow = JobSiteRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("jobsiteid=$jobSiteId"),
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);
            
			$jobId = $jobSiteRow['jobid'];
			
			$jobRow = JobRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("jobid=$jobId"),
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);		

			$office = strtolower($jobRow['office']);
			
			//get report parameters from the UX
			$reportParameters = explode(";", $reportParameters);

			foreach ($reportParameters as &$reportParameter)
			{
				$keyValue = explode("=", $reportParameter);

				switch (strtolower($keyValue[0]))
				{
					case "stationid":
						$this->stationId = $keyValue[1];
						break;
					case "specificlocation":
						$this->specificLocation = $keyValue[1];
						break;
					case "speedlimit":
						$this->postSpeedLimit = $keyValue[1];
						break;
				}
			}

			DBG_INFO(
					DBGZ_TUBE_SPEEDREPORT,
					__METHOD__,
					"office id = $office, reportformat=$this->reportFormat, stationid=$this->stationId, specificlocation=$this->specificLocation, speedlimit=$this->postSpeedLimit"
					);

			$outputFiles = array();

			$this->GetDataByDirection();

			if ($this->dataRows == NULL)
			{
				$resultString = "WARNING: No data rows found";
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, $resultString);
			}
			else
			{
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Found ".count($this->dataRows)." rows.  Tallying results...");

				$this->InitializeBuckets();

				// We need to tally by day and hour intervals
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Tallying...");

				foreach ($this->dataRows as &$dataRow)
				{
					$occurred = \DateTime::createFromFormat("Y-m-d H:i:s", $dataRow['occurred']);

					$dayBucket = $occurred->format("Y-m-d");
					$hourBucket = $occurred->format("H");

					$dr = $dataRow['dr'];
					$speed = $dataRow['speed'];
					$speedRangeIndex = $this->GetSpeedRangeIndex($speed);

					$this->dailyBuckets[$dayBucket]['directions'][$dr]['totalspeed'] += $speed;
					$this->dailyBuckets[$dayBucket]['directions'][$dr]['datapoints'][] = $speed;
					$this->dailyBuckets[$dayBucket]['directions'][$dr][$hourBucket][$this->speedRanges[$speedRangeIndex]] += 1;
					$this->dailyBuckets[$dayBucket]['directions'][$dr]['total'][$this->speedRanges[$speedRangeIndex]] += 1;

					$this->directionalBuckets[$dr]['totalspeed'] += $speed;
					$this->directionalBuckets[$dr]['datapoints'][] = $speed;
					$this->directionalBuckets[$dr][$this->speedRanges[$speedRangeIndex]] += 1;
					
					//record to allbound
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['totalspeed'] += $speed;
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['datapoints'][] = $speed;
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr][$hourBucket][$this->speedRanges[$speedRangeIndex]] += 1;
					$this->dailyBuckets[$dayBucket]['directions'][$this->allDr]['total'][$this->speedRanges[$speedRangeIndex]] += 1;					

					$this->directionalBuckets[$this->allDr]['totalspeed'] += $speed;
					$this->directionalBuckets[$this->allDr]['datapoints'][] = $speed;
					$this->directionalBuckets[$this->allDr][$this->speedRanges[$speedRangeIndex]] += 1;
				}

				// Data points have to be sorted to determine the percentiles and 10 mph paces
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Sorting...");

				sort($this->directionalBuckets[$this->primaryDr]['datapoints']);
				sort($this->directionalBuckets[$this->secondaryDr]['datapoints']);
				sort($this->directionalBuckets[$this->allDr]['datapoints']);

				foreach ($this->dailyBuckets as &$dayBucket)
				{
					foreach ($dayBucket['directions'] as &$direction)
					{
						sort($direction['datapoints']);
					}
				}

				// Load the speed template and populate the worksheets.
				$this->objPHPExcel = \PHPExcel_IOFactory::load(_IDAX_REPORTS_PATH."/templates/xlsx/Speed_Template.xlsx");

				$this->multiDayHourlyWorksheet = new \PHPExcel_Worksheet($this->objPHPExcel, "Speed_Multi-Day_Hourly");
				$this->objPHPExcel->addSheet($this->multiDayHourlyWorksheet, 0);

				$this->worksheetTemplates = $this->objPHPExcel->getSheetByName("WorksheetTemplates");

				$this->PopulateMultiDayHourlyWorksheet($maxRow);

				$sheetIndex = $this->objPHPExcel->getIndex($this->worksheetTemplates);
				$this->objPHPExcel->removeSheetByIndex($sheetIndex);

				$properties = $this->objPHPExcel->getProperties();
				$properties->setCreator("IDAX Data Solutions");

				// Save the spreadhsheet as an Excel file on the local machine.
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Saving as Excel worksheet...");

				$excelWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
				$excelWriter->save("$outputFolder/{$title}_Speed.xlsx");
				$outputFiles["xls"] = "{$title}_Speed.xlsx";

				// After the Excel file, merge wherever is necessary, to avoid text wrap due to limitation of PHPExcel PDF Render
				DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Before creating PDF...Merge cells: ".serialize($this->mergeRangesForPdf));
				foreach ($this->mergeRangesForPdf as &$mergeRange)
				{
					$this->multiDayHourlyWorksheet->mergeCells($mergeRange);
				}

				// Now save it as a PDF file on the local machine.
				$rendererName = \PHPExcel_Settings::PDF_RENDERER_IDAXPDF;
				$rendererLibraryPath = '/home/idax/ipdf';

				if (!\PHPExcel_Settings::setPdfRenderer($rendererName, $rendererLibraryPath))
				{
					DBG_ERR(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "PDF renderer (renderName=$rendererName, renderLibraryPath=$rendererLibraryPath) not found/supported");
				}
				else
				{
					DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Saving as PDF...");

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

                    //get coresponded office contact information for this job
                    $officeEmail = "mark.skaggs@idaxdata.com";
                    
                    switch ($office)
                    {
                        case "wa":
                            $officeEmail = "project.manager.wa@idaxdata.com";
                            break;
                        case "ca":
                            $officeEmail = "project.manager.ca@idaxdata.com";
                            break;
                        case "co":
                            $officeEmail = "project.manager.co@idaxdata.com";
                            break;
                        case "me":
                            $officeEmail = "nathan.warren@idaxdata.com";
                            break;
                        case "ot":
                            $officeEmail = "mark.skaggs@idaxdata.com";
                            break;
                    }

					$searchStrings = array("@_IDAX_REPORTS_PATH@", "@reporttype@", "@direction@", "@location@", "@sitecode@", "@startdate@", "@enddate@", "@stationid@", "@specificlocation@", "@officeemail@");
					$replacements = array(_IDAX_REPORTS_PATH, "Speed", "$primaryDirection / $secondaryDirection", $location, $siteCode, $startDate, $endDate, $this->stationId, $this->specificLocation, $officeEmail);

					$htmlHeaderFooter->setDifferentFirst(true);
					$htmlHeaderFooter->setDifferentOddEven(false);

					// Set pdf headers & footers depends on reportFormat
					switch ($this->reportFormat)
					{
						case "standard":
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

							break;
							
						case "bellevue":
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

							break;
							
						case "redmond":
							$html = str_replace(
									$searchStrings,
									$replacements,
									file_get_contents(_IDAX_REPORTS_PATH."/templates/html/html_first_page_header_redmond.html")
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
									file_get_contents(_IDAX_REPORTS_PATH."/templates/html/pdf_page_header_redmond.html")
									);

							$pdfHeaderFooter->setOddHeader($html);

							$html = str_replace(
									$searchStrings,
									$replacements,
									file_get_contents(_IDAX_REPORTS_PATH."/templates/html/pdf_page_footer_redmond.html")
									);

							$pdfHeaderFooter->setOddFooter($html);

							break;
					}

					$pageSetup = $this->multiDayHourlyWorksheet->getPageSetup();
					$pageSetup->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
					$pageSetup->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER);

					// We don't need to print the first 10 row because the HTML header has the same content.
					// And we don't need to print rows 33-35 in the PDF file.  Set the print areas appropriately
					// to exclude these rows.
					DBG_INFO(DBGZ_TUBE_SPEEDREPORT, __METHOD__, "Set PDF print area as..."."A11:S32,A36:S".$maxRow);
					
					$pageSetup->setPrintArea('A11:S35,A42:S'.$maxRow);

					$pdfWriter = new \PHPExcel_Writer_PDF($this->objPHPExcel);
					$pdfWriter->save("$outputFolder/{$title}_Speed.pdf");

					$outputFiles["pdf"] = "{$title}_Speed.pdf";
					unset($pdfWriter);
				}
			}

			DBG_RETURN(DBGZ_TUBE_SPEEDREPORT, __METHOD__);
			return TRUE;
		}
	}
?>
