<?php

/* * *******************************************************************************
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

 * @author J�ssica Olano L�pez,Pablo Galan Sabugo, David Fern�ndez, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * ******************************************************************************** */
require_once ("../../../config.php");
require_once ("../lib.php");
require_once ("locallib.php");
require_once ($CFG->dirroot . '/group/lib.php');
require_once ("grouping_form.php");
require_once ("$CFG->libdir/formslib.php");
require_once ($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/grade/grade_item.php');


//Get the params--------------------------------------------------------
$id = required_param('id', PARAM_INT); // Blended C Module ID, or
$nummembers = optional_param('nummembers', null, PARAM_INT);
$numteams = optional_param('numteams', null, PARAM_INT);
$itemid = required_param('itemid', PARAM_INT);
$delete_empty = optional_param('deleteEmpty', false, PARAM_BOOL);

if (!$cm = get_coursemodule_from_id('blended', $id)) {
    print_error("Course Module ID was incorrect");
}
if (!$course = get_course($cm->course)) {
    print_error("Course is misconfigured");
}
if (!$blended = $DB->get_record('blended', array('id' => $cm->instance))) {
    print_error("Course module is incorrect");
}

// Log ---------------------------------------------------------------------------
// Añade una entrada a la tabla de logs (registros). Estas son
// acciones m�s concretas que las noticias del servidor web, y
// proporcionan una forma sencilla de reconstruir qu� ha estado
// haciendo un usuario en particular.
//add_to_log ( $course->id, "blended", "savetemasgrades", "saveteamsgrades.php?a=$blended->id", "$blended->id" );
// Capabilities ------------------------------------------------------------------
// Esta funci�n comprueba que el usuario actual ha introducido el
// login en la plataforma y que tiene los privilegios requeridos.
// Si no han introducido el login los usuarios ser�n rediccionados
// a la p�gina donde puedan hacerlo, a no ser que $autologinguest
// est� fijado como true en cuyo caso el usuario entrar� en la
// plataforma como invitado. Si el usuario no est� dado de alta en
// el curso se le redirige a la p�gina del curso para darse de alta.
require_login($course->id);

$context = context_module::instance($cm->id);
$context_course = context_course::instance($cm->course);
require_capability('mod/blended:introgrades', $context);
list($studentids, $nonstudentids, $activeids, $users) = blended_get_users_by_type($context_course);
if (count($studentids) == 0) {
    print_error('errornostudents', 'blended');
}

// show headings and menus of page
$url = new moodle_url('/mod/blended/teams/saveteamsgrades.php', array(
    'id' => $id, 'itemid' => $itemid
        ));

//HEADER----
$PAGE->set_url($url);
$PAGE->set_title(format_string($blended->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');

$item = blended_get_item($itemid);
$groupingid = blended_get_groupingid($item);
// Print the page header ---------------------------------------------------------

$teams = blended_get_teams_from_form($item);
$teams = blended_get_grades_from_form($teams);

//Actualizaci�n del agrupamiento
$outputmessages = blended_actualizar_agrupamiento($teams, $item, $blended, $delete_empty);

if (!empty($array_return)) {
    $strinserted = blended_get_error_alert($array_return, "insert");
}

//Para cada equipo:
foreach ($teams as $team) {
    //Si se ha introducido calificaci�n:
    if (!empty($team->grade)) {
        $current_team = blended_get_team($team->id);
        if (!$current_team) {
            continue;
        }
        $rawgrade = $team->grade->grade == -1 ? null : $team->grade->grade;
        $newfinalgrade = (!isset($team->grade->grade) || $team->grade->grade == -1) ? null : $item->adjust_raw_grade($team->grade->grade, $item->grademin, $item->grademax);
//        $outputmessages.="<p>New finalgrade $newfinalgrade for team $current_team->name</p>";
        blended_grade_team($item, $current_team, $newfinalgrade);
        foreach ($team->members as $memberid) {
//                                if ($team->grade->rewrite)
            //Introduzco de nuevo las calificaciones
            $grade_prev = $item->get_grade($memberid);
// $outputmessages.="<p>User $memberid had grade $grade_prev->finalgrade</p>";
             if (!isset($grade_prev->finalgrade) && !isset($newfinalgrade)) { // skip students with no changes
                continue;
            }
            if (isset($grade_prev->finalgrade) && $grade_prev->finalgrade == $newfinalgrade) { // skip students with no changes
                continue;
            }

            blended_grade_student($memberid, $item, $rawgrade, $newfinalgrade);
            // Log ---------------------------------------------------------------------------
// Añade una entrada a la tabla de logs (registros). Estas son
// acciones m�s concretas que las noticias del servidor web, y
// proporcionan una forma sencilla de reconstruir qu� ha estado
// haciendo un usuario en particular.
            $info = '';
            $url = "introgrades.php?id=$id";
            if ($CFG->version >= 2014051200) {
                require_once '../classes/event/teams_updated.php';
                \mod_blended\event\teams_updated::create_from_parts($item->courseid, $USER->id, $blended->id, $itemid, $url, $info)->trigger();
            } else {
                add_to_log($item->courseid, "blended", "updateGrades", $url, "Blended: $blended->id, Item: $itemid");
            }
            $grade_new = $item->get_grade($memberid);
            $user = $DB->get_record('user', array('id' => $memberid));
            $a = new stdClass();
            $a->userlink = $OUTPUT->user_picture($user) . fullname($user);
            $a->prev_grade = grade_format_gradevalue($grade_prev->finalgrade, $item);
            $a->new_grade = grade_format_gradevalue($grade_new->finalgrade, $item);
            $msg = get_string('user_regraded', 'blended', $a);
            $outputmessages.= $OUTPUT->box($msg);
        }
    }
}
if ($outputmessages != '') {
    echo $OUTPUT->header();

    // Print the main part of the page -----------------------------------------------

    echo $OUTPUT->spacer(array('height' => 20));
    echo $OUTPUT->heading(get_string('sendgrades', 'blended'));
    echo $OUTPUT->spacer(array('height' => 30));
    echo $outputmessages;
    echo $OUTPUT->continue_button(new moodle_url('introgrades.php', array('id' => $id, 'itemid' => $itemid)));
    echo $OUTPUT->footer();
} else {

    redirect(new moodle_url('introgrades.php', array('id' => $id, 'itemid' => $itemid)));
}