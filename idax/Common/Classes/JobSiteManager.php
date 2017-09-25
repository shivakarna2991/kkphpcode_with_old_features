<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use Idax\Common\Classes\JobSite;
	use Idax\Common\Data\JobRow;
	use Idax\Common\Data\JobSiteRow;

	class JobSiteManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
			DBG_ENTER(DBGZ_JOBSITEMGR, __METHOD__, "methodName=$methodName");

			$jobsiteManager = new JobSiteManager($context);

			$response = "failed";
			$responder = "JobSiteManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'GetJobSites':
					$timeStamp = date("Y-m-d H:i:s");

					$studyTypes = array();

					$i = 0;

					while (isset($parameters["studytype_$i"]))
					{
						$studyTypes[] = $parameters["studytype_$i"];

						$i += 1;
					}

					$keywords = array();

					$i = 0;

					while (isset($parameters["keyword_$i"]))
					{
						$keywords[] = $parameters["keyword_$i"];
						$i += 1;
					}

					$result = $jobsiteManager->GetJobSites(
							isset($parameters['infolevel']) ? $parameters['infolevel'] : NULL,
							$studyTypes,
							isset($parameters['activeonly']) ? boolval($parameters['activeonly']) : NULL,
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							isset($parameters['taskid']) ? $parameters['taskid'] : NULL,
							$keywords,
							isset($parameters['nwlatitude']) ? $parameters['nwlatitude'] : NULL,
							isset($parameters['nwlongitude']) ? $parameters['nwlongitude'] : NULL,
							isset($parameters['selatitude']) ? $parameters['selatitude'] : NULL,
							isset($parameters['selongitude']) ? $parameters['selongitude'] : NULL,
							isset($parameters['since']) ? $parameters['since'] : NULL,
							$jobSites,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("jobsites" => $jobSites, "timestamp" => $timeStamp, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "JobSiteManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_JOBSITEMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_JOBSITEMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function GetJobSites(
			$infoLevel,
			$studyTypes,
			$activeOnly,
			$jobId,
			$taskId,
			$keywords,
			$nwLatitude,
			$nwLongitude,
			$seLatitude,
			$seLongitude,
			$since,
			&$jobSiteRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITE, __METHOD__, "activeOnly=$activeOnly, since=$since, infoLevel=$infoLevel");

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			if ($infoLevel === NULL)
			{
				$infoLevel = INFO_LEVEL_BASIC;
			}

			// Set job search filter based on specified parameters
			$jobFilter = array();

			if (!$this->context->account->getDeveloper())
			{
				$jobFilter[] = "testdata=0";
			}

			if (count($studyTypes) != 0)
			{
				$jobFilter[] = "studytype IN (".implode(',', $studyTypes).")";
			}

			if ($activeOnly !== NULL)
			{
				$jobFilter[] = "active=$activeOnly";
			}

			if ($jobId !== NULL)
			{
				$jobFilter[] = "jobid=$jobId";
			}

			$jobRows = JobRow::Find(
					$this->context->dbcon,
					NULL,
					$jobFilter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobRows != NULL)
			{
				// Set job site search filter based on specified parameters
				$jobSiteFilter = array();

				$jobIds = array();

				foreach ($jobRows as &$jobRow)
				{
					$jobIds[] = $jobRow["jobid"];
				}

				$jobSiteFilter[] = "jobid IN (".implode(',', $jobIds).")";

				if (!$this->context->account->getDeveloper())
				{
					$jobSiteFilter[] = "testdata=0";
				}

				if ($taskId !== NULL)
				{
					$jobSiteFilter[] = "taskid=$taskId";
				}

				if (count($keywords) > 0)
				{
					// Remove duplicate keywords, punctuation, etc.
					$keywords = implode(" ", array_unique(str_word_count(implode(" ", $keywords), 1)));

					$jobSiteFilter[] = "MATCH(sitecode, description, notes) AGAINST ('$keywords')";
				}

				if (($nwLatitude !== NULL) && ($nwLongitude !== NULL)
						&& ($seLatitude !== NULL) && ($seLongitude !== NULL))
				{
					$jobSiteFilter[] = "latitude>=$seLatitude";
					$jobSiteFilter[] = "latitude<=$nwLatitude";
					$jobSiteFilter[] = "longitude<=$seLongitude";
					$jobSiteFilter[] = "longitude>=$nwLongitude";
				}

				if ($since !== NULL)
				{
					$jobSiteFilter[] = "lastupdatetime>'$since'";
				};

				$jobSiteRows = JobSiteRow::Find(
						$this->context->dbcon,
						NULL,
						$jobSiteFilter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($jobSiteRows != NULL)
				{
					$jobSite = new JobSite($this->context);

					foreach ($jobSiteRows as &$jobSiteRow)
					{
						// Meed to know the study type of the jobsite's job
						$studyType = NULL;

						foreach ($jobRows as &$jobRow)
						{
							if ($jobSiteRow["jobid"] == $jobRow["jobid"])
							{
								$studyType = $jobRow["studytype"];
								break;
							}
						}

						if ($studyType != NULL)
						{
							$jobSiteInfo = $jobSite->GetInfoFromJobSiteRow($studyType, $infoLevel, $jobSiteRow, $resultString);

							if ($jobSiteInfo != NULL)
							{
								$jobSiteRow['info'] = $jobSiteInfo;
							}
						}
						else
						{
							//...
						}
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITE, __METHOD__, TRUE);
			return TRUE;
		}

		public function Search(
			$taskId,       // if specified, search job sites that have this task id.
			$jobId,        // if specified, search job sites that have this job id.
			$keywords,     // if specified, search only job sites with these keywords in the site code, descriptio or, notes
			$studyType,    // if specified, search only job sites that have this study type.
			$status,       // if specified, search only job sites that have this status.
			$nwLatitude,   // top-left corner of search area (NW latitude)
			$nwLongitude,  // top-left corner of search area (NW longitude)
			$seLatitude,   // bottom-right corner of search area (SE latitude)
			$seLongitude,  // bottom-right corner of search area (SE longitude),
			&$jobSiteRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBSITEMGR, __METHOD__, "taskId=$taskId, jobId=$jobId, keywords=$keywords");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_JOBSITEMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBSITEMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$filter = array();

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			if ($taskId !== NULL)
			{
				$filter[] = "taskid=$taskId";
			}

			if ($jobId !== NULL)
			{
				$filter[] = "jobid=$jobId";
			}

			if ($studyType !== NULL)
			{
				$filter[] = "studytype=$studyType";
			}

			if ($keywords !== NULL)
			{
				// Remove duplicate keywords, punctuation, etc.
				$keywords = implode(" ", array_unique(str_word_count($keywords, 1)));

				$filter[] = "MATCH(sitecode, description, notes) AGAINST ('$keywords')";
			}

			if ($status !== NULL)
			{
				$filter[] = "status=$status";
			}

			if (($nwLatitude !== NULL) && ($nwLongitude !== NULL)
					&& ($seLatitude !== NULL) && ($seLongitude !== NULL))
			{
				$filter[] = "latitude>=$seLatitude";
				$filter[] = "latitude<=$nwLatitude";
				$filter[] = "longitude<=$seLongitude";
				$filter[] = "longitude>=$nwLongitude";
			}

			$jobSiteRows = JobSiteRow::Find(
					$this->context->dbcon,
					NULL,
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobSiteRows != NULL)
			{
				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_JOBSITEMGR, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: no job sites found";
				DBG_WARN(DBGZ_JOBSITEMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOBSITEMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
