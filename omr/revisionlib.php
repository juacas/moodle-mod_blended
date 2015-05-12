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

/**
 * 
 * Show attempts made by a user
 * @param unknown_type $blended
 * @param $USER $user $USER object
 */
function review_results($blended,$user)
{
	$quiz_attempts=search_quiz_attempts_regs($blended,$user);
		
	if($quiz_attempts !== null)
	review_results_table($blended,$user,$quiz_attempts);
	
	return;
}
/**
 * 
 * Enter description here ...
 * @param unknown_type $blended
 * @param $USER $user
 */
function search_quiz_attempts_regs($blended,$user)
{
	global $DB;
	//print_object($USER);
	$quiz_attempts=null;
	
	
// 	$quiz_attempts = get_records($table='quiz_attempts', $field='userid', $value=$user->id,'timefinish DESC');
	$quiz_attempts = $DB->get_records('quiz_attempts',array('userid'=>'timefinish DESC'));
	
	if ($quiz_attempts == null)
	{
		echo "<center>";	
		echo ('No se han encontrado cuestionarios evaluados para este usuario.');
		echo "</center>";
		
		$continue =  "../../course/view.php?id=$blended->course";
		print_continue($continue); 
		//debugging('No se han encontrado cuestionarios evaluados para este usuario.');
	}

	return $quiz_attempts;
}
/**
 * 
 * List attempts made in a table
 * @param unknown_type $blended
 * @param unknown_type $user $USER object
 * @param unknown_type $quiz_attempts
 */
function review_results_table($blended,$user,$quiz_attempts)
{
	$reviews=array();
	
	foreach ($quiz_attempts as $quiz_attempt)
	{
	$timefinish=$quiz_attempt->timefinish;
	
	if($timefinish != 0)
		{	
		$acode = get_quiz_acode($quiz_attempt->uniqueid);
		
		$jobid = find_jobid($acode,$user->id);
		
		
		$reviewrow=array();
			
		//$reviewrow["name"]=fullname($user);
		//$reviewrow["id"] = print_user_picture($user,$blended->course,null,null,true);
		// actually not used. By now we hide unclosed attempts
		if ($timefinish==0)
			$reviewrow["date"] = get_string('notclosed','blended');	
		else
			$reviewrow["date"] = date(DATE_RSS,$timefinish);	
		$quizid = $quiz_attempt -> quiz;
		$quizname=get_string('modulename','quiz').' '.$quizid;
			
			$quizcorrectionlink="<a href=\"../quiz/review.php?q=$quizid&attempt=$quiz_attempt->id\">$quizname ".get_string('attempt','quiz',$quiz_attempt->attempt)."</a>";
			$blendedcorrectionlink="<a href=\"reviewdetails.php?&a=$blended->id&acode=$acode&jobid=$jobid&quizid=$quizid\">".get_string('scannedJob','blended')."</a>";
	
		$reviewrow["correctionlink"]= $quizcorrectionlink;
		if ($acode!='')
			{
			$reviewrow["scanreviewlink"]= $blendedcorrectionlink;
			}
			else
			{
			$reviewrow["scanreviewlink"]= get_string('ErrorActivityCodeNotFound','blended',$quizid);
			}							 
			$reviews[]=$reviewrow;
		}
	}
	
	if (count($reviews)!=0)
	{
		//tabla de resultados
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array("fecha",get_string('resultlink','blended'),get_string('scannedJob','blended'));
	$align = "left";
	$table->align = array ($align,$align,$align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $reviews;
	//print_object($table);
	print_table($table);
	}
	

}


function review_details_table($jobid,$acode,$course,$a,$quizid)
{
	global $CFG;
	
	$attempt = get_attempt($acode);
	$currentpage="reviewdetails.php";
	display_imagesintable($jobid,$acode,$course,$a,$currentpage,'');
	echo "<table align=center><tr><td>";
	echo "<A HREF=\"$CFG->wwwroot/mod/quiz/review.php?&id=$course->id&q=$quizid&attempt=$attempt
 			\" >Ver Evaluación</A>";
	echo "</td>";
	

	
	
	echo "</table>";
	return;
}

function find_jobid($acode,$userid)
{
	$images = get_records($table='blended_images','activitycode',$acode);
	if (!$images)
		return null;
	foreach ($images as $image)
	{	
		if ($image -> jobid !== null)
		$jobid = $image -> jobid;
		break;
	}
	
	
	return $jobid;
}

function get_attempt($acode)
{
	$uniqueid = get_field ($table='blended_attempts','attempt',$field='id',$value=$acode);
	
	$attempt = get_field ($table='quiz_attempts','id',$field='uniqueid',$value=$uniqueid);
	
	return $attempt;
}

function get_quiz_acode($uniqueid)
{
	$acode = get_field('blended_attempts','id','attempt',$uniqueid);
	
	return $acode;
}
?>
