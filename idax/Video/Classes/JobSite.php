<?php
	namespace Idax\Video\Classes;

	require_once 'idax/idax.php';
	require_once 'core/AsyncCall.php';
	require_once 'core/emailutils.php';

	use \Core\Common\Data\AccountRow;
	use \Core\Common\Classes\MethodCallContext;
	use \Core\Common\Classes\AWSFileManager;
	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Data\JobQueueRow;

	use \Idax\Common\Classes\JobSite as CommonJobSite;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Classes\Layout;
	use \Idax\Video\Data\FileRow;
	use \Idax\Video\Data\IngestionPhaseRow;
	use \Idax\Video\Data\LayoutRow;
	use \Idax\Video\Data\LayoutLegRow;
	use \Idax\Video\Data\CountRow;

	define("FFMPEG", "idax/vendor/ffmpeg/ffmpeg");
	define("FFPROBE", "idax/vendor/ffmpeg/ffprobe");

	function isRunning(
		$pid
		)
	{
		try
		{
			$result = shell_exec(sprintf('ps %d', $pid));

			if (count(preg_split("/\n/", $result)) > 2)
			{
				return true;
			}
		}
		catch (Exception $e)
		{
		}

		return false;
	}

	// substring prefix search
	function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}

	// validate the stream via it's playlist file
	function enumeratePlaylistSegments(
		$awsFileManager,
		$playlistFilename,
		$callback
		)
	{
		DBG_ENTER(DBGZ_VIDEO_JOBSITE, __FUNCTION__, "playlistFilename=$playlistFilename");

		$result = $awsFileManager->GetFile($playlistFilename, $fileContents);

		if (!$fileContents)
		{
			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __FUNCTION__, FALSE);
			return FALSE;
		}
		else
		{
			$content = strtok($fileContents, "\r\n");

			while ($content !== false)
			{
				if (startsWith($content, "#EXTINF"))
				{
					// And remove trailing "," from duration string
					$segmentduration = (float)substr(rtrim($content, ","), strlen("#EXTINF:"));
					$segmentfile = strtok("\r\n");

					$callback($awsFileManager, $segmentfile);
				}

				$content = strtok("\r\n");
			}
		}

		DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __FUNCTION__, TRUE);
		return TRUE;
	}

	class JobSite
	{
		private $context = NULL;
		private $commonJobSite = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "methodName=$methodName");

			$jobSite = new JobSite($context);

			$response = "failed";
			$responder = "JobSite::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'GetInfo':
					$response = "success";

					$result = $jobSite->GetInfo(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['infolevel']) ? $parameters['infolevel'] : NULL,
							$jobSiteInfo,
							$resultString
							);

					if ($result) 
					{
						$returnval = array("jobinfo" => $jobSiteInfo, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'CreateLayout':
					$legs = array();

					$legindex = 0;

					while (isset($parameters["leg_{$legindex}"]))
					{
						$legs[] = array(
								'index' => $legindex,
								'direction' => (isset($parameters["leg_{$legindex}_direction"])) ? $parameters["leg_{$legindex}_direction"] : "",
								'leg_pos' => (isset($parameters["leg_{$legindex}_leg_pos"])) ? $parameters["leg_{$legindex}_leg_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'lturn_pos' => (isset($parameters["leg_{$legindex}_lturn_pos"])) ? $parameters["leg_{$legindex}_lturn_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'rturn_pos' => (isset($parameters["leg_{$legindex}_rturn_pos"])) ? $parameters["leg_{$legindex}_rturn_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'uturn_pos' => (isset($parameters["leg_{$legindex}_uturn_pos"])) ? $parameters["leg_{$legindex}_uturn_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'straight_pos' => (isset($parameters["leg_{$legindex}_straight_pos"])) ? $parameters["leg_{$legindex}_straight_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'ped_pos' => (isset($parameters["leg_{$legindex}_ped_pos"])) ? $parameters["leg_{$legindex}_ped_pos"] : "0.0,0.0,0.0,0.0,0.0"
								);

						$legindex += 1;
					}

					$result = $layoutManager->CreateLayout(
							isset($parameters['videoid']) ? $parameters['videoid'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['rating']) ? $parameters['rating'] : NULL,
							isset($parameters['videospeed']) ? $parameters['videospeed'] : NULL,
							isset($parameters['lastvideoposition']) ? $parameters['lastvideoposition'] : NULL,
							$legs,
							$layoutObject,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("layoutid" => $layoutObject->getLayoutId(), "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'UploadVideo':
					$files = array();

					$i = 0;

					while (isset($parameters["filename_{$i}"]))
					{
						$files[] = $parameters["filename_{$i}"];
						$i += 1;
					}

					$result = $jobSite->UploadVideo(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['uploadtime']) ? $parameters['uploadtime'] : NULL,
							isset($parameters['cameralocation']) ? $parameters['cameralocation'] : NULL,
							isset($parameters['capturestarttime']) ? $parameters['capturestarttime'] : NULL,
							$files,
							isset($parameters['filespersegment']) ? $parameters['filespersegment'] : NULL,
							$videoIds,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"videoids" => implode(",", $videoIds),
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'DeleteVideo':
					$result = $jobSite->DeleteVideo(
							isset($parameters['videoid']) ? $parameters['videoid'] : NULL,
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

				case 'GetUserCounts':
					$result = $jobSite->GetUserCounts(
							isset($parameters['jobsiteid']) ? $parameters['jobsiteid'] : NULL,
							$rawOutputFilenames,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"rawoutputfilenames" => $rawOutputFilenames,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "JobSite";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_VIDEO_JOBSITE, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
			$this->commonJobSite = new CommonJobSite($context);
		}

		public function Update(
			$jobSiteId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = TRUE;

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return TRUE;
		}

		public function GetInfo(
			$jobSiteId,
			$infoLevel,
			&$jobSiteData,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, infoLevel=$infoLevel");

			if ($infoLevel == INFO_LEVEL_SUMMARY)
			{
				$result = $this->GetSummaryInfo($jobSiteId, $jobSiteData, $resultString);
			}
			else if ($infoLevel == INFO_LEVEL_FULL)
			{
				$result = $this->GetFullInfo($jobSiteId, $jobSiteData, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$jobSiteId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			// Delete videos for this job site.
			$fileRows = FileRow::Find(
					$this->context->dbcon,
					array("videoid", "name"),
					array("jobsiteid=$jobSiteId"),
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($fileRows != NULL)
			{
				foreach ($fileRows as &$fileRow)
				{
					$result = $this->DeleteVideo($fileRow['videoid'], $resultString);

					if (!$result)
					{
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "Failed to delete video ".$fileRow['videoid']." with resultstring='$resultString'");

						break;
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function CreateLayout(
			$videoId,
			$name,
			$rating,
			$videospeed,
			$lastvideoposition,
			$legs,
			&$layoutObject,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId, name=$name, rating=$rating");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_DESIGNER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($videoId === NULL)
			{
				$resultString = "Missing required parameter 'videoid'";
				$validParameters = FALSE;
			}
			else if (!$this->GetJobSiteVideo($videoId, $jobRow, $jobSiteRow, $videoFileRow, $resultString))
			{
				$validParameters = FALSE;
			}
			else if ($name === NULL)
			{
				$resultString = "Missing required parameter 'name'";
				$validParameters = FALSE;
			}
			else if ($videospeed === NULL)
			{
				$resultString = "Missing required parameter 'videospeed'";
				$validParameters = FALSE;
			}
			else if ($rating === NULL)
			{
				$resultString = "Missing required parameter 'rating'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				if ($lastvideoposition === NULL)
				{
					$lastvideoposition = 0;
				}

				mysqli_begin_transaction($this->context->dbcon, MYSQLI_TRANS_START_READ_WRITE);

				$date = date("Y-m-d H:i:s");

				$sqlError = 0;

				$result = LayoutRow::Create(
						$this->context->dbcon,
						$this->context->account->getDeveloper(),
						$videoId,
						$name,
						"DESIGN_STARTED",
						$videospeed,
						$lastvideoposition,
						$this->context->account->getAccountId(),  // designedby_user
						0,                    // countedby_user
						0,                    // qcedby_user
						$rating,
						$date,                // lastupdatetime
						$layoutObject,
						$sqlError
						);

				if ($result)
				{
					$result = $this->SetLegs($layoutObject, $legs, $resultString);

					if ($result)
					{
						$jobSiteRow->setLastUpdateTime($date);
						$jobSiteRow->CommitChangedFields($sqlError);

						$jobRow->setLastUpdateTime($date);
						$jobRow->CommitChangedFields($sqlError);

						$result = mysqli_commit($this->context->dbcon);

						if (!$result)
						{
							$sqlError = mysqli_errno($this->context->dbcon);
							$resultString = "SQL error $sqlError";

							DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
						}
					}
					else
					{
						DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "SetLegs failed - rolling back transaction");

						mysqli_rollback($this->context->dbcon);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Failure creating layout - unknown error";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function GetSummaryInfo(
			$jobSiteId,
			&$fileRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			$filter = array("jobsiteid=$jobSiteId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$fileRows = FileRow::Find(
					$this->context->dbcon,
					array("videoid", "name"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($fileRows != NULL)
			{
				$result = TRUE;

				foreach ($fileRows as &$fileRow)
				{
					// Determine number of layouts for each file.
					$filter = array("videoid=".$fileRow['videoid']);

					if (!$this->context->account->getDeveloper())
					{
						$filter[] = "testdata=0";
					}

					$layoutRows = LayoutRow::Find(
							$this->context->dbcon,
							array("layoutid"),
							$filter,
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					$fileRow["numlayouts"] = ($layoutRows == NULL) ? 0 : count($layoutRows);
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL error $sqlError";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: no video files";
				DBG_WARN(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function GetVideoLayouts(
			$videoId
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId");

			$accountManager = new AccountManager($this->context);
			$lastAccountId = 0;

			// Determine number of layouts for each file.
			$filter = array("videoid=$videoId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$layoutRows = LayoutRow::Find(
					$this->context->dbcon,
					array("layoutid", "name", "status", "videospeed", "lastvideoposition", "designedby_user", "countedby_user", "qcedby_user", "rating", "lastupdatetime"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($layoutRows != NULL)
			{
				$firstname = "";
				$lastname = "";
				$email = "";

				foreach ($layoutRows as &$layoutRow)
				{
					if ($layoutRow['designedby_user'] != $lastAccountId)
					{
						if ($layoutRow['designedby_user'] != 0)
						{
							$lastAccountId = $layoutRow['designedby_user'];

							$accountManager->GetUserAccount($lastAccountId, $accountRow, $resultString);

							if ($accountRow)
							{
								$firstname = $accountRow['firstname'];
								$lastname = $accountRow['lastname'];
								$email = $accountRow['email'];
							}
							else
							{
								$firstname = "";
								$lastname = "";
								$email = "";
							}
						}
					}

					$layoutRow['designedby_user_firstname'] = $firstname;
					$layoutRow['designedby_user_lastname'] = $lastname;
					$layoutRow['designedby_user_email'] = $email;

					if ($layoutRow['countedby_user'] != $lastAccountId)
					{
						if ($layoutRow['countedby_user'] != 0)
						{
							$lastAccountId = $layoutRow['countedby_user'];

							$accountManager->GetUserAccount($lastAccountId, $accountRow, $resultString);

							if ($accountRow)
							{
								$firstname = $accountRow['firstname'];
								$lastname = $accountRow['lastname'];
								$email = $accountRow['email'];
							}
							else
							{
								$firstname = "";
								$lastname = "";
								$email = "";
							}
						}
					}

					$layoutRow['countedby_user_firstname'] = $firstname;
					$layoutRow['countedby_user_lastname'] = $lastname;
					$layoutRow['countedby_user_email'] = $email;

					if ($layoutRow['qcedby_user'] != $lastAccountId)
					{
						if ($layoutRow['qcedby_user'] != 0)
						{
							$lastAccountId = $layoutRow['qcedby_user'];

							$accountManager->GetUserAccount($lastAccountId, $accountRow, $resultString);

							if ($accountRow)
							{
								$firstname = $accountRow['firstname'];
								$lastname = $accountRow['lastname'];
								$email = $accountRow['email'];
							}
							else
							{
								$firstname = "";
								$lastname = "";
								$email = "";
							}
						}
					}

					$layoutRow['qcedby_user_firstname'] = $firstname;
					$layoutRow['qcedby_user_lastname'] = $lastname;
					$layoutRow['qcedby_user_email'] = $email;

					// Remove the account ids.
					unset($layoutRow['designedby_user']);
					unset($layoutRow['countedby_user']);
					unset($layoutRow['qcedby_user']);

					$layoutRow['legs'] = LayoutLegRow::Find(
							$this->context->dbcon,
							NULL,
							array("layoutid=".$layoutRow['layoutid']),
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);
				}
			}

			DBG_RETURN(DBGZ_VIDEO_JOBSITE, __METHOD__);
			return $layoutRows;
		}

		public function GetJobSiteFiles(
			$jobSiteId,
			&$fileRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$filter = array("jobsiteid=$jobSiteId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$fileRows = FileRow::Find(
					$this->context->dbcon,
					array("videoid", "name", "addedtime", "lastupdatetime", "filesize", "uploadtime", "capturestarttime", "timediff(captureendtime, capturestarttime) as duration", "bucketfileprefix", "status", "testdata", "cameralocation"),
					$filter,
					NULL,
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($fileRows != NULL)
			{
				foreach ($fileRows as &$fileRow)
				{
					//$fileRow['bucketfilename'] = $fileRow['bucketfileprefix'].".m3u8";
					$fileRow['bucketfilename'] = $fileRow['bucketfileprefix'].".mp4";
					$fileRow['layouts'] = $this->GetVideoLayouts($fileRow['videoid']);

					$fileRow['ingestionphases'] = IngestionPhaseRow::Find(
							$this->context->dbcon,
							array("phase", "starttime", "endtime", "timediff(endtime, starttime) as duration"),
							array("videoid=".$fileRow['videoid']),
							"starttime",
							ROW_ASSOCIATIVE,
							$sqlError
							);

					//
					// If the file status is "ready" or begins with "failed", then ingestion has completed successfully
					// Ingestion time is then the "lastupdatetime" - "addedtime".
					// currenttime - addedtime.
					//
					if (($fileRow["status"] == "ready")
							|| (strpos($fileRow["status"], "failed") === 0))
					{
						$fileRow['ingestionduration'] = date("H:i:s", strtotime($fileRow["lastupdatetime"]) - strtotime($fileRow["addedtime"]));
					}
					else
					{
						$fileRow['ingestionduration'] = "in progress";
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, TRUE);
			return TRUE;
		}

		public function GetFullInfo(
			$jobSiteId,
			&$fullInfo,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = $this->GetJobSiteFiles($jobSiteId, $fullInfo, $resultString);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function GetJobSiteVideo(
			$videoId,
			&$jobRow,
			&$jobSiteRow,
			&$jobSiteVideoFileRow,
			&$resultString
			)
		{
			DBG_ENTER_LOWPRIO(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId");

			$result = FALSE;
			$sqlError = 0;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			$filter = array("videoid=$videoId");

			if (!$this->context->account->getDeveloper())
			{
				$filter[] = "testdata=0";
			}

			$fileRow = FileRow::FindOne(
					$this->context->dbcon,
					NULL,
					$filter,
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if ($fileRow != NULL)
			{
				$result = $this->commonJobSite->GetJobSite(
						$fileRow->getJobSiteId(),
						ROW_OBJECT,
						$jobSiteRow,
						$jobRow,
						$resultString
						);

				if ($result)
				{
					$jobSiteVideoFileRow = $fileRow;
				}
				else
				{
					DBG_ERR(DBGZ_JOBSITE, __METHOD__, "GetJobSite() failed with resultString='$resultString'");

					DBG_RETURN(DBGZ_JOBSITE, __METHOD__);
					return FALSE;
				}
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}
			else
			{
				$resultString = "Video not found";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL_LOWPRIO(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function UploadVideo(
			$jobSiteId,
			$name,
			$uploadTime,
			$cameraLocation,
			$captureStartTime,
			$files,
			$filesPerSegment,
			&$videoIds,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_VIDEO_JOBSITE,
					__METHOD__,
					"jobSiteId=$jobSiteId, name=$name, uploadTime=$uploadTime, cameraLocation=$cameraLocation, captureStartTime=$captureStartTime, filesPerSegment=$filesPerSegment"
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_PROJECTMANAGER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($jobSiteId === NULL)
			{
				$resultString = "Missing parameter 'jobsiteid'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($name === NULL)
			{
				$resultString = "Missing parameter 'name'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($captureStartTime === NULL)
			{
				$resultString = "Missing parameter 'capturestarttime'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if (count($files) == 0)
			{
				$resultString = "Missing parameter 'filename_n'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}
			else if ($filesPerSegment === NULL)
			{
				$resultString = "Missing parameter 'filespersegment'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				// Make sure the jobsite exists.
				$jobSiteRow = JobSiteRow::FindOne(
						$this->context->dbcon,
						array("sitecode"),
						array("jobsiteid=$jobSiteId"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($jobSiteRow == NULL)
				{
					if ($sqlError != 0)
					{
						$resultString = "SQL error $sqlError";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

						DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
						return FALSE;
					}
					else
					{
						$resultString = "Job site not found";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

						DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
						return FALSE;
					}
				}

				$videoIds = array();

				$sqlError = 0;
				$time = date("Y-m-d H:i:s");

				$numberOfSegments = ceil(count($files) / $filesPerSegment);
				$fileIndex = 0;

				$segmentCaptureStartTime = \DateTime::createFromFormat("Y-m-d H:i:s", $captureStartTime);

				$fileRows = array();

				for ($i=0; $i<$numberOfSegments; $i++)
				{
					$filesToMerge = array();

					for ($j=0; $j<$filesPerSegment && $fileIndex<count($files); $j++)
					{
						$filesToMerge[] = TEMP_FILE_FOLDER . "/" . $files[$fileIndex];
						$fileIndex += 1;
					}

					// The duration of each file is defined to be 60 minutes.
					$mergedFileDuration = new \DateInterval('PT'.(60 * count($filesToMerge)).'M');

					$segmentCaptureEndTime = clone $segmentCaptureStartTime;
					$segmentCaptureEndTime->add($mergedFileDuration);

					$segmentName = "{$name}_".$segmentCaptureStartTime->format("H:i")."-".$segmentCaptureEndTime->format("H:i");

					$result = FileRow::Create(
							$this->context->dbcon,
							$this->context->account->getDeveloper(),
							$jobSiteId,
							$segmentName,
							$cameraLocation,
							0,                      // filesize (to be determined during ingestion)
							0,                      // file upload time - will be calculated and set below.
							$time,                  // added time
							$segmentCaptureStartTime->format("Y-m-d H:i:s"),
							$segmentCaptureEndTime->format("Y-m-d H:i:s"),
							"",                     // bucketfileprefix - will be set during ingestion
							"preparing to ingest",  // status
							$time,                  // last update time
							$fileRow,
							$sqlError
							);

					if ($result)
					{
						$fileRows[] = $fileRow;
						$videoId = $fileRow->getVideoId();
						$videoIds[] = $videoId;

						// Start an async task to ingest the video file.
						/*AsyncCall(
								__FILE__,
								"\Idax\Video\Classes\JobSite::PrepareVideoFileForIngestion_Array",
								array(
										'email' => $this->context->account->getEmail(),
										'jobsiteid' => $jobSiteId,
										'videoid' => $videoId,
										'name' => $segmentName,
										'videofiles' => implode(",", $filesToMerge)
										)
								);*/
						$res = $this->PrepareVideoFileForIngestion_Array(
										 array(
										'email' => $this->context->account->getEmail(),
										'jobsiteid' => $jobSiteId,
										'videoid' => $videoId,
										'name' => $segmentName,
										'videofiles' => implode(",", $filesToMerge)
										)
								
								);
					}
					else
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
						break;
					}

					$segmentCaptureStartTime->add($mergedFileDuration);
					$segmentCaptureEndTime->add($mergedFileDuration);
				}

				// Update each file row with upload time.  The upload time is just divided
				// evenly among all the file rows we just created.
				foreach ($fileRows as &$fileRow)
				{
					$fileRow->setUploadTime($uploadTime / count($fileRows));
					$fileRow->CommitChangedFields($sqlError);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function DeleteVideo(
			$videoId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId");

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_PROJECTMANAGER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($videoId === NULL)
			{
				$resultString = "Missing parameter 'videoId'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$result = $this->GetJobSiteVideo($videoId, $jobRow, $jobSiteRow, $videoFileRow, $resultString);

				if ($result)
				{
					//
					// Delete the video files from the bucket
					//
					// The bucket filename can be used as a prefix to match all files related to this
					// video.  However, the bucket filename has a ".m3u8" file extension, which is not
					// part of the prefix.  We can use basename() to remove it.
					//
					$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

					$awsFileManager->DeleteFiles($videoFileRow->getBucketFilePrefix());

					//
					// Delete files from the jobserverlog bucket.
					//
					$awsFileManager = new AWSFileManager(IDAX_JOBSERVERLOG_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

					$awsFileManager->DeleteFiles($videoFileRow->getBucketFilePrefix());

					//
					// Delete all the video layouts.
					//
					$layoutRows = LayoutRow::Find(
							$this->context->dbcon,
							NULL,
							array("videoid=$videoId"),
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					if ($layoutRows != NULL)
					{
						$layout = new Layout($this->context);

						foreach ($layoutRows as &$layoutRow)
						{
							// This call will delete all data (e.g. layout legs and counts) associated with the Layout.
							$layout->Delete($layoutRow['layoutid'], $resultString);
						}
					}

					// Delete ingestion phases for this video
					IngestionPhaseRow::Delete($this->context->dbcon, array("videoid=$videoId"), $sqlError);

					// Delete the video record
					FileRow::Delete($this->context->dbcon, array("videoid=$videoId"), $sqlError);

					// Update the lastupdatetime on the parent job and jobsite records
					$date = date("Y-m-d H:i:s");

					$jobRow->setLastUpdateTime($date);
					$jobRow->CommitChangedFields($sqlError);

					$jobSiteRow->setLastUpdateTime($date);
					$jobSiteRow->CommitChangedFields($sqlError);
				}
				else
				{
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "GetJobSiteVideo failed with resultString='$resultString'");
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public static function TranscodeFileToHLS_Array(
			$paramsArray
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__);

			$email = $paramsArray['email'];
			$videoId = $paramsArray['videoid'];
			$srcFilename = $paramsArray['srcfilename'];
			$dstDirectory = $paramsArray['dstdirectory'];
			$dstBaseFilename = $paramsArray['dstbasefilename'];
			$width = $paramsArray['width'];
			$height = $paramsArray['height'];
			$bitRate = $paramsArray['bitrate'];
			$frameRate = $paramsArray['framerate'];
			$videoSpeed = $paramsArray['videospeed'];
			$segmentDuration = $paramsArray['segmentduration'];

			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId, srcFilename=$srcFilename, dstDirectory=$dstDirectory, dstBaseFilename=$dstBaseFilename, videoSpeed=$videoSpeed");

			// connect and verify connection
			$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

			if ($con == NULL)
			{
				$resultString = "Failure connecting to database.";
				DBG_ERROR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				file_put_contents($dstDirectory."/TranscodeToHLS_{$videoSpeed}_".getmypid().".txt", "$result:$resultString");

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;
			$accountRow = AccountRow::FindOne($con, NULL, array("email='$email'"), NULL, ROW_OBJECT, $sqlError);

			if ($accountRow != NULL)
			{
				$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
				$jobSite = new JobSite($context);

				$result = $jobSite->TranscodeFileToHLS(
						$videoId,
						$srcFilename,
						$dstDirectory,
						$dstBaseFilename,
						$width,
						$height,
						$bitRate,
						$frameRate,
						$videoSpeed,
						$segmentDuration,
						$resultString
						);

				file_put_contents($dstDirectory."/TranscodeToHLS_{$videoSpeed}_".getmypid().".txt", "$result:$resultString");
			}

			mysqli_close($con);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		private function TranscodeFileToHLS(
			$videoId,
			$srcFilename,
			$dstDirectory,
			$dstBaseFilename,
			$width,
			$height,
			$bitRate,
			$frameRate,
			$videoSpeed,
			$segmentDuration,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId, srcFilename=$srcFilename, dstDirectory=$dstDirectory, dstBaseFilename=$dstBaseFilename, videoSpeed=$videoSpeed");

			$result = FALSE;

			$jobSiteVideoFileRow = FileRow::FindOne(
					$this->context->dbcon,
					NULL,
					array("videoid=$videoId"),
					NULL,
					ROW_OBJECT,
					$sqlError
					);

			if ($jobSiteVideoFileRow == NULL)
			{
				$resultString = "Video not found";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
				return $result;
			}

			$dest = "{$dstBaseFilename}_{$videoSpeed}";

			// Transcode to specified videospeed
			IngestionPhaseRow::Create($this->context->dbcon, $videoId, "transcode {$videoSpeed}X", date("Y-m-d H:i:s"), NULL, $transcodePhaseRow, $sqlError);

			$pts = 1 / floatval($videoSpeed);

			$ffmpegCmd = FFMPEG." -hide_banner -loglevel 16 -y -i $srcFilename -vf scale=$width:$height, -filter:v \"setpts={$pts}*PTS\" $dstDirectory/{$dest}.h264";
			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "ffmpegCmd for transcode='$ffmpegCmd");

			exec(
					$ffmpegCmd." 2> {$dstDirectory}/ffmpeg_transcode_{$videoSpeed}.txt",
					$output,
					$status
					);

			$transcodePhaseRow->setEndTime(date("Y-m-d H:i:s"));
			$transcodePhaseRow->CommitChangedFields($sqlError);

			if ($status != 0)
			{
				$resultString = "ffmpeg failure during transcoding (status=$status)";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
				return $result;
			}

			// Segment the file for HTTP Live Streaming
			IngestionPhaseRow::Create($this->context->dbcon, $videoId, "segment {$videoSpeed}X", date("Y-m-d H:i:s"), NULL, $segmentPhaseRow, $sqlError);

			$ffmpegCmd = FFMPEG." -hide_banner -loglevel 16 -y -i $dstDirectory/{$dest}.h264  -codec:v libx264 -f segment -segment_time $segmentDuration -segment_list $dstDirectory/{$dest}.m3u8 -segment_format mpegts $dstDirectory/{$dest}-%05d.ts";
			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "ffmpegCmd for segmentation='$ffmpegCmd'");

			exec(
					$ffmpegCmd." 2> {$dstDirectory}/ffmpeg_segment_{$videoSpeed}.txt",
					$output,
					$status
					);

			$segmentPhaseRow->setEndTime(date("Y-m-d H:i:s"));
			$segmentPhaseRow->CommitChangedFields($sqlError);

			if ($status == 0)
			{
				$result = TRUE;
			}
			else
			{
				$resultString = "ffmpeg failure during segmentation (status=$status)";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}

			// Delete the intermediate file
			unlink("$dstDirectory/{$dest}.h264");

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		private function CreateImageFromVideo(
			$srcFilename,
			$dstDirectory,
			$dstBaseFilename,
			$width,
			$height,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "srcFilename=$srcFilename, dstDirectory=$dstDirectory, dstBaseFilename=$dstBaseFilename, width=$width, height=$height");

			$result = FALSE;

			// Create an image
			$ffmpegCmd = FFMPEG." -hide_banner -loglevel 16 -y -ss 0.5 -i $srcFilename -vframes 1 -s {$width}x{$height} -f image2 $dstDirectory/{$dstBaseFilename}.jpg";
			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "ffmpegCmd for creating thumbnail='$ffmpegCmd'");

			exec(
					$ffmpegCmd." 2> {$dstDirectory}/ffmpeg_thumbnail.txt",
					$output,
					$status
					);

			if ($status == 0)
			{
				$result = TRUE;
			}
			else
			{
				$resultString = "ffmpeg failure getting image from video file (status=$status)";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		private function GetVideoInformation(
			$srcFilename,
			&$width,
			&$height,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "srcFilename=$srcFilename");

			$result = FALSE;

			// Create an image
			$ffprobeCmd = FFPROBE." -v quiet -print_format json -show_format -show_streams $srcFilename";
			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "$fprobeCmd for getting video file info='$ffprobeCmd");

			exec(
					$ffprobeCmd." 2> {$dstDirectory}/ffprobe_getfileinfo.txt",
					$output,
					$status
					);

			if ($status == 0)
			{
				$result = TRUE;
			}
			else
			{
				$resultString = "ffprobe failure getting video file info (status=$status)";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		private function MergeVideoFiles(
			$jobSiteVideoFileRow,
			$videoFiles,
			$dstDirectory,
			$dstFilename,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__);

			$result = FALSE;
			$intermediateFileResult = TRUE;

			// Create intermediate files
			$intermediateFiles = array();
			$i = 0;

			foreach ($videoFiles as &$videoFile)
			{
				$intermediateFile = "{$dstDirectory}/intermediate_{$i}.ts";

				$intermediateFiles[] = $intermediateFile;

				$ffmpegCmd = FFMPEG." -v quiet -y -i $videoFile -c copy -bsf:v h264_mp4toannexb -f mpegts $intermediateFile";
				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "ffmpegCmd for creating intermediate file='$ffmpegCmd");

				exec(
						$ffmpegCmd." 2> {$dstDirectory}/ffmpeg_createintermediatefile.txt",
						$output,
						$status
						);

				if (!$status == 0)
				{
					$resultString = "ffmpeg failure creating intermediate file for merging (status=$status)";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

					$intermediateFileResult = FALSE;
					break;
				}

				$i++;
			}

			if ($intermediateFileResult)
			{
				// Now merge the files
				$intermediateFilesString = trim(implode("|", $intermediateFiles), "|");

				$ffmpegCmd = FFMPEG." -v quiet -y -i \"concat:$intermediateFilesString\" -c copy -bsf:a aac_adtstoasc {$dstDirectory}/{$dstFilename}";
				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "ffmpegCmd for merging intermediate files='$ffmpegCmd");

				exec(
						$ffmpegCmd." 2> {$dstDirectory}/ffmpeg_mergefiles.txt",
						$output,
						$status
						);

				if ($status == 0)
				{
					$result = TRUE;
				}
				else
				{
					$resultString = "ffmpeg failure merging intermediate files (status=$status)";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
				}
			}

			// Remove the intermediate files
			foreach ($intermediateFiles as &$intermediateFile)
			{
				@unlink($intermediateFile);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public static function PrepareVideoFileForIngestion_Array(
			$paramsArray
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__);
                        //echo '<pre>';print_r($paramsArray);exit;
			$email = $paramsArray['email'];
			$jobSiteId = $paramsArray['jobsiteid'];
			$videoId = $paramsArray['videoid'];
			$name = $paramsArray['name'];
			$videoFiles = explode(",", $paramsArray['videofiles']);

			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "email=$email, jobSiteId=$jobSiteId, videoId=$videoId, name=$name");

			// connect and verify connection
			$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

			$sqlError = 0;
			$accountRow = AccountRow::FindOne($con, NULL, array("email='$email'"), NULL, ROW_OBJECT, $sqlError);

			if ($accountRow != NULL)
			{
				$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
				$jobSite = new JobSite($context);

				$result = $jobSite->PrepareVideoFileForIngestion($jobSiteId, $videoId, $name, $videoFiles, $resultString);
			}

			mysqli_close($con);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}
                /*public static function PrepareVideoFileForIngestion_Array(
			$email,$jobSiteId, $videoId, $name,$videoFiles
			)
		{
                            //echo 'dsdsdsd';exit;
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__);

			$email = $email;
			$jobSiteId = $jobSiteId;
			$videoId = $videoId;
			$name = $name;
			$videoFiles = explode(",", $videoFiles);

			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "email=$email, jobSiteId=$jobSiteId, videoId=$videoId, name=$name");

			// connect and verify connection
			$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

			$sqlError = 0;
			$accountRow = AccountRow::FindOne($con, NULL, array("email='$email'"), NULL, ROW_OBJECT, $sqlError);

			if ($accountRow != NULL)
			{
				$context = new MethodCallContext($con, $accountRow, NULL, "127.0.0.1", NULL);
				$jobSite = new JobSite($context);

				$result = $jobSite->PrepareVideoFileForIngestion($jobSiteId, $videoId, $name, $videoFiles, $resultString);
			}

			mysqli_close($con);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}*/

		public function PrepareVideoFileForIngestion(
			$jobSiteId,
			$videoId,
			$name,
			$videoFiles,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId, videoId=$videoId, name=$name, videoFiles=".serialize($videoFiles));

			$result = $this->GetJobSiteVideo($videoId, $jobRow, $jobSiteRow, $jobSiteVideoFileRow, $resultString);

			if (!$result)
			{
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "GetJobSiteVideo failed: resultString='$resultString'");
			}

			$dstBaseFilename = md5(microtime().rand())."-".time();

			$jobSiteVideoFileRow->setBucketFilePrefix($dstBaseFilename);
			$jobSiteVideoFileRow->setStatus("preparing");
			$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
			$jobSiteVideoFileRow->CommitChangedFields($sqlError);
                        //echo $dstBaseFilename;
                        $old_umask = umask(0);
			//echo $dstDirectory = "/home/idax/Video/Ingestions/$dstBaseFilename";//exit;
			 $dstDirectory = PROJECT_ROOT."/idax/Video/Ingestions/".$dstBaseFilename;//exit;
                        
                        //if (!file_exists($dstDirectory)) {
                        if (!is_dir($dstBaseFilename)) {
                        $old_umask = umask(0);
                        mkdir(PROJECT_ROOT."/idax/Video/Ingestions/".$dstBaseFilename, 0755, true);
                        umask($old_umask);
                        //echo 'sddd'.$dstDirectory;
                    }

//exit;
			//if (!@mkdir($dstDirectory))
			if (!@mkdir($dstBaseFilename))
			{
				$result = FALSE;
				//$resultString = "failed: mkdir($dstDirectory) ".error_get_last();
				$resultString = "failed: mkdir($dstDirectory) ";

				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "resultString");

				//$jobSiteVideoFileRow->setStatus(resultString);
				$jobSiteVideoFileRow->setStatus($resultString);
				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);
			}

			if ($result)
			{
				// Merge the video files
				IngestionPhaseRow::Create($this->context->dbcon, $videoId, "merge segments", date("Y-m-d H:i:s"), NULL, $mergePhaseRow, $sqlError);

				$mergedFilename = "{$dstBaseFilename}.mp4";
				$result = $this->MergeVideoFiles($jobSiteVideoFileRow, $videoFiles, $dstDirectory, $mergedFilename, $resultString);

				if ($result)
				{
					$jobSiteVideoFileRow->setFileSize(filesize("${dstDirectory}/${mergedFilename}"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
				else
				{
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "MergeVideoFiles failed: resultString='$resultString'");

					$jobSiteVideoFileRow->setStatus("failed: MergedVideoFiles resultString=$resultString");
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}

				$mergePhaseRow->setEndTime(date("Y-m-d H:i:s"));
				$mergePhaseRow->CommitChangedFields($sqlError);
			}

			if ($result)
			{                           
				// Upload the merged mp4 file to the bucket.
				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Uploading merged file to AWS bucket");

				$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);
                                 //echo 'dddin result'.IDAX_VIDEOFILES_BUCKET.'<br/>';
                                 //echo 'dddin result'.$dstDirectory.'<br/>'.$mergedFilename.'<br/>'.$resultString.'<br/>'; //exit;

				$result = $awsFileManager->UploadFile("$dstDirectory/$mergedFilename", basename($mergedFilename), "public-read", true, $resultString);
                                //echo '<pre>123';print_r($result);exit;
				if (!$result)
				{
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "UploadFile failed: resultString='$resultString'");

					$jobSiteVideoFileRow->setStatus("failed: UploadFile merged file resultString=$resultString");
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
			}

			if ($result)
			{
				// Create an entry in the job queue table for handling the transcoding.
				$result = JobQueueRow::Create(
						$this->context->dbcon,
						"unassigned",                // serverinstanceid,
						date("Y-m-d H:i:s"),         // addedtime
						"IdaxIngestVideo",           // jobname
						json_encode(array("videoid" => $videoId)),  // jobparams
						$jobQueueRow,
						$sqlError
						);

				if ($result)
				{
					// Update the video file info
					$jobSiteVideoFileRow->setStatus("prepared");
				}
				else
				{
					// Update the video file info
					$resultString = "Failed to create jobqueuerow - SQL Error=$sqlError";

					$jobSiteVideoFileRow->setStatus("failed: failed to create JobQueueRow (SQL Error=$sqlError)");
				}

				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);
			}

			// Delete any files we created.
			/*$files = glob("$dstDirectory/*");

			foreach ($files as &$files)
			{
				unlink($files);
			}

			@rmdir($dstDirectory);

			// It's on us to remove the source files, too.
			foreach ($videoFiles as &$videoFile)
			{
				unlink($videoFile);
			}*/

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public static function IngestVideo_Array(
			$paramsArray
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__);

			$email = $paramsArray['email'];
			$serverInstanceId = $paramsArray['serverinstanceid'];
			$videoId = $paramsArray['videoid'];

			DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "serverInstanceId=$serverInstanceId, email=$email, videoId=$videoId");

			// connect and verify connection
			$dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

			$sqlError = 0;
			$accountRow = AccountRow::FindOne($dbcon, NULL, array("email='$email'"), NULL, ROW_OBJECT, $sqlError);

			if ($accountRow != NULL)
			{
				$context = new MethodCallContext($dbcon, $accountRow, NULL, "127.0.0.1", NULL);
				$jobSite = new JobSite($context);

				$result = $jobSite->IngestVideo($videoId, $resultString);
			}

			mysqli_query($dbcon, "UPDATE core_jobservers SET status='COMPLETED' WHERE instanceid='$serverInstanceId'");

			mysqli_close($dbcon);

			// Transfer idax.log to AWS bucket.
			$awsFileManager = new AWSFileManager(IDAX_JOBSERVERLOG_BUCKET, AWSREGION, AWSKEY, AWSSECRET);
			$awsFileManager->UploadFile("idax/logs/idax.log", "{$serverInstanceId}_idax.log", "public-read", true, $resultString);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		public function IngestVideo(
			$videoId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "videoId=$videoId");

			$result = $this->GetJobSiteVideo($videoId, $jobRow, $jobSiteRow, $jobSiteVideoFileRow, $resultString);

			if (!$result)
			{
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "GetJobSiteVideo failed: resultString='$resultString'");

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			$dstBaseFilename = $jobSiteVideoFileRow->getBucketFilePrefix();
			$dstDirectory = "/home/idax/Video/Ingestions/$dstBaseFilename";
			$mp4Filename = "$dstDirectory/$dstBaseFilename.mp4";

			if ($result)
			{
				if (!mkdir($dstDirectory))
				{
					$result = FALSE;
					$resultString = "failed: mkdir($dstDirectory) ".error_get_last();

					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "resultString");

					$jobSiteVideoFileRow->setStatus($resultString);
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
			}

			// Get a local copy of the mp4 file for processing.  It was transferred to bucket during the ingestion prep phase.
			$awsFileManager = new AWSFileManager(IDAX_VIDEOFILES_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

			$result = $awsFileManager->GetFile("$dstBaseFilename.mp4", $mp4FileContents);

			if ($result)
			{
				if (file_put_contents($mp4Filename, $mp4FileContents) === FALSE)
				{
					$result = FALSE;
					$resultString = "Error creating local MP4 file";

					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "resultString");

					$jobSiteVideoFileRow->setStatus(resultString);
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
			}

			if ($result)
			{
				$width = 640;
				$height = 432;
				$segmentDuration = 10;
				$bitRate = 0;
				$frameRate = 0;
				$videoSpeeds = array("1", "3.5", "7.5");

				$jobSiteVideoFileRow->setStatus("creating thumbnail");
				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);

				IngestionPhaseRow::Create($this->context->dbcon, $videoId, "thumbnail", date("Y-m-d H:i:s"), NULL, $thumbnailPhaseRow, $sqlError);

				$result = $this->CreateImageFromVideo(
						"$dstDirectory/$dstBaseFilename.mp4",
						$dstDirectory,
						$dstBaseFilename,
						$width,
						$height,
						$resultString
						);

				$thumbnailPhaseRow->setEndTime(date("Y-m-d H:i:s"));
				$thumbnailPhaseRow->CommitChangedFields($sqlError);

				if (!$result)
				{
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "CreateImageFromVideo failed: resultString='$resultString'");

					$jobSiteVideoFileRow->setStatus("failed: CreateImageFromVideo resultString=$resultString");
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
			}

			if ($result)
			{
				// Transcode the MP4 file to each video speed
				$jobSiteVideoFileRow->setStatus("transcoding");
				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);

				$transcodePIDs = array();

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Spawning transcode tasks...");

				foreach ($videoSpeeds as &$videoSpeed)
				{
					// Start an async task to transcode the video file.
					$result = AsyncCall(
							__FILE__,
							"\Idax\Video\Classes\JobSite::TranscodeFileToHLS_Array",
							array(
									'email' => $this->context->account->getEmail(),
									'videoid' => $videoId,
									'srcfilename' => $mp4Filename,
									'dstdirectory' => $dstDirectory,
									'dstbasefilename' => $dstBaseFilename,
									'width' => $width,
									'height' => $height,
									'bitrate' => $bitRate,
									'framerate' => $frameRate,
									'videospeed' => $videoSpeed,
									'segmentduration' => $segmentDuration
									)
							);

					if ($result == NULL)
					{
						$resultString = "AsyncCall(TranscodeFileToHLS($videoSpeed)) failed";
						break;
					}
					else
					{
						$transcodePIDs["$videoSpeed"] = intval($result);
						DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Transcode PID for speed ($videoSpeed) is ".$transcodePIDs[$videoSpeed]);
					}
				}

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Started transcode tasks ".serialize($transcodePIDs));

				if (count($transcodePIDs))
				{
					DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Wait for transcode tasks to complete");

					//
					// Wait for all transcode processes to complete.
					//
					// As each one completes, check for pass/fail.  If any fails, then terminate the others.
					//
					while (count($transcodePIDs))
					{
						sleep(10);

						foreach ($transcodePIDs as $videoSpeed => &$transcodePID)
						{
							if (!isRunning($transcodePID))
							{
								// This process is terminated - remove it from the PID array.
								unset($transcodePIDs["$videoSpeed"]);

								$outputFilename = $dstDirectory."/TranscodeToHLS_{$videoSpeed}_{$transcodePID}.txt";

								// And now get its exit status.
								$outputFileContents = file_get_contents($outputFilename);

								if ($outputFileContents !== FALSE)
								{
									$outputFileContentsExploded = explode(":", file_get_contents($dstDirectory."/TranscodeToHLS_{$videoSpeed}_{$transcodePID}.txt"), 2);

									$result = isset($outputFileContentsExploded[0]) ? intval($outputFileContentsExploded[0]) : -1;
									$resultString = isset($outputFileContentsExploded[1]) ? $outputFileContentsExploded[1] : "Unknown - output file doesn't contain resultString";
								}
								else
								{
									$result = FALSE;
									$resultString = "Unknown error - output file $outputFilename doesn't exist";
								}

								DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Transcode task $transcodePID for videoSpeed $videoSpeed terminated) with result=$result, resultString=$resultString");

								if (!$result)
								{
									DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, "Transcode task for speed ($videoSpeed) failed - resultString='$resultString'. Terminating remaining transcode tasks.");

									$jobSiteVideoFileRow->setStatus("transcode failure: resultString=$resultString");
									$jobSiteVideoFileRow->CommitChangedFields($sqlError);

									foreach ($transcodePIDs as $videoSpeed => &$transcodePID)
									{
										DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Terminating remaining transcode task $transcodePID for videoSpeed=$videoSpeed.");

										exec("kill $(pstree $transcodePID -p -a -l | cut -d, -f2 | cut -d' ' -f1)");

										unset($transcodePIDs["$videoSpeed"]);
									}
								}
							}
						}
					}
				}
			}

			if ($result)
			{
				// Upload everything to the bucket.
				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Uploading files to AWS bucket");

				$jobSiteVideoFileRow->setStatus("transferring");
				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);

				// Before uploading the video files, upload and then delete the temp output files generated when we exec'd ffprobe and ffmpeg,
				$tempFiles = array_merge(glob("$dstDirectory/ffprobe*"), glob("$dstDirectory/ffmpeg*"), glob("$dstDirectory/TranscodeToHLS_*"));

				$awsServerLogFileManager = new AWSFileManager(IDAX_JOBSERVERLOG_BUCKET, AWSREGION, AWSKEY, AWSSECRET);

				foreach ($tempFiles as &$tempFile)
				{
					$awsServerLogFileManager->UploadFile($tempFile, $dstBaseFilename."_".basename($tempFile), "public-read", true, $resultString);
					unlink($tempFile);
				}

				// Also remove the MP4 file we downloaded.
				unlink("$dstDirectory/$dstBaseFilename.mp4");

				IngestionPhaseRow::Create($this->context->dbcon, $videoId, "transfer to S3", date("Y-m-d H:i:s"), NULL, $uploadPhaseRow, $sqlError);

				$uploadFiles = glob("$dstDirectory/*.*");

				foreach ($uploadFiles as &$uploadFile)
				{
					$result = $awsFileManager->UploadFile($uploadFile, basename($uploadFile), "public-read", true, $resultString);

					if (!$result)
					{
						$jobSiteVideoFileRow->setStatus("failed: UploadFile resultString=$resultString");
						$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
						$jobSiteVideoFileRow->CommitChangedFields($sqlError);

						break;
					}
				}

				$uploadPhaseRow->setEndTime(date("Y-m-d H:i:s"));
				$uploadPhaseRow->CommitChangedFields($sqlError);
			}

			if ($result)
			{
				// Verify everything was uploaded successfully.
				$jobSiteVideoFileRow->setStatus("verifying");
				$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
				$jobSiteVideoFileRow->CommitChangedFields($sqlError);

				IngestionPhaseRow::Create($this->context->dbcon, $videoId, "verifying", date("Y-m-d H:i:s"), NULL, $verifyPhase, $sqlError);

				$result = $this->VerifyVideo($awsFileManager, $jobSiteVideoFileRow, $segmentDuration, $resultString);

				$verifyPhase->setEndTime(date("Y-m-d H:i:s"));
				$verifyPhase->CommitChangedFields($sqlError);

				// Delete all the files if upload to bucket was successful.
				if ($result)
				{
					foreach ($uploadFiles as &$uploadFile)
					{
						unlink($uploadFile);
					}

					@rmdir($dstDirectory);
				}
				else
				{
					$jobSiteVideoFileRow->setStatus("failed: VerifyVideo resultString=$resultString");
					$jobSiteVideoFileRow->setLastUpdateTime(date("Y-m-d H:i:s"));
					$jobSiteVideoFileRow->CommitChangedFields($sqlError);
				}
			}

			if ($result)
			{
				$sqlError = 0;
				$now = date("Y-m-d H:i:s");

				// Update the video file info
				$jobSiteVideoFileRow->setStatus("ready");
				$jobSiteVideoFileRow->setLastUpdateTime($now);
				$result = $jobSiteVideoFileRow->CommitChangedFields($sqlError);

				if ($result)
				{
					// Update the lastupdatetime on the parent job and jobsite records
					$jobRow->setLastUpdateTime($now);
					$result = $jobRow->CommitChangedFields($sqlError);

					$jobSiteRow->setLastUpdateTime($now);
					$result = $jobSiteRow->CommitChangedFields($sqlError);

					if (!$result)
					{
						$resultString = "SQL Error $sqlError";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
					}
				}
				else
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
				}
			}

			// Finally send an email notification.
			$this->SendUploadVideoCompletedNotification($jobSiteRow, $jobSiteVideoFileRow);

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}

		private function SendUploadVideoCompletedNotification(
			$jobSiteRow,
			$fileRow
			)
		{
			$videoId = $fileRow->getVideoId();
			$captureDuration = gmdate("H:i:s", strtotime($fileRow->getCaptureEndTime()) - strtotime($fileRow->getCaptureStartTime()));
			$ingestionDuration = gmdate("H:i:s", strtotime($fileRow->getLastUpdateTime()) - strtotime($fileRow->getAddedTime()));

			if ($fileRow->getStatus() == "ready")
			{
				$subjectLine = "Video upload completed successfully";
			}
			else
			{
				$subjectLine = "Video upload failed!";
			}

			$ingestionPhases = IngestionPhaseRow::Find(
					$this->context->dbcon,
					NULL,
					array("videoid=$videoId"),
					"starttime",
					ROW_ASSOCIATIVE,
					$sqlError
					);

			$ingestionPhaseHTML = "";

			if ($ingestionPhases != NULL)
			{
				foreach ($ingestionPhases as &$ingestionPhase)
				{
					$duration = gmdate("H:i:s", strtotime($ingestionPhase["endtime"]) - strtotime($ingestionPhase["starttime"]));

					$ingestionPhaseHTML .= "<tr><td>".$ingestionPhase["phase"]."</td><td>".$ingestionPhase["starttime"]."</td><td>".$ingestionPhase["endtime"]."</td><td>".$duration."</td></tr>";
				}
			}

			$body = file_get_contents("/home/core/EmailTemplates/VideoUploadCompleted.html");

			$body = str_replace(
					array(
							"@JOBSITEID@",
							"@JOBSITESITECODE@",
							"@JOBSITEDESCRIPTION@",
							"@VIDEOID@",
							"@NAME@",
							"@STATUS@",
							"@FILEPREFIX@",
							"@FILESIZE@",
							"@FILEUPLOADTIME@",
							"@CAMERALOCATION@",
							"@CAPTURESTARTTIME@",
							"@CAPTUREENDTIME@",
							"@CAPTUREDURATION@",
							"@INGESTIONTIMESTARTED@",
							"@INGESTIONTIMEENDED@",
							"@INGESTIONDURATION@",
							"@TESTFILE@",
							"@INGESTIONPHASES@"
							),
					array(
							$jobSiteRow->getJobSiteId(),
							$jobSiteRow->getSiteCode(),
							$jobSiteRow->getDescription(),
							$videoId,
							$fileRow->getName(),
							$fileRow->getStatus(),
							$fileRow->getBucketFilePrefix(),
							number_format($fileRow->getFileSize() / 1048576)." MB",
							date("H:i:s", $fileRow->getUploadTime() / 1000),
							$fileRow->getCameraLocation(),
							$fileRow->getCaptureStartTime(),
							$fileRow->getCaptureEndTime(),
							$captureDuration,
							$fileRow->getAddedTime(),
							$fileRow->getLastUpdateTime(),
							$ingestionDuration,
							$fileRow->getTestData() ? "YES" : "NO",
							$ingestionPhaseHTML
							),
					$body
					);

			$result = SendEmail(
					$this->context->account->getEmail(),
					$this->context->account->getFirstName(),
					$this->context->account->getLastName(),
					"no-reply@idaxdata.com",
					$subjectLine,
					$body,
					NULL,          // attachments
					$resultString
					);
		}

		public function VerifyVideo(
			$awsFileManager,
			$fileRow,
			$segmentDuration,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_APP, __FUNCTION__);

			$videoId = $fileRow->getVideoId();
			$filePrefix = $fileRow->getBucketFilePrefix();

			DBG_INFO(DBGZ_APP, __FUNCTION__, "Checking video $videoId - filePrefix=$filePrefix");

			// Check the mp4 file is in the bucket
			if (!$awsFileManager->FileExists("{$filePrefix}.mp4"))
			{
				$resultString = "MP4 file {$filePrefix}.mp4 missing for video $videoId";
				DBG_ERR(DBGZ_APP, __FUNCTION__, $resultString);

				DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, FALSE);
				return FALSE;
			}

			// Check the jpeg file is in the bucket
			if (!$awsFileManager->FileExists("{$filePrefix}.jpg"))
			{
				$resultString = "JPEG file {$filePrefix}.jpg missing for video $videoId";
				DBG_ERR(DBGZ_APP, __FUNCTION__, $resultString);

				DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, FALSE);
				return FALSE;
			}

			// Check the HTTP Live Streaming files are in the bucket, for each video speed
			$speeds = array("1", "3.5", "7.5");

			foreach ($speeds as &$speed)
			{
				$missingSegments = [];

				// Check all the segments are in place
				$result = enumeratePlaylistSegments(
						$awsFileManager,
						"{$filePrefix}_$speed.m3u8",
						function($awsFileManager, $segmentFilename)
						{
							if (!$awsFileManager->FileExists($segmentFilename))
							{
								$missingSegments[] = $segmentFilename;
							}
						}
						);

				if (!$result)
				{
					$resultString = "Missing playlist {$filePrefix}_$speed.m3u8";
					DBG_ERR(DBGZ_APP, __FUNCTION__, $resultString);

					DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, FALSE);
					return FALSE;
				}
				else if (count($missingSegments))
				{
					$resultString = "Missing segments for video $videoId. Missing segments=".serialize($missingSegments);
					DBG_ERR(DBGZ_APP, __FUNCTION__, $resultString);

					DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, FALSE);
					return FALSE;
				}
			}

			DBG_RETURN_BOOL(DBGZ_APP, __FUNCTION__, TRUE);
			return TRUE;
		}

		private function GetUserCountsForDate(
			$fileRows,
			$destinationFilename
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "destinationFilename=$destinationFilename");
                        //echo 'destination file'.$destinationFilename;
			$rawOutputFile = fopen($destinationFilename, "w");

			for ($i=0; $i<count($fileRows); $i++)
			{
				//
				// The file contains sections for each time range of videos, where time range is defined to be all
				// videos whose capture time spans are contiguous and/or overlap with the capture time
				// duration of the first video in the time range.
				//
                                $currentCaptureDate = $fileRows[$i]['capturedate'];
				$timeRangeStart = $fileRows[$i]['capturestarttime'];
				$timeRangeEnd = $fileRows[$i]['captureendtime'];
				$videosInTimeRange = array();

				$videosInTimeRange[] = $fileRows[$i]['videoid'];

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Added video ".$fileRows[$i]['videoid']." to time range $timeRangeStart - $timeRangeEnd");

				//
				// Get all the videos in the time range $timeRangeStart - $timeRangeEnd.
				//
				while (($i+1 < count($fileRows))
						&& ($fileRows[$i+1]['capturestarttime'] >= $timeRangeStart)
						&& ($fileRows[$i+1]['capturestarttime'] <= $timeRangeEnd))
				{
					$videosInTimeRange[] = $fileRows[$i]['videoid'];

					DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Added video ".$fileRows[$i]['videoid']." to time range $timeRangeStart - $timeRangeEnd");

					// Extend the time range as needed
					if ($fileRows[$i]['captureendtime'] > $timeRangeEnd)
					{
						DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Expanding time range $timeRangeStart - $timeRangeEnd to $timeRangeStart - ".$fileRows[$i]['captureendtime']);

						$timeRangeEnd = $fileRows[$i]['captureendtime'];
					}

					$i += 1;
				}

				$timeRangeStartObject = new \DateTime($timeRangeStart);

				fwrite(
						$rawOutputFile,
						"VideoStartTime: ".$timeRangeStartObject->format("H:i:s")." ".$timeRangeStartObject->format("m/d/Y")."\nDirection,VideoPos,VideoSpeed,CountType,ObjectType,CountedTime\n"
						);

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Getting layouts for videos captured in time range $timeRangeStart to $timeRangeEnd (video ids ".implode(",", $videosInTimeRange).")");

				// Retrieve layout ids and layout legs for the videos captured on the current day.
				$layoutRows = LayoutRow::Find(
						$this->context->dbcon,
						array("layoutid"),
						array("videoid in (".implode(",", $videosInTimeRange).")", "status IN ('COUNT_COMPLETED', 'QC_STARTED', 'QC_PAUSED', 'QC_COMPLETED')"),
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($layoutRows == NULL)
				{
					DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Skipping videos ".implode(",", $videosInTimeRange)." - no layouts.");
					continue;
				}

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Getting layout legs");

				$layoutIds = array();
				$layoutLegs = array();

				foreach ($layoutRows as &$layoutRow)
				{
					$layoutIds[] = $layoutRow["layoutid"];

					$layoutLegRows = LayoutLegRow::Find(
							$this->context->dbcon,
							array("legindex", "direction"),
							array("layoutid=".$layoutRow["layoutid"]),
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					$layoutLegs[$layoutRow["layoutid"]]["directions"] = array();

					foreach ($layoutLegRows as &$layoutLegRow)
					{
						DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, serialize($layoutLegRow));
						$layoutLegs[$layoutRow["layoutid"]]["directions"][$layoutLegRow["legindex"]] = $layoutLegRow["direction"];
					}
				}

				DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Getting counts");

				if (count($layoutIds) > 0)
				{
					// Get raw counts for the layout
					$countRows = CountRow::Find(
							$this->context->dbcon,
							array("legindex", "videoposition", "videospeed", "counttype", "objecttype", "countedtime"),
							array("layoutid IN (".implode(",", $layoutIds).")", "rejected=0"),
							"videoposition",
							ROW_ASSOCIATIVE,
							$sqlError
							);

					if ($countRows == NULL)
					{
						DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Skipping videos for $currentCaptureDate - no counts.");
						continue;
					}
					else
					{
						foreach ($countRows as &$countRow)
						{
							fwrite($rawOutputFile, $layoutLegs[$countRow["layoutid"]]["directions"][$countRow["legindex"]].",".$countRow['videoposition'].",".$countRow['videospeed'].",".$countRow['counttype'].",".$countRow['objecttype'].",".$countRow['countedtime']."\n");
						}
					}
				}
			}

			DBG_RETURN(DBGZ_VIDEO_JOBSITE, __METHOD__);
		}

		public function GetUserCounts(
			$jobSiteId,
			&$rawOutputFilenames,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_JOBSITE, __METHOD__, "jobSiteId=$jobSiteId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_PROJECTMANAGER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			$numberOfCounts = 0;

			if ($jobSiteId === NULL)
			{
				$resultString = "Mising parameter 'jobsiteid'";
				DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				//
				// Make sure the job site exists and get its location.  We'll use the location as the name
				// of the output file.
				//
				$filter = array("jobsiteid=$jobSiteId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$jobSiteRow = JobSiteRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($jobSiteRow == NULL)
				{
					if ($sqlError != 0)
					{
						$resultString = "SQL error $sqlError";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

						DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
					}
					else
					{
						$resultString = "Job site not found";
						DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);

						DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, FALSE);
					}

					return FALSE;
				}

				// Get the videos for this jobsite
				$filter = array("jobsiteid=$jobSiteId");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				//
				// We'll create one file for each date, so order the query results by date(capturestarttime).
				//
				$fileRows = FileRow::Find(
						$this->context->dbcon,
						array("name", "videoid", "capturestarttime", "captureendtime", "date(capturestarttime) as capturedate"),
						$filter,
						"capturedate, capturestarttime",
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($fileRows == NULL)
				{
					$result = TRUE;
					$resultString = "WARNING: no videos";

					DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
					return $result;
				}

				$rawOutputFilenames = array();

				for ($i=0; $i<count($fileRows); $i++)
				{
					// Get all the rows that have the same capture date.
					$currentCaptureDate = $fileRows[$i]['capturedate'];
					$fileRowsForDate = array();
					$fileRowsForDate[] = $fileRows[$i];

					while (($i + 1 < count($fileRows)) && ($fileRows[$i+1]['capturedate'] === $currentCaptureDate))
					{
						$fileRowsForDate[] = $fileRows[$i+1];
						$i += 1;
					}

					// Create a file for the current capture date.
					$rawOutputFilename = str_replace(array("*", ".", "/", "'", ":", "\"", "&"), "_", $jobSiteRow["sitecode"]."_".$fileRows[$i]["name"]."_".$fileRows[$i]["videoid"])."_".$currentCaptureDate.".txt";
					$rawOutputFilenames[] = $rawOutputFilename;

					DBG_INFO(DBGZ_VIDEO_JOBSITE, __METHOD__, "Getting counts for capture date $currentCaptureDate");

					$this->GetUserCountsForDate($fileRowsForDate, TEMP_FILE_FOLDER."/".$rawOutputFilename);
				}

				if (count($rawOutputFilenames) > 0)
				{
					$result = TRUE;
				}
				else
				{
					$result = FALSE;

					$resultString = "No counts found";
					DBG_ERR(DBGZ_VIDEO_JOBSITE, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_JOBSITE, __METHOD__, $result);
			return $result;
		}
	}
?>
