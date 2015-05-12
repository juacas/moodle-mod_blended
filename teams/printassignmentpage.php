<?php
/*********************************************************************************
* Module developed at the University of Valladolid
* Designed and directed by Juan Pablo de Castro with the effort of many other
* students of telecommunication engineering of Valladolid
* Implemented by:
* - Juan Pablo de Castro
* - Pablo Galán Sabugo
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

    
    require_once("../../../config.php");
    require_once("../lib.php");
    require_once("../omr/omrlib.php");
    require_once("locallib.php");
	
// Get the params -------------------------------------------------- 
	global $DB, $PAGE, $OUTPUT;
    $id                 = required_param('id',PARAM_INT); // blended cmid
    $fullname_code_dni  = optional_param('fullname_code_dni', 0, PARAM_RAW);     
    $fullname           = optional_param('fullname',          0, PARAM_RAW);
    $dni                = optional_param('dni',               0, PARAM_RAW);
    $code               = optional_param('code',              0, PARAM_RAW); 
    $assignment_id = required_param('assignment_id', PARAM_INT);// grade item id
    $margins['left']= optional_param('marginleft',10,PARAM_INT);
    $margins['top']= optional_param('margintop',10,PARAM_INT);
    $margins['bottom']= optional_param('marginbottom',10,PARAM_INT);
    $margins['right']= optional_param('marginright',10,PARAM_INT);
     
	
    //En fullname_code_dni se reciben juntos el nombre, el dni y el codigo EAN.
    //Obtenemos por separado $fullname, $code y $dni de $fullname_code_dni
	$tok = strtok ($fullname_code_dni,"&");
    if ($tok != "0") { 
        $fullname = $tok;
        $code     = strtok ("&");
        $dni      = strtok("&");
        $userid		  = strtok("\0");
    }

        if (! $cm = get_coursemodule_from_id('blended', $id)){
            error("Course Module ID was incorrect");
        }
    
        if (! $course = get_course($cm->course)) {
            error("Course is misconfigured");
        }
    
        if (! $blended = $DB->get_record('blended',array('id'=> $cm->instance))) {
            error("Course module is incorrect");
        }
 
   // Log --------------------------------------------------------------
    
   // add_to_log($course->id, "blended", "printassignmentpage", "printassignmentpage.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 

    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
   
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:printassignmentpage', $context);

    // show headings and menus of page
    $url =  new moodle_url('/mod/blended/teams/printassignmentpage.php',array('id'=>$id,'fullname_code_dni'=>$fullname_code_dni,'fullname'=>$fullname,'dni'=>$dni,'code'=>$code,'assignment_id'=>$assignment_id,'marginleft'=>$margins['left'],'margintop'=>$margins['top'],'marginbottom'=>$margins['bottom'],'marginright'=>$margins['right']));
    $PAGE->set_url($url);
    $PAGE->set_title(format_string($blended->name));
    $PAGE->set_heading($course->fullname);
    //$PAGE->set_pagelayout('standard');
   
//Obtenemos la referencia a toda la informaciÃ³n sobre los mÃ³dulos dentro del curso
    // TODO: interceptar id=0 y assignmenname para nombre alternativo
    $item = blended_get_item( $assignment_id);
   
    if (!$item)
    {
            print_error("Bad assignment id. Should select some assignment from list.");
    }
  
   
// Codigo  basado en DNI pero el estudiante no ha introducido su DNI
    if($code==-1 || $code==-2){
  
		// Get the strings --------------------------------------------------
    	$strassignmentpage  = get_string('assignmentpage', 'blended');
    	$strprintassignmentpage  = get_string('printassignmentpage', 'blended');
    	
		// Print the page header --------------------------------------------             
    	echo $OUTPUT->header();

		// Print the main part of the page ----------------------------------
   	echo $OUTPUT->spacer(array('height'=>30));
        
    	$url="assignmentpage.php?id=$id";
    	if($code == -1){
        	$OUTPUT->notify(get_string("cantprintassignmentpage","blended"), $url);
    	}
    	else{
       	 $OUTPUT->notify(get_string("cantprintassignmentpage2","blended"), $url);
    	}
    	
    	echo $OUTPUT->spacer(array('height'=>20));
    }
    
  
  if (true)
  {	
  	pdfAssignmentPage($code, $margins, $fullname,$item,$course,$blended,$gradingmarks=false); 	
  }
  else
  {
  	require_once("php-barcode.php");
	// Imprimimos la hoja de tarea --------------------------------------  

  	$assignmentname= blended_get_item_name($item);
        $assignmenttimedue = blended_get_item_due_date($item);
  	

    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $item_description =  blended_get_item_description($item);
    $description=format_text($item_description->text, $item_description->format, $formatoptions);
   echo "<table width=\"692\" height=\"104\" border=\"1\">";
   	echo   "<tr>";
    echo     "<td width=\"216\" height=\"132\" rowspan=\"4\" bordercolor=\"#000000\"><img src=\"printlabels.php?a=$blended->id&action=barcode&scale=2&code=$code\" width=\"210\" height=\"90\" alt=\"barcode\" /></td>";
    echo     "<td width=\"400\" height=\"23\" bordercolor=\"#000000\"><strong>Tarea:  </strong>".$assignmentname."</td>";
    echo     "<td width=\"100\" bordercolor=\"#000000\"><strong>Calificacion:</strong></td>";
    echo   "</tr>";
    echo   "<tr>";
    echo     "<td height=\"23\" bordercolor=\"#000000\"><strong>Fecha entrega:  </strong>".$assignmenttimedue."</td>";
    echo     "<td width=\"100\" rowspan=\"3\" bordercolor=\"#000000\">&nbsp;</td>";
    echo   "</tr>";
    echo   "<tr>";
    echo     "<td height=\"23\" bordercolor=\"#000000\"><strong>Alumno:  </strong>".$fullname."</td>";
    echo   "</tr>";
    echo   "<tr>";
    echo     "<td height=\"23\" bordercolor=\"#000000\"><strong>DNI:  </strong>".$dni."</td>";
    echo   "</tr>";
   echo "</table>";
  }

?>

