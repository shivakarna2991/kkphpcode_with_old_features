<?php

	//use VideoJobSiteManager;
	//use Idax\Video\Classes\JobSiteManager as VideoJobSiteManager;

	require_once '/home/idax/idax.php';
	require_once '/home/core/autoload.php';
	require_once '/home/idax/autoload.php';
	require_once '/home/core/classes/accountstable.php';
	require_once '/home/idax/classes/jobstable.php';
	require_once '/home/idax/classes/jobsitestable.php';

	use Idax\Video\Data\File;
	use Idax\Video\Data\Layout;
	use Idax\Video\Data\LayoutLeg;
	use Idax\Video\Data\Count;

	$jobs = array(
			array(
					'name' => 'Test Job 1 for KaptureKount',
					'identifier' => 'KaptureCount',
					'type' => 'VideoData',
					'jobsites' => array(
							array(
									'sitecode' => '88',
									'type' => 'VideoData',
									'latitude' => 123.456,
									'longitude' => 65.4321,
									'description' => '148th Ave NE & NE 156 St',
									'direction' => 'Northbound/Southbound',
									'oneway' => '0',
									'countpriority' => '3',
									'videofiles' => array(
											array(
													'name' => 'Segment 1',
													'capturestarttime' => '2015-04-12 18:36:41',
													'captureendtime' => '2015-04-19 13:45:05',
													'filename' => 'segment1.mp4',
													'layouts' => array(
															array(
																	'name' => 'layout 1',
																	'rating' => '1',
																	'status' => "DESIGN_STARTED",
																	'designedby_user' => 29,
																	'countedby_user' => 0,
																	'qcedby_user' => 0,
																	'legs' => array(
																			array(
																					'index' => 0,
																					"direction" => "Northbound",
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.0,1.0,1.0,1.0,1.0",
																					"rturn_pos" => "2.0,2.0,2.0,2.0,2.0",
																					"uturn_pos" => "3.0,3.0,3.0,3.0,3.0",
																					"straight_pos" => "4.0,4.0,4.0,4.0,4.0",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					),
																			array(
																					'index' => 1,
																					"direction" => "Southbound",
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.5,1.5,1.5,1.5,1.5",
																					"rturn_pos" => "2.5,2.5,2.5,2.5,2.5",
																					"uturn_pos" => "3.5,3.5,3.5,3.5,3.5",
																					"straight_pos" => "4.5,4.5,4.5,4.5,4.5",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					)
																			)
																	),
															array(
																	'status' => "DESIGN_COMPLETED",
																	'name' => 'layout 2',
																	'rating' => '2',
																	'designedby_user' => 84,
																	'countedby_user' => 0,
																	'qcedby_user' => 0,
																	'legs' => array(
																			array(
																					'index' => 0,
																					"direction" => "Eastbound",
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.0,1.0,1.0,1.0,1.0",
																					"rturn_pos" => "2.0,2.0,2.0,2.0,2.0",
																					"uturn_pos" => "3.0,3.0,3.0,3.0,3.0",
																					"straight_pos" => "4.0,4.0,4.0,4.0,4.0",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					),
																			array(
																					'index' => 1,
																					"direction" => "Westbound",
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.5,1.5,1.5,1.5,1.5",
																					"rturn_pos" => "2.5,2.5,2.5,2.5,2.5",
																					"uturn_pos" => "3.5,3.5,3.5,3.5,3.5",
																					"straight_pos" => "4.5,4.5,4.5,4.5,4.5",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					)
																			)
																	),
															array(
																	'status' => "COUNT_STARTED",
																	'name' => 'layout 3',
																	'rating' => '4',
																	'designedby_user' => 91,
																	'countedby_user' => 0,
																	'qcedby_user' => 0,
																	'legs' => array(
																			array(
																					'index' => 0,
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.0,1.0,1.0,1.0,1.0",
																					"rturn_pos" => "2.0,2.0,2.0,2.0,2.0",
																					"uturn_pos" => "3.0,3.0,3.0,3.0,3.0",
																					"straight_pos" => "4.0,4.0,4.0,4.0,4.0",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					),
																			array(
																					'index' => 1,
																					"leg_pos" => "2.0,4.0,6.0,8.0,1.0",
																					"lturn_pos" => "1.5,1.5,1.5,1.5,1.5",
																					"rturn_pos" => "2.5,2.5,2.5,2.5,2.5",
																					"uturn_pos" => "3.5,3.5,3.5,3.5,3.5",
																					"straight_pos" => "4.5,4.5,4.5,4.5,4.5",
																					"ped_pos" => "3.0,6.0,9.0,12.0,15.0"
																					)
																			)
																	)
															)
													)
											)
									)
							)
					)
			);

	function CreateData(
		$con,
		$jobData
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		$account = Account::FindOne($con, NULL, array("email='mike@kanopian.com'"), NULL, ROW_OBJECT, $sqlError=0);

		$context = new MethodCallContext($con, $account, NULL, "127.0.0.1", NULL);
		$jobManager = new JobManager($context);
		$videoJobSiteManager = new VideoJobSiteManager($context);
		$layoutManager = new VideoLayoutManager($context);

		foreach ($jobData as &$job)
		{
			DBG_INFO(DBGZ_APP, __FUNCTION__, "Creating job ".$job['name']);

			$jobManager->CreateJob($job['name'], $job['identifier'], $job['type'], $jobObject);

			foreach ($job['jobsites'] as &$jobSite)
			{
				DBG_INFO(DBGZ_APP, __FUNCTION__, "Creating jobsite ".$jobSite['sitecode']);

				$jobManager->CreateJobSite(
						$jobSite['sitecode'],
						$jobObject->getJobId(),
						$jobSite['type'],
						$jobSite['latitude'],
						$jobSite['longitude'],
						$jobSite['description'],
						$jobSite['direction'],
						$jobSite['oneway'],
						$jobSite['countpriority'],
						$alreadyExists,
						$jobSiteObject,
						$resultString
						);

				foreach ($jobSite['videofiles'] as &$videoFile)
				{
					DBG_INFO(DBGZ_APP, __FUNCTION__, "Uploading file ".$videoFile['name']);

					$videoJobSiteManager->UploadVideo(
							$jobSiteObject->getJobSiteId(),
							$videoFile['name'],
							$videoFile['capturestarttime'],
							$videoFile['captureendtime'],
							$videoFile['filename'],
							$videoObject,
							$resultString
							);

					foreach ($videoFile['layouts'] as &$layout)
					{
						DBG_INFO(DBGZ_APP, __FUNCTION__, "Creating layout ".$layout['name']);

						$layoutManager->CreateLayout(
								$videoObject->getVideoId(),
								$layout['name'],
								$layout['rating'],
								$layout['legs'],
								$layoutObject,
								$resultString
								);

						$layoutObject->setStatus($layout['status']);
						$layoutObject->setDesignedBy_User($layout['designedby_user']);
						$layoutObject->setCountedBy_User($layout['countedby_user']);
						$layoutObject->setQCedBy_User($layout['qcedby_user']);
						$layoutObject->CommitChangedFields($sqlError);
					}
				}
			}
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	function EraseData(
		$con,
		$jobData
		)
	{
		DBG_ENTER(DBGZ_APP, __FUNCTION__);

		foreach ($jobData as &$job)
		{
			// Get the job id
			$jobInfo = Job::FindOne($con, array("jobid"), array("name='".$job['name']."'"), NULL, ROW_ASSOCIATIVE, $sqlError=0);

			if ($jobInfo == NULL)
			{
				continue;
			}

			$jobId = $jobInfo['jobid'];

			// Find and delete all the job sites
			$jobSites = JobSite::Find($con, array("jobsiteid"), array("jobid=$jobId"), NULL, ROW_ASSOCIATIVE, $sqlError=0);

			if ($jobSites != NULL)
			{
				foreach ($jobSites as &$jobSite)
				{
					$jobSiteId = $jobSite['jobsiteid'];

					// Find and delete all the videos
					$videos = File::Find($con, array("videoid"), array("jobsiteid=$jobSiteId"), NULL, ROW_ASSOCIATIVE, $sqlError=0);

					if ($videos != NULL)
					{
						foreach ($videos as &$video)
						{
							$videoId = $video['videoid'];

							// Find and delete all the layouts
							$layouts = Layout::Find($con, array("layoutid"), array("videoid=$videoId"), NULL, ROW_ASSOCIATIVE, $sqlError=0);

							if ($layouts != NULL)
							{
								foreach ($layouts as &$layout)
								{
									$layoutId = $layout['layoutid'];

									// Delete all the layout legs and counts for this layout
									LayoutLeg::Delete($con, array("layoutid=$layoutId"), $sqlError=0);
									Count::Delete($con, array("layoutid=$layoutId"), $sqlError=0);

									Layout::Delete($con, array("layoutid=$layoutId"), $sqlError=0);
								}
							}

							File::Delete($con, array("videoid=$videoId"), $sqlError=0);
						}
					}

					JobSite::Delete($con, array("jobsiteid=$jobSiteId"), $sqlError=0);
				}
			}

			Job::Delete($con, array("jobid=$jobId"), $sqlError=0);
		}

		DBG_RETURN(DBGZ_APP, __FUNCTION__);
	}

	DBG_SET_PARAMS(
			DBGZ_APP | DBGZ_JOBMGR | DBGZ_JOBS | DBGZ_VIDEO_JOBSITEMGR | DBGZ_VIDEO_LAYOUTMGR | DBGZ_VIDEOFILE | DBGZ_VIDEO_LAYOUT | DBGZ_VIDEO_LAYOUTLEG,
			DBGL_TRACE | DBGL_INFO | DBGL_ERR | DBGL_WARN,
			FALSE,
			FALSE,
			dbg_dest_terminal
			);
	//DBG_SET_PARAMS(0, 0, FALSE, FALSE, dbg_dest_terminal, FALSE);

	// connect and verify connection
	$con = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

	if (isset($argv[1]))
	{
		if ($argv[1] == '-c')
		{
			CreateData($con, $jobs);
		}
		else if ($argv[1] == '-e')
		{
			EraseData($con, $jobs);
		}
	}
	else
	{
		echo "Usage: ".$argv[0]." [-c | -e]\n";
	}

	mysqli_close($con);
?>
