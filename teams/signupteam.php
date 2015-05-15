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
require_once ("locallib.php");

// Get the params ----------------------------------------------------------------

	$id = required_param ( 'id', PARAM_INT ); // Course Module ID, or
  	$itemid=required_param('itemid',PARAM_INT);

	
        if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
                print_error ( "Course Module ID was incorrect" );
        }
        if (! $course = get_course($cm->course)) {
                print_error ( "Course is misconfigured" );
        }
        if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance ) )) {
                print_error ( "Course module is incorrect" );
        }

        if (! $context = context_course::instance( $course->id )) {
                print_error ( "Context ID is incorrect" );
        }
	
// Get the current grading item --------------------------------------------------
        $item= blended_get_item( $itemid);

// Capabilities ------------------------------------------------------------------
	require_login($cm->course, false,$cm);
	$context = context_module::instance( $cm->id );
	require_capability ( 'mod/blended:signupteam', $context );
	//require_login ( $course->id );

	$context_course = context_course::instance( $cm->course );
        list($students, $non_students, $activeids, $user_records)=blended_get_users_by_type($context_course);
	if (count($students)==0) {
		print_error ( "No students in this course" );
	}

// show headings and menus of page------------------------------------------------
	$url = new moodle_url ( '/mod/blended/teams/signupteam.php', array (
		'id' => $id,'itemid'=>$itemid
	) );
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );




// Get the strings ---------------------------------------------------------------
	$strselectassignpage = get_string ( 'selectassignpage', 'blended' );
	
	$strsignupteampage = get_string ( 'signupteampage', 'blended' );
	$strsignupteam = get_string ( 'signupteam', 'blended' );
	$strnewteam = get_string ( 'newteam', 'blended' );
	$strnameteam = get_string ( 'nameteam', 'blended' );
	$strdeletemember = get_string ( 'deletemember', 'blended' );
	$strnone = get_string ( 'noselected', 'blended' );
	$strnosigned = get_string ( 'nodefinedteams', 'blended' );

// Print the main part of the page -----------------------------------------------
	$link="selectassignment.php?id=".$cm->id;
	$PAGE->navbar->add($strselectassignpage,$link);
	$PAGE->navbar->add('Inscribirse');
	echo $OUTPUT->header ();
	
	echo $OUTPUT->spacer ( array ('height' => 20 ) );
	echo $OUTPUT->heading ( format_string ( $strsignupteampage ).' '.  blended_get_item_name($item) );
	echo $OUTPUT->spacer ( array ('height' => 20 ) );

// Get all teams in the current activity---------------------------------------
	$grouping=  blended_get_grouping($item, $blended);
        $teams = blended_get_teams($item,true);
	

	$not_DNI_out = false;
	$not_DNI_in = false;
	$code = blended_gen_idvalue ( $USER, $blended );

	// Si el id es el dni y el estudiante no lo ha introducido en su perfil
	if ($code == - 1) {
		$not_DNI_in = true;
		$not_DNI_out = true;
	}
	$url14 = "selectassignment.php?id=$cm->id";
	if ($not_DNI_in) {
		echo $OUTPUT->notification ( "Hasta que posea código de identififación no puede inscribirse en ningun equipo", $url14 );
	}

// Flags de control --------------------------------------------------------------
	// Flag para imprimir el boton "Eliminar miembros"
	$printdeletebutton = false;
        $current_member_enrolled = false;

	// Compruebo si el profesor ha creado previamente un agrupamiento para esa tarea
        $groupingid = blended_get_groupingid($item);
        
	//Si no hay agrupamiento y los grupos los crean los profesores
	if ($groupingid === false && $blended->teammethod==TEAMS_BY_TEACHERS) {
		$url = "selectassignment.php?id=$cm->id";
//                echo $OUTPUT->box($strnosigned);
//		echo $OUTPUT->continue_button( $url );
		redirect($url,$strnosigned);
	}
	//Si no hay pero los grupos los crean los alumnos ya sean con leader o sin el o si hay agrupamiento
	else if($groupingid !== false || ($groupingid===false && $blended->teammethod!=0)){
		
	// Form SIGNUPTEAMFORM
	echo "<form method=\"post\" name=\"signupteamform\">";

        // Campo oculto con el identificador de la tarea.
        echo "<input type=\"hidden\" name=\"itemid\"  value=\"$item->id\">";
        // Campo oculto con el userid del usuario actual
        echo "<input type=\"hidden\" name=\"currentuser\"  value=" . $USER->id . ">";

        $table = new html_table();
        // Bucle para cada uno de los equipos existentes ---------------------------------
        
        // Array para almacenar los id y los nombres de los equipos. Necesario para el
        // campo "select" para inscribirse en determinado equipo
        $names = array ();
		
	
		foreach ( $teams as $tmid => $team ) {
			// Nombre del equipo actual
				$name = format_string ( $team->name );
				// Para el campo select de inscribirse en determinado equipo
				$names [$tmid] = $name;

				// Para saber si el usuario actual es líder de algun equipo --------------	
                                $isleader = false;
				foreach ( $team->members as $team_member ) {
					
                                        if ($team_member->id == $USER->id)
                                        {
                                            $current_member_enrolled=true;
                                        }
					if ($USER->id == $team_member->id && (isset($team->leaderid) && $team_member->id == $team->leaderid)) {
						$isleader = true;
					}
				}
			
				// Miembros de un equipo -------------------------------------------------
			
				// Para cada equipo se guardan en los arrays $pictures, $fullnames, $userids
				// las fotos, los nombres y apellidos, y los userids de los miembros de los
				// que consta
			
				// Inicializamos los arrays
				$pictures = array ();
				$fullnames = array ();
				$userids = array ();
                                $team_name_text=$name;
                                
				// Bucle MEMBERS
                                $i=0;
				foreach ( $team->members as $team_member ) {
				
//					if ($user = $DB->get_record ( 'user', array ('id' => $team_member->id ) )) 
                                    $user=$team_member;
                                            {
					
						// Comprobamos los permisos para mostrar la foto y el perfil del miembro
						if ($piclink = (has_capability ( 'moodle/user:viewdetails', $context ) || has_capability ( 'moodle/user:viewdetails', $usercontext ))) {
							$userpic = $OUTPUT->user_picture ( $user );
							$profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&course=' . $course->id . '">' . fullname ( $user, true ) . '</a>';
						} 
						else {
							$profilelink = fullname ( $user );
						}//Fin if-else
							
                                                if ( $team_member->id == $team->leaderid && $blended->teammethod==TEAMS_BY_STUDENTS_WITH_LEADER) {
                                                        $profilelink = '<strong>' . $profilelink.' (Líder)'. '</strong>';
                                                }
                                                else{
                                                        $profilelink = '<strong>' . $profilelink. '</strong>';
                                                }
						
					}
					// Rellenamos los arrays
					$pictures [] = $userpic;
					$fullnames [] = $profilelink;
					$userids [] = $user->id;
                                        $i++;
				}
			
			// TABLE
			
				
			// Strings de la tabla ---------------------------------------------------
				$strteamscount = get_string ( 'teamscount', 'blended', count ( $team->members ) );
				$strteammembers = get_string ( 'teammembers', 'blended' );
				$strmembercount = get_string ( 'membercount', 'blended' );
                                // Cabecera de la tabla --------------------------------------------------
				$table->head=array($strteamscount,$strteammembers);//,$strmembercount);
			
				// Primera fila de un equipo con el creador del equipo -------------------
                                
                                $teamname_cell = new html_table_cell();
                                $teamname_cell->rowspan=count($team->members);
                                $team_name_text="";
//                                if ($current_member_enrolled==false && $blended->teammethod==0) {
//					// Botón "Incribirse en equipo"
//					$team_name_text.= "<input type=\"radio\" name=\"addToTeam\" value=\"$tmid\">";
//                                }
                                $team_name_text.=$name;
                                $teamname_cell->text=$team_name_text;
                                if (count($team->members)>0){ 
                                    $usercell ='';
                                   
                                    // Nombre del creador (líder) del equipo
                                    // Si el usuario actual es lider puede eliminar a los miembros de su equipo
                                    if ($isleader && $blended->teammethod==TEAMS_BY_STUDENTS_WITH_LEADER) {
                                            $usercell.= "<input type=\"hidden\" name=\"team\"  value=\"$tmid\">";
                                            // Checkbox para que el creador puede eliminarse a si mismo
                                            $usercell.="<input type=\"checkbox\" name=\"$userids[0]\" value=\"delete\">";
                                            // Se activa el botón eliminar miembros
                                            $printdeletebutton = true;
                                    }
                                    $usercell.= "$pictures[0] $fullnames[0]";
                                }
                                else{
                                    $usercell = '';
                                }
				// Resto de miembros en el equipo
//                                $nummembers_cell = new html_table_cell();
//                                $nummembers_cell->rowspan=count($team_members);
//                                $nummembers_cell->text=count ( $team_members );
				$row = new html_table_row();
                                $row->cells[]=$teamname_cell;
                                $row->cells[]=$usercell;
//                                $row->cells[]=$nummembers_cell;
                                $table->data[]=$row;
				// Resto de filas de un equipo con los miembros restantes ----------------
			
				for($index = 1; $index < count ( $fullnames ); $index ++) {
				$usercell='';	
				// Si el usuario actual es lider puede eliminar a los miembros de su equipo
				if ($isleader && $blended->teammethod==TEAMS_BY_STUDENTS_WITH_LEADER) {
					// Checkbox para que el creador puede eliminarse a si mismo
					$usercell.="<input type=\"checkbox\" name=\"$userids[0]\" value=\"delete\">";
					// Se activa el botón eliminar miembros
					$printdeletebutton = true;
				}
				$usercell.= "$pictures[$index] $fullnames[$index]";
				// Número de miembros en el equipo
                               
				$row = new html_table_row();
                                $row->cells[]=$usercell;
                                $table->data[]=$row;
				}
			
		} // Fin bucle foreach para cada equipo
	  
                // output the table
		echo html_writer::table($table);
			// Si el estudiante actual es lider de equipo
		if ($printdeletebutton) {
// URL del botón "Eliminar miembros"
			$url3 = "savesignupteam.php?id=".$cm->id;
			//Table
			echo '<table align="center">';
				// Boton "Eliminar miembros"
				echo "<tr><td><input type=\"submit\" value=\"" . $strdeletemember . "\" onClick=\"document.signupteamform.action='$url3';document.signupteamform.submit()\" /></td></tr>";
				echo"<input type='hidden' name='action' value='delete'>";
			//Fin de la tabla
			echo '</table>';
		}
		echo"<input type='hidden' name='itemid' value='$itemid'>";
		// Fin del formulario
		echo '</form>';
		// Si existen equipos se imprime un espacio
		if (! empty ( $names )) {
			echo $OUTPUT->spacer ( array ('height' => 30 ) );
		}
	
		// Si el estudiante actual no esta incrito en ningun equipo
		if ($current_member_enrolled == false) {
		
			//Segun el tipo de metodo de creacion de equipos, se mostrarán unas opciones u otras:

                        // Si existen equipos
                        if (! empty ( $names )) {

                        // Select any of the existing teams
                        // URL del botón "Incribirse en equipo"
                                $url1 = "savesignupteam.php";
                                // Botón "Incribirse en equipo"
                                $form= "<form method=\"GET\" action=\"$url1\">"
                                        . "<input type=\"hidden\" name=\"itemid\" value=\"$itemid\">"
                                        . "<input type=\"hidden\" name=\"id\" value=\"$cm->id\">"
                                        . "<input type=\"hidden\" name=\"action\" value=\"signup\">"
                                        . "<select name=\"team\" align=\"left\">";
//                                        . "<option value=\"0\" selected=\"selected\">$strnone</option>";
                                foreach ( $names as $tmid => $name ) {
                                        $form.= "<option value=\"$tmid\">$name</option>";
                                }
                                $form.=  "</select> "
                                        . "<input type=\"submit\" value=\"$strsignupteam\"/>"
                                        . "</form>";
                                echo $OUTPUT->box($form);
                        }
                        //Si no existen no los puede crear
                        else if ($blended->teammethod==TEAMS_BY_TEACHERS) // teammethod=0 students can select teams
                        {
                            $url4 = "selectassignment.php?id=$cm->id";
                            echo $OUTPUT->notify( "Agrupamiento vacío. No tiene permiso para crear grupos", $url4 );
                        }
			
                        // select or create teams
			
                        if ($blended->teammethod!=TEAMS_BY_TEACHERS){
                                // URL del botón "Crear nuevo equipo"
                                $url2 = "savesignupteam.php";
                                // Boton "Crear nuevo equipo"
                                $form= "<form action=\"$url2\">";
                                $form.= "<label for=\"name_team\">$strnameteam</label>";
                                $form.= "<input type=\"text\" name=\"name_team\" size=\"10\" maxlength=\"10\">";
                                $form.= "<input type=\"hidden\" name=\"itemid\" value=\"$itemid\">";
                                $form.= "<input type=\"hidden\" name=\"id\" value=\"$id\">";
                                $form.= "<input type=\"hidden\" name=\"action\" value=\"newusergroup\">";
                                $form.= "<input type=\"submit\" value=\"$strnewteam\"/>";// Campo de texto "Nombre de equipo (opcional)"
                                $form.= "</form>";
//                                echo $OUTPUT->box
                                echo $OUTPUT->box($form);
                        //Si no existen equipos se podra crear un nuevo agrupamiento y un equipo dentro de él
                        }
		}

	}
	
	echo "<BR><BR><center>";
	echo $OUTPUT->help_icon ( 'signupteam', 'blended' );
	echo "</center>";
	
	// Finish the page --------------------------------------------------
	echo $OUTPUT->footer ();
// Log ---------------------------------------------------------------------------

//	add_to_log ( $course->id, "blended", "signupteam", "signupteam.php?a=$blended->id", "$blended->id" );
?>

