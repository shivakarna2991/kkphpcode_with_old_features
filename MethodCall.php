<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//ini_set("zlib.output_compression", "On");
//ini_set("zlib.output_compression", 4096);
	header ('Content-Type: text/html; charset=utf8');

	/**
	 * MethodCall framework front end controller
	 * 
	 */

	require_once 'core/core.php';
	require_once 'core/LocalMethodCall.php';
	require_once 'core/jsonutils.php';
	require_once 'core/Common/Classes/Request.php';
	require_once 'core/Common/Classes/Response.php';

        use \Core\Common\Classes\Request;
	use \Core\Common\Classes\Response;

	define("dbg_zones",  DBGZ_METHODCALL | DBGZ_IDAXAUTOLOAD | DBGZ_AUTOLOAD | DBGZ_JOBSITE | DBGZ_JOBSITEROW | DBGZ_TUBE_JOBSITE | DBGZ_VIDEO_JOBSITE | DBGZ_TUBE_VOLUMEREPORT | DBGZ_TUBE_CLASSREPORT | DBGZ_TUBE_SPEEDREPORT | DBGZ_UPLOADFILE);
	define("dbg_levels", DBGL_TRACE | DBGL_INFO | DBGL_WARN | DBGL_ERR | DBGL_EXCEPTION);
	define("dbg_dest",   dbg_dest_log);

	DBG_SET_PARAMS(dbg_zones, dbg_levels, FALSE, FALSE, dbg_dest, dbg_file);

	DBG_ENTER(DBGZ_METHODCALL, "MethodCall");

	/**  Parse the incoming request. */
	$request = new Request();

	if (isset($_SERVER['PATH_INFO']))
	{
		$request->url_elements = explode('/', trim($_SERVER['PATH_INFO'], '/'));
	}
	
	if (empty($request->url_elements))
	{
		die("");
	}

	$request->method = strtolower($_SERVER['REQUEST_METHOD']);

	$classInfo = explode('::', ucfirst($request->url_elements[0]));

	if (count($classInfo) == 2)
	{
		$className = $classInfo[0];
		$methodName = $classInfo[1];
	}
	else
	{
		die("Bad request\r\n");
	}

	$actionName = $request->method;

	switch ($actionName)
	{
		case 'get':
			$request->parameters = $_GET;
			break;

		case 'post':
			$request->parameters = $_POST;

			// If the token is not in the POST parameters then check for it in the _GET parameters.
			if (!isset($request->parameters['_mhdr_token']) && isset($_GET['_mhdr_token']))
			{
				$request->parameters['_mhdr_token'] = $_GET['_mhdr_token'];
			}
			break;

		case 'put':
			parse_str(file_get_contents('php://input'), $put_vars);
			$request->parameters = $put_vars;
			break;
	}

	// If provided in the parameters, we'll use 'deviceid' as the client's location.  Otherwise we'll use it's IP address.

	if (isset($request->parameters['_mhdr_deviceid']))
	{
		$location = $request->parameters['_mhdr_deviceid'];
	}
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '')
	{
		$location = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else
	{
		$location = $_SERVER['REMOTE_ADDR'];
	}

	DBG_INFO(DBGZ_METHODCALL, "MethodCall", "actionName=$actionName, className=$className, methodName=$methodName");

	$response_str = LocalMethodCall(
			$className,
			$methodName,
			$location,
			$request->parameters,
			$_SERVER['HTTP_USER_AGENT']
			);
        
        if(isset($response_str['results']['responder'])){
        $responderval = explode("::", $response_str['results']['responder']);
                        $responderval=$responderval[1];
        }else{
            $responderval='';
        }
        
	/** Send the response to the client. */
	$compressResponse = isset($request->parameters['_mhdr_compressresponse']) ? boolval($request->parameters['_mhdr_compressresponse']) : false;
	$utf8Encode = isset($request->parameters['_mhdr_utf8encode']) ? boolval($request->parameters['_mhdr_utf8encode']) : TRUE;
	//$utf8Encode = isset($request->parameters['_mhdr_utf8encode']) ? boolval($request->parameters['_mhdr_utf8encode']) : false;
        
        if(isset($responderval)  && ($responderval=='GetJobs' || $responderval=='GetJobSites')){
            $compressResponse=0;
            $utf8Encode=TRUE;
        } 
	DBG_INFO(DBGZ_METHODCALL, "MethodCall", "Encoding response: utf8Encode=$utf8Encode, compressResponse=$compressResponse.");

	if ($utf8Encode)
	{
		if (isset($_SERVER['HTTP_ACCEPT']))
		{
			$response_obj = Response::create(utf8json($response_str), $_SERVER['HTTP_ACCEPT']);
		}
		else
		{
			$response_obj = Response::create(utf8json($response_str));
		}
	}
	else
	{
		if (isset($_SERVER['HTTP_ACCEPT']))
		{
			$response_obj = Response::create($response_str, $_SERVER['HTTP_ACCEPT']);                        
                        //$response_obj = Response::create(utf8json($response_str), $_SERVER['HTTP_ACCEPT']);
		}
		else
		{
			$response_obj = Response::create($response_str);
		}
	}

	// use zlib level 6 compression if client accepts it
	if ($compressResponse)
	{
		DBG_INFO(DBGZ_METHODCALL, "MethodCall", "Compressing response.");
		echo gzencode($response_obj->render(), 6);
		//echo $response_obj->render();
	}
	else
	{
		DBG_INFO(DBGZ_METHODCALL, "MethodCall", "Uncompressed response.");
		echo $response_obj->render(); 
	}
	DBG_RETURN(DBGZ_METHODCALL, "MethodCall");
?>
