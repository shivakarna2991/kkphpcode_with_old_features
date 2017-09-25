<?php

	//require_once 'core/core.php';
	require_once __DIR__.'/../core/core.php';

	use \Core\Common\Classes\LoginManager;

	function ValidateToken(
		$token,
		$location
		)
	{
		// connect and verify connection
		$dbcon = mysqli_connect(IDAX_DATABASE_HOST, IDAX_DATABASE_USERNAME, IDAX_DATABASE_PASSWORD, IDAX_DATABASE_NAME);

		if ($dbcon == NULL)
		{                    //echo 'dsdsdsd';exit;
			$response_str = array(
					"results" => array(
							'response' => 'failed',
							'responder' => "ValidateToken",
							'returnval' => 'Failed to connect to database'
							)
					);
		}
		else
		{
                    //echo 'sssss';exit;
			$result = LoginManager::ValidateToken(
					$dbcon,
					$token,
					$location,
					$accountid,
					$tokenExpired
					);

			if (!$result || $tokenExpired)
			{
				$response_str = array(
						"results" => array(
								'response' => 'failed',
								'responder' => "ValidateToken",
								'returnval' => 'login required'
								)
						);
			}
			else
			{
				$response_str = array(
						"results" => array(
								'response' => 'success',
								'responder' => "ValidateToken",
								'returnval' => ''
								)
						);
			}

			mysqli_close($dbcon);
		}

		return $response_str;
	}
?>
