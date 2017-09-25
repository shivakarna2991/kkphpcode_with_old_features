<?php

	$mimeContentTypeByFileExtension = array(
			"doc" => "application/msword",
			"docx" => "application/msword",
			"jpg" => "image/jpeg",
			"jpeg" => "image/jpeg",
			"log" => "text/plain",
			"m3u8" => "application/x-mpegurl",
			"mp3" => "audio/mpeg3",
			"pdf" => "application/pdf",
			"ppt" => "application/mspowerpoint",
			"ts" => "audio/mpeg3",
			"txt" => "text/plain",
			"xls" => "application/msexcel",
			"xlsx" => "application/msexcel",
			"json" => "application/json"
			);

	function GetMimeTypeByFileExtension(
		$extension
		)
	{
		global $mimeContentTypeByFileExtension;

		$fileType = $mimeContentTypeByFileExtension[$extension];

		if (!isset($fileType))
		{
			$filetype = '/application/octet-stream';
		}

		return $fileType;
	}

?>
