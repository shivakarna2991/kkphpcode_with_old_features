<?php
	namespace Core\Common\Classes;

	class MethodCallResponse
	{
	    public static function create(
			$responder,
			$response,
			$result,
			$resultString,
			$data
			)
	    {
			return array(
					"response" => array(
							"responder" => $responder,
							"result" => $result,
							"resultString" => $resultString,
							"data" => $data
							)
					);
	    }
	}
?>
