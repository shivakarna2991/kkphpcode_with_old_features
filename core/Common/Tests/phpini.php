<?php

	$iniParams = ini_get_all();

	foreach ($iniParams as $name => &$iniParam)
	{
		$global_value = $iniParam["global_value"];
		$local_value = $iniParam["local_value"];
		$access = $iniParam["access"];

		if ($local_value == $global_value)
		{
			echo "$name: '$local_value'\n";
		}
		else
		{
			echo "$name\n";
			echo "\tglobal_value=$global_value\n";
			echo "\tlocal_value=$local_value\n";
			echo "\taccess=$access\n";
		}
	}

?>

