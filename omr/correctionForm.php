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

require_once ($CFG->libdir.'/formslib.php');
require_once('correctionlib.php');

require_once('ResultsError.php');


class correctionForm extends moodleform {

	var $values;

function correctionForm($values)
{
	$this->values=$values;
	parent::moodleform("showdetails.php");
}

function definition() 
	{
    
	$values=$this->values;
 
	$attributes='size=4';
	$acode=$values->activitycode;
    $jobid=$values->jobid;
    $a = $values -> a;
    $id = $values -> id;
	
	$default=$values->eval;
	
    $mform =& $this->_form; // Don't forget the underscore! 
 	$mform->addElement('text', 'TEMPLATEFIELD', get_string('activitycode', 'blended'), $attributes);
 	$mform->setHelpButton('TEMPLATEFIELD', array('TEMPLATEFIELD',get_string('activitycode', 'blended'),'blended'));
	
 	$mform->setDefault('TEMPLATEFIELD', $values->activitycode);
    $mform->addRule('TEMPLATEFIELD', null, 'required', null, 'client');
   
	$mform->addElement('text', 'USERID', get_string('idlabel', 'blended'), $attributes);
	$mform->setHelpButton('USERID', array('USERID',get_string('userid', 'blended'),'blended'));
	
	$mform->addRule('USERID', null, 'required', null, 'client');
	$mform->setDefault('USERID', $values->userid);
    
	$mform->addElement('hidden', 'id', $id);
	$mform->addElement('hidden', 'a', $a);
	$mform->addElement('hidden', 'acode', $acode);
	$mform->addElement('hidden', 'jobid', $jobid);
	
	
	try {
	display_eval($mform,$acode,$default,$jobid);
	
	$mform->addElement('static', 'PREGUNTA', 'Zona de respuestas: ');
	$mform->setHelpButton('PREGUNTA', array('PREGUNTA',get_string('PREGUNTA', 'blended'),'blended'),false);
	
	display_questions($mform, $acode,$jobid,$values->warnings);
	}
	catch (ResultsError $e)
	{
		//print "no llego aqui";
		throw $e;
	} 

	
    $buttonarray=array();
    $buttonarray[] = &$mform->createElement('submit', 'errors_resolved', get_string('errors_resolved','blended'));
    
    $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
    //$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('reset'));
    $buttonarray[] = &$mform->createElement('cancel');
    $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    $mform->closeHeaderBefore('buttonar');
        
    return $mform;    	
     
    }                           // Close the function
   
}                               // Close the class

class activitycodeForm extends moodleform {
 
    function definition() {
        
       	$attributes='size=8';
  
        $mform =& $this->_form; // Don't forget the underscore! 
        $mform->addElement('static',get_string('activitycodeRemoveLastDigit','blended'));
 		$mform->addElement('text', 'TEMPLATECODEFIELD', get_string('templatecode', 'blended'), $attributes);
		$mform->setHelpButton('TEMPLATECODEFIELD', array('TEMPLATECODEFIELD',get_string('templatecode', 'blended'),'blended'));
	
		$mform->addRule('TEMPLATECODEFIELD', null, 'required', null, 'client');
   
    //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
               
    }                           // Close the function
   
}                               // Close the class

class deleteForm extends moodleform {
 
    function definition() {
        
        $mform =& $this->_form; // Don't forget the underscore! 
 		      
         //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('delete'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
               
    }                           // Close the function
   
}                               // Close the class



?>