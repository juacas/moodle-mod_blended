<?php

    require_once("../../config.php");
    require_once("lib.php");
    require_once("alertslib.php");
    
// Get the params ----------------------------------------------------------------
global $DB;
$id = required_param('id', PARAM_INT); // Course Module ID, or
$studentroleid = optional_param('srid',  0, PARAM_INT); 
$message = optional_param('message',  '', PARAM_TEXT);


	if (! $cm = get_coursemodule_from_id('blended', $id)){
		error("Course Module ID was incorrect");
	}

	if (! $course = get_course($cm->course)) {
		error("Course is misconfigured");
	}

	if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
		error("Course module is incorrect");
	}
	if (! $user = $DB->get_record('user',array('id'=> $USER->id))) {
		error("No such user in this course");
	}

// Capabilities -----------------------------------------------------

$context = context_module::instance($cm->id);
require_capability('mod/blended:viewalerts', $context);

$context_course = context_course::instance($cm->course);

list($students, $non_students, $activeuserids, $user_records)= blended_get_users_by_type($context_course);
if (count($students)==0)
{
    print_error('no_students_in_course');
}
require_login($cm->course, false,$cm);



// Get the strings ---------------------------------------------------------------

$strviewalertspage    = get_string('viewalerts','blended');
$strtable				= get_string("table","blended");

// Print the page header ---------------------------------------------------------
 // show headings and menus of page
$url =  new moodle_url('/mod/blended/alertmessages.php',array('id'=>$id,'srid'=>$studentroleid,'message'=>$message));
$PAGE->set_url($url);
$PAGE->set_title(format_string($blended->name));
// $PAGE->set_context($context_module);
$PAGE->set_heading($course->fullname);
//$PAGE->set_pagelayout('standard');
$PAGE->navbar->add($strviewalertspage);
echo $OUTPUT->header ();

// Print the main part of the page ----------------------------------
//print_spacer(20);
echo $OUTPUT->spacer(array('height'=>20));
//print_heading(format_string(get_string('viewalerts', 'blended')));
echo $OUTPUT->heading(format_string($strviewalertspage));
//print_box(format_text(get_string($message, 'blended')), 'generalbox', 'intro');
echo $OUTPUT->box(format_text($message, FORMAT_MOODLE), 'generalbox', 'intro');
//print_spacer(20);
echo $OUTPUT->spacer(array('height'=>20));

$alertinfo = '';

display_alerts_table($blended,$studentroleid,$course,$context_course,$alertinfo);

//print_footer($course);
echo $OUTPUT->footer();

?>