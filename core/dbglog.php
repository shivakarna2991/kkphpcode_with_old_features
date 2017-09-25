<?php
	define ("DBGL_TRACE",       0x80);
	define ("DBGL_INFO",        0x40);
	define ("DBGL_WARN",        0x20);
	define ("DBGL_ERR",         0x10);
	define ("DBGL_EXCEPTION",   0x08);

	define("dbg_dest_terminal", 0x00000001);
	define("dbg_dest_log",      0x00000002);

	$dbg_levels = 0;
	$dbg_zones = 0;
	$dbg_print_lowprio = FALSE;
	$dbg_print_memusage = FALSE;
	$dbg_destination = 0;
	$dbg_log_file = NULL;
	$dbg_tab_level = 0;
	$dbg_tabs = "\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t";

	function DBG_SET_PARAMS(
		$zones,
		$levels,
		$lowprio=FALSE,
		$memusage=FALSE,
		$destination,
		$logfile=NULL
		)
	{
		global $dbg_zones;
		global $dbg_levels;
		global $dbg_print_lowprio;
		global $dbg_print_memusage;
		global $dbg_destination;
		global $dbg_log_file;

		$dbg_zones = $zones;
		$dbg_levels = $levels;
		$dbg_print_lowprio = $lowprio;
		$dbg_print_memusage = $memusage;
		$dbg_destination = $destination;
		$dbg_log_file = $logfile;

		if ($dbg_log_file == NULL)
		{
			// If no dbg_log_file is specified, then remove log file from the destination options.
			$dbg_destination &= ~dbg_dest_log;

			// Restore ini file settings that may have been set in a previous call to DBG_SET_PARAMS
			ini_restore("error_reporting");
			ini_restore("log_errors");
			ini_restore("error_log");
		}

		if (($dbg_destination & dbg_dest_log) == dbg_dest_log)
		{
			// redirect php errors to the specified log file.
			ini_set("error_reporting", E_ALL);
			ini_set("log_errors", 1);
			ini_set("error_log", $dbg_log_file);
		}
	}

	function dbg_write(
		$zone,
		$level,
		$str
		)
	{
		global $dbg_zones;
		global $dbg_levels;
		global $dbg_print_lowprio;
		global $dbg_print_memusage;
		global $dbg_destination;
		global $dbg_log_file;

		global $dbg_tabs;
		global $dbg_tab_level;

		if (($level == DBGL_EXCEPTION)
				|| (($zone & $dbg_zones) == $zone) && (($level & $dbg_levels) == $level))
		{
			$date = date_create();
			$microTimeStamp = microtime(true);
			$timeStamp = floor($microTimeStamp);
			$microSeconds = round(($microTimeStamp - $timeStamp) * 100000);

			if ($dbg_print_memusage)
			{
				//$dbgstr = getmypid().":".$date->format("[D M d H:i:s.$microSeconds Y]").":".memory_get_usage()."/".memory_get_peak_usage().substr($dbg_tabs, 0, $dbg_tab_level)."$str\n";
				$dbgstr = getmypid().":".$date->format("[D M d H:i:s.$microSeconds Y]").":".memory_get_usage()."/".memory_get_peak_usage().": $str\n";
			}
			else
			{
				//$dbgstr = getmypid().":".$date->format("[D M d H:i:s.$microSeconds Y]").substr($dbg_tabs, 0, $dbg_tab_level)."$str\n";
				$dbgstr = getmypid().":".$date->format("[D M d H:i:s.$microSeconds Y]").": $str\n";
			}

			if (($dbg_destination & dbg_dest_terminal) == dbg_dest_terminal)
			{
				echo $dbgstr;
			}

			if (($dbg_destination & dbg_dest_log) == dbg_dest_log)
			{
				error_log($dbgstr, 3, $dbg_log_file);
			}
		}
	}

	function DBG_ENTER(
		$zone,
		$function,
		$args=NULL
		)
	{
		global $dbg_tab_level;

		if ($args == NULL)
		{
			$extra = "";
		}
		else
		{
			$extra = ": $args";
		}

		dbg_write($zone, DBGL_TRACE, "<".$function.$extra);

		$dbg_tab_level += 1;
	}

	function DBG_ENTER_LOWPRIO(
		$zone,
		$function,
		$args=NULL
		)
	{
		global $dbg_print_lowprio;

		if ($dbg_print_lowprio)
		{
			DBG_ENTER($zone, $function, $args);
		}
	}

	function DBG_RETURN(
		$zone,
		$function,
		$args=NULL
		)
	{
		global $dbg_tab_level;

		$dbg_tab_level -= 1;

		if ($args == NULL)
		{
			$extra = "";
		}
		else
		{
			$extra = " $args";
		}

		dbg_write($zone, DBGL_TRACE, "/".$function.">".$extra);
	}

	function DBG_RETURN_LOWPRIO(
		$zone,
		$function,
		$args=NULL
		)
	{
		global $dbg_print_lowprio;

		if ($dbg_print_lowprio)
		{
			DBG_RETURN($zone, $function, $args);
		}
	}

	function DBG_RETURN_RESULT(
		$zone,
		$function,
		$result
		)
	{
		global $dbg_tab_level;

		$dbg_tab_level -= 1;

		dbg_write($zone, DBGL_TRACE, "/$function> returning '$result'");
	}

	function DBG_RETURN_RESULT_LOWPRIO(
		$zone,
		$function,
		$result
		)
	{
		global $dbg_print_lowprio;

		if ($dbg_print_lowprio)
		{
			DBG_RETURN_RESULT($zone, $function, $result);
		}
	}

	function DBG_RETURN_BOOL(
		$zone,
		$function,
		$boolResult
		)
	{
		global $dbg_tab_level;

		$dbg_tab_level -= 1;

		$strResult = ($boolResult == FALSE) ? "FALSE" : "TRUE";

		dbg_write($zone, DBGL_TRACE, "/$function> returning '$strResult'");
	}

	function DBG_RETURN_BOOL_LOWPRIO(
		$zone,
		$function,
		$boolResult
		)
	{
		global $dbg_print_lowprio;

		if ($dbg_print_lowprio)
		{
			DBG_RETURN_BOOL($zone, $function, $boolResult);
		}
	}

	function DBG_INFO(
		$zone,
		$function,
		$str
		)
	{
		dbg_write($zone, DBGL_INFO, ".".$function.": ".$str);
	}

	function DBG_INFO_LOWPRIO(
		$zone,
		$function,
		$str
		)
	{
		global $dbg_print_lowprio;

		if ($dbg_print_lowprio)
		{
			DBG_INFO($zone, $function, $str);
		}
	}

	function DBG_WARN(
		$zone,
		$function,
		$str
		)
	{
		dbg_write($zone, DBGL_WARN, "?".$function.": ".$str);
	}

	function DBG_ERR(
		$zone,
		$function,
		$str
		)
	{
		dbg_write($zone, DBGL_ERR, "!".$function.": ".$str);
	}

	function DBG_EXCEPTION(
		$zone,
		$function,
		$str
		)
	{
		dbg_write($zone, DBGL_EXCEPTION, ".***".$function.": ".$str);
	}
?>
