<?php
	namespace Idax\Video\Classes;

	use \Idax\Video\Classes\JobSite as VideoJobSite;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Data\FileRow;
	use \Idax\Video\Data\LayoutRow;
	use \Idax\Video\Data\LayoutLegRow;
	use \Idax\Video\Data\CountRow;

	require_once 'idax/idax.php';

	class LayoutManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "methodName=$methodName");

			$layoutManager = new LayoutManager($context);

			$response = "failed";
			$responder = "LayoutManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateLayout':
					$legs = array();

					$legindex = 0;

					while (isset($parameters["leg_{$legindex}"]))
					{
						$legs[] = array(
								'index' => $legindex,
								'type' => (isset($parameters["leg_{$legindex}_type"])) ? $parameters["leg_{$legindex}_type"] : "",
								'direction' => (isset($parameters["leg_{$legindex}_direction"])) ? $parameters["leg_{$legindex}_direction"] : "",
								'leg_pos' => (isset($parameters["leg_{$legindex}_leg_pos"])) ? $parameters["leg_{$legindex}_leg_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button1_pos' => (isset($parameters["leg_{$legindex}_button1_pos"])) ? $parameters["leg_{$legindex}_button1_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button2_pos' => (isset($parameters["leg_{$legindex}_button2_pos"])) ? $parameters["leg_{$legindex}_button2_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button3_pos' => (isset($parameters["leg_{$legindex}_button3_pos"])) ? $parameters["leg_{$legindex}_button3_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button4_pos' => (isset($parameters["leg_{$legindex}_button4_pos"])) ? $parameters["leg_{$legindex}_button4_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button5_pos' => (isset($parameters["leg_{$legindex}_button5_pos"])) ? $parameters["leg_{$legindex}_button5_pos"] : "0.0,0.0,0.0,0.0,0.0",
								'button1_def' => (isset($parameters["leg_{$legindex}_button1_def"])) ? $parameters["leg_{$legindex}_button1_def"] : "",
								'button2_def' => (isset($parameters["leg_{$legindex}_button2_def"])) ? $parameters["leg_{$legindex}_button2_def"] : "",
								'button3_def' => (isset($parameters["leg_{$legindex}_button3_def"])) ? $parameters["leg_{$legindex}_button3_def"] : "",
								'button4_def' => (isset($parameters["leg_{$legindex}_button4_def"])) ? $parameters["leg_{$legindex}_button4_def"] : "",
								'button5_def' => (isset($parameters["leg_{$legindex}_button5_def"])) ? $parameters["leg_{$legindex}_button5_def"] : ""
								);

						$legindex += 1;
					}

					DBG_INFO(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "parameters=".serialize($parameters));
					DBG_INFO(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "legs=".serialize($legs));

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

				default:
					$response = "failed";
					$responder = "LayoutManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_VIDEO_LAYOUTMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		private function SetLegs(
			$layoutObject,
			$legs,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUTMGR, __METHOD__);

			$result = TRUE;

			$layoutId = $layoutObject->getLayoutId();

			// Delete any existing legs.
			LayoutLegRow::Delete($this->context->dbcon, array("layoutid=$layoutId"), $sqlError);

			// Now add the legs.
			if ($legs != NULL)
			{
				if (count($legs) > 0)
				{
					foreach ($legs as &$leg)
					{
						$sqlError = 0;

						$result = LayoutLegRow::Create(
								$this->context->dbcon,
								$layoutId,
								$leg['index'],
								$leg['type'],
								$leg['direction'],
								$leg['leg_pos'],
								$leg['button1_pos'],
								$leg['button2_pos'],
								$leg['button3_pos'],
								$leg['button4_pos'],
								$leg['button5_pos'],
								$leg['button1_def'],
								$leg['button2_def'],
								$leg['button3_def'],
								$leg['button4_def'],
								$leg['button5_def'],
								$layoutLegRow,
								$sqlError
								);

						if (!$result)
						{
							if ($sqlError != 0)
							{
								$resultString = "SQL error $sqlError";
								DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
							}
							else
							{
								$resultString = "Failure creating layout leg - unknown error";
								DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
							}

							$result = FALSE;

							break;
						}
					}
				}
				else
				{
					$resultString = "No legs";
					DBG_WARN(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
				}
			}
			else
			{
				$resultString = "No legs";
				DBG_WARN(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $result);
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
			DBG_ENTER(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "videoId=$videoId, name=$name, rating=$rating");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_DESIGNER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			$videoJobSite = new VideoJobSite($this->context);

			if ($videoId === NULL)
			{
				$resultString = "Missing required parameter 'videoid'";
				$validParameters = FALSE;
			}
			else if (!$videoJobSite->GetJobSiteVideo($videoId, $jobRow, $jobSiteRow, $videoFile, $resultString))
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
						$jobRow->setLastUpdateTime($date);
						$jobRow->CommitChangedFields($sqlError);

						$jobSiteRow->setLastUpdateTime($date);
						$jobSiteRow->CommitChangedFields($sqlError);

						$result = mysqli_commit($this->context->dbcon);

						if (!$result)
						{
							$sqlError = mysqli_errno($this->context->dbcon);
							$resultString = "SQL error $sqlError";

							DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
						}
					}
					else
					{
						DBG_INFO(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, "SetLegs failed - rolling back transaction");

						mysqli_rollback($this->context->dbcon);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Failure creating layout - unknown error";
					DBG_ERR(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUTMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
