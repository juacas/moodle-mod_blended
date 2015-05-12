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
	$action=required_param('action',PARAM_ALPHA);	
	$id_team=  optional_param('team', '', PARAM_INT);
	$itemid=  required_param('itemid',PARAM_INT);
	
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

//	add_to_log ( $course->id, "blended", "savesignupteam", "savesignupteam.php?a=$blended->id", "$blended->id" );

// Capabilities ------------------------------------------------------------------
	require_login($cm->course, false,$cm);
	$idvalue = blended_gen_idvalue ( $USER, $blended );
	$context_course = context_course::instance( $cm->course );
	$context_module = context_module::instance( $cm->id );
	require_capability ( 'mod/blended:signupteam', $context_module );
	
// show headings and menus of page

	$url = new moodle_url ( '/mod/blended/savesignupteam.php', array (
		'id' => $id,'itemid'=>$itemid,'team'=>$id_team,'action'=>$action
	) );
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );

// Get the strings ---------------------------------------------------------------

	$strsignupteampage = get_string ( 'signupteampage', 'blended' );
	$strinserted = get_string ( "inserted", "blended" );
// Print the page header ---------------------------------------------------------

	echo $OUTPUT->header ();

// Print the main part of the page -----------------------------------------------

	echo $OUTPUT->spacer ( array ('height' => 20 ) );
	echo $OUTPUT->heading ( format_string ( $strsignupteampage ) );
	echo $OUTPUT->spacer ( array ('height' => 20 ) );

// Get the action ----------------------------------------------------------------
        $item= blended_get_item( $itemid);
        $grouping = blended_get_grouping($item,$blended);
	$return = null;
     
	// Borrar miembros del equipo ------------------------------------------------
	
	//Comprobación de que sea el leader el que hace la función o un manager(permiso)
	
	if ($action == 'delete' && $blended->teammethod==TEAMS_BY_STUDENTS_WITH_LEADER){
            $id_team = required_param ( 'team', PARAM_INT );//id equipo
            //   // se obtienen todos los miembros del equipo
            $members = blended_get_team_members($id_team);
            $team = blended_get_team($id_team);
         
		//Comprobar que el ussuario actual: currentuserid es el leader:
            $team_leader = $team->leaderid;
            if($team_leader!=$USER->id){
		echo $OUTPUT->notification('El usuario no es líder del equipo',"selectassignment.php?id=".$cm->id);            
		}
		else{
                $choosenewleader=false;
		//Bucle MEMBERS
		foreach ( $members as $member ) {
			// Se obtiene el valor del checkbox que marca los que desea eliminar
			$deleteuser = optional_param ( $member->id, 'nodelete', PARAM_ALPHA);
		
			// Si el líder elimina al miembro
			if ($deleteuser == 'delete') {
				// Si el lider del equipo se elimina a si mismo
				$choosenewleader = ($member->id == $team_leader);
				blended_remove_team_member ( $team, $member->id);
                                $a=new stdClass();
                                $a->username=  fullname($member);
                                $a->teamname=$team->name;
                                echo $OUTPUT->notification(get_string('userremovedfromteam','blended',$a));
			}
		}
		// Si el lider elimina todos los miembros del equipo se borra el equipo
		if (! $members = blended_get_team_members($id_team)) {
                    blended_delete_team( $id_team );
                    
                    echo $OUTPUT->notification(get_string('teamremoved','blended',$team));
		} 	
		// Si no se han borrado todos los miembros
		else {
		
			foreach ( $members as $member ) {
				// Si el lider del equipo se elimina a si mismo, y quedan miembros,
				// el siguiente miembro pasa a ser el nuevo lider del equipo
				if ($choosenewleader) {
                                    blended_set_team_leaderid($id_team, $member->id);
                                    $a = new stdClass();
                                    $a->username = fullname($member);
                                    $a->teamname = blended_get_team($id_team)->name;
                                    echo $OUTPUT->notication(get_string('userpromotedtoleader','blended',$a));
                                    break;
       				}
			} // Fin foreach select new leader
		} // Fin if-else
            }// end if user is leader
	} // Fin if delete by leader
  
// Crear nuevo equipo --------------------------------------------------------

	else if ($action == 'newusergroup' && $blended->teammethod!=TEAMS_BY_TEACHERS) {
        	$name_team = optional_param ( 'name_team','',PARAM_ALPHANUMEXT );//nombre equipo

		
                    $groupingid = blended_get_groupingid($item);
                    if (!$groupingid){ // create a default grouping
                        $itemname=  blended_get_item_name($item);
                        $grouping_name = "$itemname Teams";
                        $groupingid= blended_create_unique_grouping($grouping_name,$course);
                        $grouping = groups_get_grouping($groupingid);
                        blended_assign_grouping($item, $grouping, $USER->id, null);
                    }
                    $members = array($USER->id=>$USER);
                  
                    $teamid =blended_add_new_group($course->id, $name_team, $members, $groupingid);
                    blended_set_team_leaderid($teamid, $USER->id);
                     $a=new stdClass();
                        $a->username = fullname($USER);
                        $a->teamname = $name_team;
                        echo $OUTPUT->notification(get_string('userenrolledtoteam','blended',$a));
	} 

// Inscribirse en equipo creado previamente ----------------------------------
	else if ($action == 'signup' && $blended->teammethod!=TEAMS_BY_TEACHERS) {
		//Comprobar que:
		//-no esta en otro equipo 
		//- que el tamaño no supere el tamaño maximo propuesto por el profesor
            $id_team = required_param ( 'team', PARAM_INT );//id equipo
            $members = blended_get_team_members($id_team,$blended);
            if (count($members)>=$blended->nummembers || ($grouping->maxmembers!==null && count($members)>=$grouping->maxmembers))
                {
			echo $OUTPUT->notification(get_string('teamisfull','blended'));
		}
		else{
			if(blended_enrol_user_to_team($id_team,$USER->id,$item,$blended)){
                            
                        $a=new stdClass();
                        $a->username = fullname($USER);
                        $a->teamname = blended_get_team($id_team)->name;
                        echo $OUTPUT->notification(get_string('userenrolledtoteam','blended',$a));
                        }
		}
		
	}
        else
        {
            print_error('nopermissions','blended');
        }

// Print the page and finish up --------------------------------------------------
	
        echo $OUTPUT->continue_button(new moodle_url('signupteam.php',array('id'=>$id,'itemid'=>$itemid)));
	//Form FROM_SIGNUP
//	$url_signup = "signupteam.php?id=".$cm->id;
//	echo "<form name='form_signup' method='POST' action='".$url_signup."'>";	
//		echo"<input type='hidden' name='assignment' value='".$itemid."'>";
//		echo"<input type='hidden' name='tipoTarea' value='".$tipoTarea."'>";		
//		//Bot�n CONTINUAR
//		echo "<center>";
//			echo $strinserted."<input type='submit' value='continuar'>";
//		echo "</center>";
//	echo"</form>";

?> 
    