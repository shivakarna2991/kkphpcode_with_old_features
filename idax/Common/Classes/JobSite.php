<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Classes\Direction;
	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Tube\Classes\JobSite as TubeJobSite;
	use \Idax\Tube\Data\Ingestion;
	use \Idax\Tube\Data\IngestionDataSet;
	use \Idax\Tube\Data\IngestionData;
	use \Idax\Video\Classes\JobSite as VideoJobSite;

	class JobSite
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "methodName=$methodName");

			$jobsite = new JobSite($context);

			$response = "failed";
			$responder = "JobSite::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'Update':
					$durations = array();
					$blockTimes = array();

					$i = 0;

					while (isset($parameters["duration_start_$i"]))
					{
						$durations[] = array($parameters["duration_start_$i"], $parameters["duration_end_$i"]);
						$i += 1;
					}

					$i = 0;

					while (isset($parameters["timeblock_start_$i"]))
					{
						$blockTimes[] = array($parameters["timeblock_start_$i"], $parameters["timeblock_end_$i"]);
						$i += 1;
					}

					$result = $jobsite->Update(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['sitecode']) ? $parameters['sitecode'] : NULL,
							isset($parameters['latitude']) ? $parameters['latitude'] : NULL,
							isset($parameters['longitude']) ? $parameters['longitude'] : NULL,
							isset($parameters['setupdate']) ? $parameters['setupdate'] : NULL,
							$durations,
							$blockTimes,
							isset($parameters['status']) ? $parameters['status'] : NULL,
							isset($parameters['description']) ? $parameters['description'] : NULL,
							isset($parameters['notes']) ? $parameters['notes'] : NULL,
							isset($parameters['n_street']) ? $parameters['n_street'] : NULL,
							isset($parameters['s_street']) ? $parameters['s_street'] : NULL,
							isset($parameters['e_street']) ? $parameters['e_street'] : NULL,
							isset($parameters['w_street']) ? $parameters['w_street'] : NULL,
							isset($parameters['ne_street']) ? $parameters['ne_street'] : NULL,
							isset($parameters['nw_street']) ? $parameters['nw_street'] : NULL,
							isset($parameters['se_street']) ? $parameters['se_street'] : NULL,
							isset($parameters['sw_street']) ? $parameters['sw_street'] : NULL,
							isset($parameters['direction']) ? $parameters['direction'] : NULL,
							isset($parameters['oneway']) ? $parameters['oneway'] : NULL,
							isset($parameters['countpriority']) ? $parameters['countpriority'] : NULL,
							isset($parameters['reportformat']) ? $parameters['reportformat'] : NULL,
							isset($parameters['reportparameters']) ? $parameters['reportparameters'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'GetInfo':
					$jobSite = $jobsite->GetInfo(
							$parameters['jobsiteid'],
							$parameters['infolevel'],
							$jobInfo,
							$jobSiteInfo,
							$resultString
							);

					if ($jobSite)
					{
						$response = "success";
						$returnval = array(
								"jobinfo" => $jobInfo,
								"jobsiteinfo" => $jobSiteInfo,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'Delete':
					$result = $jobsite->Delete(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'AssignDevice':
					$result = $jobsite->AssignDevice(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'UnassignDevice':
					$result = $jobsite->UnassignDevice(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['deviceid']) ? $parameters['deviceid'] : NULL,
							$resultString
							);

					if ($result)
					{
						$response = "success";
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				default:
					$response = "failed";
					$responder = "JobSite";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_JOBSITE, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_JOBSITE, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function GetJobSite(
			$jobSiteId,
			$resultType,
			&$jobSiteRow,
			&$jobRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			$sqlError = 0;

			$filter = array("jobsiteid=$jobSiteId");
                        //echo '$jobSiteId';print_r($jobSiteId);exit;
			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$jobSiteRow = JobSiteRow::FindOne(
					$this->context->dbcon,
					NULL,
					$filter,
					NULL,
					$resultType,
					$sqlError
					);
                        //echo '$jobSiteRow';print_r($jobSiteRow);exit;
			if ($jobSiteRow != NULL)
			{
				$jobId = ($resultType == ROW_OBJECT) ? $jobSiteRow->getJobId() : $jobSiteRow['jobid'];

				$filter = array("jobid=$jobId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$jobRow = JobRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						$resultType,
						$sqlError
						);

				if ($jobRow != NULL)
				{
					$result = TRUE;
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Job not found";
					DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Job site not found";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function Update(
			$jobSiteId,
			$siteCode,
			$latitude,
			$longitude,
			$setupDate,
			$durations,
			$timeBlocks,
			$status,
			$description,
			$notes,
			$n_street,
			$s_street,
			$e_street,
			$w_street,
			$ne_street,
			$nw_street,
			$se_street,
			$sw_street,
			$direction,
			$oneway,
			$countPriority,
			$reportFormat,
			$reportParameters,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			$result = $this->GetJobSite(
					$jobSiteId,
					ROW_OBJECT,
					$jobSiteRow,
					$jobRow,
					$resultString
					);

			if ($result)
			{
				$commitChanges = FALSE;

				$updateTime = date("Y-m-d H:i:s");

				if ($siteCode !== NULL)
				{
					$jobSiteRow->setSiteCode($siteCode);
					$commitChanges = TRUE;
				}

				if ($latitude !== NULL)
				{
					$jobSiteRow->setLatitude($latitude);
					$commitChanges = TRUE;
				}

				if ($longitude !== NULL)
				{
					$jobSiteRow->setLongitude($longitude);
					$commitChanges = TRUE;
				}

				if ($setupDate !== NULL)
				{
					$jobSiteRow->setSetupDate($setupDate);
					$commitChanges = TRUE;
				}

				//
				// Format of durations and timeblocks strings is time ranges.
				//
				// Each range has start and end date/time separated by a ','.
				//     -Example duration date range: 2016-10-13,2016-10-15
				//     -Example: time block time range 05:00,07:00
				//
				// Each range is separated by a ';'.
				//     -Example duration with two ranges: 2016-10-13,2016-10-15;2016-10-16,2016-10-20
				//     -Example time block with two ranges: 05:00,07:00;16:00,18:00
				//

				if (count($durations) > 0)
				{
					$durationsString = "";

					foreach ($durations as &$duration)
					{
						$durationsString .= $duration[0].",".$duration[1].";";
					}

					// Remove trailing ";"
					$durationsString = rtrim($durationsString, ";");

					$jobSiteRow->setDurations($durationsString);
					$commitChanges = TRUE;
				}

				if (count($timeBlocks) > 0)
				{
					$timeBlocksString = "";

					foreach ($timeBlocks as &$timeBlock)
					{
						$timeBlocksString .= $timeBlock[0].",".$timeBlock[1].";";
					}

					// Remove trailing ";"
					$timeBlocksString = rtrim($timeBlocksString, ";");

					$jobSiteRow->setTimeBlocks($timeBlocksString);
					$commitChanges = TRUE;
				}

				if ($status !== NULL)
				{
					$jobSiteRow->setStatus($status);
					$commitChanges = TRUE;
				}

				if ($description !== NULL)
				{
					$jobSiteRow->setDescription($description);
					$commitChanges = TRUE;
				}

				if ($notes !== NULL)
				{
					$jobSiteRow->setNotes($notes);
					$commitChanges = TRUE;
				}

				if ($n_street !== NULL)
				{
					$jobSiteRow->setNStreet($n_street);
					$commitChanges = TRUE;
				}

				if ($s_street !== NULL)
				{
					$jobSiteRow->setSStreet($s_street);
					$commitChanges = TRUE;
				}

				if ($e_street !== NULL)
				{
					$jobSiteRow->setEStreet($e_street);
					$commitChanges = TRUE;
				}

				if ($w_street !== NULL)
				{
					$jobSiteRow->setWStreet($w_street);
					$commitChanges = TRUE;
				}

				if ($ne_street !== NULL)
				{
					$jobSiteRow->setNEStreet($ne_street);
					$commitChanges = TRUE;
				}

				if ($nw_street !== NULL)
				{
					$jobSiteRow->setNWStreet($nw_street);
					$commitChanges = TRUE;
				}

				if ($se_street !== NULL)
				{
					$jobSiteRow->setSEStreet($se_street);
					$commitChanges = TRUE;
				}

				if ($sw_street !== NULL)
				{
					$jobSiteRow->setSWStreet($sw_street);
					$commitChanges = TRUE;
				}

				if ($description !== NULL)
				{
					$jobSiteRow->setDescription($description);
					$commitChanges = TRUE;
				}

				if ($direction !== NULL)
				{
					$jobSiteRow->setDirection($direction);
					$commitChanges = TRUE;
				}

				if ($oneway !== NULL)
				{
					$jobSiteRow->setOneWay($oneway);
					$commitChanges = TRUE;
				}

				if ($countPriority !== NULL)
				{
					$jobSiteRow->setCountPriority($countPriority);
					$commitChanges = TRUE;
				}

				if ($reportFormat !== NULL)
				{
					$jobSiteRow->setReportFormat($reportFormat);
					$commitChanges = TRUE;
				}

				if ($reportParameters !== NULL)
				{
					$jobSiteRow->setReportParameters($reportParameters);
					$commitChanges = TRUE;
				}

				if ($commitChanges)
				{
					$jobRow->setLastUpdateTime($updateTime);
					$result = $jobRow->CommitChangedFields($sqlError);

					if ($result)
					{
						$jobSiteRow->setLastUpdateTime($updateTime);
						$result = $jobSiteRow->CommitChangedFields($sqlError);

						if (!$result)
						{
							$resultString = "SQL Error $sqlError";
							DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
						}
					}
					else
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
				}
			}
			else
			{
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, "GetJobSite() failed with resultString='$resultString'");

				DBG_RETURN(DBGZ_JOBSITE, __METHOD__);
				return FALSE;
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function GetInfoFromJobSiteRow(
			$studyType,
			$infoLevel,
			&$jobSiteRow,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "studyType=$studyType, infoLevel=$infoLevel");

			$result = TRUE;

			//
			// Convert durations and timeblocks to arrays
			//
			//
			// Format of durations and timeblocks strings is time ranges.
			//
			// Each range has start and end date/time separated by a ','.
			//     -Example duration date range: 2016-10-13,2016-10-15
			//     -Example: time block time range 05:00,07:00
			//
			// Each range is separated by a ';'.
			//     -Example duration with two ranges: 2016-10-13,2016-10-15;2016-10-16,2016-10-20
			//     -Example time block with two ranges: 05:00,07:00;16:00,18:00
			//
			$durations = explode(";", $jobSiteRow['durations']);
			$durationElements = array();

			foreach ($durations as &$duration)
			{
				$durationComponents = explode(",", $duration);

				if (count($durationComponents) == 2)
				{
					$durationElements[] = array("start" => $durationComponents[0], "end" => $durationComponents[1]);
				}
			}

			$jobSiteRow['durations'] = $durationElements;

			$timeBlocks = explode(";", $jobSiteRow['timeblocks']);
			$timeBlockElements = array();

			foreach ($timeBlocks as &$timeBlock)
			{
				$timeBlockComponents = explode(",", $timeBlock);

				if (count($timeBlockComponents) == 2)
				{
					$timeBlockElements[] = array("start" => $timeBlockComponents[0], "end" => $timeBlockComponents[1]);
				}
			}

			$jobSiteRow['timeblocks'] = $timeBlockElements;

			//
			// Get additional jobsite info based on the type.
			//
			if ($infoLevel != INFO_LEVEL_BASIC)
			{
				if ($studyType == STUDY_TYPE_ROADWAY)
				{
					$tubeJobSite = new TubeJobSite($this->context);

					$result = $tubeJobSite->GetInfo($jobSiteRow['jobsiteid'], $infoLevel, $tubeData, $resultString);

					if ($result)
					{
						$jobSiteRow['tubedata'] = $tubeData;
					}
				}
				else if (($studyType == STUDY_TYPE_TMC)
						|| ($studyType == STUDY_TYPE_ADT))
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetInfo($jobSiteRow['jobsiteid'], $infoLevel, $videoData, $resultString);

					if ($result)
					{
						$jobSiteRow['videodata'] = $videoData;
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function GetInfo(
			$jobSiteId,
			$infoLevel,
			&$jobInfo,
			&$jobSiteInfo,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, infoLevel=$infoLevel");
                       // echo 'jobSiteId='.$jobSiteId;exit;
			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId === NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$result = $this->GetJobSite(
						$jobSiteId,
						ROW_ASSOCIATIVE,
						$jobSiteRow,
						$jobRow,
						$resultString
						);
                                                //echo '$resultdsdsd';print_r($result);exit;
				if (!$result)
				{
					DBG_ERR(DBGZ_JOBSITE, __METHOD__, "GetJobSite() failed with resultString='$resultString'");

					DBG_RETURN(DBGZ_JOBSITE, __METHOD__);
					return FALSE;
				}

				if ($infoLevel === NULL)
				{
					$infoLevel = INFO_LEVEL_BASIC;
				}

				$result = $this->GetInfoFromJobSiteRow($jobRow["studytype"], $infoLevel, $jobSiteRow, $resultString);

				$jobInfo = $jobRow;
				$jobSiteInfo = $jobSiteRow;
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$jobSiteId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId === NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$result = $this->GetJobSite(
						$jobSiteId,
						ROW_OBJECT,
						$jobSiteRow,
						$jobRow,
						$resultString
						);

				if ($result)
				{
					$updateTime = date("Y-m-d H:i:s");

					if ($jobRow->getStudyType() == STUDY_TYPE_TMC)
					{
						$videoJobSite = new VideoJobSite($this->context);
						$videoJobSite->Delete($jobSiteRow->getJobSiteId(), $resultString);
					}
					else if ($jobRow->getStudyType() == STUDY_TYPE_ROADWAY)
					{
						$tubeJobSite = new TubeJobSite($this->context);
						$tubeJobSite->Delete($jobSiteRow->getJobSiteId(), $resultString);
					}

					$result = JobSiteRow::Delete(
							$this->context->dbcon,
							array("jobsiteid=$jobSiteId"),
							$sqlError
							);

					if ($result)
					{
						$jobRow->setLastUpdateTime($updateTime);
						$result = $jobRow->CommitChangedFields($sqlError);

						if (!$result)
						{
							$resultString = "SQL Error $sqlError";
							DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
						}
					}
					else
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
				}
				else
				{
					DBG_ERR(DBGZ_JOBSITE, __METHOD__, "GetJobSite() failed with resultString='$resultString'");
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function AssignDevice(
			$jobSiteId,
			$deviceId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, deviceId=$deviceId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId === NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$result = $this->GetJobSite(
						$jobSiteId,
						ROW_OBJECT,
						$jobSiteRow,
						$jobRow,
						$resultString
						);

				if ($result)
				{
					$deviceRow = DeviceRow::FindOne(
							$this->context->dbcon,
							NULL,
							array("deviceid=$deviceId"),
							NULL,
							ROW_OBJECT,
							$sqlError
							);

					if ($deviceRow != NULL)
					{
						$deviceIds = explode(",", $jobSiteRow->getDeviceIds());

						if (!$deviceRow->getDeployed())
						{
							$sqlError = 0;

							$deviceRow->setDeployed(TRUE);
							$deviceRow->setJobSiteId($jobSiteId);
							$result = $deviceRow->CommitChangedFields($sqlError);

							if ($result)
							{
								$deviceIds[] = $deviceId;

								$jobSiteRow->setDeviceIds(implode(",", $deviceIds));
								$result = $jobSiteRow->CommitChangedFields($sqlError);

								if (!$result)
								{
									$resultString = "SQL error $sqlError";
									DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
								}
							}
							else
							{
								$resultString = "SQL error $sqlError";
								DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
							}
						}
						else
						{
							// If this device is already assigned to this job site, then we'll return success.
							if (in_array($deviceId, $deviceIds))
							{
								$result = TRUE;
							}
							else
							{
								$resultString = "Device already deployed to job site $deviceRow->getJobSiteId()";
								DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
							}
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
					else
					{
						$resultString = "Device not found";
						DBG_WARN(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function UnassignDevice(
			$jobSiteId,
			$deviceId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, deviceId=$deviceId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId === NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$result = $this->GetJobSite(
						$jobSiteId,
						ROW_OBJECT,
						$jobSiteRow,
						$jobRow,
						$resultString
						);

				if ($result)
				{
					$deviceRow = DeviceRow::FindOne(
							$this->context->dbcon,
							NULL,
							array("deviceid=$deviceId"),
							NULL,
							ROW_OBJECT,
							$sqlError
							);

					if ($deviceRow != NULL)
					{
						$deviceIds = explode(",", $jobSiteRow->getDeviceIds());

						// Make sure the device is actually assigned to this job site.
						if (in_array($deviceId, $deviceIds))
						{
							$sqlError = 0;

							$deviceRow->setDeployed(FALSE);
							$deviceRow->setJobSiteId(0);
							$result = $deviceRow->CommitChangedFields($sqlError);

							if ($result)
							{
								// Remove the deviceid from the job site deviceids
								unset($deviceIds[$deviceId]);

								$jobSiteRow->setDeviceIds(implode(",", $deviceIds));
								$result = $jobSiteRow->CommitChangedFields($sqlError);

								if (!$result)
								{
									$resultString = "SQL error $sqlError";
									DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
								}
							}
							else
							{
								$resultString = "SQL error $sqlError";
								DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
							}
						}
						else
						{
							$resultString = "Device not assigned to this job site";
							DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
					else
					{
						$resultString = "Device not found";
						DBG_WARN(DBGZ_JOBSITE, __METHOD__, $resultString);
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, $result);
			return $result;
		}
	}
?>
