<?php

 require_once("../../config.php");
 require_once("lib.php");
require_once('correctionForm.php');
require_once('omrlib.php');
require_once('recognitionprocess.php');
 
$id =			 optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  =		 	 optional_param('a',  0, PARAM_INT); // Blended ID
$jobid = optional_param('jobid',  0, PARAM_INT); // Blended ID
$resultid = optional_param('resultid',  0, PARAM_TEXT); // Blended ID


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
    $strtable				= get_string("table","blended");
    
?>
<script type="text/javascript" src="tjpzoom.js"></script>
<script type="text/javascript" src="tjpzoom_config_smart.js"></script>
<?php
// Print the page header ---------------------------------------------------------
     
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strscannedJobpage,'link'=>"../../mod/blended/scannedJob.php?a=$blended->id&jobid=$jobid", 'type'=>'misc'),
                                        array('name' => $strshowdetailspage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
    print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));
    print_box(format_text(get_string('undetectedActivitycodepagedesc', 'blended')), 'generalbox', 'intro');
    print_spacer(20);


    $job=get_record($table='blended_images', 
				$field1='id', $resultid, 
				$field2='jobid', $value2=$jobid);
				
    $mform = new activitycodeForm();
    if($job->activitycode!=NULL)
    	$mform->_form->setDefault('TEMPLATECODEFIELD', $job->activitycode.$job->pageindex);
	$mform->_form->addElement('hidden', 'id', $id);
	$mform->_form->addElement('hidden', 'a', $a);
	$mform->_form->addElement('hidden', 'jobid', $jobid);
	$mform->_form->addElement('hidden', 'resultid', $resultid);

	if (!$mform->is_cancelled() && $data=$mform->get_data())
	{
	//this branch is where you process validated data.
	//$data->blended=$blended->id;
	$acode=substr($data->TEMPLATECODEFIELD,0,strlen($data->TEMPLATECODEFIELD)-1);
	$page =substr($data->TEMPLATECODEFIELD,strlen($data->TEMPLATECODEFIELD)-1);
	
	if (check_quiz_course($acode,$course))
	{
		if($job== false)
			{
			error('Error al obtener el registro de la tabla blended_images');
			}
			else
			{
			$job->activitycode = $acode;
			$job->pageindex=$page;
			}
		
	    if(($scanjob=get_record($table='blended_scans','id',$jobid)) == false)
	    	{
	    		error('Error al obtener el omrpath de la tabla blended_scans, no se encontró el ID');
	    	}
	   
	 
	    $omrpath=blended_getOMRFieldsetDir($scanjob);
	    
	    // Si no hay registrado ningún cuestionario con ese activitycode, se crean registros nuevos.
		if (get_records($table='blended_results', $field='activitycode', $value=$acode) == false)
	    {
	    	register_template_fields($job,$omrpath);
	    	register_result_files($job,$omrpath);	
	    }
		// Mark all results of this new page as doubtful
		set_field_select('blended_results', 'invalid', 2, "activitycode='$acode' and page='$page' and jobid='$jobid'");
	    
		//update_blended_images($job);
		if(update_record('blended_images', $job) == false)
			error(" No se pudo actualizar la base de datos blended_images");
			
		//$continue ="$CFG->wwwroot/mod/blended/showdetails.php?a=$a&acode=$acode&jobid=$jobid";
		$continue = "$CFG->wwwroot/mod/blended/scannedJob.php?a=$a&jobid=$jobid";
		echo "<CENTER>Se han cargado los datos de la página $page del cuestionario con identificador $acode.</CENTER><BR>";
		print_continue($continue);
		return;
	}//if check_quiz_course
    else
    {
		$statusMsg=get_string('ErrorActivityCodeNotFound', 'blended',$acode);
    	//set_field('blended_images','activitycode',$acode,'jobid',$jobid,'imgout',$imgout);
 	 //  $continue ="$CFG->wwwroot/mod/blended/activitycode.php?a=$a&jobid=$jobid&resultid=$resultid";
		//echo "<CENTER>El proceso ha sido cancelado.</CENTER><BR>";
		//print_continue($continue);
    }
    
// continue to show again the form	
	
	}
	if ($mform->is_cancelled()){
	//you need this section if you have a cancel button on your form
	//here you tell php what to do if your user presses cancel
	//probably a redirect is called for!
	
	$continue ="$CFG->wwwroot/mod/blended/view.php?&a=$a";
	echo "<CENTER>El proceso ha sido cancelado.</CENTER><BR>";
	print_continue($continue);
	}
else
{	
	$message="Introduzca el ActivityCode del cuestionario y pulse enviar para cargar las preguntas";
	if (isset($statusMsg) && $statusMsg!='')
		print_box(format_text($statusMsg), 'generalbox', 'intro');
	
 	show_details_table($message);
	
 	echo '<table  border="1" align="center"><tr align="top"><td valign="top">'';
 	$mform->display();
 	echo "</td><td>";

	display_orphan_imageintable($jobid, $resultid);
	
	echo "</td></table>";
	
	
}	
?>