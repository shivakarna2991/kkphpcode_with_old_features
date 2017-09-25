<?php

	namespace Idax\Tube\Classes;

	require_once '/home/core/core.php';
	require_once '/home/idax/idax.php';

	use \Idax\Tube\Data\ReportFormatRow;

	class ReportFormatManager
	{
		private $context = NULL;

		public static function MethodCallDispatcher(
			$context,
			$methodName,
			$parameters
			)
		{
 			DBG_ENTER(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, "methodName=$methodName");

			$reportFormatManager = new ReportFormatManager($context);

			$response = "failed";
			$responder = "ReportFormatManager::$methodName";
			$returnval = "failed";

			switch ($methodName)
			{
				case 'CreateReportFormat':
					$reportFields = array();

					$i = 0;

					while (isset($parameters["reportfield_".$i]))
					{
						DBG_INFO(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, "reportfield_{$i}=".$parameters["reportfield_".$i]);

						$reportFields[] = $parameters["reportfield_{$i}"];

						$i += 1;
					}

					if (count($reportTypes) == 0)
					{
						DBG_WARN(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, "No type parameters");
					}

					$result = $reportFormatManager->CreateReportFormat(
							isset($parameters['name']) ? $parameters['name'] : NULL,
							isset($parameters['displayorder']) ? $parameters['displayorder'] : NULL,
							$reportFields,
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

				case 'GetReportFormats':
					$result = $reportFormatManager->GetReportFormats(
							$reportFormats,
							$resultString
							);

					if ($result)
					{
						$response = "success";
						$returnval = array("reportformats" => $reportFormats, "resultstring" => $resultString);
					}
					else
					{
						$returnval = array("resultstring" => $resultString);
					}

					break;

				default:
					$response = "failed";
					$responder = "ReportFormatManager";
					$returnval = "method not found";
					break;
			}

			DBG_INFO(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, "responder=$responder, response=$response");

			$response_str = array(
					"results" => array(
							'response' => $response,
							'responder' => $responder,
							'returnval' => $returnval
							)
					);

			DBG_RETURN(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__);
			return $response_str;
		}

		public function __construct(
			$context
			)
		{
			$this->context = $context;
		}

		public function CreateReportFormat(
			$name,
			$displayOrder,
			$reportFields,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, "name=$name, displayOrder=$displayOrder");

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, FALSE);
				return FALSE;
			}
			else if ($this->context->account->getRole() < ACCOUNT_ROLE_PROJECTMANAGER)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, FALSE);
				return FALSE;
			}

			// Validate parameters
			$validParameters = TRUE;

			if ($name === NULL)
			{
				$resultString = "Missing parameter 'name'";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($displayOrder === null)
			{
				$resultString = "Missing parameter 'displayorder'";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if (count($reportFields) === 0)
			{
				$resultString = "Missing parameter 'reportfield'";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				$validParameters = FALSE;
			}

			if ($validParameters)
			{
				$sqlError = 0;

				if (count($reportFields) == 0)
				{
					$reportFields = "";
				}
				else
				{
					// Convert to string.
					$reportFields = json_encode($reportFields);
				}

				$result = ReportFormatRow::Create(
						$this->context->dbcon,
						$name,
						$displayOrder,
						$reportFields,
						$reportFormatRow,
						$sqlError
						);

				if (!$result)
				{
					$resultString = "SQL Error $sqlError";
					DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);
				}
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $result);
			return TRUE;
		}

		public function GetReportFormats(
			&$reportFormatRows,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__);

			$result = FALSE;

			// Make sure we have a valid user
			if ($this->context->account == NULL)
			{
				$resultString = "Access denied";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);

				DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, FALSE);
				return FALSE;
			}

			$sqlError = 0;

			$reportFormatRows = ReportFormatRow::Find(
					$this->context->dbcon,
					NULL,
					NULL,
					"displayorder",
					ROW_ASSOCIATIVE,
					$sqlError
					);

			if ($reportFormatRows != NULL)
			{
				foreach ($reportFormatRows as &$reportFormatRow)
				{
					// Convert the JSON-encoded string into an associative array
					$reportFormatRow["fields"] = json_decode($reportFormatRow["fields"], true);
				}

				$result = TRUE;
			}
			else if ($sqlError != 0)
			{
				$resultString = "SQL Error $sqlError";
				DBG_ERR(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);
			}
			else
			{
				$result = TRUE;

				$resultString = "WARNING: No report formats";
				DBG_WARN(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_TUBE_REPORTFORMATMGR, __METHOD__, $result);
			return TRUE;
		}
	}
?>
