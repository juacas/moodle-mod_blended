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
*********************************************************************************/

    require_once("../../config.php");
    require_once("lib.php");
	require_once('recognitionprocess.php');
	

// Get the params ---------------------------------------------------------------- 
	global $DB, $PAGE, $OUTPUT;
    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a',  0, PARAM_INT); // Blended ID

    if ($id) {
        if (! $cm = get_coursemodule_from_id('blended', $id)){
            error("Course Module ID was incorrect");
        }
    
        if (! $course =get_course($cm->course)) {
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
        if (! $user =  $DB->get_record('user', array ( 'id' => $USER->id))) {
            error("No such user in this course");
        }
    } 

    // Log ---------------------------------------------------------------------------

    add_to_log($course->id, "blended", "correction", "correction.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:viewscannedjobs', $context);
    
    // show headings and menus of page
    $url =  new moodle_url('/mod/blended/correction.php',array('id'=>$id,'a'=>$a));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    // $PAGE->set_context($context_module);
    $PAGE->set_heading($course->fullname);
    //$PAGE->set_pagelayout('standard');

// Get the strings --------------------------------------------------------------- 

    $strcorrectionpage    = get_string('correction','blended');
    $strtable				= get_string("table","blended");
    
// Print the page header ---------------------------------------------------------
     
//     $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
//                                          array('name' => $strcorrectionpage,'link'=>null, 'type'=>'misc')));
                                         
//     print_header("$course->shortname: $blended->name: $strcorrectionpage", "$course->shortname",$navigation, 
//                   "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strcorrectionpage), 
//                   navmenu($course, $cm));
    echo $OUTPUT->header();
    
    echo $OUTPUT->spacer(array('height'=>20));
echo $OUTPUT->box(format_text($strtable), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));


// Print the main part of the page ----------------------------------   
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string(get_string('correction', 'blended')));
    echo $OUTPUT->box(format_text(get_string('selectquiz', 'blended')), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
    $currentpage="correction.php";
    /**
     * Show a list of quizzes
     */
   
    show_quizzes($currentpage, $context, $blended);
    
    /**
     * Show a list of Scans
     */
    echo $OUTPUT->box(format_text(get_string('correctionpagedesc', 'blended')), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
$arewaiting= show_scan_jobs($currentpage,$context,$blended);
if ($arewaiting==true)
{
	echo '<script type="text/JavaScript">setTimeout("location.reload(true);", 1000*60);</script>';
}

	echo "<center>";
//     helpbutton($page='correction', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
	echo $OUTPUT->help_icon('pagehelp','blended');
	echo "</center>";

    echo $OUTPUT->footer();
  
?>