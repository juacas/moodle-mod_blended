<?php
require_once(dirname(__FILE__)."/../blended_locallib.php");

define("TEAMS_BY_TEACHERS",0);
define("TEAMS_BY_STUDENTS",1);
define("TEAMS_BY_STUDENTS_WITH_LEADER",2);
/**
 * Generate the value to be included in the form fields
 * @param stdClass $member user
 */
function blended_generate_memberid_field($member, $blended) {
    $id = blended_gen_idvalue($member, $blended);
    $member_field_id = "$id|" . fullname($member);
    return $member_field_id;
}

/**
 * Obtains the id of the user from the formatted content of the fields
 * @param unknown $field
 */
function blended_extract_memberid_field($field) {
    $parts = explode('|', $field);
    return $parts[0];
}

function blended_include_autocomplete_support($context, $blended) {
    global $PAGE;
    /**
     * Include Scripts
     */
    $dependencies = array('autocomplete',
        'autocomplete-filters', 'autocomplete-highlighters');
    $module = array(
        'name' => 'M.local_blended',
        'fullpath' => '/mod/blended/module.js',
        'requires' => $dependencies,
    );
    // 	$jsarguments['cfg']['query_string'] = $query;
    // 	$jsarguments['cfg']['intuitel_proxy'] = $url;
    $users = get_enrolled_users($context);
    $jsarguments = array();
    foreach ($users as $user)
        $jsarguments['param']['userlist'][] = blended_generate_memberid_field($user, $blended);
    $jsarguments['param']['other'] = "prueba";

    $PAGE->requires->js_init_call('M.local_blended.init', $jsarguments, true, $module);
//    	$PAGE->requires->css('/mod/blended/css/autocomplete.css');
}

/* * ******************************************************************************
 * COMPRUEBA la existencia de los usuarios introducidos y  repetici�n de
 * los identificadores de los mismos en distintos grupos
 *
 * Recibe el array 'members_group' y el dato 'action' con el que se identifica
 * desde donde proviene la petici�n de comprobaci�n.
 *
 * Primero se recogen todos los usuarios registrados en el curso en el array 'lista_usuarios'.
 * Posteriormente se introducen en el array 'lista_idnumbers' los identificadores de los usuarios.
 * Este array se usar� para comprobar la existencia del usuario introducido en el sistema
 * 	- Si el usuario introducido existe se introducir� en el vector 'miembros_grupos',
 *  	  comprobaci�n que ser� distinta seg�n el valor de 'action'
 * 	- Si el usuario no existe no se introducir� en el vector
 *
 * Una vez comprobado si los usuarios existen, se comprueba si hay alg�n identificador repetido
 * 	- Para cada miembro del vector 'miembros_grupos' se introduce
 * 	  en el array 'contador' el numero de veces que aparece
 *
 * @see blended_clean_idnumber()
 *
 * @param array $members_group array con los miembros introducidos en los grupos
 * @param string $action constante que define de donde proviene la petici�n de comprobaci�n
 *
 * @return  array con el numero de veces que se repite cada estudiante introducido
 * ****************************************************************************** */

function blended_num_members($contextCourse, $members_groups, $action = null) {

    global $DB;

    //Lista de todos los usuarios
    $lista_usuarios = get_enrolled_users($contextCourse, '', 0, 'u.id,u.idnumber');

    $contador = array();
    //Comprobación de la existencia de los usuarios pasados en 'members_group' en el sistema
    foreach ($members_groups as $members) {
        foreach ($members as $member) {
            foreach ($lista_usuarios as $user) {
                if ($action == 'saveteams') {//si la peticion proviene de saveteam
                    error("debug this!!");
                    $usuario_idnumber = substr($user->idnumber, 0, 8); // TODO Blended_clean ????
                    if ($usuario_idnumber == $member) {
                        if (!key_exists($user->id, $contador))
                            $contador[$user->id] = 1;
                        else
                            $contador[$user->id] ++;
                    }
                }
                else {//Si proviene de teamsmanagement
                    if ($user->id == $member->id) {
                        if (!key_exists($user->id, $contador))
                            $contador[$user->id] = 1;
                        else
                            $contador[$user->id] ++;
                    }
                }
            }
        }
    }
    return $contador;
}

/**
 * 
 * @global $DB $DB
 * @param grade_item $item
 * @param include_members
 * @return array of groups_group extended with $group->leaderid  $group->members
 */
function blended_get_teams(grade_item $item, $include_members) {
    $groupingid = blended_get_groupingid($item);
    if ($groupingid) {
        $teams = groups_get_all_groups($item->courseid, 0, $groupingid);
    } else {
        $teams = array();
    }
    if ($include_members) {
        foreach ($teams as $teamid => $team) {
            $team = blended_get_team($team, true);
        }
    }
    // add leader information
    global $DB;
    $keys = array_keys($teams);
    $leaders = $DB->get_records_list('blended_team', 'id_team', $keys);
    foreach ($leaders as $team_info) {
        $teams[$team_info->id_team]->leaderid = $team_info->userid_leader;
    }
    return $teams;
}

/**
 * 
 * @param type $teamid
 * @return array(stdClass) user records
 */
function blended_get_team_members($teamid) {
    return groups_get_members($teamid);
}

function blended_set_team_leaderid($teamid, $leaderid) {
    global $DB;
    $team = $DB->get_record('blended_team', array('id_team' => $teamid));
    if (!$team) {
        $team = new stdClass();
        $team->id_team = $teamid;
        $team->userid_leader = $leaderid;
        $DB->insert_record('blended_team', $team);
    } else {
        $team->userid_leader = $leaderid;
        $DB->update_record('blended_team', $team);
    }
}

function blended_get_team_leaderid($teamid) {
    global $DB;

    $team_leader = $DB->get_field('blended_team', 'userid_leader', array('id_team' => $teamid));
    return $team_leader;
}

/* * ******************************************************************************
 *
 * @param \stdClass $team objeto 'team'
 * @param int $id_member identificador del objeto 'member'
 * @return null
 * ****************************************************************************** */

function blended_remove_team_member($team, $id_member) {
    global $DB;
    $userid_leader = $team->leaderid;
    $members = blended_get_team_members($team->id);
    if (isset($members[$id_member])) {
        $deleted = groups_remove_member($team->id, $id_member);
        if ($userid_leader == $id_member) {  // if removed user id the leader removes the leader
            blended_set_team_leaderid($team->id, null);
            $team->leaderid = null;
        }
    }
}

/**
 * 
 * @global $DB $DB
 * @param int|stdClass $teamorid if stdClass the object is complemented with leaderid and members
 * @param boolean includemembers if true load members with array or user records
 * @return stdClass  with group fields plud leaderid
 */
function blended_get_team($teamorid, $includemembers = false) {
    if ($teamorid instanceof stdClass) {
        $team = $teamorid;
    } else {
        $team = groups_get_group($teamorid);
    }

    if ($team) {
        global $DB;
        $meta = $DB->get_record('blended_team', array('id_team' => $team->id));
        if ($meta) {
            $team->leaderid = $meta->userid_leader;
        } else {
            $team->leaderid = null;
        }
        if ($includemembers) {
            $members = groups_get_members($team->id);
            $team->members = $members;
        } else {
            $team->members = array();
        }
    }
    return $team;
}

function blended_grade_team(grade_item $item, $team, $newfinalgrade) {
    global $DB;
    if (!isset($team) || !isset($item)) {
        return;
    }
    if ($newfinalgrade == null) {
        $DB->delete_records('blended_grade', array('id_item' => $item->id, 'id_team' => $team->id));
    } else {
        $gradeteam = $DB->get_record('blended_grade', array('id_item' => $item->id, 'id_team' => $team->id));
        if (!$gradeteam) {
            $gradeteam = new stdClass();
            $gradeteam->id_item = $item->id;
            $gradeteam->id_team = $team->id;
            $gradeteam->grade = $newfinalgrade;
            $gradeteam->rewrite = false;
            $id = $DB->insert_record('blended_grade', $gradeteam);
        } else {
            $gradeteam->grade = $newfinalgrade;
            $DB->update_record('blended_grade', $gradeteam);
        }
    }
}

function blended_enrol_user_to_team($id_team, $userid, grade_item $item, $blended) {
    global $DB, $OUTPUT;
    //Comprobacion de que el alumno  no éste en otro equipo:
    $grouping = blended_get_grouping($item, $blended);
    $groups = blended_get_teams($item, true);

    foreach ($groups as $group) {
        foreach ($group->members as $tid => $member) {
            if ($userid == $tid) {
                $a = $group;
                echo $OUTPUT->notification("ya esta inscrito en el equipo {$a->name}");
                return false;
            }
        }
    }

    groups_add_member($id_team, $userid);
    if (!isset($groups[$id_team]->leaderid)) { // First coming becomes leader
        blended_set_team_leaderid($id_team, $userid);
        $a = new stdClass();
        $a->username = fullname($DB->get_record('user', array('id' => $userid)));
        $a->teamname = blended_get_team($id_team)->name;
        echo $OUTPUT->notication(get_string('userpromotedtoleader','blended',$a));
    }
    // TODO regrade
    //Objeto equipo
//	$team = new object ();
//	$team->id = $id_team;
//	$leader = 0;
//	$checknummembers = true;
//	$teams = $DB->get_records('groups');
//	foreach($teams as $team){
//		if($team->id==$id_team){
//			$s=$team;
//			break;
//		}
//	}
//	$return = blended_insert_team_member ( $idvalue, $team, $id_assignment, $course, $blended, $leader, $checknummembers );
    //Actualizaci�n del agrupamiento
//	blended_update_grouping ( $course->id, $s->name, $idvalue, null, true, $s->id );
    //blended_update_teams_by_groups($id_assignment);
    return true;
}

/* * ******************************************************************************
 * BORRADO del agrupamiento para la tarea elegida
 *
 * Esta funci�n est� encargada de borrar los registros de las tablas de los agrupamientos
 * para la tarea elegida con identificador 'activitycm_or_cmid'
 *
 * @see get_coursemodule_from_id()
 * @see groups_get_all_groups()
 * @see groups_delete_group()
 *
 * @param int $itemid int con el identificador de la grade_item
 * 
 * ****************************************************************************** */

function blended_delete_teams(grade_item $item) {
    global $DB;
    $delgroups = blended_get_teams($item, false);
    foreach ($delgroups as $delgroup) {
        blended_delete_team($delgroup);
    }
    $DB->delete_records('blended_assign_grouping', array('id_item' => $item->id));
}

function blended_delete_team($teamid) {
    groups_delete_group($teamid);
    global $DB;
    $DB->delete_record('blended_team', array('id_team' => $teamid));
}
/********************************************************************************
 * INSERTA un nuevo grupo en el agrupamiento junto con sus miembros
*
* Recibe los arrays 'mem' y 'temaname' con los datos a introducir y
* el identificador de agrupamiento 'groupingid' y del curso 'courseid'
* 
* Tras realizar una comprobaci�n del numero de miembros que posee cada grupo:
*	- Si es mayor que uno 'mem' es un array
*	- Si es 1 puede ser:
*		+ Un array si la orden procede de un profesor
*		+ Un elemento si la orden procede de un alumno
*
* Se busca la existencia de cada miembro en la base de datos y si existe se introducir� en el equipo
* Por �ltimo se le asigna al grupo el agrupamiento mendiante 'groupingid'
*
* @see groups_create_group()
* @see blended_clean_idnumber()
* @see groups_add_member()
* @see groups_assign_grouping()
* 
* @param int $courseid identificador del curso
* @param string $teamname nombre del equipo
* @param array $mem array con los miembros introducidos en el grupo
* @param int $groupingid identificador del agrupamiento
*
* @return $idgroup con el id del grupo creado
********************************************************************************/
function blended_add_new_group($courseid,$teamname,$mem,$groupingid){
	
	global $DB;
	if ($teamname==''){
            $teamname='Team';
        }
	$data=new object();
	$data->courseid= $courseid;
	$data->name= $teamname;
	$data->description_editor['text']=' ';
	$data->description_editor['format']=1;
	$idgroup=groups_create_group($data);
	$memid=new object();
	
        foreach ($mem as $id => $member) {
            groups_add_member($idgroup,$member->id);
        }	
	groups_assign_grouping($groupingid, $idgroup);
	return $idgroup;
}

/**
 * 
 * @param int $item_id
 * @param boolean $forceid teamids are mandatory in the form, otherwise only team_name are mandatory
 * @param null|array(\stdClass) $users array of user records indexed by userid: members will contain these records
 * @return array of team(name,id,members array(id=>user_record),id_activity) members will be a sequential list of userids if no $users are provided
 */
function blended_get_teams_from_form($item_id, $forceid = true, $users = null) {
    $teams = array();

    for ($i = 0;; $i ++) {
// 		$teamname [$i] = $_POST ['team_' . $i . '_name'];
// 		$team_id [$i] =$_POST['team_'. $i .'_id'];
        $team_id_param = optional_param('team_' . $i . '_id', null, PARAM_INT);
        $team_name_param = optional_param('team_' . $i . '_name', '', PARAM_ALPHANUMEXT);
        if (($team_id_param === null && $team_name_param === '') || ($team_id_param === null && $forceid === true)) {
            break;
        } else {
            $team_obj = new stdClass();
            $team_obj->name = $team_name_param;
            $team_obj->id = $team_id_param;
            $team_obj->members = array();
            $team_obj->id_activity = $item_id;
            for ($j = 0;; $j ++) {
                $memberid = optional_param('team_' . $i . '_member_' . $j, null, PARAM_INT);
                if (isset($memberid)) {
                    if ($memberid != 0) {
                        if ($users) {
                            $team_obj->members[$memberid] = $users[$memberid];
                        } else {
                            $team_obj->members[$j] = $memberid;
                        }
                    }
                } else {
                    break;
                }
            }
            $teams[$i] = $team_obj;
        }
    }
    return $teams;
}

function blended_get_scale_array(grade_item $item) {
    // Calificación máxima
    $grademax = $item->grademax;
    switch ($item->gradetype) {
        case 1: // numeric
            //$gradearray = array_combine ( range ( 0, $grademax ), range ( 0, $grademax ) );
            $gradearray = make_grades_menu($grademax);
            break;
        case 2: // scale
//            $scale = grade_scale::fetch(array('id'=>$item->scaleid));
//            $scale->load_items();
//            $gradearray = $scale->scale_items;
            $gradearray = make_grades_menu(-$item->scaleid);
            break;
        case 3: // text
            $gradearray = null;
            break;
    }
    return $gradearray;
}

/* * ******************************************************************************
 * Obtiene la calificación de un equipo para un determinada tarea.
 *
 * Primero se obtiene la nota del grupo de la tabla "blended_grade". Despues se
 * compara con cada una de las notas individuales de los miembros del equipo
 * obtenidas del libro de calificaciones.
 *
 * Si a algún miembro del equipo se le ha modificado su nota indivual (en el libro
 * de calificaciones), resultando distinta a la de grupo (guardada en "blended_grade")
 * se activa el flag $alert pasado por referencia.
 *
 * Si se cambian manualmente las notas individuales de varios miembros del equipo
 * en el libro de calificaciones a un mismo valor, y el número de estos miembros
 * con su nota modificada es mayor que la mitad de los miembros del equipo esta
 * nueva nota pasa a ser la nueva nota global del equipo en la tabla "blended_grade".
 *
 * @see blended_count_repeat_values()
 *
 * global $CFG
 * @param int $id_team identificador de objeto 'team'
 * @param int $id_assignment identificador de objeto 'assignment'
 * @param boolean $alert (por referencia) true si alguna nota individual de los
 *                miembros del equipo es distinta a la de grupo
 * @return array(float,boolean) la nota de grupo,  true si alguna nota individual de los
 *                miembros del equipo es distinta a la de grupo
 * ****************************************************************************** */

function blended_get_team_grade($team, $item) {
    global $DB;
    // Obtenemos la nota de la tabla "blended_grade"
    $blendedgrade = $DB->get_record('blended_grade', array('id_team' => $team->id, 'id_item' => $item->id));
    if (!$blendedgrade) {
        return array(null, false);
    }
    $members = groups_get_members($team->id);
    // Compara con las notas de los miembros del equipo obtenidas del gradebook
//        $grades = grade_get_grades($team->courseid, $item->itemtype, $item->itemmodule, $item->iteminstance,array_keys($members));
    if (count($members)==0){
        $bookgrades = array();
    }else{
        $bookgrades = grade_grade::fetch_users_grades($item, array_keys($members), true);
    }
    $grade = isset($blendedgrade->grade) ? $blendedgrade->grade : null;
    list($averagebookgrade, $alert) = blended_count_repeated_grades($grade, $bookgrades);

    if (!isset($blendedgrade) && isset($averagebookgrade) && $grade != $averagebookgrade) {
        $blendedgrade->grade = $averagebookgrade;

        if (!$DB->update_record("blended_grade", $blendedgrade)) {
            print("Encountered a problem trying to update grades.<br>");
        }
    }


    return array($blendedgrade, $alert);
}

/* * ******************************************************************************
 * Comprueba si ha habido alguna modificación de la calificación individual de un
 * miembro de un equipo en el gradebook.
 *
 * Recibe un array con todas las calificaciones individuales (obtenidas del libro
 * de calificaciones de Moodle) de todos los miembros del equipo y comprueba si
 * todas ellas son iguales a la del equipo (obtenida de la tabla "blended_grade").
 *
 * Si se cambian manualmente las notas individuales de varios miembros del equipo
 * en el libro de calificaciones a un mismo valor, y el número de estos miembros
 * con su nota modificada es mayor que la mitad de los miembros del equipo esta
 * nueva nota pasa a ser la nueva nota global del equipo en la tabla "blended_grade".
 *
 * @param int $grade nota del equipo obtenida de la tabla "blended_grade"
 * @param array $grades_array array con todas las notas individuales de los miembros
 *              del equipo obtenidas del libro de calificaciones
 * @return  array(float, boolean)  $teambookgrade, si nota individual modificada, true nota modificada o false si todas las notas
 *                  de los miembros igual a la de equipo
 * ****************************************************************************** */

function blended_count_repeated_grades($grade, $grades_array) {

    $matches_array = array();
    $no_matches_array = array();
    $finalgrades_array = array();

    foreach ($grades_array as $g) {
        if (isset($g->rawgrade)) {
            $finalgrades_array[] = $g->rawgrade;
            if ($g->rawgrade == $grade) {
                // Si la nota es igual a la de grupo
                $matches_array[] = $grade;
            } else {
                // Si la nota es diferente a la de grupo
                $no_matches_array[] = $g->rawgrade;
            }
        } else { // if rawgrade is null there is no conflict
            $matches_array[] = $grade;
        }
    }

    // Se cuentan los elementos de cada vector
    $number_of_grades = count($grades_array);
    $number_of_matches = count($matches_array);
    $number_of_no_matches = count($no_matches_array);

    // Nota de equipo a partir del libro de calificaciones: si la mayoria de
    // las calificaciones individuales se han puesto al mismo valor
    //$teambookgrade = ($number_of_matches > $number_of_no_matches) ? $matches_array[0] : $no_matches_array[0];
    // Calcular moda
    $cuenta = array_count_values($finalgrades_array);
    arsort($cuenta);
    $teambookgrade = count($cuenta) < count($finalgrades_array) ? key($cuenta) : null;

    if ($number_of_grades != $number_of_matches) {
        $alert = true;
    } else {
        $alert = false;
    }
    return array($teambookgrade, $alert);
}

function blended_get_grades_from_form($teams) {
    $updategrades = array();
//Bucle TEAMS
    foreach ($teams as $i => $team) {
        //Recogida de los parametros 'gradeid', 'gradelist' , 'grade'
        $id_grade = optional_param('team_' . $i . '_gradeid', null, PARAM_INT);
        $glvalue = optional_param('team_' . $i . '_gradelist', null, PARAM_NUMBER); // índice en la lista desplegable "Calificación"
        $gvalue = optional_param('team_' . $i . '_grade', null, PARAM_RAW_TRIMMED); // valor del campo de texto "Calificación"
        $rewriteteam = optional_param("rewrite_team_$i", null, PARAM_ALPHANUM);
//     if ($id_grade === null){
//         continue;
//     }
        // Objeto grade
        $grade = new object ();
        $grade->id = $id_grade;
        $grade->id_team = $team->id;
        $grade->id_item = $team->id_activity;

        if ($glvalue == -1 ) {
            $grade->grade = null;
        }else{
            $grade->grade = $glvalue;
        }

        if ($grade->grade != null) {
            $grade->rewrite = $rewriteteam == 'rewrite';     // TODO check this variable???
            $grade->override_grades = $rewriteteam == 'rewrite';
        }
        $teams[$i]->grade = $grade;
    }//Fin bucle TEAMS
    return $teams;
}

/* * ******************************************************************************
 * INSERTA nuevos equipos en las tablas asociadas a los agrupamientos. Cada equipo consta de
 * un objeto 'team',que contiene array 'members' y un objeto 'grade'.
 *
 * Recibe el array unidimensional 'teams' con todos los objetos 'team' que contiene un
 * array 'members'  con los identificadores de los miembros
 * asociados a cada 'team'  y un campo 'grade'
 *
 * - Cuando recibe los arrays 'teams' y 'members', inserta uno a uno cada objeto
 *   'team' junto con el array de los identificadores de todos sus miembros
 *   asociados. Guarda en el array 'insertedteams' cada objeto team insertado,
 *   'insertedteam' (y despues recuperado para saber cual es la clave primaria
 *   (id) asignada por moodle y poder completar asi los objetos 'member' y 'grade'
 *   antes de insertarlos).
 * - Solo insertar� aquellos equipos que posean alg�n miembro que exista en la base
 * 	 de datos puesto que lo miembros que no exiten no ser�n guardados
 *
 *   Si hay error al insertar alguno de los elementos del array 'members':
 *
 *   - Se guarda en el array $array_return indexado por el nombre del equipo, el
 *     código de error que contiene el tipo de error junto con el identificador
 *     de cada uno de los miembros que ha dado error.
 *
 *   - Se borra el objeto 'team' recien insertado y todos los elementos de 'members'
 *     asociados al mismo que hayan sido insertados.
 *
 * - Cuando solo recibe el array 'grades' inserta uno a uno cada objeto 'grade' en
 *   la tabla correspondiente.
 *
 * - Cuando recibe todos los arrays, primero inserta 'teams' y 'members' y genera
 *   el array 'insertedteams' que lo utiliza posteriormente para completar cada
 *   objeto 'grade' del array 'grades' antes de insertarlos todos de uno en uno.
 *
 * Además actualiza 'assigment_submissions' y el libro de calificaciones (gradebook).
 *  *
 * @see blended_insert_team()
 * @see blended_clean_idnumber()
 * @see blended_add_new_group()
 * @see blended_delete_all()
 * @see blended_update_assignment_and_gradebook()
 *
 * @param array $teams array con los objetos 'team' de la tabla "blended_team"
 * @param array $members array bidimensional indexado con el identificador del
 *              equipo y con los identificadores de los miembros asociados
 * @param object $course objeto de la instancia del curso
 * @param object $blended objeto de la instancia del modulo blended
 * @param int $leader bandera para saber si un miembro es líder de su equipo
 * @param int $itemid identificador de la tarea actual
 * @param int $original_itemid identificador de la tarea de la cual se extrajeron los equipos
 * @param int $id el identificador del módulo del curso
 * @param int $currentuserid el objeto de la tabla "user" perteneciente al usuario
 *            que esta usando el módulo
 * @param int $groupingid el identificador del agruapmiento que se esta actualizando
 * @return  null|array nada si equipos insertados correctamente o array con los códigos
 *                     de error y los identificadores de los estudiantes no insertados
 * ****************************************************************************** */

function blended_insert_teams($teams, $course, $blended = null, $leader = null, grade_item $item = null, $original_itemid = null, $id = null, $currentuserid = null, $groupingid) {

    global $DB;
    $insertedteam = new object();
    $insertedteams = array();
    $array_return = array();
    $lista_id = array();

    foreach ($teams as $team) {
        // $insertedteam=blended_insert_team($team, null, $insertedteam);
        // $insertedteams[$index]= $insertedteam;
        $memberids = $team->members;
        $members=array();
        if ($memberids === null) {
            $memberids = array();
        }
        foreach ($memberids as $memberid) {//lista de dni sin la letra
            $user = $DB->get_record('user', array('id' => $memberid));
            $members[]=$user;
            $lista_id[] = blended_clean_idnumber($user->id);
        }
        /*
         * Actualizo los grupos actualizando el nombre del grupo y volviendo a introducir
         * los miembros comprobando que el usuario introducido existe
         */
        $contador_miembros = 0;
        $contador_miembros_id = 0;
        $miembros_introducir_validos = array();
        foreach ($members as $member) {
            if (in_array($member->id, $lista_id)) {//compruebo para no introducir ningun DNI que no exista en la base de datos
                $miembros_introducir_validos[] = $member;
            }
        }

        //Si existe el id del agrupamiento:
        if ($groupingid !== null) {
            $groupid = blended_add_new_group($course->id, $team->name, $miembros_introducir_validos, $groupingid);
            // 							blended_update_assignment_and_gradebook($itemid, $original_itemid, $miembros_introducir_validos, $blended,
            // 							$id, $currentuserid);
//                   if($contador_miembros==count($members)){//Si existe un grupo vac�o o con todos los dni no v�lidos se borrar�
//                        groups_delete_group($groupid);
//                    }
        } else if ($groupingid === null) {//Si no existe el id del agrupamiento
            $groupingid = blended_get_groupingid($item);
            blended_add_new_group($course->id, $team->name, $miembros_introducir_validos, $groupingid);
        }
//                $ys=$DB->get_records('groupings_groups',array('groupingid'=>$groupingid));
//                $m=array();
//                foreach($ys as $u=>$y){
//                    $m[$u]=$DB->get_records('groups_members',array('groupid'=>$y->groupid));
//                }
//                $mbss=array();
//                foreach($m as $fs){
//                    if(!empty($fs)){
//                        foreach($fs as $t=>$f){
//                            $mb=$DB->get_record('user',array('id'=>$f->userid));
//                            $mbss[$t]=blended_clean_idnumber($mb->idnumber);
//                        }
//                    }
//                }
        // Si hay ERROR al insertar los miembros se borra el equipo insertado
        if (!empty($return)) {
            // Array con los códigos de error ocurridos
            $array_return[$insertedteam->name] = $return;
            // Deprecated: Se borra el equipo insertado
            // TODO: comprobar que no es problematico dejar el equipo con algún usuario sin insertar
            //  solo borra si el equipo a resultado vacío.
            if (count($return) == 0) {
                blended_delete_all($insertedteam->id);
                unset($insertedteams[$index]);
            }
        }
    }

    // Si hubo ERROR
    if (!empty($array_return)) {
        return $array_return;
    }
}

function blended_create_unique_grouping($grouping_name, $course) {
    $data = new object();

    if ($grouping_name == '') {
        $grouping_name = get_string('newgrouping', 'group');
    }
    $data->name = $grouping_name;
    $data->courseid = $course->id;
    $data->description_editor ['text'] = ' ';
    $data->description_editor ['format'] = 1;
    // Check existence of grouping name
    $count = 1;
    do {
        $prev_grouping = groups_get_grouping_by_name($course->id, $data->name);
        if ($prev_grouping) {
            $data->name = $grouping_name . " ($count)";
        } else {
            $data->name = $grouping_name;
        }
        $count++;
    } while ($prev_grouping);

    $groupingid = groups_create_grouping($data);
    return $groupingid;
}

/**
 * 
 * @param grade_item $item
 * @param stdClass $grouping
 */
function blended_assign_grouping(grade_item $item, stdClass $grouping, $leader, $maxmembers) {
    global $DB;
    if ($item ===null){
        return false;
    }
    $prev_groupingid = blended_get_groupingid($item);
    if ($prev_groupingid) {
        $DB->delete_records('blended_assign_grouping', array('id_item' => $item->id));
        // TODO deal with previous grades in gradebook
        // TODO unlock grades
    }
    $data = new stdClass();
    $data->id_item = $item->id;
    $data->id_grouping = $grouping->id;
    $data->leader = $leader;
    $data->maxmembers = $maxmembers;

    $DB->insert_record('blended_assign_grouping', $data);
}

/**
 * Returns the groupingid of a grouping with the id specified for the course.
 *
 * @category group
 * @param int $courseid The id of the course
 * @param int $idnumber id of the group
 * @return grouping object
 */
function groups_get_grouping_by_id($courseid, $id) {
    if (empty($id)) {
        return false;
    }
    $data = groups_get_course_data($courseid);
    foreach ($data->groupings as $grouping) {
        if ($grouping->id == $id) {
            return $grouping;
        }
    }
    return false;
}
function blended_restrict_items($blended,$selecteditems)
{
    global $DB;
    //remove all records
    $DB->delete_records('blended_items',array('id_blended'=>$blended->id));  
    foreach ($selecteditems as $itemid){
        $DB->insert_record('blended_items',array('id_blended'=>$blended->id,'id_item'=>$itemid));
    }
}
function blended_get_available_items($blended){
    global $DB;
    $selecteditems = $DB->get_records('blended_items',array('id_blended'=>$blended->id));
    $items = blended_get_calificable_items($blended->course);
    if ($selecteditems){ // blended has restricted items
        $filtereditems=array();
        foreach ($selecteditems as $itemrecord) {
           $filtereditems[$itemrecord->id_item]=$items[$itemrecord->id_item];
        }
        return $filtereditems;
    }else{ // all items activated
        return $items;
    }
}
/**
 * 
 * @param stdClass|int $course|$courseid
 * @return array(grade_item)
 */
function blended_get_calificable_items($course) {
    if ($course instanceof \stdClass){
        $courseid = $course->id;
    }
    else{
        $courseid=$course;
    }
    $calificables = new grade_tree($courseid);
    $items = $calificables->items;
    $selectable = array();
    foreach ($items as $key => $value) {
        if ($value->itemtype != 'course') {
            $selectable[$key] = $value;
        }
    }
    return $selectable;
}

/**
 * 
 * @param integer $item_id
 * @return grade_item
 */
function blended_get_item($item_id) {
    if ($item_id<0){
        return null;
    }else{
        $item = new grade_item(array('id' => $item_id), true);
    return $item;
    }
}

function blended_get_item_description(grade_item $item) {
    if ($item->itemtype == "mod") {
        $module = blended_get_mod($item);
        $item_desc = new stdClass();
        if (isset($module->intro)) {
            $item_desc->text = $module->intro;
            $item_desc->format = $module->introformat;
            return $item_desc;
        }
    }
    $item_desc = new stdClass();
    $item_desc->text = isset($item->description)?$item->description:"";
    $item_desc->format = 'text/plain';
    return $item_desc;
}

/**
 * 
 * @param grade_item $item
 * @return type
 */
function blended_get_item_name($item) {
    $mod_names = get_module_types_names();
    if ($item->itemtype == 'course') {
        $itemtypename = get_string('course');
    } else if ($item->itemtype == 'mod') {
        $itemtypename = $mod_names[$item->itemmodule];
    } else {
        $itemtypename = $item->itemtype;
    }
    return $itemtypename . ': ' . $item->itemname;
}

function blended_get_item_html_title($item) {
    if ($item->itemtype == "mod") {
        global $PAGE;
        $cm = get_fast_modinfo($item->courseid)->instances[$item->itemmodule][$item->iteminstance];
        $assignmentlink = $PAGE->get_renderer('core', 'course')->course_section_cm_name($cm);
    } elseif ($item->itemtype == "manual") {
        global $OUTPUT;
        $assignmentlink = "<img src=\"" . $OUTPUT->pix_url('i/manual_item') . "\" class=\"icon itemicon\" alt=\"$item->itemname\" role=\"presentation\" />" . blended_get_item_name($item) . " $item->iteminfo";
    } else {
        $assignmentlink = "UNSUPPORTED $item->itemtype: $item->itemname";
    }
    return $assignmentlink;
}

/**
 * 
 * @param grade_item $items
 * @param boolean $showresetcolumn
 * @param boolean $showeditcolumn
 * @return \html_table table object
 */
function blended_get_items_table($blended, $cm, $items, $showresetcolumn = false, $showeditcolumn = false) {

    // Get the strings ------------------------------------------------- 
global $OUTPUT;
    $strname = get_string("name", "blended");
    $strduedate = get_string("duedate", "blended");
    $strnumteams = get_string("teams", "blended");
    $strgraded = get_string("graded", "blended");
    $strresetteams = get_string("resetteams", "blended");
    $strcreateteams2 = get_string("createteams2", "blended");
    $strno = get_string("no", "blended");
    $stryes = get_string("yes", "blended");
    $strpartially = get_string("partially", "blended");
    $strteamsmanagementpage = get_string("teamsmanagementpage", "blended");
    $strteamsmanagementpagedesc = get_string("teamsmanagementpagedesc", "blended");
    $table = new html_table;
    foreach ($items as $r => $item) {
        $class = "";
        if (!blended_item_is_visible($item)) {
            //Show dimmed if the mod is hidden
            $class = $instance->visible ? '' : 'class="dimmed"';
        }
        $due_date = blended_get_item_due_date($item);


        //Array  donde se almacenar�n los equipos con sus miembros para una tarea dada
        //Llamada a funci�n de blended/lib.php
        //$members=blended_get_teams_members (null,$item->id, null, $blended); //unused

        //Fecha Limite de entrega
        if ($due_date != 0) {
            $due = $due_date ? userdate($due_date, "%A %d, %B, %Y") : '-';
        } else {
            $due = ' ';
        }
        global $DB;
        //Numero de equipos definidos
        $teams = blended_get_teams($item, false);

        $numteams = count($teams);
       
        //Si no hay equipos la tarea no se podr� calificar
        if ($numteams == 0) {
            $grade = null;
            $graded = null;
            $grouping_name=null;
        }
        //Si hay equipos
        else {
            $grouping_name = blended_get_grouping($item, $blended)->name;           
            $gradeurl = new moodle_url('/mod/blended/teams/introgrades.php', array('id' => $cm->id, 'itemid' => $item->id));

            //Tarea no calificada
            $grades = $DB->get_records("blended_grade", array("id_item" => $item->id));
            if (!$grades) {
                $gradestr=$strno;
                $graded = 0;
            } else {
                //Tarea completamente calificada
                if (count($grades) == $numteams) {
                    $gradestr= $stryes;
                }
                //Tarea calificada parcialmente
                else {
                    $gradestr = $strpartially;
                }            
                $graded = 1;
            }
            $icon = $OUTPUT->pix_url('i/grades');
            $grade = "<a $class href=\"$gradeurl\"><img src=\"$icon\"/></a>$gradestr";
           
        }

        $assignmentlink = blended_get_item_html_title($item);
     
    
       if (isset($grouping_name)){
           $numteams.=" <small>($grouping_name)</small>";
       }
        
        $row = array($assignmentlink, $due, $numteams, $grade);
        if ($showresetcolumn) {
            $teamurl = "introteams.php?id=$cm->id&itemid=" . $item->id;
            $teamlink = "<a $class href=\"$teamurl\">" . $strresetteams . "</a>";
            $row[] = $teamlink;
        }
         if ($showeditcolumn) {
                $row[] = $teamlink1;
            }
        $table->data[] = $row;
    }
    $table->head = array($strname, $strduedate, $strnumteams, $strgraded, "", "");
    $table->align = array("left", "left", "left", "left", "left", "left");
    return $table;
}

function blended_item_is_visible($item) {
    if ($item->itemtype == "mod") {
        $modinfo = get_fast_modinfo($item->courseid);
        $instance = $modinfo->instances[$item->itemmodule][$item->iteminstance];
        return $instance->uservisible;
    } else if ($item->itemtype == "manual") {
        return !$item->hidden;
    } else {
        return false;
    }
}

/**
 * 
 * @global $DB $DB
 * @param grade_item $item
 * @return boolean false if no grouping is assigned to this grade_item
 */
function blended_get_groupingid(grade_item $item) {
    global $DB;
    $agrupamiento_tarea = $DB->get_record('blended_assign_grouping', array('id_item' => $item->id));
    if ($agrupamiento_tarea) {
        return $agrupamiento_tarea->id_grouping;
    } else {
        return false;
    }
}

/**
 * 
 * @global $DB $DB
 * @param grade_item $item
 * @return boolean|groups_grouping  grouping alterd with extra fields from blended: mmaxmembers
 */
function blended_get_grouping(grade_item $item, $blended) {
    global $DB;
    $agrupamiento_tarea = $DB->get_record('blended_assign_grouping', array('id_item' => $item->id));
    if (!$agrupamiento_tarea){
        return false;
    }
    $grouping = groups_get_grouping_by_id($item->courseid, $agrupamiento_tarea->id_grouping);
    
    if ($grouping) {
        $grouping->maxmembers = isset($agrupamiento_tarea->maxmembers) ? $agrupamiento_tarea->maxmembers : $blended->nummembers;
        return $grouping;
    } else {
        return false;
    }
}

function blended_get_mod(grade_item $item) {
    if ($item->itemtype == 'mod') {
        global $DB;
        return $DB->get_record($item->itemmodule, array('id' => $item->iteminstance));
    } else {
        return null;
    }
}

function blended_get_item_due_date($item) {
    // find module instance
    $mod = blended_get_mod($item);
    if ($mod != null) {
        $due_date = null;
//Obtenemos la fecha de entrega (si extiste ese campo para esa actividad)
        if (isset($mod->duedate)) {
            $due_date = $mod->duedate;
        } else if (isset($mod->timedue)) {
            $due_date = $mod->timedue;
        } else if (isset($mod->dateend)) {
            $due_date = $mod->dateend;
        } else if (isset($mod->timeclose)) {
            $due_date = $mod->timeclose;
        } else {
            $due_date = null;
        }
        return $due_date;
    } else {
        return null;
    }
}

/////////////////////////////////////////////////////////////////////////////////
/////                  High-level functions                                 /////
/////////////////////////////////////////////////////////////////////////////////


/* * ******************************************************************************
 * Esta funci�n devuelve la p�gina de tarea del usuario 
 * 
 * @global $CFG
 * @return pdf 
 * ****************************************************************************** */


function pdfAssignmentPage($code, $margins, $fullname, grade_item $item, $course, $blended, $gradingmarks = false) {
    global $CFG;

    $assignmentname = blended_get_item_name($item);
    $assignmenttimedue = blended_get_item_due_date($item);
//	if(isset($item->duedate)){
//		$assignmenttimedue= $item->duedate ? userdate($item->duedate,"%A %d, %B, %Y") : ' ';
//	}
//	else if(isset($item->timedue)){
//		$assignmenttimedue= $item->timedue ? userdate($item->timedue,"%A %d, %B, %Y") : ' ';
//	}

    $assignment_code = $item->id;

    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $item_desc = blended_get_item_description($item);
    $cellHtmlDescription = format_text($item_desc->text, $item_desc->format, $formatoptions);
    $coursename = $course->fullname;

    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Blended Module');
    $pdf->SetTitle('Submission form for activity "$assigment_name".');
    $pdf->SetSubject('Blended module. (C)ITAST group. Juan Pablo de Castro');
    $pdf->SetKeywords('Moodle, blended, ITAST');

    //set margins
    $pdf->SetMargins($margins['left'], $margins['top'], $margins['right']);

    $pdf->SetFooterMargin(0); //PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, $margins['bottom']);

    //set image scale factor
    $pdf->setImageScale(2.5);

    // set font
    $pdf->AddFont('courier', '', '');
    $pdf->SetFont('courier', '', 10);

    // add a page
    $pdf->AddPage();

    $dims = new stdClass();
    $dims->coords = array();
    $headeroptions = new stdClass();
    $headeroptions->rowHeight = 6;
    $headeroptions->logoWidth = 30;
    $headeroptions->codebarWidth = 30;
    $headeroptions->logo_url = "pix/UVa_logo.jpg";
    $headeroptions->codebarType = $blended->codebartype;
    $headeroptions->cellHtmlText = '<b>' . $assignmentname . '</b>';
    $headeroptions->cellHtmlDate = get_string('duedate', 'blended') . ':<b>' . $assignmenttimedue . '</b>' . '  ';
    $headeroptions->cellHtmlUser = get_string('username', 'blended') . ':' . $fullname;
    $headeroptions->cellCourseName = get_string('course') . ':' . $coursename;
    $headeroptions->marksize = 5;
    $headeroptions->marksName = 'a';
    $identifylabel = "printCourseLabels.php?a=$blended->id&action=barcode&scale=2&code=$code";

    blended_print_page_header($pdf, $dims, $code, $assignment_code . "1", $headeroptions, '', $identifylabel, '');

    $pdf->writeHTMLCell($pdf->getPageWidth() - $margins['left'] - $margins['right'], '', $margins['left'], '', $cellHtmlDescription, true, 0, 0, true);

    $pdf->Ln();

    //Close and output PDF document
    ob_end_clean();
    $pdf->Output('labels.pdf', 'I');
}

/* * ******************************************************************************
 * OBTENCI�N de las calificaciones de las tablas "blended_grade" y "grade_grades"
 * 
 * Se obtienen de ambas tablas aquellos alumnos que poseen calificaciones y se
 * introducen en un vector sus ids y sus calificaciones(vectores distintos para cada tabla)
 *  
 * @global $DB
 * @param grade_item $item elemento de calificación
 * @param array $users list of users
 * @return array(userid=>array('blended'=>grade,'grade'=>grade) con los ids de 
 * miembros que poseen calificaci�n con sus calificaciones
 * ****************************************************************************** */

function blended_comprobar_calificaciones(grade_item $item, array $users) {

    global $DB;
    $user_grades = array();
    foreach ($users as $user) {
        $user_grade = new stdClass();
        $user_grade->blended = '';
        $user_grade->grade = '';
        $user_grades[$user->id] = $user_grade;
    }
    $blended_grades = $DB->get_records('blended_grade', array('id_item' => $item->id));
    foreach ($blended_grades as $blended_grade) {
        $team = $blended_grade->id_team;
        $blended_members = groups_get_members($team);
        foreach ($blended_members as $blended_member) {
            if (array_key_exists($blended_member->id, $user_grades)) {
                $user_grades[$blended_member->id]->blended = isset($blended_grade->grade) ? $blended_grade->grade : null;
            }
        }
    }

//        $grades_bd=$DB->get_records('grade_grades', array('itemid'=>$item->id));
    $ids = array_map(function ($user) {
        return $user->id;
    }, $users);
    if (count($ids)>0){
    $grades_db = grade_grade::fetch_users_grades($item, $ids, true);
    }else{
        $grades_db=array();
    }
    foreach ($grades_db as $grade) {
        $rawgrade = $grade->rawgrade;
        $user_grades[$grade->userid]->grade = isset($rawgrade) ? $rawgrade : '';
        //$user_grades[$grade->userid]->rawscaleid = isset($grade->rawscaleid)?($grade->rawscaleid):null;
    }

    return $user_grades;
}

// STUDENT ID -------------------------------------------------------------------


/* * ******************************************************************************
 * ACTUALIZA los grupos y miembros del agrupamiento de la tarea elegida
 *
 * Primero se recogen todos los usuarios registrados en el curso en el array 'lista_usuarios'.
 * Posteriormente se introducen en el array 'lista_idnumbers' los identificadores de los usuarios.
 * Este array se usar� para comprobar la existencia del usuario introducido en el sistema
 * 	
 * Para cada grupo, se comprueba su existencia en la base de datos:
 * 	- Si el grupo existe se actualiza
 * 	- Si no existe se crear� un nuevo grupo
 * 
 * Una vez comprobado si los usuarios existen, se comprueba si hay alg�n identificador repetido
 * 	- Para cada miembro del vector 'miembros_grupos' se introduce
 * 	  en el array 'contador' el numero de veces que aparece
 *
 * Tras comprobar los grupos se comprueban los miembros:
 * 	- Se comprueba la existencia de cada mimerbo introducdo en la base de datos
 * 		+ Si no existe no se introduce
 * 		+ Si existe lo a�ado al grupo
 * 	- Si un grupo esta vac�o o todos sus miembros no existen en la base de datos se borrar�
 *
 * Por �ltimo se introduce en un array el numero de veces que se repite cada identificador
 *
 * @see blended_clean_idnumber()
 * @see groups_group_exists()
 * @see groups_update_group()
 * @see groups_create_group()
 * @see groups_delete_group()
 * @see groups_add_member()
 * @see groups_get_members()
 *
 * @param int $groupingid int con el id del agrupamiento
 * @param array $teams array with stdClass representing updated teams. Properties name and members(Array)
 * @param int $numteams int con el numero de equipos a introducir
 * @param int $nummembers int con el numero máximo de miembros a introducir
 * @param int $itemid int con el identificador de la tarea
 *
 * @return  outputmessages html text for screen reporting
 * ****************************************************************************** */

function blended_actualizar_agrupamiento($updated_teams, grade_item $item, $blended, $delete_empty) {

    global $DB;
    $output='';
    $r = 0;
    //obtengo el identificador del curso
    $grouping = blended_get_grouping($item, $blended); //groups_get_grouping($groupingid,'*',MUST_EXIST);//$DB->get_record('groupings',array('id'=>$groupingid));
    $courseid = $grouping->courseid;

    //Obtención de la  lista de id de los usuarios introducidos en el curso:
    $lista_usuarios = get_enrolled_users(context_course::instance($courseid), '', 0, 'u.id,u.idnumber');
    $lista_idnumbers = array();


// 	//Obtenci�n de la lista de 'idnumber' de estos usuarios
    foreach ($lista_usuarios as $id => $lista) {
        if (!empty($lista->idnumber)) {
            $lista_idnumbers[$id] = $lista->idnumber;
        }
// 		$r++;
    }
// 	$r=0;
// 	$lista_id=array();
// 	foreach($lista_idnumbers as $lista){
// 		$lista_id[$r]=blended_clean_idnumber($lista);
// 		$r++;
// 	}
    $members_processed = array();
    /* Actualizo los grupos actualizando el nombre del grupo
      y volviendo a introducir los miembros comprobando que el usuario introducido existe */
    $r = 0;
    foreach ($updated_teams as $i => $updatedteam) {
        
        // Check members
        $form_members = array();
        foreach ($updatedteam->members as $memberid) {
            //compruebo que el usuario introducido existe y si es as� que lo guarde
            if (key_exists($memberid, $lista_idnumbers)) { // user is specified by moodle id
                if (array_search($memberid, $form_members) === false)
                    $form_members[] = $memberid;
            }
            else if (key_exists($memberid, $lista_usuarios)) {// user is specified by id_number
                if (array_search($memberid, $form_members) === false)
                    $form_members[] = $memberid;
            }
        }

        // Update/Create/delete Group
        // Create/Update Group information
        //
    if (count($form_members) == 0 && $delete_empty) { // An empty membership deletes the group ??
            groups_delete_group($updatedteam->id);
        } else { //guardo los miembros en un array

            $current_team = blended_get_team($updatedteam->id);
            if ($current_team) {//si existe lo actualizo
                $current_team->name = $updatedteam->name;
                groups_update_group($current_team);
            } else if (count($form_members)>0 || $updatedteam->name!=''){  //si no existe lo creo
                $itemname = blended_get_item_name($item);
                $data = new stdClass();
                $data->courseid = $courseid;
                $data->name = $updatedteam->name;
                $data->description_editor['text'] = "Group for activity:  '$itemname'.";
                $data->description_editor['format'] = FORMAT_PLAIN;
                $updatedteam->id = groups_create_group($data);
                groups_assign_grouping($grouping->id, $updatedteam->id, time(), true);
            }
            else{
                continue; // team with no id, no name, and no components
            }
              
            $current_team = blended_get_team($updatedteam->id, true);
            $current_members = array_keys($current_team->members);
            // find members to remove
            $members_to_delete = array_diff($current_members, $form_members);
            $members_to_add = array_diff($form_members, $current_members);
            // remove old members
            $need_reelect_leader = false;
            foreach ($members_to_delete as $memberid) {
                $need_reelect_leader = $current_team->leaderid == $memberid;
                blended_remove_team_member($current_team, $memberid);
//				     groups_remove_member($team->id, $member->id);
            }
            // update team conf
            $current_team = blended_get_team($current_team);
            // Add current members

            foreach ($members_to_add as $memberid) {
                groups_add_member($current_team->id, $memberid);
                if ($memberid == $current_team->leaderid) {
                    $need_reelect_leader = false;
                }
                if (key_exists($memberid, $members_processed))
                    $members_processed[$memberid] ++;
                else
                    $members_processed[$memberid] = 1;
            }
            if (!isset($current_team->leaderid)) {
                $need_reelect_leader = true;
            }
            // check leadership
            if (count($members_to_add) > 0 && count($members_to_delete) > 0 && $need_reelect_leader) { // elect new leader
                blended_set_team_leaderid($current_team->id, $form_members[0]);
                global $OUTPUT, $DB;
                $a = new stdClass();
                $a->username = fullname($DB->get_record('user', array('id' => $form_members[0])));
                $a->teamname = $current_team->name;

                $output.= $OUTPUT->notification(get_string('userpromotedtoleader','blended',$a));
            }
        }
    }

    return $output;

// 	//Actualizaci�n de las calificaciones(si existen) : comparar los miembros con los de "grade_grades" y si 
// 	//no estan en el vector borrarlos de la tabla

    $grades_moodle = grade_get_grades($courseid, $item->itemtype, $item->itemmodule, $item->iteminstance);
    $grades_item = $grades_moodle->items[0];
    $grades = $grades_item->grades;
    //Para cada miembro de la tabla miro si esta en el vector $miembros_grupos
    $cont_gr = 0;
    $cont_group = 0;
    foreach ($grades as $grade) {
        foreach ($miembros_grupos as $miembro_gr) {
            foreach ($miembro_gr as $miembro) {
                if ($grade->userid != $miembro->id) {
                    $cont_gr++;
                }
                if ($cont_gr == count($miembro_gr)) {
                    $cont_group++;
                    continue;
                }
            }
            if ($cont_gr == count($miembros_grupos)) {
                print_error("borra en tabla!!");
                //Entonces ese id no esta en los mimebros y hay que borrarlo de la tabla
                $DB->delete_records('grade_grades', array('itemid' => $grade_item->id, 'userid' => $grade->userid));
                continue;
            }
        }
    }
// 	$DB->delete_records('grade_grades',array('itemid'=>$grade_item->id));
    //Comprobaci�n de la repetici�n de miembros en distintos equipos:
    $contador = array();
    foreach ($lista_idnumbers as $t) {
        $valor = 1;
        foreach ($miembros_grupos as $miembro_gr) {
            foreach ($miembro_gr as $miembro) {
                if ($miembro->idnumber == $t) {
                    //meto en un contador para el valor del id de cada usuario las veces que se repite
                    $contador[$miembro->id] = $valor;
                    $valor++;
                }
            }
        }
    }
    return $contador;
}

function blended_grade_student($memberid, grade_item $item, $rawgrade, $newfinalgrade) {
    $array_members_grades = array();
    $array_members_grades['userid'] = $memberid;
    $array_members_grades['rawgrade'] = $rawgrade==null?null:(string)$rawgrade;
    $status = grade_update('mod/blended', $item->courseid, $item->itemtype, $item->itemmodule, $item->iteminstance, $item->itemnumber, $array_members_grades);
    $item->update_final_grade($memberid, $newfinalgrade==null?null:(string)$newfinalgrade, 'mod/blended'); 
}

function blended_generate_groups_table($item, $blended, $is_grading = true) {
    global $DB, $OUTPUT, $CFG;
    $context_course = context_course::instance($item->courseid);
    $context = context_module::instance($blended->id);


    //Obtenci�n de los grupos del agrupamiento asociado a la tarea elegida	
    //Obtengo los parámetros del agrupamiento
    $grouping = blended_get_grouping($item, $blended);
    $groups = groups_get_all_groups($item->courseid, null, $grouping->id);
    $i = 0;
    //Introducci�n de los miembros del agrupamiento en un vector
    // array[groupid][userid] for group/members
    $group_members = [];
    $users_in_teams = [];
    $numteams = count($groups) + 1;
    $nummembers = 0;

    foreach ($groups as $gr) {
        $members_loc = groups_get_members($gr->id);
        // obtengo el numero de cuadros a poner en el formulario segun la
        // maxima longitud del mayor equipo
        if ($nummembers < count($members_loc)) {
            $nummembers = count($members_loc);
        }
        $group_members[$gr->id] = $members_loc;
        $users_in_teams = array_merge($users_in_teams, $members_loc);
    }

    //Contador del numero de repticiones de cada miembro
    $repetir_miembro = blended_num_members($context_course, $group_members, 'teamsmanagement');

    //Introduzco un cuadro m�s por si se desea introducir otro miembro
    $nummembers = $nummembers + 1;
    $t = 0;
    $stridteam = get_string('idteam', 'blended');
    $stridmembers = get_string('idmembers', 'blended');
    $strgrade = get_string('grade', 'blended');
    $stralertcol = get_string('viewalertsdesc', "blended");
    $strnone = get_string('nograded', 'blended');
    $strrewritegrades = get_string ( 'rewritegrades', 'blended' );
    //Grading
    //Obtengo las calificaciones(Si existen) de los miembros tanto de la tabla "blended_grade" con de "grade_grades"
    if ($is_grading) {
        $user_grades = blended_comprobar_calificaciones($item, $users_in_teams);
        $gradearray = blended_get_scale_array($item);
        $grademax = $item->grademax;
        $gradelength = 3;
    }
    $table = new html_table();
    $table->align = array("left");
    
    if ($is_grading){
        $table->head = array($stridteam, $stridmembers, $strgrade, $stralertcol);
    }else{
        $table->head = array($stridteam, $stridmembers, $stralertcol);
    }
    $table->headspan = array(1, $nummembers, 1, 1);

    // Realizo el formulario rellenado con los campos que corresponden
    foreach ($groups as $group) {
        $rewriteteam = "rewrite_team_" . $t;
        $fontsize = 1;
        $gradealert="";
        $idteam = "team_" . $t . "_id";
        $teamname = "team_" . $t . "_name";
        $tablerow = new html_table_row();
        //Obtengo los grupos por su id		
        $teamfield = "<input type=\"hidden\" name=\"$idteam\" id=\"$group->id\" value=\"$group->id\" >" .
                "<input type=\"text\" name=\"$teamname\" id=\"$teamname\" value=\"$group->name\" size=\"6\" maxlength=\"8\"  align=\"center\">";
        $tablerow->cells[] = $teamfield;
        // Obtengo los miembros de cada grupo
        $mem = $group_members[$group->id];
        // bucle miembros
        for ($f = 0; $f < $nummembers; $f ++) {
            $membername = "team_" . $t . "_member_" . $f;
            // Nombre actual del campo de texto "Identificador" (utilizando el contador)
            $emptyteam = false;
            $member = current($mem);
            if ($f < count($mem)) {
                // Objeto `user`
                $memberuserid = $member->id;


                if (!$user = $DB->get_record('user', array('id' => $memberuserid))) {
                    $emptyteam = true;
                }

                if (!$emptyteam) {
                    // Foto y vinculo a perfil de `user`
                    if ($piclink = (has_capability('moodle/user:viewdetails', $context) || has_capability('moodle/user:viewdetails', $usercontext))) {
                        $userpic = $OUTPUT->user_picture($user);
                        $profilelink = '<a href="' . $CFG->wwwroot . '/user/view.php?id=' . $user->id . '&course=' . $item->courseid . '">' . fullname($user, true) . '</a>';
                    }

                    // Estudiante lider de un equipo
                    if (isset($member->leader) && $member->leader == 1) {
                        $profilelink = '<strong>' . $profilelink . '  (leader)' . '</strong>';
                    }
                    // ID del miembro del equipo
                    $membervalue = blended_gen_idvalue($member, $blended);

                    if ($membervalue == - 1) {
                        $membervalue = "\"\"";
                        $stridnumber = get_string('withoutidnumber', 'blended');
                    } else if ($membervalue == - 2) {
                        $membervalue = "\"\"";
                        $stridnumber = get_string('withoutuserinfodata', 'blended');
                    } else {
                        $stridnumber = "";
                        $membervalue = blended_generate_memberid_field($member, $blended);
                    }
                    if ($membervalue == ' ') {
                        prev($mem);
                        continue;
                    }
                    $teammember_field = $userpic;
                    /* Si la calificaci�n de "blended_grade" y "grade_grades"
                     * no coincide se muestra un mensaje de alerta al usuario */
                    if ($is_grading) {
                        $user_grade = $user_grades[$memberuserid];
                        if ($user_grade->blended != '' && $user_grade->grade != '' && ((float) $user_grade->blended) != ((float) $user_grade->grade)) {
                            $grade_string = grade_format_gradevalue($user_grade->grade, $item);
                            $icon_url = $OUTPUT->pix_url('alerta', 'blended');
//									$teammember_field = "$userpic <img alt=\"$stralertanota.Nota individual: $cal_gr_int\" title=\"$stralertanota.Nota individual: $cal_gr_int\" src=\"../../../mod/blended/pix/alerta.jpg\">";
                            $a = new stdClass();
                            $a->grade = $grade_string;
                            $a->finalgrade = $user_grade->grade;
                            $stralertanota = get_string('alertgrade', "blended", $a);
                            $stralertanota = htmlentities($stralertanota);
                            $teammember_grade_alert = " <img alt=\"$stralertanota\" title=\"$stralertanota\" src=\"$icon_url\">";
                            $teammember_field .= $teammember_grade_alert;
                        }
                    }
                    /* Si existe un miembro repetido en distintos grupos se recuadra
                     * el miembro en color rojo */

                    if ($repetir_miembro [$memberuserid] == 1) {
                        $teammember_field .= "<input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"$membervalue\" size=\"7\"
                                                                maxlength=\"8\"  align=\"center\"><br><font size=\"1\">$profilelink<font size=\"1\" color=\"#FF0000\">$stridnumber</font>";
                    } else {
                        $teammember_field .= "<input type=\"text\" style=\"border-color:red\" title=\"Id repetido la calificacion individual puede ser distinta a la grupal\" name=\"$membername\" id=\"ac-userid\" value=\"$membervalue\" size=\"7\"
                                                                maxlength=\"8\"  align=\"center\"><br><font size=\"1\">$profilelink<font size=\"1\" color=\"#FF0000\">$stridnumber</font>";
                    }
                    next($mem);
                }      // Equipo vacio
                else {
                    // Campo de texto "Identificador" vacio.
                    $teammember_field = "<input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\"
                                                        maxlength=\"8\"  align=\"center\">";
                }
            } else {
                // Campo de texto "Identificador" vacio.
                $teammember_field = "<input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\" maxlength=\"8\"  align=\"center\">";
            }
//                                           
            $tablerow->cells[] = $teammember_field;
        }//Fin del bucle MEMBERS				
        //GRADES:									
        $alert = false;
        $rewriteteam = "rewrite_team_" . $t;
        
        // Si equipo vac�o							
        if ($emptyteam == true) {
            $gradealert = get_string('teamempty','blended');
        }
        if ($is_grading) {
            list ( $grade, $alert ) = blended_get_team_grade($group, $item);
            // Nombre del campo de texto "Calificación"
            $grade_text_input_name = "team_" . $t . "_grade";
            // Nombre para las opciones de la lista deplegable "Calificación"
            $grade_select_input_name = "team_" . $t . "_gradelist";
            //Si equipo no calificado	
            if (!$grade) {
                if ($emptyteam == true) {
                    $gradealert = get_string('teamempty','blended');
                } else {
                    $gradealert = get_string('teamnotgraded','blended');
                }//Fin if-else
                $gvalue = null;
                $fontsize = 2;
                $gradehidden_field = '';
            }
            // Si equipo ya calificado
            else {
                // Si calificación individual distinta a la de grupo
                if ($alert) {
                    $gradealert = $strrewritegrades;
                    $gradealert = $gradealert . "<center><input type=\"checkbox\" name=\"$rewriteteam\" id=\"$rewriteteam\" value=\"rewrite\" checked=\"true\"></center>";
                    $fontsize = 1;
                }
                // Si calificacion de grupo igual para todos los miembros
                else {
                    $gradealert = "" . "<input type=\"hidden\" name=\"$rewriteteam\" id=\"$rewriteteam\" value=\"rewrite\">";
                    $fontsize = null;
                }//Fin if-else
                if (empty($teamsfromassignmentid) || !empty($grade)) {
                    // `id` de la tabla "blended_grade"
                    $idgradevalue = $grade->id;
                    // `grade` de la tabla "blended_grade"
                    $gvalue = $grade->grade;
                    // Nombre del campo oculto `id` de "blended_grade"
                    $idgrade = "team_" . $t . "_gradeid";
                    // Campo oculto con `id` de la tabla "blended_grade"
                    $gradehidden_field = "<input type=\"hidden\" name=\"$idgrade\" id=\"$idgrade\" value= \"$idgradevalue\" >";
                } else {
                    $gvalue = null;
                }//Fin if-else
            } // Fin if-else



            if ($gradearray !== null) {
                // Campo de texto "Calificación"
                // Lista desplegable "Calificación"
              
                $gvalue = $gvalue !== null ? (int) $gvalue : null; // scales uses integer values
                $grade_select_field = "<select name=\"$grade_select_input_name\" id=\"$grade_select_input_name\" align=\"left\" onchange=\"setGradeTextField(this,$grade_text_input_name)\">";
                $grade_select_field.= "<option value=\"-1\">$strnone</option>";
                foreach ($gradearray as $key => $val) {
                    if ($gvalue !== null && $key == $gvalue) {
                        $grade_select_field.= "<option value=\"$key\" selected=\"selected\">$val</option>";
                    } else {
                        $grade_select_field.= "<option value=\"$key\">$val</option>";
                    }
                }
                $grade_select_field.= "</select>";
            } else {
                $grade_select_field = '';
            }
            $grade_field = "<input type=\"text\" name=\"$grade_text_input_name\" id=\"$grade_text_input_name\" value=\"$gvalue\"  size=\"$gradelength\" maxlength=\"$gradelength\"  align=\"center\" onkeyup=\"setGradeSelect(this,$grade_select_input_name,$gradelength,null,$grademax)\">";
//                                        $grade_field="<input type=\"text\" name=\"$gname\" id=\"$gname\" value=\"$gvalue\"  size=\"$gradelength\" maxlength=\"$gradelength\"  align=\"center\" >";

            $tablerow->cells[] = $gradehidden_field . $grade_field . $grade_select_field;
        }
        $tablerow->cells[] = $gradealert;

        $t ++;
        $table->data[] = $tablerow;
    }//Fin bucle TEAMS groups

    $tablerow = new html_table_row();
    //Introducci�n de un grupo vac�o

    $groups = groups_get_all_groups($item->courseid);

    $idteam = "team_" . $t . "_id";
    $teamname = "team_" . $t . "_name";

    $tablerow->cells[] = "<input type=\"hidden\" name=\"$idteam\" id=\"newTeam\" value=\"\" > <input type=\"text\" name=\"$teamname\" id=\"newTeamName\" value=\"\" size=\"6\"
			    maxlength=\"8\"  align=\"center\">";

    // mostramos los identificadores de los miembros del grupo
    // bucle miembros
    for ($f = 0; $f < $nummembers; $f ++) {
        $membername = "team_" . $t . "_member_" . $f;
        // Campo de texto "Identificador" vacio.
        $tablerow->cells[] = "<input type=\"text\" name=\"$membername\" id=\"ac-userid\" value=\"\" size=\"7\" maxlength=\"8\"  align=\"center\">";
    }
    $gradealert = get_string('teamempty','blended');
    if ($is_grading) {
        $gvalue = null;
        $fontsize = 2;
        // Nombre del campo de texto "Calificación"
        $grade_text_input_name = "team_" . $t . "_grade";
        // Nombre para las opciones de la lista deplegable "Calificación"
        $grade_select_input_name = "team_" . $t . "_gradelist";

        // Campo de texto "Calificación"
        $gradehidden_field = "<input type=\"text\" name=\"$grade_text_input_name\" id=\"$grade_text_input_name\" value=\"$gvalue\"  size=\"$gradelength\" maxlength=\"$gradelength\"  align=\"center\" onkeyup=\"setGradeSelect(this,$grade_select_input_name,$gradelength,null,$grademax)\">";
//                            $gradehidden_field = "<input type=\"text\" name=\"$gname\" id=\"$gname\" value=\"$gvalue\"  size=\"$gradelength\" maxlength=\"$gradelength\"  align=\"center\" >";
        // Lista desplegable "Calificación"
        $grade_select_field = "<select name=\"$grade_select_input_name\" id=\"$grade_select_input_name\" align=\"left\" onchange=\"setGradeTextField(this,$grade_text_input_name)\">";
        $grade_select_field .= "<option value=\"-1\">$strnone</option>";
        foreach ($gradearray as $key => $val) {
            if ($gvalue !== null && $val == $gvalue) {
                $grade_select_field = $grade_select_field . "<option value=\"$key\" selected=\"selected\">$val</option>";
            } else {
                $grade_select_field = $grade_select_field . "<option value=\"$key\">$val</option>";
            }
        }
        $grade_select_field .= "</select>";
        $tablerow->cells[] = "$gradehidden_field $grade_select_field";
    }
    $tablerow->cells[] = $gradealert;
    $table->data[] = $tablerow;
    //Fin de la tabla
    return $table;
}

/********************************************************************************
 * GENERACI�N del metodo random en la creaci�n de equipos
*
* Seg�n el tipo de alumnos escogido pasado en el parametro 'studensselection'
* se recoger�n mediante la funci�n blended_get_course_students_id los identificadores
* de los alumnos bien sean s�o los activos o todos los matriculados
*
* 
* @see blended_get_course_students_id()
*
* @param string $studentsselection string con el tipo de alumnos escogido(activos/todos)
* @param object $course objeto de la instancia del curso
* @param int|null $id_assignment int con el identificador de la tarea
*
* @return  array formado por:
* 	- array 'freemembers': identificadores de los estudiantes del curso (activos/todos)
* 	- int free_index' : �ndice de referencia
* 	- int 'numfreemembers': numero de alumnos recogidos en 'freemembers'
* 	- array 'userids': ids de los alumnos recogidos en 'freemembers'
* 
********************************************************************************/
function blended_method_random($teams, $studentids,$context){
	
	shuffle($studentids);
      
        $chunks = array_chunk($studentids, ceil(count($studentids)/count($teams)));
        for ($i=0;$i<count($chunks);$i++){
            $teams[$i]->members=$chunks[$i];
        }
	return $teams;
}


