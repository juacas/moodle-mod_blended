<?php
 
 require_once("../../config.php");
 require_once("lib.php");

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a',  0, PARAM_INT); // Blended ID
$jobname  = optional_param('jobname',  0, PARAM_TEXT); 
$jobid  = optional_param('jobid',  0, PARAM_INT); 



if ($id) {
	if (! $cm = get_coursemodule_from_id('blended', $id)){
		error("Course Module ID was incorrect");
	}

	if (! $course = get_record("course", "id", $cm->course)) {
		error("Course is misconfigured");
	}

	if (! $blended = get_record("blended", "id", $cm->instance)) {
		error("Course module is incorrect");
	}
	if (! $user = get_record("user", "id", $USER->id) ) {
		error("No such user in this course");
	}
} else {
	if (! $blended = get_record("blended", "id", $a)) {
		error("Course module is incorrect");
	}
	if (! $course = get_record("course", "id", $blended->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	if (! $user = get_record("user", "id", $USER->id) ) {
		error("No such user in this course");
	}
}
   // Log ---------------------------------------------------------------------------

    add_to_log($course->id, "blended", "scannedJob", "scannedJob.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    } 
   
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:viewscannedjobs', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('scannedJob','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strscannedJobpage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
    $jobname = get_jobname($jobid);

	$jobpath = create_url($jobname,$course->id);
	$job->href = $jobpath;
    $job->hrefText = $jobname;
     
	print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));
    print_box(format_text(get_string('scannedjobpagedesc', 'blended',$job)), 'generalbox', 'intro');
    print_spacer(20);

show_images_table($blended,$jobid,$course,$context);

	echo "<BR><BR><center>";
    helpbutton($page='scannedjob', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
    echo "</center>";
    
print_footer($course);

?>