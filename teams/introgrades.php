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

 * @author J�ssica Olano L�pez,Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * ******************************************************************************* */
require_once ("../../../config.php");
require_once ("../lib.php");
require_once ("locallib.php");

// Get the params --------------------------------------------------
global $DB, $PAGE, $OUTPUT;

$id = required_param('id', PARAM_INT); // Course Module ID, or
$teamsfromassignmentid = optional_param('teamsfromassignment', 0, PARAM_INT);
$item_id = required_param('itemid', PARAM_INT);


if (!$cm = get_coursemodule_from_id('blended', $id)) {
    print_error("Course Module ID was incorrect");
}

if (!$course = get_course($cm->course)) {
    print_error("Course is misconfigured");
}

if (!$blended = $DB->get_record('blended', array('id' => $cm->instance))) {
    print_error("Course module is incorrect");
}
if (!$context = context_course::instance($course->id)) {
    print_error("Context ID is incorrect");
}

// Log --------------------------------------------------------------
//add_to_log ( $course->id, "blended", "introgrades", "introgrades.php?a=$blended->id", "$blended->id" );
// Capabilities -----------------------------------------------------
require_login($cm->course, false, $cm);


$items = blended_get_available_items($blended);
if (count($items) == 0) {
    print_error("No calificable items in this course");
}

$context = context_module::instance($cm->id);
require_capability('mod/blended:introgrades', $context);

$url = new moodle_url('/mod/blended/teams/introgrades.php', array(
    'id' => $id,
    'itemid' => $item_id
        ));

blended_include_autocomplete_support($context, $blended);

$PAGE->set_url($url);
$PAGE->set_title(format_string($blended->name));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('standard');



$item = blended_get_item($item_id);
$assignmentname = blended_get_item_name($item);
$existingteams = false;
$existinglinkedteams = false;
//Obtenci�n de los miembros de esa actividad
//$members = blended_get_teams_members ( null, $item_id, null, $blended ); // unused
// Get the strings --------------------------------------------------
$strgradepage = get_string("gradepage", "blended");
$strintrogradepage = get_string('introgradepage', 'blended');

$strsendgrades = get_string("sendgrades", "blended");
$strteamsfromassignment = get_string('teamsfromassignment', 'blended');

$strexistinglinkedteams = get_string('existinglinkedteams', 'blended', $assignmentname);
$strexistingteams = get_string('existingteams', 'blended', $assignmentname);


// Print the page header ---------------------------------------------
$link = "grades.php?id=" . $id;
$PAGE->navbar->add($strgradepage, $link);
$PAGE->navbar->add($strintrogradepage);

echo $OUTPUT->header();

// Print the main part of the page ----------------------------------
echo $OUTPUT->heading(format_string($strintrogradepage).$OUTPUT->help_icon('introgrades', 'blended'));
$module_link = blended_get_item_html_title($item);
$grouping = blended_get_grouping($item, $blended);

$subheading = get_string('gradeassignments','blended') . $module_link;
if ($grouping){
$subheading.=" ". get_string('teams_from', 'blended')." ".$OUTPUT->action_link(new moodle_url('/group/overview.php', array('id' => $course->id)), $grouping->name);
}
echo $subheading;
echo "<br/>";
echo $OUTPUT->spacer(array('height' => 20));

//Elegir AGRUPAMIENTO existente----------------------------
//Compruebo si existe algun agrupamiento:
//if ($groupings = groups_get_all_groupings($course->id)) {
//    $selected_groupingid = blended_get_groupingid($item);
//
//    //Se muestran los agrupamientos existentes mediante un select:		
//    $url1 = "createTeams.php";
//    echo "<form name=\"f1\" action=\"$url1\" method=\"GET\">";
//    echo "<select name=\"groupingid\">";
//    foreach ($groupings as $i => $grouping_option) {
//        $selected = $i == $selected_groupingid ? 'selected="selected"' : '';
//        echo "<option value=\"$i\" $selected >$grouping_option->name</option>";
//    }
//    echo "</select>";
//    echo "<input type=\"submit\" value=\"" . get_string('select_grouping', 'blended') . "\">";
//    echo "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">";
//    echo "<input type=\"hidden\" name=\"itemid\" value=\"$item_id\">";
//    echo "<input type=\"hidden\" name=\"action\" value=\"select_grouping\">";
//    echo "</form>";
//}

echo'</br>';


echo $OUTPUT->spacer(array('height' => 30));
if ($grouping){
// Formulario GRADESFORM
$url1 = "saveteamsgrades.php?id=" . $cm->id . "&itemid=" . $item_id;
//action=\"$url1\"
echo "<form method=\"post\" action=\"$url1\" name=\"teamsform\" id =\"teamsform\" >";
$table = blended_generate_groups_table($item, $blended);
echo \html_writer::table($table);
echo $OUTPUT->spacer(array('height' => 30));
echo '<label for="deleteEmpty">' ;
echo '<input type="checkbox" name="deleteEmpty" value="true"/>'.get_string('deleteemptyteams', 'blended').'</label>';
echo '<table align="center">';
echo "<tr><td><input type=\"submit\" value=\"" . $strsendgrades . "\" id=\"update\" /></td>";
echo '</table>';
echo "<input type=\"hidden\" name=\"groupingid\"        id=\"groupingid\"         value=\"$grouping->id\">";
echo "<input type=\"hidden\" name=\"itemid\"  id=\"assignment\"   value=\"$item_id\">";
echo "<input type=\"hidden\" name=\"id\"            id=\"id\"             value=\"$cm->id\">";

//Fin del formulario
echo "</form>";
}else{
    echo $OUTPUT->notification(get_string('groupingnotselected','blended'));
}
// Finish the page -------------------------------------------------

?>
<script type="text/javascript">
<!--
    function searchTeam(field, length, numTeams, numMembers) {

        if (field.value.length == length) {
            var t;
            var m;

            for (t = 0; t < numTeams; t++) {
                gradename = "team_" + t + "_grade";
                for (m = 0; m < numMembers; m++) {
                    membername = "team_" + t + "_member_" + m;
                    if (document.getElementById(membername).value == field.value) {
                        document.getElementById(gradename).focus();
                    }
                }//Fin for members
            }//Fin for teams 

        }
    }

    function setGradeSelect(textfield, select, gradelength, nextfield, grademax) {
        if (textfield.value <= grademax) {
            select.value = parseInt(textfield.value, 10);
        }
        else {
            textfield.value = "";
            select.value = -1;
        }

        if (nextfield != 'end' && textfield.value.length == gradelength) {
            document.getElementById(nextfield).focus();
        }
    }

    function setGradeTextField(select, textfield) {
        if (select.value != -1) {
            textfield.value = select.value;
        }
        else {
            textfield.value = "";
        }
    }

    function jumpcursor(field, nextfield) {
        if (field.value.length == <?php echo $blended->lengthuserinfo; ?>) {
            document.getElementById(nextfield).focus();
        }
    }

    function checkData(numTotalTeams, numTeams, numMembers, url) {
        var deleteteam;
        var rewriteteam = false;
        var confirm_rewrite = false;
        var idteam;
        var teamname;
        var teamnamevalue;
        var idgrade;
        var gradename;
        var gradelistname;
        var membername;
        var t;
        var m;
        var updateTeams = 0;
        var found = 0;

        if (numTotalTeams > numTeams) {
            updateTeams = numTotalTeams - numTeams;
        }

        for (t = 1; t <= numTotalTeams; t++) {

            deleteteam = "delete_team_" + t;
            rewriteteam = "rewrite_team_" + t;
            idteam = "team_" + t + "_id";
            teamname = "team_" + t + "_name";
            idgrade = "team_" + t + "_gradeid";
            gradename = "team_" + t + "_grade";
            gradelistname = "team_" + t + "_gradelist";

            rewriteteam = document.getElementById(rewriteteam) ? document.getElementById(rewriteteam) : false;
            if (rewriteteam != false) {
                if (rewriteteam.checked == true) {
                    confirm_rewrite = true;
                }
            }

            if ((t > updateTeams) && (updateTeams != 0)) {
                if (document.getElementById(gradelistname).options[document.getElementById(gradelistname).selectedIndex].value == -1) {
                    document.getElementById(gradelistname).disabled = "true";
                }
            }

            for (m = 1; m <= numMembers; m++) {
                membername = "team_" + t + "_member_" + m;

                if (document.getElementById(membername).value == "") {
                    found++;
                }
            }//Fin for members

            if (found == numMembers) {

                document.getElementById(teamname).disabled = "true";
                document.getElementById(gradename).disabled = "true";
                document.getElementById(gradelistname).disabled = "true";

                for (m = 1; m <= numMembers; m++) {
                    membername = "team_" + t + "_member_" + m;
                    document.getElementById(membername).disabled = "true";
                }

                if (document.getElementById(deleteteam)) {

                    teamnamevalue = document.getElementById(teamname).value;

                    document.getElementById(deleteteam).disabled = !document.getElementById(deleteteam).disabled;
                    document.getElementById(deleteteam).checked = "true";
                    if (!confirm("¿Desea eliminar el equipo " + teamnamevalue + "?")) {
                        document.getElementById(deleteteam).disabled = !document.getElementById(deleteteam).disabled;
                        document.getElementById(deleteteam).checked = !document.getElementById(deleteteam).checked;
                    }
                }

            }//Fin if
            found = 0;
        }//Fin for teams

        if (confirm_rewrite) {
            if (confirm("<?php print_string("confirmrewritegrades", "blended") ?>")) {
                document.gradesform.action = url;
                document.gradesform.submit()
            }
            else {
                alert("<?php print_string("checkbox", "blended") ?>");
                return;
            }
        }

        return true;
        //document.gradesform.action=url; 
        //document.gradesform.submit()
    }

// END -->  

</script>
<?php
echo $OUTPUT->footer();
?>
