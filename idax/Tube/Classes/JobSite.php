<?php

	namespace Idax\Tube\Classes;

	require_once '/home/core/core.php';
	require_once '/home/idax/idax.php';

	use \Core\Common\Classes\AWSFileManager;
	use \Idax\Common\Classes\JobSite as CommonJobSite;
	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Common\Classes\Direction;
	use \Idax\Common\Classes\ExcelHelpers;
	use \Idax\Tube\Data\IngestionRow;
	use \Idax\Tube\Data\IngestionDataSetRow;
	use \Idax\Tube\Data\IngestionDataRow;
	use \Idax\Tube\Data\ReportRow;
	use \Idax\Tube\Reports\Classes\ClassReport;
	use \Idax\Tube\Reports\Classes\SpeedReport;
	use \Idax\Tube\Reports\Classes\VolumeReport;

	class JobSite
	{
		private $context = NULL;
		private $jobManager = NULL;
		private $jobSite = NULL;
		private $jobSitePrimaryDirection = NULL;
		private $jobSiteSecondaryDirection = NULL;
		private $jobSiteId = NULL;
		private $awsFileManager = NULL;
		private $stream = NULL;
		private $profileInfo = NULL;
		private $sites = NULL;
		private $dsToSiteMap = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "methodName=$methodName");

			$jobSite = new JobSite($context);

			$response = "failed";
			$responder = "TubeJobSite::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'IngestData':
					$date = date("Y-m-d H:i:s");

					$result = $jobSite->IngestData(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['ingestionkey']) ? $parameters['ingestionkey'] : NULL,
							isset($parameters['reverseprimary']) ? $parameters['reverseprimary'] : NULL,
							isset($parameters['filecontents']) ? $parameters['filecontents'] : NULL,
							isset($parameters['replaceexisting']) ? boolval($parameters['replaceexisting']) : NULL,
							$ingestionId,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"ingestionid" => $ingestionId,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array(
								"resultstring" => $resultString
								);
					}

					break;

				case 'CreateReports':
					$reportTypes = array();

					$i = 0;

					while (isset($parameters["type".$i]))
					{
						DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "type{$i}=".$parameters["type".$i]);

						$reportTypes[] = $parameters["type".$i];
						$i += 1;
					}

					if (count($reportTypes) == 0)
					{
						DBG_WARN(DBGZ_TUBE_JOBSITE, __METHOD__, "No type parameters");
					}

					//
					// Client passes dates in the form "mm/dd/yyyy" but the APIs expect time
					// in the form YYYY-mm-dd hh:mm:ss.
					//
					$startTime = isset($parameters['startdate']) ? strtotime($parameters['startdate']." 00:00:00") : NULL;
					$endTime = isset($parameters['enddate']) ? strtotime($parameters['enddate']." 23:59:59") : NULL;

					$result = $jobSite->CreateReports(
							isset($parameters['ingestionid']) ? $parameters['ingestionid'] : NULL,
							($startTime != NULL) ? date("Y-m-d H:i:s", $startTime) : NULL,
							($endTime != NULL) ? date("Y-m-d H:i:s", $endTime) : NULL,
							$reportTypes,
							isset($parameters['reportformat']) ? $parameters['reportformat'] : NULL,
							isset($parameters['reportparameters']) ? $parameters['reportparameters'] : NULL,
							TEMP_FILE_FOLDER,
							$outputFiles,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("outputFiles" => $outputFiles, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "TubeJobSite";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
			$this->awsFileManager = new AWSFileManager(IDAX_DATA_BUCKET, AWSREGION, AWSKEY, AWSSECRET);
		}

		public function Update(
			$jobSiteId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = TRUE;

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return TRUE;
		}

		public function GetInfo(
			$jobSiteId,
			$infoLevel,
			&$jobSiteData,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, infoLevel=$infoLevel");

			$result = TRUE;

			// Get ingestions - rows and files in bucket
			// Get reports - rows and files in bucket

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return TRUE;
		}

		public function Delete(
			$jobSiteId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = TRUE;

			$ingestionRows = IngestionRow::Find(
					$this->context->dbcon,
					array("ingestionid", "bucketfilename"),
					array("jobsiteid=$jobSiteId"),
					NULL,
					ROW_ASSOCIATIVE
					);

			if ($ingestionRows != NULL)
			{
				// Delete ingestions - rows and files in bucket
				foreach ($ingestionRows as &$ingestionRow)
				{
					$ingestionId = $ingestionRow['ingestionid'];

					IngestionDataSetRow::Delete($this->context->dbcon, array("ingestionid=$ingestionId"));
					IngestionDataRow::Delete($this->context->dbcon, array("ingestionid=$ingestionId"));

					$this->awsFileManager->DeleteFile($ingestionRow['bucketfilename']);

					IngestionRow::Delete($this->context->dbcon, array("ingestionid=$ingestionId"));
				}
			}

			// Delete reports - rows and files in bucket
			$reportRows = ReportRow::Find(
					$this->context->dbcon,
					array("reportid", "bucketfilename"),
					array("jobsiteid=$jobSiteId"),
					NULL,
					ROW_ASSOCIATIVE
					);

			if ($reportRows != NULL)
			{
				// Delete reports - rows and files in bucket
				foreach ($reportRows as &$reportRow)
				{
					$reportId = $reportRow['reportid'];

					$this->awsFileManager->DeleteFile($reportRow['bucketfilename']);

					ReportRow::Delete($this->context->dbcon, array("reportid=$reportId"));
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return TRUE;
		}

		private function ParseReportInfo()
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__);

			$reportInfo = array();

			// Parse lines until we get to "Datasets:".
			$line = fgets($this->stream);

			while (($line !== FALSE)
					&& (substr($line, 0, 9) != "Datasets:"))
			{
				$line = trim($line);

				if (strlen($line) > 0)
				{
					$reportInfo[] = $line;
				}

				$line = fgets($this->stream);
			}

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__);
			return $reportInfo;
		}

		private function ParseSiteInfo(
			$siteName,
			$siteIndex,
			$reversePrimary
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__);

			$siteInfo = array();
			$siteInfo["Name"] = $siteName;

			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				$token = strtok($line, ":");

				if ($token == "Site")
				{
					// Found another site that needs to be parsed.
					$siteName = trim(strtok(""));

					$this->ParseSiteInfo($siteName, $siteIndex + 1, $reversePrimary);
					break;
				}
				else if ($token == "Profile")
				{
					// No more data sets once we reach the "Profile" section in the file.
					break;
				}
				else if ($token == "Survey Duration")
				{
					$value1 = trim(strtok("="));
					$value2 = trim(strtok(""), " \t\n\r,>");

					$date1 = date_create_from_format("H:i D, M d, Y", $value1);
					$date2 = date_create_from_format("H:i D, M d, Y+", $value2);

					$siteInfo[$token] = array("start" => $date1, "end" => $date2);
				}
				else if ($token == "Direction")
				{
					// Example of value: 3 - South bound, A trigger first. Lane: 0
					// Get the direction store the "dr" value in the site info.
					$components = explode(" ", trim(strtok("")));

					$direction = trim($components[2].$components[3], ",");
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Primary direction for site ".$siteInfo["Name"]." is $direction");

					$boxDirectionObj = new Direction($direction);

					// siteIndex 0 is for the job site's primary direction.
					if ($direction == "Northbound")
					{
						$jobSiteDirectionObj = new Direction($this->jobSitePrimaryDirection);
					}
					else
					{
						$jobSiteDirectionObj = new Direction($this->jobSiteSecondaryDirection);
					}

					$siteInfo['dr'][$boxDirectionObj->GetDr()] = $jobSiteDirectionObj->GetDr();
					$siteInfo['dr'][$boxDirectionObj->GetOppositeDr()] = $jobSiteDirectionObj->GetOppositeDr();

					if ($reversePrimary)
					{
						$siteInfo['type'][$boxDirectionObj->GetDr()] = 'secondary';
						$siteInfo['type'][$boxDirectionObj->GetOppositeDr()] = 'primary';
					}
					else
					{
						$siteInfo['type'][$boxDirectionObj->GetDr()] = 'primary';
						$siteInfo['type'][$boxDirectionObj->GetOppositeDr()] = 'secondary';
					}

					$siteInfo[$token] = $value;
				}
				else if (strlen(trim($token)) > 0)
				{
					$value = trim(strtok(""));

					$siteInfo[$token] = $value;
				}

				$line = fgets($this->stream);
			}

			$this->sites[$siteIndex] = $siteInfo;

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__);
			return;
		}

		private function ParseDatasets(
			$reversePrimary
			)
		{
			$this->sites = array();

			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				$token = strtok($line, ":");

				if ($token == "Profile")
				{
					break;
				}
				else if ($token == "Site")
				{
					$siteName = trim(strtok(""));

					$this->ParseSiteInfo($siteName, 0, $reversePrimary);

					break;
				}

				$line = fgets($this->stream);
			}

			return;
		}

		private function ParseProfileInfo()
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__);

			$this->profileInfo = array();

			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				$token = strtok($line, ":");

				if ($token[0] == "")
				{
					break;
				}
				else if ($token == "Filter time")
				{
					$value1 = trim(strtok("="));
					$value2 = trim(strtok(""), " \t\n\r,>");

					$date1 = date_create_from_format("H:i D, M d, Y", $value1);
					$date2 = date_create_from_format("H:i D, M d, Y+", $value2);

					$this->profileInfo[$token] = array("start" => $date1, "end" => $date2);
				}
				else if ($token == "Speed range")
				{
					$value1 = intval(trim(strtok("-")));
					$value2 = intval(trim(strtok(""), " \t\n\r,>"));

					$this->profileInfo[$token] = array("low" => $value1, "high" => $value2);
				}
				else if ($token == "Included classes")
				{
					$this->profileInfo[$token] = explode(", ", trim(strtok("")));
				}
				else if (strlen(trim($token)) > 0)
				{
					$value = trim(strtok(""));

					$this->profileInfo[$token] = $value;
				}

				$line = fgets($this->stream);
			}

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__);
			return;
		}

		private function ParseDataRows()
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__);

			$oneway = $this->jobSite->getOneWay();

			$dataRows = array();

			// Read until we get to the column headers.  Column header will be identified by "DS".
			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				$token = strtok($line, " ");

				if ($token == "DS")
				{
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Found the columns row");
					break;
				}

				$line = fgets($this->stream);
			}

			// Now read rows until we get to the end of the file.
			$line = fgets($this->stream);
			$rowNumber = 0;
			$numDataRows = 0;
			$numSkippedRows = 0;

			$numSites = count($this->sites);

			DBG_INFO(
					DBGZ_TUBE_JOBSITE,
					__METHOD__,
					"numSites=$numSites, primaryDirection=$this->jobSitePrimaryDirection, secondaryDirection=$this->jobSiteSecondaryDirection, oneway=$oneway"
					);

			while ($line !== FALSE)
			{
				$rowNumber += 1;

				// Columns
				// DS Trig Num Ht YYYY-MM-DD hh:mm:ss Dr  Speed     Wb   Hdwy    Gap Ax Gp  Rho Cl Nm         Vehicle

				$dsString = trim(strtok($line, " "));

				if (strlen($dsString) < 2)
				{
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Got to end of data rows.");
					break;
				}

				$ds = intval($dsString);
				$trigNum = intval(strtok(" "));
				$ht = intval(strtok(" "));
				$date = strtok(" ");
				$time = strtok(" ");
				$datetime = date_create_from_format("Y-m-d H:i:s", "$date $time");
				$dr = rtrim(strtok(" "), '0..9');
				$speed = floatval(strtok(" "));
				$wb = floatval(strtok(" "));
				$hdwy = floatval(strtok(" "));
				$gap = floatval(strtok(" "));
				$ax = intval(strtok(" "));
				$gp = intval(strtok(" "));
				$rho = floatval(strtok(" "));
				$cl = intval(strtok(" "));
				$nm = strtok(" ");
				$vehicle = strtok(" ");
				$other = trim(strtok("\n"));

				$reverse = FALSE;
				$isCoercedSequence = FALSE;
				$numberInSequence = -1;

				if (($numSites == 1) && !$oneway)
				{
					//
					// If there is a coerced sequece, then we have to reverse the direction
					// of every other row in the coerced sequence *if* the gap is 0.  If gap
					// is greater than 0, then terminate the coerced sequence.
					//
					// Coerced sequeces are marked with "Coerced sequence"" in the "$other" field followed by the
					// number of rows in the coerced sequence.
					//
					$isCoercedSequence = FALSE;
					$numberInSequence = 0;

					$coercedSequenceString = stristr($other, "Coerced sequence ");

					if ($coercedSequenceString !== FALSE)
					{
						$isCoercedSequence = TRUE;
						$numberInSequence = intval($coercedSequenceString[17]);

						// Don't reverse the first row in the sequence.
						$reverse = FALSE;
					}

					if (($numberInSequence > 0) && ($gap == 0))
					{
						$reverse = !$reverse;
						$numberInSequence -= 1;

						if ($reverse)
						{
							$dr = new Direction($dr);
							$dr = $dr->getOppositeDr();
						}
					}
					else
					{
						// This effectively terminates the coerced sequence.
						$numberInSequence = 0;
					}
				}

				$site = &$this->dsToSiteMap[$ds];

				$includeRow = TRUE;

				if ($numSites > 1)
				{
					// Filter out secondary direction for a given dataset
					if ($site['type'][$dr] != 'primary')
					{
						DBG_INFO_LOWPRIO(DBGZ_TUBE_JOBSITE, __METHOD__, "Skipping row with ds=$ds, dr=$dr");

						$includeRow = FALSE;
						$numSkippedRows += 1;
					}
				}
				else if ($oneway)
				{
					// Filter out secondary direction if jobsite is oneway.
					if ($site['type'][$dr] != 'primary')
					{
						DBG_INFO_LOWPRIO(DBGZ_TUBE_JOBSITE, __METHOD__, "Skipping row with ds=$ds, dr=$dr");
						$includeRow = FALSE;
						$numSkippedRows += 1;
					}
				}

				DBG_INFO_LOWPRIO(DBGZ_TUBE_JOBSITE, __METHOD__, "$rowNumber: dr=$dr, speed=$speed, gap=$gap, vehicle=$vehicle, isCoercedSequence=$isCoercedSequence, other=$other, reverse=$reverse, numberInSequence=$numberInSequence, includeRow=$includeRow");

				if ($includeRow)
				{
					$numDataRows += 1;

					// Add the row to the ingestion data table
					IngestionDataRow::Create(
							$this->context->dbcon,
							$this->ingestionId,
							$this->jobSiteId,
							$ds,
							$trigNum,
							$ht,
							$datetime->format("Y-m-d H:i:s"),
							$site['dr'][$dr],
							$speed,
							$wb,
							$hdwy,
							$gap,
							$ax,
							$gp,
							$rho,
							$cl,
							$nm,
							$vehicle,
							$isCoercedSequence,
							$other,
							$ingestionData
							);
				}

				$line = fgets($this->stream);
			}

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__, "numDataRows=$numDataRows, numSkippedRows=$numSkippedRows");
			return $dataRows;
		}

		//
		// The file has one or more datasets (max should be two, but we'll code it for N).  And each
		// data row identifies the dataset it belongs to in the "ds" column.  There doesn't seem to
		// be a defined way to map the ds value to a dataset, so we'll assign the lowest ds value to
		// the first dataset, next lowest to the second dataset, etc.
		//
		private function ScanDsValuesAndAssignToDatasets()
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__);

			// Read until we get to the column headers.  Column header will be identified by "DS".
			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				$token = strtok($line, " ");

				if ($token == "DS")
				{
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Found the columns row");
					break;
				}

				$line = fgets($this->stream);
			}

			//
			// Now read rows until we've read all the possible ds values or reached the end of
			// the data rows.
			//
			$dsValues = array();

			// Read the first row.
			$line = fgets($this->stream);

			while ($line !== FALSE)
			{
				// Columns
				// DS Trig Num Ht YYYY-MM-DD hh:mm:ss Dr  Speed     Wb   Hdwy    Gap Ax Gp  Rho Cl Nm         Vehicle

				$dsString = trim(strtok($line, " "));

				if (strlen($dsString) < 2)
				{
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Got to end of data rows.");
					break;
				}

				$ds = intval($dsString);

				if (!in_array($ds, $dsValues))
				{
					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Adding ds value $ds ($dsString) to dsValues array");

					$dsValues[] = $ds;

					if (count($dsValues) == count($this->sites))
					{
						DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "All dataset values have been read.");
						break;
					}
				}

				$line = fgets($this->stream);
			}

			// We need the dsValues sorted in ascending order.
			sort($dsValues);

			// Now map the ds values to the ds sites.
			$this->dsToSiteMap = array();

			$i = 0;

			foreach ($dsValues as $dsValue)
			{
				DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Assiging ds '$dsValue' to dataset ".$this->sites[$i]["Name"]);

				$this->dsToSiteMap[$dsValue] = $this->sites[$i];
				$i++;
			}

			DBG_RETURN(DBGZ_TUBE_JOBSITE, __METHOD__);
			return;
		}

		public function IngestData(
			$jobSiteId,
			$ingestionKey,
			$reversePrimary,
			$fileContents,
			$replaceExisting,
			&$ingestionId,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_TUBE_JOBSITE,
					__METHOD__,
					"jobSiteId=$jobSiteId, ingestionKey=$ingestionKey, reversePrimary=$reversePrimary, replaceExisting=".strval($replaceExisting)
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId == NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($ingestionKey == NULL)
			{
				$resultString = "Missing parameter 'ingestionkey'";
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($fileContents == NULL)
			{
				$resultString = "Missing parameter 'filecontents'";
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				// Make sure it's a valid job, jobsite.
				$commonJobSite = new CommonJobSite($this->context);

				$result = $commonJobSite->GetJobSite(
						$jobSiteId,
						ROW_OBJECT,
						$this->jobSite,
						$jobRow,
						$resultString
						);

				if (!$result)
				{
					DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, "GetJobSite failed with resultString='$resultString'");

					DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, FALSE);
					return FALSE;
				}

				$this->jobSiteId = $jobSiteId;

				$directions = explode("/", $this->jobSite->getDirection());

				$this->jobSitePrimaryDirection = $directions[0];
				$this->jobSiteSecondaryDirection = $directions[1];


				// Set defaults if no value is specified for optional params.
				if ($reversePrimary == NULL)
				{
					$reversePrimary = FALSE;
				}

				if ($replaceExisting == NULL)
				{
					$replaceExisting = FALSE;
				}

				$ingestionObject = NULL;

				if ($replaceExisting)
				{
					$ingestionObject = IngestionRow::FindOne(
							$this->context->dbcon,
							array("ingestionid"),
							array("ingestionkey='$ingestionKey'"),
							NULL,
							ROW_OBJECT
							);

					if ($ingestionObject != NULL)
					{
						$ingestionId = $ingestionObject->getIngestionId();

						IngestionDataSetRow::Delete($this->context->dbcon, array("ingestionid='$ingestionId'"));
						IngestionDataRow::Delete($this->context->dbcon, array("ingestionid='$ingestionId'"));
					}
				}

				$result = FALSE;

				$ingestDate = date('Y-m-d H:i:s');

				//
				// Upload the fileContents to the bucket.
				//
				// Ensure a unique filename in the bucket by prepending the ingestionkey with current time
				//
				$microTimeStamp = microtime(true);
				$timeStamp = floor($microTimeStamp);
				$microSeconds = round(($microTimeStamp - $timeStamp) * 100000);

				$bucketFilename = "{$microSeconds}_{$ingestionKey}";

				$result = $this->awsFileManager->UploadData(
						$bucketFilename,
						"public-read",
						$fileContents,
						GetMimeTypeByFileExtension("txt"),
						FALSE,
						$resultString
						);

				if (!$result)
				{
					$resultString = "WARNING: upload to bucket failed";
				}

				//
				// Now parse the fileContents.  Put in a file stream to facilitate reading lines with fgets.
				//
				$this->stream = fopen('php://memory','r+');
				fwrite($this->stream, $fileContents);
				rewind($this->stream);

				$reportInfo = $this->ParseReportInfo();
				$this->ParseDatasets($reversePrimary);
				$this->ParseProfileInfo();

				//
				// Scanning ds values will read lines of input - lines that we need to process latter.  So
				// save the current file position before calling, and then restore the file position after
				// it returns.
				//
				$pos = ftell($this->stream);

				$this->ScanDsValuesAndAssignToDatasets();

				fseek($this->stream, $pos, SEEK_SET);

				mysqli_begin_transaction($this->context->dbcon, MYSQLI_TRANS_START_READ_WRITE);

				// If $replaceExisting is true, then we might have found an ingestion record above.  And, if so,
				// we'll update it with new data related to this ingestion.  If not, then we create a new record
				// for this ingestion.
				if ($ingestionObject != NULL)
				{
					$ingestionObject->setJobSiteId($jobSiteId);
					$ingestionObject->setAccountId($this->context->account->getAccountId());
					$ingestionObject->setIngestDate($ingestDate);
					$ingestionObject->setIngestionKey($ingestionKey);
					$ingestionObject->setReversed($reversePrimary);
					$ingestionObject->setBucketFileName($bucketFilename);
					$ingestionObject->setTitle($reportInfo[0]);
					$ingestionObject->setSubTitle($reportInfo[1]);
					$ingestionObject->setDescription($reportInfo[2]);
					$ingestionObject->setFilterBeginTime($this->profileInfo["Filter time"]["start"]->format("Y-m-d H:i:s"));
					$ingestionObject->setFilterEndTime($this->profileInfo["Filter time"]["end"]->format("Y-m-d H:i:s"));
					$ingestionObject->setIncludedClasses(implode(",", $this->profileInfo["Included classes"]));
					$ingestionObject->setSpeedRangeHigh($this->profileInfo["Speed range"]["high"]);
					$ingestionObject->setSpeedRangeLow($this->profileInfo["Speed range"]["low"]);
					$ingestionObject->setDirection($this->profileInfo["Direction"]);
					$ingestionObject->setSeparation($this->profileInfo["Separation"]);
					$ingestionObject->setName($this->profileInfo["Name"]);
					$ingestionObject->setScheme($this->profileInfo["Scheme"]);
					$ingestionObject->setUnits($this->profileInfo["Units"]);

					$result = $ingestionObject->CommitChangedFields();
				}
				else
				{
					// Create ingestion row
					$result = IngestionRow::Create(
							$this->context->dbcon,
							$jobSiteId,
							$this->context->account->getAccountId(),
							$ingestDate,
							$ingestionKey,
							$reversePrimary,
							$bucketFilename,
							$reportInfo[0],  // title
							$reportInfo[1],  // subtitle
							$reportInfo[2],  // description
							$this->profileInfo["Filter time"]["start"]->format("Y-m-d H:i:s"),
							$this->profileInfo["Filter time"]["end"]->format("Y-m-d H:i:s"),
							implode(",", $this->profileInfo["Included classes"]),
							$this->profileInfo["Speed range"]["high"],
							$this->profileInfo["Speed range"]["low"],
							$this->profileInfo["Direction"],
							$this->profileInfo["Separation"],
							$this->profileInfo["Name"],
							$this->profileInfo["Scheme"],
							$this->profileInfo["Units"],
							$ingestionObject,
							$sqlError
							);

					$ingestionId = $ingestionObject->getIngestionId();
				}

				if ($result)
				{
					$this->ingestionId = $ingestionObject->getIngestionId();

					$ds = 0;

					foreach ($this->sites as &$site)
					{
						IngestionDataSetRow::Create(
								$this->context->dbcon,
								$this->ingestionId,
								$this->jobSiteId,
								$ds,
								$site["Name"],
								$site["Attribute"],
								$site["Direction"],
								$site["Survey Duration"]["start"]->format("Y-m-d H:i:s"),
								$site["Survey Duration"]["end"]->format("Y-m-d H:i:s"),
								$site["Zone"],
								$site["File"],
								$site["Identifier"],
								$site["Algorithm"],
								$site["Data type"],
								$ingestionDataSet,
								$sqlError
								);

						$ds += 1;
					}

					$dataRows = $this->ParseDataRows();
				}
				else
				{
					if ($sqlError == 1062) // 1062 is ER_DUP_ENTRY
					{
						$resultString = "alreadyexists";
					}
					else
					{
						$resultString = "SQL error $sqlError";
					}
				}

				mysqli_commit($this->context->dbcon);
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function CreateReports(
			$ingestionId,
			$startTime,
			$endTime,
			$reportTypes,
			$reportFormat,
			$reportParameters,
			$destinationDirectory,
			&$reportInfo,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "ingestionId=$ingestionId, startTime=$startTime, endTime=$endTime, reportFormat=$reportFormat");

			ini_set('memory_limit', '1024M');
			ini_set('max_execution_time', 300);

			$result = FALSE;

			// Find the ingestion record
			$ingestion = IngestionRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("ingestionid=$ingestionId"),
					NULL,
					ROW_ASSOCIATIVE
					);

			if ($ingestion == NULL)
			{
				$resultString = "Ingestion not found";
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Make sure it's a valid job, jobsite.
			$commonJobSite = new CommonJobSite($this->context);

			$result = $commonJobSite->GetJobSite(
					$ingestion['jobsiteid'],
					ROW_ASSOCIATIVE,
					$this->jobSite,
					$jobRow,
					$resultString
					);

			if (!$result)
			{
				DBG_ERR(DBGZ_TUBE_JOBSITE, __METHOD__, "GetJobSite failed with resultString='$resultString'");

				DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			$directions = explode("/", $this->jobSite['direction']);

			if ($reportFormat === NULL)
			{
				$reportFormat = $this->jobSite['reportformat'];
			}

			if ($reportParameters === NULL)
			{
				$reportParameters = $this->jobSite['reportparameters'];
			}

			$reportInfo = array();

			$result = TRUE;
			$resultString = "";

			if (count($reportTypes) == 0)
			{
				$resultString = "No report types provided";
				DBG_WARN(DBGZ_TUBE_JOBSITE, __METHOD__, $resultString);
			}

			foreach ($reportTypes as &$reportType)
			{
				$outputFiles = array();

				switch ($reportType)
				{
					case 'volume':
						DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Creating volume report with ingestionId=$ingestionId");

						$reportResult = $this->CreateVolumeReport(
								$ingestion['jobsiteid'],
								$this->jobSite['sitecode'],    // title
								$ingestionId,
								$reportFormat,
								$reportParameters,
								$ingestion['filterbegintime'],
								$ingestion['filterendtime'],
								$startTime,
								$endTime,
								$this->jobSite['sitecode'],    // siteCode
								$this->jobSite['description'], // location
								$directions[0],                // primaryDirection
								$directions[1],                // secondaryDirection
								$destinationDirectory,
								$outputFiles,
								$resultString
								);

						if ($reportResult)
						{
							$reportInfo['volume'] = $outputFiles;
						}

						break;

					case 'class':
						$reportResult = $this->CreateClassReport(
								$ingestion['jobsiteid'],
								$this->jobSite['sitecode'],    // title
								$ingestionId,
								$reportFormat,
								$reportParameters,
								$ingestion['filterbegintime'],
								$ingestion['filterendtime'],
								$startTime,
								$endTime,
								$this->jobSite['sitecode'],    // siteCode
								$this->jobSite['description'], // location
								$directions[0],                // primaryDirection
								$directions[1],                // secondaryDirection
								$destinationDirectory,
								$outputFiles,
								$resultString
								);

						if ($reportResult)
						{
							$reportInfo['class'] = $outputFiles;
						}

						break;

					case 'speed':
						$reportResult = $this->CreateSpeedReport(
								$ingestion['jobsiteid'],
								$this->jobSite['sitecode'],    // title
								$ingestionId,
								$reportFormat,
								$reportParameters,
								$ingestion['filterbegintime'],
								$ingestion['filterendtime'],
								$startTime,
								$endTime,
								$this->jobSite['sitecode'],    // siteCode
								$this->jobSite['description'], // location
								$directions[0],                // primaryDirection
								$directions[1],                // secondaryDirection
								$destinationDirectory,
								$outputFiles,
								$resultString
								);

						if ($reportResult)
						{
							$reportInfo['speed'] = $outputFiles;
						}

						break;

					default:
						break;
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function CreateClassReport(
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
			$destinationDirectory,
			&$outputFiles,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, ingestionId=$ingestionId, reportFormat=$reportFormat, startTime=$startTime, endTime=$endTime");

			$classReport = new ClassReport($this->context);

			$result = $classReport->Create(
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
					$destinationDirectory,
					$outputFiles,
					$resultString
					);

			unset($classReport);

			if ($result)
			{
				// Upload the output files to the bucket.

				// Ensure a unique filename in the bucket by prepending the filename with current time
				$date = date_create();
				$microTimeStamp = microtime(true);
				$timeStamp = floor($microTimeStamp);
				$microSeconds = round(($microTimeStamp - $timeStamp) * 100000);

				foreach ($outputFiles as &$outputFile)
				{
					$bucketFilename = $date->format("Y-m-d H:i:s.$microSeconds")."-".basename($outputFile);

					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Uploading '$outputFile' to the bucket as '$bucketFilename'");

					$result = $this->awsFileManager->UploadFile(
							$destinationDirectory."/".$outputFile,
							$bucketFilename,
							"public-read",
							TRUE,
							$resultString
							);

					// Create a record in the reports table
					$sqlError = 0;

					$result = ReportRow::Create(
							$this->context->dbcon,
							$date->format("Y-m-d H:i:s"),
							$ingestionId,
							$reportFormat,
							$reportParameters,
							$jobSiteId,
							$siteCode,
							$startTime,
							$endTime,
							"class",   // report type
							$bucketFilename,
							$reportObject,
							$sqlError
							);
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function CreateSpeedReport(
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
			$destinationDirectory,
			&$outputFiles,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, ingestionId=$ingestionId, reportFormat=$reportFormat, startTime=$startTime, endTime=$endTime");

			$speedReport = new SpeedReport($this->context);

			$result = $speedReport->Create(
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
					$destinationDirectory,
					$outputFiles,
					$resultString
					);

			unset($speedReport);

			if ($result)
			{
				// Upload the output files to the bucket.

				// Ensure a unique filename in the bucket by prepending the filename with current time
				$date = date_create();
				$microTimeStamp = microtime(true);
				$timeStamp = floor($microTimeStamp);
				$microSeconds = round(($microTimeStamp - $timeStamp) * 100000);

				foreach ($outputFiles as &$outputFile)
				{
					$bucketFilename = $date->format("Y-m-d H:i:s.$microSeconds")."-".basename($outputFile);

					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Uploading '$outputFile' to the bucket as '$bucketFilename'");

					$result = $this->awsFileManager->UploadFile(
							$destinationDirectory."/".$outputFile,
							$bucketFilename,
							"public-read",
							TRUE,
							$resultString
							);

					// Create a record in the reports table
					$sqlError = 0;

					$result = ReportRow::Create(
							$this->context->dbcon,
							$date->format("Y-m-d H:i:s"),
							$ingestionId,
							$reportFormat,
							$reportParameters,
							$jobSiteId,
							$siteCode,
							$startTime,
							$endTime,
							"speed",   // report type
							$bucketFilename,
							$reportObject,
							$sqlError
							);
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function CreateVolumeReport(
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
			$destinationDirectory,
			&$outputFiles,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, ingestionId=$ingestionId, reportFormat=$reportFormat, reportParameters=$reportParameters, startTime=$startTime, endTime=$endTime");

			$volumeReport = new VolumeReport($this->context);

			$result = $volumeReport->Create(
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
					$destinationDirectory,
					$outputFiles,
					$resultString
					);

			unset($volumeReport);

			if ($result)
			{
				// Upload the output files to the bucket.

				// Ensure a unique filename in the bucket by prepending the filename with current time
				$date = date_create();
				$microTimeStamp = microtime(true);
				$timeStamp = floor($microTimeStamp);
				$microSeconds = round(($microTimeStamp - $timeStamp) * 100000);

				foreach ($outputFiles as &$outputFile)
				{
					$bucketFilename = $date->format("Y-m-d H:i:s.$microSeconds")."-".basename($outputFile);

					DBG_INFO(DBGZ_TUBE_JOBSITE, __METHOD__, "Uploading '$outputFile' to the bucket as '$bucketFilename'");

					$result = $this->awsFileManager->UploadFile(
							$destinationDirectory."/".$outputFile,
							$bucketFilename,
							"public-read",
							TRUE,
							$resultString
							);

					// Create a record in the reports table
					$sqlError = 0;

					$result = ReportRow::Create(
							$this->context->dbcon,
							$date->format("Y-m-d H:i:s"),
							$ingestionId,
							$reportFormat,
							$reportParameters,
							$jobSiteId,
							$siteCode,
							$startTime,
							$endTime,
							"volume",   // report type
							$bucketFilename,
							$reportObject,
							$sqlError
							);
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_JOBSITE, __METHOD__, $result);
			return $result;
		}
	}
?>
