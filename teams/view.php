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


	// There are students enroled in course.
    if(!$no_students_in_course_alert) 	{
        // Links relacionados con las etiquetas de codigos de barras
        $link_labels  = "<a href=\"labels.php?id=$id\">".$strlabelspage."</a>";   
        $link_assignment_page  = "<a href=\"teams/assignmentpage.php?id=$id\">".$strassignmentpage."</a>";            
        // Link a calificar actividad 
        $link_gradepage  = "<a href=\"teams/grades.php?id=$id\">".$strgradepage."</a>";
        // Link a gestión de equipos  
        $link_teamsmgnpage = "<a href=\"teams/introteams.php?id=$cm->id\">".$strteamsmanagementpage."</a>"; 
        $url= new moodle_url('/group/autogroup.php',array('courseid'=>$course->id));
        $link_teamsmgnpage .= "<p>Moodle: <a href=\"$url\">".get_string('creategrouping','group')."</a></p>";
        // Link a creaciÃ³n/inscripciÃ³n a equipo
        if($blended->assignment != 0){
            $link_signupteampage='<a href="teams/signupteam.php?id='.$cm->id.'&assignment='.$blended->assignment.'">'.$strsignupteampage.'</a>'; 
        }
        else {
            $link_signupteampage="<a href=\"teams/selectassignment.php?id=$cm->id\">".$strsignupteampage."</a>"; 
        } 
    }
    // Si no hay estudiantes matriculados: links desactivados
    else {
        $link_labels  = "<label>".$strlabelspage."</label>";   
        $link_assignment_page  = "<label>".$strassignmentpage."</label>";   
        $link_gradepage  = "<label>".$strgradepage."</label>";   
        $link_teamsmgnpage = "<label>".$strteamsmanagementpage."</label>";
    }
   
//Imprimimos la tabla con los links.
// CÃ³digo exclusivo para profesores y administradores
    if (has_capability('mod/blended:rolelinks', $context_module)){
	/******
	* Warnings
	********/
	
	// Mensajes de alerta:
        
        
        if (has_capability('mod/blended:viewalerts', $context_module))
        {
        // NingÃºn estudiante matriculado en el curso
        $warnings="";
        if($no_students_in_course_alert){
           $warnings="<p><font size=\"2\"color=\"#FF0000\">$strnostudentsincourse</font></p>\n";
        }
        // NingÃºn estudiante activo en el curso
        if(!$no_students_in_course_alert && $none_is_active_alert ){
        	$message = "noneisactive";
            $warnings.="<p><font size=\"2\" color=\"#FF0000\"><a href=\"alertmessages.php?id=$id&message=$message\">$strnoneisactive</font></a></p>";
        }
        // Hay estudiantes que no estÃ¡n activos
        if(!$no_students_in_course_alert && !$none_is_active_alert && $student_not_active_alert){
            $message = "studentisnotactive";
            $warnings.="<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?id=$id&message=$message\">$strstudentisnotactive</font></a></p>";
        }
        // Hay estudiantes que no han introducido su dni
        if(!$no_students_in_course_alert && !$none_is_active_alert && $no_DNI_alert){
        	$message = "noidnumberview";
            $warnings.="<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?id=$id&message=$message\">$strnoidnumber</font></a></p>";
        }
        // Hay estudiantes que no han introducido su campo de identificador personalizado
        if(!$no_students_in_course_alert && !$none_is_active_alert && !$no_DNI_alert && $no_userinfodata_alert){
        	$message = "nouserinfodataview";
            $warnings.= "<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?id=$id&message=$message\">$strnouserinfodata</font></a></p>";
        }
        echo $OUTPUT->box(format_text($warnings));
        }
		
/**
* Labels
**/	
    echo $OUTPUT->spacer(array('height'=>20));
//    echo $OUTPUT->heading(format_string($strlabelspage));
    $blendedStickersOptions = "<table cellpadding=\"10\" border=0 >
								<tr><td  valign=\"top\">$icons1</td><td> $link_labels
								</td><td valign=\"top\">".get_string('labelsGenerateStickersdesc', 'blended')."</td></tr></table>";
    echo $OUTPUT->box(format_text($blendedStickersOptions),'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));

    
    $linksection3 = new stdClass();
    $linksection3->l2= $link_assignment_page;
    $linksection3->l3 = $link_gradepage;
    $linksection3->l4a = $link_teamsmgnpage;
/**
*  Teamwork management
**/ 
//    echo $OUTPUT->heading(format_string($strmanagement));
   // grade a team
    $icons_grade_team = "<img src=\"images/grade_teams.png\" align=\"left\" height=\"100\"/>";//<img src=\"images/rightArrow.png\" align=\"left\" height=\"50\"/><img src=\"images/grades_team.jpg\" align=\"left\" height=\"100\"/>";
	
    $teamOptions="<table cellpadding=\"10\" border=0 >
								<tr><td valign=\"top\">$icons2</td><td valign=\"top\">$link_assignment_page
								</td><td valign=\"top\">".get_string('managementGenerateTaskSheet', 'blended')."
								<tr><td valign=\"top\">$icons_grade_team</td><td valign=\"top\"> $link_gradepage
								</td><td valign=\"top\">".get_string('managementTeamGrading', 'blended')."</td></tr>".
//                                                                "<tr><td valign=\"top\">$icons4</td><td valign=\"top\"> $link_teamsmgnpage </td><td valign=\"top\">".get_string('managementTeamCreation', 'blended')."</td></tr>".
                                                                "</table>";
    echo $OUTPUT->box(format_text($teamOptions), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
       
    }
 //Links solo visible para estudiantes
    else {
        // Comprobamos que cuando el tipo de identificador es `idnumber` de la 
        // tabla "user", el usuario lo ha introducido. En caso contrario se le redirige 
        // a la pagina de edicion de su perfil.   
        // CÃ³digo EAN a partir de `idnumber` (dni):
        if ( $blended->idtype == 1 && empty($user->idnumber)){
            // Si el estudiante no ha introducido el campo `idnumber` 
            // (esto no afecta a un profesor que no haya introducido su idnumber)
            // Los links relacionados con las etiquetas de codigos de barras
            // se redirigen a la edicion del perfil de usuario:
            // Links relacionados con las etiquetas de codigos de barras
                $message = get_string('noidnumberview','blended');
                $url="<a href=\"../../user/edit.php?id=$USER->id&course=$cm->course\">$message</a>";
                $link_labels="<label>.$strlabelspage.</label>";              
                $link_assignment_page="<label>$strassignmentpage</label>";
                $link_signupteampage="<label>$strsignupteampage</label>"; // Link a creación/inscripción a equipo
                $text_labels = get_string('studentlabelsGenerateStickers', 'blended'). $url;
                $text_assignment = get_string('studentmanagementGenerateTaskSheet', 'blended'). $url;
                $text_signupteam = get_string('studentJoinAGroup', 'blended'). $url;
        } else
        // Código EAN a partir de identificador personalizado:
        if ( substr($blended->idtype,0,1)=="2"){
            
            // Si el estudiante no ha introducido el campo identificador personalizado
            // (esto no afecta a un profesor)
            $fieldid = intval(substr($blended->idtype,1));
           
            if( $iamstudent && !get_field('user_info_data', 'data' ,'userid', $user->id,'fieldid', (int)$fieldid ) ) {
                
                // Los links relacionados con las etiquetas de codigos de barras
                // se redirigen a la edicion del perfil de usuario:
                $message = get_string('nouserinfodataview');
                $url="<a href=\"../../user/edit.php?id=$USER->id&course=$cm->course\">$message</a>";
                $link_labels="<label>.$strlabelspage.</label>";              
                $link_assignment_page="<label>$strassignmentpage</label>";
                $link_signupteampage="<label>$strsignupteampage</label>"; // Link a creación/inscripción a equipo
                $text_labels = get_string('studentlabelsGenerateStickers', 'blended'). $url;
                $text_assignment = get_string('studentmanagementGenerateTaskSheet', 'blended'). $url;
                $text_signupteam = get_string('studentJoinAGroup', 'blended'). $url;
            }
            // Si el estudiante ha introducido el campo `idnumber`
            else { 
            }
        }
        else {//normal links
            $text_labels = get_string('studentlabelsGenerateStickers', 'blended');
            $text_assignment = get_string('studentmanagementGenerateTaskSheet', 'blended');
            $text_signupteam = get_string('studentJoinAGroup', 'blended');
        }
    
    echo $OUTPUT->heading(format_string($strstudent));
    $studentMenu[]=array('icon'=>$icons1, 'link'=>$link_labels,'text'=>$text_labels);
    $studentMenu[]=array('icon'=>$icons2, 'link'=>$link_assignment_page, 'text'=>$text_assignment);
    $studentMenu[]=array('icon'=>$icons4, 'link'=>$link_signupteampage, 'text'=>$text_signupteam);
    
//    $studentMenu=  "<table border=0 >
//                    <tr><td width=\"300\" valign=\"top\">$icons1</td><td valign=\"top\">$link_labels
//                    </td><td valign=\"top\">".get_string('studentlabelsGenerateStickers', 'blended')."
//                    <tr><td valign=\"top\">$icons2</td><td valign=\"top\"> $link_assignment_page
//                    </td><td valign=\"top\">".get_string('studentmanagementGenerateTaskSheet', 'blended')."
//                     </td></tr><tr><td valign=\"top\">$icons4</td><td valign=\"top\"> $link_signupteampage 
//                     </td><td valign=\"top\">".get_string('studentJoinAGroup', 'blended')."
//                     </td></tr><tr><td valign=\"top\">$icons8</td><td valign=\"top\"> $link8 
//                     </td><td valign=\"top\">".get_string('studentReviewResults', 'blended')."
//                    </td></tr></table>";
    $table=new html_table();
    $table->data=$studentMenu;
    
    echo \html_writer::table($table);
   
    } 
