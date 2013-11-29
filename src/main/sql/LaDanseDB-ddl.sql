CREATE TABLE `Account` (
  `id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `Event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `inviteTime` datetime DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `organiser` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EVENT_ORGANISER` (`organiser`),
  CONSTRAINT `FK_EVENT_ORGANISER` FOREIGN KEY (`organiser`) REFERENCES `Account` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `LoginEvent` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `loginTime` datetime DEFAULT NULL,
  `account` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_LOGINEVENT_ACCOUNT` (`account`),
  CONSTRAINT `FK_LOGINEVENT_ACCOUNT` FOREIGN KEY (`account`) REFERENCES `Account` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `SignUp` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `end` datetime DEFAULT NULL,
  `signUpType` varchar(255) DEFAULT NULL,
  `since` datetime DEFAULT NULL,
  `accountId` bigint(20) NOT NULL,
  `eventId` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_SIGNUP_ACCOUNT` (`accountId`),
  KEY `FK_SIGNUP_EVENT` (`eventId`),
  CONSTRAINT `FK_SIGNUP_EVENT` FOREIGN KEY (`eventId`) REFERENCES `Event` (`id`),
  CONSTRAINT `FK_SIGNUP_ACCOUNT` FOREIGN KEY (`accountId`) REFERENCES `Account` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

CREATE TABLE `ForRole` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `roleType` varchar(255) DEFAULT NULL,
  `signUpId` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_FORROLE_SIGNUPID` (`signUpId`),
  CONSTRAINT `FK_FORROLE_SIGNUPID` FOREIGN KEY (`signUpId`) REFERENCES `SignUp` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

CREATE TABLE `Setting` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `value` datetime DEFAULT NULL,
  `account` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_SETTING_ACCOUNT` (`account`),
  CONSTRAINT `FK_SETTING_ACCOUNT` FOREIGN KEY (`account`) REFERENCES `Account` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;