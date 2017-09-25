<?php

	/** database connection constants - idaxdata */
      	/*define("IDAX_DATABASE_HOST", "vadata.crllkva3db2t.us-west-2.rds.amazonaws.com");
	define("IDAX_DATABASE_NAME", "vadata");
	define("IDAX_DATABASE_USERNAME", "vadbadmin");
	define("IDAX_DATABASE_PASSWORD", "VaDBMaster");*/
	define("IDAX_DATABASE_HOST", "localhost");
	//define("IDAX_DATABASE_NAME", "vadata");
	define("IDAX_DATABASE_NAME", "va_sanbox");
	define("IDAX_DATABASE_USERNAME", "root");
	define("IDAX_DATABASE_PASSWORD", "root");
	
	/** AWS secure connection parameters */
	/*define("AWSKEY", "AKIAJSZV463JAITTLCYQ");
	define("AWSSECRET", "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");
	define("AWSREGION", "us-west-2");

	// Production buckets
	define("BUCKETNAME", "va-data");
	define("IDAX_DATA_BUCKET", "va-data");
	define("IDAX_ATTACHMENTS_BUCKET", "va-attachments");
	define("IDAX_VIDEOFILES_BUCKET", "va-videofiles");
	define("IDAX_JOBSERVERLOG_BUCKET", "va-jobserverlog");*/
        
        define("AWSKEY", "");
	define("AWSSECRET", "");
	define("AWSREGION", "");

	// Production buckets
	define("BUCKETNAME", "va-data");
	define("IDAX_DATA_BUCKET", "va-data");
	define("IDAX_ATTACHMENTS_BUCKET", "va-attachments");
	define("IDAX_VIDEOFILES_BUCKET", "va-videofiles");
	define("IDAX_JOBSERVERLOG_BUCKET", "va-jobserverlog");
?>
