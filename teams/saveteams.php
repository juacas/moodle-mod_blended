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
*********************************************************************************/
require_once ("../../../config.php");
require_once ("../lib.php");
require_once ("locallib.php");
require_once ($CFG->dirroot . '/group/lib.php');
require_once ("$CFG->libdir/formslib.php");

// Get the params --------------------------------------------------
	global $DB, $PAGE, $OUTPUT;
	$id = optional_param ( 'id', 0, PARAM_INT ); // Course Module ID, or
	$numteams = optional_param ( 'numteams', 0, PARAM_INT ); // numteams
	$num_teams = optional_param ( 'num_teams', 0, PARAM_INT ); // num_teams
	$nummembers = optional_param ( 'num_members', 0, PARAM_INT ); // nummembers
	$itemid = optional_param ( 'itemid', 0, PARAM_INT ); // id actividad
	$groupingid = optional_param ( 'groupingid', 0, PARAM_INT ); // id agrupamiento
	

		if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
			print_error ( "Course Module ID was incorrect" );
		}
		if (! $course = $DB->get_record ( 'course', array (
				'id' => $cm->course 
		) )) {
			print_error ( "Course is misconfigured" );
		}
		if (! $blended = $DB->get_record ( 'blended', array (
				'id' => $cm->instance 
		) )) {
			print_error ( "Course module is incorrect" );
		}
		if (! $context = context_course::instance ( $course->id )) {
			print_error ( "Context ID is incorrect" );
		}
	
	
	// Log --------------------------------------------------------------
	
//	add_to_log ( $course->id, "blended", "saveteams", "saveteams.php?id=$cm->id", "$blended->id" );
	
	// Capabilities -----------------------------------------------------
	
	require_login ( $course );
	
	$context_course = context_course::instance ( $course->id );
	if (! get_role_users ( 5, $context_course, false, 'u.id, u.lastname, u.firstname' )) {
		print_error ( "No students in this course" );
	}
	
	$context = context_module::instance ( $cm->id );
	require_capability ( 'mod/blended:introteams', $context );
	
	$url = new moodle_url ( '/mod/blended/saveteams.php', array (
			'id' => $id,
			'numteams' => $numteams,
			'num_teams' => $num_teams,
			'num_members' => $nummembers,
			'assignmentname' => $assignmentname,
			'itemid' => $itemid,
			'groupingid' => $groupingid
	));
	
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );
	
	// Get the strings -------------------------------------------------
	
	$strintroteamspage = get_string ( "introteams", "blended" );
	$strinserted = get_string ( "inserted", "blended" );
	
	// Print the page header --------------------------------------------
	
	echo $OUTPUT->header ();
	echo $OUTPUT->spacer ( array ('height' => 30 ) );
	
	// Print the main part of the page -----------------------------------------------
	
	echo $OUTPUT->spacer ( array (	'height' => 20	) );
	echo $OUTPUT->heading ( format_string ( $strintroteamspage . $assignmentname ) );
	echo $OUTPUT->spacer ( array (	'height' => 30	) );
	
	
	// store the data --------------------------------------------------
	
	$insertteams = array ();
	$insertmembers = array (array ());
	$insert_index = - 1;
	$insertteams = blended_get_teams_from_form($itemid,false);
	// Bucle for TEAMS
//	for($e = 1; $e <= $numteams; $e ++) {
//		
//		$t = $e; // Contador del equipo
//		         
//		// Nombres de los campos recibidos
//		$ti = "team_" . $t . "_id"; // Nombre del campo oculto "idteam"
//		$tn = "team_" . $t . "_name"; // Nombre del campo de texto "Equipo"
//		                              
//		// Obtenemos el valor de los campos recibidos
//		$id_team = optional_param ( $ti, null, PARAM_RAW );
//		$teamname = optional_param ( $tn, null, PARAM_RAW );
//		
//		// Get team & members objects ----------------------------------------
//		
//		// Obtener los arrays con los objetos team y member
//		// por separado para cada una de las acciones
//		
//		$team = new object ();
//		$grade = new object ();
//		
//		if ($teamname == null) {
//			continue;
//		} 	
//		// Objetos team
//		else {
//			// Objeto team
//			$team->id_assignment = $itemid;
//			$team->name_team = $teamname;
//			
//			$insert_index ++;
//			$insertteams [$insert_index] = $team;
//		} // Fin else if
//		  
//		// Bucle for MEMBERS
//		for($m = 1; $m <= $nummembers; $m ++) {
//			
//			// Nombre del campo de texto "Identificador"
//			$mi = "team_" . $t . "_member_" . $m;
//			
//			// Obtenemos el valor del campo "Identificador" del formulario
//			$idvalue = optional_param ( $mi, null, PARAM_RAW );
//			
//			// Guardamos los identificadores en un array. Si se han borrado
//			// identificadores en el form se guarda un null
//			
//			if ($idvalue != null) {
//				// Objetos member para INSERT
//				$insertmembers [$insert_index] [] = $idvalue;
//				
//			}
//		} // Fin bucle for MEMBERS
//	} // Fin bucle for TEAMS
	
	//Compruebo que se han introducido miembros en algún equipo ya que no se puede guardar un agrupamiento vacío:
//	if($insertmembers==array(array())){
//		$url_teams="teamsmanagement.php?a=".$a;
//		notice("No ha introducido ningún miembro! Grupos no guardados",$url_teams);
//	}
//	else{
		echo "<center>";
		print('Actualizando los grupos de la tarea seleccionada...');
		echo "</center>";
//	}
	// Execute the action ------------------------------------------------
	  
	// Una vez obtenidos los arrays con los objetos por separado
	  // ejecutamos la accion 
	  
	// INSERT TEAMS & MEMBERS
	if (! empty ( $insertteams )) {
		
		$array_return = blended_insert_teams ( $insertteams, $course, $blended, null, $itemid, null, $id, $USER->id, $groupingid );
		
		if (! empty ( $array_return )) {
			$strinserted = blended_get_error_alert ( $array_return, "insert" );
		}
	}
	
	// Print the page and finish up -----------------------------------------	

	//Actualizaci�n tabla "blended_assign_grouping"
	$DB->insert_record ( 'blended_assign_grouping', array (
			'id_assign' => $itemid,
			'id_grouping' => $groupingid 
	) );
	//Form INSERTTEAMS
	$url1="updateTeams.php?id=".$cm->id;
	echo "<form name='insertteams' method='POST' action=\"$url1\">";
		echo "<input type='hidden' name='itemid' value='" . $itemid . "'>";
		echo "<input type='hidden' name='act' value='update'>";
		echo "<input type='hidden' name='numteams' value='" . $numteams . "'>";
		echo "<input type='hidden' name='nummembers' value='" . $nummembers . "'>";
		echo "<input type='hidden' name='groupingid' value='".$groupingid."'>";
		echo "<input type='hidden' name='actualizar_agr' value='1'>";				
		echo '<script type="text/javascript">';
			echo 'document.insertteams.submit()';
		echo '</script>';
	echo "</form>";
			
// Finish the page -------------------------------------------------
		
	echo "<BR><BR><center>";
	echo $OUTPUT->help_icon ( 'pagehelp', 'blended' );
	echo "</center>";
	echo $OUTPUT->footer ();
	
?>