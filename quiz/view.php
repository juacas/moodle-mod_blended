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
    if(!$no_students_in_course_alert) 
	{   
        //Link a generar cuestionario en papel
        $link5 =  "<a href=\"edit_paperquiz.php?a=$blended->id\">".$strgeneratepaperquiz."</a>";
        //Link a iniciar procesado de escaneo
        $link6 = "<a href=\"scan.php?a=$blended->id\">".$strscan."</a>";
        //Link a supervisar estado de la correcciÃ³n
	
        $link7 = "<a href=\"correction.php?a=$blended->id\">".$strcorrection."</a>";
        
        //Link a revisiÃ³n de los test
        $link8 = "<a href=\"revision.php?a=$blended->id\">".$strrevision."</a>";
        
        
        // Comprobamos que cuando el tipo de identificador es `idnumber` de la 
        // tabla "user", el usuario lo ha introducido. En caso contrario se le redirige 
        // a la pagina de edicion de su perfil.   
    
        /*
        $iamstudent            = false;
        $no_DNI_alert          = false;
        $no_userinfodata_alert = false;
        
        foreach ($students as $student){
            
             //Comprobamos si el usuario estÃ¡ activo
             if(blended_is_not_active_student($student->id, $course)){
                 $student_not_active_alert=true;
             }
            
             $user = get_record('user','id',$student->id);
             $code = blended_gen_idvalue ($user, $blended);
             
             // CÃ³digo EAN a partir de `idnumber` (dni): el estudiante no lo ha introducido
             // en su perfil: $code == -1
             if($code == -1){
                 $no_DNI_alert = true;
             }  
             else if($code == -2){
                 $no_userinfodata_alert = true;
             }    
            
            // Comprobar si el usuario actual del modulo es un estudiante
            if($student->id == $USER->id){
                $iamstudent = true;
                break;
            }        
        }
	*/
        // CÃ³digo EAN a partir de `idnumber` (dni):
        if ( $blended->idtype == 1 ){
            
            // Si el estudiante no ha introducido el campo `idnumber` 
            // (esto no afecta a un profesor que no haya introducido su idnumber)
            if(  $iamstudent && empty($user->idnumber) ){
                
                // Los links relacionados con las etiquetas de codigos de barras
                // se redirigen a la edicion del perfil de usuario:
                
                // Links relacionados con las etiquetas de codigos de barras
                $link_labels="<a href=\"javascript:confirma('../../user/edit.php?id=$USER->id&course=$cm->course')\">".$strlabelspage."</a>";   
                $link_assignment_page="<a href=\"javascript:confirma('../../user/edit.php?id=$USER->id&course=$cm->course')\">".$strassignmentpage."</a>";
                
                // Link a creaciÃ³n/inscripciÃ³n a equipo
                $link_signupteampage="<a href=\"../../user/edit.php?id=$USER->id&course=$cm->course\">".$strsignupteampage."</a>";
            }
            // Si el estudiante ha introducido el campo `idnumber`
            else { 
            }
        } 
        
        // CÃ³digo EAN a partir de identificador personalizado:
        if ( substr($blended->idtype,0,1)=="2"){
            
            // Si el estudiante no ha introducido el campo identificador personalizado
            // (esto no afecta a un profesor)
            $fieldid = intval(substr($blended->idtype,1));
            if(  $iamstudent && !get_field('user_info_data', 'data' ,'userid', $user->id, 
                                                      'fieldid', (int)$fieldid ) ) {
                
                // Los links relacionados con las etiquetas de codigos de barras
                // se redirigen a la edicion del perfil de usuario:
                
                // Links relacionados con las etiquetas de codigos de barras
                $link_labels="<a href=\"javascript:confirma2('../../user/edit.php?id=$USER->id&course=$cm->course')\">".$strlabelspage."</a>";   
                $link_assignment_page="<a href=\"javascript:confirma2('../../user/edit.php?id=$USER->id&course=$cm->course')\">".$strassignmentpage."</a>";
                
                // Link a creaciÃ³n/inscripciÃ³n a equipo
                $link_signupteampage="<a href=\"javascript:confirma2('../../user/edit.php?id=$USER->id&course=$cm->course')\">".$strsignupteampage."</a>";
            }
            // Si el estudiante ha introducido el campo `idnumber`
            else { 
            }
        } 
        
        
    }
    // Si no hay estudiantes matriculados: links desactivados
    else {
        $link_labels  = "<label>".$strlabelspage."</label>";   
        $link_assignment_page  = "<label>".$strassignmentpage."</label>";   
        $link_gradepage  = "<label>".$strgradepage."</label>";   
        $link_teamsmgnpage = "<label>".$strteamsmanagementpage."</label>";
		//Link a generar cuestionario en papel
        $link5 =  "<a href=\"edit_paperquiz.php?a=$blended->id\">".$strgeneratepaperquiz."</a>";
        
        //Link a iniciar procesado de escaneo
        $link6 = "$strscan";
        
        //Link a supervisar estado de la correciï¿½n
        $link7 = "$strcorrection";
        
        //Link a revisiÃ³n de los test
        $link8 = "<label>".$strrevision."</label>";
    }

	
    
//Imprimimos la tabla con los links.
// CÃ³digo exclusivo para profesores y administradores
    if (has_capability('mod/blended:rolelinks', $context_module)) 
	{
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
            $warnings.="<p><font size=\"2\" color=\"#FF0000\"><a href=\"alertmessages.php?a=$blended->id&message=$message&srid=$studentroleid\">$strnoneisactive</font></a></p>";
        }
        // Hay estudiantes que no estÃ¡n activos
        if(!$no_students_in_course_alert && !$none_is_active_alert && $student_not_active_alert){
            $message = "studentisnotactive";
        	$warnings.="<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?a=$blended->id&message=$message&srid=$studentroleid\">$strstudentisnotactive</font></a></p>";
        }
        // Hay estudiantes que no han introducido su dni
        if(!$no_students_in_course_alert && !$none_is_active_alert && $no_DNI_alert){
        	$message = "noidnumberview";
            $warnings.="<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?a=$blended->id&message=$message&srid=$studentroleid\">$strnoidnumber</font></a></p>";
        }
        // Hay estudiantes que no han introducido su campo de identificador personalizado
        if(!$no_students_in_course_alert && !$none_is_active_alert && !$no_DNI_alert && $no_userinfodata_alert){
        	$message = "nouserinfodataview";
            $warnings.= "<p><font size=\"2\"color=\"#FF0000\"><a href=\"alertmessages.php?a=$blended->id&message=$message&srid=$studentroleid\">$strnouserinfodata</font></a></p>";
        }
        echo $OUTPUT->box(format_text($warnings));
        }
		
/**
* Labels
**/	
    echo $OUTPUT->spacer(array('height'=>20));
    echo $OUTPUT->heading(format_string($strlabelspage));
    $blendedStickersOptions = "<table border=0 >
								<tr><td width=\"300\"  valign=\"top\">$icons1</td><td> $link_labels
								</td><td valign=\"top\">".get_string('labelsGenerateStickersdesc', 'blended')."</td></tr></table>";
    echo $OUTPUT->box(format_text($blendedStickersOptions),'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));

    $linksection2 = new stdClass();
    $linksection2->l5 = $link5;
    $linksection2->l6 = $link6;
    $linksection2->l7 = $link7;
    $linksection2->l8 = '';
    
/**
* BlendedQuiz Options
**/
 if ($blended->omrenabled)
 {
    echo $OUTPUT->heading(format_string($strblendedquizzes));
	
	$blendedQuizOptions = "<table border=0 >
								<tr><td width=\"300\" valign=\"top\">$icons5</td><td valign=\"top\">$link5
								</td><td valign=\"top\">".get_string('blendedquizPrintPDFdesc', 'blended')."
								<tr><td valign=\"top\">$icons6</td><td valign=\"top\"> $link6
								</td><td valign=\"top\">".get_string('blendedquizUploadScansdesc', 'blended')."
								 </td></tr><tr><td valign=\"top\">$icons7</td><td valign=\"top\"> $link7 
								 </td><td valign=\"top\">".get_string('blendedquizReviewScansdesc', 'blended')."
								 </td></tr><tr><td valign=\"top\" >$icons8</td><td valign=\"top\"> $strrevision
								 </td><td valign=\"top\">".get_string('blendedquizReviewResultsdesc', 'blended')."
								</td></tr></table>";
	
    echo $OUTPUT->box(format_text($blendedQuizOptions), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
 }
    $linksection3 = new stdClass();
    $linksection3->l2= $link_assignment_page;
    $linksection3->l3 = $link_gradepage;
    $linksection3->l4a = $link_teamsmgnpage;
/**
*  Teamwork management
**/ 
    echo $OUTPUT->heading(format_string($strmanagement));
   
    $teamOptions="<table border=0 >
								<tr><td width=\"300\" valign=\"top\">$icons2</td><td valign=\"top\">$link_assignment_page
								</td><td valign=\"top\">".get_string('managementGenerateTaskSheet', 'blended')."
								<tr><td valign=\"top\">$icons3</td><td valign=\"top\"> $link_gradepage
								</td><td valign=\"top\">".get_string('managementTeamGrading', 'blended')."
								 </td></tr><tr><td valign=\"top\">$icons4</td><td valign=\"top\"> $link_teamsmgnpage 
								 </td><td valign=\"top\">".get_string('managementTeamCreation', 'blended')."
								</td></tr></table>";
    echo $OUTPUT->box(format_text($teamOptions), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
       
    }
    
 //Links solo visible para estudiantes
    else {
    $linksection4 = new stdClass();
    $linksection4->l1= $link_labels;
    $linksection4->l2 = $link_assignment_page;
    $linksection4->l8 = $link8;

     if($blended->teammethod == 0)
     {
     	$linksection4->l4b = $link_signupteampage;
     	
     }
     else{
     	$linksection4->l4b= 'No configurado por el administrador.';
     	
     }
     
    echo $OUTPUT->heading(format_string($strstudent));
    $studentMenu=  "<table border=0 >
								<tr><td width=\"300\" valign=\"top\">$icons1</td><td valign=\"top\">$link_labels
								</td><td valign=\"top\">".get_string('studentlabelsGenerateStickers', 'blended')."
								<tr><td valign=\"top\">$icons2</td><td valign=\"top\"> $link_assignment_page
								</td><td valign=\"top\">".get_string('studentmanagementGenerateTaskSheet', 'blended')."
								 </td></tr><tr><td valign=\"top\">$icons4</td><td valign=\"top\"> $link_signupteampage 
								 </td><td valign=\"top\">".get_string('studentJoinAGroup', 'blended')."
								 </td></tr><tr><td valign=\"top\">$icons8</td><td valign=\"top\"> $link8 
								 </td><td valign=\"top\">".get_string('studentReviewResults', 'blended')."
								</td></tr></table>";
    echo $OUTPUT->box(format_text($studentMenu), 'generalbox', 'intro');
    echo $OUTPUT->spacer(array('height'=>20));
   
    } 
