<?php

/**  Require idax library */
$pdfRendererClassFile = PHPExcel_Settings::getPdfRendererPath() . '/ipdf.php';
if (file_exists($pdfRendererClassFile)) {
    require_once $pdfRendererClassFile;
} else {
    throw new PHPExcel_Writer_Exception('Unable to load PDF Rendering library');
}

/**
 *  PHPExcel_Writer_PDF_idax
 *
 *  Copyright (c) 2006 - 2015 PHPExcel
 *
 *  This library is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU Lesser General Public
 *  License as published by the Free Software Foundation; either
 *  version 2.1 of the License, or (at your option) any later version.
 *
 *  This library is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *  Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU Lesser General Public
 *  License along with this library; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  @category    PHPExcel
 *  @package     PHPExcel_Writer_PDF
 *  @copyright   Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 *  @license     http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 *  @version     ##VERSION##, ##DATE##
 */
class PHPExcel_Writer_PDF_idaxPDF extends PHPExcel_Writer_PDF_Core implements PHPExcel_Writer_IWriter
{
    /**
     *  Create a new PHPExcel_Writer_PDF
     *
     *  @param  PHPExcel  $phpExcel  PHPExcel object
     */
    public function __construct(PHPExcel $phpExcel)
    {
        parent::__construct($phpExcel);
    }

    /**
     *  Save PHPExcel to file
     *
     *  @param     string     $pFilename   Name of the file to save as
     *  @throws    PHPExcel_Writer_Exception
     */
    public function save($pFilename = null)
    {
        $fileHandle = parent::prepareForSave($pFilename);

        //  Default PDF paper size
        $paperSize = 'LETTER';    //    Letter    (8.5 in. by 11 in.)

        //  Check for paper size and page orientation
        if (is_null($this->getSheetIndex())) {
			$pSheet = $this->phpExcel->getSheet(0);
            $orientation = ($this->phpExcel->getSheet(0)->getPageSetup()->getOrientation()
                == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->phpExcel->getSheet(0)->getPageSetup()->getPaperSize();
            $printMargins = $this->phpExcel->getSheet(0)->getPageMargins();
        } else {
			$pSheet = $this->phpExcel->getSheet($this->getSheetIndex());
            $orientation = ($this->phpExcel->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation()
                == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->phpExcel->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
            $printMargins = $this->phpExcel->getSheet($this->getSheetIndex())->getPageMargins();
        }
        $this->setOrientation($orientation);

        //  Override Page Orientation
        if (!is_null($this->getOrientation())) {
            $orientation = ($this->getOrientation() == PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT)
                ? PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT
                : $this->getOrientation();
        }
        $orientation = strtoupper($orientation);

        //  Override Paper Size
        if (!is_null($this->getPaperSize())) {
            $printPaperSize = $this->getPaperSize();
        }

        if (isset(self::$paperSizes[$printPaperSize])) {
            $paperSize = self::$paperSizes[$printPaperSize];
        }

		$pdfHeaderFooter = $pSheet->getPDFHeaderFooter();

		$pdfHeader = $pdfHeaderFooter->getOddHeader();
		$pdfFooter = $pdfHeaderFooter->getOddFooter();

		// Save the header and footer in temp files
		$baseTempFilename = md5(microtime().rand());
		$pdfTempHeaderFile = "/tmp/{$baseTempFilename}_header.html";
		$pdfTempFooterFile = "/tmp/{$baseTempFilename}_footer.html";

		file_put_contents($pdfTempHeaderFile, $pdfHeader);
		file_put_contents($pdfTempFooterFile, $pdfFooter);

        //  Create PDF
        $pdf = new ipdf();
        $ortmp = $orientation;
        $pdf->_setPageSize(strtoupper($paperSize), $ortmp);
        $pdf->DefOrientation = $orientation;
        $pdf->AddPage($orientation, $printMargins->getPDFTop(), $printMargins->getPDFBottom(), $printMargins->getPDFLeft(), $printMargins->getPDFRight());

        //  Document info
        $pdf->SetTitle($this->phpExcel->getProperties()->getTitle());
        $pdf->SetAuthor($this->phpExcel->getProperties()->getCreator());
        $pdf->SetSubject($this->phpExcel->getProperties()->getSubject());
        $pdf->SetKeywords($this->phpExcel->getProperties()->getKeywords());
        $pdf->SetCreator($this->phpExcel->getProperties()->getCreator());
		$pdf->SetPDFHeaderFooter($pdfTempHeaderFile, $pdfTempFooterFile);

		$html = $this->generateHTMLHeader(false).$this->generateSheetData().$this->generateHTMLFooter();

		// if (false)
		// {
		// 	$pathInfo = pathinfo($pFilename);
		// 	$htmlfile = fopen($pathInfo['dirname']."/".$pathInfo['filename'].".html", "w");
		// 	fwrite($htmlfile, $html);
		// 	fclose($htmlfile);
		// }

        $pdf->WriteHTML($html);

        //  Write to file
        fwrite($fileHandle, $pdf->Output('', 'S'));

		// Clean up the temp files.
		unlink($pdfTempHeaderFile);
		unlink($pdfTempFooterFile);

        parent::restoreStateAfterSave($fileHandle);
    }
}
