<?php
	require_once 'core/core.php';

	function AsyncCall(
		$implementationFile,
		$functionName,
		$functionParamsArray
		)
	{
		DBG_ENTER(DBGZ_ASYNCCALL, __FUNCTION__, "implementationFile=$implementationFile, functionName=$functionName, numargs=".count($functionParamsArray));

		//
		// The function name can actually be a class method (in the form of "namespace\classname::methodname").
		// The backslashes in the namespace will be dropped off when passed on a command line.  So replace them
		// with a @ character (which is not valid in namespaces, classnames, and method names).
		//
		$functionName = str_replace("\\", "@", $functionName);

		$commandLine = "php /home/core/AsyncCall_Exec.php $implementationFile $functionName";

		while (($paramValue = current($functionParamsArray)) !== FALSE)
		{
			$paramName = key($functionParamsArray);

			$commandLine .= ' '.$paramName.' "'.addslashes(json_encode($paramValue)).'"';

			next($functionParamsArray);
		}

		//$commandLine .= " > /dev/null 2>/dev/null &";
		$commandLine .= " > /dev/null 2>&1 & echo $!";

		DBG_INFO(DBGZ_ASYNCCALL, __FUNCTION__, "commandLine=$commandLine");

		$retval = shell_exec($commandLine);

		DBG_RETURN_RESULT(DBGZ_ASYNCCALL, __FUNCTION__, $retval);
		return $retval;
	}

	function RunAsyncCallTest()
	{
		AsyncCall("/home/idax/Video/Classes/JobSiteManager.php", "VideoJobSiteManager::IngestVideoFile_Array", array('email'=>'mike@kanopian.com', 'jobsiteid'=>'129', 'videoid'=>'36', 'name'=>'test video upload', 'videofile'=>'/home/idax/temp_file_upload/phpdiari2.mp4'));
	}

	if (isset($argv[1]) && ($argv[1] == '-t'))
	{
		RunAsyncCallTest();
	}
?>