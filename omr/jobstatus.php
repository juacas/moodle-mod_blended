<?php

require_once("../../config.php");
 require_once("lib.php");

 
 
$id =			 optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  =		 	 optional_param('a',  0, PARAM_INT); // Blended ID
$jobid  =		 optional_param('jobid',  0, PARAM_INT); 
//$acode = 		 optional_param('acode',0,PARAM_INT);
$page = optional_param('page',  0, PARAM_TEXT); 

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
    require_capability('mod/blended:viewstatus', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strscanpage		  = get_string("scan","blended");
    $strcorrectionpage    = get_string('correction','blended');
    $strjobstatuspage    = get_string('jobstatus','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------

    if ($page == "scan.php")
    {
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strscanpage,'link'=>"../../mod/blended/scan.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strjobstatuspage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strjobstatuspage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strjobstatuspage), 
                  navmenu($course, $cm));

    }
    if ($page == "correction.php")
    {
     $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strjobstatuspage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strjobstatuspage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strjobstatuspage), 
                  navmenu($course, $cm));
    
    }
    
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
print_spacer(20);	
	if ($page == "scan.php")
    {    
		print_heading(format_string(get_string('scan', 'blended')));
    	
    }
	if ($page == "correction.php")
    {    
		print_heading(format_string(get_string('correction', 'blended')));
    }
    
print_box(format_text(get_string('jobstatusdesc', 'blended')), 'generalbox', 'intro');
print_spacer(20);

$message = get_status_message($jobid);
show_status_message($jobid,$message,$context,$blended,$page);

if ($page == "scan.php")
{
$volver ="$CFG->wwwroot/mod/blended/scan.php?a=$a";
}

if ($page == "correction.php")
{
$volver ="$CFG->wwwroot/mod/blended/correction.php?a=$a";
}

print_spacer(100);
print_continue($volver);

 	echo "<BR><BR><center>";
    helpbutton($page='jobstatus', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
    echo "</center>";

print_footer($course);
?>