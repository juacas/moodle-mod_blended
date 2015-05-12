<?php
require_once("../../../config.php");
require_once("../lib.php");
require_once($CFG->libdir.'/gradelib.php');
require_once ($CFG->dirroot.'/grade/lib.php');


class activities_form extends  moodleform{

	function definition(){

   		global $DB, $PAGE, $OUTPUT;
    	$id = required_param('id',PARAM_INT);   // course module
    
    
        if (! $cm = get_coursemodule_from_id('blended', $id)){
        error("Course Module ID was incorrect");
        }

        if (! $course = get_course($cm->course)) {
        error("Course is misconfigured");
        }

        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
        error("Course module is incorrect");
        }

    
    	$form= &$this->_form;
    	
    	$calificables=new grade_tree($course->id);
    	$items=$calificables->items;
    	$ins = array ();  
    	$assignmentname=array();
    	// Obtenemos las referencias a toda la informaci�n sobre los modulos dentro del curso
    	$modinfo = get_fast_modinfo ( $course->id );
    	
    	foreach ( $modinfo->instances as $abc ) {
    		
    		foreach ( $abc as $cmd ) {
    	
    			foreach ( $items as $r => $ite ) {
    		
    				$ins [$r] = $ite->iteminstance;
    				
    				if ($cmd->instance == $ins [$r]) {
    			
    					$assignmentname[$r] = $cmd->name ;
    				}
    					
    			}
    		}
    	}
    	$form->addElement('select', 'activities','Selecciona la Tarea', $assignmentname);

	}
}
