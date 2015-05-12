<?php
 
 require_once("../../config.php");
 require_once("lib.php");

$a  = required_param('a', PARAM_INT); // Blended ID
$jobid  = required_param('jobid', PARAM_INT); 


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
	if (! $scan = get_record("blended_scans", "blended", $a,'id',$jobid) ) {
		error("No such scanjob for launching!");
	}

   // Log ---------------------------------------------------------------------------

    add_to_log($course->id, "blended", "launchScanJob", "launchScanJob.php?a=$blended->id&jobid=$jobid", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    require_login($cm->course, false,$cm);
    
       
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:createscannedjob', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('launchScanJob','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strscannedJobpage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
	set_field('blended_scans', 'status', JOB_STATE_WAITING, 'id', $jobid);
	
	print_spacer(20);
    print_heading(format_string(get_string('launchJob', 'blended')));
    print_box(format_text(get_string('inserted', 'blended')), 'generalbox', 'intro');
    print_spacer(20);
    print_continue("correction.php?a=$a");
print_footer($course);

?>