<?php

	namespace Idax\Common\Classes;

	class ExcelHelpers
	{
		static public function GetColumnFromInteger(
			$columnNumber
			)
		{
			$letters = "";

			while ($columnNumber > 26)
			{
				$digit = intval($columnNumber / 26);

				$letters .= chr($digit + 65 - 1);
				$columnNumber -= (26 * $digit);
			}

			if ($columnNumber > 0)
			{
				$letters .= chr($columnNumber + 65 - 1);
			}

			return $letters;
		}

		static public function GetCellInfo(
			$cell,
			&$column,
			&$columnOrdinal,
			&$row
			)
		{
			$cell = strtoupper($cell);

			$column = "";
			$columnOrdinal = 0;
			$row = "";

			$i = 0;
			$ord = ord($cell[$i]);

			while (($ord >= 65) && ($ord <= 90))
			{
				$column .= $cell[$i];
				$columnOrdinal = (26 * $columnOrdinal) + ($ord - 65 + 1);

				$i += 1;
				if (!isset($cell[$i])) break;

				$ord = ord($cell[$i]);
			}

			while (($ord >= 48) && ($ord <= 57))
			{
				$row .= $cell[$i];

				$i += 1;
				if (!isset($cell[$i])) break;

				$ord = ord($cell[$i]);
			}

			$row = intval($row);
		}

		static public function GetCellRangeInfo(
			$cellRange
			)
		{
			$cells = explode(":", $cellRange);

			ExcelHelpers::GetCellInfo($cells[0], $firstColumn, $firstColumnOrdinal, $firstRow);
			ExcelHelpers::GetCellInfo($cells[1], $lastColumn, $lastColumnOrdinal, $lastRow);

			$rangeInfo = array(
					"firstinrange" => array(
							"column" => array("letters" => $firstColumn, "ordinal" => $firstColumnOrdinal),
							"row" => $firstRow
							),
					"lastinrange" => array(
							"column" => array("letters" => $lastColumn, "ordinal" => $lastColumnOrdinal),
							"row" => $lastRow
							),
					"numcolumns" => $lastColumnOrdinal - $firstColumnOrdinal + 1,
					"numrows" => $lastRow - $firstRow + 1
					);

			return $rangeInfo;
		}

		static public function ReplicateStyles(
			$srcWorksheet,
			$srcRange,
			$dstWorksheet,
			$dst
			)
		{
			$srcRangeInfo = ExcelHelpers::GetCellRangeInfo($srcRange);
			ExcelHelpers::GetCellInfo($dst, $dstColumn, $dstColumnOrdinal, $dstRow);

			for ($i=0; $i<$srcRangeInfo["numcolumns"]; $i++)
			{
				for ($j=0; $j<$srcRangeInfo["numrows"]; $j++)
				{
					$srcCell = ExcelHelpers::GetColumnFromInteger($srcRangeInfo["firstinrange"]["column"]["ordinal"] + $i) . strval($srcRangeInfo["firstinrange"]["row"] + $j);
					$dstCell = ExcelHelpers::GetColumnFromInteger($dstColumnOrdinal + $i) . strval($dstRow + $j);

					$style = $srcWorksheet->getCell($srcCell)->getStyle();
					$dstWorksheet->duplicateStyle($style, $dstCell);
				}
			}
		}

		static public function ReplicateRange(
			$srcWorksheet,
			$srcRange,
			$dstWorksheet,
			$dst,
			$preserveFormatting
			)
		{
			if ($preserveFormatting)
			{
				ExcelHelpers::ReplicateStyles($srcWorksheet, $srcRange, $dstWorksheet, $dst);
			}

			$cellValues = $srcWorksheet->rangeToArray(
					$srcRange,
					null,        // null value
					false,       // calculate formulas
					false
					);

			$dstWorksheet->fromArray($cellValues, null, $dst);
		}
	};

?>
