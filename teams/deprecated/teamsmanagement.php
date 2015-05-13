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
    require_once("../lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/gradelib.php');
    require_once ($CFG->dirroot.'/grade/lib.php');
    
// Get the params ----------------------------------------------------------------
    global $DB, $PAGE, $OUTPUT,$USER;
    
    $id = required_param('id', PARAM_INT);   // cmid
    $groupingid= optional_param('groupingid',0,PARAM_INT);//id del agrupamiento
    
    	if (! $cm = get_coursemodule_from_id('blended', $id)){
    		error("Course Module ID was incorrect");
    	}
        
    	if (! $course = get_course($cm->course)) {
    		error("Course is misconfigured");
    	}
    
    	if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
    		error("Course module is incorrect");
    	}
    	$user=$USER; 
    
// Log ---------------------------------------------------------------------------
//   add_to_log($course->id, "blended", "teamsmanagement", "teamsmanagement.php?a=$blended->id", "$blended->id");
    
// Capabilities ------------------------------------------------------------------    
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:introteams', $context);
       
// show headings and menus of page------------------------------------------------

    $url =  new moodle_url('/mod/blended/teams/teamsmanagement.php',array('id'=>$id,'groupingid'=>$groupingid));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
    $PAGE->set_pagelayout('standard');

// Get the strings ------------------------------------------------- 
   
    $strname                    = get_string("name","blended");
    $strduedate                 = get_string("duedate", "blended");
    $strnumteams                = get_string("teams", "blended");
    $strgraded                  = get_string("graded", "blended");
    $strcreateteams             = get_string("createteams", "blended");
    $strcreateteams2            = get_string("createteams2", "blended");
    $strno                      = get_string("no", "blended");
    $stryes                     = get_string("yes", "blended");
    $strpartially               = get_string("partially", "blended");
    $strteamsmanagementpage     = get_string("teamsmanagementpage","blended");
    $strteamsmanagementpagedesc = get_string("teamsmanagementpagedesc","blended");
    
    
    // Print the page header ---------------------------------------------------------
   
    $PAGE->navbar->add($strteamsmanagementpage);
    echo $OUTPUT->header();
    
    // Print the main part of the page -----------------------------------------------
    
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($strteamsmanagementpage));
    echo'<center>';
    echo $OUTPUT->box(format_text($strteamsmanagementpagedesc), 'generalbox', 'intro');
    echo'</center>';
    echo $OUTPUT->spacer(array('height'=>20));

// Imprimimos el formulario por pantalla -----------------------------------------

	//Lista de las actividades creadas en el modulo que puedan poseer calificaci�n
    $items = blended_get_available_items($blended);
    //No hay ninguna actividad
    if(empty($items)){
    	echo "<center>";
    	echo ('No hay actividades calificables creadas para este curso');
    	echo "</center>";
    	$continue =  "../../course/view.php?id=$course->id";
    	print_continue($continue);
    }
    //Lista de actividades calificables
    else{
    	//Table
    
    	$timenow = time();
    	$currentsection = "";
    	
        $table = blended_get_items_table($blended,$cm, $items,true,true);
   	
        echo html_writer::table($table);
   }
   
    echo "<BR><BR><center>";
    echo $OUTPUT->help_icon('pagehelp','blended');
    echo "</center>";
    
// Finish the page
   echo $OUTPUT->footer();
?>