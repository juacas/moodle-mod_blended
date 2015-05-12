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

* @author J�ssica Olano L�pez, Juan Pablo de Castro .
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
*********************************************************************************/

require_once("../../../config.php");
require_once("../lib.php");
require_once($CFG->dirroot . '/group/lib.php');
require_once("$CFG->libdir/formslib.php");


//Definicion de los agrupamientos existentes
class groupings_form extends  moodleform{
	
	function definition(){
		
		global $DB, $PAGE, $OUTPUT;
		$id   = optional_param('id',  0, PARAM_INT); 
		$a    = optional_param('a',   0, PARAM_INT); 
		
		if ($id) {	
			if (! $cm = get_coursemodule_from_id('blended', $id)){
           	 	print_error("Course Module ID was incorrect");
        	}
    
       	 	if (! $course = get_course($cm->course)) {
           	 	print_error("Course is misconfigured");
        	}
    
        	if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            	print_error("Course module is incorrect");
        	}
        	if (! $context = context_course::instance( $course->id)) {
           	 	print_error("Context ID is incorrect");
        	}

    	} else {
        	if (! $blended = $DB->get_record('blended', array( 'id'=> $a))) {
            	print_error("Course module is incorrect");
        	}
        	if (! $course = $DB->get_record('course', array('id' => $blended->course))) {
            	print_error("Course is misconfigured");
        	}
        	if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
            	print_error("Course Module ID was incorrect");
        	}
        	if (! $context = context_course::instance( $course->id)) {
            	print_error("Context ID is incorrect");
        }
    }
		
					
		$form= &$this->_form;
		$t=0;
		$agrupamientos=array();
		$agrupamientos[$t]='Eliga';
		
		if($groups= groups_get_all_groupings($course->id)){
			foreach($groups as $group){
				$t++;
				$agrupamientos[$t] = $group->name;
		
			}
		}
		//Select Elements
		$form->addElement('select', 'grouping', get_string('select_grouping','blended'), $agrupamientos);
		
	}
}

?>