# $Id: mysql.sql,v 1.2 2006/08/28 16:41:20 Pablo Gal�n Sabugo Exp $
# This file contains a complete database schema for all the
# tables used by this module, written in SQL
# It may also contain INSERT statements for particular data
# that may be used, especially new entries in the table log_display

CREATE TABLE `prefix_blended` (
`id`             int(10) unsigned NOT NULL auto_increment,
`course`         int(10) unsigned NOT NULL default '0',
`name`           varchar(255) NOT NULL default '',
`description`    text NOT NULL default '',
`idmethod`       int(10) unsigned NOT NULL default '0',
`idtype`         int(10) unsigned NOT NULL default '1',
`codebartype`    varchar(5) NOT NULL default 'QR2D',
`lengthuserinfo` int(10) unsigned NOT NULL default '0',
`teammethod`     int(10) unsigned NOT NULL default '0',
`numteams`       int(10) unsigned NOT NULL default '0',
`nummembers`     int(10) unsigned NOT NULL default '0',
`assignment`     int(10) unsigned NOT NULL default '0',
`randomkey`      int(32) unsigned NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define module blended';

CREATE TABLE `prefix_blended_team` (
`id`              int(10) unsigned NOT NULL auto_increment,
`id_assignment`   int(10) unsigned NOT NULL,
`name_team`       varchar(255)     NOT NULL default '',
`userid_leader`   int(10) unsigned NOT NULL default '0',
PRIMARY KEY (`id`)
) COMMENT='Define table equipment';


CREATE TABLE `prefix_blended_grade` (
`id`              int(10) unsigned NOT NULL auto_increment,
`id_assignment`   int(10) unsigned NOT NULL,
`id_assignment_0` int(10) unsigned NOT NULL,
`id_team`         int(10) unsigned NOT NULL,
`grade`           int(10) unsigned NOT NULL,
`rewrite`         int(1)  unsigned NOT NULL default '1',
PRIMARY KEY (`id`)
) COMMENT='Define table grade';

CREATE TABLE `prefix_blended_attempts` (
`attempt`             int(10) unsigned NOT NULL,
`id`        int(10) unsigned NOT NULL auto_increment,
`quiz`            int(10) unsigned NOT NULL,
`userid`     int(10) unsigned NOT NULL,
`layout`    text(30) NOT NULL,
`status`     varchar(15) NOT NULL,
`timestampt`        int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define paperquiz attempt';

CREATE TABLE `prefix_blended_jobs` (

`id`        int(10) unsigned NOT NULL auto_increment,
`blended`        int(10) unsigned NOT NULL,
`quiz`            int(10) unsigned NOT NULL,
`quiz_name`    varchar(40) NOT NULL,
`userid`     int(10) unsigned NOT NULL,
`attempt_id`    int(10) unsigned NOT NULL,
`timestampt`        int(10) unsigned NOT NULL,
`status`     varchar(15) NOT NULL,
`identifyLabel`     varchar(15) NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define paperquiz';

CREATE TABLE `prefix_blended_scans` (

`id`        	int(10) unsigned NOT NULL auto_increment,
`blended`       int(10) unsigned NOT NULL,
`scan_name`   	varchar(255) NOT NULL,
`userid`     	varchar(30) NOT NULL,
`timestamp`     int(10) unsigned NOT NULL,
`status`     	varchar(15) NOT NULL,
`course`        int(10) unsigned NOT NULL,
`timestatus`     int(10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define scan';

CREATE TABLE `prefix_blended_images` (

`id`        	int(10) unsigned NOT NULL auto_increment,
`jobid`    	int(10)  unsigned NOT NULL,
`imgsrc`    	varchar(255) NOT NULL,
`pageindex`     int(10) unsigned NOT NULL,
`imgout`    	varchar(255) NOT NULL,
`results`    	varchar(255) NOT NULL,
`userid`     	varchar(30) NOT NULL,
`page`        	int(10) unsigned NOT NULL,
`status`     	varchar(15) NOT NULL,
`activitycode` 	int (10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define images';

CREATE TABLE `prefix_blended_results` (

`id`        	int(10) unsigned NOT NULL auto_increment,
`jobid`    	int(10)  unsigned NOT NULL,
`label`    	varchar(25) NOT NULL,
`userid`     	varchar(30) NOT NULL,
`value`        	varchar(10) NOT NULL,
`questiontype`     varchar(30) NOT NULL,
`invalid`         int(1) NOT NULL,
`page`        	int(10) unsigned NOT NULL,
`activitycode` 	int (10) unsigned NOT NULL,
PRIMARY KEY (`id`)
) COMMENT='Define results';

