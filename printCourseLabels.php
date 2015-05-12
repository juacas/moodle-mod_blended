<?php
/*********************************************************************************
 * Module developed at the University of Valladolid
 * Designed and directed by Juan Pablo de Castro with the effort of many other
 * students of telecommunication engineering of Valladolid
 * Implemented by Juan Pablo de Castro
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

 * @author J�ssica Olano L�pez, Juan Pablo de Castro.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 *********************************************************************************/
require_once ("../../config.php");
require_once ("lib.php");
require_once("blended_locallib.php");
require_once ("omr/omrlib.php");

// Get the params --------------------------------------------------
global $DB, $PAGE, $OUTPUT;
$id = optional_param ( 'id', 0, PARAM_INT );
$a = optional_param ( 'a', 0, PARAM_INT );
$action = optional_param ( 'do', 'sheet', PARAM_ALPHA );
$scale = optional_param ( 'scale', 2, PARAM_INT );
$identifyLabel = optional_param ( 'identifyLabel', 'none', PARAM_ALPHA );
$whatstudents = optional_param ( 'whatstudents', 'active', PARAM_ALPHA );

$margins ['left'] = optional_param ( 'marginleft', 10, PARAM_INT );
$margins ['top'] = optional_param ( 'margintop', 10, PARAM_INT );
$margins ['bottom'] = optional_param ( 'marginbottom', 10, PARAM_INT );
$margins ['right'] = optional_param ( 'marginright', 10, PARAM_INT );

if ($id) {
	if (! $cm = get_coursemodule_from_id ( 'blended', $id )) {
		error ( "Course Module ID was incorrect" );
	}
	if (! $course = get_course($cm->course)) {
		error ( "Course is misconfigured" );
	}
	
	if (! $blended = $DB->get_record ( 'blended', array ('id' => $cm->instance ) )) {
		error ( "Course module is incorrect" );
	}
	if (! $user = $DB->get_record ( 'user', array ('id' => $USER->id ) )) {
		error ( "No such user in this course" );
	}
} else {
	if (! $blended = $DB->get_record ( 'blended', array ('id' => $a ) )) {
		error ( "Course module is incorrect" );
	}
	if (! $course = $DB->get_record ( 'course', array ('id' => $blended->course ) )) {
		error ( "Course is misconfigured" );
	}
	if (! $cm = get_coursemodule_from_instance ( "blended", $blended->id, $course->id )) {
		error ( "Course Module ID was incorrect" );
	}
	if (! $user = $DB->get_record ( 'user', array ('id' => $USER->id ) )) {
		error ( "No such user in this course" );
	}
}
// Log --------------------------------------------------------------

//add_to_log ( $course->id, "blended", "printCourselabels", "printCourseLabels.php?id=$blended->id", "$blended->id" );

// Capabilities -----------------------------------------------------

require_login ( $cm->course, false, $cm );

$context_course = context_course::instance ( $cm->course );
if (! $students = get_role_users ( 5, $context_course, false, 'u.id, u.lastname, u.firstname' )) {
	error ( "No students in this course" );
}

$context = context_module::instance ( $cm->id );
require_capability ( 'mod/blended:printlabels', $context );

// show headings and menus of page ----------------------------------
$url = new moodle_url ( '/mod/blended/printCourseLabels.php', array (
		'id' => $id,
		'a' => $a,
		'do' => $action,
		'scale' => $scale,
		'identifyLabel' => $identifyLabel,
		'whatstudents' => $whatstudents,
		'marginleft' => $margins ['left'],
		'margintop' => $margins ['top'],
		'marginbottom' => $margins ['bottom'],
		'marginright' => $margins ['right'] 
) );
$PAGE->set_url ( $url );
$PAGE->set_title ( format_string ( $blended->name ) );
$PAGE->set_heading ( $course->fullname );

// Print the labels --------------------------------------------

// Codigo EAN basado en DNI cuando el estudiante no ha introducido su DNI

$code = blended_gen_idvalue ( $user, $blended );

if ($code == - 1 || $code == - 2) {
	
	// Print the page header --------------------------------------------
	
	$strlabelspage = get_string ( 'labelspage', 'blended' );
	echo $OUTPUT->header ();
	
	// Print the main part of the page ----------------------------------
	
	echo $OUTPUT->spacer ( array (
			'height' => 30 
	) );
	
	//Print the messages ---------------------
	$url = "labels.php?a=$blended->id";
	if ($code == - 1) {	
		notice ( get_string ( "cantprintlabel", "blended" ), $url );
	} else if ($code == - 2) {
		notice ( get_string ( "cantprintlabel2", "blended" ), $url );
	}
	echo $OUTPUT->spacer ( array ('height' => 20 ) );
}

$numrows = required_param ( 'numrows', PARAM_INT );
$numcolumns = required_param ( 'numcolumns', PARAM_INT );
$url1 = "labels.php?a=$blended->id";
//Comprobación de que el numero de filas y columnas no supera los valores máximos
if($numrows >30 || $numrows<1 || $numcolumns<1 ||$numcolumns>10){
	notice('Los tamaños elegidos no son valores permitidos',$url1);
}
else{
	
	switch ($whatstudents) {
		case 'active' :
			$userids = blended_get_course_students_ids ( $course, null, true );
			break;
		case 'all' :
			$userids = blended_get_course_students_ids ( $course, null, false );
			break;
		case 'list' :
			$userListcode = required_param_array ( 'users', PARAM_RAW );
			
			// compose a page with this users
			$total = $numcolumns * $numrows;
			$num = count ( $userListcode );
			$numpages = ceil ( $num / $total );
			
			$repeat = ($numpages * $total) / $num;
			$userids = array ();
			foreach ( $userListcode as $uid ) {
				for($i = 0; $i < $repeat; $i ++) {
					$userids [] = $uid;
					if (count ( $userids ) == $total)
						break;
				}
			}
			break;
		default: error ( "Invalid user selection." );
	
	}
	pdfLabelsPage( $userids, $blended, $numrows, $numcolumns, $margins, $identifyLabel );
}
function pdfLabelsPage($userids, $blended, $numrows, $numcolumns, $margins, $identifyLabels) {
		global $CFG, $DB;
		
		// create new PDF document
		$pdf = new TCPDF ( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
		$pdf->SetPrintHeader ( false );
		$pdf->SetPrintFooter ( false );
		// set document information
		$pdf->SetCreator ( PDF_CREATOR );
		$pdf->SetAuthor ( 'Blended Module' );
		$pdf->SetTitle ( 'Blended for Moodle page of labels.' );
		$pdf->SetSubject ( 'Labels for indentifying students.' );
		$pdf->SetKeywords ( 'TCPDF, PDF, blended, moodle, EDUVaLab' );
		$pdf->setPageOrientation ( 'PORTRAIT', false, $margins ['bottom'] );
		
		// set margins
		$pdf->SetMargins ( $margins ['left'], $margins ['top'], $margins ['right'] );
		$pdf->SetHeaderMargin ( 0 ); // PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin ( 0 ); // PDF_MARGIN_FOOTER);
		$margins = $pdf->getMargins ();
		// set auto page breaks
		$pdf->SetAutoPageBreak ( FALSE, $margins ['bottom'] );
		
		// set font
		$pdf->SetFontSize ( 8 );
		// add a page
		$pdf->AddPage ();
		
		$style = array (
				'position' => 'S',
				'border' => false,
				'padding' => 1,
				'fgcolor' => array (
						0,
						0,
						0 
				),
				'bgcolor' => false, // array(255,255,255),
				'text' => true,
				'font' => 'courier',
				'fontsize' => 8,
				'stretchtext' => 4 
		);
		
		$columnsWidth = ($pdf->getPageWidth () - $margins ['left'] - $margins ['right']) / $numcolumns;
		$rowsHeight = ($pdf->getPageHeight () - $margins ['bottom'] - $margins ['top'] - $margins ['footer'] - $margins ['header']) / $numrows;
		//if ($blended->idmethod != 0 && $identifyLabels != 'fullname')
			//$identifyLabels = 'code'; // show the bar-encoded code
		
		$r = 0;
		$c = 0;
		foreach ( $userids as $uid ) {
			
			if (! isset ( $user ) || $user->id != $uid) {
				$user = $DB->get_record ( 'user', array (
						'id' => $uid 
				) );
				
				if (! isset ( $user->idnumber )) {
					
					$url = "labels.php?a=$blended->id";
					notice ( get_string ( "cantprintlabel", "blended" ), $url );
				}
			}
			
			if ($r == $numrows) {
				$r = 0;
				$pdf->AddPage ();
			}
			
			$x = $margins ['left'] + $c * $columnsWidth;
			$y = $r * $rowsHeight + $margins ['top'] + $margins ['header'];
			$pdf->SetXY ( $x, $y );
			
			blended_print_student_label ( $pdf, $blended, $blended->codebartype, $style, $identifyLabels, $user, $columnsWidth, $rowsHeight );
			
			$c ++;
			if ($c == $numcolumns) {
				$c = 0;
				$r ++;
			}
		}
		// Close and output PDF document
		$pdf->Output ( 'labels.pdf', 'I' );
	}
	






?>