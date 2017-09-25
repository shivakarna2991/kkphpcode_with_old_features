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

CREATE TABLE `idax_video_files` (
  `videoid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `testdata` int(10) unsigned DEFAULT '0',
  `jobsiteid` int(10) unsigned DEFAULT NULL,
  `name` varchar(256) DEFAULT NULL,
  `cameralocation` varchar(50) DEFAULT NULL,
  `filesize` int(10) unsigned DEFAULT NULL,
  `addedtime` datetime DEFAULT NULL,
  `capturestarttime` datetime DEFAULT NULL,
  `captureendtime` datetime DEFAULT NULL,
  `bucketfilename` varchar(128) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `phase` varchar(100) DEFAULT NULL,
  `lastupdatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`videoid`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=latin1;

CREATE TABLE `idax_video_ingestionphases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `videoid` int(10) unsigned DEFAULT NULL,
  `phase` varchar(100) DEFAULT NULL,
  `starttime` timestamp NULL DEFAULT NULL,
  `endtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1300 DEFAULT CHARSET=latin1;

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

CREATE TABLE `idax_video_layoutnotes` (
  `layoutid` int(10) unsigned NOT NULL,
  `designer_notes` varchar(21800) DEFAULT NULL,
  `counter_notes` varchar(21800) DEFAULT NULL,
  `qc_notes` varchar(21800) DEFAULT NULL,
  PRIMARY KEY (`layoutid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=723 DEFAULT CHARSET=latin1;
