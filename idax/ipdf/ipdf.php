<?php
	class ipdf
	{
		private $pageSize = 'Letter';
		private $ortmp = NULL;
		private $orientation = 'Portrait';
		private $topMargin = '0.75';
		private $BottomMargin = '0.75';
		private $leftMargin = '0.75';
		private $rightMargin = '0.75';
		private $title = NULL;
		private $author = NULL;
		private $subject = NULL;
		private $keywords = NULL;
		private $creator = NULL;
		private $html = NULL;
		private $pdfHeader = NULL;
		private $pdfFooter = NULL;
		private static $command = "/home/idax/vendor/wkhtmltox/wkhtmltopdf";

		public function _setPageSize($pageSize, $ortmp)
		{
			if ($pageSize == 'LETTER')
			{
				$this->pageSize = 'Letter';
			}
			else
			{
				$this->pageSize = $pageSize;
			}

			$this->ortmp = $ortmp;
		}

		public function AddPage(
			$orientation,
			$topMargin,
			$bottomMargin,
			$leftMargin,
			$rightMargin
			)
		{
			if ($orientation == 'L')
			{
				$this->orientation = 'Landscape';
			}
			else if ($orientation == 'P')
			{
				$this->orientation = 'Portrait';
			}

			// Convert the margins from inches to millimeters.
			$this->topMargin = 25.4 * $topMargin;
			$this->bottomMargin = 25.4 * $bottomMargin;
			$this->leftMargin = 25.4 * $leftMargin;
			$this->rightMargin = 25.4 * $rightMargin;
		}

		public function SetTitle($title)
		{
			$this->title = $title;
		}

		public function SetAuthor($author)
		{
			$this->author = $author;
		}

		public function SetSubject($subject)
		{
			$this->subject = $subject;
		}

		public function SetKeywords($keywords)
		{
			$this->keywords = $keywords;
		}

		public function SetCreator($creator)
		{
			$this->creator = $creator;
		}

		public function SetPDFHeaderFooter($pdfHeader, $pdfFooter)
		{
			$this->pdfHeader = $pdfHeader;
			$this->pdfFooter = $pdfFooter;
		}

		public function WriteHTML($html)
		{
			$this->html = $html;
		}

		public function Output($arg1, $arg2)
		{
			// Save the html contents and pdf output in temporary files.
			$baseTempFilename = md5(microtime().rand());
			$tempHTMLFilename = "/tmp/$baseTempFilename.html";
			$tempPDFFilename = "/tmp/$baseTempFilename.pdf";

			$tempHTMLFile = fopen($tempHTMLFilename, "w");
			fwrite($tempHTMLFile, $this->html);
			fclose($tempHTMLFile);

			// Use the pdf tool to generate pdf from the html.
			//echo self::$command." -q --zoom 0.85 -O $this->orientation -T $this->topMargin -B $this->bottomMargin -L $this->leftMargin -R $this->rightMargin -s $this->pageSize --footer-html $this->pdfFooter --header-html $this->pdfHeader $tempHTMLFilename $tempPDFFilename\n";
			exec(self::$command." -q --zoom 0.85 -O $this->orientation -T $this->topMargin -B $this->bottomMargin -L $this->leftMargin -R $this->rightMargin -s $this->pageSize --footer-html $this->pdfFooter --header-html $this->pdfHeader $tempHTMLFilename $tempPDFFilename", $output, $return_var);

			$tempPDFFile = fopen($tempPDFFilename, "rb");
			$tempPDFFileSize = filesize($tempPDFFilename);
			$output = fread($tempPDFFile, $tempPDFFileSize);
			fclose($tempPDFFile);

			// Delete the temporary files
			unlink($tempHTMLFilename);
			unlink($tempPDFFilename);

			return $output;
		}
	}
?>
