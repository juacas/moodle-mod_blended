<?php

/*********************************************************************************
* Module developed at the University of Valladolid
* Designed and directed by Juan Pablo de Castro with the effort of many other
* students of telecommunication engineering of Valladolid
* Implemented by Pablo GalÃ¡n Sabugo
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

* @author J�ssica Olano L�pez, Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
*********************************************************************************/
 
    require_once("../../../config.php");
    require_once("../lib.php");
    require_once("locallib.php");
    

// Get the params ---------------------------------------------------------------- 
    global $DB, $PAGE, $OUTPUT;
    $id = required_param('id', PARAM_INT); // blended Course Module ID, or

    if ($id) {
        if (! $cm = get_coursemodule_from_id('blended', $id)){
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_course($cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            error("Course module is incorrect");
        }
        if (! $user = $DB->get_record('user',array('id'=> $USER->id))) {
            error("No such user in this course");
        }
    }
    

// Log ---------------------------------------------------------------------------
    //add_to_log($course->id, "blended", "assignmentpage", "assignmentpage.php?a=$blended->id", "$blended->id");

// Capabilities ------------------------------------------------------------------ 
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printassignmentpage', $context);
    
    // show headings and menus of page
   $url =  new moodle_url('/mod/blended/teams/assignmentpage.php',array('id'=>$id));
   $PAGE->set_url($url);
   $PAGE->set_title(format_string($blended->name));
   $PAGE->set_heading($course->fullname);

// Get the strings --------------------------------------------------------------- 

    $strassignmentpage    = get_string('assignmentpage','blended');
    $strassignpagedescr   = get_string('assignmentpagedescr','blended');
    $strdefaultassignment = get_string('defaultassignment','blended');
    $strassignments       = get_string('assignments','blended');                                         
    $strassignment        = get_string('assignment','blended');
    $struser              = get_string('user','blended');
    $strnoactiveuser      = get_string('noactiveuser','blended');
    $strnoidnumber        = get_string('noidnumber','blended');
    $strnoidnumber2       = get_string('noidnumber2','blended');
    $strnouserinfodata   = get_string("nouserinfodata","blended");
    $strnone              = get_string('noselected','blended');  
    $strprintassignpage   = get_string('printassignmentpage','blended');
    $strnoassignments     = get_string('noassignments','blended');

// Print the page header ---------------------------------------------------------
    $PAGE->navbar->add($strprintassignpage);
    
    echo $OUTPUT->header();
    
// Print the main part of the page -----------------------------------------------

    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($strassignmentpage));
    echo'<center>';
    echo $OUTPUT->box(format_text($strassignpagedescr), 'generalbox', 'intro');
    echo'</center>';
    echo $OUTPUT->spacer(array('height'=>20));

// Imprimimos el formulario por pantalla ----------------------------------------- 

    // Formulario ASSIGNMENTFORM
   
    $url=  'printassignmentpage.php';   
     
    echo "<form method=\"post\" action=\"$url\" id=\"assignmentform\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"$id\"/>";
        //Table
        echo '<table align="center" width="40%" cellspacing="10" cellpadding="5" >'; 

        //Obtenemos la referencia a toda la informaci�n sobre los m�dulos dentro del curso
        $activities= blended_get_available_items($blended);
       
       
       
        
        //Etiqueta de la lista desplegable
        echo "<tr><td><label>$strassignments</label></td>";
        //Lista desplegable de las tareas existentes en el m�dulo
         echo '<td><select name="assignment_id" align="left">';
        
        foreach($activities as $item){
            if (blended_item_is_visible($item)) {
                    $assignmentname  = blended_get_item_name($item);
                    $value           = $item->id;
                    echo "<option value=\"$value\">$assignmentname</option>";
            }
        }	
        echo "</select></td></tr>";
        
        // Codigo exclusivo para profesores y administradores
        if (has_capability('mod/blended:selectoneamongallstudents', $context)) {           
            list($userids,$nonstudentids,$activeids,$users)=  blended_get_users_by_type($context_course);
            // Obtenemos todos los estudiantes del curso 
            if($userids){
 
                // Etiqueta de la lista desplegable
                echo "<tr><td><label>$struser</label></td>";
                // Calculamos el nÃºmero de opciones de la lista que serÃ¡n mostradas al mismo tiempo        
                $size=count($userids);
                if($size>5){
                    // Como maximo 5
                    $size=5;   
                }
                   
            // Lista desplegable FULLNAME_CODE_DNI
                echo '<td><select name="fullname_code_dni" align="left" size='.$size.'>';
                
                $not_DNI_out          = false;
                $not_userinfodata_out = false;
                $not_active_out       = false;

                foreach ($userids as $userid) {
                    $not_DNI_in          = false;
                    $not_userinfodata_in = false;
                    $not_active_in       = false;

                    //Comprobamos si el usuario está activo
                    if(!array_search($userid, $activeids)){
                        $not_active_in  = true;
                        $not_active_out = true;
                    }

                    // Obtenemos el objeto 'user' de cada estudiante del curso
                    $user = $users[$userid];
                    
                    // Obtenemos el codigo EAN de la etiqueta de cada estudiante
                    $code = blended_gen_idvalue ($user, $blended);
                    
                    // Si el id es el dni y el estudiante no lo ha introducido en su perfil
                    if($code == -1){
                    	$not_DNI_in = true;
                    	$not_DNI_out = true;
                    }   
                    else if($code == -2){
                        $not_userinfodata_in  = true;
                        $not_userinfodata_out = true;
                    }                                         
                    
                    // Obtenemos el dni de cada estudiante
                    $dni = $user->idnumber;
      
                    // Obtenemos el nombre completo de cada estudiante
                    $fullname2=$fullname = fullname($user, true);
                    
                    if(!$not_active_in && !$not_DNI_in ){
                        
                        $fullname2 = $fullname;
                        
                        if($not_userinfodata_in){
                            $fullname2 = $fullname2." (#)";
                        }
                    }
                     else if($not_active_in){
                        
                        $fullname2 = $fullname." (*)";
                        
                        if($not_DNI_in){
                            $fullname2 = $fullname2." (**)";                            
                        }
                        if($not_userinfodata_in){
                            $fullname2 = $fullname2." (#)";
                        }
                    }
                    else {
                        if($not_DNI_in){
                            $fullname2 = $fullname." (**)";                            
                        }  
                        if($not_userinfodata_in){
                            $fullname2 = $fullname2." (#)";
                        }  
                    }
            
                    // Juntamos los tres valores separados por un '&'
                    $value=$fullname."&".$code."&".$dni."&".$user->idnumber;
                    echo "<option value=\"$value\">".$fullname2."</option>";   
                }  
                echo "</select></td></tr>";
            }             
            // Si no hay ningun estudiante en el curso
            
            // Avisos para profesores
            if($not_active_out){    
                // Hay estudiantes no activos
                echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnoactiveuser</font></center></td></tr>";
            
            }      
            if($not_DNI_out){
 		       // Hay estudiantes que no han introducido su dni
                echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnoidnumber</font></center></td></tr>";
            
            }  
            if($not_userinfodata_out){
                // Hay estudiantes que no han introducido su dni
                echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnouserinfodata</font></center></td></tr>";
            
            }   
    	}
     	//Codigo exclusivo para estudiantes
        else {
            
            // Obtenemos el codigo EAN de la etiqueta del estudiante  
            $code     = blended_gen_idvalue ($user, $blended);
            
            // Obtenemos el nombre completo del estudiante
            $fullname = fullname($user, true);
            
            // Obtenemos el dni del estudiante
            $dni      = $user->idnumber; 
            
            if(empty($dni)&& $blended->idtype=='1'){
                echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnoidnumber2</font></center></td></tr>";
            }
                        
            //Campos ocultos con el nombre del usuario, DNI y el codigo EAN  
            echo "<input type=\"hidden\" name=\"code\"     value='$code'>";  
            echo "<input type=\"hidden\" name=\"fullname\" value='$fullname'>";
            echo "<input type=\"hidden\" name=\"dni\"      value='$dni'>";       
        }
        // Fin tabla  
        echo '</table>';
        
        // tabla con el boton
        echo $OUTPUT->spacer(array('height'=>30));
        echo '<table align="center">';
        echo "<tr><td><input type=\"button\" value=\"".$strprintassignpage."\" onClick=\"checkform()\" /></td></tr>";
        echo '</table>';
    
    // Fin formulario
    echo "</form>";
    
    
    echo "<center>";
   	echo $OUTPUT->help_icon('assignmentpage','blended');
    echo "</center>";
    
   echo $OUTPUT->footer();
?>

<script type="text/javascript">
<!--
function checkform() {

var num_users=0;
var i;
var alert_no_user=false;
var submit_form=true;

   if ((document.getElementById('assignmentform').assignment_id.value==0) && (submit_form==true)){
       if (document.getElementById('assignmentform').assignment_id.value.length==0) {
           submit_form=false;
           alert("<?php print_string("assignmentnotselected1", "blended") ?>");    
       }  
       else{
           submit_form=true;
       }        
   }

    if((document.getElementById('assignmentform').fullname_code_dni) && (submit_form==true)){
        num_users=document.getElementById('assignmentform').fullname_code_dni.length;
        
        for(i=0;i<num_users;i++){
            if((document.getElementById('assignmentform').fullname_code_dni.options[i].selected == false) && (alert_no_user == false)){
                alert_no_user=true;
            }
            else if((document.getElementById('assignmentform').fullname_code_dni.options[i].selected == true)){
                alert_no_user=false;
                break;
            }
        }
        if(alert_no_user){
            alert("<?php print_string("usernotselected", "blended") ?>");
            submit_form=false;
        }
        else{
            submit_form=true;
        }
   }

   if(submit_form){
       document.getElementById('assignmentform').submit();
   } 
}
// END -->    
</script>