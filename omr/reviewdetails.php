<?php


 require_once("../../config.php");
 require_once("lib.php");
 require_once("revisionlib.php");

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a',  0, PARAM_INT); // Blended ID
$acode  = optional_param('acode',  0, PARAM_INT); 
$jobid = optional_param('jobid',  0, PARAM_INT); 
$quizid = optional_param('quizid', 0, PARAM_INT);

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
    require_capability('mod/blended:reviewresults', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strrevisionpage    = get_string('revision','blended');
    $strreviewdetailspage    = get_string('reviewdetails','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strrevisionpage,'link'=>"../../mod/blended/revision.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strreviewdetailspage,'link'=>null, 'type'=>'misc')));
 
 /**
Include a image viewer
*/
?>
<link rel="stylesheet" href="thumbnailviewer.css" type="text/css" />

<script src="thumbnailviewer.js" type="text/javascript">

/***********************************************
* Image Thumbnail Viewer Script-  Dynamic Drive (www.dynamicdrive.com)
* This notice must stay intact for legal use.
* Visit http://www.dynamicdrive.com/ for full source code
***********************************************/

</script>
<script type="text/javascript" src="tjpzoom.js"></script>
<script type="text/javascript" src="tjpzoom_config_smart.js"></script>
<?php
/**
END image Viewer
*/
   print_header("$course->shortname: $blended->name: $strreviewdetailspage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strreviewdetailspage), 
                  navmenu($course, $cm));


// Print the main part of the page ----------------------------------   
    
	print_spacer(20);
    print_heading(format_string(get_string('revision', 'blended')));
    print_box(format_text(get_string('reviewdetailspagedesc', 'blended')), 'generalbox', 'intro');
    print_spacer(20);

    
//print ("Detalles del trabajo del alumno");
	review_details_table($jobid,$acode,$course,$a,$quizid);



print_footer($course);

?>
