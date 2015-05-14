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
require_once ("locallib.php");
require_once ($CFG->dirroot . '/group/lib.php');
require_once ("grouping_form.php");
require_once ("$CFG->libdir/formslib.php");
require_once ($CFG->libdir . '/filelib.php');


// Get the params ----------------------------------------------------------------
	global $DB, $PAGE, $OUTPUT;
	$id = required_param( 'id', PARAM_INT ); // Course Module ID, or
	$itemid = optional_param('itemid', -1,PARAM_INTEGER);
 //Valores procedentes del formulario SIGNUPFORM (crear nuevo agrupamiento) de introteams.php
	
        $action = required_param('action',PARAM_ALPHA);

	
		if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
			print_error ( "Course Module ID was incorrect" );
		}
		if (! $course = get_course($cm->course)) {
			print_error ( "Course is misconfigured" );
		}
		if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance) )) {
			print_error ( "Course module is incorrect" );
		}
		if (! $context = context_course::instance( $course->id )) {
			print_error ( "Context ID is incorrect" );
                }
	$item = blended_get_item($itemid);

	// Log ---------------------------------------------------------------------------
	
	// A�ade una entrada a la tabla de logs (registros). Estas son
	// acciones m�s concretas que las noticias del servidor web, y
	// proporcionan una forma sencilla de reconstruir qu� ha estado
	// haciendo un usuario en particular.
//	add_to_log ( $course->id, "blended", "createTeams", "createTeams.php?a=$blended->id", "$blended->id" );
	
	// Capabilities ------------------------------------------------------------------
	
	// Esta funci�n comprueba que el usuario actual ha introducido el
	// login en la plataforma y que tiene los privilegios requeridos.
	// Si no han introducido el login los usuarios ser�n rediccionados
	// a la p�gina donde puedan hacerlo, a no ser que $autologinguest
	// est� fijado como true en cuyo caso el usuario entrar� en la
	// plataforma como invitado. Si el usuario no est� dado de alta en
	// el curso se le redirige a la p�gina del curso para darse de alta.
	require_login ( $course->id );
	
	
	$context_course = context_course::instance( $cm->course );
	$context = context_module::instance( $cm->id );
	require_capability ( 'mod/blended:introteams', $context );
	
	// show headings and menus of page
	$url = new moodle_url ( '/mod/blended/introteams.php', array (
			'id' => $id,
			'itemid'=>$itemid,
	) );

	$PAGE->set_url ( $url );
	$PAGE->set_title ( format_string ( $blended->name ) );
	$PAGE->set_heading ( $course->fullname );
	$PAGE->set_pagelayout ( 'standard' );
        
        $strgradepage     = get_string("gradepage","blended");
        $strcreatenewgroupingnotify = get_string('newgroupingnotify','blended');
        
        $strnobodyactive = get_string('noneisactive','blended');
        $link="grades.php?id=".$cm->id;
	$PAGE->navbar->add($strgradepage,$link);
	$PAGE->navbar->add(get_string('autocreategroups','group'));
	// Get assignment name before header ---------------------------------------------	
	if ($item){
            $assignmentname = blended_get_item_html_title ( $item);
        }else{
            $assignmentname = '';
        }
	// Get the strings ---------------------------------------------------------------
	$strintroteamspage = get_string ( "introteams", "blended" );

	// Print the page header ---------------------------------------------------------
	echo $OUTPUT->header ();
	// Print the main part of the page -----------------------------------------------
	echo $OUTPUT->spacer ( array ('height' => 20) );
	echo $OUTPUT->heading ( $strintroteamspage . $assignmentname );
	echo $OUTPUT->spacer ( array ('height' => 30) );
	$teams=array();
	$nummembers = optional_param('nummembers', null,PARAM_ALPHANUM);
               
     
	if ($action=='selectgrouping'){
            $groupingid = required_param('groupingid',PARAM_INT);
	    $grouping = groups_get_grouping( $groupingid);
               $a=new stdClass();
               $a->grouping_name= $grouping->name;
               $a->item = blended_get_item_html_title($item);
               $strcreatenewgroupingnotify = get_string('selectgroupingnotify','blended',$a);
               echo $OUTPUT->box($strcreatenewgroupingnotify);
        }else	
//Si el tipo de accion recogida es 'nuevo' procedente de introteams.php, se crear� un nuevo agrupamiento
	if($action=='create'){
                $creationmethod=required_param('creationmethod',PARAM_ALPHA);
                $numteams = required_param('numteams',PARAM_INT);
          
		//Creaci�n del agrupamiento
                $grouping_name = required_param('grouping_name', PARAM_ALPHANUMEXT);
                $groupingid= blended_create_unique_grouping($grouping_name,$course);
		
                $grouping = groups_get_grouping($groupingid);

		if (!$groupingid){
                    print_error("Can't create a new grouping.");
                }
		
                // Create empty teams
               
                for ($i=0;$i<$numteams;$i++){
                    $team = new stdClass();
                    $team->name = "$grouping->name-$i";
                    $team->members = array();
                    $teams[$i]= $team;
                }
                  //populate teams with userids:	
                if($creationmethod=='random'){
                      list($students,$non_students,$active,$userrecs)= blended_get_users_by_type($context_course);
                      //Si no hay ningun alumno ya sea activo o en el curso matriculado
                        if(count($active)==0){
                                $url2=new moodle_url("/mod/blended/view.php",array('id'=>$blended->id));
                                echo $OUTPUT->notification($strnobodyactive,$url2);
                        }
                        $studentsselection=required_param('studentsselection',PARAM_ALPHANUMEXT);
                            if ($studentsselection == 'activestudents') {
                                    $selection = $active;
                            }else if ($studentsselection == 'allstudents') {
                                    $selection = $students;
                            }
                        $teams=blended_method_random($teams, $selection, $context_course); 
                }
                $errors = blended_insert_teams($teams, $course,$blended, null, $item,null, $id, $USER->id,$groupingid);
                $a=new stdClass();
               $a->grouping_name= $grouping->name;
               $a->num_teams = count($teams);
               $strcreatenewgroupingnotify = get_string('newgroupingnotify','blended',$a);
               echo $OUTPUT->box($strcreatenewgroupingnotify);
	}else	
	/*Si el tipo de acci�n recogida es 'sobreescribir' procedente de updateTeams,php,
 	se sobreescribir�n los grupos del agrupamiento ya existente*/	
	if($action=='sobreescribir'){
		//Obtenci�n del valor del id del agrupamiento
		$groupingid=  blended_get_groupingid($item);
		//Llamada a la funci�n que me borra el agrupamiento
		blended_delete_teams($item);
	}
        else
        {
            print_error('unknownaction','blended',null,$action);
        }
        
	if ($item!==null){
            blended_assign_grouping($item, $grouping, null, $nummembers);
            }
       
// Finish the page -------------------------------------------------
if ($itemid==-1){
            echo $OUTPUT->continue_button(new moodle_url('/mod/blended/view.php',array('id'=>$id)));
}else{
            echo $OUTPUT->continue_button(new moodle_url('/mod/blended/teams/introgrades.php',array('id'=>$id,'itemid'=>$itemid)));
}
	
	echo "</center>";
	echo $OUTPUT->footer ();


?>
<br />
<script type="text/javascript">
<!--

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
                if (!confirm("�Desea eliminar el equipo " + teamnamevalue + "?")){
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
-->
</script>

