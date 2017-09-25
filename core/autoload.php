<?php

	/* Generic class autoloader. */
	function autoload_class($class_name)
	{
		DBG_ENTER(DBGZ_AUTOLOAD, __FUNCTION__, "class_name=$class_name");

		$directories = array(
				'/home/core/Common/Classes/'
				);

		$pos = strrpos($class_name, '\\');

		if ($pos)
		{
			//
			// The class name should begin with Core\ as the root namespace.  We'll remove that since
			// the __DIR__ magic constant contains the root path (which may not be Core/)
			//
			if (strncmp($class_name, "Core/", strlen("Core/")))
			{
				DBG_INFO(DBGZ_AUTOLOAD, __FUNCTION__, "Stripping Core from $class_name");
				$class_name = substr($class_name, strlen("Core"));
			}

			$pos = strrpos($class_name, '\\');

			// retain the trailing namespace separator in the prefix
			$classPath = substr($class_name, 0, $pos + 1);

			// the rest is the relative class name
			$class_name = substr($class_name, $pos + 1);

			$fullClassPath = __DIR__.str_replace("\\", "/", $classPath);

			DBG_INFO(DBGZ_AUTOLOAD, __FUNCTION__, "Adding $fullClassPath for class $class_name");

			array_unshift($directories, $fullClassPath);
		}

		foreach ($directories as $directory)
		{
			$filename = $directory . $class_name . '.php';

			DBG_INFO(DBGZ_AUTOLOAD, __FUNCTION__, "Checking for file $filename");

			if (is_file($filename))
			{
				DBG_INFO(DBGZ_AUTOLOAD, __FUNCTION__, "Loading file $filename");

				require_once($filename);
				break;
			}
		}

		DBG_RETURN(DBGZ_AUTOLOAD, __FUNCTION__);
	}

	/** Register autoloader functions. */
	spl_autoload_register('autoload_class');
?>
