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

* @author J�ssica Olano L�pez,Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
*********************************************************************************/

    require_once("../../../config.php");
    require_once("$CFG->dirroot/mod/assign/lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/gradelib.php');
    require_once ($CFG->dirroot.'/grade/lib.php');
    
    
    

// Get the params ----------------------------------------------------------------
    global $DB, $PAGE, $OUTPUT;
    $id    = required_param('id', PARAM_INT); // blended Course Module ID
    $groupingid= optional_param('groupingid',0,PARAM_INT);//Grouping ID
    
   
        if (! $cm = get_coursemodule_from_id('blended', $id)){
           print_error("Course Module ID was incorrect");
        }    
        if (! $course = get_course($cm->course)) {
            print_error("Course is misconfigured");
        }    
        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            print_error("Course module is incorrect");
        }
   

// Log ---------------------------------------------------------------------------

//    add_to_log($course->id, "blended", "grades", "grades.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    //require_login($course->id);
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    list($students,$non_students,$activeids, $user_records)=blended_get_users_by_type($context_course);
    if(count($students)==0)
    {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:introteams', $context);
 
    // show headings and menus of page
    $url =  new moodle_url('/mod/blended/grades.php',array('id'=>$id,'groupingid'=>$groupingid));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
   	$PAGE->set_pagelayout('standard');
    
// Get the strings --------------------------------------------------
    $strgradepage               = get_string("gradepage","blended");
    $strname                    = get_string("name","blended");
    $strduedate                 = get_string("duedate", "blended");
    $strgraded                  = get_string("graded", "blended");
    $strno                      = get_string("no", "blended");
    $stryes                     = get_string("yes", "blended");
    $strpartially               = get_string("partially", "blended");
    $strnumteams                = get_string('numteams', 'blended');

// Print the page header -------------------------------------------- 
    $PAGE->navbar->add($strgradepage);
    
    echo $OUTPUT->header();
    
// Print the main part of the page ----------------------------------   

    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string(get_string('gradepage', 'blended')));
    echo'<center>';
    echo $OUTPUT->box(format_text('Tareas a calificar: '), 'generalbox', 'intro');
    echo'</center>';
    echo $OUTPUT->spacer(array('height'=>20));
   

// Imprimimos el formulario por pantalla ---------------------------- 

    //Obtenci�n de las actividades que son calificables
    $items = blended_get_available_items($blended);

    if(empty($items)){
    	echo "<center>";
    	echo ('No hay actividades creadas para este curso');
    	echo "</center>";
    	
    	$continue =  "../../course/view.php?id=$course->id";
    	print_continue($continue);
    }
    else{
    	//Tabla que contiene las activdades calficables del curso
    	$table = new html_table;
    	$timenow = time();
    	$currentsection = "";
    	
    	$table = blended_get_items_table($blended,$cm,$items,true,false);	
   	echo html_writer::table($table);
   	}
   		  
    echo "<BR><BR><center>";
    echo $OUTPUT->help_icon('pagehelp','blended');
    echo "</center>";
    
// Finish the page
   echo $OUTPUT->footer();
?>
