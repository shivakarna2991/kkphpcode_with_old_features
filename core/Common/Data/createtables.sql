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

CREATE TABLE `core_accounts` (
  `accountid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL DEFAULT '',
  `state` int(11) DEFAULT '0',
  `creationtime` datetime DEFAULT NULL,
  `registeredtime` datetime DEFAULT NULL,
  `lastlogintime` timestamp NULL DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `passwordhash` varchar(255) NOT NULL,
  `failedloginattempts` int(10) unsigned DEFAULT '0',
  `role` int(10) unsigned DEFAULT NULL,
  `rating` int(10) unsigned DEFAULT NULL,
  `developer` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`email`),
  UNIQUE KEY `accountid` (`accountid`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=latin1;

CREATE TABLE `core_issueattachments` (
  `attachmentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `issueid` int(10) unsigned NOT NULL,
  `lastupdated` datetime DEFAULT NULL,
  `filename` varchar(256) DEFAULT NULL,
  `bucketfilename` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`attachmentid`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=405 DEFAULT CHARSET=latin1;

CREATE TABLE `core_logins` (
  `loginid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accountid` int(10) unsigned NOT NULL,
  `loggedintime` datetime DEFAULT CURRENT_TIMESTAMP,
  `loggedouttime` datetime DEFAULT NULL,
  `token` varchar(500) DEFAULT NULL,
  `tokenexpirationtime` int(10) unsigned DEFAULT NULL,
  `loggedinlocation` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`accountid`),
  UNIQUE KEY `loginid` (`loginid`)
) ENGINE=InnoDB AUTO_INCREMENT=6995 DEFAULT CHARSET=latin1;
