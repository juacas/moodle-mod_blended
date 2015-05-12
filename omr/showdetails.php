<?php

require_once("../../config.php");
require_once("lib.php");
require_once('correctionForm.php');

require_once('ResultsError.php');
 
 
$id =			 optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  =		 	 optional_param('a',  0, PARAM_INT); // Blended ID
$acode = 		 required_param('acode', PARAM_INT); //
$jobid = 		 required_param('jobid', PARAM_INT); //



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
    require_capability('mod/blended:editresults', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('scannedJob','blended');
    $strshowdetailspage		  = get_string("showdetailspage","blended");
    $strprocesscancelled = get_string ('processcancelled','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strscannedJobpage,'link'=>"../../mod/blended/scannedJob.php?a=$blended->id&jobid=$jobid", 'type'=>'misc'),
                                        array('name' => $strshowdetailspage,'link'=>null, 'type'=>'misc')));
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
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
	$message = new stdClass();

	$jobname = get_jobname($jobid);	

	$message->acode = $acode;
	$message->jobname = $jobname;
	$jobpath = create_url($jobname,$course->id);
	$message->href = $jobpath;
    $message->hrefText = $jobname;
         

	print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));
    print_box(format_text(get_string('showdetailspagedesc', 'blended',$message)), 'generalbox', 'intro');
    print_spacer(20);
    

   	$values = new stdClass();	
	
   	try{
   	
   		$defaulteval = get_eval_value($acode,$jobid);
   		
	$values->eval = $defaulteval;
    $values->activitycode = $acode;
    $values->jobid = $jobid;
	
    $useridvalue=find_userid($acode,$jobid);
    print("Act $acode, job $jobid user ($useridvalue)");;
    $values->userid=$useridvalue;
    $values->a = $a;
    $values->id = $id;
    $values->warnings=get_doubtfull_marks($acode, $jobid);
  
	$mform = new correctionForm($values);
   
	if (!$mform->is_cancelled() && $data=$mform->get_data())
	{
		process_results_form($data);
	}
	
   	if (isset($data->errors_resolved))
	{
		$continue ="$CFG->wwwroot/mod/blended/scannedJob.php?a=$a&jobid=$jobid";
		print_continue($continue);
	}
	else
	if ($mform->is_cancelled()){
	//you need this section if you have a cancel button on your form
	//here you tell php what to do if your user presses cancel
	//probably a redirect is called for!
	
	$continue ="$CFG->wwwroot/mod/blended/scannedJob.php?a=$a&jobid=$jobid";
	echo $strprocesscancelled;
	print_continue($continue);
    }
    else
	{	
	$evaluar="Pasar a QUIZ";
	//echo "<BR>";
	$link="<a href=\"evaluate.php?&a=$a&acode=$acode&jobid=$jobid\">$evaluar</a>";
	echo "<center>$link</center><BR><BR>";	

	$currentpage="showdetails.php";
 	display_details($mform,$jobid,$acode,$course,$a,$currentpage);
 	
	}	

    }catch (ResultsError $e)
    {
   		 if ($e->getCode() == ResultsError::TABLE_BLENDED_RESULTS_IS_EMPTY)
		{
			debugging ("Fatal ResultsError: ".$e->getMessage());
		
			register_exception ($e,$jobid);
		}
		
		else
		{
    		debugging ("Ha ocurrido un error durante la obtención de datos del formulario.<BR>");
    		debugging ("FATAL ResultsError: ".$e->getMessage()."<BR>");
		
			register_exception ($e,$jobid);
		}
		
    }
	
    echo "<BR><BR><center>";
    helpbutton($page='showdetails', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
    echo "</center>";
    
print_footer($course);
?>
