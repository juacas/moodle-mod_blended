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

* @author Jessica Olano Lopez,Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
* @license http://www.gnu.org/copyleft/gpl.html GNU Public License
* @package blended
*********************************************************************************/
 
    require_once("../../config.php");
    require_once("lib.php");

// Get the params --------------------------------------------------
    global $DB, $PAGE, $OUTPUT;
    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a', 0,  PARAM_INT); // blended ID
    
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
    } else {
        if (! $blended = $DB->get_record('blended', array( 'id'=> $a))) {
            error("Course module is incorrect");
        }
        if (! $course = $DB->get_record('course', array('id' => $blended->course))) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
        if (! $user = $DB->get_record('user', array ( 'id' => $USER->id))) {
            error("No such user in this course");
        }
    }

// Log --------------------------------------------------------------
    
    add_to_log($course->id, "blended", "labels", "labels.php?a=$blended->id", "$blended->id");

// Capabilities -----------------------------------------------------
    
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printlabels', $context);
    
    
// show headings and menus of page-------------------------------------
    $url =  new moodle_url('/mod/blended/labels.php',array('id'=>$id,'a'=>$a));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
    
// Get the strings -------------------------------------------------- 
    
    $strlabelspage      	 = get_string('labelspage', 'blended');
    $strlabelspagedescr1	 = get_string('labelspagedescr1', 'blended');  
    $strlabelspagedescr2	 = get_string('labelspagedescr2', 'blended');                  
    $strnumrows	  	         = get_string('numrows', 'blended');
    $strnumcolumns    		 = get_string('numcolumns', 'blended'); 
    $struser             	 = get_string('user','blended');
    $strnoactiveuser     	 = get_string('noactiveuser','blended');
    $strnoidnumber      	 = get_string('noidnumber','blended');
    $strnouserinfodata  	 = get_string("nouserinfodata","blended");
    $strprintlabels     	 = get_string("printlabels", "blended");
    $strpageformat			 = get_string("pageformat", "blended");
    $strprintforone 		 = get_string("printforone","blended");
    $strlayoutmethod		 = get_string("layoutmethod","blended");
    $stroneforeachactive	 = get_string("oneforeachactive","blended");
    $stroneforeachenrolled	 = get_string("oneforeachenrolled","blended");
    $strfullpages			 = get_string("fullpages","blended");
    $strlabelsformat		 = get_string("labelsformat","blended");
    $stridentifyforhumans	 = get_string("identifyforhumans","blended");
 	$strdonotidentify		 = get_string("donotidentify","blended");
 	$strshowreadableid		 = get_string("showreadableid","blended");
 	$strshowfullname		 = get_string("showfullname","blended");
   
 	
    if(has_capability('mod/blended:selectoneamongallstudents', $context)){
        $strlabelspagedescr = $strlabelspagedescr1;
    }
    else{
        $strlabelspagedescr = $strlabelspagedescr2;
    }
 
// Print the page header --------------------------------------------  
    $PAGE->navbar->add($strlabelspage);
    
	echo $OUTPUT->header();

// Print the main part of the page ----------------------------------

    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($strlabelspage));
    echo'<center>';
    echo $OUTPUT->box(format_text($strlabelspagedescr), 'generalbox', 'intro');
    echo'</center>';
    echo $OUTPUT->spacer(array('height'=>20));

// Imprimimos el formulario por pantalla ----------------------------- 
   
    $scale ="2"; 
    $rowsoptions    = array_combine(range(1, ROWS_MAX_ENTRIES),range(1, ROWS_MAX_ENTRIES));                                        
    $columnsoptions = array_combine(range(1, COLUMNS_MAX_ENTRIES),range(1, COLUMNS_MAX_ENTRIES));
    
    // Form ROWSCOLUMNSFORM
    
    $url = "printCourseLabels.php";
    
    echo "<form method=\"post\" action=\"$url\"  id=\"rowscolumnsform\">";
       echo '<input name="a" value="'.$blended->id.'" type="hidden"/>';
        // Table
       echo '<fieldset ><legend style="color:#2A0A1B; font-style:italic; font-weight:bold; font-size:15px;">'.$strpageformat.':</legend>'; 
       echo '<table  width="60%" cellspacing="10" cellpadding="5" >';
       
          // Page Configuration
         echo "<tr><td  rowspan=\"2\"><label>".get_string("pageformat","blended")."</label></td>";   
         echo '<td rowspan="2"><select name="pageformat" align="left">';
         echo '<option value="A4" selected="selected">A4</option>';
         echo '<option value="A3" >A3</option>';
         echo '<option value="B4" >B4</option>';
         echo '<option value="B3" >B3</option>';
         echo '<option value="C4" >C4</option>';
         echo '<option value="C3" >C3</option>';
         echo "</select></td></tr>";
         
         echo "<td><label>".get_string("margin_top_mm","blended")."</label></td>";
         echo '<td><input name="margintop" align="left" size="4" value="5">';
        
         echo "<td><label>".get_string("margin_bottom_mm","blended")."</label></td>";
         echo '<td><input name="marginbottom" align="left" size="4" value="5">';
         
         echo "<tr><td></td><td></td><td><label>".get_string("margin_left_mm","blended")."</label></td>";
         echo '<td><input name="marginleft" align="left" size="4" value="8">';
        
         echo "<td><label>".get_string("margin_right_mm","blended")."</label></td>";
         echo '<td><input name="marginright" align="left" size="4" value="8">';
             
         // Etiqueta de la lista desplegable
         echo "<tr><td><label>$strnumrows</label></td>";       
      
         // Lista desplegable NUMROWS
         echo '<td><select name="numrows" align="left">';
         foreach ($rowsoptions as $key => $val) {
            //Valor seleccionado
            if($key=="17"){
                echo "<option value=\"$key\" selected=\"selected\">$val</option>";
            }
            else {
                echo "<option value=\"$key\">$val</option>";
            }
         }
         echo "</select></td></tr>";
        
         // Etiqueta de la lista desplegable
         echo "<tr><td><label>$strnumcolumns</label></td>";     
        
         // Lista desplegable NUMCOLUMNS
         echo '<td><select name="numcolumns" align="left">';
         foreach ($columnsoptions as $key => $val) {
            // Valor seleccionado
            if($key=="4"){
                echo "<option value=\"$key\" selected=\"selected\">$val</option>";
            }
            else {
                echo "<option value=\"$key\">$val</option>";
            }
         }
         echo "</select></td></tr>";
       echo "</table></fieldset>";
        
        
       // Codigo exclusivo para profesores y administradores
       if (has_capability('mod/blended:selectoneamongallstudents', $context))
       {    
           // Obtenemos todos los estudiantes del curso 	
            if($userids = blended_get_course_students_ids ($course, null, false)){
   				echo '<table><tr><td>';
   				echo '<fieldset><legend style="color:#2A0A1B; font-style:italic; font-weight:bold; font-size:15px;">'.$strprintforone.':</legend>';
                echo '<table  align="center" width="40%" cellspacing="10" cellpadding="5" >';
                
                // Etiqueta de la lista desplegable
                echo "<tr><td valign=\"top\"><label>$struser</label></td>";              
              
                //calculamos el numero de el numero de filas que seran mostradas al mismo tiempo       
                $size=count($userids);
                if($size>5){
                    // Como maximo 5
                    $size=5;   
                }

                // Lista desplegable CODE           
                echo '<td><select name="users[]" align="left" multiple="multiple" size='.$size.'>';
                $not_DNI_out          = false;
                $not_userinfodata_out = false;
                $not_active_out       = false;
              
                foreach ($userids as $userid) {
                    
                    $not_DNI_in          = false;
                    $not_userinfodata_in = false;
                    $not_active_in       = false;
                    
                    //Comprobamos si el usuario esta activo
                    if(blended_is_not_active_student($userid, $course)){
                        $not_active_in  = true;
                        $not_active_out = true;
                    }           
                    // Obtenemos el objeto 'user' de cada estudiante del curso
                    $user = $DB->get_record('user',array('id'=>$userid));
                   
                    // Obtenemos el codigo EAN de la etiqueta de cada estudiante 
                    $code = blended_gen_idvalue ($user, $blended) ;
               
                    // Si el id es el dni y el estudiante no lo ha introducido en su perfil
                    if($code == -1){
                        $not_DNI_in=true;
                        $not_DNI_out=true;
                    }    
                    else if($code == -2){
                        $not_userinfodata_in=true;
                        $not_userinfodata_out=true;
                    }  
                    // Obtenemos el nombre completo de cada estudiante
                    $fullname=fullname($user, true);
                    if($not_active_in){
                        $fullname = $fullname." (*)";
                    }                    
                    if($not_DNI_in){
                        $fullname = $fullname." (**)";
                    }
                    if($not_userinfodata_in){
                        $fullname = $fullname." (#)";
                    }

                    // Nombre de la opci�n de la lista desplegable
                    if($not_DNI_in){ 
                        echo "<option value=\"-1\">".$fullname."</option>";      
                    }    
                    else if ($not_userinfodata_in){
                        echo "<option value=\"-2\">".$fullname."</option>";  
                    }
                    else{
                        echo "<option value=\"$user->id\">".$fullname."</option>";
                    }     
                }  
                echo "</select></td></tr>";
                echo '</fieldset>';
                // Mostramos un aviso cuando hay estudiantes que no introdujeron su dni               
           }   
           // Si no hay ningun usuario en el curso
           else {    
            	echo 'No hay ning�n usuario matriculado en el curso';            
           }    
           if($not_active_out){
               //Etiqueta del campo de texto
               echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnoactiveuser</font></center></td></tr>";
           }
           if($not_DNI_out){
               echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnoidnumber</font></center></td></tr>";
           }
           if($not_userinfodata_out){
               echo "<tr><td colspan=\"2\"><center><font size=\"2\"color=\"#FF0000\">$strnouserinfodata</font></center></td></tr>";
           }
        // Tabla 
        echo '</table>';
        echo '<td height="100%" valign="TOP">';
        
        echo '<fieldset><legend style="color:#0B3861; font-size:12px;">';
        echo $strlayoutmethod.':</legend>';
        echo '<br/><label for="active"><input type="radio" name="whatstudents" checked="true" id="active" value="active">'.$stroneforeachactive.'</input></label>';
        echo '<br/><label for="all"><input type="radio" name="whatstudents" id="all" value="all">'.$stroneforeachenrolled.'</input></label>';
        echo '<br/><label for="list"><input type="radio" name="whatstudents" id="list" value="list">'.$strfullpages.'</input></label>';
        echo '</fieldset>';           
      }    
        
      //Codigo exclusivo para estudiantes
      else {
         echo '<input type="hidden" name="whatstudents" value="list"/>';       
         echo "<input type=\"hidden\" name=\"users[]\" value=\"$user->id\"";        
      }    

	  echo '<fieldset ><legend style="color:#0B3861; font-size:12px;">'.$strlabelsformat.':</legend>';
        echo '<br/><label for="none"><input type="radio" id="none" name="identifyLabel" checked="true" value="none">'.$strdonotidentify.'</input></legend>';
        echo '<br/><label for="id"><input type="radio" name="identifyLabel" id="id" value="id">'.$strshowreadableid.'</input></legend>';
        echo '<br/><label for="fullname"><input type="radio" name="identifyLabel" id="fullname" value="fullname">'.$strshowfullname.'</input></legend>';
	  echo '</fieldset>';
     // Fin tabla 
     echo '</table>';
        
     echo $OUTPUT->spacer(array('height'=>30));
        
     // Boton IMPRIMIR ETIQUETAS
   	 echo '<table align="center">';
    	echo "<tr><td><input type=\"button\" value=\"".$strprintlabels."\" onClick=\"checkform()\" /></td></tr>";
     echo '</table>';    
        
    // Fin formulario
    echo "</form>";

    echo "<center>";
    echo $OUTPUT->help_icon('pagehelp','blended');
    echo "</center>";

    // Finish the page -------------------------------------------------
    
    echo $OUTPUT->footer();     
?>
<script type="text/javascript">
<!--
function checkform() {

var num_users=0;
var i;
var submit_form=true;
var form=document.getElementById('rowscolumnsform');
var whatstudents=form.whatstudents[2];
    if(typeof(whatstudents) !== 'undefined' && whatstudents.checked)
        {

        num_users=form.elements["users[]"].length;
        
        var alert_no_user=true;
       for(i=0;i<num_users;i++){
            if(form.elements["users[]"].options[i].selected == true)
            {
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
	form.submit();
   }
}


// END -->    
</script>
