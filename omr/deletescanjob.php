<?php


require_once("../../config.php");
require_once("lib.php");
require_once("correctionForm.php");
require_once ("recognitionprocess.php");

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a',  0, PARAM_INT); // Blended ID
$scanjobid = optional_param('scanjobid',  0, PARAM_INT);
$acode = optional_param('acode',  0, PARAM_INT); 

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
    //require_capability('mod/blended:deletejob', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('scannedJob','blended');
    $strdeletequizpage    = get_string('deletequiz','blended');
    $strdeletescanjob	  = get_string('deletescanjob','blended');	
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------


    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strdeletescanjob,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strdeletescanjob", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strdeletescanjob), 
                  navmenu($course, $cm));

print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
$scan=blended_getOMRScanJob($scanjobid);

$text -> jobname = $scan->scan_name;
$text -> acode = $acode;

	print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));

    print_box(format_text(get_string('deletescanjobdesc', 'blended',$text)), 'generalbox', 'intro');

    print_spacer(20);

    $mform = new deleteForm();
	$mform->_form->addElement('hidden', 'id', $id);
	$mform->_form->addElement('hidden', 'a', $a);
	$mform->_form->addElement('hidden', 'acode', $acode);
	$mform->_form->addElement('hidden', 'scanjobid', $scan->id);
	$mform->_form->addElement('hidden', 'jobname', $scan->scan_name);
	
	if (!$mform->is_cancelled() && $data=$mform->get_data())
	{	
	$acode = $data -> acode;
	
	$continue = "$CFG->wwwroot/mod/blended/scan.php?&a=$a";
	$continuescanned = "$CFG->wwwroot/mod/blended/scan.php?&a=$a&jobid=$scanjobid";
	

	$owner = is_owner(0,$scan->id);
	 
	if ((has_capability('mod/blended:deletescanjob', $context) and $owner) or has_capability('mod/blended:deleteall', $context))
	{

	blended_delete_scan_job($scan);
	echo "<CENTER>El trabajo ha sido eliminado.</CENTER><BR>";
	}
	else
	{
		echo "<CENTER>No cuenta con permisos suficientes para realizar la acción.</CENTER><BR>";
	
	}
	
	print_continue($continue);

	
	//$redirect = 'scannedJob.php?id=$course->id&a=$a&jobname=$jobname';
	//		redirect($redirect,'');
   	
	}
	elseif ($mform->is_cancelled())
	{
	//you need this section if you have a cancel button on your form
	//here you tell php what to do if your user presses cancel
	//probably a redirect is called for!
	

	echo "<CENTER>El proceso ha sido cancelado.</CENTER><BR>";
	print_continue($continuescanned);
    }
 
    else
    {
    	echo"<CENTER>";
    	$mform -> display();
    	echo"</CENTER>";
    }
    
print_footer($course);


?>