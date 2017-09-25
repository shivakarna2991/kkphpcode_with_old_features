<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

	/* Generic class autoloader. */

	function idax_autoload_class($class_name)
	{
		DBG_ENTER(DBGZ_IDAXAUTOLOAD, __FUNCTION__, "class_name=$class_name");

		$directories = array(
				_IDAX_REPORTS_PATH.'/Classes/',
				PROJECT_ROOT.'/Common/Classes/'
				);

		$pos = strrpos($class_name, '\\');

		if ($pos)
		{
			//
			// The class name should begin with Idax\ as the root namespace.  We'll remove that since
			// the __DIR__ magic constant contains the root path (which may not be Idax/)
			//
			if (strncmp($class_name, "Idax/", strlen("Idax/")))
			{
				DBG_INFO(DBGZ_IDAXAUTOLOAD, __FUNCTION__, "Stripping Idax from $class_name");
				$class_name = substr($class_name, strlen("Idax"));
			}

			$pos = strrpos($class_name, '\\');

			// retain the trailing namespace separator in the prefix
			$classPath = substr($class_name, 0, $pos + 1);

			// the rest is the relative class name
			$class_name = substr($class_name, $pos + 1);

			$fullClassPath = __DIR__.str_replace("\\", "/", $classPath);

			if (!strcmp($fullClassPath, "/home/idax/Tube/Reports/Classes/"))
			{
				$fullClassPath = _IDAX_REPORTS_PATH;
			}

			DBG_INFO(DBGZ_IDAXAUTOLOAD, __FUNCTION__, "Adding $fullClassPath for class $class_name");

			array_unshift($directories, $fullClassPath);
		}

		foreach ($directories as $directory)
		{
			$filename = $directory . $class_name . '.php';

			DBG_INFO(DBGZ_IDAXAUTOLOAD, __FUNCTION__, "Checking for file $filename");

			if (is_file($filename))
			{
				DBG_INFO(DBGZ_IDAXAUTOLOAD, __FUNCTION__, "Loading file $filename");

				require_once($filename);
				break;
			}
		}

		DBG_RETURN(DBGZ_IDAXAUTOLOAD, __FUNCTION__);
	}

	/** Register autoloader functions. */
	spl_autoload_register('idax_autoload_class');
?>
