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

* @author J�ssica Olano Lopez,Pablo Galan Sabugo, David FernÃ¡ndez, Natalia Haro, Juan Pablo de Castro and other contributors.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
*********************************************************************************/

    require_once("../../config.php");
    require_once("lib.php");
    require_once("blended_locallib.php");
    require_once("teams/locallib.php");
    require_once("alertslib.php");

// Get the params --------------------------------------------------
    global $DB, $PAGE, $OUTPUT;
    $id = required_param('id', PARAM_INT); // Course Module ID, or

    $cm = get_coursemodule_from_id('blended', $id,null,null,MUST_EXIST);
    require_login($cm->course, false, $cm);
    $course=get_course($cm->course);
     
    if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            print_error("Course module is incorrect");
        }
        $user=$USER;

// Capabilities -----------------------------------------------------
    $context_module = context_module::instance($cm->id);
    require_capability('mod/blended:blended', $context_module);
    
   
    // Log --------------------------------------------------------------
    $info='';
    $url="view.php?id=$cm->id";
    if ($CFG->version >= 2014051200) {
        require_once 'classes/event/blended_viewed.php';
        \mod_blended\event\blended_viewed::create_from_parts($course->id, $user->id, $blended->id,$url, $info)->trigger();
    } else {
        add_to_log($course->id, "blended", "view", $url, "$blended->id");
    }

    
    // show headings and menus of page
    $url =  new moodle_url('/mod/blended/view.php',array('id'=>$id));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
   // $PAGE->set_context($context_module);
    $PAGE->set_heading($course->fullname);
// Print the page header --------------------------------------------    

    echo $OUTPUT->header();
// Print the main part of the page ---------------------------------- 
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($blended->name).$OUTPUT->help_icon('mainpage','blended'));

    echo $OUTPUT->box(format_text($blended->intro, FORMAT_MOODLE), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
    
// Print the links -------------------------------------------------- 

// Comprobamos que hay estudiantes matriculados en el curso. 
// En caso contrario el profesor no puede acceder a los distintos vinculos.  
// Obtenemos el contexto del curso
    $context_course = context_course::instance($cm->course);
   
    $alert = alert_messages($blended,$course,$context_course);
    
    $no_students_in_course_alert = $alert->no_students_in_course_alert;
    $none_is_active_alert = $alert->none_is_active_alert;
    $student_not_active_alert    = $alert->student_not_active_alert;
    $iamstudent            = $alert->iamstudent;
    $no_DNI_alert          = $alert->no_DNI_alert ;
    $no_userinfodata_alert = $alert->no_userinfodata_alert;
// Get the strings -------------------------------------------------
$strlabelspage = get_string("labelspage", "blended");
$strblendedquizzes = get_string("blendedquizzes", "blended");
$strmanagement = get_string("management", "blended");
$strstudent = get_string("studentOptions", "blended");
$strassignmentpage = get_string("assignmentpage", "blended");
$strgradepage = get_string("gradepage", "blended");
$strteamsmanagementpage = get_string("teamsmanagementpage", "blended");
$strsignupteampage = get_string("signupteampage", "blended");
$strnostudentsincourse = get_string("nostudentsincourse", "blended");
$strnoidnumber = get_string("noidnumberview", "blended");
$strnouserinfodata = get_string("nouserinfodataview", "blended");
$strnoneisactive = get_string('noneisactive', 'blended');
$strstudentisnotactive = get_string('studentisnotactive', 'blended');
$strgeneratepaperquiz = get_string("generatepaperquiz", "blended");
$strscan = get_string("scan", "blended");
$strcorrection = get_string("correction", "blended");
$strrevision = get_string("revision", "blended");
$strheader1 = get_string("header1", "blended");
$strheader2 = get_string("header2", "blended");

   /* 
    // Comprobamos que al menos hay un estudiante matriculado en el curso 
    if($students = get_role_users($studentroleid, $context_course, false, 'u.id, u.lastname, u.firstname')){
        $no_students_in_course_alert=false;
                
        // Comprobamos que al menos hay un estudiante activo (ha entrado al menos una vez)
        // en el curso. Nos devuelve array con todos los estudiantes        
        if($userids = blended_get_course_students_ids ($course, null, true)){
            // Se desactiva la alerta de que ningÃºn estudiante estÃ¡ activo si el array tiene
            // al menos un elemento
            $none_is_active_alert = false;
        }
    }
    
   */
    //generate stickers
	$icons1 = "<img src=\"images/codebarSticker.png\" align=\"left\" height=\"100\"/>";
	// generate form
	$icons2 = "<img src=\"images/form_icon.jpg\" align=\"left\" height=\"100\"/>";
	// team management
	$icons4 = "<img src=\"images/group.jpg\" align=\"left\" height=\"100\"/>";
	// generate pdfs
	$icons5 = "<img src=\"images/pdficon.jpg\" align=\"left\" height=\"100\"/><img src=\"images/rightArrow.png\" align=\"left\" height=\"50\"/><img src=\"images/doExam.jpg\" align=\"left\" height=\"100\"/>";
  
	//Link a iniciar procesado de escaneo
	$icons6 = "<img src=\"images/scanner.jpg\" align=\"left\" height=\"100\"/><img src=\"images/rightArrow.png\" align=\"left\" height=\"50\"/><img src=\"images/uploadphotos.jpg\" align=\"left\" height=\"100\"/>";
   //Link a supervisar estado de la correcciÃ³n
	$icons7 = "<img src=\"images/examicons.jpg\" align=\"left\" height=\"100\"/><img src=\"images/rightArrow.png\" align=\"left\" height=\"50\"/><img src=\"images/grades.jpg\" align=\"left\" height=\"100\"/>";
//Link a revisiÃ³n de los test
	$icons8 = "<img src=\"images/examicon2.png\" align=\"left\" height=\"100\"/>";	
	
//  include ('quiz/view.php');7
 
// Teams management section 
    include ('teams/view.php');

    echo "<center>";
    //$OUTPUT->help_icon($page='view', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
    echo "</center>";

// Finish the page
    echo $OUTPUT->footer();
//     <script>
//     function confirma (url) {
//     	if (confirm("<?php print_string("edituserinfo", "blended") ? >")) location.replace(url);
//     }
//     function confirma2 (url) {
//     	if (confirm("<?php print_string("edituserinfo2", "blended") ? >")) location.replace(url);
//     }
//     </script>
?>
