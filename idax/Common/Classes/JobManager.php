<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Tube\Classes\JobSite as TubeJobSite;
	use \Idax\Video\Classes\JobSite as VideoJobSite;
	use \Idax\Common\Classes\Job;
	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\TaskRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Data\File;
	use \Idax\Video\Data\Layout;

	class JobManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_JOBMGR, __METHOD__, "methodName=$methodName");

			$jobManager = new JobManager($context);

			$response = "failed";
			$responder = "JobManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateJob':
					$result = $jobManager->CreateJob(
							isset($parameters['number']) ? $parameters['number'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['nickname']) ? $parameters['nickname'] : NULL,
							isset($parameters['studytype']) ? $parameters['studytype'] : NULL,
							isset($parameters['office']) ? $parameters['office'] : "",// NULL,
							isset($parameters['area']) ? $parameters['area'] : "",//NULL,
							isset($parameters['notes']) ? $parameters['notes'] : NULL,
							isset($parameters['orderdate']) ? $parameters['orderdate'] : "",//NULL,
							isset($parameters['deliverydate']) ? $parameters['deliverydate'] : "",//NULL,
							$job,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("jobid" => $job->getJobId());
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'GetJobs':
					$timeStamp = date("Y-m-d H:i:s");

					$studyTypes = array();

					$i = 0;

					while (isset($parameters["studytype_$i"]))
					{
						$studyTypes[] = $parameters["studytype_$i"];

						$i += 1;
					}

					$result = $jobManager->GetJobs(
							isset($parameters['infolevel']) ? $parameters['infolevel'] : NULL,
							isset($parameters['activeonly']) ? boolval($parameters['activeonly']) : NULL,
							$studyTypes,
							isset($parameters['since']) ? $parameters['since'] : NULL,
							$jobs,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("jobs" => $jobs, "timestamp" => $timeStamp, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "JobManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_JOBMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_JOBMGR, __METHOD__);
                        //echo '<pre>'; print_r($response_str); exit;
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function CreateJob(
			$number,
			$name,
			$nickname,
			$studyType,
			$office,
			$area,
			$notes,
			$orderDate,
			$deliveryDate,
			&$jobRowObject,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBMGR, __METHOD__, "number=$number, name=$name, office=$office, area=$area");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($number === NULL)
			{
				$resultString = "Missing parameter 'number'";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($name === NULL)
			{
				$resultString = "Missing parameter 'name'";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($nickname === NULL)
			{
				$resultString = "Missing parameter 'nickname'";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($office === NULL)
			{
				$resultString = "Missing parameter 'office'";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			// else if ($area === NULL)
			// {
			// 	$resultString = "Missing parameter 'area'";
			// 	DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }
			else if ($studyType === NULL)
			{
				$resultString = "Missing parameter 'studytype'";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			// else if ($orderDate === NULL)
			// {
			// 	$resultString = "Missing parameter 'orderdate'";
			// 	DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }
			// else if ($deliveryDate === NULL)
			// {
			// 	$resultString = "Missing parameter 'deliverydate'";
			// 	DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }

			if ($validParameters)
			{
				$creationTime = date('Y-m-d H:i:s');

				// Set defaults for unspecifed parameters
				$result = JobRow::Create(
						$this->context->dbcon,
						$this->context->account->getDeveloper(),
						$number,
						$name,
						$nickname,
						$studyType,
						$office,
						$area,
						$notes,
						1,               // active
						1,               // status.  TODO: should this be a required parameter?  Or what should initial value be?
						$creationTime,
						$orderDate,
						$deliveryDate,
						$creationTime,   // user creationTime as lastupdatetime
						$jobRowObject,
						$sqlError
						);

				if (!$result)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, $result);
			return $result;
		}

		public function GetJobs(
			$infoLevel,
			$activeOnly,
			$studyTypes,
			$since,
			&$jobRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOBMGR, __METHOD__, "infoLevel=$infoLevel, activeOnly=$activeOnly, since=$since");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOBMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$filter = array();

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			if (count($studyTypes) != 0)
			{
				$filter[] = "studytype IN (".implode(',', $studyTypes).")";
			}

			if ($activeOnly === NULL)
			{
				$activeOnly = TRUE;
			}

			$filter[] = "active=".intval($activeOnly);

			if ($since !== NULL)
			{
				$filter[] = "lastupdatetime>'$since'";
			}

			$sqlError = 0;

			$jobRows = JobRow::Find(
					$this->context->dbcon,
					NULL,
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobRows != NULL)
			{
				$result = TRUE;

				$job = new Job($this->context);

				foreach ($jobRows as &$jobRow)
				{
					$jobRow["jobsites"] = NULL;

					//
					// Set since to "0000-00-00 00:00:00" so we get all info for the job.
					//
					// Ignore any errors that occur so that we can continue the loop and look for additional jobs.
					//
					if ($job->GetJobSitesForJob($jobRow, $infoLevel, "0000-00-00 00:00:00", $jobSiteRows, $jobSitesResultString))
					{
						$jobRow["jobsites"] = $jobSiteRows;
					}
				}
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: no jobs found";
				DBG_WARN(DBGZ_JOBMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOBMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
