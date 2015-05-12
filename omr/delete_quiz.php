<?php
/*********************************************************************************
 * Module developed at the University of Valladolid
 * Designed and directed by Juan Pablo de Castro with the effort of many other
 * students of telecommunication engineering of Valladolid
 * Copyright 2009-2011 EdUVaLab http://www.eduvalab.uva.es
 * this module is provides as-is without any guarantee. Use it as your own risk.

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 * @author Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/


require_once("../../config.php");
require_once("lib.php");
require_once("correctionForm.php");
require_once ("recognitionprocess.php");

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a',  0, PARAM_INT); // Blended ID
$scanjobid = optional_param('scanjobid',  0, PARAM_INT);
$acode = optional_param('acode',  0, PARAM_INT); 
$page = optional_param('page',  0, PARAM_TEXT);
$imgout  = optional_param('imgout',  0, PARAM_TEXT); 

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
$jobname= get_jobname($scanjobid);

$text -> jobname = $jobname;
$text -> acode = $acode;
$text -> imgout = $imgout;

 if ( ($page == 'scan.php') or ($page == 'correction.php') )
 {   
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strdeletescanjob,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strdeletescanjob", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strdeletescanjob), 
                  navmenu($course, $cm));
 }
 if ($page == 'scannedJob.php')	
 {
 	 $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strscannedJobpage,'link'=>"../../mod/blended/scannedJob.php?a=$blended->id&jobname=$jobname", 'type'=>'misc'),
                                        array('name' => $strdeletequizpage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    
 }	
 
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   

	print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));

if ( ($page == 'scan.php') or ($page == 'correction.php') )
 { 
    print_box(format_text(get_string('deletescanjobdesc', 'blended',$text)), 'generalbox', 'intro');
 }
 else
 {
 	if ($acode !== 0)
 	print_box(format_text(get_string('deletequizdesc', 'blended',$text)), 'generalbox', 'intro');
 	else
 	print_box(format_text(get_string('deleteimgdesc', 'blended',$text)), 'generalbox', 'intro');
 }
    print_spacer(20);

    $mform = new deleteForm();
	$mform->_form->addElement('hidden', 'id', $id);
	$mform->_form->addElement('hidden', 'a', $a);
	$mform->_form->addElement('hidden', 'acode', $acode);
	$mform->_form->addElement('hidden', 'scanjobid', $scanjobid);
	//$mform->_form->addElement('hidden', 'action', $action);
	$mform->_form->addElement('hidden', 'page', $page);
	
	if (!$mform->is_cancelled() && $data=$mform->get_data())
	{
	//this branch is where you process validated data.
	//print_object($data);
	
	$page = $data ->page;
	$acode = $data -> acode;
	
	$continue = "$CFG->wwwroot/mod/blended/$page?&a=$a";
	$continuescanned = "$CFG->wwwroot/mod/blended/$page?&a=$a&jobid=$scanjobid";
	
	
	if ( ($page == 'deleteScanJob')) 
	{
		$owner = is_owner(0,$data->jobid);
		 
		if ((has_capability('mod/blended:deletescanjob', $context) and $owner) or has_capability('mod/blended:deleteall', $context))
		{
		$scan=getOMRScanJob($scanjobid);
		delete_scan_result($scan,$acode);
		echo "<CENTER>El trabajo ha sido eliminado.</CENTER><BR>";
		}
		else
		{
			echo "<CENTER>No cuenta con permisos suficientes para realizar la acción.</CENTER><BR>";
		}
		
		print_continue($continue);
	}
	elseif ($page == 'scannedJob.php')
	{
		if ($acode !== 0)
		{
			$owner = is_owner($acode,0);
			if ((has_capability('mod/blended:deletequiz', $context) and $owner) or has_capability('mod/blended:deleteall', $context))
		{
			delete_quiz($data);
			echo "<CENTER>El cuestionario ha sido eliminado.</CENTER><BR>";
		}
		else
		{
			echo "<CENTER>No cuenta con permisos suficientes para realizar la acción.</CENTER><BR>";
		
		}
		
		print_continue($continuescanned);	
		}
	
		if ($acode == 0)
		{
			$owner = is_owner(0,$jobid);
		
			if ((has_capability('mod/blended:deletescanjob', $context) and $owner) or has_capability('mod/blended:deleteall', $context))
		{
			delete_image($data); 	
			echo "<CENTER>La imagen ha sido eliminada.</CENTER><BR>";
		}
		else
		{
			echo "<CENTER>No cuenta con permisos suficientes para realizar la acción.</CENTER><BR>";
		
		}
			print_continue($continuescanned);	
		}
	}
	else
	{
		error("Unknown referrral page: $page");
	}
	
	//$redirect = 'scannedJob.php?id=$course->id&a=$a&jobname=$jobname';
	//		redirect($redirect,'');
   	
	}
	

	elseif ($mform->is_cancelled()){
	//you need this section if you have a cancel button on your form
	//here you tell php what to do if your user presses cancel
	//probably a redirect is called for!
	
	$continue ="$CFG->wwwroot/mod/blended/$page?id=$course->id&a=$a&jobname=$jobname";
	echo "<CENTER>El proceso ha sido cancelado.</CENTER><BR>";
	print_continue($continue);
    }
 
    else
    {
    	echo"<CENTER>";
    	$mform -> display();
    	echo"</CENTER>";
    }
    
print_footer($course);


?>