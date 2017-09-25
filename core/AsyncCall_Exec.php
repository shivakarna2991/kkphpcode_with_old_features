<?php
	require_once 'core/core.php';

	define("dbg_zones",  DBGZ_ASYNCCALL | DBGZ_CORE | DBGZ_ASYNCCALL | DBGZ_ACCOUNTMGR | DBGZ_JOBMGR | DBGZ_JOB | DBGZ_JOBSITE | DBGZ_VIDEO_LAYOUT | DBGZ_VIDEO_FILEROW | DBGZ_VIDEO_COUNTROW | DBGZ_TUBE_JOBSITE | DBGZ_VIDEO_JOBSITE | DBGZ_JOBSITEMGR | DBGZ_JOBSITEROW | DBGZ_URLKEYDIRECT);
	define("dbg_levels", DBGL_TRACE | DBGL_INFO | DBGL_WARN | DBGL_ERR | DBGL_EXCEPTION);
	define("dbg_dest",   dbg_dest_log);

	DBG_SET_PARAMS(dbg_zones, dbg_levels, FALSE, FALSE, dbg_dest, dbg_file);

	$progname = $argv[0];

	DBG_ENTER(DBGZ_ASYNCCALL, "AsyncCall_Exec", "progname=$progname");

	function ExecCall(
		$implementationFile,
		$functionName,
		$argv,
		$argc,
		&$usage
		)
	{
		DBG_ENTER(DBGZ_ASYNCCALL, __FUNCTION__, "implementationFile=$implementationFile, functionName=$functionName");

		$usage = FALSE;

		//
		// The function name can actually be a class method (in the form of "namespace\classname::methodname").
		// The backslashes in the namespace would be dropped off when passed on a command line, so they were replaced
		// with a @ character (which is not valid in namespaces, classnames, and method names).  So we need to put
		// the backslashes back.
		//
		$functionName = str_replace("@", "\\", $functionName);

		$functionParams = array();

		for ($i=3; $i<$argc; $i++)
		{
			if (($i + 1) >= $argc)
			{
				$usage = true;
				break;
			}

			$functionParams[$argv[$i]] = json_decode(stripslashes($argv[$i+1]), true);

			$i += 1;
		}

		if ($usage)
		{
			DBG_RETURN(DBGZ_ASYNCCALL, __FUNCTION__, "improper usage");
			return -1;
		}

		$processUser = posix_getpwuid(posix_geteuid());

		DBG_INFO(DBGZ_ASYNCCALL, __FUNCTION__, "calling function - process username=".$processUser['name']);

		require_once $implementationFile;

		$classInfo = explode('::', $functionName);

		if (count($classInfo == 2))
		{
			$result = call_user_func($classInfo, $functionParams);
		}
		else
		{
			$result = call_user_func($functionName, $functionParams);
		}

		DBG_RETURN_RESULT(DBGZ_ASYNCCALL, __FUNCTION__, $result);
		return $result;
	}

	if ($argc > 2)
	{
		$implementationFile = $argv[1];
		$functionName = $argv[2];
		$numParams = ($argc-3) / 2;

		DBG_INFO(DBGZ_ASYNCCALL, "AsyncCall_Exec", "implementationFile=$implementationFile, functionName=$functionName, numParams=$numParams");

		$result = ExecCall($implementationFile, $functionName, $argv, $argc, $usage);

		if ($usage)
		{
			DBG_INFO(DBGZ_ASYNCCALL, __FUNCTION__, "Usage: ".$progname." implementationFile -f functionName p1name p1value ... pNname pNvalue");
		}

		DBG_RETURN_RESULT(DBGZ_ASYNCCALL, "AsyncCall_Exec", $result);
		return $result;
	}

	DBG_RETURN(DBGZ_ASYNCCALL, "AsyncCall_Exec", "Error: argc=$argc");
?>
