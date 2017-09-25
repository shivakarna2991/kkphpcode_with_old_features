<?php

namespace Core\Common\Classes;

require_once 'core/core.php';
require_once 'core/fileutils.php';
require_once 'idax/vendor/Amazon/vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class AWSFileManager {

    private $dbcon = NULL;
    private $bucketname = NULL;
    private $s3;

    /**
     * Default directory persmissions (destination dir)
     */
    protected $default_permissions = 0755;

    /**
     * File post array
     *
     * @var array
     */
    protected $files_post = array();

    /**
     * Destination directory
     *
     * @var string
     */
    protected $destination;

    /**
     * Fileinfo
     *
     * @var object
     */
    protected $finfo;

    /**
     * Data about file
     *
     * @var array
     */
    public $file = array();

    /**
     * Max. file size
     *
     * @var int
     */
    protected $max_file_size;

    /**
     * Allowed mime types
     *
     * @var array
     */
    protected $mimes = array();

    /**
     * External callback object
     *
     * @var obejct
     */
    protected $external_callback_object;

    /**
     * External callback methods
     *
     * @var array
     */
    protected $external_callback_methods = array();

    /**
     * Temp path
     *
     * @var string
     */
    protected $tmp_name;

    /**
     * Validation errors
     *
     * @var array
     */
    protected $validation_errors = array();

    /**
     * Filename (new)
     *
     * @var string
     */
    protected $filename;

    /**
     * Internal callbacks (filesize check, mime, etc)
     *
     * @var array
     */
    private $callbacks = array();

    /**
     * Root dir
     *
     * @var string
     */
    protected $root;

    /**
     * Return upload object
     *
     * $destination		= 'path/to/your/file/destination/folder';
     *
     * @param string $destination
     * @param string $root
     * @return Upload
     */
    /* public function __construct(
      $bucketname, $region, $awsKey, $awsSecret
      ) {
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
      } */
    public function __construct(
    $destination, $root = false, $key = false,$secret = false
    ) {
        echo 'dest'.$destination;exit;
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__);

        /* $this->bucketname = $bucketname;
          $this->s3 = S3Client::factory(
          array(
          'region' => $region,
          'version' => '2006-03-01',
          'credentials' => array(
          'key' => $awsKey,
          'secret' => $awsSecret
          )
          )
          ); */
        if ($root) {

            $this->root = $root;
        } else {

            //$this->root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
            $this->root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        }

        // set & create destination path
        if (!$this->set_destination($destination)) {

            throw new Exception('Upload: Can\'t create destination. ' . $this->root . $this->destination);
        }

        //create finfo object
        $this->finfo = new finfo();

        DBG_RETURN(DBGZ_AWSFILEMGR, __METHOD__);
    }
    
    /**
	 * Set target filename
	 *
	 * @param string $filename
	 */
	public function set_filename($filename) {

		$this->filename = $filename;

	}

        
	/**
	 * Get current state data
	 *
	 * @return array
	 */
	public function get_state() {

		return $this->file;

	}
        

    public function UploadFile(
    $sourceFilename, $destinationFilename, $cannedacl, $overwrite, &$resultString
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "sourceFilename=$sourceFilename, destinationFilename=$destinationFilename, cannedacl=$cannedacl, overwrite=$overwrite");

        $result = FALSE;

        $file_parts = pathinfo($sourceFilename);

        $extension = $file_parts['extension'];
        $basefile = $file_parts['basename'];

        $mimetype = GetMimeTypeByFileExtension($extension);

        $filecontents = file_get_contents($sourceFilename);

        if ($filecontents === FALSE) {
            $resultString = "file_get_contents('$sourceFilename') failed";
            DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
        } else {
            if ($cannedacl == NULL) {
                $cannedacl = "private";
            }

            $result = $this->UploadData($destinationFilename, $cannedacl, $filecontents, $mimetype, $overwrite, $resultString);
        }

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }

    // Uploads a file to the bucket
    public function UploadData(
    $name, $cannedacl, $contents, $contenttype, $overwrite, &$resultString
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "name=$name, cannedacl=$cannedacl, contenttype=$contenttype, overwrite=$overwrite");

        $result = FALSE;

        if ($overwrite || !$this->FileExists($name)) {
            if ($cannedacl == NULL) {
                $cannedacl = "private";
            }

            $retry = TRUE;
            $retryOnTimeoutCount = 5;

            while ($retry) {
                $retry = FALSE;

                try {
                    /* $object_attributes = array(
                      'ACL' => $cannedacl,
                      'Bucket' => $this->bucketname,
                      'Key' => $name,
                      'Body' => $contents,
                      'ContentType' => $contenttype
                      );

                      // Upload a file.
                      $response = $this->s3->putObject($object_attributes);

                      $statusCode = $response["@metadata"]["statusCode"]; */
                    if(empty($this->filename)){
			$this->create_new_filename();
		}

		//set filename
		$this->file['filename']	= $this->filename;

		//set full path
		$this->file['full_path'] = $this->root . $this->destination . $this->filename;
        	$this->file['path'] = $this->destination . $this->filename;

		$status = move_uploaded_file($this->tmp_name, $this->file['full_path']);

		//checks whether upload successful
		if (!$status) {
			throw new Exception('Upload: Can\'t upload file.');
		}

		//done
		$this->file['status']	= true;

                    if ($statusCode == 200) {
                        $result = TRUE;

                        // We can poll the object until it is accessible
                        $this->s3->waitUntil(
                                'ObjectExists', array('Bucket' => $this->bucketname, 'Key' => $name)
                        );
                    } else {
                        $resultString = "file not transferred successfully - statusCode=$statusCode";
                        DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
                    }
                } catch (InvalidArgumentException $e) {
                    $resultString = "InvalidArgumentException occured during transfer";
                    DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
                } catch (S3Exception $e) {
                    $AwsErrorType = $e->getAwsErrorType();
                    $AwsErrorCode = $e->getAwsErrorCode();
                    $ExceptionCode = "unknown"; //$e->getExceptionCode();
                    $StatusCode = "unknown"; //$e->getStatusCode();

                    if (($AwsErrorCode == "RequestTimeout") && ($retryOnTimeoutCount > 0)) {
                        $retryOnTimeoutCount -= 1;
                        $retry = TRUE;
                    } else {
                        $resultString = "S3Exception: AwsErrorType=$AwsErrorType, AwsErrorCode=$AwsErrorCode, ExceptionCode=$ExceptionCode, StatusCode=$StatusCode";
                        DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
                    }
                } catch (Aws\Exception\CredentialsException $e) {
                    $resultString = "Aws\\Exception\\CredentialsException occured during transfer";
                    DBG_ERR(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
                }
            }
        } else {
            $resultString = "file already exists in bucket.";
            DBG_INFO(DBGZ_AWSFILEMGR, __METHOD__, $resultString);
        }

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }

    public function FileExists(
    $filename
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filename=$filename");

        $result = $this->s3->doesObjectExist($this->bucketname, $filename);

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }

    public function GetFile(
    $filename, &$fileContents
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filename=$filename");

        $result = FALSE;

        $response = $this->s3->getObject(
                array(
                    "Bucket" => $this->bucketname,
                    "Key" => $filename
                )
        );

        $statusCode = $response["@metadata"]["statusCode"];

        if ($statusCode == 200) {
            $result = TRUE;
            $fileContents = (string) $response['Body'];
        }

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }

    public function GetFileListings(
    $filenameBeginsWith, &$fileListings
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filenameBeginsWith=$filenameBeginsWith");

        $fileListings = array();

        $descriptor = array("Bucket" => $this->bucketname);

        if ($filenameBeginsWith != NULL) {
            $descriptor["Prefix"] = $filenameBeginsWith;
        }

        //
        // We want to get a listing of all files, but listObjects might
        // not return all in one shot.  So need to loop, making additional
        // calls until we have everything.
        //
			$listedAllObjects = FALSE;

        while (!$listedAllObjects) {
            $response = $this->s3->listObjects($descriptor);

            $statusCode = $response["@metadata"]["statusCode"];

            if ($statusCode == 200) {
                $result = TRUE;

                foreach ($response["Contents"] as &$object) {
                    $fileListings[] = $object["Key"];
                }

                if ($response["IsTruncated"]) {
                    DBG_INFO(DBGZ_AWSFILEMGR, __METHOD__, "Truncated - last key was " . $fileListings[count($fileListings) - 1]);
                    $descriptor["Marker"] = $fileListings[count($fileListings) - 1];
                } else {
                    $listedAllObjects = TRUE;
                }
            }
        }

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }

    public function DeleteFile(
    $filename
    ) {
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
    ) {
        DBG_ENTER(DBGZ_AWSFILEMGR, __METHOD__, "filenameBeginsWith=$filenameBeginsWith");

        $result = TRUE;

        $this->s3->deleteMatchingObjects(
                $this->bucketname, $filenameBeginsWith
        );

        DBG_RETURN_BOOL(DBGZ_AWSFILEMGR, __METHOD__, $result);
        return $result;
    }
    
    /**
	 * Set data about file
	 */
	protected function set_file_data() {

		$file_size = $this->get_file_size();

		$this->file = array(
			'status'				=> false,
			'destination'			=> $this->destination,
			'size_in_bytes'			=> $file_size,
			'size_in_mb'			=> $this->bytes_to_mb($file_size),
			'mime'					=> $this->get_file_mime(),
			'original_filename'		=> $this->file_post['name'],
			'tmp_name'				=> $this->file_post['tmp_name'],
			'post_data'				=> $this->file_post,
		);

	}

	/**
	 * Set validation error
	 *
	 * @param string $message
	 */
	public function set_error($message) {

		$this->validation_errors[] = $message;

	}


	/**
	 * Return validation errors
	 *
	 * @return array
	 */
	public function get_errors() {

		return $this->validation_errors;

	}


	/**
	 * Set external callback methods
	 *
	 * @param object $instance_of_callback_object
	 * @param array $callback_methods
	 */
	public function callbacks($instance_of_callback_object, $callback_methods) {

		if (empty($instance_of_callback_object)) {

			throw new Exception('Upload: $instance_of_callback_object can\'t be empty.');

		}

		if (!is_array($callback_methods)) {

			throw new Exception('Upload: $callback_methods data type need to be array.');

		}

		$this->external_callback_object	 = $instance_of_callback_object;
		$this->external_callback_methods = $callback_methods;

	}


	/**
	 * Execute callbacks
	 */
	protected function validate() {

		//get curent errors
		$errors = $this->get_errors();

		if (empty($errors)) {

			//set data about current file
			$this->set_file_data();

			//execute internal callbacks
			$this->execute_callbacks($this->callbacks, $this);

			//execute external callbacks
			$this->execute_callbacks($this->external_callback_methods, $this->external_callback_object);

		}

	}


	/**
	 * Execute callbacks
	 */
	protected function execute_callbacks($callbacks, $object) {

		foreach($callbacks as $method) {

			$object->$method($this);

		}

	}


	/**
	 * File mime type validation callback
	 *
	 * @param obejct $object
	 */
	protected function check_mime_type($object) {

		if (!empty($object->mimes)) {

			if (!in_array($object->file['mime'], $object->mimes)) {

				$object->set_error('Mime type not allowed.');

			}

		}

	}


	/**
	 * Set allowed mime types
	 *
	 * @param array $mimes
	 */
	public function set_allowed_mime_types($mimes) {

		$this->mimes		= $mimes;

		//if mime types is set -> set callback
		$this->callbacks[]	= 'check_mime_type';

	}


	/**
	 * File size validation callback
	 *
	 * @param object $object
	 */
	protected function check_file_size($object) {

		if (!empty($object->max_file_size)) {

			$file_size_in_mb = $this->bytes_to_mb($object->file['size_in_bytes']);

			if ($object->max_file_size <= $file_size_in_mb) {

				$object->set_error('File is too big.');

			}

		}

	}


	/**
	 * Set max. file size
	 *
	 * @param int $size
	 */
	public function set_max_file_size($size) {

		$this->max_file_size	= $size;

		//if max file size is set -> set callback
		$this->callbacks[]	= 'check_file_size';

	}


	/**
	 * Set File array to object
	 *
	 * @param array $file
	 */
	public function file($file) {

		$this->set_file_array($file);

	}


	/**
	 * Set file array
	 *
	 * @param array $file
	 */
	protected function set_file_array($file) {

		//checks whether file array is valid
		if (!$this->check_file_array($file)) {

			//file not selected or some bigger problems (broken files array)
			$this->set_error('Please select file.');

		}

		//set file data
		$this->file_post = $file;

		//set tmp path
		$this->tmp_name  = $file['tmp_name'];

	}


	/**
	 * Checks whether Files post array is valid
	 *
	 * @return bool
	 */
	protected function check_file_array($file) {

		return isset($file['error'])
			&& !empty($file['name'])
			&& !empty($file['type'])
			&& !empty($file['tmp_name'])
			&& !empty($file['size']);

	}


	/**
	 * Get file mime type
	 *
	 * @return string
	 */
	protected function get_file_mime() {

		return $this->finfo->file($this->tmp_name, FILEINFO_MIME_TYPE);

	}


	/**
	 * Get file size
	 *
	 * @return int
	 */
	protected function get_file_size() {

		return filesize($this->tmp_name);

	}


	/**
	 * Set destination path (return TRUE on success)
	 *
	 * @param string $destination
	 * @return bool
	 */
	protected function set_destination($destination) {

		$this->destination = $destination . DIRECTORY_SEPARATOR;

		return $this->destination_exist() ? TRUE : $this->create_destination();

	}


	/**
	 * Checks whether destination folder exists
	 *
	 * @return bool
	 */
	protected function destination_exist() {

		return is_writable($this->root . $this->destination);

	}


	/**
	 * Create path to destination
	 *
	 * @param string $dir
	 * @return bool
	 */
	protected function create_destination() {

		return mkdir($this->root . $this->destination, $this->default_permissions, true);

	}


	/**
	 * Set unique filename
	 *
	 * @return string
	 */
	protected function create_new_filename() {

		$filename = sha1(mt_rand(1, 9999) . $this->destination . uniqid()) . time();
		$this->set_filename($filename);

	}


	/**
	 * Convert bytes to mb.
	 *
	 * @param int $bytes
	 * @return int
	 */
	protected function bytes_to_mb($bytes) {

		return round(($bytes / 1048576), 2);

	}

}

?>
