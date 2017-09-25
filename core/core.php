<?php
	define ("CORE_ROOT", __DIR__);

	require_once CORE_ROOT."/dbglog.php";

	define ("ROW_OBJECT",      1);
	define ("ROW_NUMERIC",     2);
	define ("ROW_ASSOCIATIVE", 3);

	// Debug zones for database tables.
	define ("DBGZ_ACCOUNTROW",         0x8000000000000000);
	define ("DBGZ_ACCOUNTLINKROW",     0x4000000000000000);
	define ("DBGZ_LOGINROW",           0x2000000000000000);
	define ("DBGZ_ISSUEROW",           0x1000000000000000);
	define ("DBGZ_ISSUEATTACHMENTROW", 0x0800000000000000);
	define ("DBGZ_ISSUEHISTORYROW",    0x0400000000000000);
	define ("DBGZ_SERVERROW",          0x0200000000000000);
	define ("DBGZ_JOBQUEUEROW",        0x0100000000000000);

	// Debug zones for business logic / processes.
	define ("DBGZ_CORE",               0x0080000000000000);
	define ("DBGZ_AUTOLOAD",           0x0040000000000000);
	define ("DBGZ_METHODCALL",         0x0020000000000000);
	define ("DBGZ_APP",                0x0010000000000000);
	define ("DBGZ_ACCOUNTMGR",         0x0008000000000000);
	define ("DBGZ_ACCOUNTLINKMGR",     0x0004000000000000);
	define ("DBGZ_LOGINMGR",           0x0002000000000000);
	define ("DBGZ_ISSUEMGR",           0x0001000000000000);
	define ("DBGZ_AWSFILEMGR",         0x0000800000000000);
	define ("DBGZ_ASYNCCALL",          0x0000400000000000);
	define ("DBGZ_JOBQUEUESERVER",     0x0000200000000000);

	// Account states - states are mutually exclusive - can only have one state.
	define("ACCOUNT_STATE_INACTIVE",             0);
	define("ACCOUNT_STATE_ACTIVE",               1);
	define("ACCOUNT_STATE_REGISTERING",          2);
	define("ACCOUNT_STATE_LOCKED",               3);
	define("ACCOUNT_STATE_VALIDATING_EMAIL",     4);
	define("ACCOUNT_STATE_SETPASSWORD_REQUIRED", 5);

	// Account roles - roles are mutually exclusive - can only have one role.
	define ("ACCOUNT_ROLE_ANONYMOUS",            0);
	define ("ACCOUNT_ROLE_USER",                 1);
	define ("ACCOUNT_ROLE_QUALITYCONTROL",       2);
	define ("ACCOUNT_ROLE_DESIGNER",             3);
	define ("ACCOUNT_ROLE_PROJECTMANAGER",       4);
	define ("ACCOUNT_ROLE_ADMIN",                5);

	// Account link states - states are mutually exclusive - can only have one state.
	define("ACCOUNTLINK_STATE_INACTIVE",         0);
	define("ACCOUNTLINK_STATE_ACTIVE",           1);
	define("ACCOUNTLINK_STATE_USED",             2);
	define("ACCOUNTLINK_STATE_EXPIRED",          3);
	define("ACCOUNTLINK_STATE_LOCKED",           4);

	// global defines for project folder locations 
	define ("PROJECT_NAME", "idax");
	define ("PROJECT_ROOT", dirname(__DIR__));
	// temp upload/download file folder location
	define ("TEMP_FILE_FOLDER", PROJECT_ROOT . "/temp_file_upload");
//require_once "D:\\xampp\htdocs\home\idax\idax.php";
require_once "/var/www/html/home/idax/idax.php";
//require_once "/var/www/html/va_aws/idax/idax.php";
//require_once "\var\www\html\va_aws\idax\idax.php";
	//require_once PROJECT_ROOT."/".PROJECT_NAME.".php";

?>
