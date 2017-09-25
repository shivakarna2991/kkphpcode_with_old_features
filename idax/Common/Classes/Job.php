<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Classes\JobSite as CommonJobSite;
	use \Idax\Common\Classes\Task;
	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\TaskRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Data\File;
	use \Idax\Video\Data\Layout;
        
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
	class Job
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_JOB, __METHOD__, "methodName=$methodName");

			$job = new Job($context);

			$response = "failed";
			$responder = "Job::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'Update':
					$result = $job->Update(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							isset($parameters['number']) ? $parameters['number'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['nickname']) ? $parameters['nickname'] : NULL,
							isset($parameters['studytype']) ? $parameters['studytype'] : NULL,
							isset($parameters['office']) ? $parameters['office'] : NULL,
							isset($parameters['area']) ? $parameters['area'] : NULL,
							isset($parameters['notes']) ? $parameters['notes'] : NULL,
							isset($parameters['orderdate']) ? $parameters['orderdate'] : NULL,
							isset($parameters['deliverydate']) ? $parameters['deliverydate'] : NULL,
							$job,
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
					$response = "success";

					$result = $job->GetJob(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							$jobInfo,
							$resultString
							);

					if ($result) 
					{
						$returnval = array("job" => $jobInfo, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'Delete':
					$result = $job->Delete(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
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

				case 'Close':
					$result = $job->Close(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
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

				case 'CreateJobSite':
					$result = $job->CreateJobSite(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							isset($parameters['sitecode']) ? $parameters['sitecode'] : NULL,
							isset($parameters['latitude']) ? $parameters['latitude'] : NULL,
							isset($parameters['longitude']) ? $parameters['longitude'] : NULL,
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
							//isset($parameters['status']) ? $parameters['status'] : NULL,
							$jobsite,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("jobsiteid" => $jobsite->getJobSiteId(), "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'GetJobSites':
					$response = "success";

					$result = $job->GetJobSites(
							$parameters['jobid'],
							isset($parameters['infolevel']) ? $parameters['infolevel'] : NULL,
							isset($parameters['since']) ? $parameters['since'] : NULL,
							$jobSites,
							$resultString
							);

					if ($result)
					{
						$returnval = array("jobsites" => $jobSites, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'CreateTask':
					$result = $job->CreateTask(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['setupdate']) ? $parameters['setupdate'] : NULL,
							isset($parameters['devicetype']) ? $parameters['devicetype'] : NULL,
							isset($parameters['status']) ? $parameters['status'] : NULL,
							isset($parameters['assignedto']) ? $parameters['assignedto'] : NULL,
							$task,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("taskid" => $task->getTaskId(), "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'GetTasks':
					$response = "success";

					$result = $job->GetTasks(
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							$tasks,
							$resultString
							);

					if ($result) 
					{
						$returnval = array("tasks" => $tasks, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "Job";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_JOB, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_JOB, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function Update(
			$jobId,
			$number,
			$name,
			$nickname,
			$studyType,
			$office,
			$area,
			$notes,
			$orderDate,
			$deliveryDate,
			&$job,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId, number=$number, name=$name, studyType=$studyType, office=$office, area=$area");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$filter = array("jobid=$jobId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				// Make sure the job exists and that it is active
				$jobRow = JobRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($jobRow)
				{
					$updateTime = date("Y-m-d H:i:s");

					if ($number !== NULL)
					{
						$jobRow->setNumber($number);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($name !== NULL)
					{
						$jobRow->setName($name);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($nickname !== NULL)
					{
						$jobRow->setNickname($nickname);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($office !== NULL)
					{
						$jobRow->setOffice($office);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($area !== NULL)
					{
						$jobRow->setArea($area);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($notes !== NULL)
					{
						$jobRow->setNotes($notes);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($studyType !== NULL)
					{
						$jobRow->setStudyType($studyType);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($orderDate !== NULL)
					{
						$jobRow->setOrderDate($orderDate);
						$jobRow->setLastUpdateTime($updateTime);
					}

					if ($deliveryDate !== NULL)
					{
						$jobRow->setDeliveryDate($deliveryDate);
						$jobRow->setLastUpdateTime($updateTime);
					}

					$result = $jobRow->CommitChangedFields($sqlError);

					if (!$result)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Job not found";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function GetInfo(
			$jobId,
			&$job,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;

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
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobRow != NULL)
			{
				$result = TRUE;

				// Get the most recently updated jobsite and return it as lastupdatetime time of the job.
				$jobSiteRow = JobSiteRow::FindOne(
						$this->context->dbcon,
						array("lastupdatetime"),
						$filter,
						"lastupdatetime DESC limit 1",
						ROW_ASSOCIATIVE
						);

				if ($jobSiteRow != NULL)
				{
					$jobRow["lastupdatetime"] = $jobSiteRow["lastupdatetime"];
				}
			}
			else
			{
				$resultString = "Job not found";
				DBG_WARN(DBGZ_JOB, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$jobId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId");

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$taskRows = TaskRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("jobid=$jobId"),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($taskRows != NULL)
				{
					$task = new Task($this->context);

					foreach ($taskRows as &$taskRow)
					{
						$task->Delete($jobSiteRow->getJobSiteId());
					}
				}

				// Delete any job sites for that were created by this job.
				$jobSiteRows = JobSiteRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("jobid=$jobId"),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($jobSiteRows != NULL)
				{
					$commonJobSite = new CommonJobSite($this->context);

					foreach ($jobSiteRows as &$jobSiteRow)
					{
						$commonJobSite->Delete($jobSiteRow->getJobSiteId());
					}
				}

				$result = JobRow::Delete(
						$this->context->dbcon,
						array("jobid=$jobId"),
						$sqlError
						);

				if (!$result)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function Close(
			$jobId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId");

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$now = date("Y-m-d H:i:s");

				$result = JobRow::Update(
						$this->context->dbcon,
						array("active=0", "status=0", "lastupdatetime='$now'"),
						array("jobid=$jobId"),
						$sqlError
						);

				if ($result)
				{
					// Deactivate the tasks and job sites, too.
					$result = TaskRow::Update(
							$this->context->dbcon,
							array("status=0"),
							array("jobid=$jobId"),
							$sqlError
							);

					$result = JobSiteRow::Update(
							$this->context->dbcon,
							array("state=0", "status=0", "lastupdatetime='$now'"),
							array("jobid=$jobId"),
							$sqlError
							);
				}
				else
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function CreateJobSite(
			$jobId,
			$siteCode,
			$latitude,
			$longitude,
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
			&$jobsiteObject,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_JOB,
					__METHOD__,
					"jobId=$jobId, siteCode=$siteCode, latitude=$latitude, longitude=$longitude, description=$description, direction=$direction, reportFormat=$reportFormat"
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Set defaults if no value is specified for optional params.
			if ($oneway === NULL)
			{
				$oneway = FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($siteCode === NULL)
			{
				$resultString = "Missing parameter 'sitecode'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			// else if ($reportFormat === NULL)
			// {
			// 	$resultString = "Missing parameter 'reportformat'";
			// 	DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }
			// else if (($studytype == STUDY_TYPE_ROADWAY) && ($direction === NULL))
			// {
			// 	$resultString = "Missing parameter 'direction'";
			// 	DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }
			// else if (($studytype == STUDY_TYPE_TMC) && ($countPriority === NULL))
			// {
			// 	$resultString = "Missing parameter 'countpriority'";
			// 	DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

			// 	$validParameters = FALSE;
			// }

			if ($validParameters)
			{
				// Make sure the job exists and that it is active
				$filter = array("jobid=$jobId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$jobRow = JobRow::FindOne(
						$this->context->dbcon,
						array("jobid", "active", "studytype", "lastupdatetime"),
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($jobRow == NULL)
				{
					if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
					}
					else
					{
						$resultString = "Job not found";
					}

					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
				else if ($jobRow->getActive() == '0')
				{
					$resultString = "Job not active";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
				else
				{
					$creationTime = date('Y-m-d H:i:s');

					$result = JobSiteRow::Create(
							$this->context->dbcon,
							$this->context->account->getDeveloper(),
							$siteCode,
							0,               // task id
							$jobId,
							$latitude,
							$longitude,
							$creationTime,
							"",              // setup date
							"",              // durations
							"",              // time blocks
							1,               // state
							1,               // status
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
							$creationTime,   // lastupdatetime
							$jobsiteObject,
							$sqlError
							);

					if ($result)
					{
						$jobRow->setLastUpdateTime($creationTime);
						$jobRow->CommitChangedFields($sqlError);
					}
					else
					{
						if ($sqlError == 1062) // 1062 is ER_DUP_ENTRY
						{
							$resultString = "alreadyexists";
						}
						else
						{
							$resultString = "SQL Error $sqlError";
						}
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function GetJobSitesForJob(
			$jobRow,
			$infoLevel,
			$since,
			&$jobSiteRows,
			&$resultString
			)
		{
			$jobId = $jobRow["jobid"];

			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId, infoLevel=$infoLevel, since=$since");

			$result = FALSE;

			$filter = array("jobid=$jobId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			if ($since !== NULL)
			{
				$filter[] = "lastupdatetime>'$since'";
			}

			// Find the job site records
			$sqlError = 0;

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

				$commonJobSite = new CommonJobSite($this->context);

				if ($infoLevel === NULL)
				{
					$infoLevel = INFO_LEVEL_BASIC;
				}

				foreach ($jobSiteRows as &$jobSiteRow)
				{
					// Ignore errors, but at least log them.
					if (!$commonJobSite->GetInfoFromJobSiteRow($jobRow["studytype"], $infoLevel, $jobSiteRow, $resultString))
					{
						DBG_WARN(DBGZ_JOB, __METHOD__, "GetInfoFromJobSiteRow() failed with resultString='$resultString'");
					}
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: no job sites";
				DBG_WARN(DBGZ_JOB, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result; 
		}

		public function GetJobSites(
			$jobId,
			$infoLevel,
			$since,
			&$jobSiteRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId, infoLevel=$infoLevel, since=$since");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Find the job record
			$sqlError = 0;

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
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobRow != NULL)
			{
				$result = $this->GetJobSitesForJob(
						$jobRow,
						$infoLevel,
						$since,
						$jobSiteRows,
						$resultString
						);

				if (!$result)
				{
					DBG_ERR(DBGZ_JOB, __METHOD__, "GetJobSitesForJob() failed with resultString='$resultString'");
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Job not found";
				DBG_WARN(DBGZ_JOB, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result; 
		}

		public function CreateTask(
			$jobId,
			$name,
			$setupDate,
			$deviceType,
			$status,
			$assignedTo,
			&$taskRowObject,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId, name=$name, deviceType=$deviceType, status=$status, assignedTo=$assignedTo");

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($name === NULL)
			{
				$resultString = "Missing parameter 'name'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($setupDate === NULL)
			{
				$resultString = "Missing parameter 'setupdate'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($deviceType === NULL)
			{
				$resultString = "Missing parameter 'devicetype'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($status === NULL)
			{
				$resultString = "Missing parameter 'status'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($assignedTo === NULL)
			{
				$resultString = "Missing parameter 'assignedto'";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				// Make sure the job exists and that it is active
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
						ROW_OBJECT,
						$sqlError
						);

				if ($jobRow == NULL)
				{
					if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
					}
					else
					{
						$resultString = "Job not found";
					}

					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
				else if (!$jobRow->getActive())
				{
					$resultString = "Job not active";
					DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
				}
				else
				{
					$now = date('Y-m-d H:i:s');

					$result = TaskRow::Create(
							$this->context->dbcon,
							$this->context->account->getDeveloper(),
							$jobId,
							$name,
							$setupDate,
							$deviceType,
							$status,
							$assignedTo,
							$taskRowObject,
							$sqlError
							);

					if ($result)
					{
						$jobRow->setLastUpdateTime($now);
						$jobRow->CommitChangedFields($sqlError);
					}
					else
					{
						$resultString = "SQL Error $sqlError";
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result;
		}

		public function GetTasks(
			$jobId,
			&$taskRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_JOB, __METHOD__, "jobId=$jobId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, FALSE);
				return FALSE;
			}

			$filter = array("jobid=$jobId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$taskRows = TaskRow::Find(
					$this->context->dbcon,
					NULL,
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($taskRows != NULL)
			{
				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_JOB, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;
				$resultString = "WARNING: no tasks";
			}

			DBG_RETURN_BOOL(DBGZ_JOB, __METHOD__, $result);
			return $result; 
		}
	}
?>
