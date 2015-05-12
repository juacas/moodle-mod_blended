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
require_once ("../blended_locallib.php");
require_once ("locallib.php");
require_once ("grouping_form.php");
require_once ("$CFG->libdir/formslib.php");
require_once ($CFG->libdir . '/filelib.php');


//Get the params---------------------------------------------------------------
	global $DB, $PAGE, $OUTPUT;
	$id = required_param ( 'id', PARAM_INT ); // Course Module ID, or
	//Valores recogidos de introteams.php o teamsmanagement.php
	$itemid=required_param('itemid',  PARAM_INT);  // gradeitem id
	

	$eleg_agr_ex  = optional_param('eleg_agr_ex', '', PARAM_ALPHA);

	$groupingid= optional_param('groupingid', null, PARAM_INT);
	
		if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
			print_error ( "Course Module ID was incorrect" );
		}
		if (! $course = get_course($cm->course)) {
			print_error ( "Course is misconfigured" );
		}
		if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance	) )) {
			print_error ( "Course module is incorrect" );
		}
		if(!$context = context_course::instance($course->id)){
			print_error ( "Context ID is incorrect" );
		}
// 	

	
	// Capabilities ------------------------------------------------------------------
	
	// Esta funci�n comprueba que el usuario actual ha introducido el
	// login en la plataforma y que tiene los privilegios requeridos.
	// Si no han introducido el login los usuarios ser�n rediccionados
	// a la p�gina donde puedan hacerlo, a no ser que $autologinguest
	// est� fijado como true en cuyo caso el usuario entrar� en la
	// plataforma como invitado. Si el usuario no est� dado de alta en
	// el curso se le redirige a la p�gina del curso para darse de alta.
	//require_login ( $course->id );
	require_login($cm->course, false,$cm);
	$link_prev = new moodle_url ( '/mod/blended/teams/teamsmanagement.php', array (
			'id' => $id,
			'itemid' => $itemid,
	           ) );
	
	$context_course = context_course::instance($cm->course);
        list($student_ids,$nonstudent_ids,$activeids, $users) = blended_get_users_by_type($context_course);
	if (count($student_ids)==0) {
            print_error(  'errornostudents', 'blended',$link_prev);
	}

	$context = context_module::instance($cm->id);
	require_capability ( 'mod/blended:introteams', $context );
	
	// show headings and menus of page
	$url = new moodle_url ( '/mod/blended/teams/updateTeams.php', array (
			'id' => $id,
			'itemid' => $itemid, 
	) );
	
	blended_include_autocomplete_support($context,$blended);
	
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );
	
	// Get assignment name before header ---------------------------------------------
	$item = blended_get_item($itemid);
	$assignmentname = blended_get_item_name($item);
	
	// Get the strings ---------------------------------------------------------------
	$strteamsmanagementpage     = get_string("teamsmanagementpage","blended");
	$stridteam = get_string ( 'idteam', 'blended' );
	$stridmembers = get_string ( 'idmembers', 'blended' );
	$strintroteamspage = get_string ( "introteams", "blended" );
	$strcreationmethod = get_string ( "creationmethod", "blended" );
	$strbyhand = get_string ( "byhand", "blended" );
	$strrandomly = get_string ( "randomly", "blended" );
	$strstudentsselection = get_string ( 'studentsselection', 'blended' );
	$stractivestudents = get_string ( 'activestudents', 'blended' );
	$strallstudents = get_string ( 'allstudents', 'blended' );
	$strnumteams = get_string ( 'numteams', 'blended' );
	$strnummembers = get_string ( 'nummembers', 'blended' );
	
	// Print the page header ---------------------------------------------------------
	
	$PAGE->navbar->add($strteamsmanagementpage,$link_prev);
	$PAGE->navbar->add(get_string('teamsfromassignment','blended').$assignmentname);
	echo $OUTPUT->header ();
	
	// Print the main part of the page -----------------------------------------------
	
	echo $OUTPUT->spacer ( array (	'height' => 20	) );
	echo $OUTPUT->heading ( format_string ( $strintroteamspage . $assignmentname ) );
	echo $OUTPUT->spacer ( array (	'height' => 30	) );


// Asignaci�n del agrupamiento de la tarea elegida proveniente de introteams.php -------
// TODO review old code
	if(isset($eleg_agr_ex) && $eleg_agr_ex=='insertnewgrouping'){	
		$groupingid = required_param('groupingid',PARAM_INT);
		$grouping = groups_get_grouping_by_id($course->id, $groupingid);
		if ($grouping)
		{
                //borro la relaci�n tarea-agrupamiento anterior
		$DB->delete_records('blended_assign_grouping',array('id_assign'=>$itemid)); 
    		$DB->insert_record ( 'blended_assign_grouping', array (
    				'id_assign' => $itemid,
    				'id_grouping' => $groupingid
    		) );
		}
		else
		{
		    print_error("Bad Grouping id $groupingid for course $course->id");// TODO i18n
		}
	}
        $groupings = groups_get_all_groupings ( $course->id );
        // Select an existent grouping
	if ($groupings) {
                    $selected_groupingid = blended_get_groupingid($item);
			//Si existe:
                                            echo $OUTPUT->box_start();
                        echo $OUTPUT->heading(get_string('select_grouping','blended'),2);
			//Se muestran los agrupamientos existentes mediante un select:		
			$url1="updateTeams.php?id=$cm->id&itemid=$itemid";
			echo "<form name=\"f1\" action=\"$url1\" method=\"POST\">";
			echo "<select name='groupingid'>";	
			foreach($groupings as $i=>$grouping){
                                $selected= $i==$selected_groupingid?'selected="selected"':'';
				echo "<option value=\"$i\" $selected >$grouping->name</option>";
			}
			echo "</select>";
			echo "<input type='submit' value='continuar'>";
			echo "<input type='hidden' name='id' value='".$cm->id."'>";
			echo "<input type='hidden' name='itemid' value='".$itemid."'>";
			echo "<input type='hidden' name='eleg_agr_ex' value='insert_new_grouping'>";
			echo "</form>";
                        echo $OUTPUT->box_end();
		}
			
//Actualizaci�n del agrupamiento para la tarea
//Comprobaci�n de que los dni solo se han introducido una vez por miembro-------------------------
//Recogida de la fila asociada a esa tarea en blended_assign_grouping-----------------------
	$task_to_grouping=$DB->get_record('blended_assign_grouping', array('id_assign'=>$itemid));

// 	if(optional_param('actualizar_agr',null,PARAM_INT)!==null)
// 	{//recogo el vector contador de miembros en el caso de que estemos ya dentro del agrupamiento a modificar
// 		$user_count = unserialize($_POST ['contador']);
// 	}
// 	else
// 	{
		/* 
		 * Si proviene de teamsmanagement se debe obtener el agrupamiento y sus miembros
		 * para poder realizar la comprobaci�n de el n�mero de veces que existe cada identificador en los grupos
		 */
		$groupingid=$task_to_grouping->id_grouping;
		$groups_groupings_g=groups_get_all_groups($course->id,null,$groupingid);
		$i=0;
		$grouping_members=array();
		foreach($groups_groupings_g as $gr){
			$grouping_members[$i] = groups_get_members($gr->id);
			$i++;
		}
		$user_count=blended_num_members($context_course,$grouping_members,'teamsmanagement');
// 	}

	
	//Actualizacion de las calificaciones:
	$grades=null;
	// Tabla que permite reinicializar los equipos del agrupamiento---------------------------------
	$numteamspage = array_combine ( range ( 1, TEAMS_MAX_ENTRIES ), range ( 1, TEAMS_MAX_ENTRIES ) );
	$nummembersteam = array_combine ( range ( 1, MEMBERS_MAX_ENTRIES ), range ( 1, MEMBERS_MAX_ENTRIES ) );
	
	//Form SIGNUPFORM
		// Tabla que permite reinicializar los equipos del agrupamiento---------------------------------
	$numteamspage = array_combine ( range ( 1, TEAMS_MAX_ENTRIES ), range ( 1, TEAMS_MAX_ENTRIES ) );
	$nummembersteam = array_combine ( range ( 1, MEMBERS_MAX_ENTRIES ), range ( 1, MEMBERS_MAX_ENTRIES ) );
	
	//Form SIGNUPFORM
	$url2 = "createTeams.php?id=" . $cm->id;
                                echo $OUTPUT->box_start();

	echo "<form method=\"POST\" id=\"signupform\" name=\"signupform\" action =\"$url2\">";
		//Table	
		echo '<table align="center" width="30%" cellspacing="10" cellpadding="5" >';
		//Selecci�n del metodo de creaci�n: manual o aleatorio
		echo "<tr><td><label>$strcreationmethod</label></td>";
		echo "<td><select name=\"creationmethod\" id=\"creationmethod\" align=\"left\" onChange=\"enableSelectStudents()\">";
			echo "<option value=\"byhand\" selected=\"selected\">$strbyhand</option>";
			echo "<option value=\"random\">$strrandomly</option>";
		echo '</select></td></tr>';
		
		//Si el m�todo es manual se elegir�n los estudiantes: activos / todos
		echo "<tr><td><label>$strstudentsselection</label></td>";
		echo "<td><select name=\"studentsselection\" id=\"studentsselection\" align=\"left\" disabled =\"true\">";
			echo "<option value=\"activestudents\">$stractivestudents</option>";
			echo "<option value=\"allstudents\">$strallstudents</option>";
		echo '</select></td></tr>';
		
		//Elecci�n número de equipos
		echo "<tr><td><label>$strnumteams</label></td>";
		echo '<td><select name="numteams" id="numteams" align="left">';
			foreach ( $numteamspage as $key => $val ) {
				if ($key == $blended->numteams) {
					echo "<option value=\"$key\" selected=\"selected\">$val</option>";
				} else {
					echo "<option value=\"$key\">$val</option>";
				}
			}
			
		//Elecci�n n�mero de miembros por equipo
		echo "<tr><td><label>$strnummembers</label></td>";
		echo '<td><select name="nummembers" id="nummembers" align="left">';
			foreach ( $nummembersteam as $key => $val ) {
				if ($key == $blended->nummembers) {
					echo "<option value=\"$key\" selected=\"selected\">$val</option>";
				} else {
					echo "<option value=\"$key\">$val</option>";
				}
			}
		echo "</select></td></tr>";
		//Fin de la tabla
		echo '</table>';
		
		echo "<BR><center>";
		
		//Botón REINICIALIZAR EQUIPOS
		echo "<tr><td><input type=\"submit\" value=\"Reinicializar grupos\"></td>";
		echo "<input type=\"hidden\" name=\"itemid\" value=\"$itemid\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"sobreescribir\">";
		echo "<input type=\"hidden\" name=\"groupingid\" value=\"$task_to_grouping->id_grouping\">";	
		echo "<BR><center>";
		
	//Fin del formulario	
	echo "</form>";
	                        echo $OUTPUT->box_end();

// Tabla con los equipos y miembros del agrupamiento--------------------------------------
	
	//Form TEAMSFORM
	$url6 = "update_groupings.php?id=" . $cm->id . "&itemid=" . $itemid;
	echo "<form method=\"post\" name=\"teamsform\" id =\"teamsform\" action=\"$url6\" >";
		//Table
		echo '<table align="center" cellspacing="1" cellpadding="7" width="90%" >';
		
		//Obtenci�n fila de la tabla GROUPINGS del agrupamiento elegido
		$nombreagr = $DB->get_record ( 'groupings', array (	'id' => $task_to_grouping->id_grouping 	) );
		
		echo '<caption style="color:#A901DB;font-size: 11pt;font-weight: bolder;">Equipos de: ' . $nombreagr->name . '</caption>';
		echo '</br>';
		echo '</br>';
		//Obtenci�n fila de la tabla GROUPINGS_GROUPS del agrupamiento elegido
// 		$groups_for_task = $DB->get_records ( 'groupings_groups', array ('groupingid' => $task_to_grouping->id_grouping ) );
		$groups_for_task = groups_get_all_groups($course->id,null,$task_to_grouping->id_grouping);
		$team_number = 0;
		$numteams = count ( $groups_for_task ) + 1;
		$nummembers = 0;
		$groupingid = $task_to_grouping->id_grouping;
		
		/* obtenci�n del numero de cuadros a poner en el formulario segun la m�xima longitud del mayor equipo*/
		// list of users in each team
		$group_components=array();
		foreach ( $groups_for_task as $team ) {
			$gromems = groups_get_members($team->id );
			$group_components[$team->id]=$gromems;
			if ($nummembers < count ( $gromems )) {
				$nummembers = count ( $gromems );
			}
		}
		$nummembers = $nummembers + 1; // make room for expanding groups members
		
		// Realizaci�n del formulario rellenado con los campos que corresponden
		foreach($groups_for_task as $team )
		{	
			$rewriteteam = "rewrite_team_" . $team_number;
			$idteam = "team_" . $team_number . "_id";
			$teamname = "team_" . $team_number . "_name";
			
				echo "<tr>";
				
				echo "<td>  </td><td>  </td>";
				echo "<td align=\"center\"><label>$stridteam</label></td>";
				
				echo "<input type=\"hidden\" name=\"$idteam\" id=\"$team->id\" value=\"$team->id\" >";
				
				echo "<td align=\"center\"><input type=\"text\" name=\"$teamname\" id=\"$teamname\" value=\"$team->name\" size=\"6\"
							maxlength=\"8\"  align=\"center\"></td>";
				
				echo "<td align=\"center\"><label>$stridmembers</label></td>";
				
				// Para cada grupo, se muestran los identificadores de los miembros del grupo
				$members = $group_components[$team->id];
				
				// bucle miembros
				for($f = 0; $f < $nummembers; $f ++) {
					
					$membername = "team_" . $team_number . "_member_" . $f;
					
					// Nombre actual del campo de texto "Identificador" (utilizando el contador)
					
					$emptyteam = false;
					
					if ($f < count ( $members )) {
						// Objeto `user`
						$memberuserid = current ( $members )->id;
						// $emptyteam=false;
						if (! $user = $DB->get_record ( 'user', array (
								'id' => $memberuserid 
						) )) {
							$emptyteam = true;
						}
						
						if (! $emptyteam) {
							// Foto y vinculo a perfil de `user`
							if ($piclink = (has_capability ( 'moodle/user:viewdetails', $context ) || has_capability ( 'moodle/user:viewdetails', $usercontext ))) {
								$userpic = $OUTPUT->user_picture ( $user );
								$profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&course=' . $course->id . '">' . fullname ( $user, true ) . '</a>';
							}
							
							// Estudiante lider de un equipo
							if (isset ( current ( $members )->leader ) && current ( $members )->leader == 1) {
								$profilelink = '<strong>' . $profilelink . '  (leader)' . '</strong>';
							}
							
							// ID del miembro del equipo
							$membervalue = blended_gen_idvalue ( current ( $members ), $blended );
							
							if ($membervalue == - 1) {
								$membervalue = "\"\"";
								$stridnumber = get_string ( 'withoutidnumber', 'blended' );
							} else if ($membervalue == - 2) {
								$membervalue = "\"\"";
								$stridnumber = get_string ( 'withoutuserinfodata', 'blended' );
							} else {
								$stridnumber = "";
								$membervalue = blended_generate_memberid_field(current($members),$blended);
							}
							
							if ($membervalue == ' ') {
								prev ( $members );
								continue;
							}
							
							// Foto
							echo "<td align=\"center\">$userpic</td>";
							// Campo de texto "Identificador" lleno.
							
							
							//Cuando un mismo identificador se repite en varios equipos el recuadro se remarca en rojo
							if (
							    //key_exists($memberuserid, $user_count) &&
							    $user_count[$memberuserid] == 1
						      )
							{
								echo "<td align=\"center\"><input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"$membervalue\" size=\"7\"
														maxlength=\"8\"  align=\"center\"><br><font size=\"1\">$profilelink<font size=\"1\" color=\"#FF0000\">$stridnumber</font></td>";
							} else {
								echo "<td align=\"center\"><input type=\"text\" style=\"border-color:red\" title=\"id repetido\" name=\"$membername\" id=\"ac-userid\" value=\"$membervalue\" size=\"7\"
														maxlength=\"8\"  align=\"center\"><br><font size=\"1\">$profilelink<font size=\"1\" color=\"#FF0000\">$stridnumber</font></td>";
							}
							next ( $members );
						} 					
						// Equipo vacio
						else {
							echo "<td></td>";
							// Campo de texto "Identificador" vacio.
							echo "<td align=\"center\"><input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\"
															maxlength=\"8\"  align=\"center\"></td>";
						}
					} else {
						echo "<td></td>";
						// Campo de texto "Identificador" vacio.
						echo "<td align=\"center\"><input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\" maxlength=\"8\"  align=\"center\"></td>";
					}
				}
				
				echo "</tr>";
			
	
			 
			$team_number ++;
		}
		{
		    //(Introducci�n de una fila por si se desea introducir otro grupo
		    echo "<td>  </td><td>  </td>";
		    echo "<td align=\"center\"><label>$stridteam</label></td>";
		
		    //$id_nuevo = $gid + 1;
		    $idteam = "team_" . $team_number . "_id";
		    $teamname = "team_" . $team_number . "_name";
		    echo "<input type=\"hidden\" name=\"$idteam\" id=\"-1\" value=\"$team_number\" >";
		
		    echo "<td align=\"center\"><input type=\"text\" name=\"$teamname\" id=\"$teamname\" value=\"\" size=\"6\"
		    maxlength=\"8\"  align=\"center\"></td>";
		
		    // mostramos los identificadores de los miembros del grupo
		    echo "<td align=\"center\"><label>$stridmembers</label></td>";
		
		    // bucle miembros
		    for($f = 0; $f < $nummembers; $f ++) {
		    $membername = "team_" . $team_number . "_member_" . $f;
		        echo "<td></td>";
		            // Campo de texto "Identificador" vacio.
		            echo "<td align=\"center\"><input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\" maxlength=\"8\"  align=\"center\"></td>";
		}
					}
		//Fin de la tabla
		echo "</table>";
// 		$urlback="teamsmanagement.php?id=$cm->id";
		echo '<table align="center">';
		echo "<tr><td><input type=\"submit\" value=\"Actualizar Agrupamiento\"/></td>";
// 		echo "<td><input type=\"submit\" value=\"Ir a: Administración de Equipos\" onclick=\"this.form.action='$urlback'\"/></td></tr>";
		echo '</tr></table>';
		echo "<input type=\"hidden\" name=\"numteams\"      id=\"numteams\"       value=\"$numteams\">";
		echo "<input type=\"hidden\" name=\"nummembers\"   id=\"nummembers\"    value=\"$nummembers\">";
		echo "<input type=\"hidden\" name=\"groupingid\"        id=\"groupingid\"         value=\"$groupingid>\"";
		echo "<input type=\"hidden\" name=\"itemid\"  id=\"assignment\"   value=\"$itemid\">";
		echo "<input type=\"hidden\" name=\"id\"            id=\"id\"             value=\"$cm->id\">";
	//Fin del formulario
	echo "</form>";

	
// Finish the page -------------------------------------------------

echo "<BR><BR><center>";
echo $OUTPUT->help_icon ( 'pagehelp', 'blended' );
echo "</center>";
echo $OUTPUT->footer ();

?>

<br />
<script type="text/javascript">

function enableSelectStudents(){

    if(creationmethod.value=='random'){
        studentsselection.disabled=false;
    }
    if(creationmethod.value=='byhand'){
    	studentsselection.disabled=true;
    }
}

function jumpcursor(field,nextfield){
    if (nextfield != 'end' && field.value.length == 13) {
       document.getElementById(nextfield).focus();        
    }
}
  
</script>