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
 
    
    
//    list($students,$nonstudents,$active,$users)=  blended_get_users_by_type($context_course);
    $relations=array();
    $groups = groups_get_all_groups($course->id);
    foreach ($groups as $group){
        $members = groups_get_members($group->id);
        foreach ($members as $member){
            if (isset($relations[$member->id])){
                $member_entry =$relations[$member->id];
            }else{
                $member_entry = array();
                $member_entry["teamedwith"]=array();
                $member_entry["teamedwithnames"]=array();
                $member_entry["name"] = fullname($member);
            }
            
            $member_entry["teamedwith"]+=$members;
            unset($member_entry["teamedwith"][$member->id]);
            foreach ($member_entry["teamedwith"] as $user){
//                        $userpic = $OUTPUT->user_picture($user);
//                        $profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&course=' . $course->id . '">' . fullname($user, true) . '</a>';

                $member_entry["teamedwithnames"][]= fullname($user);
            }
            $relations[$member->id]=$member_entry;
        }
    }
    // clean data
    $cleaned = array();
    foreach ($relations as $rel){
        unset($rel["teamedwith"]);
        $cleaned[]=$rel;
    }
 $jsonvar= json_encode($cleaned);
 header("Content-Type: application/json");
 echo $jsonvar;