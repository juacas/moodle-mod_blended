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

    $id = required_param('id', PARAM_INT);   // course
global $DB;
    if (! $course = $DB->get_record("course", "id", $id))
    {
        error("Course ID is incorrect");
    }

    require_login($cm->course, false,$cm);

    add_to_log($course->id, "blended", "view all", "index.php?id=$course->id", "");


/// Get all required strings

    $strblendeds = get_string("modulenameplural", "blended");
    $strblended  = get_string("modulename", "blended");


/// Print the header

    if ($course->category) {
            $navigation = build_navigation(array(array('name' => $course->shortname,'link'=>"../../course/view.php?id=$course->id", 'type'=>'misc')));
    } else {
            $navigation = build_navigation(array(array('name' => $course->shortname,'link'=>null, 'type'=>'misc')));
    }

// Print the page header ---------------------------------------------------------
               
    print_header("$course->shortname: $strblendeds", "$course->fullname", $navigation, "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $blendeds = get_all_instances_in_course("blended", $course)) {
        notice("There are no blendeds", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances 

///
/// (your module will probably extend this)
///
    $timenow = time();
    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("left", "left", "left");
    }

    foreach ($blendeds as $blended) {
        if (!$blended->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$blended->coursemodule\">$blended->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$blended->coursemodule\">$blended->name</a>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($blended->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>
