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
require_once ("../../../config.php");
require_once ("../lib.php");
require_once("locallib.php");
require_once('select_activity_form.php');
require_once ($CFG->libdir . '/gradelib.php');
require_once ($CFG->dirroot . '/grade/lib.php');

// Get the params ----------------------------------------------------------------
	global $DB, $PAGE, $OUTPUT;

	$id = required_param ( 'id',  PARAM_INT ); // Course Module ID, or

	
        if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
                print_error ( "Course Module ID was incorrect" );
        }

        if (! $course = get_course($cm->course)) {
                print_error ( "Course is misconfigured" );
        }

        if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance ) )) {
                print_error ( "Course module is incorrect" );
        }
	
// Log ---------------------------------------------------------------------------
//	add_to_log ( $course->id, "blended", "selectassignment", "selectassignment.php?a=$blended->id", "$blended->id" );

// Capabilities ------------------------------------------------------------------
	require_login($cm->course, false,$cm);
	
	//require_login ( $course->id );

	$context_course = context_course::instance( $cm->course );
	if (! get_role_users ( 5, $context_course, false, 'u.id, u.lastname, u.firstname' )) {
		print_error ( "No students in this course" );
	}

	$context = context_module::instance( $cm->id );
	require_capability ( 'mod/blended:signupteam', $context );

	// show headings and menus of page
	$url = new moodle_url ( '/mod/blended/selectassignment.php', array (
		'id' => $id,
	) );
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );

// Get the strings ---------------------------------------------------------------

	$strselectassignpage = get_string ( 'selectassignpage', 'blended' );
	$strnone = get_string ( 'noselected', 'blended' );

// Print the page header ---------------------------------------------------------
	$PAGE->navbar->add('Inscribirse en equipo');
	
	echo $OUTPUT->header ();

// Print the main part of the page -----------------------------------------------

	echo $OUTPUT->spacer ( array ('height' => 20 ) );
	echo $OUTPUT->heading ( format_string ( $strselectassignpage ).$OUTPUT->help_icon ( 'pagehelp', 'blended' ) );
	echo $OUTPUT->spacer ( array ('height' => 20 ) );

// Imprimimos el formulario por pantalla -----------------------------------------

	//Obtenci�n de una lista desplegable con las actividades calificables
	
	$items = blended_get_available_items($blended);

	if (empty ( $items )) {
		echo $OUTPUT->box(get_string('noassignments','blended'));
		$continue = "../../course/view.php?id=$course->id";
		print_continue ( $continue );	
	} else{
		$form = new activities_form();
		if ($form->is_cancelled ()) {
			redirect ( 'selectassignment.php' );
		}
		if ($data = $form->get_data ()) {
			redirect ( 'selectassignment.php' );
		}
		else {
		
    		
            // Obtenemos las referencias a toda la informaci�n sobre los modulos dentro del curso
//    		$modinfo = get_fast_modinfo ( $course->id );
               

    		//Etiqueta del la lista desplegable
               echo $OUTPUT->box(format_text(get_string('assignments','blended'), FORMAT_MOODLE), 'generalbox', 'intro');
                $strsignupteam = get_string ( 'signupteam', 'blended' );
    		//Form SIGNUPTEAM
    		$url1= new moodle_url( "signupteam.php");
    		echo "<form name=\"signupteam\" method=\"get\" action=\"$url1\">";
    			echo"<select name=\"itemid\">";
    				//echo "<option value=\"0\" selected=\"selected\">$strnone</option>";
    				foreach ( $items as $r => $item ) {
                                    $itemtypename = blended_get_item_name($item);
                                    echo"<option value=\"$item->id\">$itemtypename</option>"; 			
    				}  	
    			echo"</select>";
                        echo "<input type=\"hidden\" name=\"id\" value=\"$id\" />";
    			//Botón INSCRIBIRSE
//    				echo"<input type=\"submit\" name=\"Submit\" value=\"Inscribirse\" onsubmit=\"checkform($url1)\"  />";
    				echo"<input type=\"submit\"  onsubmit=\"checkform($url1)\" value=\"$strsignupteam\" />";
    		//Fin del formulario
    		echo'</form>';
		}//Fin if-else
		echo "</center>";	
	}//Fin if-else
	// Finish the page
	echo $OUTPUT->footer ();
?>

<script type="text/javascript">
function checkform(url) {
	valid =document.forms['signupteam'].activity.value!='';
        return valid;
}
</script>