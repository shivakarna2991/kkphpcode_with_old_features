<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Classes\JobSite as CommonJobSite;
	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\TaskRow;
	use \Idax\Common\Data\JobSiteRow;

	class Task
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_TASK, __METHOD__, "methodName=$methodName");

			$task = new Task($context);

			$response = "failed";
			$responder = "Task::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'Update':
					$result = $task->Update(
							isset($parameters['taskid']) ? $parameters['taskid'] : NULL,
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
					}
					else
					{
						$response = "failed";
					}

					$returnval = array("resultstring" => $resultString);

					break;

				case 'Delete':
					$result = $task->Delete(
							isset($parameters['taskid']) ? $parameters['taskid'] : NULL,
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

					$result = $task->CreateJobSite(
							isset($parameters['sitecode']) ? $parameters['sitecode'] : NULL,
							isset($parameters['taskid']) ? $parameters['taskid'] : NULL,
							isset($parameters['jobid']) ? $parameters['jobid'] : NULL,
							isset($parameters['latitude']) ? $parameters['latitude'] : NULL,
							isset($parameters['longitude']) ? $parameters['longitude'] : NULL,
							isset($parameters['setupdate']) ? $parameters['setupdate'] : NULL,
							$durations,
							$blockTimes,
							isset($parameters['status']) ? $parameters['status'] : NULL,
							isset($parameters['description']) ? $parameters['description'] : NULL,
							isset($parameters['notes']) ? $parameters['notes'] : NULL,
							isset($parameters['reportformat']) ? $parameters['reportformat'] : NULL,
							isset($parameters['direction']) ? $parameters['direction'] : NULL,
							isset($parameters['oneway']) ? $parameters['oneway'] : NULL,
							isset($parameters['countpriority']) ? $parameters['countpriority'] : NULL,
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
					$result = $task->GetJobSites(
							isset($parameters['taskid']) ? $parameters['taskid'] : NULL,
							$jobSites,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("jobsites" => $jobSites, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "Task";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_TASK, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_TASK, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function Update(
			$taskId,
			$name,
			$setupDate,
			$deviceType,
			$status,
			$assignedTo,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TASK, __METHOD__, "taskId=$taskId, deviceType=$deviceType, status=$status, assignedTo=$assignedTo");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($taskId === NULL)
			{
				$resultString = "Missing parameter 'taskid'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				// Make sure the task exists
				$filter = array("taskid=$taskId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$taskRow = TaskRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($taskRow != NULL)
				{
					// Make sure the job exists and that it is active
					$filter = array("jobid={$taskRow->getJobId()}");

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

					if ($jobRow != NULL)
					{
						if ($jobRow->getActive())
						{
							if ($name !== NULL)
							{
								$taskRow->setName($name);
							}

							if ($setupDate !== NULL)
							{
								$taskRow->setSetupDate($setupDate);
							}

							if ($deviceType !== NULL)
							{
								$taskRow->setDeviceType($deviceType);
							}

							if ($status !== NULL)
							{
								$taskRow->setStatus($status);
							}

							if ($assignedTo !== NULL)
							{
								$taskRow->setAssignedTo($assignedTo);
							}

							$result = $taskRow->CommitChangedFields($sqlError);

							$now = date('Y-m-d H:i:s');

							$jobRow->setLastUpdateTime($now);

							$result = $jobRow->CommitChangedFields($sqlError);
						}
						else
						{
							$resultString = "Job not active";
							DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
					}
					else
					{
						$resultString = "Job not found";
					}

					DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
				}
				else
				{
					$resultString = "Task not found";
				}
			}

			DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$taskId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TASK, __METHOD__, "taskid=$taskId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($taskId === NULL)
			{
				$resultString = "Missing parameter 'taskid'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$now = date("Y-m-d H:i:s");

				$sqlError = 0;

				// Make sure the task exists
				$filter = array("taskid=$taskId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$taskRow = TaskRow::FindOne(
						$this->context->dbcon,
						NULL,
						array("taskid=$taskId"),
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($taskRow != NULL)
				{
					// Make sure the job exists
					$filter = array("jobid={$taskRow->getJobId()}");

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

					if ($jobRow != NULL)
					{
						// Delete any job sites for that were created by this task.
						$jobSiteRows = JobSiteRow::Find(
								$this->context->dbcon,
								NULL,
								array("taskid=$taskId"),
								NULL,
								ROW_OBJECT,
								$sqlError
								);

						if ($jobSiteRows != NULL)
						{
							$commonJobSite = new CommonJobSite($this->context);

							foreach ($jobSiteRows as &$jobSiteRow)
							{
								// Ignore errors, but at least log them
								if (!$commonJobSite->Delete($jobSiteRow->getJobSiteId(), $resultString))
								{
									DBG_ERR(DBGZ_TASK, __METHOD__, "commongJobSite->Delete() failed with resultString='$resultString'");
								}
							}
						}

						$result = TaskRow::Delete(
								$this->context->dbcon,
								array("taskid=$taskId"),
								$sqlError
								);

						if ($result)
						{
							$jobRow->setLastUpdateTime($now);

							$result = $jobRow->CommitChangedFields($sqlError);

							if (!$result)
							{
								$resultString = "SQL Error $sqlError";
								DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
							}
						}
						else
						{
							$resultString = "SQL Error $sqlError";
							DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
					}
					else
					{
						$resultString = "Task not found";
						DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Task not found";
					DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, $result);
			return $result;
		}

		public function CreateJobSite(
			$siteCode,
			$taskId,
			$jobId,
			$latitude,
			$longitude,
			$setupDate,
			$durations,
			$timeBlocks,
			$status,
			$description,
			$notes,
			$reportFormat,
			$direction,
			$oneway,
			$countPriority,
			&$jobsiteObject,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_TASK,
					__METHOD__,
					"siteCode=$siteCode, jobId=$jobId, siteCode=$siteCode, latitude=$latitude, longitude=$longitude, direction=$direction, reportFormat=$reportFormat, blockTimes=".serialize($timeBlocks)
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
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
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($taskId === NULL)
			{
				$resultString = "Missing parameter 'taskid'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($jobId === NULL)
			{
				$resultString = "Missing parameter 'jobid'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($reportFormat === NULL)
			{
				$resultString = "Missing parameter 'reportformat'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				// Make sure the job exists
				$filter = array("taskid=$taskId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$taskRow = TaskRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($taskRow == NULL)
				{
					if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
					}
					else
					{
						$resultString = "Task not found";
					}

					DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
				}
				else
				{
					// Make sure the job exists and that it is active
					$filter = array("jobid=".$taskRow["jobid"]);

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

					if ($jobRow != NULL)
					{
						if ($jobRow->getActive())
						{
							$creationTime = date('Y-m-d H:i:s');

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

							$durationsString = "";
							$timeBlocksString = "";

							foreach ($durations as &$duration)
							{
								$durationsString .= $duration[0].",".$duration[1].";";
							}

							// Remove trailing ";"
							$durationsString = rtrim($durationsString, ";");

							foreach ($timeBlocks as &$timeBlock)
							{
								$timeBlocksString .= $timeBlock[0].",".$timeBlock[1].";";
							}

							// Remove trailing ";"
							$timeBlocksString = rtrim($timeBlocksString, ";");

							// If no setup date is provided, then use the setup date from the task.
							if ($setupDate === NULL)
							{
								$setupDate = $taskRow['setupdate'];
							}

							DBG_INFO(DBGZ_TASK, __METHOD__, "Creating jobsite with setupDate=$setupDate, timeBlocksString=$timeBlocksString");

							$result = JobSiteRow::Create(
									$this->context->dbcon,
									$this->context->account->getAccountId(),
									$siteCode,
									$taskId,
									$jobId,
									$latitude,
									$longitude,
									$creationTime,
									$setupDate,
									$durationsString,
									$timeBlocksString,
									1,               // state
									$status,
									$description,
									$notes,
									$reportFormat,
									$direction,
									$oneway,
									$countPriority,
									$creationTime,   // lastupdatetime
									$jobsiteObject,
									$sqlError
									);

							if ($result)
							{
								$jobRow->setLastUpdateTime($creationTime);

								$result = $jobRow->CommitChangedFields($sqlError);

								if (!$result)
								{
									$resultString = "SQL Error $sqlError";
									DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
								}
							}
							else
							{
								$resultString = "SQL Error $sqlError";
								DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
							}
						}
						else
						{
							$resultString = "Job not active";
							DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
					}
					else
					{
						$resultString = "Job not found";
						DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, $result);
			return $result;
		}

		public function GetJobSites(
			$taskId,
			&$jobSiteRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TASK, __METHOD__, "taskId=$taskId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($taskId === NULL)
			{
				$resultString = "Missing parameter 'taskid'";
				DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("taskid=$taskId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
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
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_TASK, __METHOD__, $resultString);
				}
				else
				{
					$result = TRUE;
					$jobSiteRows = array();
					$resultString = "WARNING: no job sites";
				}
			}

			DBG_RETURN_BOOL(DBGZ_TASK, __METHOD__, $result);
			return $result; 
		}
	}
?>
