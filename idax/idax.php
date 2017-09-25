<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
	//require_once "core/core.php";
	require_once __DIR__.'/../core/core.php';
	//require_once "core/autoload.php";
	require_once __DIR__.'/../core/autoload.php';;
	require_once "DBParams.php";
	require_once "autoload.php";

	if (!defined("_IDAX_REPORTS_PATH"))
	{
		define ("_IDAX_REPORTS_PATH", PROJECT_ROOT."/idax/Tube/Reports");
	}

	define ("dbg_file", PROJECT_ROOT."/idax/logs/".PROJECT_NAME.".log");

	// Info levels
	define ("INFO_LEVEL_BASIC",             1);
	define ("INFO_LEVEL_SUMMARY",           2);
	define ("INFO_LEVEL_FULL",              3);

	// Device types
	define ("DEVICE_TYPE_METROCOUNT",       1);
	define ("DEVICE_TYPE_KAPTURRKAM",       2);
	define ("DEVICE_TYPE_MIOVISIONSCOUNT",  3); 

	// Study types
	define ("STUDY_TYPE_TMC",                1);
	define ("STUDY_TYPE_ROADWAY",            2);
	define ("STUDY_TYPE_ORIGINDESTINATION",  3);
	define ("STUDY_TYPE_ADT",                4);

	// Debug zones for database tables.
	define ("DBGZ_JOBROW",                   0x0000000000000001);
	define ("DBGZ_TASKROW",                  0x0000000000000002);
	define ("DBGZ_JOBSITEROW",               0x0000000000000004);
	define ("DBGZ_TUBE_INGESTIONROW",        0x0000000000000008);
	define ("DBGZ_TUBE_INGESTIONDATASETROW", 0x0000000000000010);
	define ("DBGZ_TUBE_INGESTIONDATAROW",    0x0000000000000020);
	define ("DBGZ_TUBE_REPORTROW",           0x0000000000000040);
	define ("DBGZ_TUBE_REPORTFORMATROW",     0x0000000000000080);
	define ("DBGZ_VIDEO_FILEROW",            0x0000000000000100);
	define ("DBGZ_VIDEO_LAYOUTROW",          0x0000000000000200);
	define ("DBGZ_VIDEO_LAYOUTNOTEROW",      0x0000000000000400);
	define ("DBGZ_VIDEO_LAYOUTLEGROW",       0x0000000000000800);
	define ("DBGZ_VIDEO_COUNTROW",           0x0000000000001000);
	define ("DBGZ_VIDEOINGESTIONPHASEROW",   0x0000000000002000);
	define ("DBGZ_DEVICEROW",                0x0000000000004000);

	// Debug zones for business logic / processes.
	define ("DBGZ_IDAXAUTOLOAD",             0x0000000000008000);
	define ("DBGZ_IDAXMGR",                  0x0000000000010000);
	define ("DBGZ_DEVICEMGR",                0x0000000000020000);
	define ("DBGZ_DEVICE",                   0x0000000000040000);
	define ("DBGZ_JOBMGR",                   0x0000000000080000);
	define ("DBGZ_JOB",                      0x0000000000100000);
	define ("DBGZ_JOBSITEMGR",               0x0000000000200000);
	define ("DBGZ_JOBSITE",                  0x0000000000400000);
	define ("DBGZ_TASK",                     0x0000000000800000);
	define ("DBGZ_TUBE_JOBSITE",             0x0000000001000000);
	define ("DBGZ_TUBE_VOLUMEREPORT",        0x0000000002000000);
	define ("DBGZ_TUBE_CLASSREPORT",         0x0000000004000000);
	define ("DBGZ_TUBE_SPEEDREPORT",         0x0000000008000000);
	define ("DBGZ_TUBE_REPORTFORMATMGR",     0x0000000010000000);
	define ("DBGZ_VIDEO_JOBSITE",            0x0000000020000000);
	define ("DBGZ_VIDEO_JOBSITEMGR",         0x0000000040000000);
	define ("DBGZ_VIDEO_LAYOUTMGR",          0x0000000080000000);
	define ("DBGZ_VIDEO_LAYOUT",             0x0000000100000000);
	define ("DBGZ_KAPTURRKAM",               0x0000000200000000);
	define ("DBGZ_URLKEYDIRECT",             0x0000000400000000);
	define ("DBGZ_UPLOADFILE",               0x0000000800000000);

	// Account options
	// Password options
	define ("PASSWORD_OPTIONS_REGEX", "(?=\d*)(?=[a-z]*)(?=[A-Z]*).{8,20}");
	define ("EMAIL_OPTIONS_REGEX",    "[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}");

	// Login options
	define ("LOGIN_OPTION_TOKEN_VALIDITY_PERIOD",         (100000 * 60 * 60));        // 100,000 hours - in seconds
	define ("LOGIN_OPTION_EXTEND_TOKEN_VALIDITY_PERIOD",  TRUE);
	define ("LOGIN_OPTION_ALLOW_MULTIPLE_LOGINS",         TRUE);
	define ("LOGIN_OPTION_IMPLICITLOGOUT",                TRUE);
	define ("LOGIN_OPTION_ALLOWED_FAILED_LOGIN_ATTEMPTS", 1000000);
	define ("LOGIN_OPTION_RETAIN_LOGINS_FOR_AUDITING",    TRUE);
?>
