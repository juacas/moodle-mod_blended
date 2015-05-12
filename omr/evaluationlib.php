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

    require_once("$CFG->dirroot/config.php");
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');
    require_once ("../lib.php");
    require_once("correctionlib.php");
    require_once("EvaluationError.php");

function evaluate_quiz($acode,$jobid,$newattempt,$blended)
{
	global $USER;
	global $CFG;
	
	mtrace ("Evaluation QUIZ Processing..."."<BR><BR>");
	try{
	
	

	print "New Attempt is: ".$newattempt."<BR/>";
	$detected_userid = find_userid($acode,$jobid); 
	
	if ($detected_userid == null or $detected_userid == '')
		throw new EvaluationError(get_string('ErrorUserIDEmpty','blended'), EvaluationError::USERID_IS_EMPTY);
		
	$user_reg = blended_get_user($detected_userid,$blended);	
	
	if ($user_reg == null)
		throw new EvaluationError(get_string('ErrorUserNotInCourse','blended'),EvaluationError::USER_NOT_IN_THIS_COURSE);
	
	$userid = $user_reg -> id;
	mtrace('Obtained USERID value: '.$userid." OK. <BR/>");
	
	$quiz = get_quiz($acode);
	$attempts = quiz_get_user_attempts($quiz->id, $userid,'all',true);
		
	mtrace ("Searching quiz... Success."."<BR/>");
	
	$uniqueid=get_uniqueid($acode);
	mtrace('Obtained uniqueid: OK. <BR/>');
	
	$timestamp=get_timestamp($acode);
	mtrace('Obtained timestamp: OK. <BR/>');

	if (!get_record('quiz_attempts', 'uniqueid', $uniqueid))
	{
		$newattempt=true;
	}
	else
	{
		$newattempt=false;
		mtrace("User $userid had opened this attempt already.");
	}
	
	$attemptnumber=1;
	
	if ($newattempt == false)
	{
		mtrace('Obtaining user attempt...<BR/>');
		set_attempt_unfinished($uniqueid);
		$attempt = quiz_get_user_attempt_unfinished($quiz->id, $userid);
	}	
	elseif ($newattempt == true)
	{
		mtrace('Creating new attempt...<BR/>');
	$attempt = create_new_attempt($quiz, $attemptnumber,$userid,$acode,$uniqueid,$timestamp);
        // Save the attempt
    if (!insert_record('quiz_attempts', $attempt)) {
        throw new EvaluationError(get_string('ErrorCouldNotCreateAttempt','blended'),EvaluationError::CREATE_QUIZ_ATTEMPT_ERROR);
    	}
	// Actualizamos el estado de las imágenes para indicar que ya está creado un nuevo attempt
     	update_images_status($acode,$jobid);
	}
	
    update_question_attempts($uniqueid);
// /*   

    mtrace ('<BR>Getting questions and question options... ');
	$questions=get_questions($attempt,$quiz);

	if (!get_question_options($questions)) {
        error('Could not load question options');
    }
    mtrace ('Success! <BR>');
	
    //	print ("<BR>He obtenido questions: ");
	//print_object($questions);

	
	$lastattemptid=false;
//	 if ($attempt->attempt > 1 and $quiz->attemptonlast and !$attempt->preview) {
        // Find the previous attempt
  //      if (!$lastattemptid = get_field('quiz_attempts', 'uniqueid', 'quiz', $attempt->quiz, 'userid', $attempt->userid, 'attempt', $attempt->attempt-1)) {
    //        error('Could not find previous attempt to build on');
      //  }
    //}
   //print ('He obtenido lastattemptid');

 	mtrace ('Getting question states... ');
	if (!$states = get_question_states($questions, $quiz, $attempt, $lastattemptid)) {
        error('Could not restore question sessions');
    }
 	mtrace ('Success! <BR>');
  
    mtrace ('Getting responses... <BR>');
    $responses=get_responses($acode,$jobid,$attempt);
    //print('Estas son las responses:');
	//print_object($responses);


	//$timestamp=time();
	$event=8;
    $actions = question_extract_responses($questions, $responses, $event);
    
       	$questionids=get_questionids($acode);
 	//	print $questionids;
	 	
       	$questionidarray = explode(',', $questionids);
        $success = true;
        mtrace ('<BR> Processing responses and saving session... ');
        foreach($questionidarray as $i) {
            if (!isset($actions[$i])) {
                $actions[$i]->responses = array('' => '');
                $actions[$i]->event = QUESTION_EVENTOPEN;
            }
            $actions[$i]->timestamp = $timestamp;
           
            if (question_process_responses($questions[$i], $states[$i], $actions[$i], $quiz, $attempt)) {
                save_question_session($questions[$i], $states[$i]);
            } else {
                $success = false;
            }
        }
		mtrace ('Success! <BR>');
      
		
        // Set the attempt to be finished
        $timestamp = time();
        //$attempt->timefinish = $timestamp;
		         
// Update the quiz attempt and the overall grade for the quiz
        mtrace ('<BR> Finishing the attempt... ');
       // print_object ($attempt);
        if ((set_field('quiz_attempts','timefinish',$timestamp,'uniqueid',$uniqueid)) == false) {
           	throw new EvaluationError('Unable to finish the quiz attempt!',EvaluationError::FINISH_QUIZ_ATTEMPT_ERROR);
        } mtrace ('Success! <BR>');
        
        if (($attempt->attempt > 1 || $attempt->timefinish > 0) and !$attempt->preview) {
      	 mtrace ('<BR> Saving quiz grade... ' );
        	quiz_save_best_grade($quiz,$userid);
        } mtrace ('Success! <BR>');
   


// */ 
	 mtrace("Process Done. <BR><BR>");
     mtrace("<center> Your quiz has been succesfully evaluated!! </center>");
     
     } catch (EvaluationError $e)
	{
		throw $e;
	}
	
        return;
}

function get_quiz($acode)
{
	if(($reg = get_record($table='blended_attempts', $field='id', $value=$acode)) == false)
	throw new EvaluationError(get_string('ErrorActivityCodeNotFound','blended',$acode) ,EvaluationError::MISSING_BLENDED_ATTEMPT);
	
	else
	$quiz_id = $reg->quiz;
	
	$quiz= get_quiz_module($quizid);	
	return $quiz;
}

/**
 * Load a record from the quiz module table
 * @param int $quizid
 * @throws EvaluationError
 */
function get_quiz_module($quiz_id)
{
	if(($quiz = get_record($table='quiz', $field='id', $value=$quiz_id)) == false)
		throw new EvaluationError(get_string('ErrorQuizNotFound','blended',$quiz_id)	,EvaluationError::QUIZ_DOES_NOT_EXIST);
	return $quiz;
}


function get_questions($attempt,$quiz)
{
	global $CFG;
	
		$questionlist = $attempt -> layout;
		//$questionlist = explode(',',$questionlist);
		//print_object ($questionlist);
		
		$sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}question q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";

    // Load the questions
    if (!$questions = get_records_sql($sql)) {
       mtrace("error al obtener questions");
    	// print_error('noquestionsfound', 'quiz', 'view.php?q='.$quiz->id);
    }
	
return $questions;
}

function get_responses($acode,$jobid,$attempt)
{
	$responses = new stdClass();
	$layout = get_layout($acode);
	array_pop($layout);
	
	$labels=get_labels($acode,$jobid);
	
	foreach ($layout as $numberofquestion)
	{
	
		$idqs=select_question($labels,$numberofquestion,$acode);
		$answers=get_question_values($idqs,$acode,$jobid);
		//print_object ($answers);
		
		if($answers !== null)
		foreach ($answers as $key => $answer)
		{
			if ($answer == true)
			{
				//print ($key.'=>'.$answer);
				//print "<BR>";
				$keyarray=explode('-',$key);
				$qid = $numberofquestion;
				$qanswer = $keyarray[1];
				$qlabel = 'resp'.$qid.'_'.$qanswer;
				$responses -> $qlabel = $qanswer;
			}
		}
		
	}
	
	$responses->finishattempt='Enviar todo y terminar';
	$responses->timeup=0;
	
	$questionids=get_questionids($acode);
	$responses->questionids=$questionids;
	
	return $responses;
}


function get_questionids($acode)
{
	$layout = get_layout($acode);
	array_pop($layout);
	$questionids=implode(',',$layout);
	
	return $questionids;
}

function get_uniqueid($acode)
{
	$record=get_record($table='blended_attempts', $field='id',$value=$acode);
	
	$uniqueid = $record->attempt;
	
	return $uniqueid;
}

function get_timestamp($acode)
{
	$record=get_record($table='blended_attempts',$field='id',$value=$acode);

	$timestamp = $record -> timestamp;	
	
	return $timestamp;
}

function update_question_attempts($uniqueid)
{
	$question_attempt = new stdClass();
	
	$question_attempt->id = $uniqueid;
	$question_attempt->modulename = 'quiz';
	
	update_record('question_attempts', $question_attempt);
	return;
}

function set_attempt_unfinished($uniqueid)
{
	$quiz_attempt = get_record($table = 'quiz_attempts', $field = 'uniqueid', $value = $uniqueid);
	
	$quiz_attempt -> timefinish = 0;
	
	update_record('quiz_attempts', $quiz_attempt);
	return;
}

function create_new_attempt($quiz, $attemptnumber,$userid,$acode,$uniqueid,$timestamp) {
    global $CFG;

    $layarray=get_layout($acode);
    $layout = implode (',',$layarray);
	//print_object($layout);
    
    if (!$attemptnumber > 1 or !$quiz->attemptonlast or !$attempt = get_record('quiz_attempts', 'quiz', $quiz->id, 'userid', $userid, 'attempt', $attemptnumber-1)) {
        // we are not building on last attempt so create a new attempt
        $attempt->quiz = $quiz->id;
        $attempt->userid = $userid;
        $attempt->preview = 0;
        $attempt->layout = $layout;
        
    }

   	$attempt->attempt = $attemptnumber;
    $attempt->sumgrades = 0.0;
    $attempt->timestart = $timestamp;
    $attempt->timefinish = 0;
    $attempt->timemodified = $timestamp;
    $attempt->uniqueid = $uniqueid;
	//$attempt->id = $uniqueid;
    
    return $attempt;
}

function update_images_status($acode,$jobid)
{
	if (set_field('blended_images', 'status', IMAGE_STATUS_PASSED, 'activitycode', $acode,'jobid',$jobid)==false)
		{		
			error(" No se pudo actualizar la base de datos blended_images");
		}	
	return;
}

function find_quizid($acode)
{
	if(($record = get_record($table='blended_attempts', $field='id', $value=$acode)) == false)
	error('Error al intentar encontrar el registro id='.$acode.' en la tabla blended_attempts');
	
	$quizid=$record->quiz;
	//print("He encontrado el numero de cuestionario ".$quizid);
	return $quizid;
}

?>