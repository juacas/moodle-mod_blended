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
require_once ("$CFG->dirroot/group/lib.php");
require_once ("grouping_form.php");
require_once ("locallib.php");
require_once ("../blended_locallib.php");
require_once ("$CFG->libdir/formslib.php");
require_once ("$CFG->libdir/filelib.php");


// Get the params ----------------------------------------------------------------
	global $DB, $PAGE, $OUTPUT;
	$id = required_param ( 'id', PARAM_INT ); // Course Module ID, or
	$itemid = optional_param ( 'itemid', -1, PARAM_INT );//id actividad
	$creationmethod = optional_param ( 'creationmethod', 'byhand', PARAM_ALPHA );//metodo para crear nuevos grupos
	$studentsselection = optional_param ( 'studentsselection', 'activestudents', PARAM_ALPHA );//activos o todos

		if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
			print_error ( "Course Module ID was incorrect" );
		}
		if (! $course = get_course($cm->course)) {
			print_error ( "Course is misconfigured" );
		}
		if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance) )) {
			print_error ( "Course module is incorrect" );
		}


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
	
	
	$context_course = context_course::instance( $cm->course );
	if (! get_role_users ( 5, $context_course, false, 'u.id, u.lastname, u.firstname' )) {
		error ( get_string ( 'errornostudents', 'blended' ) );
	}

	$context = context_module::instance( $cm->id );
	require_capability ( 'mod/blended:introteams', $context );


// Get assignment name before header ---------------------------------------------
	$item = blended_get_item( $itemid);
        if (!$item){
            $assignmentname ='';
        }else{
            	$assignmentname = blended_get_item_name ( $item );
        }

// Get the strings ---------------------------------------------------------------
	$strteamsmanagementpage     = get_string("teamsmanagementpage","blended");
	$strintroteamspage = get_string ( "introteams", "blended" );
	$strcreationmethod          = get_string("creationmethod", "blended");
	$strbyhand                  = get_string("byhand", "blended");
	$strrandomly                = get_string("randomly", "blended");
	$strstudentsselection       = get_string('studentsselection','blended');
	$stractivestudents          = get_string('activestudents','blended');
	$strallstudents             = get_string('allstudents','blended');
	$strnumteams                = get_string('numteams', 'blended');
	$strnummembers              = get_string('nummembers', 'blended');
        $strgradepage = get_string("gradepage", "blended");
        $strintrogradepage = get_string('introgradepage', 'blended');
    
// Print the page header ---------------------------------------------------------
        // show headings and menus of page-----------------------------------------
	$url = new moodle_url ( '/mod/blended/teams/introteams.php', array (	
		'id' => $id,
	) );
	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );
	$link=new moodle_url("grades.php",array('id'=>$id));;
	$PAGE->navbar->add($strteamsmanagementpage,$link);
	$PAGE->navbar->add('Gestión de Equipos');
	echo $OUTPUT->header ();

// Print the main part of the page -----------------------------------------------
    $heading= format_string($strintrogradepage );
    if ($item){
    $module_link = blended_get_item_html_title($item);
    $grouping = blended_get_grouping($item, $blended);
    $heading .= ": ". $module_link;
    }
    if (isset($grouping) && $grouping){
        $groupingid=$grouping->id;
        $heading.= " ".get_string('teams_from', 'blended') .
        $OUTPUT->action_link(new moodle_url('/group/overview.php', array('id' => $course->id)), $grouping->name);
    }else{
        $groupingid=null;
    }
 echo $OUTPUT->heading($heading);
       

//Elegir o crear AGRUPAMIENTO-------------------------------------------------------
//Elegir AGRUPAMIENTO existente----------------------------
	if ($item){	
		//Compruebo si existe algun agrupamiento:
		if ($groupings = groups_get_all_groupings ( $course->id )) {
                    $selected_groupingid = blended_get_groupingid($item);
			//Si existe:
                        echo $OUTPUT->box_start();

                        echo $OUTPUT->heading(get_string('select_grouping','blended'),3);

			//Se muestran los agrupamientos existentes mediante un select:		
			$url1="createTeams.php";
			echo "<form name=\"f1\" action=\"$url1\" method=\"GET\">";
			echo "<select name='groupingid'>";	
			foreach($groupings as $i=>$grouping){
                                $selected= $i==$selected_groupingid?'selected="selected"':'';
				echo "<option value=\"$i\" $selected >$grouping->name</option>";
			}
			echo "</select>";
			echo "<input type='submit' value='continuar'>";
			echo "<input type='hidden' name='id' value='".$cm->id."'>";
			echo "<input type='hidden' name='itemid' value='".$itemid."'>";
			echo "<input type='hidden' name='action' value='select_grouping'>";
			echo "</form>";
                                echo $OUTPUT->box_end();

		}		
		//Crear un nuevo AGRUPAMIENTO-----------------------------------
        }
			// Tabla que permite reinicializar los equipos del agrupamiento---------------------------------
	$numteamspage = array_combine ( range ( 1, TEAMS_MAX_ENTRIES ), range ( 1, TEAMS_MAX_ENTRIES ) );
	$nummembersteam = array_combine ( range ( 1, MEMBERS_MAX_ENTRIES ), range ( 1, MEMBERS_MAX_ENTRIES ) );
	
	//Form SIGNUPFORM
	
        echo $OUTPUT->box_start();
        echo $OUTPUT->heading(get_string('teamsmanagementpagedesc','blended'),3);
        $url2 = "createTeams.php?id=" . $cm->id;
	echo "<form method=\"POST\" id=\"signupform\" name=\"signupform\" action =\"$url2\">";
		//Table	
		echo '<table  width="30%" cellspacing="10" cellpadding="5" >';
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
		echo "<tr><td><label for=\"nummembers\" >$strnummembers</label></td>";
		echo '<td><select name="nummembers" id="nummembers" align="left">';
			foreach ( $nummembersteam as $key => $val ) {
				if ($key == $blended->nummembers) {
					echo "<option value=\"$key\" selected=\"selected\">$val</option>";
				} else {
					echo "<option value=\"$key\">$val</option>";
				}
			}
		echo "</select></td></tr>";		
		echo "<tr><td><label for=\"grouping_name\">".get_string('groupingname','group')."</label></td>";
                echo "<td><input type=\"text\" name=\"grouping_name\" value=\"\"/></td></tr>";
		//Botón REINICIALIZAR EQUIPOS
		echo "<tr><td><input type=\"submit\" value=\"".get_string('resetgroups','blended')."\"></td></tr>";
		echo "<input type=\"hidden\" name=\"itemid\" value=\"$itemid\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"create\">";
		echo "<input type=\"hidden\" name=\"groupingid\" value=\"$groupingid\">";	
		//Fin de la tabla
		echo '</table>';
	//Fin del formulario	
	echo "</form>";
	echo $OUTPUT->box_end();
        
// Finish the page -------------------------------------------------

echo "<BR><BR><center>";
echo $OUTPUT->help_icon ( 'introteams', 'blended' );
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



function checkData(numTeams,numMembers, url){
    var deleteteam;
    var idteam;        
    var teamname;
    var teamnamevalue;       
    var membername;         
    var t;
    var m;
    var found=0;

    for(t=1;t<=numTeams; t++){
        deleteteam    = "delete_team_" + t;
        idteam        = "team_" + t + "_id";
        teamname      = "team_" + t + "_name";
                       
        for(m=1;m<=numMembers; m++){
            membername    = "team_" + t + "_member_" + m;

            if (document.getElementById(membername).value=="") {
                found++;
            }
        }//Fin for members
                
        if (found == numMembers){

            document.getElementById(teamname).disabled="true";
            
            for(m=1;m<=numMembers; m++){
                membername    = "team_" + t + "_member_" + m;
                document.getElementById(membername).disabled="true";            
            }
            
            if(document.getElementById(deleteteam)){
                
                teamnamevalue = document.getElementById(teamname).value;
                if(document.getElementById(deleteteam).disabled){
                document.getElementById(deleteteam).disabled=!document.getElementById(deleteteam).disabled;
                document.getElementById(deleteteam).checked ="true";
                if (!confirm("¿Desea eliminar el equipo $teamnamevalue ?")){
                    document.getElementById(deleteteam).disabled=!document.getElementById(deleteteam).disabled;
                    document.getElementById(deleteteam).checked =!document.getElementById(deleteteam).checked;                    
                }    
                }           
            }
            
        }//Fin if
        
        found=0;
            
    }//Fin for teams
    //return true;
    document.teamsform.action=url; 
    document.teamsform.submit()
}

</script>