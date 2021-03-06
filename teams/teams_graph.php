<?php

/* 
 * Copyright (C) 2015 juacas
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

    require_once("../../../config.php");
    require_once("$CFG->dirroot/mod/assign/lib.php");
    require_once("locallib.php");
    require_once($CFG->libdir.'/gradelib.php');
    require_once ($CFG->dirroot.'/grade/lib.php');
    
    
    

// Get the params ----------------------------------------------------------------
    global $DB, $PAGE, $OUTPUT;
    $id    = required_param('id', PARAM_INT); // blended Course Module ID
   
   
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
     // show headings and menus of page
    $url =  new moodle_url('/mod/blended/teams/teams_graph.php',array('id'=>$id));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
    $PAGE->set_pagelayout('standard');
    
    $PAGE->navbar->add('graphs');

    $PAGE->requires->css(new moodle_url('/mod/blended/teams/teams_graph_flare.css'));
    $PAGE->requires->js(new moodle_url('/mod/blended/script/d3.min.js'));
    $PAGE->requires->js(new moodle_url('/mod/blended/script/teams_graph_flare.js'));
    $PAGE->requires->js(new moodle_url('/mod/blended/script/packages.js'));
    echo $OUTPUT->header();
    
echo $OUTPUT->container('','',"teams_graph");
echo '<script>var blendedid='.$id.';</script>';
echo $OUTPUT->footer();
?>

