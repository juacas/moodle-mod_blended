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

    require("php-barcode.php");
    require_once("../../config.php");
    require_once("lib.php");

// Get the params -------------------------------------------------- 

    $id     = optional_param('id',         0, PARAM_INT);
    $a      = optional_param('a',          0, PARAM_INT);
    $action = optional_param('action','sheet',PARAM_ALPHA);  
    $code   = required_param('code',          PARAM_RAW);   
    $scale  = optional_param('scale',       2,PARAM_INT);

    if ($id) {
        if (! $cm = get_coursemodule_from_id('blended', $id)){
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $blended = get_record("blended", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
        if (! $user = get_record("user", "id", $USER->id) ) {
            error("No such user in this course");
        }
    } else {
        if (! $blended = get_record("blended", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $blended->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        if (! $user = get_record("user", "id", $USER->id) ) {
            error("No such user in this course");
        }
    }   
    //if (! $user_info_data = get_record("user_info_data", "userid", $USER->id ) ) {
    //}

// Log --------------------------------------------------------------
    
    add_to_log($course->id, "blended", "printlabels", "printlabels.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
    
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printlabels', $context);

// Print the labels --------------------------------------------

    // Codigo EAN basado en DNI pero el estudiante no ha introducido su DNI
    if($code==-1 || $code==-2){
  
        // Print the page header --------------------------------------------   
  
        $strlabelspage  = get_string('labelspage', 'blended');
        $strprintlabels = get_string('printlabels', 'blended');
        
        $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                         array('name' => $strlabelspage,'link'=>"../../mod/blended/labels.php?a=$blended->id", 'type'=>'misc'),
                                         array('name' => $strprintlabels,'link'=>null, 'type'=>'misc')));
        print_header("$course->shortname: $blended->name: $strlabelspage: $strprintlabels", "$course->shortname",
                      $navigation, 
                      "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strlabelspage,$strprintlabels), 
                      navmenu($course, $cm));

        // Print the main part of the page ----------------------------------

        print_spacer(30);
        
        $url="labels.php?a=$blended->id";
        if($code == -1){
            notice(get_string("cantprintlabel","blended"), $url);
        }
        else if($code == -2){
            notice(get_string("cantprintlabel2","blended"), $url);
        }
        print_spacer(20);
    }

    // ACTION BARCODE imprime una etiqueta
    
    if($action=='barcode')
    {
        barcode_print($code,"ean",$scale,"png");
    }


    // ACTION SHEET imprime hoja de etiquetas

    else if ($action=='sheet'){
        $numrows    = required_param('numrows', PARAM_INT);  
        $numcolumns = required_param('numcolumns', PARAM_INT);  

        echo '<table align="left">';
        for ($r = 0; $r < $numrows; $r++) {
            echo "<tr>"; 
            for ($c = 0; $c < $numcolumns; $c++) {
                echo "<td><img src=\"printlabels.php?a=$blended->id&action=barcode&scale=2&code=$code\" ></td>";
            }
            echo "</tr>";     
        }
        echo '</table>'; 
    }
?>
