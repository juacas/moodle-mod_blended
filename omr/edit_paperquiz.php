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
require_once("../../lib/weblib.php");
require_once("PDFQuizzesForm.php");

// Get the params --------------------------------------------------
    global $DB, $PAGE, $OUTPUT, $USER;
    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a', 0, PARAM_INT);  // blended ID
	$timestamp_delete  = optional_param('delete', 0, PARAM_INT);
   
    
    if ($timestamp_delete && $timestamp_delete!=0 )
    {
    	//delete_records_select ('blended_attempts', "timestamp = '$timestamp_delete'");
    	delete_records_select ('blended_jobs', "timestamp = '$timestamp_delete'");
    }
    
    
	if ($id) {
        if (! $cm = get_coursemodule_from_id('blended', $id)){
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_course($cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            error("Course module is incorrect");
        }
        if (! $user = $DB->get_record('user',array('id'=> $USER->id))) {
            error("No such user in this course");
        }
    } else {
        if (! $blended = $DB->get_record('blended', array( 'id'=> $a))) {
            error("Course module is incorrect");
        }
        if (! $course = $DB->get_record('course', array('id' => $blended->course))) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        if (! $user = $DB->get_record('user', array ( 'id' => $USER->id))) {
            error("No such user in this course");
        }
    }

// Log --------------------------------------------------------------

    add_to_log($course->id, "blended", "edit_paperquiz", "edit_paperquiz.php?id=$cm->id", "$blended->id");
    
// Capabilities -----------------------------------------------------
    
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printquizes', $context);
    
    // show headings and menus of page
    $url =  new moodle_url('/mod/blended/edit_paperquiz.php',array('id'=>$id,'a'=>$a,'delete'=>$timestamp_delete));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    // $PAGE->set_context($context_module);
    $PAGE->set_heading($course->fullname);
    //$PAGE->set_pagelayout('standard');
    
    
// Get the strings --------------------------------------------------

    $strpaperquiz           = get_string("paperquiz", "blended");     
    $strassignmentpage      = get_string("assignmentpage", "blended");
    $strpaperquizdescr	    = get_string("paperquizdescr","blended");
    $strpaperquizformat	    = get_string("paperquizformat","blended");
	$strselectquiz	    	= get_string("selectquiz","blended");
    $strnumquiz	    		= get_string("numquiz","blended");
    $strlater	    		= get_string("later","blended");
    $strlabelformat			= get_string("labelformat","blended");
    $stridentify			= get_string("identify","blended");
    $strlabelformat			= get_string("labelformat","blended");
    $stridentify	    	= get_string("identify","blended");
	$strnotidentify	    	= get_string("notidentify","blended");
	$strreadable	    	= get_string("readable","blended");
	$strtable				= get_string("table","blended");
    
// Print the page header --------------------------------------------    
	
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                         array('name' => $strpaperquiz,'link'=>null, 'type'=>'misc')));
    echo $OUTPUT->header();
    
//     print_header("$course->shortname: $blended->name: $strpaperquiz", "$course->shortname",
//                  $navigation, 
//                   "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strpaperquiz), 
//                   navmenu($course, $cm));


// Print the main part of the page ---------------------------------- 
  	
                  
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->box(format_text($strtable), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
    
//

 
      
// Print a table with requested jobs


if (!$jobs_view = $DB->get_records_sql ('SELECT id,timestamp,quiz_name,userid,blended,quiz, count(*) as total FROM ' ."{$CFG->prefix}blended_jobs group by quiz,timestamp ORDER BY timestamp"))
{
	
    echo $OUTPUT->box("There are no jobs"); 
}
else
{
	$i = 0;
	$jobs=array();
	foreach ($jobs_view as $job)
	{
		if (($job->userid == $USER->id OR has_capability('mod/blended:managealljobs', $context)) 
			and ($job->blended == $blended->id))
		{
		
		$status = array();
		$status [JOB_STATE_IN_ERROR] = 0;
		$status [JOB_STATE_FINISHED] = 0;
		$status [JOB_STATE_WAITING] = 0;
		
		if(!$attempts_id = get_records_select('blended_jobs',"timestamp = '$job->timestamp'")) 
	    {
	    	error("Encountered a problem trying to get attempt");
	    }
	    $attempts_with_problems=array();
		foreach ($attempts_id as $attempt)
	    {
	  	    		
			if(!$attemptdata = get_record_select('blended_attempts', "id = '{$attempt->attempt_id}'")) 
			{
				mtrace ("Encountered a problem trying to get attempt: $attempt->attempt_id");
				$attempts_with_problems[$attempt->attempt_id]=$attempt->attempt_id;
			}
			else
			{
			$index = $attemptdata->status;
			$status ["$index"]++;
			}	
			
	    }
		if ($status[JOB_STATE_IN_ERROR] != 0)
		{
			if ($status[JOB_STATE_IN_ERROR] == $job->total)
			{
				$estado = "Failed";
			}
			else
			{
				$total = $job->total - $status[JOB_STATE_IN_ERROR];
				$statusMsg = "OK Partially".$total."/".$job->total;
			}
		}    	
		else
		{
			if (count($attempts_with_problems)>0)
			{
				$statusMsg = $job->total.' '.get_string('modulenameplural','quiz').':'.get_string('ErrorActivityCodeNotFound','blended', join(',',$attempts_with_problems));
			}
			else
			{
				$statusMsg = $job->total.' '.get_string('modulenameplural','quiz').':'.$attemptdata->status;
			}
			$estado = 'terminado';
		}
		
		if (has_capability('mod/blended:deletejob', $context)) 
		{
			$imgdelete="<img src=\"delete.gif\"/>";	
			$link =  "<a href=\"edit_paperquiz.php?delete=$job->timestamp&a=$blended->id\">$imgdelete</a>";
			$delete = $link;
		}
		
		//print $estado;
		if ($estado !== 'terminado')
		{
			if (has_capability('mod/blended:launchjob', $context)) 
			{ 	
			$link_assignment_page =  "<a href=\"generateQuizesPDF.php?jobid=$job->id&launch=$job->timestamp&a=$blended->id\">".get_string('runTask','blended')."</a>";
			$launch = $link_assignment_page;
			}
		}
		else
		{
		//$quiz = $DB->get_record('quiz', "id", $job->quiz);
		$quiz = $DB->get_record('quiz',array('id'=>$job->quiz));
		$pdfFile = blended_prepare_and_get_paths ($job,$quiz);
		
		
		$launch = "<a href=\"$CFG->wwwroot/file.php/$pdfFile->RelativePath?forcedownload=1\">".get_string('viewPDF','blended')."</a>";
		}		
	
		$fecha = date("Y-m-d-H-i-s", $job->timestamp);		
		//$user_reg = $DB->get_record($table='user', $field='id', $value=$job->userid);
		$user_reg = $DB->get_record('user',array('id'=> $USER->id));
		$fullname=fullname($user_reg);
		
		$jobrow=array();	
		$jobrow["name"] = $job->quiz_name;
    	$jobrow["date"]= $fecha;
    	$jobrow["estado"]= $statusMsg;
    	$jobrow["userid"]= $fullname;
    	$jobrow["delete"]= $delete;
    	$jobrow["launch"]= $launch;
    	
    	$jobs[$i]=$jobrow;
		unset ($status);
		$i++; 
		
		}//if userid, blended
		
	}// foreach
	$table = new object();
	$table->class = 'mytable';
	$table->head  = array(
							get_string('modulename','quiz'), 
							get_string('date'),
							get_string('resultlink','blended'),
							get_string('user'),
							get_string('delete'),
							''
							);
	$align = "left";
	$table->align = array ($align, $align, $align, $align, $align, $align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $jobs;
	
  	echo $OUTPUT->table($table); 
}

    
// Get all the appropriate data
    if (!$quizzes = get_all_instances_in_course("quiz", $course)) 
    {
 //   	helpbutton($page='noquizzes', get_string('noquizzes','blended'), $module='blended', $image=true, $linktext=false, $text='', $return=false,$imagetext='');
    	echo $OUTPUT->help_icon('noquizzes','blended');
    	notice(get_string('noquizzes', 'blended'), "../../course/view.php?id=$course->id");
        die;
    }
    $data = array();
  
 	foreach ($quizzes as $quiz) 
 	{
//        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
//        $context = context_module::instance( $cm->id);
 		$data [$quiz->id] = $quiz->name;
 	}
 	
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($strpaperquiz));
    echo $OUTPUT->box(format_text($strpaperquizdescr), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
 	
   // helpbutton($page='noquizzes', get_string('noquizzes','blended'), $module='blended', $image=true, $linktext=false, $text='', $return=false,$imagetext='');
  
   	$url = "generateQuizesPDF.php";
   	$mform = new PDFQuizzesForm($url,array('id'=>$blended->id,'quizzes'=>$data));
 
  
	
    if ($mform->is_cancelled())
    {
	//you need this section if you have a cancel button on your form
	//here you tell php what to do if your user presses cancel
	//probably a redirect is called for!

	$continue ="$CFG->wwwroot/mod/blended/view.php?id=$course->id";
	echo $strprocesscancelled;
	echo $OUTPUT->continue($continue);
	}
	else
	$mform->display();

/* else
  {
//FORMULARIO 	

	
    echo "<form method=\"post\" action=\"$url\"  id=\"paperquizform\" name=\"paperquizform\">"; 	
		echo '<input name="a" value="'.$blended->id.'" type="hidden"/>';
    // Tabla
	       echo "<fieldset><legend>$strpaperquizformat:</legend>";
	       echo '<table  width="40%" cellspacing="10" cellpadding="5" >';
	       
	// Page Configuration
			echo '<table>';
	       echo "<tr><td  rowspan=\"2\"><label>$strselectquiz</label></td>";   
	       echo '<td rowspan="2"><select name="paperquizformat" align="center">';
	       for ($i=0; $i < count($data); $i++)
	       {
	       		$id = $data[$i]->id;
	       		echo "<option value=".$id.">".$data[$i]->name."</option>";
	       }
	       echo "</select></td></tr>";
	       echo "<tr></tr>";
	       echo "<tr><td><label>$strnumquiz</label></td>";
	       echo '<td><input name="quiznumber" align="left" size="4" value="1"></td></tr>';
	       echo "<td><input type= checkbox name='cron' value = '1' align='left'>$strlater<br></td></tr>";
		   echo '</table>';
	       echo '</fieldset>';
	       
		echo '<td height="100%" valign="TOP">';
		echo "<fieldset><legend>$strlabelformat</legend>";
        echo "<LABEL FOR='identifyLabel'>$stridentify</legend>";
        echo "<br/><label for='none'><input type='radio' id='none' name='identifyLabel' value='none'>$strnotidentify</input></legend>";
        echo "<br/><label for='id'><input type='radio' name='identifyLabel' checked='true' id='id' value='id'>$strreadable</input></legend>";
		echo '</fieldset>';
		echo '</table>';
		//echo '</table>';
		
		// Boton GENERAR FICHEROS
	   	   echo '<table align="center">';
	         //echo "<tr><td><input type=\"submit\" value=\"Generar ficheros para el cuestionario\" onClick=\"checkform()\" /></td></tr>";
	        echo "<tr><td><input type=\"submit\" value=\"Generar ficheros para el cuestionario\"/></td></tr>";
	         
	         echo '</table>';
	 echo "</form>";    
  }*/
  
  
//     echo "<center>";
//     helpbutton($page='edit_paperquiz', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
//     echo "</center>";
    echo $OUTPUT->help_icon('pagehelp','blended');
    

    // Finish the page
    //print_footer($course);

    echo $OUTPUT->footer();
?>
