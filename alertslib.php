<?php
require_once 'blended_locallib.php';
/**
 * A student is an user that participates in a course 'moodle/course:view'
 * and can't manage groups 'moodle/course:managegroups'
 * @global type $USER
 * @global type $DB
 * @param type $blended
 * @param type $course
 * @param type $context_course
 * @return \stdClass
 */
function alert_messages($blended,$course,$context_course)
{
	global $USER,$DB;
	
	$alert = new stdClass();
		
		//Ningún estudiante está matriculado
   	$alert->no_students_in_course_alert = true;
    	// Ningún estudiante está activo
    	$alert->none_is_active_alert        = true;
    	// Hay estudiantes inactivos
    	$alert->student_not_active_alert    = false;
		
	$alert->iamstudent            = false;
        $alert->no_DNI_alert          = false;
        $alert->no_userinfodata_alert = false;
	
              
        list($students, $non_students, $activeuserids, $user_records)= blended_get_users_by_type($context_course);
        if($students)
            {    
            $alert->no_students_in_course_alert = false;         
            // Comprobamos que al menos hay un estudiante activo (ha entrado al menos una vez)
            // en el curso. Nos devuelve array con todos los estudiantes        
            if(count($activeuserids)>0){
            // Se desactiva la alerta de que ningún estudiante está activo si el array tiene
            // al menos un elemento
            $alert->none_is_active_alert = false;
        }
    }


    
	// Devuelve un array que contiene todos los valores de $alluserids que no
	// aparezcan $activeuserids, es decir, los estudiantes inactivos
	$inactivemembers = array_diff($students,$activeuserids);
 //Comprobamos si el usuario está activo
        if(count($inactivemembers)>0)
            {
                 $alert->student_not_active_alert=true;
                 $alert->inactive_students=$inactivemembers;
            }
    
        foreach ($students as $student)
            {
            // $user = get_record('user','id',$student->id);
             $user = $student;
             $code = blended_gen_idvalue ($user_records[$student], $blended);
             
             //print $code.'<BR>';
             // Código EAN a partir de `idnumber` (dni): el estudiante no lo ha introducido
             // en su perfil: $code == -1
             if($code == -1 && $blended->idtype==2){
                 $alert->no_DNI_alert = true;
             }  
             else if($code == -2 && $blended->idtype=3){
                 $alert->no_userinfodata_alert = true;
             }    
            // Comprobar si el usuario actual del modulo es un estudiante
            if($user_records[$student]->id === $USER->id){
                $alert->iamstudent = true;
                break;
            }        
        }
    
    return $alert;
}


function display_alerts_table($blended, $studentroleid, $course, $context_course, $alertinfo)
{
	global $DB,$OUTPUT;
	mtrace ('<center>'.$alertinfo.'</center>');
	
	if ( $blended->idtype == 0 )
		{
		mtrace ("<center>El identificador seleccionado es ID de usuario. No será necesario haber introducido el DNI ni el ID personalizado para el 
		correcto funcionamento del módulo.<BR>
		Para modificar esta opción, actualice los parámetros de configuración del módulo Blended.</center><BR><BR>");
		}
	if ( $blended->idtype == 1 )
		{
		mtrace ("<center>El identificador seleccionado es el DNI del alumno. El DNI será necesario para realizar algunas operaciones en el módulo.<BR>
		Para modificar esta opción, actualice los parámetros de configuración del módulo Blended.</center><BR><BR>");
		}
	if (substr($blended->idtype,0,1) == 2)
		{
		mtrace ("<center>El identificador seleccionado es el ID personalizado del alumno. Este campo será necesario para realizar algunas operaciones en el módulo.<BR>
		Para modificar esta opción, actualice los parámetros de configuración del módulo Blended.</center><BR><BR>");
		}	
        list($students, $non_students, $activeuserids, $user_records)= blended_get_users_by_type($context_course);

	$i = 0;
	$displays=array();
        
	foreach ($students as $studentid){
            $displayrow=array();
		// $user_reg = get_record('user','id',$student->id);
             $user_reg =  $user_records[$studentid];
	     $fullname=fullname($user_reg);

	     $displayrow["username"]= $fullname;
	     
	     $displayrow["profile"]= $OUTPUT->user_picture($user_reg,array('id'=>$course->id));
             //Comprobamos si el usuario está activo
         
	     $displayrow["status"]= '';    

	     if(!array_search($studentid, $activeuserids))
                {
	     		$alert = new stdClass();
                 $alert->student_not_active_alert=true;
                 $displayrow["status"]= "<font color=\"#FF0000\">Inactivo</font>";
             }
             else
             {
             	 $displayrow["status"]= "Activo";
             }
      
		$displayrow["idnumber"]= ''; 
		
		$code=$user_reg->idnumber;
			// Si no se ha introducido el `idnumber` finalizamos
			// pues lo necesitamos para el c�digo
		
			if(empty($code)){
				$displayrow["idnumber"]= "<font color=\"#FF0000\">No introducido.</font>";
			}
			else
			{
				$displayrow["idnumber"]=$code;
			}
			
		$displayrow["userinfodata"]= ''; 

		if (substr($blended->idtype,0,1) == 2)
		{
			//print($blended->idtype);
			$fieldid = intval(substr($blended->idtype,1));
			//print $fieldid;
			if (!$code = get_field('user_info_data', 'data' ,'userid', $user_reg->id,'fieldid', (int)$fieldid )) {

				$displayrow["userinfodata"]= "<font color=\"#FF0000\">No introducido.</font>";
			}
			else
			{
				$displayrow["userinfodata"]=$code;
			}
		}
		else
			$displayrow["userinfodata"]="No utilizado";
			
		$displays[$i]=$displayrow;
		$i++;
	 }
	
	//tabla de alumnos
	$table = new html_table;
	$table->class = 'mytable';
	$table->head  = array('Alumno','Perfil','Estado','DNI','ID Personalizado');
	$align = "left";
	$table->align = array ($align, $align, $align,$align,$align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $displays;
	//print_object($table);
	//print_table($table); 
	echo html_writer::table($table);

	
	return;
}



?>