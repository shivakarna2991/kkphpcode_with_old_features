CREATE TABLE `idax_customers` (
  `customerid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`customerid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE `idax_devices_log` (
  `deviceid` int(10) unsigned DEFAULT NULL,
  `jobsiteid` int(10) unsigned DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `datatype` varchar(20) DEFAULT NULL,
  `data` varchar(20000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=191 DEFAULT CHARSET=latin1;

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
  `timeblocks` varchar(100) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=408 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
