<?php
	namespace Core\Common\Classes;

	class MethodCallContext
	{
		public $dbcon;
		public $account;
		public $token;
		public $location;
		public $client_version;
		public $client_build;
		public $client_os;
		public $client_device;

		public function __construct(
			$dbcon,
			$accountRow,
			$token = NULL,
			$location = "127.0.0.1",
			$parameters = NULL,
			$userAgentString = NULL
			)
		{
			$this->dbcon = $dbcon;
			$this->account = $accountRow;
			$this->token = $token;
			$this->location = $location;

			$this->client_version = isset($parameters['_mhdr_version']) ? $parameters['_mhdr_version'] : "UNKNOWN";
			$this->client_build = isset($parameters['_mhdr_build']) ? $parameters['_mhdr_build'] : "UNKNOWN";

			$this->client_os = 'UNKNOWN';
			$this->client_device = 'UNKNOWN';

			if (isset($userAgentString) && $userAgentString)
			{
				// get client OS type
				if (strpos($userAgentString, "Win") !== false)
				{
					$this->client_os = 'win';
				}
				else if (strpos($userAgentString, "Windows NT") !== false)
				{
					$this->client_os = 'win';
				}
				else if (strpos($userAgentString, "Mac") !== false)
				{
					$this->client_os = 'mac';
				}
				else if (strpos($userAgentString, "Linux") !== false)
				{
					$this->client_os = 'lin';
				}

				// get client device type
				if (strpos($userAgentString, "WPDesktop") !== false)
				{
					$this->client_device = 'WindowsPhone';
				}
				else if (strpos($userAgentString, "Windows Phone") !== false)
				{
					$this->client_device = 'WindowsPhone';
				}
				else if (strpos($userAgentString, "Macintosh") !== false)
				{
					$this->client_device = 'Macintosh';
				}
				else if (strpos($userAgentString, "iPhone") !== false)
				{
					$this->client_device = 'iPhone';
				}
				else if (strpos($userAgentString, "iPad") !== false)
				{
					$this->client_device = 'iPad';
				}
				else if (strpos($userAgentString, "Android") !== false)
				{
					$this->client_device = 'Android';
				}
			}
		}
	}
?>
