<?php
/*********************************************************************************
 * Module developed at the University of Valladolid
 * Designed and directed by Juan Pablo de Castro with the effort of many other
 * students of telecommunication engineering of Valladolid
 * Copyright 2009-2011 EdUVaLab http://www.eduvalab.uva.es
 * this module is provides as-is without any guarantee. Use it as your own risk.

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 * @author J�ssica Olano L�pez,Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/


require_once($CFG->dirroot."/config.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once ($CFG->dirroot . '/group/lib.php');
require_once ("$CFG->libdir/formslib.php");
require_once ($CFG->libdir . '/filelib.php');
require_once ($CFG->dirroot .'/grade/lib.php');
require_once ($CFG->dirroot .'/grade/querylib.php');



/////////////////////////////////////////////////////////////////////////////////
/////                          Constants                                    /////
/////////////////////////////////////////////////////////////////////////////////




define ('ROWS_MAX_ENTRIES', 30);
define ('COLUMNS_MAX_ENTRIES', 10);
define ('TEAMS_MAX_ENTRIES', 40);
define ('MEMBERS_MAX_ENTRIES', 10);
define ('SECRET_KEY', 2147483647);




/////////////////////////////////////////////////////////////////////////////////
/////                          Low-level functions                          /////
/////////////////////////////////////////////////////////////////////////////////

/**
 * Indicates API features that the forum supports.
 *
 * @uses FEATURE_GROUPS
 * @uses FEATURE_GROUPINGS
 * @uses FEATURE_GROUPMEMBERSONLY
 * @uses FEATURE_MOD_INTRO
 * @uses FEATURE_COMPLETION_TRACKS_VIEWS
 * @uses FEATURE_COMPLETION_HAS_RULES
 * @uses FEATURE_GRADE_HAS_GRADE
 * @uses FEATURE_GRADE_OUTCOMES
 * @param string $feature
 * @return mixed True if yes (some features may use other values)
 */
function blended_supports($feature) {
	switch($feature) {
		case FEATURE_GROUPS:                  return false;
		case FEATURE_GROUPINGS:               return false;
		case FEATURE_GROUPMEMBERSONLY:        return false;
		case FEATURE_MOD_INTRO:               return true;
		case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
		case FEATURE_COMPLETION_HAS_RULES:    return false;
		case FEATURE_GRADE_HAS_GRADE:         return false;
		case FEATURE_GRADE_OUTCOMES:          return false;
		case FEATURE_RATE:                    return false;
		case FEATURE_BACKUP_MOODLE2:          return false;
		case FEATURE_SHOW_DESCRIPTION:        return true;
		case FEATURE_PLAGIARISM:              return false;

		default: return false;
	}
}



/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod_form.php
 * @return int The id of the newly inserted blended record
 **/
function blended_add_instance($blended) {
	global $DB;
	$blended->timecreated  = time();
	$blended->timemodified = $blended->timecreated;

	//return insert_record("blended", $blended);
	
	// Try to store it in the database.
	$blended->id = $DB->insert_record('blended', $blended);
        blended_restrict_items($blended,$blended->selecteditems);
	return $blended->id;
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod_form.php
 * @return boolean Success/Fail
 **/
function blended_update_instance($blended) {
	global $DB;
	$blended->timemodified = time();
	$blended->id = $blended->instance;
        if (!isset($blended->selecteditems)){
            blended_restrict_items($blended,array());
        }else{
        blended_restrict_items($blended,$blended->selecteditems);
        }
	return $DB->update_record("blended", $blended);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 
 * @todo TO-DO Borrar la información en todas las tablas relacionadas.
 * 
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function blended_delete_instance($id) {
	global $DB;
	
	if (!$blended = $DB->get_record('blended', array('id'=>$id))) {
		return false;
	}

	$result = true;

	# Delete any dependent records here #

	if (! $DB->delete_records('blended', array('id'=>$blended->id))) {
		$result = false;
	}
        blended_restrict_items($blended,array());
	return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function blended_user_outline($course, $user, $mod, $blended) {
	$return = null;
	return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function blended_user_complete($course, $user, $mod, $blended) {
	return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in blended activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @global $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function blended_print_recent_activity($course, $isteacher, $timestart) {
	global $CFG;

	return false;  //  True if anything was printed, otherwise false
}
   
/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @global $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/

function blended_cron() 
{
    global $CFG;
    global $COURSE;
    global $USER;
    global $DB;
    $course->id = $COURSE->id;

    mtrace("\n=======================");
    mtrace("Blended module.");
    mtrace("=======================");

	$query='SELECT timestamp, quiz, userid, count(*) FROM '.
		"{$CFG->prefix}blended_jobs WHERE status = '".JOB_STATE_WAITING."' group by quiz, timestamp, userid";
	mtrace("Querying with: $query");
	if (!$jobs = $DB->get_records_sql ($query))
	{
		mtrace ("There are no waiting PDF jobs.");
	}
	else
	{
		mtrace(" Found:".count($jobs)." questionnaire sets.");
	
	/**
	 * $jobs contain jobs generated simultaneouly from the same quiz, in the same timestamp and from the same userid
	 */
		foreach ($jobs as $job)
		{
	    	generateJobs($job);
	    }
	}
	//blended_check_scans_status();
	/**
	 * Consider it hung if delayed more than 30 minutes.
	 */
	$expiration_time=time()-30*60;
	// TODO implement a locking mechanism
	//$scans = get_records($table="blended_scans",$field="status",$value="Espera")
	$condition ="status = '".JOB_STATE_WAITING."' OR (status='".JOB_STATE_BUSY."' AND timestatus < $expiration_time)";
	mtrace($condition);
	if (!$scans = $DB->get_records_select('blended_scans',$condition))
	{
		mtrace ("There are no ready scan jobs");
	}
	else
	{
		mtrace(" Found:".count($scans)." scanned sets.");
		foreach ($scans as $scan)
			{
	    	omrprocess($scan);
	    	}
	}


	if ($scans = $DB->get_records_select('blended_scans',"status='".JOB_STATE_BUSY."'"))
	{
		mtrace ("There are ".count($scans)." scanned sets waiting for retrying in less than 5 minutes.");
	}
    mtrace("=======================");
	mtrace("=======================");
    return true;


}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $blendedid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function blended_grades($blendedid) {
	return NULL;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of blended. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $blendedid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function blended_get_participants($blendedid) {
	return false;
}

/**
 * This function returns if a scale is being used by one blended
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $blendedid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function blended_scale_used ($blendedid,$scaleid) {
	$return = false;
		 
		return $return;
}

function blended_extend_settings_navigation(settings_navigation $settings, navigation_node $navref) {
     global $PAGE, $DB;

     
    $cm = $PAGE->cm;
    
    if (!$cm) {
        return;
    }
    if ($PAGE->pagetype=='mod-blended-teams-grades' 
      || $PAGE->pagetype=='mod-blended-teams-introteams')
     if (has_capability('mod/blended:introteams', $cm->context) ) {
        $link = new moodle_url('/mod/blended/teams/teams_graph.php', array('id' => $cm->id));
        $linkname = get_string('view_teams_graph', 'blended');
        $node = $navref->add($linkname, $link, navigation_node::TYPE_CUSTOM);
    }
}
?>