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




// UPDATE GROUPINGS -------------------------------------------------------------------

/********************************************************************************
 * ACTUALIZACI�N de la tabla de agrupamientos 
 * ya sea creando un nuevo grupo o actualizando uno existente
*
* Se comprueba si en el curso existe un grupo con ese nombre y si no existe se crea 
* y si existe se actualiza
* 
* @see groups_get_group_by_name()
* @see blended_update_group()
* @see blended_add_new_group()
*
* @param array $mem array con los alumnos del equipo
* @param string $teamname el nombre del equipo
* @param int $courseid id del objeto de la instancia del curso
* @param string|null $ya parametro pasado por defecto a las otras funciones
* @param int $groupingid id del agrupamiento
*
********************************************************************************/
function blended_update_grouping($courseid,$teamname,$mem,$groupingid,$ya,$groupid=null){

	if($groupid==null){
		$group_id = groups_get_group_by_name ( $courseid, $teamname );
		if ($group_id!=false ) {
			blended_update_group($courseid,$teamname,$mem,$group_id,$ya);
		}
		
		if ($group_id==false) {
		
			$id_group=blended_add_new_group($courseid, $teamname, $mem, $groupingid,$ya);
			return $id_group;
		}
	}
	else{
		if ($groupid!=false ) {
			blended_update_group($courseid,$teamname,$mem,$groupid,$ya);
		}
		
		if ($groupid==false) {
		
			$id_group=blended_add_new_group($courseid, $teamname, $mem, $groupingid,$ya);
			return $id_group;
		}
		
	}
		
}





/********************************************************************************
 * COMPROBACI�N de la existencia de un agrupamiento assignado a la actividad
 * con id ='id_assignment'
 * 
 * @return:true si existe/false si no existe
********************************************************************************/

function blended_probe_grouping_exist($id_assignment){
	global $DB;
	
	$k=$DB->record_exists('blended_assign_grouping', array('id_item'=>$id_assignment));
	
	if($k==true){
		return true;
	}
	else{
		return false;
	}
}


// INSERT ----------------------------------------------------------------------



//Funcion que permite a un alumno crear un nuevo equipo
function blended_crear_equipo_por_alumno($cm,$blended,$id_assignment,$name_team,$course,$idvalue,$currentuser){
	global $DB;
	
	if (empty ( $name_team )) {
		$teamname = 1;
	} else {
		$teamname = $name_team;
	}
	
	//Obtenci�n del nombre
	$teamname=blended_get_name_team ( $id_assignment, $teamname );
	
	//Comprobacion de que el alumno o no éste en otro equipo:	
	$group_assign=$DB->get_record('blended_assign_grouping', array('id_assign'=>$id_assignment));
	$groups_groupings=groups_get_all_groups($course->id,0,$group_assign->id_grouping);
	foreach($groups_groupings as $group){
		$members=groups_get_members($group->id);
		foreach($members as $tid=>$member){
			if( $currentuser==$tid){
				notice('ya esta inscrito en otro equipo',"selectassignment.php?id=".$cm->id);
			}
		}
	}
		
		//Introduzco un nuevo grupo con su miembro en el agrupamiento:
		$groupid=blended_update_grouping ( $course->id, $teamname, $idvalue, $group_assign->id_grouping, null );
		//Según el valor de $blended_teammethod, el alumno podrá ser líder o no:
		if($blended->teammethod==2){
			//Actualizar tabla blended con leader=1:
			$DB->insert_record_raw('blended_team',array('id_team'=>$groupid,'id_assignment'=>$id_assignment,'name_team'=>$teamname,'userid_leader'=>$currentuser));
			$DB->insert_record_raw('blended_member',array('id_team'=>$groupid,'userid'=>$currentuser,'id_member'=>$idvalue,'leader'=>1));
				

			//blended_update_teams_by_groups($id_assignment,1,$groupid,$currentuser);
		}
		else{
			$DB->insert_record_raw('blended_team',array('id_team'=>$groupid,'id_assignment'=>$id_assignment,'name_team'=>$teamname,'userid_leader'=>0));
			$DB->insert_record_raw('blended_member',array('id_team'=>$groupid,'userid'=>$currentuser,'id_member'=>$idvalue,'leader'=>0));
			//blended_update_teams_by_groups($id_assignment,0,$groupid);
		}
}

//Funcion para inscribirse en un equipo

function blended_inscribir_equipo_por_alumno($id_team,$idvalue,$id_assignment,$course,$cm,$blended,$currentuser){
	global $DB;
	//Comprobacion de que el alumno o no éste en otro equipo:
	$group_assign=$DB->get_record('blended_assign_grouping', array('id_assign'=>$id_assignment));
	$groups_groupings=groups_get_all_groups($course->id,0,$group_assign->id_grouping);
	foreach($groups_groupings as $group){
		$members=groups_get_members($group->id);
		foreach($members as $tid=>$member){
			if( $currentuser==$tid){
				echo $OUTPUT->notification('ya esta inscrito en otro equipo',"selectassignment.php?id=".$cm->id);
			}
		}
	}
	
	//Objeto equipo
	$team = new object ();
	$team->id = $id_team;
	$leader = 0;
	$checknummembers = true;
	$teams = $DB->get_records('groups');
	foreach($teams as $team){
		if($team->id==$id_team){
			$s=$team;
			break;
		}
	}
//	$return = blended_insert_team_member ( $idvalue, $team, $id_assignment, $course, $blended, $leader, $checknummembers );
	//Actualizaci�n del agrupamiento
	
	blended_update_grouping ( $course->id, $s->name, $idvalue, null, true, $s->id );
	//blended_update_teams_by_groups($id_assignment);
}

/*******************************************
//Funcion por la que sin haber agrupamiento el aluno puede crear unmo nuevo
*******************************************/
function blended_crear_nuevo_agrupamiento_por_alumno($course,$name_team,$mem,$id_assignment,$blended,$currentuserid){
	global $DB;
	//Pongo el nombre al agrupamiento el mismo que posee la actividad
	$modulos=get_course_mods($course->id);
	foreach($modulos as $modulo){
		if($modulo->id==$id_assignment){
			$curse_modulos=get_coursemodule_from_instance($modulo->modname, $modulo->instance);
			$name=$curse_modulos->name;
		}
	}
		
		
	//Creaci�n del agrupamiento
	$data=new object();
	$data->name=$name;
	$data->courseid= $course->id;
	$data->description_editor ['text'] = ' ';
	$data->description_editor ['format'] = 1;
	$groupingid=groups_create_grouping($data);
	
	$groupid=blended_add_new_group($course->id, $name_team, $mem, $groupingid);
	$DB->insert_record_raw('blended_assign_grouping',array('id_assign'=>$id_assignment,'id_grouping'=>$groupingid));
//Actualizo las tablas de blended dependiendo del tipo de creación de equipos:
	if($blended->teammethod==1){
		blended_update_teams_by_groups($course->id,$id_assignment,0,$groupid);
	}
	else{
		blended_update_teams_by_groups($course->id, $id_assignment,1,$groupid,$currentuserid);
		
	}
	
}
// UPDATE -----------------------------------------------------------------------


/********************************************************************************
 * ACTUALIZACI�N de un grupo del agrupamiento
 * 
 * Seg�n el valor de 'ya':
 * 	- Si 'ya'=null la acci�n la realiza un profesor
 * 	- Si 'ya'!=null la acci�n es realizada por un alumno
 * 
 * @see groups_remove_member()
 * @see groups_update_group()
 * @see blended_clean_idnumber()
 * @see groups_add_member()
 * @see groups_update_grouping()
 *
 * @param string $mem string con los alumnos del equipo
 * @param string $teamname el nombre del equipo
 * @param int $courseid id del objeto de la instancia del curso
 * @param string|null $ya parametro pasado por defecto
 * @param int $groupid id del grupo a actualizar
 * 
********************************************************************************/
function blended_update_group($courseid,$teamname,$mem,$groupid,$ya){

	
	global $DB;
	
	$data = new object ();
	$data->courseid = $courseid;
	$data->name = $teamname;
	$data->description_editor ['text'] = ' ';
	$data->description_editor ['format'] = 1;
	$data->id = $groupid;
	groups_update_group ( $data );
	
	$oldmems=$DB->get_records('groups_members',array('groupid'=>$groupid));

	if($ya==null){
		foreach($oldmems as $oldmem){
		groups_remove_member($groupid, $oldmem->userid);
		}
	
		$us=$DB->get_records('user');
		for($y=0;$y<count($mem);$y++){
			$use=array();
			foreach($us as $f=>$u){
				$use[$f]=blended_clean_idnumber($u->idnumber);
				if($use[$f]==$mem[$y]){
					groups_add_member($groupid,$u->id);
				}
			}	
		}
	}
	else if($ya==true){
	
		$memid = $DB->get_records ( 'user');
		foreach($memid as $mid){
			if($mem==substr($mid->idnumber,0,8)){
				groups_add_member($groupid,$mid->id);
				$DB->insert_record_raw('blended_member', array('userid'=>$mid->id,'id_member'=>$mem,'id_team'=>$groupid,'leader'=>0));
			break;
		}

		}
	}
	
		
	groups_update_grouping($data);
}


/********************************************************************************
 * ACTUALIZA equipos existentes en las tablas del módulo blended.
 *
 * Recibe el array unidimensional 'teams' con todos los objetos 'team' y otro
 * bidimensional 'members' indexado por un indice entero secuencial que hace
 * referencia a cada objeto 'team' y por los identificadores de los miembros
 * asociados a cada 'team'  y/o otro array unidimensional 'grades' con todos los
 * objetos 'grade':
 *
 * - Cuando recibe los arrays 'teams' y 'members', ACTUALIZA uno a uno cada objeto
 *   'team' junto con el array de los identificadores de todos sus miembros
 *   asociados. Actualizar los miembros de un equipo implica BORRARLOS TODOS de
 *   la tabla "blended_member" y volverlos a INSERTAR. Se guardan los miembros que
 *   había antes de la actualización en el array $oldmembers por si ocurre algún
 *   error.
 *
 *   Si hay error al insertar alguno de los elementos del array 'members' asociado
 *   a un objeto 'team', se guarda en el array $array_return indexado por el nombre
 *   del equipo el código de error junto con el identificador de cada uno de los
 *   miembros que ha dado error. Además se restauraran los miembros anteriores a la
 *   actualización guardados en $oldmembers.
 *
 * - Cuando solo recibe el array 'grades' ACTUALIZA uno a uno cada objeto 'grade' en
 *   la tabla correspondiente.
 *
 * Además actualiza 'assigment_submissions' y el 'gradebook' y los agrupamientos.
 *
 * @see blended_update_team()
 * @see blended_delete_all_team_members()
 * @see blended_insert_all_team_members()
 * @see blended_update_assignment_and_gradebook()
 *
 * @param array $teams array con los objetos 'team' de la tabla "blended_team"
 * @param array $grades array con los objetos 'grade' de la tabla "blended_grade"
 * @param array $members array bidimensional indexado con el identificador del
 *              equipo y con los identificadores de los miembros asociados
 * @param object $course objeto de la instancia del curso
 * @param object $blended objeto de la instancia del modulo blended
 * @param int $leader bandera para saber si un miembro es líder de su equipo
 * @param int $id_assignment identificador de la tarea actual
 * @param int $id_assignment_0 identificador de la tarea de la cual se extrajeron los equipos
 * @param int $id el identificador del módulo del curso
 * @param int $currentuserid el objeto de la tabla "user" perteneciente al usuario
 *            que esta usando el módulo
 * @return  null|array nada si equipos insertados correctamente o array con los códigos
 *                     de error y los identificadores de los estudiantes no insertados
 ********************************************************************************/
function blended_update_teams($teams=null,$grades=null,$members=null,
$course=null,$blended=null,$id_assignment=null,$id_assignment_0=null,$id=null,$currentuserid=null,$groupingid){

	global $DB;
	if ($blended==null) error("Blended Module Instance Wrong!!");
	
	if($teams !=null){
		foreach($teams as $index=>$team){
			
			if($grades!=null){
				$grade= array_key_exists( $index, $grades)?$grades[$index]:null;
			}else{
				$grade=null;
			}
		
			blended_update_team($team, $grade);

			if($members!=null){
				// Guardamos los miembros a actualizar por si ocurre algún error
				// volver a los valores anteriores
				if(count($members)<$index+1)
				{
					continue;
				}
					
				
				if($members[$index]!=null)
				{
				
					$prev_members=$members[$index];
				
					$oldmembers= blended_get_teams_members_by_id($team->id, null, $blended, $prev_members,'false',true);

					blended_update_assignment_and_gradebook($id_assignment,$id_assignment_0,$members,$blended,$id,$currentuserid,$team->id);
					// Si ocurre error
					
					
					if(!empty($return))
					{
						// [Deprecated]Se restauran los valores antes de la actualización
						// JPC: TODO: REVISAR Mejor no restaurar el equipo anterior si hay algún error identificando a algún miembro.
						//  Es preferible dejar anotados los elementos correctos y su calificación.
						// blended_insert_all_team_members($oldmembers, $team, $id_assignment, $course, $blended, null);
						// Se guarda en un array todos los códigos de error
						$array_return[$team->name_team] = $return;
					}
				}
			}
			
		}

		if(!empty($array_return)){
			return $array_return;
		}
	}

	
}





// DELETE ----------------------------------------------------------------------

/********************************************************************************
 * BORRAR un grupo o un miembro de un grupo especifico
 * 
 * Mediante el par�metro 'option' se decide:
 * 	- Si 'option'='member' se borrar el mimebro dado por 'idmember' del equipo  con id 'idteam' 
 * 	- Si 'option'='group' se borrar el equipo con id 'idteam' 
 * 
 * @see blended_delete_team()
 * @see blended_delete_team_member()
 * @see groups_remove_member()
 * @see groups_delete_group()
 * @see groups_unassign_grouping()
 * @see groups_delete_grouping()
 * 
 * @params int $idteam identificador del equipo
 * @params string $member onjeto de la instancia de un miembro
 * @params string $option indica que acción se va a realizar
 *
********************************************************************************/
function blended_delete_sign_member_or_group($idteam,$member,$option){
	global $DB;
	
	$blended_teams=$DB->get_records('blended_team');
	foreach($blended_teams as $blended_team){
		if($blended_team->id_team==$idteam){
			$f=$blended_team;
		}
	}
	$g=$DB->get_record('groups',array('id'=>$idteam));
	$l=$DB->get_record('blended_assign_grouping',array('id_assign'=>$f->id_assignment));
	
	if($option=='member'){
		$del=groups_remove_member($idteam, $member->userid);
		blended_delete_team_member($idteam,$member->id);
	}
	if($option=='group'){
		$del=groups_delete_group($idteam);
		groups_unassign_grouping($l->id_grouping, $idteam);
		blended_delete_team($idteam,$f->id_assignment);
		blended_delete_team_member($idteam);
		//Se debe realizar la comprobación de que si no hay mas equipos se borre el agrupamiento
		if(!$teams=$DB->get_records('groupings_groups',array('groupingid'=>$l->id_grouping))){
			groups_delete_grouping($l->id_grouping);
			$DB->delete_records('blended_assign_grouping',array('id_grouping'=>$l->id_grouping));
		}
	}


}


/********************************************************************************
 * ELIMINA los objetos 'team', 'grade' y todos los objetos 'member' asociados.
 * Además actualiza los 'assigment_submissions' y el gradebook.
 *
 * Dos modos de funcionamiento:
 *
 * - 1: Si recibe $id_assignment (identificador de tarea) elimina todos los objetos
 *   'member' asociados al equipo $id_team (identificador de objeto 'team') definido
 *   en dicha tarea (o en su defecto al definido en la tarea $id_assignment_0).
 *
 * - 2: Si NO recibe $id_assignment y no existen objetos 'member' asociados al
 *   objeto $id_team (equipo vacio) borra dicho objeto 'team' y el ojeto 'grade'
 *   asociado.
 *
 * Despues elimina el objeto 'team'.
 *
 * A continuación elimina el objeto 'grade' asociado. Dos modos de funcionamiento:
 *
 * - 1: Si recibe $id_grade distinto a -1 significa que el objeto 'team' ya fue calificado.
 *   En este caso lo borra usando $id_grade.
 *
 * - 2: si recibe $id_grade igual a -1 significa que el objeto 'team' no fue calificado o
 *   ha sido borrada su nota. En este caso lo borra usando $id_team.
 *
 * @see blended_delete_all_team_members()
 *
 * @param int $id_team
 * @param int $id_grade
 * @param int $id_assignment
 * @param int $id_assignment_0
 * @param int $id
 * @return  null
 ********************************************************************************/
function blended_delete_all($id_team, $id_grade=null, $id_assignment=null,$id_assignment_0=null,$id=null){

	$delete=false;
	global $DB;

	// DELETE BLENDED_MEMBER & ASSIGNMENT_SUBMISSION & GRADE_GRADES

	if($id_assignment != null){
		blended_delete_all_team_members($id_team, $id_assignment, $id_assignment_0, $id);
		$delete=true;
	}
	else {
		if(!$DB->get_records('blended_member', array('id_member'=> $id_team))){
			$delete=true;
		}
	}

	// DELETE BLENDED_TEAM
	if($delete){
		if (! $DB->delete_records('blended_team',array('id'=> $id_team))) {
			error("Encountered a problem trying to delete team.");
		}
	}

	// DELETE BLENDED_GRADE

	//No calificado
	if($id_grade==null) {
	}
	//Calificado, borramos por 'id'
	else if($id_grade!=-1) {
		if (! $DB->delete_records('blended_grade',array('id'=> $id_grade))){
			error("Encountered a problem trying to delete grade.");
		}
	}
	//Parcialmente calificado o calificado, borramos por 'id_team'
	else if($id_grade==-1) {
		if (! $DB->delete_records('blended_grade',array('id_team'=> $id_team))){
			error("Encountered a problem trying to delete grade.");
		}
	}
}

/********************************************************************************
 * ELIMINA TODOS los objetos 'member' asociados  a un objeto 'team'. Y si además
 * se ha borrado su calificación de la tabla "blended_grade" o si se desea
 * sobreescribirla se borra también del libro de calificaciones.
 *
 * Obtiene todos los objetos 'member' de la tabla "blended_member" asociados al
 * objeto 'team'. También se obtiene el objeto 'assignment'. Para cada objeto
 * 'member' se obtiene su nota de la tabla "blended_grade". Si dicha nota se
 * ha borrado de la tabla "blended_grade" o se desea sobreescribirla se elimina
 * esta calificación del del libro de calificaciones.
 *
 * Por ultimo eliminamos todos los objetos 'member' pertenecientes al equipo $id_team
 * de la tabla "blended_member".
 *
 * @see blended_get_assignment()
 * @see blended_get_user_grade()
 * @see blended_delete_submission()
 * @see assignment_grade_item_update()
 * @see blended_delete_team_member()
 *
 * @global $CFG
 * @param int $id_team identificador del objeto 'team'
 * @param int $id_assignment identificador de la tarea
 * @param int $id_assignment_0 en caso de que se utilicen equipos definidos en otra tarea
 *            identificador de esa tarea.
 * @param int $id
 * @return   null
 ********************************************************************************/
function blended_delete_all_team_members($id_team, $id_assignment, $id_assignment_0, $id=null){

	global $CFG, $DB;
	require_once("$CFG->dirroot/mod/assign/lib.php");

	
	$members = array();
	// Se comprueba que el equipo tiene miembros antes de borrarlos
	if ($members = $DB->get_records('blended_member',array('id_team'=>$id_team))) {
	
		$assignment = blended_get_assignment($id_assignment, $id);
		

		$grades = array();
		
		foreach ($members as $member) {

			$grade = blended_get_user_grade(null, $id_assignment, $id_assignment_0, null, null, $member->userid);

			// Si tiene calificación y no se desea sobreescribirla no hace nada
			if(($grade!=null) && ($grade->rewrite == 0)){
				continue;
			}
			// Si no tiene calificación o si la tiene pero se desea sobreescribirla
			// la borra del libro de calificaciones
			if ( $grade==null || ( ($grade!=null) && ($grade->rewrite == 1) ) ){

				// DELETE ASSIGNMENT_SUBMISSION
				blended_delete_submission($id_assignment, $member->userid);
				 
				// UPDATE GRADE_GRADES
				$grades[$member->userid]=new stdClass();
				$grades[$member->userid]->userid   = $member->userid;
				$grades[$member->userid]->rawgrade = null;
// 				activity_grade_item_update($assignment, $grades);
			}
		}

		// DELETE ALL BLENDED_MEMBER

		// Borra todos los miembros del equipo $id_team
		blended_delete_team_member($id_team);

	}
}



/********************************************************************************
 * ELIMINA de la tabla "blended_team" un objeto 'team' y de la tabla "blended_grade"
 * el objeto 'grade' asociado. Para tareas en las que se usan equipos definidos en
 * otra tarea solo borra los objetos 'grade' asociados a dichos equipos y los borra
 * todos de una vez.
 *
 * Dos modos de funcionamiento
 *
 * - 1: Si recibe $id_team (para equipo definido inicialmente para esa tarea) borra
 *   el objeto correspondiente de la tabla "blended_team" y el objeto 'grade'
 *   asociado en la tabla "blended_grade".
 *
 * - 2: Si no recibe $id_team pero recibe $id_assignment e $id borra todos los objetos
 *   'grade' de la tabla "blended_grade" asociados a la tarea $id_assignment y
 *   actualiza el gradebook.
 *
 * @see get_coursemodule_from_id()
 * @see activity_grade_item_update()
 *
 * @param int $id_team identificador del objeto 'team' de la tabla "blended_team"
 * @param int $id_assignment identificador de la tarea
 * @param int $id el identificador del módulo del curso
 * @return  null
 ********************************************************************************/
//function blended_delete_team($id_team=null, $id_assignment, $id=null){
//	global $CFG, $DB;
//	//require_once("$CFG->dirroot/mod/assign/lib.php");
//	//require_once("$CFG->dirroot/mod/assignment/lib.php");
//	//require_once("$CFG->dirroot/mod/forum/lib.php");
//
//	$activity_cm= get_coursemodule_from_id(null, $id_assignment);
//	
//	if($id_team!=null){
//		$blended_teams=$DB->get_records('blended_team');
//		foreach($blended_teams as $blended_team){
//			if($blended_team->id_team==$id_team){
//				if (! $DB->delete_records('blended_team',array('id'=>$blended_team->id))){
//					error("Encountered a problem trying to delete team.");
//				}
//				break;
//			}
//			
//		}	
//		if (! $DB->delete_records('blended_grade', array('id_team'=>$id_team, 'id_assignment'=> $activity_cm->id))){
//			error("Encountered a problem trying to delete grade.");
//		}
//	}
//	else{
//		if (! $DB->delete_records('blended_grade', array('id_assignment'=>$activity_cm->id))){
//			error("Encountered a problem trying to delete grade.");
//		}
//
//
//	}
//}



// GRADEBOOK --------------------------------------------------------------------



/********************************************************************************
 * ACTUALIZA el libro de calificaciones cada vez que se califica a un equipo y
* también se actualizan los 'assignment submission'.
*
* Si el equipo está calificado (posee una calificación en la tabla "blended_grade")
* pero no se desea sobreescribir las notas individuales del libro de calificaciones
* pues un miembro tiene una calificación distinta a la del equipo no hace nada.
*
* Si el equipo esta calificado y se desea sobreescribir una posible nota individual
* de algún miembro distinta a la de grupo se actualiza el libro de calificaciones
* con la nota actual obtenida de la tabla "blended_grade".
*
* Crea un objeto 'submission' y los inserta en la tabla "assignment_submissions".
*
* @see blended_get_assignment()
* @see blended_get_user_grade()
* @see blended_delete_submission()
* @see blended_prepare_new_submission()
* @see activity_grade_item_update()
*
* @global $CFG
* @param int $id_assignment identificador de la tarea
* @param int $id_assignment_0 identificador de la tarea de la cual se extrajeron
*            los equipo
* @param array $all_teams array bidimensional con
* @param object $blended objeto del modulo blended
* @param int $id el identificador del módulo del curso
* @param int $currentuserid identificador' del objeto 'user' del usuario actual
*            del módulo (profesor o administrador)
* @param int $tid_team para saber a que id de que grupo del agrupamiento tengo que actualizar la calificaci�n
* @return null
********************************************************************************/
function blended_update_assignment_and_gradebook($id_assignment,$id_assignment_0=null,
$all_teams, $blended, $id, $currentuserid,$id_team=null){
	global $CFG,$DB;
	//require_once("$CFG->dirroot/mod/assign/lib.php");
	//require_once("$CFG->dirroot/mod/forum/lib.php");
	//require_once("$CFG->dirroot/mod/assignment/lib.php");
	
	global $DB;
	
	$activity_cm= get_coursemodule_from_id(null, $id_assignment);
	
	$assignment = blended_get_assignment($activity_cm->id, $id);	

	foreach($all_teams as $index=>$id_member){
		$blendedgrade = new object();
		$blendedgrade->rewrite = null;
		
		$g=new object();
		$grades=array();
	
			// Dos modos de funcionamiento
			
	 		$user = blended_get_user($id_member, $blended, null);		 		 		
			$blendedgrade = blended_get_user_grade($user, $activity_cm->id, $id_assignment_0,	$id_member, $blended, null);
					
			if (($blendedgrade!=null)){
				
				// ASSIGNMENT SUBMISSION -----------------------------------------
				blended_delete_submission($activity_cm->id,$user->id);
								
				$newsubmission = blended_prepare_new_submission($activity_cm->id, $user->id,
				$currentuserid, $blendedgrade->grade) ;
				
				if (!$DB->insert_record("assignment_submissions", $newsubmission)) {
					error("Could not insert a new empty submission");
				}	
				
				$g->grades[$user->id] = new object();
				$g->grades[$user->id]->userid   = $user->id;
				$g->grades[$user->id]->finalgrade = $blendedgrade->grade;
				
				$actividad=$DB->get_record('grade_items',array('iteminstance'=>$assignment->instance));
				$assignments=$DB->get_records($actividad->itemmodule);
					
				foreach($assignments as $ass){
					if($ass->id==$assignment->instance){
						activity_grade_item_update($assignment, $g);
					}
				}								
			}	

		if(($blendedgrade!=null) && ($blendedgrade->rewrite == 0)){
			continue;
			
		}
	}// Fin foreach
}

/********************************************************************************
 * Create grade item for given activity
 *
 * @category grade
 * @param stdClass $activity An activity instance
 * @param mixed $grades Optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok, error code otherwise
*
********************************************************************************/
function activity_grade_item_update($assignment, $grades=NULL) {
	
	global $CFG,$DB;
	require_once($CFG->libdir.'/gradelib.php');
	
	
	if (!isset($assignment->courseid)) {
		$assignment->courseid = $assignment->course;
	}

	if(isset($assignnmet->cmidnumber)){
		$params = array('itemname'=>$assignment->name, 'idnumber'=>$assignment->cmidnumber);
	}
	else{
		$params = array('itemname'=>$assignment->name);
	}
	
	$grades_s=$DB->get_record('grade_items',array('iteminstance'=>$assignment->instance));
	$assignment->grade=$grades_s->grademax;

	
	if ($assignment->grade > 0) {
		$params['gradetype'] = GRADE_TYPE_VALUE;
		$params['grademax']  = $assignment->grade;
		$params['grademin']  = 0;

	} else if ($assignment->grade < 0) {
		$params['gradetype'] = GRADE_TYPE_SCALE;
		$params['scaleid']   = -$assignment->grade;
			
	} else {
		$params['gradetype'] = GRADE_TYPE_TEXT; // allow text comments only
	}

	if ($grades  === 'reset') {
		$params['reset'] = true;
		$grades = NULL;
	}
		
	
	$actividad=$DB->get_record('grade_items',array('iteminstance'=>$assignment->instance));
	
	
	foreach($grades as $grade){
		return grade_update("mod/.$actividad->itemmodule", $assignment->courseid, 'mod', $actividad->itemmodule, $assignment->instance, 0, $grade, $params);
	}
}



// ASSIGNMENT SUBMISSION --------------------------------------------------------



/********************************************************************************
 * Instancia un nuevo objeto 'submission' para un usuario dado.
 *
 * Inicializa assignment, userid, teacher y grade. Todo lo demás lo deja en sus
 * valores por defecto.
 *
 * @param int $id_assignment identificador de la tarea
 * @param int $userid identificador del objeto 'user' del miembro del equipo
 * @param int $currentuserid identificador' del objeto 'user' del usuario actual
 *            del módulo (profesor o administrador)
 * @param int $grade calificación del miembro del equipo
 * @return object objeto 'submission'
 ********************************************************************************/
function blended_prepare_new_submission($id_assignment,$userid,$currentuserid,$grade) {
	$submission = new Object;

	$submission->assignment   = $id_assignment;
	$submission->userid       = $userid;
	$submission->timecreated  = time();
	$submission->timemodified = $submission->timecreated;
	$submission->numfiles     = 0;
	$submission->data1        = '';
	$submission->data2        = '';
	$submission->grade        = $grade;
	$submission->submissioncomment = '';
	$submission->format       = 0;
	$submission->teacher      = 0;
	$submission->timemarked   = 0;
	$submission->mailed       = 0;

	return $submission;
}

/********************************************************************************
 * ELIMINA un objeto 'submission' para un 'user' y un 'assignment' dados.
 *
 * @param int $id_assignment identificador de la tarea
 * @param int $userid identificador del objeto 'user' del miembro del equipo
 * @return null
 ********************************************************************************/
//function blended_delete_submission($id_assignment,$userid){
//	global $DB;
//	
//	if ($submission = $DB->get_record('assignment_submissions', array('assignment'=>$id_assignment,'userid'=>$userid))) {
//		if (! $DB->delete_records('assignment_submissions', array('assignment'=>$id_assignment,'userid'=>$userid))){
//			error("Encountered a problem trying to delete submission of assignment with id: ".$id_assignment. ".");
//		}
//	}
//
//}


// GET --------------------------------------------------------------------------




/********************************************************************************
 * Comprueba si un estudiante está inactivo en el curso, es decir si no ha entrado
 * todavía.
 *
 * Se obtiene en el array $alluserids todos los estudiantes matriculados mediante
 * blended_get_course_students_ids() y el parametro $selectactivestudents igual a false.
 * Se obtiene en el array $activeuserids todos los estudiantes activos mediante
 * blended_get_course_students_ids() y el parametro $selectactivestudents igual a true.
 * Con la función array_diff se guarda en el array $noactivemembers todos los valores
 * de $alluserids que no aparezcan $activeuserids, es decir, los estudiantes inactivos.
 * Por ultimo se comprueba si el parametro $userid está en el array $noactivemembers.
 *
 * @see blended_get_course_students_ids()
 *
 * @param int $userid identificador del objeto 'user' del miembro del equipo
 * @param int $course objeto de la instancia del curso
 * @return boolean|null devuelve true si el estudiante está inactivo y null si
 *                      está activo.
 ********************************************************************************/
function blended_is_not_active_student($userid,$course){

	// Obtiene en un array todos los estudiantes matriculados
	$alluserids    = blended_get_course_students_ids ($course, null, false);
	// Obtiene en un array todos los estudiantes activos
	$activeuserids = blended_get_course_students_ids ($course, null, true);
	// Devuelve un array que contiene todos los valores de $alluserids que no
	// aparezcan $activeuserids, es decir, los estudiantes inactivos
	$noactivemembers = array_diff($alluserids,$activeuserids);

	foreach($noactivemembers as $noactivemember){
		if($noactivemember == $userid){
			return true;
		}
	}
}
function blended_get_teams_members_by_id ($id_team=null,  $currentuser=null, $blended, $members, $membersignedup='false', $onlyidmember=false)
{
	return	blended_get_teams_members ($id_team, null, $currentuser,
	$blended, $members, $membersignedup, $onlyidmember);
}
function blended_get_teams_members_by_assignment ($id_assignment=null,  $currentuser=null, $blended, $members, $membersignedup='false', $onlyidmember=false)
{
	return	blended_get_teams_members (null, $id_assignment, $currentuser,
	$blended, $members, $membersignedup, $onlyidmember);
}


/********************************************************************************
 * Obtiene todos los miembros de los equipos que se están utilizando en la tarea
 * actual pero que fueron definidos en otra tarea. Cada miembro es reprensentado
 * por un objeto que contiene campos de su objeto 'user' y campos de otras tablas.
 *
 * Recibe un array bidimensional vacio pasado por referencia
 * (equipos para la tarea $id_assignment x miembros de cada equipo) en donde se
 * guardan indexados por el identificador de cada equipo todos los objetos 'user'
 * pertenecientes a los miembros de cada equipo.
 *
 * La diferencia con la función anterior es que los equipos definidos para la tarea
 * $id_assignment son los mismos que los que se crearon para otra tarea anterior
 * ($id_assignment_0), por eso el identificador de equipo id_team lo obtenemos de
 * la tabla "blended_grade" y no de la tabla "blended_team".
 *
 * También se accede a la tabla "assignment" para obtener el nombre de la tarea
 * (name_assignment_0) para la cual fueron creados originalmente los equipos que
 * ahora se utilizaran para la tarea $id_assignment.
 *
 * Si se recibe el objeto $currentuser se comprueba si el estudiante que llama a
 * esta función ya esta inscrito en un equipo para esa tarea, en tal caso se activa
 * el flag $membersignedup
 *
 * @global $CFG
 * @param int $id_assignment identificador de la tarea
 * @param object $currentuser objeto 'user' del usuario actual del módulo
 * @param array  $members (por referencia) array donde se guardarán los objetos user
 *               de los miembros del equipo
 * @param string $membersignedup flag para saber si el usuario actual del módulo
 *               está inscrito en algun equipo para la tarea
 * @return boolean true si array obtenido correctamente y false si error
 ********************************************************************************/
function blended_get_teams_members_from_grades ($id_assignment, $currentuser=null,$membersignedup='false') {
	
	global $CFG,$DB;
	
	$members = array();
	// SQL statement to get teams and their members -----------------------------

	$sql = "SELECT g.id_team AS id_team, g.id_assignment AS id_assignment,
                  an.name AS name_assignment_0, t.name_team AS name_team, 
                   tm.leader AS leader, u.id AS userid, u.firstname, u.lastname, 
                   u.idnumber, u.username, u.picture
             
                FROM {$CFG->prefix}blended_grade g
                
                    LEFT JOIN {$CFG->prefix}blended_team t ON t.id = g.id_team
                    LEFT JOIN {$CFG->prefix}blended_member tm ON t.id = tm.id_team
                    LEFT JOIN {$CFG->prefix}user u ON tm.userid = u.id
                    LEFT JOIN {$CFG->prefix}assignment a ON a.id = t.id_assignment
                    LEFT JOIN {$CFG->prefix}assign an ON an.id = t.id_assignment
                    
                    
 				WHERE g.id_assignment = $id_assignment
                ORDER BY t.name_team, u.lastname, u.firstname ";

	// Execute the SQL statement ------------------------------------------------
	if ($rs = $DB->get_recordset_sql($sql)) {

		//while ($row = rs_fetch_next_record($rs)) {
		foreach ($rs as $row) {
			$user = new object();
			// De table "user"
			$user->id                = $row->userid;
			$user->firstname         = $row->firstname;
			$user->lastname          = $row->lastname;
			$user->username          = $row->username;
			$user->idnumber          = $row->idnumber;
			$user->picture           = $row->picture;
			// De otras tablas
			$user->name_assignment_0 = $row->name_assignment_0;
			$user->leader            = $row->leader;
			$user->id_team           = $row->id_team;
			 
			//Matriz bidimensional (equipos x miembros)
			$members[$row->id_team][] = $user;

			// Si el estudiante actual ya esta inscrito en un equipo
			// no se le permitira la opcion de inscribirse en uno
			if($currentuser!= null && $currentuser->id == $user->id){
				$membersignedup = 'true';
			}
		}
		//rs_close($rs);
		$rs->close();
		return $members;
	}
	else{
		return $members;
	}

}
/********************************************************************************
 * Obtiene los equipos que fueron definidos para la tarea.
 *
 * Recupera de "blended_team" y guarda en un array los equipos
 * (los objetos 'team' correspondientes) definidos para la tarea $id_assignment.
 *
 * @global $CFG
 * @param int $id_assignment identificador de la tarea
 * @return  array|boolean array si obtiene los equipos correctamente o false si error
 ********************************************************************************/
function blended_get_teams_deprecated($id_assignment){
	global $DB;	
	if(!$teams = $DB->get_records('blended_team',array('id_assignment'=> $id_assignment))){
		return false;
	}
	else {
		return $teams;
	}
}

/********************************************************************************
 * Obtiene los equipos que se están utilizando para la tarea actual pero que fueron
 * definidos en otra tarea.
 *
 * Recibe un array vacio pasado por referencia en donde se guardan los equipos
 * (los objetos 'team' correspondientes) definidos para la tarea $id_assignment
 * pero que fueron creados originalmente para otra tarea distinta ($id_assignment_0).
 * Por eso el identificador de equipo (id) lo obtenemos de la tabla "blended_grade"
 * y no de la tabla "blended_team".
 *
 * @global $CFG
 * @param array $teams (por referencia) array donde se guardará todos los objetos
 *                     'team' definidos para la tarea $id_assignment
 * @param int $id_assignment identificador de la tarea
 * @return  boolean true si array obtenido correctamente y false si error
 ********************************************************************************/
function blended_get_teams_from_grades ($teams, $id_assignment) {
	global $CFG,$DB;

	// SQL statement to get teams and their members -----------------------------

	$sql = "SELECT g.id_team AS id, g.id_assignment AS id_assignment,
                   t.name_team AS name_team
            FROM {$CFG->prefix}blended_grade g
            LEFT JOIN {$CFG->prefix}blended_team t ON t.id = g.id_team
            WHERE g.id_assignment = $id_assignment";

	// Execute the SQL statement -------------------------------------------------

	if ($rs = $DB->get_recordset_sql($sql)) {
		//while ($row = rs_fetch_next_record($rs)) {
		foreach ($rs as $row) {	
			$team = new object();
			$team->id        = $row->id;
			$team->id_assignment = $row->id_assignment;
			$team->name_team  = $row->name_team;

			$teams[] = $team;
		}
		//rs_close($rs);
		$rs->close();

		if(empty($teams)){
			return false;
		}
		else {
			return $teams;
		}
	}
	else{
		return false;
	}
}

/********************************************************************************
 * OBTENCI�N Del nombre de equipo disponible para la tarea actual.
 *
 * Por defecto a los equipos se les asigna un nombre que consiste en un entero
 * que se incrementa secuencialmente. Busca cual es el primer entero libre en la
 * secuencia (que comienza desde 1) en el campo `name_team` de la tabla
 * "blended_team" para la tarea dada para asignarlo como nombre de equipo.
 *
 * @global $CFG
 * @param int $id_assignment identificador de la tarea
 * @param int $r (por referencia) donde se guardará el primer entero libre en la
 *               secuencia, es decir el nombre por defecto del siguiente equipo a
 *               insertar
 * @return  null
 ********************************************************************************/
function blended_get_name_team($id_assignment,$r){
	global $DB;
	$prevr=1;
	while($DB->get_records('blended_team',array('id_assignment'=> $id_assignment,'name_team'=> $r))){
		$r++;
		if($r == $prevr){
			$r++;
		}
	}
	$prevr=$r;
	return $prevr;
}

/********************************************************************************
 * Obtiene un nombre de equipo disponible (para la tarea actual) de entre los
 * nombres de los equipos que fueron definidos para otra tarea.
 *
 * En el caso de que los equipos para una determinada tarea se obtengan de los
 * equipos definidos anteriormente para otra tarea, la manera de buscar cual es
 * el primer entero libre en la secuencia (que comienza desde 1) en el campo
 * `name_team` de la tabla "blended_team" pasa por obtener previamente el id de
 * la tabla "blended_team" a partir del campo `id_team` de la tabla
 * "blended_grade" para la tarea en cuestión.
 *
 * @global $CFG
 * @param int $id_assignment identificador de la tarea
 * @param int $r (por referencia) donde se guardará el primer entero libre en la
 *               secuencia, es decir el nombre por defecto del siguiente equipo a
 *               insertar
 * @return  null
 ********************************************************************************/
function blended_get_name_team_from_grades($id_assignment, $r){
	global $CFG,$DB;
	 
	$prevr=1;
	while ($team = $DB->get_record_sql ("SELECT t.name_team
                                   FROM {$CFG->prefix}blended_grade g
                                   LEFT JOIN {$CFG->prefix}blended_team t ON t.id = g.id_team
                                   WHERE g.id_assignment = $id_assignment AND t.name_team = $r")){                        
	$r++;
	if($r == $prevr){
		$r++;
	}
                                   }
                                   $prevr=$r;
}

/********************************************************************************
 * Obtiene el objeto 'assignment' a partir de su clave primaria y si se recibe el
 * identificador del curso se le añade el campo cmidnumber.
 *
 * @param int $id_assignment el identificador del objeto 'gradeitem'
 * @return grade_item objeto 'grade item'
 ********************************************************************************/
function blended_get_assignment($id){
        global $DB;
        $grade_item = grade_item::fetch(array('id' => $id));
 	return $grade_item;
}

/********************************************************************************
 * Obtiene el nombre de la tarea a evaluar
 *
 * @param int $id
 * @return string el nombre de la tarea
 ********************************************************************************/
function blended_get_assignment_name($id)
{
	return blended_get_item_name(grade_item::fetch(array('id' => $id)));
}

/********************************************************************************
 * Obtiene el objeto 'user' asociado a un usuario de Moodle a partir de:
 * - su identificador codificado segun define Blended (id o idnumber de la tabla "user" o bien el campo
 *   personalizado de la tabla "user_info_data").
 * - a partir de su id de la tabla "user" directamente.
 *
 * @see blended_get_idvalue()
 *
 * @param int $id_member identificador codificado del usuario en blended
 * @param object $blended instancia del modulo
 * @param int $userid identificador de moodle del objeto 'user' del usuario
 * @return  object/null objeto 'user' o null si error
 ********************************************************************************/
function blended_get_user($id_member=null, $blended=null, $userid=null){
	global $DB;
	$is_user_info_data = false;
	// Modo de funcionamiento 1
	if($id_member!=null && $blended!=null && $userid==null){
		switch (substr($blended->idtype,0,1)) {
			case "0":
				$strfield="idnumber";
				break;
			case "1":
				$strfield="id";
				break;
			case "2":
				$is_user_info_data = true;
				$strfield='';
				break;
		}
		$value=$id_member;
		//$value = blended_get_idvalue ($id_member, $blended);
	}

	// Modo de funcionamiento 2
	if($userid!=null){
		$strfield = "idnumber";
		$value    = $userid;
	}

	// Modo de funcionamiento 1 case "ui"
	if($is_user_info_data){
		$fieldid = substr($blended->idtype,1);
		if (!$userid = get_field('user_info_data', 'userid' ,'data', $value,
                                 'fieldid', $fieldid))
		{
		return null;
        }
        else 
        {
        $strfield = "idnumber";
        $value = $userid;
        }
	}
//mtrace("<p>idmember=$id_member Searching value= $value in field: $strfield</p>");
	// Se obtiene el objeto 'user'
	if($id_member!=null || $userid!=null){
		
		$filterDNI="$strfield LIKE '$value"."_'";
		$filterDNIwithZero="$strfield LIKE '0$value"."_'";
		$filter="$strfield = '$value'";

		if(!($user = $DB->get_records_select('user', $filter)) &&
		!($user = $DB->get_records_select('user', $filterDNI)) &&
		!($user = $DB->get_records_select('user', $filterDNIwithZero))
		) //DNI in Spain has a checksum letter at the end and maybe a initial 0
		{

			print("<p>User not found. Searching: $filter  and $filterDNI</p>");

			return;
		}
		else
		if (count($user)>1)
		{
			print("<p>There are more than one user with the same $strfield value.</p><ul>");
			foreach($user as $us)
			{
				print("<li>$us->lastname, $us->firstname with email: $us->email</li>");
			}
			print("</ul><p> Please contact with the administrator.</p>");
			return;
		}
		else
		{
			return reset($user);
		}
	}
	else{
		return;
	}
	 
}

/********************************************************************************
 * Obtiene la calificación de un miembro de un equipo para una determinada tarea
 * de la tabla "blended_grade".
 *
 * El miembro es identificado mediante su codigo EAN de donde se extrae su
 * identificador (id o idnumber de la tabla "user") o bien a partir del id
 * asociado de la tabla "user" directamente.
 *
 * @see blended_get_user()
 *
 * @global $CFG
 * @param object $user  objeto 'user' del estudiante
 * @param int $id_assignment identificador de objeto 'assignment'
 * @param int $id_assignment_0
 * @param int $id_member identifiacdor del miembro del equipo
 * @param object $blended
 * @param int $userid identificador del objeto 'user' del estudiante
 * @return  int/null la nota del estudiante o null en caso de error al buscar al
 *                   estudiante
 ********************************************************************************/
function blended_get_user_grade($user=null, $id_assignment, $id_assignment_0=null, $id_member=null, $blended=null, $userid=null){
	global $CFG, $DB;

	if ($user==null && $userid==null)
		throw new Exception();
	if ($userid==null)
		$userid=$user->id;
	
	$id_assignment_0 = !empty($id_assignment_0) ? $id_assignment_0 : $id_assignment;

	$sql = "SELECT g.grade, g.rewrite AS rewrite
            FROM {$CFG->prefix}blended_grade g
            LEFT JOIN {$CFG->prefix}blended_team t ON t.id = g.id_team
            LEFT JOIN {$CFG->prefix}blended_member m ON m.id_team = t.id
            WHERE g.id_assignment = $id_assignment AND t.id_assignment = $id_assignment_0 
                  AND m.userid = ".$userid;

 	$grade = $DB->get_record_sql($sql);


	return $grade;
}



/********************************************************************************
 * Obtiene la calificación máxima que se puede dar a una determinada tarea.
 * Por defecto la máxima calificación es 10.
 *
 * @see grade_get_grade_items_for_activity()
 *
 * @param stdClass $activity_cm course module de la actividad
 * @return  int nota máxima
 ********************************************************************************/
function blended_get_grademax ($activity_cm)
{
	$grade_item=grade_get_grade_items_for_activity($activity_cm,true);
	
	foreach($grade_item as $grade){
		return $grade->grademax;
	}
}




// ERROR MANAGEMENT -------------------------------------------------------------



/********************************************************************************
 * Muestra por pantalla los mensajes de error cuando ha ocurrido algún error al
 * insertar nuevos miembros de equipo en las tablas.
 *
 * Hay tres tipos de errores:
 *
 * - Estudiante con identificador no válido.
 * - Estudiante no matriculado en el curso.
 * - Estudiante ya inscrito en otro equipo para la misma tarea.
 *
 * La función genera un string donde se listán clasificados por cada uno de los tres
 * posibles errores los identificadores de aquellos miembros de equipo y a que equipo
 *  pertenecen que hayan ocasiado el error.
 *
 * Se hace control del número de equipos y el número de miembros por equipo que han
 * ocasionado error para generar las distintas frases con la correcta concordancia
 * de singular/plural (entre 'sujetos' y 'verbos').
 *
 * @param array array bidimensional indexado por los identificadores de los equipos
 *              que han dado error al ser insertados junto con los código de error
 *              de cada miembro del equipo que ha ocasionado algún tipo de error.
 * @return string mensaje que se mostrará por pantala con la lista de todos los
 *                errores que han ocurrido al insertar los miembros de equipo
 ********************************************************************************/
function blended_get_error_alert($array_return){
	if(!empty($array_return)){
		 
		$str_final_1= "";
		$str_final_2= "";
		$str_final_3= "";

		$str_alert_1  = get_string('alert_error_1', 'blended');
		$str_alert_2  = get_string('alert_error_2', 'blended');
		$str_alert_9  = get_string('alert_error_9', 'blended');
		$str_alert_10 = get_string('alert_error_10', 'blended');

		// Comprobamos si hay más de un equipo
		$morethanone = ( count($array_return)>1 ) ? true : false ;

		foreach($array_return as $id_team=>$team){

			if(count($team) > 1){

			}

			$str_id_member_1 = "";
			$str_id_member_2 = $str_id_member_1;
			$str_id_member_3 = $str_id_member_1;
			$array_error_code = array();
			$i=0;
			$j=0;
			$k=0;
			foreach($team as $member){

				$error_code = substr($member,0,2);
				$array_error_code[]=$error_code;
				$id_member = substr($member,2);

				if($error_code == -1){
					$i++;
					$str_id_member_1 = $str_id_member_1.$id_member.", ";
				}
				else if($error_code == -2){
					$j++;
					$str_id_member_2 = $str_id_member_2.$id_member.", ";
				}
				else if($error_code == -3){
					$k++;
					$str_id_member_3 = $str_id_member_3.$id_member.", ";
				}
			}

			$morethanone_1 = ($i>1) ? true : false ;
			$morethanone_2 = ($j>1) ? true : false ;
			$morethanone_3 = ($k>1) ? true : false ;

			 
			$str1=$str_alert_1;
			$str2=$str_alert_2;
			$str_inicial_1 = ($i>1) ? $str1 : $str2 ;
			$str_inicial_2 = ($j>1) ? $str1 : $str2 ;
			$str_inicial_3 = ($k>1) ? $str1 : $str2 ;
			 
			$str_inicial_1 .= $str_id_member_1 ;
			$str_inicial_2 .= $str_id_member_2 ;
			$str_inicial_3 .= $str_id_member_3 ;
			 
			$array_error_code = array_unique($array_error_code);
			 
			foreach ($array_error_code as $error_code){
				if($error_code == -1){
					$s = ($i>1) ? "s" : "" ;
					$str_inicial_1 = substr($str_inicial_1,-strlen($str_inicial_1),-2);
					$str_inicial_1 .= get_string('alert_error_3', 'blended', $s).$id_team."<br>";
					$str_final_1.=$str_inicial_1;
				}
				else if($error_code == -2){
					$s = ($j>1) ? "s" : "" ;
					$str_inicial_2 = substr($str_inicial_2,-strlen($str_inicial_2),-2);
					$str_inicial_2 .= get_string('alert_error_3', 'blended', $s).$id_team."<br>";
					$str_final_2.=$str_inicial_2;
				}
				else if($error_code == -3){
					$s = ($k>1) ? "s" : "" ;
					$str_inicial_3 = substr($str_inicial_3,-strlen($str_inicial_3),-2);
					$str_inicial_3 .= get_string('alert_error_3', 'blended', $s).$id_team."<br>";
					$str_final_3.=$str_inicial_3;
				}
			}
		}
		 
		// Se concatenan todos los mensajes de error
		$strinserted = "";
		if(!empty($str_final_1)){
			$strinserted .= get_string('alert_error_4', 'blended',$str_final_1);
		}
		if(!empty($str_final_2)){
			if ($morethanone_2)
			$strinserted .= get_string('alert_error_5', 'blended',$str_final_2);
			else
			$strinserted .= get_string('alert_error_6', 'blended',$str_final_2);
			 
		}
		if(!empty($str_final_3)){
			if ($morethanone_3)
			$strinserted .= get_string('alert_error_7', 'blended',$str_final_3);
			else
			$strinserted .= get_string('alert_error_8', 'blended',$str_final_3);;
		}
		if ($morethanone || $morethanone_1 || $morethanone_2 || $morethanone_3)
		$strinserted.= $str_alert_9;
		else
		$strinserted.= $str_alert_10;
		 
		return $strinserted;

		 
	}

}



// SORT TEAMS -------------------------------------------------------------------



/********************************************************************************
 * Ordena el nombre de los equipos. Si $a y su atributo es menor o igual que $b y
 * su atributo devuelve 1. En caso contrario devuelve -1
 *
 * @param object $a
 * @param object $a
 * @return int
 ********************************************************************************/
function blended_cmpNameTeamInt($a,$b){
	return ($a->name_team <= $b->name_team) ? 1 : -1;
}

/********************************************************************************
 * Ordena el nombre de los equipos. Usa sort() para comparación alfabetica simple.
 * Si los nombres conmutan de posición devuelve 1 sino -1.
 *
 * @param object $a
 * @param object $a
 * @return int
 ********************************************************************************/
function blended_cmpNameTeamAlphaInt($a,$b){
	// Convierte a minisculas para asegurar un comportamiento mas robusto
	$sortable = array(strtolower($a->name_team),strtolower($b->name_team));
	$sorted = $sortable;
	sort($sorted);

	return ($sorted[0] == $sortable[0]) ? 1 : -1;
}
