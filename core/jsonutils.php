<?php
	function millitime()
	{
		$microtime = microtime();
		$comps = explode(' ', $microtime);

		// Note: Using a string here to prevent loss of precision
		// in case of "overflow" (PHP converts it to a double)
		return sprintf('%d%03d', $comps[1], $comps[0] * 1000);
	}
	
	function utf8json(
		$inArray
		)
	{
		/* our return object */
		$newArray = array();

		/* step through inArray */
		foreach ($inArray as $key => $val)
		{
			if (is_array($val))
			{
				/* recurse on array elements */
				$newArray[$key] = utf8json($val);
			}
			else
			{
				/* encode string values */
				$newArray[$key] = utf8_encode($val);
			}
		}

		/* return utf8 encoded array */
		return $newArray;
	}
?>
