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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13091324 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=804 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=154 DEFAULT CHARSET=latin1;

CREATE TABLE `idax_tube_reportformats` (
  `name` varchar(100) NOT NULL,
  `fields` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=3426 DEFAULT CHARSET=latin1;
