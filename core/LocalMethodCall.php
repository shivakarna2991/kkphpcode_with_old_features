<?php
//echo 'ddd'.CORE_ROOT.'/autoload.php';exit;
	require_once 'core/core.php';
	//require_once '/autoload.php';
	require_once 'idax/autoload.php';
	require_once CORE_ROOT.'/autoload.php';

	use \Core\Common\Classes\AccountManager;
	use \Core\Common\Classes\MethodCallContext;

	function MapPublicClassName(
		$publicClassName
		)
	{
		DBG_ENTER(DBGZ_METHODCALL, __FUNCTION__, "publicClassName=$publicClassName");

		$classMap = array(
				"AccountManager" => "\Core\Common\Classes\AccountManager",
				"IssueManager" => "\Core\Common\Classes\IssueManager",
				"IdaxManager" => "\Idax\Common\Classes\IdaxManager",
				"DeviceManager" => "\Idax\Common\Classes\DeviceManager",
				"Device" => "\Idax\Common\Classes\Device",
				"JobManager" => "\Idax\Common\Classes\JobManager",
				"Job" => "\Idax\Common\Classes\Job",
				"Task" => "\Idax\Common\Classes\Task",
				"JobSiteManager" => "\Idax\Common\Classes\JobSiteManager",
				"JobSite" => "\Idax\Common\Classes\JobSite",
				"TubeJobSite" => "\Idax\Tube\Classes\JobSite",
				"TubeReportFormatManager" => "\Idax\Tube\Classes\ReportFormatManager",
				"VideoJobSite" => "\Idax\Video\Classes\JobSite",
				"VideoJobSiteManager" => "\Idax\Video\Classes\JobSite",
				"VideoLayoutManager" => "\Idax\Video\Classes\LayoutManager",
				"VideoLayout" => "\Idax\Video\Classes\Layout",
				"VideoKapturrKam" => "\Idax\Video\Classes\KapturrKam"
				);

		if (isset($classMap[$publicClassName]))
		{
			$mappedClassName = $classMap[$publicClassName];
		}
		else
		{
			$mappedClassName = $publicClassName;
		}

		DBG_RETURN(DBGZ_METHODCALL, __FUNCTION__, "Mapped $publicClassName to $mappedClassName");
		return $mappedClassName;
	}

	function LocalMethodCall(
		$className,
		$methodName,
		$location,
		$parameters,
		$userAgentString
		)
	{

                    
		DBG_ENTER(DBGZ_METHODCALL, __FUNCTION__, "className=$className, methodName=$methodName, location=$location");

		$accountRow = NULL;
		$token = isset($parameters['_mhdr_token']) ? $parameters['_mhdr_token'] : NULL;
		$validToken = TRUE;  // A "NULL" token is considered valid for anonymous clients
		$tokenexpired = FALSE;

		// connect and verify connection
		$dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		if ($dbcon == NULL)
		{
			DBG_RETURN(DBGZ_METHODCALL, __FUNCTION__, "Failed to connect to database");

			$response_str = array(
					"results" => array(
							'response' => 'failed',
							'responder' => $className,
							'returnval' => array("resultstring" => 'Failed to connect to database')
							)
					);
		}
		else
		{
                    //echo 'params:--$className-'.$className.'--$methodName-'.$methodName.'--$location-'.$location.'--$userAgentString-'.$userAgentString;
                    //echo '<pre>--$parameters-'; print_r($parameters);
                    //echo '$token'.$token;
                  //  echo 'dddddddsa1211sdas113';exit;
			if ($token != NULL)
			{
				$validToken = AccountManager::ValidateToken($dbcon, $token, $location, $accountRow, $tokenexpired);
			}

			if ($validToken == TRUE)
			{
				if ($tokenexpired == TRUE)
				{
					DBG_ERR(DBGZ_METHODCALL, __FUNCTION__, "token expired");

					$validToken = FALSE;

					$response_str = array(
							"results" => array(
									'response' => 'failed',
									'responder' => "AccountManager",
									'returnval' =>  array("resultstring" => "login required")
									)
							);
				}
				else
				{
					$className = MapPublicClassName($className);

					if (class_exists($className))
					{
						$context = new MethodCallContext(
								$dbcon,
								$accountRow,
								$token,
								$location,
								$parameters,
								$userAgentString
								);

						try
						{
							$response_str = call_user_func(
									array($className, "MethodCallDispatcher"),
									$context,
									$methodName,
									$parameters
									);
						}
						catch (Exception $exception)
						{
							DBG_EXCEPTION(DBGZ_METHODCALL, __FUNCTION__, $exception->__toString());

							$response_str = array(
									"results" => array(
											'response' => 'exception',
											'responder' => "$className::$methodName",
											'returnval' =>  array("resultstring" => $exception->__toString())
									)
									);
						}
					}
					else
					{
						DBG_ERR(DBGZ_METHODCALL, __FUNCTION__, "Class $className not found");

						$response_str = array(
								"results" => array(
										'response' => 'failed',
										'responder' => $className,
										'returnval' =>  array("resultstring" => "failed")
								)
								);
					}
				}
			} 
			else
			{
				DBG_ERR(DBGZ_METHODCALL, __FUNCTION__, "invalid token");

				$response_str = array(
						"results" => array(
								'response' => 'failed',
								'responder' => "AccountManager",
								'returnval' =>  array("resultstring" => "login required")
								)
						);
			}

			mysqli_close($dbcon);
		}

		// use zlib level 6 compression if client accepts it
		DBG_RETURN(DBGZ_METHODCALL, __FUNCTION__);
		return $response_str;
	}
?>
