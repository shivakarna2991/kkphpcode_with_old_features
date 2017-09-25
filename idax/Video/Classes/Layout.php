<?php
	namespace Idax\Video\Classes;

	use \Idax\Video\Classes\JobSite as VideoJobSite;
	use \Idax\Common\Data\JobSiteRow;
	use \Idax\Video\Data\FileRow;
	use \Idax\Video\Data\LayoutRow;
	use \Idax\Video\Data\LayoutNotesRow;
	use \Idax\Video\Data\LayoutLegRow;
	use \Idax\Video\Data\CountRow;

	require_once 'idax/idax.php';

	class Layout
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "methodName=$methodName");

			$layout = new Layout($context);

			$response = "failed";
			$responder = "Layout::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'Update':
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

					$result = $layout->Update(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['rating']) ? $parameters['rating'] : NULL,
							isset($parameters['videospeed']) ? $parameters['videospeed'] : NULL,
							isset($parameters['lastvideoposition']) ? $parameters['lastvideoposition'] : NULL,
							$legs,
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

				case 'SetNote':
					$result = $layout->SetNote(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['by']) ? $parameters['by'] : NULL,
							isset($parameters['note']) ? $parameters['note'] : NULL,
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

				case 'GetNote':
					$result = $layout->GetNote(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['by']) ? $parameters['by'] : NULL,
							$note,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("note" => $note, "resultstring" => $resultString);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'Delete':
					$result = $layout->Delete(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
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

				case 'StartAction':
					$result = $layout->StartAction(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['actiontype']) ? $parameters['actiontype'] : NULL,
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

				case 'PauseAction':
					$result = $layout->PauseAction(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['actiontype']) ? $parameters['actiontype'] : NULL,
							isset($parameters['lastvideoposition']) ? $parameters['lastvideoposition'] : NULL,
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

				case 'CompleteAction':
					$result = $layout->CompleteAction(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['actiontype']) ? $parameters['actiontype'] : NULL,
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

				case 'AddMovement':
					$result = $layout->AddMovement(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['legindex']) ? $parameters['legindex'] : NULL,
							isset($parameters['counttype']) ? $parameters['counttype'] : NULL,
							isset($parameters['objecttype']) ? $parameters['objecttype'] : NULL,
							isset($parameters['videoposition']) ? $parameters['videoposition'] : NULL,
							isset($parameters['videospeed']) ? $parameters['videospeed'] : NULL,
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

				case 'GetCountData':
					$countTypes = array();

					$i = 0;

					while (isset($parameters['counttype_$i']))
					{
						$countTypes[] = $parameters['counttype_$i'];
					}

					$objectTypes = array();

					$i = 0;

					while (isset($parameters['objecttype_$i']))
					{
						$objectTypes[] = $parameters['objecttype_$i'];
					}


					$result = $layout->GetCountData(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
							isset($parameters['legindex']) ? $parameters['legindex'] : NULL,
							isset($parameters['beginoffset']) ? $parameters['beginoffset'] : NULL,
							isset($parameters['endoffset']) ? $parameters['endoffset'] : NULL,
							isset($parameters['includerejectedcounts']) ? $parameters['includerejectedcounts'] : NULL,
							$countTypes,
							$objectTypes,
							$countedByUser,
							$layoutStatus,
							$countData,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array(
								"countedby_user" => $countedByUser,
								"layout_status" => $layoutStatus,
								"data" => $countData,
								"resultstring" => $resultString
								);
					}
					else
					{
						$response = "failed";
						$returnval = array("resultstring" => $resultString);
					}

					break;

				case 'RejectCount':
					$result = $layout->RejectCount(
							isset($parameters['layoutid']) ? $parameters['layoutid'] : NULL,
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
					$responder = "Layout";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval)
							);

			DBG_RETURN(DBGZ_VIDEO_LAYOUT, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		private function SetLegs(
			$layoutRow,
			$legs,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__);

			$result = TRUE;

			$layoutId = $layoutRow->getLayoutId();

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
								DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
							}
							else
							{
								$resultString = "Failure creating layout leg - unknown error";
								DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
							}

							$result = FALSE;

							break;
						}
					}
				}
				else
				{
					$resultString = "No legs";
					DBG_WARN(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}
			else
			{
				$resultString = "No legs";
				DBG_WARN(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function Update(
			$layoutId,
			$name,
			$rating,
			$videospeed,
			$lastvideoposition,
			$legs,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, name=$name, rating=$rating");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_DESIGNER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'videoid'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$result = FALSE;

						// The layout status must be "DESIGN_STARTED".
						if ($layoutRow->getStatus() != "DESIGN_STARTED")
						{
							$resultString = "Layout status invalid for this operation.";
							DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else
						{
							mysqli_begin_transaction($this->context->dbcon, MYSQLI_TRANS_START_READ_WRITE);

							if ($name !== NULL)
							{
								$layoutRow->setName($name);
							}

							if ($rating !== NULL)
							{
								$layoutRow->setRating($rating);
							}

							if ($videospeed !== NULL)
							{
								$layoutRow->setVideoSpeed($videospeed);
							}

							if ($lastvideoposition !== NULL)
							{
								$layoutRow->setLastVideoPosition($lastvideoposition);
							}

							$result = $this->SetLegs($layoutRow, $legs, $resultString);

							$date = date("Y-m-d H:i:s");

							$jobRow->setLastUpdateTime($date);
							$jobRow->CommitChangedFields($sqlError);

							$jobSiteRow->setLastUpdateTime($date);
							$jobSiteRow->CommitChangedFields($sqlError);

							$layoutRow->setLastUpdateTime($date);
							$layoutRow->CommitChangedFields($sqlError);

							if ($result)
							{
								$result = mysqli_commit($this->context->dbcon);

								if (!$result)
								{
									$sqlError = mysqli_errno($this->context->dbcon);
									$resultString = "SQL error $sqlError";

									DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
								}
							}
							else
							{
								DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, "SetLegs failed - rolling back transaction");

								mysqli_rollback($this->context->dbcon);
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function SetNote(
			$layoutId,
			$by,
			$note,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, by=$by");

			$result = FALSE;

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($by === NULL)
			{
				$resultString = "Missing required parameter 'by'";
				$validParameters = FALSE;
			}
			else if (!in_array($by, array("designer", "counter", "qc")))
			{
				$resultString = "Invalid value for 'by' parameter";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$layoutNotesRow = LayoutNotesRow::FindOne(
							$this->context->dbcon,
							NULL,
							array("layoutid='$layoutId'"),
							NULL,
							ROW_OBJECT,
							$sqlError
							);

					if ($layoutNotesRow != NULL)
					{
						if ($by == "designer")
						{
							$layoutNotesRow->setDesignerNotes($note);
						}
						else if ($by == "counter")
						{
							$layoutNotesRow->setCounterNotes($note);
						}
						else if ($by == "qc")
						{
							$layoutNotesRow->setQCNotes($note);
						}

						$result = $layoutNotesRow->CommitChangedFields($sqlError);
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL error $sqlError";
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
					else
					{
						$sqlError = 0;

						$designerNote = "";
						$counterNote = "";
						$qcNote = "";

						if ($by == "designer")
						{
							$designerNote = $note;
						}
						else if ($by == "counter")
						{
							$counterNote = $note;
						}
						else if ($by == "qc")
						{
							$qcNote = $note;
						}

						$result = LayoutNotesRow::Create(
								$this->context->dbcon,
								$layoutId,
								$designerNote,
								$counterNote,
								$qcNote,
								$layoutNotesRow,
								$sqlError
								);

						if (!$result)
						{
							$resultString = "SQL error $sqlError";
							DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}
			
			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function GetNote(
			$layoutId,
			$by,
			&$note,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, by=$by");

			$result = FALSE;

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($by === NULL)
			{
				$resultString = "Missing required parameter 'by'";
				$validParameters = FALSE;
			}
			else if (!in_array($by, array("designer", "counter", "qc")))
			{
				$resultString = "Invalid value for 'by' parameter";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_ASSOCIATIVE,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$note = "";
					$result = TRUE;

					$layoutNotesRow = LayoutNotesRow::FindOne(
							$this->context->dbcon,
							NULL,
							array("layoutid='$layoutId'"),
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					if ($layoutNotesRow != NULL)
					{
						if ($by == "designer")
						{
							$note = $layoutNotesRow["designer_notes"];
						}
						else if ($by == "counter")
						{
							$note = $layoutNotesRow["counter_notes"];
						}
						else if ($by == "qc")
						{
							$note = $layoutNotesRow["qc_notes"];
						}
					}
					else if ($sqlError != 0)
					{
						$resultString = "SQL error $sqlError";
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}
			
			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function Delete(
			$layoutId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_DESIGNER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				mysqli_begin_transaction($this->context->dbcon, MYSQLI_TRANS_START_READ_WRITE);

				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$result = LayoutRow::Delete(
								$this->context->dbcon,
								array("layoutid='$layoutId'"),
								$sqlError
								);

						if ($result)
						{
							$result = LayoutLegRow::Delete(
									$this->context->dbcon,
									array("layoutid='$layoutId'"),
									$sqlError
									);

							if ($result)
							{
								$result = LayoutNotesRow::Delete(
										$this->context->dbcon,
										array("layoutid='$layoutId'"),
										$sqlError
										);

								if ($result)
								{
									$result = CountRow::Delete(
											$this->context->dbcon,
											array("layoutid='$layoutId'"),
											$sqlError
											);

									if ($result)
									{
										$now = date("Y-m-d H:i:s");

										$jobRow->setLastUpdateTime($now);
										$jobRow->CommitChangedFields($sqlError);

										$jobSiteRow->setLastUpdateTime($now);
										$jobSiteRow->CommitChangedFields($sqlError);

										$result = mysqli_commit($this->context->dbcon);

										if (!$result)
										{
											$sqlError = mysqli_errno($this->context->dbcon);
											$resultString = "SQL error $sqlError";

											DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
										}
									}
									else
									{
										DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to delete counts - rolling back transaction");

										$sqlError = mysqli_errno($this->context->dbcon);
										$resultString = "SQL error $sqlError";

										mysqli_rollback($this->context->dbcon);
									}
								}
							}
							else
							{
								DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to delete layout legs - rolling back transaction");

								$sqlError = mysqli_errno($this->context->dbcon);
								$resultString = "SQL error $sqlError";

								mysqli_rollback($this->context->dbcon);
							}
						}
						else
						{
							DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, "Failed to delete layout - rolling back transaction");

							$sqlError = mysqli_errno($this->context->dbcon);
							$resultString = "SQL error $sqlError";

							DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

							mysqli_rollback($this->context->dbcon);
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function StartAction(
			$layoutId,
			$actionType,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, actionType=$actionType");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($actionType === NULL)
			{
				$resultString = "Missing required parameter 'actiontype'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$layoutStatus = $layoutRow->getStatus();
						$result = FALSE;
						$commitChanges = FALSE;

						switch ($actionType)
						{
							case "DESIGN":
								// Allow user who previously started a design to "Start it again" - effectively resume it.
								if (($layoutStatus == 'DESIGN_STARTED') && ($layoutRow->getDesignedBy_User() == $this->context->account->getAccountId()))
								{
									$result = TRUE;
								}
								// Confirm the caller has proper role level - ACCOUNT_ROLE_DESIGNER
								else if ($this->context->account->getRole() >= ACCOUNT_ROLE_DESIGNER)
								{
									// Confirm the layout status is either DESIGN_COMPLETE or COUNT_PAUSED.
									if (($layoutStatus == 'DESIGN_PAUSED') || ($layoutStatus == 'DESIGN_COMPLETED'))
									{
										// Change the status and countby_user fields
										$layoutRow->setStatus('DESIGN_STARTED');
										$layoutRow->setDesignedBy_User($this->context->account->getAccountId());

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for COUNT action type";
										DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
									}
								}
								else
								{
									$resultString = "Access denied";
									DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
								}

								break;

							case "COUNT":
								// Allow user who previously started a count to "Start it again" - effectively resume it.
								if (($layoutStatus == 'COUNT_STARTED') && ($layoutRow->getCountedBy_User() == $this->context->account->getAccountId()))
								{
									$result = TRUE;
								}
								// Confirm the layout status is either DESIGN_COMPLETE or COUNT_PAUSED.
								else if (($layoutStatus == 'DESIGN_COMPLETED') || ($layoutStatus == 'COUNT_PAUSED'))
								{
									// Change the status and countby_user fields
									$layoutRow->setStatus('COUNT_STARTED');
									$layoutRow->setCountedBy_User($this->context->account->getAccountId());

									$commitChanges = TRUE;
								}
								else
								{
									$resultString = "Invalid layout status for COUNT action type";
									DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
								}

								break;

							case "QC":
								// Allow user who previously started a QC to "Start it again" - effectively resume it.
								if (($layoutStatus == 'QC_STARTED') && ($layoutRow->getQCedBy_User() == $this->context->account->getAccountId()))
								{
									$result = TRUE;
								}
								// Confirm the caller has proper role level - ACCOUNT_ROLE_QC
								else if ($this->context->account->getRole() >= ACCOUNT_ROLE_QUALITYCONTROL)
								{
									// Confirm the layout status is either DESIGN_COMPLETE or COUNT_PAUSED.
									if (($layoutStatus == 'COUNT_COMPLETED') || ($layoutStatus == 'QC_PAUSED'))
									{
										// Change the status and qcedby_user fields
										$layoutRow->setStatus('QC_STARTED');
										$layoutRow->setQCedBy_User($this->context->account->getAccountId());

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for QC action type";
										DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
									}
								}
								else
								{
									$resultString = "Access denied";
									DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
								}

								break;

							default:
								$resultString = "Invalid action type '$actionType'";
								break;
						}

						if ($commitChanges)
						{
							$sqlError = 0;

							$date = date("Y-m-d H:i:s");

							$jobRow->setLastUpdateTime($date);
							$jobRow->CommitChangedFields($sqlError);

							$jobSiteRow->setLastUpdateTime($date);
							$jobSiteRow->CommitChangedFields($sqlError);

							$layoutRow->setLastUpdateTime($date);
							$result = $layoutRow->CommitChangedFields($sqlError);

							if (!$result)
							{
								if ($sqlError != 0)
								{
									$resultString = "SQL error $sqlError";
								}
								else
								{
									$resultString = "Failure starting action - unknown error";
								}
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function PauseAction(
			$layoutId,
			$actionType,
			$lastVideoPosition,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, actionType=$actionType, lastVideoPosition=$lastVideoPosition");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($actionType === NULL)
			{
				$resultString = "Missing required parameter 'actiontype'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$layoutStatus = $layoutRow->getStatus();
						$result = FALSE;
						$commitChanges = FALSE;

						switch ($actionType)
						{
							case "DESIGN":
								// The accountid must match the DesginedBy_user accountid
								if ($this->context->account->getAccountId() == $layoutRow->getDesignedBy_User())
								{
									// Confirm the layout status is DESIGN_STARTED.
									if ($layoutStatus == 'DESIGN_STARTED')
									{
										// Change the status field
										$layoutRow->setStatus('DESIGN_PAUSED');

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for DESIGN action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							case "COUNT":
								// The accountid must match the CountedBy_user accountid
								if ($this->context->account->getAccountId() == $layoutRow->getCountedBy_User())
								{
									// Confirm the layout status is COUNT_STARTED.
									if ($layoutStatus == 'COUNT_STARTED')
									{
										// Change the status field
										$layoutRow->setStatus('COUNT_PAUSED');

										if ($lastVideoPosition !== NULL)
										{
											$layoutRow->setLastVideoPosition($lastVideoPosition);
										}

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for COUNT action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							case "QC":
								// The accountid must match the QCedBy_user accountid
								if ($this->context->account->getAccountId() == $layoutRow->getQCedBy_User())
								{
									// Confirm the layout status is QC_STARTED.
									if ($layoutStatus == 'QC_STARTED')
									{
										// Change the status field
										$layoutRow->setStatus('QC_PAUSED');

										if ($lastVideoPosition !== NULL)
										{
											$layoutRow->setLastVideoPosition($lastVideoPosition);
										}

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for QC action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							default:
								$resultString = "Invalid action type '$actionType'";
								break;
						}

						if ($commitChanges)
						{
							$date = date("Y-m-d H:i:s");

							$jobRow->setLastUpdateTime($date);
							$jobRow->CommitChangedFields($sqlError);

							$jobSiteRow->setLastUpdateTime($date);
							$jobSiteRow->CommitChangedFields($sqlError);

							$layoutRow->setLastUpdateTime($date);
							$result = $layoutRow->CommitChangedFields($sqlError);

							if (!$result)
							{
								if ($sqlError != 0)
								{
									$resultString = "SQL error $sqlError";
								}
								else
								{
									$resultString = "Unknown error";
								}
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout does not exist";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function CompleteAction(
			$layoutId,
			$actionType,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId, actionType=$actionType");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($actionType === NULL)
			{
				$resultString = "Missing required parameter 'actiontype'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$layoutStatus = $layoutRow->getStatus();
						$result = FALSE;
						$commitChanges = FALSE;

						switch ($actionType)
						{
							case "DESIGN":
								if ($this->context->account->getAccountId() == $layoutRow->getCountedBy_User()
										&& ($layoutStatus == "COUNT_STARTED"))
								{
									// This case is for a counter who cancels counting action.  We need to change the
									// status field back to DESIGN_COMPLETED to make it eligible for re-design and/or re-count.
									$layoutRow->setStatus('DESIGN_COMPLETED');

									$commitChanges = TRUE;
								}
								// The accountid must match the DesginedBy_user accountid
								else if ($this->context->account->getAccountId() == $layoutRow->getDesignedBy_User())
								{
									// Confirm the layout status is DESIGN_STARTED or DESIGN_PAUSED.
									if (($layoutStatus == 'DESIGN_STARTED') || ($layoutStatus == 'DESIGN_PAUSED'))
									{
										// Change the status field
										$layoutRow->setStatus('DESIGN_COMPLETED');

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for DESIGN action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							case "COUNT":
								if ($this->context->account->getAccountId() == $layoutRow->getQCedBy_User()
										&& ($layoutStatus == "QC_STARTED"))
								{
									// This case is for a qc user who cancels qc.  We need to change the
									// status field back to COUNT_COMPLETED to make it eligible for qc again.
									$layoutRow->setStatus('COUNT_COMPLETED');

									$commitChanges = TRUE;
								}
								// The accountid must match the CountedBy_user accountid
								else if ($this->context->account->getAccountId() == $layoutRow->getCountedBy_User())
								{
									// Confirm the layout status is COUNT_STARTED or COUNT_PAUSED.
									if (($layoutStatus == 'COUNT_STARTED') || ($layoutStatus == 'COUNT_PAUSED'))
									{
										// Change the status field
										$layoutRow->setStatus('COUNT_COMPLETED');

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for COUNT action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							case "QC":
								// The accountid must match the QCedBy_user accountid
								if ($this->context->account->getAccountId() == $layoutRow->getQCedBy_User())
								{
									// Confirm the layout status is QC_STARTED or QC_PAUSED.
									if (($layoutStatus == 'QC_STARTED') || ($layoutStatus == 'QC_PAUSED'))
									{
										// Change the status field
										$layoutRow->setStatus('QC_COMPLETED');

										$commitChanges = TRUE;
									}
									else
									{
										$resultString = "Invalid layout status for QC action type";
									}
								}
								else
								{
									$resultString = "Access denied";
								}

								break;

							default:
								$resultString = "Invalid action type '$actionType'";
								break;
						}

						if ($commitChanges)
						{
							$date = date("Y-m-d H:i:s");

							$jobRow->setLastUpdateTime($date);
							$jobRow->CommitChangedFields($sqlError);

							$jobSiteRow->setLastUpdateTime($date);
							$jobSiteRow->CommitChangedFields($sqlError);

							$layoutRow->setLastUpdateTime($date);
							$result = $layoutRow->CommitChangedFields($sqlError);

							if (!$result)
							{
								$resultString = "SQL error $sqlError";
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout does not exist";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function AddMovement(
			$layoutId,
			$legIndex,
			$countType,
			$objectType,
			$videoPosition,
			$videoSpeed,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_VIDEO_LAYOUT,
					__METHOD__,
					"layoutId=$layoutId, legIndex=$legIndex, countType=$countType, objectType=$objectType, videoPositiion=$videoPosition, videoSpeed=$videoSpeed"
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}
			else if ($legIndex === NULL)
			{
				$resultString = "Missing required parameter 'legindex'";
				$validParameters = FALSE;
			}
			else if ($countType === NULL)
			{
				$resultString = "Missing required parameter 'counttype'";
				$validParameters = FALSE;
			}
			else if ($objectType === NULL)
			{
				$resultString = "Missing required parameter 'objecttype'";
				$validParameters = FALSE;
			}
			else if ($videoPosition === NULL)
			{
				$resultString = "Missing required parameter 'videoposition'";
				$validParameters = FALSE;
			}
			else if ($videoSpeed === NULL)
			{
				$resultString = "Missing required parameter 'videospeed'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				//
				// Find the layout record to get the countedby_user and the status.  The account id
				// of the client must match the countedby_user.  And the layout status
				// must be "COUNT_STARTED".
				//
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						if ($this->context->account->getAccountId() != $layoutRow->getCountedBy_User())
						{
							$resultString = "Access denied";
							DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else if ($layoutRow->getStatus() != 'COUNT_STARTED')
						{
							$resultString = "Layout status must be COUNT_STARTED";
							DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else
						{
							$sqlError = 0;

							// Get the layutleg for this legindex - ensure it exists.
							$layoutLeg = LayoutLegRow::FindOne(
									$this->context->dbcon,
									array("legindex"),
									array("layoutid=$layoutId", "legindex=$legIndex"),
									NULL,
									ROW_ASSOCIATIVE,
									$sqlError
									);

							if ($layoutLeg != NULL)
							{
								$sqlError = 0;

								// Everything checks out - let's add the count.
								$result = CountRow::Create(
										$this->context->dbcon,
										$layoutId,
										$legIndex,
										$videoPosition,
										$videoSpeed,
										$countType,
										$objectType,
										date("Y-m-d H:i:s"),                      // countedtime
										$this->context->account->getAccountId(),  // countedby_user,
										false,                                    // rejected
										NULL,                                     // rejectedtime
										NULL,                                     // rejectedby_user
										$countObject,
										$sqlError
										);

								if ($result)
								{
									$date = date("Y-m-d H:i:s");

									if ($videoPosition !== NULL)
									{
										$layoutRow->setLastVideoPosition($videoPosition);
									}

									$layoutRow->setLastUpdateTime($date);
									$layoutRow->CommitChangedFields($sqlError);
								}
								else
								{
									if ($sqlError != 0)
									{
										$resultString = "SQL error $sqlError";
										DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
									}
									else
									{
										$resultString = "Unknown error";
										DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
									}
								}
							}
							else if ($sqlError != 0)
							{
								$resultString = "SQL error $sqlError";
								DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
							}
							else
							{
								$resultString = "Layout leg does not exist";
								DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout does not exist";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function GetCountData(
			$layoutId,
			$legIndex,
			$beginOffset,
			$endOffset,
			$includeRejectedCounts,
			$countTypes,
			$objectTypes,
			&$countedBy_User,
			&$layoutStatus,
			&$countData,
			&$resultString
			)
		{
			DBG_ENTER(
					DBGZ_VIDEO_LAYOUT,
					__METHOD__,
					"layoutId=$layoutId, legIndex=$legIndex, beginOffset=$beginOffset, endOffset=$endOffset"
					);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account === NULL)
			{
				$resultString = "access denied - no user account";
				DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, FALSE);
				return FALSE;
			}

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				//
				// Find the layout record to get the countedby_user and the status.
				//
				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						NULL,
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$countedBy_User = $layoutRow->getCountedBy_User();
					$layoutStatus = $layoutRow->getStatus();

					// Find the video file
					$filter = array("videoid={$layoutRow->getVideoId()}");

					if (!$this->context->account->getDeveloper())
					{
						$filter[] = "testdata=0";
					}

					$videoFile = FileRow::FindOne(
							$this->context->dbcon,
							array("capturestarttime", "captureendtime"),
							$filter,
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

					$fields = array("legindex", "videoposition", "counttype", "objecttype", "countedtime", "countedby_user");
					$findFilter = array("layoutid={$layoutRow->getLayoutId()}");

					if ($legIndex !== NULL)
					{
						$findFilter[] = "legindex=$legindex";
					}

					if ($includeRejectedCounts !== NULL)
					{
						if ($includeRejectedCounts)
						{
							$fields[] = "rejected";
							$fields[] = "rejectedtime";
							$fields[] = "rejectedby_user";
						}
						else
						{
							$findFilter[] = "rejected=0";
						}
					}
					else
					{
						$findFilter[] = "rejected=0";
					}

					if (count($countTypes) > 0)
					{
						$countTypeFilter = "";

						foreach ($countTypes as &$countType)
						{
							if ($countTypeFilter == "")
							{
								$countTypeFilter = "counttype='$countType'";
							}
							else
							{
								$countTypeFilter = "$countTypeFilter OR counttype='$countType'";
							}
						}

						$findFilter[] = $countTypeFilter;
					}

					if (count($objectTypes) > 0)
					{
						$objectTypeFilter = "";

						foreach ($objectTypes as &$objectType)
						{
							if ($objectTypeFilter == "")
							{
								$objectTypeFilter = "counttype='$objectType'";
							}
							else
							{
								$objectTypeFilter = "$objectTypeFilter OR counttype='$objectType'";
							}
						}

						$findFilter[] = $objectTypeFilter;
					}

					$sqlError = 0;

					$countData = CountRow::Find(
							$this->context->dbcon,
							$fields,
							$findFilter,
							NULL,
							ROW_ASSOCIATIVE,
							$sqlError
							);

						if ($countData != NULL)
						{
							$result = TRUE;
						}
						else if ($sqlError != 0)
						{
							$resultString = "SQL Error $sqlError";
							DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else
						{
							$resultString = "No matching records";
							DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
				}
				else
				{
					$resultString = "Layout not found";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}

		public function RejectCount(
			$layoutId,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_VIDEO_LAYOUT, __METHOD__, "layoutId=$layoutId");

			$result = FALSE;

			// Parameter validation
			$validParameters = TRUE;

			if ($layoutId === NULL)
			{
				$resultString = "Missing required parameter 'layoutid'";
				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				//
				// Find the layout record to get the qcedby_user and the status.  The account id
				// of the client must match the countedby_user.  And the layout status must be one of
				// "COUNT_STARTED", "COUNT_PAUSED", "COUNT_COMPLETED", "QC_STARTED", "QC_PAUSED".
				//
				$sqlError = 0;

				$filter = array("layoutid='$layoutId'");

				if (!$this->context->account->getDeveloper())
				{
					$filter[] = "testdata=0";
				}

				$layoutRow = LayoutRow::FindOne(
						$this->context->dbcon,
						array("videoid", "qcedby_user", "status"),
						$filter,
						NULL,
						ROW_OBJECT,
						$sqlError
						);

				if ($layoutRow != NULL)
				{
					$videoJobSite = new VideoJobSite($this->context);

					$result = $videoJobSite->GetJobSiteVideo(
							$layoutRow->getVideoId(),
							$jobRow,
							$jobSiteRow,
							$videoFile,
							$resultString
							);

					if ($result)
					{
						$result = FALSE;

						if ($this->context->account->getAccountId() != $layoutRow->getQCedBy_User())
						{
							$resultString = "Access denied";
							DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else if (!in_array($layoutRow->getStatus(), array("COUNT_PAUSED", "COUNT_COMPLETED", "QC_STARTED", "QC_PAUSED", "QC_COMPLETED")))
						{
							$resultString = "Layout status invalid for this operation";
							DBG_INFO(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
						}
						else
						{
							mysqli_begin_transaction($this->context->dbcon, MYSQLI_TRANS_START_READ_WRITE);

							// Everything checks out - let's add the count.
							$result = CountRow::Update(
									$this->context->dbcon,
									array("rejected=1", "rejectedtime='".date("Y-m-d H:i:s")."'", "rejectedby_user={$this->context->account->getAccountId()}"),
									array("layoutid=$layoutId", "rejected!=1"),
									$sqlError
									);

							if ($result)
							{
								// Set the status to DESIGN_COMPLETE to make it available for counting.
								$date = date("Y-m-d H:i:s");

								$jobRow->setLastUpdateTime($date);
								$result = $jobRow->CommitChangedFields($sqlError);

								$jobSiteRow->setLastUpdateTime($date);
								$result = $jobSiteRow->CommitChangedFields($sqlError);

								$layoutRow->setStatus("DESIGN_COMPLETED");
								$layoutRow->setLastUpdateTime($date);
								$result = $layoutRow->CommitChangedFields($sqlError);

								if ($result)
								{
									$result = mysqli_commit($this->context->dbcon);
								}
								else
								{
									mysqli_rollback($this->context->dbcon);

									$resultString = "SQL error $sqlError";
									DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
								}
							}
							else
							{
								$resultString = "SQL error $sqlError";
								DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
							}
						}
					}
					else
					{
						DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
					}
				}
				else if ($sqlError != 0)
				{
					$resultString = "SQL error $sqlError";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
				else
				{
					$resultString = "Layout does not exist";
					DBG_ERR(DBGZ_VIDEO_LAYOUT, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_VIDEO_LAYOUT, __METHOD__, $result);
			return $result;
		}
	}
?>
