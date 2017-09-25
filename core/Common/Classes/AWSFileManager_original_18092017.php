<?php

	namespace Core\Common\Classes;

	require_once 'core/core.php';
	require_once 'core/fileutils.php';
	require_once 'idax/vendor/Amazon/vendor/autoload.php';

	use Aws\S3\S3Client;
	use Aws\S3\Exception\S3Exception;

	class AWSFileManager
	{
		private $dbcon = NULL;
		private $bucketname = NULL;
		private $s3;

		public function __construct(
			$bucketname,
			$region,
			$awsKey,
			$awsSecret
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__);

			$this->bucketname = $bucketname;
			$this->s3 = S3Client::factory(
					array(
							'region' => $region,
							'version' => '2006-03-01',
							'credentials' => array(
									'key' => $awsKey,
									'secret' => $awsSecret
									)
							)
					);

			DBG_RETURN(DBGZ_AWSFILEMGR, __METHOD__);
		}

		public function UploadFile(
			$sourceFilename,
			$destinationFilename,
			$cannedacl,
			$overwrite,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "sourceFilename=$sourceFilename, destinationFilename=$destinationFilename, cannedacl=$cannedacl, overwrite=$overwrite");

			$result = FALSE;

			$file_parts = pathinfo($sourceFilename);

			$extension = $file_parts['extension'];
			$basefile = $file_parts['basename'];

			$mimetype = GetMimeTypeByFileExtension($extension);

			$filecontents = file_get_contents($sourceFilename);

			if ($filecontents === FALSE)
			{
				$resultString = "file_get_contents('$sourceFilename') failed";
				DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
			}
			else
			{
				if ($cannedacl == NULL)
				{
					$cannedacl = "private";
				}

				$result = $this->UploadData($destinationFilename, $cannedacl, $filecontents, $mimetype, $overwrite, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		// Uploads a file to the bucket
		public function UploadData(
			$name,
			$cannedacl,
			$contents,
			$contenttype,
			$overwrite,
			&$resultString
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "name=$name, cannedacl=$cannedacl, contenttype=$contenttype, overwrite=$overwrite");

			$result = FALSE;

			if ($overwrite || !$this->FileExists($name))
			{
				if ($cannedacl == NULL)
				{
					$cannedacl = "private";
				}

				$retry = TRUE;
				$retryOnTimeoutCount = 5;

				while ($retry)
				{
					$retry = FALSE;

					try
					{
						$object_attributes = array(
								'ACL' => $cannedacl,
								'Bucket' => $this->bucketname,
								'Key' => $name,
								'Body' => $contents,
								'ContentType' => $contenttype
								);

						// Upload a file.
						$response = $this->s3->putObject($object_attributes);

						$statusCode = $response["@metadata"]["statusCode"];

						if ($statusCode == 200)
						{
							$result = TRUE;

							// We can poll the object until it is accessible
							$this->s3->waitUntil(
									'ObjectExists',
									array('Bucket' => $this->bucketname, 'Key' => $name)
									);
						}
						else
						{
							$resultString = "file not transferred successfully - statusCode=$statusCode";
							DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
						}
					}
					catch (InvalidArgumentException $e)
					{
						$resultString = "InvalidArgumentException occured during transfer";
						DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
					}
					catch (S3Exception $e)
					{
						$AwsErrorType = $e->getAwsErrorType();
						$AwsErrorCode = $e->getAwsErrorCode();
						$ExceptionCode = "unknown";//$e->getExceptionCode();
						$StatusCode = "unknown";//$e->getStatusCode();

						if (($AwsErrorCode == "RequestTimeout")
								&& ($retryOnTimeoutCount > 0))
						{
							$retryOnTimeoutCount -= 1;
							$retry = TRUE;
						}
						else
						{
							$resultString = "S3Exception: AwsErrorType=$AwsErrorType, AwsErrorCode=$AwsErrorCode, ExceptionCode=$ExceptionCode, StatusCode=$StatusCode";
							DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
						}
					}
					catch (Aws\Exception\CredentialsException $e)
					{
						$resultString = "Aws\\Exception\\CredentialsException occured during transfer";
						DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
					}
				}
			}
			else
			{
				$resultString = "file already exists in bucket.";
				DBG_INFO(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
			}

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		public function FileExists(
			$filename
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filename=$filename");

			$result = $this->s3->doesObjectExist($this->bucketname, $filename);

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		public function GetFile(
			$filename,
			&$fileContents
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filename=$filename");

			$result = FALSE;

			$response = $this->s3->getObject(
					array(
							"Bucket" => $this->bucketname,
				 			"Key" => $filename
							)
					);

			$statusCode = $response["@metadata"]["statusCode"];

			if ($statusCode == 200)
			{
				$result = TRUE;
				$fileContents = (string) $response['Body'];
			}

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		public function GetFileListings(
			$filenameBeginsWith,
			&$fileListings
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filenameBeginsWith=$filenameBeginsWith");

			$fileListings = array();

			$descriptor = array("Bucket" => $this->bucketname);

			if ($filenameBeginsWith != NULL)
			{
				$descriptor["Prefix"] = $filenameBeginsWith;
			}

			//
			// We want to get a listing of all files, but listObjects might
			// not return all in one shot.  So need to loop, making additional
			// calls until we have everything.
			//
			$listedAllObjects = FALSE;

			while (!$listedAllObjects)
			{
				$response = $this->s3->listObjects($descriptor);

				$statusCode = $response["@metadata"]["statusCode"];

				if ($statusCode == 200)
				{
					$result = TRUE;

					foreach ($response["Contents"] as &$object)
					{
						$fileListings[] = $object["Key"];
					}

					if ($response["IsTruncated"])
					{
						DBG_INFO(DBGZ_AWSFILEMGR, __METHOD__, "Truncated - last key was ".$fileListings[count($fileListings)-1]);
						$descriptor["Marker"] = $fileListings[count($fileListings)-1];
					}
					else
					{
						$listedAllObjects = TRUE;
					}
				}
			}

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		public function DeleteFile(
			$filename
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__);

			$object_attributes = array(
					'Bucket' => $this->bucketname,
					'Key' => $filename,
					);

			// Delete the file.
			$result = $this->s3->deleteObject($object_attributes);

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}

		public function DeleteFiles(
			$filenameBeginsWith
			)
		{
			DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filenameBeginsWith=$filenameBeginsWith");

			$result = TRUE;

			$this->s3->deleteMatchingObjects(
					$this->bucketname,
					$filenameBeginsWith
					);

			DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
			return $result;
		}
	}
?>
