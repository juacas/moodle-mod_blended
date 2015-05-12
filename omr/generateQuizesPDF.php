<?php
require_once("../../config.php");
//require_once("tcpdf/tcpdf.php");
require_once("omrlib.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');


// Get the params --------------------------------------------------

$a      			= required_param('a', PARAM_INT);
$id     			= optional_param('id', 0 ,PARAM_INT);
$timestamp_launch = optional_param ('launch',null, PARAM_INT);

$identifyLabel 		= optional_param('identifyLabel','none',PARAM_ALPHA);
$columns			= optional_param('columns','1',PARAM_INT);
$fontsize			= optional_param('fontsize','6',PARAM_INT);
$logourl			= optional_param('logourl',null,PARAM_URL);

$quizid 			= required_param('quizid', PARAM_INT);
$numberpaperquiz 	= required_param('quiznumber', PARAM_INT);
$cron 				= optional_param('cron',1, PARAM_INT);
$jobid 				= optional_param('jobid',null, PARAM_INT);

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
// Log --------------------------------------------------------------

	add_to_log($course->id, "blended", "generateQuizPDF", "generateQuizesPDF.php?id=$blended->id", "$blended->id");

;
// Capabilities -----------------------------------------------------
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printquizes', $context);
   

// Get the strings --------------------------------------------------

 	$strlabelspage          =  get_string("labelspage", "blended");  
	$strpaperquiz           =  get_string("paperquiz", "blended");
 	
// Print the page header --------------------------------------------    

   
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                         array('name' => $strpaperquiz,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strpaperquiz", "$course->shortname",
                 $navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strpaperquiz), 
                  navmenu($course, $cm));
	/**
	 * Launch pending job?? TODO aparently this do nothing
	 */
   	if ($timestamp_launch && $timestamp_launch!=0 )
    {
    	error("operation unsuported. Please contact Admin.");
    	
    	$timestamp = time();
    	//TODO cambiar por question_create_uniqueid_attempt 
    	if(!$unique_attempts = get_records_sql('SELECT * FROM ' . "{$CFG->prefix}blended_attempts WHERE timestamp= '$timestamp_launch'")) 
		{
			error("Encountered a problem trying to get attempt.");
		}
		$i=0;
		foreach ($unique_attempts as $attempt)
		{
			$attempts[$i] = $attempt;
			$attempts[$i]->status =  JOB_STATE_WAITING;
			$attempts[$i]->timestamp =  $timestamp;
			if(!$unique_jobs = get_records_sql('SELECT * FROM ' . "{$CFG->prefix}blended_jobs WHERE attempt_id= '$attempt->id'")) 
			{
				error("Encountered a problem trying to get job.");
			}
			
			
			foreach ($unique_jobs as $job)
			{
				$jobs[$i] = $job;
				$jobs[$i]->status = JOB_STATE_WAITING;
				$jobs[$i]->timestamp = $timestamp;
			}

			$q = $job->quiz;
			$opts=array_explode('=', ',', $jobs[$i]->identifylabel);
			$identifyLabel = $opts['identifyLabel'];
			$i++;
		}
		
  		$quiz = get_record("quiz", "id", $q);
		$numberpaperquiz = count($attempts);
    }            
    else
    /**
     * New pdf job
     */
    {
		$q = $quizid;
		$quiz = get_record("quiz", "id", $q);
		if (!$quiz)
		{
			error("Refereced quiz is not found: id=".$q);
		}
    	//TODO: cambiar por question_create_uniqueid_attempt 
		// Get number for the next or unfinished attempt

		
		
		/*if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
  			"{$CFG->prefix}blended_attempts WHERE quiz = '{$quiz->id}' AND " .
  			"userid = '{$USER->id}'")) 
    	{
        	$attemptnumber = 1;
    	}
    	*/
		$timestamp = time();
	
		
		for ($i=0; $i< $numberpaperquiz; $i++)
		{
			$attemptnumber = question_new_attempt_uniqueid('blended');
			$attempts[$i] = blended_generate_attempt ($attemptnumber, $quiz, $USER, $timestamp);
			$jobs[$i]->quiz = $quiz->id;
			$jobs[$i]->quiz_name = $quiz->name;
			$jobs[$i]->blended = $blended->id;
			$jobs[$i]->userid = $USER->id;
			$jobs[$i]->status = JOB_STATE_WAITING;
			$jobs[$i]->timestamp = $timestamp;	
			$jobs[$i]->attempt_id = $attempts[$i]->id;
			// encode options in this column
			$options=array();
			$option['identifyLabel']=$identifyLabel;
			$option['columns']=$columns;
			$option['logourl']=$logourl['value'];
			$option['fontsize']=$fontsize;
			$optStr=array_implode('=', ',', $option);
			//print("<pre>".$optStr."</pre>");print_object($logourl);die;
			$jobs[$i]->options = $optStr;
			
			if (!$jobs[$i]->id = insert_record('blended_jobs', $jobs[$i])) 
			{
				error('Could not create new quiz');
			}	
		}// for numberpaperquiz	
    }

	if ($cron == 1)
	{
		mtrace("<p>Su cuestionario sera generado mas tarde</a></p>");
		$continue ="$CFG->wwwroot/mod/blended/edit_paperquiz.php?a=$a";
		print_continue($continue);
		// Finish the page
    	print_footer($course);
	}
	else
	{
		// Generate every job matching this data. (This makes a grouping of attempts)
		$job->quiz=$quiz->id;
		$job->userid=$USER->id;
		$job->blended = $blended->id;
		$job->timestamp=$timestamp;
		
		list ($numattempts,$pdfFile)=generateJobs($job);
		
		
		if ($numattempts>0)
		{
			$tex=new object();
			$tex->href="$CFG->wwwroot/files/index.php?id=$course->id&wdir=%2F$pdfFile->inCourseFolderRelativePath";
			$tex->hrefText=$pdfFile->inCourseFolderRelativePath;
			$tex->directLinkhref="$CFG->wwwroot/file.php/$pdfFile->RelativePath?forcedownload=1";
			$tex->directLinkText=$pdfFile->Name;

		echo get_string("PDFgeneratedMessage","blended",$tex);
		
		}
		else
		{
			echo("<p>No PDF file generated.</p>");
		}
		$continue ="$CFG->wwwroot/mod/blended/edit_paperquiz.php?a=$a";
		print_continue($continue);
		// Finish the page
    	
		echo "<center>";
    	helpbutton($page='generateQuizesPDF', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
   		echo "</center>";
		print_footer($course);
	}

?>