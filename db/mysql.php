<?php // $Id: mysql.php,v 1.3 2006/08/28 16:41:20 mark-nielsen Exp $

function blended_upgrade($oldversion)
{
	 global $CFG;
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 
    if ($oldversion < 2010060805) {
     execute_sql(" ALTER TABLE `{$CFG->prefix}blended` ADD `numcols`  int(10) unsigned NOT NULL ");
     }

     if ($oldversion < 2010061702){
    	execute_sql("CREATE TABLE `mdl_blended_attempts` (
    	`attempt`             int(10) unsigned NOT NULL,
		`timestampt`        int(10) unsigned NOT NULL,
		`id`        int(10) unsigned NOT NULL auto_increment,
		`quiz`            int(10) unsigned NOT NULL,
		`userid`     int(10) unsigned NOT NULL,
		`layout`    text(30) NOT NULL,
		`status`     varchar(15) NOT NULL,
		PRIMARY KEY (`id`)
	) COMMENT='Define paperquiz attempt'");
    }
    if($oldversion < 2010061705)
       {
       	execute_sql ("CREATE TABLE `mdl_blended_jobs` (

		`id`        int(10) unsigned NOT NULL auto_increment,
		`blended`    int(10) unsigned NOT NULL,
		`quiz`            int(10) unsigned NOT NULL,
		`quiz_name`    varchar(50) NOT NULL,
		`userid`     int(10) unsigned NOT NULL,
		`attempt_id`    int(10) unsigned NOT NULL,
		`timestampt`    int(10) unsigned NOT NULL,
		`status`     varchar(15) NOT NULL,
		`identifyLabel`     varchar(15) NOT NULL,
		PRIMARY KEY (`id`)
		) COMMENT='Define paperquiz'");
     }
    if($oldversion < 2010110900)
       {
       	execute_sql ("CREATE TABLE `mdl_blended_scans` (

		`id`        	int(10) unsigned NOT NULL auto_increment,
		`blended`       int(10) unsigned NOT NULL,
		`scan_name`    	varchar(255) NOT NULL,
		`userid`     	varchar(30) NOT NULL,
		`timestamp`     int(10) unsigned NOT NULL,
		`status`     	varchar(15) NOT NULL,
		`course`        int(10) unsigned NOT NULL,
		`timestatus`     int(10) unsigned NOT NULL 
		PRIMARY KEY (`id`)
		) COMMENT='Define scan'");
       }
if($oldversion < 2010121600)
       {
       	execute_sql ("CREATE TABLE `mdl_blended_images` (

	`id`        	int(10) unsigned NOT NULL auto_increment,
	`jobid`    		int(10)  unsigned NOT NULL,
	`imgsrc`    	varchar(255) NOT NULL,
	`pageindex`        	int(10) unsigned NOT NULL,
	`imgout`    	varchar(255) NOT NULL,
	`results`    	varchar(255) NOT NULL,
	`userid`     	varchar(30) NOT NULL,
	`page`        	int(10) unsigned NOT NULL,
	`status`     	varchar(15) NOT NULL,
	`activitycode` 	int (10) unsigned NOT NULL,
		PRIMARY KEY (`id`)
		) COMMENT='Define images';");
       }
	if($oldversion < 2010121900)
       {
       	execute_sql ("CREATE TABLE `mdl_blended_results` (

		`id`        int(10) unsigned NOT NULL auto_increment,
		`jobid`    	int(10)  unsigned NOT NULL,
		`userid`    varchar(30) NOT NULL,
		`label`    	varchar(25) NOT NULL,
		`value`     	varchar(10) NOT NULL,
		`questiontype`   varchar(30) NOT NULL,
		`invalid`         int(1) NOT NULL,
		`page`        	int(10) unsigned NOT NULL,
		`activitycode` 	int (10) unsigned NOT NULL,
		PRIMARY KEY (`id`)
		) COMMENT='Define results';");
       }
       
  		
       return true;
}


?>