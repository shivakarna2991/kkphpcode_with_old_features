<?php

	namespace Idax\Common\Classes;

	require_once 'idax/idax.php';

	use \Idax\Common\Data\JobRow;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Data\FileRow;

	class IdaxManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_IDAXMGR, __METHOD__, "methodName=$methodName");

			$idaxManager = new IdaxManager($context);

			$response = "failed";
			$responder = "IdaxManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'GetVideoInfo':
					$timeStamp = date("Y-m-d H:i:s");

					$result = $idaxManager->GetVideoInfo(
							isset($parameters['since']) ? $parameters['since'] : NULL,
							$videoFileRows,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("timestamp" => $timeStamp, "videofiles" => $videoFileRows, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "IdaxManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_IDAXMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array (
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_IDAXMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function GetVideoInfo(
			$since,
			&$videoFileRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_IDAXMGR, __METHOD__);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_IDAXMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_IDAXMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_USER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_IDAXMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_IDAXMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;

			$filter = array("lastupdatetime>'$since'", "active=1", "studytype=".STUDY_TYPE_TMC." OR studytype=".STUDY_TYPE_ADT);

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$jobRows = JobRow::Find(
					$this->context->dbcon,
					array("jobid", "number", "name", "nickname", "office", "lastupdatetime"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($jobRows)
			{
				$result = TRUE;

				$jobIds = array();

				foreach ($jobRows as &$jobRow)
				{
					$jobIds[] = $jobRow["jobid"];
				}

				$filter = array("lastupdatetime>'$since'", "jobid IN (".implode(",", $jobIds).")");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$jobSiteRows = JobSiteRow::Find(
						$this->context->dbcon,
						array("jobid", "jobsiteid", "sitecode", "description", "countpriority", "lastupdatetime"),
						$filter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($jobSiteRows)
				{
					$result = TRUE;

					$jobSiteIds = array();

					foreach ($jobSiteRows as &$jobSiteRow)
					{
						$jobSiteIds[] = $jobSiteRow["jobsiteid"];
					}

					$filter = array("lastupdatetime>'$since'", "status='ready'", "jobsiteid IN (".implode(",", $jobSiteIds).")");

					if (!$this->context->account->getDeveloper())
					{
						$filter[] = "testdata=0";
					}

					$videoFileRows = FileRow::Find(
							$this->context->dbcon,
							array("bucketfileprefix", "lastupdatetime"),
							$filter,
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					foreach ($videoFileRows as &$videoFileRow)
					{
						$videoFileRow['bucketfilename'] = $videoFileRow['bucketfileprefix'].".m3u8";
						unset($videoFileRow["bucketfileprefix"]);
						unset($videoFileRow["videoid"]);
					}
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_IDAXMGR, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "WARNING: No jobs found";
				DBG_ERR(DBGZ_IDAXMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_IDAXMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
