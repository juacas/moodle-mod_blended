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

 * @author Pablo Galan Sabugo, David Fernandez, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/

require_once ('ResultsError.php');

function find_userid($acode,$jobid)
{
	$select="label='USERID' AND activitycode=$acode AND jobid=$jobid AND value IS NOT NULL AND value!='null' AND value!=''";
	$regs = get_record_select($table='blended_results', $select);
	
	if ($regs)
		return $regs->value;	
	else 
		return null;
}

/**
 * Obtain question fields for a QUIZ attempt with $acode id
 * @param MoodleQuickForm $mform
 * @param unknown_type $acode
 * @param unknown_type $jobid
 * @param unknown_type $warnings
 */
function display_questions(MoodleQuickForm $mform,$acode,$jobid,$warnings)
{
	$keys=array('A','B','C','D','E','F','G','H','I','J','K','L');
	$rarray = array();
	$iarray = array();
	$keysarray = array();
	$iresponses = array();
	
	$labels=get_labels($acode,$jobid);
	//layout is the array with questions id. Warning does not correspond with actual order in page.
	$layout=get_layout($acode);
	$select="activitycode=$acode AND jobid=$jobid AND label LIKE 'q%'";
	$options= get_records_select('blended_results',$select, 'page, id');
	
	/**
	 * Convert to an array of questions
	 */
	$questions=array();
	foreach ($options as $option)
	{
		$pos=strpos($option->label,'-');
		$qid=substr($option->label, 1,$pos-1);
		$questions[$qid][]=$option;	
	}
	
	$i=1;
	$page=-1;
	/**
	 * iterate according with the fieldset order
	 */
//	print_object($questions);die;
//	foreach ($layout as $qid)
	foreach ($questions as $question)
	{
		
		//$question=$questions[(int)$qid];
		
		$optpage=$question[0]->page;
		if ($page!=$optpage)
		{
			$page=$optpage;
			$mform->addElement('static','PAGE'.$page,get_string('page','blended').' '.$page);
			$mform->addElement('html','<hr/>');
		}
		
		//$idqs=select_question($labels,$numberofquestion,$acode);

	//	$defaults=get_question_values($question,$acode,$jobid);

		$returnvalues = create_question($mform,$i,$question,$keys,$warnings);//,$defaults->invalid);
	
	
		$invalid = $returnvalues -> invalid;
		if(isset($returnvalues->radioindex))
			$radioindex = $returnvalues ->radioindex;
		if ($radioindex !== null)
			$rarray[$radioindex] = 1; //creo un array con los numeros radiobutton
		
		if ($invalid !== null)
		{
		$iarray[$radioindex] = 1;
		
		//$iresponses[$radioindex] = $defaults;
		$ilabels = get_ilabels($defaults);
		$iname = 'ilabel'.$radioindex;
		$mform->addElement('hidden', $iname, $ilabels);
		}
		
	$i++;
	}   	
	
	if (count($iarray) !== 0)
	{
		//print_object($iarray);
		print ("<CENTER>Por favor, corrija manualmente las respuestas inválidas</CENTER>");
	}
	//print_object ($iarray);
	
	$keysarray = array_keys($rarray);
	$radiokeys = implode(',',$keysarray);
	
	
	$ikeysarray = array_keys($iarray);
	$invalidkeys = implode(',',$ikeysarray);
	
//	print_object ($invalidkeys);
//	print_object ($ilabels);
	
	
	$mform->addElement('hidden', 'radioindex', $radiokeys);
	
	if($invalidkeys !== null)
	$mform->addElement('hidden', 'invalidquestions', $invalidkeys);
	
	
	return;
}

function get_ilabels($default)
{
	$ikeys = array();
	$ikeys = array_keys($default);
	$ilabels = implode(',',$ikeys);
	
	return $ilabels;
}
/**
 * 
 * @throws ResultsError
 * @param unknown_type $acode
 * @param unknown_type $jobid
 */
function get_labels($acode,$jobid)
{
	$records=get_records_select($table='blended_results',
		"activitycode='$acode' AND jobid='$jobid'", $sort='page,id');
	
	if ($records) 
	{
		$i=0;
		foreach ($records as $record)
		{		
			$labels[$i] = $record->label;	
		$i++;
		}
	}
	else 
		{
		throw new ResultsError("No existen registros en la tabla blended_results para este cuestionario.
								Es posible que haya ocurrido un error durante el proceso de reconocimiento OMR.",ResultsError::TABLE_BLENDED_RESULTS_IS_EMPTY);
		}
	return $labels;
}


function get_layout($acode)
{
	if	(($record=get_record($table='blended_attempts', $field='id', $value=$acode)) !== false) 
	$layout = $record->layout;
	else
	throw new ResultsError ('No existe el registro en la tabla blended_attempts para el cuestionario número'.$acode. 
	'. No se pudo encontrar el conjunto de preguntas del quiz: '. $acode, ResultsError::BLENDED_ATTEMPT_REG_DOES_NOT_EXIST);

	$layout=explode(',',$layout);
	array_pop($layout);
	return $layout;
}

function select_question($labels,$numberofquestion,$acode)
{
	
	$idqs = new stdClass();
	foreach ($labels as $key => $label)
	{
		if(($question = find_question($label,$numberofquestion,$acode)) !== false)
		{
			//print('estoy en el if y question: '.$question);
			$idqs->$key= $question;
		
		}

	}
	
	$idqs=(array)$idqs;
	return $idqs;
}

function find_question($label,$numberofquestion,$acode)
{
	$questionlabel='q'.$numberofquestion.'-';
	
	//print("buscando: ".$questionlabel.'<br>');
	
	if( ($pos=strpos($label,$questionlabel)) !== false)
	{
	$question -> label = $label;
	$question -> questiontype = get_questiontype($label,$acode);
	
	}
	
	else
	{
		//print('estoy en else false');
		$question=false;
	}
	
	return $question;
}

function get_questiontype($label,$acode)
{
	$questiontype = get_field ($table = 'blended_results', $field='questiontype', $field1='activitycode', $value1=$acode,
	$field2 = 'label', $value2 = $label);
	
	return $questiontype;
}
/**
 * Populate mform with a new question
 * @param MoodleQuickForm $mform
 * @param unknown_type $number
 * @param unknown_type $options
 * @param array $keys array of letters or symbols to numerate the options i.e. A,B,C,D...
 * @param unknown_type $defaults
 */
function create_question(MoodleQuickForm $mform,$number,$options,$keys,$warnings=null)
{
	
	//print_object($options);
	//print_object($number);
	$returnobj = new stdClass();
	
//	if (isset($defaults['invalid']))
//		$invalid = $defaults['invalid'];
//	else
//		$invalid = null;
	
	$i=0;
	$questionarray=array();
	$warn='';
	$icon='';
	/**
	 * option to unmark all others. Marked if all options are clear
	 */
		if ($options[0]->questiontype == 'CIRCLE')
				{
				$questionarray[] =&MoodleQuickForm::createElement('radio', ''.$number, '', get_string('unmarked','blended'),$number.'none');
				$mform->setDefault(''.$number,$number.'none');
				}
	foreach ($options as $option)
	{
		$option->key=$keys[$i];
	/**
	 * Report about any warning of error with this option
	 */
		if (isset($warnings[$option->id]))
			{
				$warn.=$warn?',':'';
				$warn.=" $option->key";
				$icon='<img src="images/warning.png" width="16" alt="'.get_string('MarkWarning', 'blended').'"/>';
			}
		if (isset($warnings[$option->id]))
			$warnOptionStyle=$icon;
			else
			$warnOptionStyle='';
			
	
				
		if ($option->questiontype == 'SQUARE')
		{
		$questionarray[] =&MoodleQuickForm::createElement('advcheckbox', $option->label, '', $warnOptionStyle.$option->key, array('group' => 1));
		$returnobj->radioindex = null;
		}
		else
		if ($option->questiontype == 'CIRCLE')
		{
		$questionarray[] =&MoodleQuickForm::createElement('radio', ''.$number, '', $warnOptionStyle.$option->key,$option->label);
		
		$returnobj->radioindex = $number;
		}
			
		$i++;
	}
	
	
	foreach ($options as $option)
	{
		if ($option->value==1)
			$mform->setDefault(''.$number,$option->label);
	}
	
	$questionName='Pregunta '.$number;
	if ($warn)
	{
		$questionName=$questionName.$icon;
	}

	$mform->addGroup($questionarray, $number, $questionName, array(''), false);
if ($warn)
	{
		$mform->setHelpButton($number, array('MarkWarning',get_string('MarkWarning', 'blended'),'blended'),false);
	}
	/**
	 * TODO resolver manejo de opciones inválidas
	 * Enter description here ...
	 * @var unknown_type
	 */
	$invalid=0;
	if ($invalid == 1)
	{
		$returnobj -> invalid = 1;
		
		//$mform->addElement('hidden', 'invalid', $invalidquestions);
		mtrace('<CENTER>La pregunta número '.$number.' es inválida. Se han encontrado múltiples respuestas 
		para una pregunta de selección simple. <BR><BR></CENTER>');
	}
	else
		$returnobj -> invalid = null;
	
	return $returnobj;	
}

function get_question_values($idqs,$acode,$jobid)
{
	$regs=get_records($table = 'blended_results', $field='jobid', $val=$jobid);
	
	foreach ($regs as $reg)
	{	
		if ($reg->activitycode == $acode)
		{
			foreach ($idqs as $question)
			{
			$label=$question->label;
			
				if ($reg->label == $label)
				{
				$default ->$label = $reg -> value;
				$default ->invalid = $reg -> invalid;
				}
			
			}
		}
	
	}
	
	if (isset($default))
	$default=(array)$default;
	else
	$default = null;
	
	return $default;
}
/**
 * Extract special Labels named EVAL-0 to EVAL-10
 * @param unknown_type $labels
 */
function find_numberofeval($labels)
{
	$pos=array_search('USERID', $labels);
	$split=array_slice($labels,0,$pos);
	$evals=select_eval($split);
//print_object($evals);
	$numberofeval=count($evals);
	//print $numberofeval;
	return $numberofeval;
}

function display_eval($mform,$acode,$default,$jobid)
{
	
	$labels=get_labels($acode,$jobid);
	
	$numberofeval=find_numberofeval($labels);
	
	create_eval($mform,$numberofeval,$default);
		
	return;
}


function select_eval($labels)
{
	$evals = new StdClass();
	foreach ($labels as $key => $label)
	{
		if(($eval = find_eval($label)) !== false)
		{
			//print('estoy en el if y eval: '.$eval);
			$evals->$key= $eval;
		}

	}
	
	$evals=(array)$evals;
	return $evals;
}

function find_eval($label)
{
	$evallabel='EVAL-';
	//print("buscando: ".$evallabel.'<br>');
	
	if( ($pos=strpos($label,$evallabel)) !== false)
	{
	$eval=$label;
	//print("encontrado: ".$eval);
	}
	
	else
	{
		//print('estoy en else false');
		$eval=false;
	}
	
	return $eval;
}

function create_eval($mform,$numberofeval,$default)
{


	//print ("estoy en create_eval con default: ".$default);
	$radioarray=array();
	for ($i=0; $i< $numberofeval; $i++)
	{
		$name = 'Eval-'.$i;
		$radioarray[] = &MoodleQuickForm::createElement('radio', 'Eval', '', $name , $i);
	}
	
	//print $default;
	$mform->setDefault('Eval',$default);
	$mform->addGroup($radioarray, 'EVAL', 'Eval: ', array(''), false);
	
	$mform->setHelpButton('EVAL', array('EVAL',get_string('EVAL', 'blended'),'blended'),false);
	
	return;

}

function get_eval_value($acode,$jobid)
{
	$defarray = array();
	
	
	$labels=get_labels($acode,$jobid);
	
	
	$numberofeval=find_numberofeval($labels);
	
	if(($evalregs=get_records($table='blended_results', $f2 = 'jobid', $v2 = $jobid)) !== false)
	
	foreach ($evalregs as $evalreg)
	{
		
		if ($evalreg->activitycode == $acode)
		{ 
		if 	($evalreg->page == 1)
		{	
			for ($i=0; $i < $numberofeval; $i++)
			{
			
				$labelvalue='EVAL-'.$i;
							
				if ($evalreg->label == $labelvalue)
				{
					//print_object($evalreg);
				
					if ($evalreg->value == 1)
					{
					$evallabel = $evalreg->label;
					$defarray=explode('-',$evallabel);
					//print ("llego y label =");
					//print $evallabel;
					//print_object($defarray);
					}
					
				}
			}
		
		}
		}
		
	}

	if (isset($defarray[1]))
	$defaulteval=$defarray[1];
	else
	$defaulteval = 0;
		
	return $defaulteval;
}

function process_results_form($data)
{
	//print_object($data);
	// data is an object.
	$arraydata=(array)$data;
	$options=array();
	if (isset($arraydata['submitbutton']))
		$action='updatedata';
		else
	if (isset($arraydata['errors_resolved']))
		$action='deleteinvalidmark';
		
	//print_object($arraydata);print($action);die;

	//arreglamos los datos que vienen de radiobutton
	$radioarray = array();
	$radioarray = explode(',',$data->radioindex);
	//print_object($radioarray);
	
	foreach ($radioarray as $radiokey => $radiovalue)
	{
		//print ($radiokey.$radiovalue.'<BR>');
		if ($err=array_key_exists($radiovalue,$arraydata))
		{
			$label = $arraydata[$radiovalue];
			if (strpos($label, 'none')==true) // Exclude unmarked questions in form
				continue;
			$options[$label]=1;
			//print($arraydata[$radiovalue].'<BR>');
		}
	}
	$options['TEMPLATEFIELD']=$arraydata['TEMPLATEFIELD'];
	$options['USERID']=$arraydata['USERID'];
	$options['Eval']=$arraydata['Eval'];
	
	//tratamiento de respuestas inválidas
	if ($data->invalidquestions !== null)
	{
		$invalidarray = array();
		$invalidarray = explode(',',$data->invalidquestions);
		//print_object($invalidarray);
	
		
			foreach ($invalidarray as $invalidkey => $invalidvalue)
			{
			
			if ($invalidvalue !== 0)	
			{	
				//print ('Soy invalidvalue '.$invalidvalue);
				if (array_key_exists($invalidvalue,$arraydata))
				if (($arraydata[$invalidvalue]) !== null)
				{
				print('<CENTER>La pregunta número '.$invalidvalue.' ha sido corregida.</CENTER>');
				update_invalid_status($invalidvalue,$data);
				}
				else
				{
				print('');
			//print('La pregunta número '.$invalidvalue.' NO ha sido corregida.');
				}
		
			}
	
		}
	}
	$acode=$data->acode;
	$jobid=$data->jobid;
	
	//print_object($arraydata);print_object($options);die;
	unset_values($acode,$jobid);
	//print_object($arraydata);
	
	foreach ($options as $key =>$value)
	{
		$label=$key;
		$value=$value;
		
		if ($key == 'Eval')
		{
		$label=$key.'-'.$value;
		$value=true;
		}
		//mtrace("<p>update $label=$value</p>");	
		update_blended_results($acode,$jobid,$label,$value);	
	}
	
	//print_object($data);
		$userid = $data -> USERID;
		update_blended_results($acode,$jobid,'USERID',$userid);
	//	update_userid_blended_attempts($acode,$userid);
	if ($action=='deleteinvalidmark')
	{
		set_field('blended_results', 'invalid', 0, 'activitycode', $acode,'jobid',$jobid);
	}
	return;

}

function update_invalid_status($invalidnumber, $data)
{
	$ilabelsarray = array();
	$acode = $data -> acode;
	$jobid = $data -> jobid;
	
	$ilabel = 'ilabel'.$invalidnumber;
	$ilabelsarray = explode(',',$data->$ilabel);
	//print_object($ilabelsarray);
	
	foreach ($ilabelsarray as $key => $label)
	{
		if ($label !== 'invalid')
		{
		//print ('actualizo '.$label);	
		
		set_field($table = 'blended_results', $field = 'invalid', $value = 0, $field1 = 'activitycode',
		$value1 = $acode, $field2 = 'jobid', $value2 = $jobid, $field3 = 'label', $value3 = $label);
		
		}
		
	}
	
	return;
}

function update_blended_results($acode,$jobid,$label,$value)
{
	if (!set_field('blended_results', 'value', $value, 'activitycode',$acode,'label',$label,'jobid',$jobid))
	{
		mtrace(" No se pudo actualizar la base de datos blended_results");
	}
	return;
}
/**
 * Unset defined values.Do not remove doubt marks.
 * @param unknown_type $acode
 * @param unknown_type $jobid
 */
function unset_values($acode,$jobid)
{
	$result=set_field_select('blended_results', 'value', null, "activitycode='$acode' and jobid='$jobid' and value != '?'" );
	
/*	if ($result == false);
	{
		error ('No se pudo resetear el campo VALUES antes de guardar.');
	}*/
	return;
}

function update_userid_blended_attempts($acode,$userid)
{
	set_field('blended_attempts', 'userid', $userid, 'activitycode', $acode);
	return;
}
function count_doubtfull_marks($acode,$jobid)
{
	$reg=get_record_select('blended_results',"activitycode = $acode AND jobid=$jobid AND (value='?' OR invalid=2)",'count(*) as count');
	return $reg->count;
}
function get_doubtfull_marks($acode,$jobid)
{
	$reg=get_records_select('blended_results',"activitycode = $acode AND jobid=$jobid AND (value='?' OR invalid=2)");
	
	return $reg;
}
?>