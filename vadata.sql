-- MySQL dump 10.13  Distrib 5.5.41, for debian-linux-gnu (x86_64)
--
-- Host: vadata.crllkva3db2t.us-west-2.rds.amazonaws.com    Database: vadata
-- ------------------------------------------------------
-- Server version	5.6.27-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `core_accountlinks`
--

DROP TABLE IF EXISTS `core_accountlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_accountlinks` (
  `accountid` int(10) unsigned NOT NULL,
  `email` varchar(200) DEFAULT NULL,
  `urlkey` varchar(200) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `creationtime` datetime DEFAULT NULL,
  `expirationtime` datetime DEFAULT NULL,
  `usedtime` datetime DEFAULT NULL,
  `usecount` int(10) unsigned DEFAULT NULL,
  `state` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`accountid`),
  UNIQUE KEY `accountid` (`accountid`),
  UNIQUE KEY `urlkey` (`urlkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_accounts`
--

DROP TABLE IF EXISTS `core_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_accounts` (
  `accountid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL DEFAULT '',
  `state` int(11) DEFAULT '0',
  `creationtime` datetime DEFAULT NULL,
  `registeredtime` datetime DEFAULT NULL,
  `lastlogintime` timestamp NULL DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `passwordhash` varchar(255) NOT NULL,
  `failedloginattempts` int(10) unsigned DEFAULT '0',
  `role` int(10) unsigned DEFAULT NULL,
  `rating` int(10) unsigned DEFAULT NULL,
  `developer` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`email`),
  UNIQUE KEY `accountid` (`accountid`)
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_issueattachments`
--

DROP TABLE IF EXISTS `core_issueattachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_issueattachments` (
  `attachmentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issueid` int(10) unsigned NOT NULL,
  `lastupdated` datetime DEFAULT NULL,
  `filename` varchar(256) DEFAULT NULL,
  `bucketfilename` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`attachmentid`)
) ENGINE=InnoDB AUTO_INCREMENT=370 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_issues`
--

DROP TABLE IF EXISTS `core_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_issues` (
  `issueid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned DEFAULT NULL,
  `accountid` int(10) unsigned DEFAULT NULL,
  `opendate` datetime DEFAULT NULL,
  `app` varchar(64) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `reprosteps` varchar(1000) DEFAULT NULL,
  `state` int(10) unsigned DEFAULT NULL,
  `lastupdated` datetime DEFAULT NULL,
  `lastupdatedby` varchar(100) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `priority` int(10) unsigned DEFAULT NULL,
  `assignedto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`issueid`)
) ENGINE=InnoDB AUTO_INCREMENT=672 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_jobqueue`
--

DROP TABLE IF EXISTS `core_jobqueue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_jobqueue` (
  `jobqueueid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `serverinstanceid` varchar(25) DEFAULT NULL,
  `addedtime` datetime DEFAULT CURRENT_TIMESTAMP,
  `jobname` varchar(50) DEFAULT NULL,
  `jobparams` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`jobqueueid`)
) ENGINE=InnoDB AUTO_INCREMENT=579 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_jobservers`
--

DROP TABLE IF EXISTS `core_jobservers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_jobservers` (
  `instanceid` varchar(25) NOT NULL DEFAULT '',
  `status` varchar(50) DEFAULT NULL,
  `ipaddress` char(16) DEFAULT NULL,
  `manualtakedown` int(10) unsigned DEFAULT '0',
  `creationtime` datetime DEFAULT CURRENT_TIMESTAMP,
  `lastupdatetime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`instanceid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `core_logins`
--

DROP TABLE IF EXISTS `core_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `core_logins` (
  `loginid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountid` int(10) unsigned NOT NULL,
  `loggedintime` datetime DEFAULT CURRENT_TIMESTAMP,
  `loggedouttime` datetime DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `tokenexpirationtime` int(10) unsigned DEFAULT NULL,
  `loggedinlocation` varchar(100) DEFAULT NULL,
  UNIQUE KEY `loginid` (`loginid`)
) ENGINE=InnoDB AUTO_INCREMENT=12598 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_deployed_devices`
--

DROP TABLE IF EXISTS `idax_deployed_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_deployed_devices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deviceid` int(10) unsigned DEFAULT NULL,
  `jobsiteid` int(10) unsigned NOT NULL,
  `location` varchar(50) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `status` int(10) unsigned NOT NULL,
  `secret` varchar(500) DEFAULT NULL,
  `ipv4address` char(16) DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `dateranges` varchar(200) DEFAULT NULL,
  `timeranges` varchar(100) DEFAULT NULL,
  `config` varchar(5000) DEFAULT NULL,
  `lastupdatetime` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `devicelastupdatetime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_devices`
--

DROP TABLE IF EXISTS `idax_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_devices` (
  `deviceid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(10) unsigned DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serialnumber` varchar(100) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `config` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`deviceid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_jobs`
--

DROP TABLE IF EXISTS `idax_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_jobs` (
  `jobid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `number` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `studytype` int(10) unsigned DEFAULT NULL,
  `office` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `active` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `creationdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `orderdate` date DEFAULT NULL,
  `deliverydate` date DEFAULT NULL,
  `lastupdatetime` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jobid`)
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_jobsites`
--

DROP TABLE IF EXISTS `idax_jobsites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_jobsites` (
  `jobsiteid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `sitecode` varchar(100) DEFAULT NULL,
  `taskid` int(10) unsigned DEFAULT NULL,
  `jobid` int(10) unsigned NOT NULL,
  `deviceids` varchar(100) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `creationtime` datetime DEFAULT CURRENT_TIMESTAMP,
  `setupdate` date DEFAULT NULL,
  `durations` varchar(200) DEFAULT NULL,
  `dateranges` varchar(200) DEFAULT NULL,
  `timeblocks` varchar(200) DEFAULT NULL,
  `timeranges` varchar(200) DEFAULT NULL,
  `state` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `notes` varchar(100) DEFAULT NULL,
  `n_street` varchar(100) DEFAULT NULL,
  `s_street` varchar(100) DEFAULT NULL,
  `e_street` varchar(100) DEFAULT NULL,
  `w_street` varchar(100) DEFAULT NULL,
  `ne_street` varchar(100) DEFAULT NULL,
  `nw_street` varchar(100) DEFAULT NULL,
  `se_street` varchar(100) DEFAULT NULL,
  `sw_street` varchar(100) DEFAULT NULL,
  `direction` varchar(25) DEFAULT NULL,
  `oneway` tinyint(1) DEFAULT '0',
  `countpriority` tinyint(3) unsigned DEFAULT NULL,
  `reportformat` varchar(50) DEFAULT NULL,
  `reportparameters` varchar(1000) DEFAULT NULL,
  `lastupdatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`jobsiteid`),
  UNIQUE KEY `jobid_sitecode` (`jobid`,`sitecode`),
  FULLTEXT KEY `idax_jobsites_sitecode_description_notes_ft` (`sitecode`,`description`,`notes`)
) ENGINE=InnoDB AUTO_INCREMENT=3268 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tasks`
--

DROP TABLE IF EXISTS `idax_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tasks` (
  `taskid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `jobid` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `setupdate` date DEFAULT NULL,
  `devicetype` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `assignedto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`taskid`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tube_ingestiondata`
--

DROP TABLE IF EXISTS `idax_tube_ingestiondata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tube_ingestiondata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ingestionid` int(10) unsigned DEFAULT NULL,
  `jobid` int(10) unsigned NOT NULL,
  `jobsiteid` int(10) unsigned NOT NULL,
  `ds` int(10) unsigned DEFAULT NULL,
  `trignum` char(8) DEFAULT NULL,
  `ht` char(2) DEFAULT NULL,
  `occurred` datetime DEFAULT NULL,
  `dr` char(4) DEFAULT NULL,
  `speed` float DEFAULT NULL,
  `wb` float DEFAULT NULL,
  `hdwy` float DEFAULT NULL,
  `gap` float DEFAULT NULL,
  `ax` int(10) unsigned DEFAULT NULL,
  `gp` int(10) unsigned DEFAULT NULL,
  `rho` float DEFAULT NULL,
  `cl` int(10) unsigned DEFAULT NULL,
  `nm` char(8) DEFAULT NULL,
  `vehicle` char(2) DEFAULT NULL,
  `coercedsequence` tinyint(1) DEFAULT NULL,
  `other` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobsiteid_occurred` (`jobsiteid`,`occurred`)
) ENGINE=InnoDB AUTO_INCREMENT=106041723 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tube_ingestiondatasets`
--

DROP TABLE IF EXISTS `idax_tube_ingestiondatasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tube_ingestiondatasets` (
  `datasetid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ingestionid` int(10) unsigned DEFAULT NULL,
  `jobid` int(10) unsigned NOT NULL,
  `jobsiteid` int(10) unsigned NOT NULL,
  `ds` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `attribute` varchar(100) DEFAULT NULL,
  `direction` char(100) DEFAULT NULL,
  `surveybegintime` datetime DEFAULT NULL,
  `surveyendtime` datetime DEFAULT NULL,
  `zone` varchar(100) DEFAULT NULL,
  `file` varchar(100) DEFAULT NULL,
  `identifier` varchar(100) DEFAULT NULL,
  `algorithm` varchar(100) DEFAULT NULL,
  `datatype` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`datasetid`)
) ENGINE=InnoDB AUTO_INCREMENT=2539 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tube_ingestions`
--

DROP TABLE IF EXISTS `idax_tube_ingestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tube_ingestions` (
  `ingestionid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobid` int(10) unsigned NOT NULL,
  `jobsiteid` int(10) unsigned NOT NULL,
  `accountid` int(10) unsigned DEFAULT NULL,
  `ingestdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ingestionkey` varchar(128) DEFAULT NULL,
  `reversed` tinyint(1) NOT NULL,
  `bucketfilename` varchar(120) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `subtitle` varchar(100) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `filterbegintime` datetime DEFAULT NULL,
  `filterendtime` datetime DEFAULT NULL,
  `includedclasses` varchar(100) DEFAULT NULL,
  `speedrangehigh` int(10) unsigned DEFAULT NULL,
  `speedrangelow` int(10) unsigned DEFAULT NULL,
  `direction` varchar(100) DEFAULT NULL,
  `separation` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `scheme` varchar(100) DEFAULT NULL,
  `units` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ingestionid`),
  UNIQUE KEY `ingestionkey` (`ingestionkey`)
) ENGINE=InnoDB AUTO_INCREMENT=762 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tube_reportformats`
--

DROP TABLE IF EXISTS `idax_tube_reportformats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tube_reportformats` (
  `name` varchar(100) NOT NULL,
  `displayorder` int(10) unsigned NOT NULL,
  `reportclasses` varchar(100) DEFAULT NULL,
  `fields` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`name`),
  UNIQUE KEY `displayorder` (`displayorder`),
  UNIQUE KEY `displayorder_2` (`displayorder`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_tube_reports`
--

DROP TABLE IF EXISTS `idax_tube_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_tube_reports` (
  `reportid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `creationdate` datetime DEFAULT CURRENT_TIMESTAMP,
  `ingestionid` int(10) unsigned DEFAULT NULL,
  `reportformat` varchar(50) DEFAULT NULL,
  `reportparameters` varchar(1000) DEFAULT NULL,
  `jobid` int(10) unsigned NOT NULL,
  `jobsiteid` int(10) unsigned NOT NULL,
  `sitecode` int(10) unsigned NOT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  `bucketfilename` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`reportid`)
) ENGINE=InnoDB AUTO_INCREMENT=9847 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_counts`
--

DROP TABLE IF EXISTS `idax_video_counts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_counts` (
  `layoutid` int(10) unsigned DEFAULT NULL,
  `legindex` tinyint(3) unsigned DEFAULT NULL,
  `videoposition` float DEFAULT NULL,
  `videospeed` float DEFAULT NULL,
  `counttype` varchar(10) DEFAULT NULL,
  `objecttype` varchar(25) DEFAULT NULL,
  `countedtime` datetime DEFAULT NULL,
  `countedby_user` int(10) unsigned DEFAULT NULL,
  `rejected` tinyint(1) DEFAULT NULL,
  `rejectedtime` datetime DEFAULT NULL,
  `rejectedby_user` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_files`
--

DROP TABLE IF EXISTS `idax_video_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_files` (
  `videoid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `jobsiteid` int(10) unsigned DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `cameralocation` varchar(50) DEFAULT NULL,
  `filesize` int(10) unsigned DEFAULT NULL,
  `uploadtime` int(10) unsigned DEFAULT NULL,
  `addedtime` datetime DEFAULT NULL,
  `capturestarttime` datetime DEFAULT NULL,
  `captureendtime` datetime DEFAULT NULL,
  `bucketfileprefix` varchar(128) DEFAULT NULL,
  `status` varchar(200) DEFAULT NULL,
  `lastupdatetime` datetime DEFAULT NULL,
  `phase` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`videoid`)
) ENGINE=InnoDB AUTO_INCREMENT=1482 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_ingestionphases`
--

DROP TABLE IF EXISTS `idax_video_ingestionphases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_ingestionphases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `videoid` int(10) unsigned DEFAULT NULL,
  `phase` varchar(100) DEFAULT NULL,
  `starttime` timestamp NULL DEFAULT NULL,
  `endtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11919 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_layoutlegs`
--

DROP TABLE IF EXISTS `idax_video_layoutlegs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_layoutlegs` (
  `layoutid` int(10) unsigned DEFAULT NULL,
  `legindex` tinyint(4) DEFAULT NULL,
  `type` varchar(10) DEFAULT 'TMC',
  `direction` varchar(50) DEFAULT NULL,
  `leg_pos` varchar(50) DEFAULT NULL,
  `lturn_pos` varchar(50) DEFAULT NULL,
  `rturn_pos` varchar(50) DEFAULT NULL,
  `uturn_pos` varchar(50) DEFAULT NULL,
  `straight_pos` varchar(50) DEFAULT NULL,
  `ped_pos` varchar(50) DEFAULT NULL,
  `button1_pos` varchar(50) DEFAULT NULL,
  `button2_pos` varchar(50) DEFAULT NULL,
  `button3_pos` varchar(50) DEFAULT NULL,
  `button4_pos` varchar(50) DEFAULT NULL,
  `button5_pos` varchar(50) DEFAULT NULL,
  `button1_def` varchar(50) DEFAULT 'UTURN',
  `button2_def` varchar(50) DEFAULT 'LTURN',
  `button3_def` varchar(50) DEFAULT 'STRAIGHT',
  `button4_def` varchar(50) DEFAULT 'RTURN',
  `button5_def` varchar(50) DEFAULT 'PED'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_layoutnotes`
--

DROP TABLE IF EXISTS `idax_video_layoutnotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_layoutnotes` (
  `layoutid` int(10) unsigned NOT NULL,
  `designer_notes` varchar(21800) DEFAULT NULL,
  `counter_notes` varchar(21800) DEFAULT NULL,
  `qc_notes` varchar(21800) DEFAULT NULL,
  PRIMARY KEY (`layoutid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `idax_video_layouts`
--

DROP TABLE IF EXISTS `idax_video_layouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `idax_video_layouts` (
  `layoutid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `videoid` int(10) unsigned DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `videospeed` int(10) unsigned DEFAULT NULL,
  `lastvideoposition` float DEFAULT NULL,
  `designedby_user` int(10) unsigned DEFAULT NULL,
  `countedby_user` int(10) unsigned DEFAULT NULL,
  `qcedby_user` int(10) unsigned DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `lastupdatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`layoutid`)
) ENGINE=InnoDB AUTO_INCREMENT=2398 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-16 11:54:22
