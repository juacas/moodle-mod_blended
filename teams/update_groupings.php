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

* @author J�ssica Olano L�pez,Pablo Galan Sabugo, David Fern�ndez, Natalia Haro, Juan Pablo de Castro and other contributors.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
**********************************************************************************/
require_once ("../../../config.php");
require_once ("../lib.php");
require_once 'locallib.php';
require_once ($CFG->dirroot . '/group/lib.php');
require_once ("grouping_form.php");
require_once ("$CFG->libdir/formslib.php");
require_once ($CFG->libdir . '/filelib.php');


//Get the params---------------------------------------------------------------

	$id = required_param ( 'id', PARAM_INT ); // Course Module ID, or

	$nummembers = optional_param('nummembers',null,PARAM_INT);
	$numteams = optional_param('numteams',null,PARAM_INT);;
	$groupingid = required_param('groupingid',PARAM_INT);
	$itemid = required_param('itemid',PARAM_INT);

		if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
			print_error ( "Course Module ID was incorrect" );
		}
		if (! $course = get_course($cm->course)) {
			print_error ( "Course is misconfigured" );
		}
		if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance) )) {
			print_error ( "Course module is incorrect" );
		}
		if (! $context = context_course::instance( $course->id )) {
			print_error ( "Context ID is incorrect" );
		}

        $item = blended_get_item( $itemid);

	// Capabilities ------------------------------------------------------------------
	
	// Esta funci�n comprueba que el usuario actual ha introducido el
	// login en la plataforma y que tiene los privilegios requeridos.
	// Si no han introducido el login los usuarios ser�n rediccionados
	// a la p�gina donde puedan hacerlo, a no ser que $autologinguest
	// est� fijado como true en cuyo caso el usuario entrar� en la
	// plataforma como invitado. Si el usuario no est� dado de alta en
	// el curso se le redirige a la p�gina del curso para darse de alta.
	require_login ( $course->id );
	
	$context_course = context_course::instance( $cm->course );
	if (! get_role_users ( 5, $context_course, false, 'u.id, u.lastname, u.firstname' )) {
		error ( get_string ( 'errornostudents', 'blended' ) );
	}
	
	
	// show headings and menus of page
	$url = new moodle_url ( '/mod/blended/update_groupings.php', array (
			'id' => $id,
	) );

	//HEADER----
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );

	// Print the page header ---------------------------------------------------------
	
	echo $OUTPUT->header ();
	
	// Print the main part of the page -----------------------------------------------
	
	echo $OUTPUT->spacer ( array ('height' => 20) );
	echo $OUTPUT->heading ( 'Actualizacion de los agrupamientos' );
	echo $OUTPUT->spacer ( array ('height' => 30) );
	
	
	//Recogida de los parametros 'teamname', 'teamid' y 'teammember'
    $team_id=array();
    $teamname=array();
    $memberid=array();
    
    // key idteam 
    $teams = blended_get_teams_from_form($numteams,$nummembers,$itemid);	
	/*Llamada a la funci�n que me actualiza el agrupamiento	
	recogiendo en el array 'contador' las veces que se repite cada miembro*/
    $outputmessages=blended_actualizar_agrupamiento ( $teams, $item,$blended);
	// Log ---------------------------------------------------------------------------
	
	// Añade una entrada a la tabla de logs (registros). Estas son
	// acciones m�s concretas que las noticias del servidor web, y
	// proporcionan una forma sencilla de reconstruir qu� ha estado
	// haciendo un usuario en particular.
	// 	add_to_log ( $course->id, "blended", "update_groupings", "update_groupings.php?a=$blended->id", "$blended->id" );
	$info='';
	$url="update_groupings.php?id=$id";
	if ($CFG->version >= 2014051200) {
	    require_once '../classes/event/teams_updated.php';
	    \mod_blended\event\teams_updated::create_from_parts($course->id, $USER->id, $blended->id, $itemid, $url, $info)->trigger();
	} else {
	    add_to_log ( $course->id, "blended", "updateTeams", $url, "Blended: $blended->id, Assignment: $itemid" );
	}
	//Form UPDATEGROUPING
	$url1 = "updateTeams.php?id=" . $id ;
	echo "<body onload=actualizar()>";
	echo "<form name='updategrouping' action ='".$url1."' method='POST' >";
		echo "<input type='hidden' name='actualizar_agr' value=1>";
		echo "<input type='hidden' name='numteams' value='$numteams'>";
		echo "<input type='hidden' name='nummembers' value='$nummembers'>";
		echo "<input type='hidden' name='itemid' value='$itemid'>";
		echo "<input type='hidden' name='groupingid' value='$groupingid'>";
		echo "<input type='hidden' name='id' value='$id'>";
	
	//Fin del formulario
	echo "</form>";
	echo "</body>";
	echo "<center>";
	print('Actualizando agrupamiento...');
	echo "</center>";
?>

<br />
<script type="text/javascript">
function actualizar(){
	document.updategrouping.submit();
}
</script>