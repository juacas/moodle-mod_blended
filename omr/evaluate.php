<?php  // $Id: attempt.php,v 1.131.2.20 2010/08/06 11:41:48 tjhunt Exp $
/**
 * This page prints a particular instance of quiz
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package quiz
 */

    require_once("../../config.php");
    require_once("recognitionprocess.php");
    require_once("evaluationlib.php");
    require_once("EvaluationError.php");

    // remember the current time as the time any responses were submitted
    // (so as to make sure students don't get penalized for slow processing on this page)
   // $timestamp = time();

    // Get submitted parameters.
    $id = optional_param('id', 0, PARAM_INT);               // Course Module ID
    $a = required_param('a', PARAM_INT);                 // or blended ID
	$acode = 		 optional_param('acode',  null, PARAM_INT); //
	$numActivities = optional_param('numActivities',PARAM_INT);
	$jobid = 		 optional_param('jobid',  0, PARAM_INT); //
	$newattempt  = 	 optional_param('newattempt',  0, PARAM_TEXT); 
	
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

    add_to_log($course->id, "blended", "evaluate", "scannedJob.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:evaluatequiz', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('scannedJob','blended');
    $strevaluatepage		  = get_string("evaluatepage","blended");
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strscannedJobpage,'link'=>"../../mod/blended/scannedJob.php?a=$blended->id&jobid=$jobid", 'type'=>'misc'),
                                        array('name' => $strevaluatepage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);

 /**
  * If numActivities is defined iterate and make the array 
  */ 
  $acodes=array();
  
  for($i=0;$i<$numActivities;$i++)
  {
  if (isset($_REQUEST["selectedActivity$i"]))
  	$acodes[]=	$_REQUEST["selectedActivity$i"];
  }
  if ($acode)
  	$acodes[]=$acode;

global $CFG;

   print_spacer(20);
   print_heading(format_string(get_string('correction', 'blended')));
// iterate over the activity codes
foreach ($acodes as $acode)
 {

	$link = new stdClass();
	$quizid=find_quizid($acode);
	
	$id_member = find_userid($acode,$jobid);
	$user_reg = blended_get_user($id_member,$blended);
		
	$link->hrefText="Resultados";
	$link->href="$CFG->wwwroot/mod/quiz/report.php?&q=$quizid";
	


	
	try
	{    
		evaluate_quiz($acode,$jobid,$newattempt,$blended);
	
	} catch (EvaluationError $e)
	{
		print ("EvaluationError: " . $e->getMessage());
		
		register_exception ($e, $jobid);
	}  catch (ResultsError $e)
	{
		print ("ResultsError: " . $e->getMessage());
		
		register_exception ($e, $jobid);
	}    
	/**
	 * Mark images as passed
	 */
 set_field('blended_images','status',IMAGE_STATUS_PASSED,'activitycode',$acode);
		// Print the main part of the page ----------------------------------   
	$userText= print_user_picture($user_reg,$course->id,null,null,true). fullname($user_reg); 
	print_box($userText.format_text(get_string('evaluatepagedesc', 'blended',$link)), 'generalbox', 'intro');
	print_spacer(20);
	
 }
    print_continue("scannedJob.php?a=$a&jobid=$jobid");
    // Finish the page
    if (empty($popup)) {
        print_footer($course);
  }

?>
