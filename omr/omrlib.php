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

 * @author Pablo Galan Sabugo, David Fernández, Natalia Haro, Juan Pablo de Castro and other contributors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package blended
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/

//require_once("tcpdf/tcpdf.php");
require_once($CFG->dirroot . '/lib/tcpdf/tcpdf.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');
require_once($CFG->dirroot . '/config.php');
//require_once(dirname(__FILE__)."/evaluationlib.php");
//require_once(dirname(__FILE__)."recognitionprocess.php");

require_once(dirname(__FILE__)."/PDFError.php");

$letters=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
$scansfoldername = "Scans";
$MARKSCOLOR=array(0);
$MARKLETTERCOLOR=array(80);// if grayscale {G}, if color {R,G,B}

/**
 * Constants for tagging the status of the queued jobs
 * @var unknown_type
 */
define('JOB_STATE_BUSY','busy');
define('JOB_STATE_WAITING','waiting');
define('JOB_STATE_FINISHED','finished');
define('JOB_STATE_IN_ERROR','error');

define('IMAGE_STATUS_PENDING','pending');
define('IMAGE_STATUS_PASSED','finished');

define('DEFAULT_LOGO',$CFG->wwwroot.'/mod/blended/pix/UVa_logo.jpg');
/**
 *  Path for the fieldsets of a scanjob
 * @param unknown_type $scan object with:
 *  $scan->course identification of the owner course
 */
function blended_getOMRFieldsetDir($scan)
{
global $CFG;
//	$pos = strripos($rutaorigen,"/");
//	return substr($rutaorigen,0,$pos);
$path= "$CFG->dataroot/$scan->course/moddata/blended";
return $path;
}
/**
 * calculate the path for the results of the omr process
 * $rutascandir."/".$scansfoldername.'-'.$scan->id;
 * 
 * @param array $scan register with scan data
 */
function blended_getOMRTargetPath($scan)
{
	global $scansfoldername;
	$rutaorigen=blended_getOMRSourcePath($scan);
	$pos = strripos($rutaorigen,"/");
	$rutascandir= substr($rutaorigen,0,$pos);
	return $rutascandir."/".$scansfoldername.'-'.$scan->id;
}
function blended_getOMRSourcePath($scan)
{
	global $CFG;
	$rutaorigen = "$CFG->dataroot/$scan->course/$scan->scan_name";  
	return $rutaorigen;  
}
/**
 * 
 * Path to the log.txt for a scanjob
 * @param unknown_type $scan
 */
function blended_getOMRInputLogFilePath($scan)
{
	return blended_getOMRTargetPath($scan)."/log.txt";
}
/**
 *
 * @param $blended_quiz
 * @param $job array with timestamp, userid
 * TODO allow more flexible configuration of folders
 * @return $pdfileobject with paths for the process
 */
function blended_prepare_and_get_paths($job, $blended_quiz)
{
	global $scansfoldername;
	$timestamp=$job->timestamp;
	$formattedtimestamp=date("Y-m-d-H-i-s",$timestamp);
	$pdfFile->wdir ="moddata/blended";
	$jobDescription = "$blended_quiz->name-".$formattedtimestamp."-$job->userid";
	$courseid=$blended_quiz->course;
	$descriptiondir="meta";
	
	
	
	

	if (! $coursebasedir = make_upload_directory("$courseid"))
	{
		error("The site administrator needs to fix the file permissions. (Course files folder.)");
	}

	if (! $blendeddir= make_upload_directory("$courseid/$pdfFile->wdir"))
	{
		error("Requested directory does not exist.(moddata/blended folder).", "$CFG->wwwroot/files/index.php?id=$id");
	}

	$jobFolder=clean_filename($jobDescription); //job's part of the directory (relative)
	
	if (! $descriptionsdir= make_upload_directory("$courseid/$pdfFile->wdir/$jobFolder"))
	{
		error("Requested directory does not exist.($jobFolder)", "$CFG->wwwroot/files/index.php?id=$id");
	}

	if (! $resultados= make_upload_directory("$courseid/$pdfFile->wdir/$jobFolder/$scansfoldername"))
	{
		error("Requested directory does not exist.", "$CFG->wwwroot/files/index.php?id=$id");
	}

//$pdfFile->wdir ="moddata/blended";
	$pdfFile->Name=clean_filename("$blended_quiz->name-".$formattedtimestamp."-$job->userid.pdf");
	$pdfFile->inCourseFolderRelativePath="$pdfFile->wdir/$jobFolder";
	$pdfFile->RelativePath="$courseid/$pdfFile->inCourseFolderRelativePath/$pdfFile->Name";
	$pdfFile->Path="$coursebasedir/$pdfFile->inCourseFolderRelativePath/$pdfFile->Name";
	$pdfFile->BasePath="$blendeddir/$jobFolder";
	
	$scan = new stdClass();
	$scan->course=$courseid;
	$pdfFile->FieldsetsPath= blended_getOMRFieldsetDir($scan);


	//$pdfFile->RelativePath="$courseid/$pdfFile->wdir/$pdfFile->Name";
	//$pdfFile->Path="$blendeddir/$pdfFile->Name";
	//$pdfFile->PathOMR="$blendeddir";

return $pdfFile;
}

/**
 * Generate the PDF from a job
 * if no job->id is passed then groups all attempts with the same:
 * userid, quiz and timestamp with status=JOB_STATUS_WAITING in a multipage PDF
 * returns the number of attempts rendered and the pdf path produced.
 * @param unknown_type $job
 */
function generateJobs($job)
{
	global $CFG;

	try{
		
	/**
	 * Avoid empty quizzes
	 */
	$quiz = get_record("quiz", "id", $job->quiz);
	if (!($quiz->questions))
		{
		throw new PDFError("The quiz is empty!!",PDFError::QUIZ_IS_EMPTY);
		}
				
	if (isset($job->id)) // a specific job is requested
	{
		$query = "id = '$job->id' and quiz = '$job->quiz' and status = '".JOB_STATE_WAITING."'";
	}
	else // Try to guess the job using hints such as timestamp quiz and user TODO remove this situation.
	{
		$query="timestamp = '$job->timestamp' and quiz = '$job->quiz' and userid='$job->userid' and status = '".JOB_STATE_WAITING."'";
	}
	print("Getting pdf jobs with query: $query\n");
	if(!$jobs = get_records_select('blended_jobs',$query))
	{
		echo("<p>There are no jobs: job's timestamp: $job->timestamp  job's quiz: $job->quiz</p>\n");
		return array(0,null);
	}
	
	// Mark all jobs as BUSY
	if (isset($job->id))
		set_field('blended_jobs','status',JOB_STATE_BUSY,'id',$job->id,'quiz',$job->quiz);
		else
		set_field('blended_jobs','status',JOB_STATE_BUSY,'timestamp',$job->timestamp,'quiz',$job->quiz,'userid',$job->userid);
	
	$numberpaperquiz=count($jobs);
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	$pdfFile = blended_prepare_and_get_paths ($job,$quiz);

	debugging("<p>  Processing job from user $job->userid. Quiz with id=$job->quiz</p>\n");
	echo("<p>Generating $numberpaperquiz quizes out of <a href=\"$CFG->wwwroot/mod/quiz/view.php?q=$quiz->id\">\"$quiz->name\"</a></p>\n");
	debugging("<p>PDFfile: $pdfFile->Path</p>");
	foreach ($jobs as $quiz_job)
	{
		// parse formatting options into an array
		$options = array_explode('=', ',', $quiz_job->options);

		$blended = get_records('blended', 'id',$quiz_job->blended);
		
		if(!$attemptdata = get_records('blended_attempts', 'id',$quiz_job->attempt_id))
		{
			echo"Error ";
		}
		$a=0;
		foreach ($attemptdata as $attempt)
		{
			echo("<p>Generating quiz number ".($a+1)."...\n");
			try
			{
				if (!($attempt->layout))
				{
					throw new PDFError("El cuestionario no contiene ninguna pregunta",PDFError::QUIZ_IS_EMPTY);
				}
				$start=microtime(true);
		/**
		 * Actually generate the PDF document
		 */
				$pdf = blended_generate_quiz ($attempt, $quiz, $pdf, $blended["$quiz_job->blended"], $options, $pdfFile);
				$printtime=(microtime(true)-$start)*1000;
				mtrace(" done in $printtime (ms)</p>");
				$attempt->status = JOB_STATE_FINISHED;
				$quiz_job->status = JOB_STATE_FINISHED;
			}
			catch(PDFError $e)
			{
				echo("Error creating a page for quiz ".$quiz_job->attempt_id." Continuing\n");
				echo("<BR>".$e->getMessage()."");
				$attempt->status = JOB_STATE_IN_ERROR;
				$quiz_job->status = JOB_STATE_IN_ERROR;
			}
			update_record('blended_attempts', $attempt);
			update_record('blended_jobs', $quiz_job);
			$a=$a+1;

		}


	}


	$pdf->Output($pdfFile->Path, 'F');
	}catch(PDFError $e)
	{
		debugging("Fatal PDF error: ".$e->getMessage());
		
		foreach ($jobs as $quiz_job)
		{
		$quiz_job->status = JOB_STATE_IN_ERROR;
		update_record('blended_jobs', $quiz_job);
		delete_records('blended_attempts','status',JOB_STATE_IN_ERROR,'id',$quiz_job->attempt_id);
		}
		return array(0, null);
	}
	return array($a, $pdfFile);
}
/**
 * Draw a circle to put the answer.
 *
 * @param TCPDF &$pdf
 * @param $marksize
 * @param &$dims
 * @param $label='0'
 * @param $name
 * @param $fill=false
 *
 * @return $object with a coords and marks properties
 */
function blended_draw_mark(TCPDF &$pdf, $marksize,&$dims,$label='0', $name, $text, $fill=false,$type='CIRCLE')
{
global $MARKSCOLOR,$MARKLETTERCOLOR;

	$fontsize=$pdf->getFontSizePt();
	
	
	$pdf->SetFontSize(6);
	if (!isset($text))
		$text = $label;
		
	$pdf->SetTextColorArray($MARKLETTERCOLOR);
	blended_saveXY($dims,$pdf);
	$render=$fill?'F':'D';
	$pdf->Cell($marksize,$marksize,$text,0,0,'C',0,'','');
	blended_saveWH($dims,$pdf);
	//store mark coords
	blended_saveMark($dims,$name, $label,$type);
	$pdf->SetFillColor(0,0,0);
	$pdf->SetTextColorArray($MARKSCOLOR);
	$pdf->SetDrawColorArray($MARKSCOLOR);
	if($type == 'CIRCLE')
	{
		$pdf->Circle($pdf->getX()-$marksize/2,$pdf->getY()+$marksize/2,($marksize)/2,0,360,$render);
	}
	else
	{
		$pdf->Rect($pdf->getX()-$marksize,$pdf->getY(),$marksize,$marksize,$render);
	}

	//restore font size
	$pdf->SetFontSize($fontsize);
}
/**
 * Draw a box to put the barcodes.
 *
 * @param TCPDF &$pdf
 * @param $marksize
 * @param &$dims
 * @param $label='0'
 * @param $name
 * @param $fill=false
 *
 * @return $object with a coords and marks properties
 */
function blended_draw_barcode_placeholder(TCPDF &$pdf, $marksizew,$marksizeh,&$dims,$label='0', $name, $fill=false)
{
	$fontsize=$pdf->getFontSizePt();
	$pdf->SetFontSize(4);
	blended_saveXY($dims,$pdf);
	$render=$fill?'F':'D';
	$pdf->Cell($marksizew,$marksizeh,$label,1,0,'C',$fill,'','');
	//	$pdf->SetFillColor(0,0,0);
	//$pdf->Rect($pdf->getX(),$pdf->getY(),$marksizew,$marksizeh,$render);
	//label mark inside
	$pdf->SetFontSize($fontsize);
	blended_saveWH($dims,$pdf);
	//store mark coords
	blended_saveBarCode($dims,$name);
}

/**
 * Generate template file with the coords.
 *
 * @param TCPDF &$pdf
 * @param $dims
 * @param $page
 * @param $uniqueid
 * @param $pdfFile
 *
 */
function blended_generate_omrfile (TCPDF &$pdf,$dims, $page, $uniqueid, $pdfFile)
{
	$texto = blended_generate_template($dims);
	$ruta = "$pdfFile->FieldsetsPath/fieldset_OMR[$uniqueid].fields";
	if (file_exists($ruta) && $page==1)
	{
		throw new OMRError("File definition $ruta already exists.", OMRError::FIELDS_FILE_ALREADY_EXISTS);
	}
	
debugging("Fileset written to $ruta");

	$DescriptorFichero = fopen("$ruta","a");
	if ($page==1)
	{
		fputs($DescriptorFichero,"TemplateId=$uniqueid\n");
	}
	fputs($DescriptorFichero,"[Page$page]\n");
	fputs($DescriptorFichero,$texto);
}


/*
 function illustrate_layout(TCPDF &$pdf,$dims, $page, $uniqueid)
 {
 //posible descriptor de marcas
 //$pdf->writeHTMLCell('','','','','<pre>'.generate_template($dims).'</pre>',0,0,0,0,'L');

 $texto = generate_template($dims);
 $DescriptorFichero = fopen("fichero_prueba[$uniqueid][$page].txt","w");
 fputs($DescriptorFichero,$texto);
 //close($DescriptorFichero);


 //$coords=$dims->coords;
 //$pdf->SetLineStyle(array("dash"=>"2,1","width"=>"0.8"));

 //Prueba de medida de coordenadas.
 foreach($coords as $coord)
 {
 $x=number_format($coord->X,1);
 $y=number_format($coord->Y,1);
 $pdf->Text($coord->X,$coord->Y,"($x,$y)",'','');
 $pdf->SetDrawColor(255,0,0);
 $dx=rand(1,5);
 $pdf->Line($coord->X+$dx,$coord->Y,$coord->X+$dx,$coord->Y+$coord->H);
 $pdf->Circle($coord->X+$dx,$coord->Y,  1);
 $pdf->Circle($coord->X+$dx,$coord->Y+$coord->H,1);
 }

 //prueba de deteccion de marcas
 foreach($dims->marks as $mark)
 {
 $pdf->SetDrawColor(0,200,200);
 $pdf->SetTextColor(0,200,200);
 //$pdf->Rect($mark->coord->X,$mark->coord->Y,$mark->coord->W,$mark->coord->H);
 //$pdf->Text($mark->coord->X,$mark->coord->Y,$mark->name.'-'.$mark->label);
 }
 //marco externo elementos
 foreach($dims->coords as $key=>$coord)
 {
 if(is_string($key))//element with id
 {
 $pdf->SetDrawColor(0,200,0);
 $pdf->Rect($coord->X,$coord->Y,$coord->W,$coord->H);
 $pdf->SetTextColor(0,200,0);
 $pdf->Text($coord->X,$coord->Y,$key);
 }
 }

 }*/



function  blended_generate_template($dims)
{
	// export marks coords
	//$test="\n[MarksDesc]\n";
	$text="";

	foreach($dims->marks as $markgroup)
	{

		foreach($markgroup as $mark)
		{
			
			$ma=$mark->coord;
				
			if (!isset($mark->label) || $mark->label=="")
			{	$label="";
				if ($mark->name == 'EVAL')
				$label = '-0';
			}
			else
			$label='-'.$mark->label;
			

			$text=$text.$mark->name.$label.'['.$mark->type.']'."=$ma->X,$ma->Y,$ma->W,$ma->H\n";
				
		}
	}

	return $text;
}



/**
 * Save actual X and Y coords.
 *
 * @param &$dims
 * @param &$pdf
 * @param $id='0'
 *
 * @return $object with a coords and marks properties
 */
function blended_saveXY(&$dims,&$pdf,$id='0')
{

	$coord=new stdClass();
	$coord->X=$pdf->GetX();
	$coord->Y=$pdf->GetY();
	if (!empty($id))
		$dims->coords[$id]=$coord;
	else
	{
		$len=count($dims->coords);
		$dims->coords[$len]=$coord;
	}
}


/**
 * Save actual X and Y coords.
 *
 * @param &$dims
 * @param &$pdf
 * @param $id='0'
 *
 */
function blended_saveWH(&$dims,&$pdf,$id='0')
{
	if (!empty($id))
	{
		$coord=$dims->coords[$id];
	}
	else
	{
		$len=count($dims->coords)-1;
		$coord=$dims->coords[$len];
	}

	$coord->H= $pdf->GetLastH();
	$coord->W= $pdf->GetX()-$coord->X;
}


/**
 * Save Mark coords and dims.
 *
 * @param &$dims
 * @param $name
 * @param $label
 *
 *
 * @return $object with a coords and marks properties
 */
function blended_saveMark(&$dims,$name,$label,$type='CIRCLE')
{
	$mark=new stdClass();
	$mark->coord=$dims->coords[count($dims->coords)-1];
	$mark->label = $label;
	$mark->name=$name;
	$mark->type=$type;
	$dims->marks[$name][]=$mark;
}


/**
 * Saver barcode coords and dims..
 *
 * @param &$dims
 * @param $name
 * @param $label
 *
 * @return $object with a coords and marks properties
 */
function blended_saveBarCode(&$dims,$name)
{
	$mark=new stdClass();
	$mark->coord=$dims->coords[count($dims->coords)-1];
	$mark->name=$name;
	$mark->type='CODEBAR';
	$dims->marks[$name][]=$mark;
}



/**
 * Draws a header.
 *
 *	$headeroptions=new stdClass();
	$headeroptions->rowHeight=6;
	$headeroptions->logoWidth=30;
	$headeroptions->codebarWidth=40;
	$headeroptions->textStyle=$style;
	
	$headeroptions->logo_url=$CFG->dirroot . '/mod/blended/pix/UVa_logo.jpg';
	$headeroptions->cellHtmlText= get_string('modulename','quiz').':'.$quizname;//Nombre:
	$headeroptions->cellHtmlDate= '';
	$headeroptions->cellHtmlUser= get_string('student','moodle').':';// Alumno:
	$headeroptions->cellCourseName= $COURSE->fullname;
	$headeroptions->evaluationmarksize=3; // if null evaluation marks are not included in header
	$headeroptions->marksName='EVAL';
	$headeroptions->codebarType = $blended->codebartype;
	$headeroptions->instructions // text HTML about how to use the form
 * @param TCPDF $pdf
 * @param &$dims holder for the metrics of the header and sensible areas such as codebars and marks
 * @param $usercode
 * @param $activity_code
 * @param $headeroptions
 * @param $page
 *
 *
 * @return object with a coords and marks properties
 */
function blended_print_page_header(TCPDF $pdf,$dims,$usercode,$activity_code,$headeroptions, $page)//, $identifyLabel, $idText)
{

	global $CFG;
	// TODO control this by means of a module's param
	blended_print_align_marks($pdf,$dims);
	if (isset($headeroptions->identifyLabel))
	{
	$identifyLabel=$headeroptions->identifyLabel;
	}
	else
	{
	$identifyLabel='none';
	}
	
	if($identifyLabel!='none')
		$identifyHeight=3;
	else
		$identifyHeight=0;

	$style = array(
    'position' => 'S',
    'border' => true,
    'padding' => 1,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'courier',
    'fontsize' => 5,
    'stretchtext' => 4
	);

	$pdf->SetHeaderMargin(5);


	$logoHeight=3*$headeroptions->rowHeight-1;
	$logoHtml="<img width='20mm' src='$headeroptions->logo_url'>";
	$margins=$pdf->getMargins();

	$pdf->SetFillColor(255, 255, 255);
	$fontsize=$pdf->getFontSizePt();

	/**
	 * Set font style for course title.
	 */
//JAV.  Se modifica esta linea para evitar un warning	
// 	$pdf->SetFont($headeroptions->textStyle['font'],'B',$headeroptions->textStyle['fontsize']+3);
	
	
	if (isset($headeroptions->textStyle['font']))
	{
		$pdf->SetFont($headeroptions->textStyle['font'],'B',$headeroptions->textStyle['fontsize']+3);
	}
	else
	{
		$pdf->SetFont('courier','B',10);
	}
	
	
	$pdf->Cell($pdf->getPageWidth()-$margins['right']-11*$headeroptions->marksize,
			'',$headeroptions->cellCourseName,0,0,'C',true,'',0);
	/**
	 * Row of 10 marks for evaluation
	 */
	if ($headeroptions->marksize)
	{
		$pdf->SetX($pdf->getPageWidth()-$margins['right']-11*($headeroptions->marksize));
		for($i=0;$i<11;$i++)
		{
			blended_draw_mark($pdf,$headeroptions->marksize,$dims,$label=$i,$name=$headeroptions->marksName,$text=$i,false);
		}
	}
/**
 * Logo
 */
	
	try{
	$pdf->Image($headeroptions->logo_url, $margins['left'],
			$margins['top']+$margins['header'],
			$headeroptions->logoWidth,
			$logoHeight,
			'',
			'',
			'',
			true,
			150);
	}catch( Exception $exception)
	{
		$pdf->Image(DEFAULT_LOGO, $margins['left'],
			$margins['top']+$margins['header'],
			$headeroptions->logoWidth,
			$logoHeight,
			'',
			'',
			'',
			true,
			150);
	}
	
	
/**
 * User barcode Placeholder
 */
	$codebarW=min($headeroptions->codebarWidth/2,$headeroptions->rowHeight*3);
	$pdf->setXY($pdf->getPageWidth()-$margins['right']-$headeroptions->codebarWidth*2,
	$margins['top']+$margins['header']);
	// reserve this area for the sticker
	$pdf->SetFillColor(230);
	//blended_draw_barcode_placeholder($pdf,
		//$headeroptions->codebarWidth,
		//$headeroptions->rowHeight*3,
		//$dims,"Place UserID Label here.",'USERID',true);

/**
 * Activity CodeBar
 */
	
	$pdf->setXY($pdf->getPageWidth()-$margins['right']-$headeroptions->codebarWidth,
			$margins['top']+$margins['header']);

	switch($headeroptions->codebarType)
	{
		case 'QR2D':
			blended_print_2Dbarcode ($dims, $pdf, $headeroptions, $usercode, $page, $margins, $style, $identifyLabel);
			break;
		default:

			blended_print_1Dbarcode ($dims, $pdf, $headeroptions, $usercode, $page, $margins, $style);

	}
/**
 * Header fields
 */
	//$pdf->SetFontSize(6);
	
	//JAV.  Se modifica esta linea para evitar un warning
	// 	$pdf->SetFont($headeroptions->textStyle['font'],'',$headeroptions->textStyle['fontsize']);
	
	
	if (isset($headeroptions->textStyle['font']))
	{
		$pdf->SetFont($headeroptions->textStyle['font'],'',$headeroptions->textStyle['fontsize']);
	}
	else
	{
		$pdf->SetFont('courier','',6);
	}
		
	$pdf->writeHTMLCell($pdf->getPageWidth()-$headeroptions->logoWidth-4-$margins['left']-$margins['right']-$headeroptions->codebarWidth,
						$headeroptions->rowHeight,
						$headeroptions->logoWidth+4+$margins['left'],
						$margins['top']+$margins['header'],
						$headeroptions->cellHtmlText,
						true,
						0,
						0,
						true);


	$pdf->writeHTMLCell($pdf->getPageWidth()-$headeroptions->logoWidth-4-$margins['left']-$margins['right']-$headeroptions->codebarWidth,
	$headeroptions->rowHeight,
	$headeroptions->logoWidth+4+$margins['left'],
	$margins['top']+$margins['header']+$headeroptions->rowHeight,
	$headeroptions->cellHtmlDate,
	true,
	0,
	0,
	true);

	$pdf->writeHTMLCell($pdf->getPageWidth()-$headeroptions->logoWidth-4-$margins['left']-$margins['right']-$headeroptions->codebarWidth,
	$headeroptions->rowHeight,
	$headeroptions->logoWidth+4+$margins['left'],
	$margins['top']+$margins['header']+2*$headeroptions->rowHeight,
	$headeroptions->cellHtmlUser,
	true,
	0,
	0,
	true);

/**
 * Instructions about how to use the form
 */
	if (isset($headeroptions->instructions) && $page==1)
		{
		/*$pdf->writeHTMLCell($pdf->getPageWidth()-$headeroptions->logoWidth-4-$margins['left']-$margins['right'],
						$headeroptions->rowHeight,
						$headeroptions->logoWidth+4+$margins['left'],
						$margins['top']+$margins['header']+3*$headeroptions->rowHeight,
						$headeroptions->instructions,
						true,
						0,
						0,
						true);*/
		
		$pdf->writeHTMLCell($pdf->getPageWidth()-$margins['left']-$margins['right'],
						$headeroptions->rowHeight,
						$margins['left'],
						$margins['top']+$margins['header']+3*$headeroptions->rowHeight,
						$headeroptions->instructions,
						true,
						0,
						0,
						true);
		};
	$pdf->SetFontSize($fontsize);
	$pdf->Ln();
	$pdf->SetHeaderMargin(3*$headeroptions->rowHeight+5);//PDF_MARGIN_HEADER);
	
}
/**
 * Does not update XY state
 * @param TCPDF $pdf
 * @param unknown_type $dims
 */
function blended_print_align_marks(TCPDF $pdf,&$dims)
{
	$name="Align";
	$type="FRAME";
	$label='';
	$oldX=$pdf->GetX();
	$oldY=$pdf->GetY();

	$margins=$pdf->getMargins();

	$minX=$margins['left']-1;
	$minY=$margins['top']-1;
	$maxX=$pdf->getPageWidth()-$margins['right']+1;
	$maxY=$pdf->getPageHeight()-$margins['bottom']+1;

	$pdf->setXY($margins['left'],$margins['top']);
	blended_saveXY($dims,$pdf);


	$pdf->SetFillColor(0,0,0);

	$pdf->Line($minX,$minY,$minX+10,$minY);
	$pdf->Line($minX,$minY,$minX,$minY+10);

	$pdf->Line($maxX,$minY,$maxX-10,$minY);
	$pdf->Line($maxX,$minY,$maxX,$minY+10);


	$pdf->Line($minX,$maxY,$minX+10,$maxY);
	$pdf->Line($minX,$maxY,$minX,$maxY-10);

	$pdf->Line($maxX,$maxY,$maxX-10,$maxY);
	$pdf->Line($maxX,$maxY,$maxX,$maxY-10);


	$pdf->setXY($pdf->getPageWidth()-$margins['right'],$pdf->getPageHeight()-$margins['bottom']);
	$pdf->setLastH($pdf->getPageHeight()-$margins['bottom']-$margins['top']);

	blended_saveWH($dims,$pdf);
	//store mark coords
	blended_saveMark($dims,$name, $label,$type);
	$pdf->setXY($oldX,$oldY);
}


/**
 *
 *
 *
 *
 * @param $pdf
 * @param &$dims
 * @param $question
 * @param $columnsWidth
 * @param $ln  Indicates where the current position should go after the call. Possible values are:
 *				0: to the right
 *				1: to the beginning of the next line
 *				2: below
 * @param $markSize
 * @param $fillMarks
 * @param $numcols
 * @param $border draws a border
 * @return object with a coords and marks properties
 */
function blended_draw_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=4, $fillMarks=false, $numcols=2,$border=true)
{
	switch ($question->qtype)
	{
		case "truefalse":
			blended_draw_truefalse_question ($pdf,$dims,$question, $columnsWidth,$ln, $markSize, $fillMarks=false, $numcols=2, $border=true);
			break;
		case "description":
			blended_draw_description_question ($pdf,$dims,$question, $columnsWidth,$ln, $markSize, $fillMarks=false, $numcols=2,$border=true);
			break;
		case "multichoice":
			blended_draw_multichoice_question ($pdf,$dims,$question, $columnsWidth,$ln, $markSize, $fillMarks=false, $numcols=2,$border=true);
			break;
		case "essay":
			blended_draw_essay_question ($pdf,$dims,$question, $columnsWidth,$ln, $markSize, $fillMarks=false, $numcols=2,$border=true);
			break;
			/*case "random":
			 $wrappedQuestion=$state->options->question;
			 if ($wrappedQuestion->qtype!="multichoice" &&
			 $wrappedQuestion->qtype!="truefalse")
			 error("Unsuported Question Type.");
			 	
			 $quest->answers=$wrappedQuestion->options->answers;//aqui esta el estado real de la question
			 $question->questiontext=$wrappedQuestion->questiontext;
			 $question->questiontextformat=$wrappedQuestion->questiontextformat;
			 $question->options=$wrappedQuestion->options;

			 break;
			 */
		default:
			error("Unsuported Question Type. 1");
	}
}


function blended_draw_essay_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=4, $fillMarks=false, $numcols=2,$border=true)
{

	$borderDescription= false;
	$borderOption= false;
	$Xorigin=$pdf->GetX();

	$Yorigin= $pdf->GetY();

	$maxY=0;
	//$lastH=$pdf->getLastH();
	$setlasH = $pdf->setLastH(1);


	//output question
	blended_saveXY($dims,$pdf,$question->id);
	$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->questiontext  ,$borderDescription, 0 ,0,false);
	blended_saveWH($dims,$pdf,$question->id);

	$pdf->Ln();
	$pdf->SetX($Xorigin);
	
	blended_saveXY($dims,$pdf);
	$pdf->Cell( $columnsWidth, 40,'', $borderOption, 0, '');
	blended_saveWH($dims,$pdf);
	
//register global dimensions
	$coord=new stdClass();
	$coord->X= $dims->coords[$question->id]->X;
	$coord->Y= $dims->coords[$question->id]->Y;
	$coord->W= $columnsWidth;

	$a = $dims->coords[count($dims->coords)-1]->H;
	$b = $dims->coords[count($dims->coords)-3]->H;
	$maxH = max($a,$b);
	$coord->H= $dims->coords[count($dims->coords)-1]->Y+$maxH-$coord->Y;
	$dims->coords[$question->id]=$coord;
	switch($ln)
	{
		case 0: $pdf->SetXY($coord->X+$coord->W,$coord->Y);
		break;
		case 1: $margins=$pdf->getMargins();
		$pdf->SetXY($margins['left'],$coord->Y+$coord->H);
		break;
		case 2: $pdf->SetXY($coord->X+$coord->W,$coord->Y,$coord->Y+$coord->H);
		break;
	}
	if ($border) $pdf->Rect($coord->X,$coord->Y,$coord->W,$coord->H);
	$pdf->setLastH($coord->H);
	
	return;
}

/**
 * Draws a multichoice question with many options on the TCPDF object.
 * It relies on the current X,Y pointer of the TCPDF context.
 *
 * Generates some metrics about the layout.
 * retuns an object with the following structure:
 * object->coords[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *        ->marks[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *
 * @param $pdf
 * @param $question
 * @param $columnsWidth
 * @param $ln  Indicates where the current position should go after the call. Possible values are:
 *				0: to the right
 *				1: to the beginning of the next line
 *				2: below
 * @param $markSize
 * @param $fillMarks
 * @param $numcols
 * @param $border draws a border
 * @return object with a coords and marks properties and TCPDF object.
 */
function blended_draw_multichoice_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=3, $fillMarks=false, $numcols=2,$border=true)
{

	$borderDescription= false;
	$borderOption= false;
	$Xorigin=$pdf->GetX();

	$Yorigin= $pdf->GetY();

	$maxY=0;
	//$lastH=$pdf->getLastH();
	$setlasH = $pdf->setLastH(1);
	print("Question: $question->questiontext</p>");
	//output question
	blended_saveXY($dims,$pdf,$question->id);
	$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->questiontext  ,$borderDescription, 0 ,0,false);
	blended_saveWH($dims,$pdf,$question->id);

	$pdf->Ln();
	$pdf->SetX($Xorigin);
	$name=$question->id;
	//output options
	$formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $formatoptions->para = false;
	$i=0;
	//Las marcas se ordenan segun

	if ($question->options->single==1)		// averiguar si es respuesta unica o multiple
	{
		$type='CIRCLE';
	}
	else
	{
		$type='SQUARE';
	}
	foreach($question->anss as $id=>$anss) //¿que es anss ? cambia el nombre por algo inteligible
	{
	// format_text($answer->answer, FORMAT_MOODLE, $formatoptions, $cmoptions->course);
		//$option = $anss->answer;
		$option = format_text($anss->answer,FORMAT_MOODLE,$formatoptions);
		//circulo para marcar con un número correlativo a cada respuesta.
		// la marca $i+1 corresponde con la respuesta $id (ver $state)
		
// little margin
	$pdf->Cell(1,$markSize,'',0,0,'C',0,'','');
	global $letters;
		blended_draw_mark($pdf,$markSize,$dims,$label=$anss->id,$name,$text=$letters[$i],$fillMarks,$type);

		blended_saveXY($dims,$pdf);

		$pdf->writeHTMLCell($columnsWidth/2-$markSize-1,0,'','',$option,$borderOption,0,false);

		blended_saveWH($dims,$pdf);
		$coord=$dims->coords[(int)(count($dims->coords)-1)];
		$renderedY=$coord->Y+$coord->H;
		$maxY=max($maxY,$renderedY);
		if ($i%$numcols==1)
		{
			$pdf->SetY($maxY);
			$pdf->SetX($Xorigin);
		}
		$i++;
	}

	//register global dimensions
	$coord=new stdClass();
	$coord->X= $dims->coords[$question->id]->X;
	$coord->Y= $dims->coords[$question->id]->Y;
	$coord->W= $columnsWidth;




	$a = $dims->coords[count($dims->coords)-1]->H;
	$b = $dims->coords[count($dims->coords)-3]->H;
	$maxH = max($a,$b);

	$Yfinal = $pdf->GetY();

	$coord->H2 = $Yfinal - $Yorigin;

	$coord->H= $dims->coords[count($dims->coords)-1]->Y+$maxH-$coord->Y;
	$dims->coords[$question->id]=$coord;



	switch($ln)
	{
		case 0: $pdf->SetXY($coord->X+$coord->W,$coord->Y);
		break;
		case 1: $margins=$pdf->getMargins();
		$pdf->SetXY($margins['left'],$coord->Y+$coord->H);
		break;
		case 2: $pdf->SetXY($coord->X+$coord->W,$coord->Y,$coord->Y+$coord->H);
		break;
	}

	if ($border) $pdf->Rect($coord->X,$coord->Y,$coord->W,$coord->H);
	$pdf->setLastH($coord->H);

}

/**
 * Draws a truefalse question with many options on the TCPDF object.
 * It relies on the current X,Y pointer of the TCPDF context.
 *
 * Generates some metrics about the layout.
 * retuns an object with the following structure:
 * object->coords[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *        ->marks[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *
 * @param $pdf
 * @param $question
 * @param $columnsWidth
 * @param $ln  Indicates where the current position should go after the call. Possible values are:
 *				0: to the right
 *				1: to the beginning of the next line
 *				2: below
 * @param $markSize
 * @param $fillMarks
 * @param $numcols
 * @param $border draws a border
 * @return object with a coords and marks properties and TCPDF object.
 */
function blended_draw_truefalse_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=4, $fillMarks=false, $numcols=2,$border=true)
{

	$borderDescription= false;
	$borderOption= false;
	$Xorigin=$pdf->GetX();
	$maxY=0;
	//$lastH=$pdf->getLastH();
	$pdf->setLastH(1);

	//output question
	blended_saveXY($dims,$pdf,$question->id);
	$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->questiontext  ,$borderDescription, 0 ,0,false);
	blended_saveWH($dims,$pdf,$question->id);
	$pdf->Ln();
	$pdf->SetX($Xorigin);
	$name=$question->id;
	//output options
	$i=0;
	//Las marcas se ordenan segun

	foreach($question->options->answers as $id=>$anss) //¿que es anss ? cambia el nombre por algo inteligible
	{
		$option = $anss->answer;
		//circulo para marcar con un número correlativo a cada respuesta.
		// la marca $i+1 corresponde con la respuesta $id (ver $state)
		global $letters;
		blended_draw_mark($pdf,$markSize,$dims,$label=$i+1,$name,$text=$letters[$i],$fillMarks);



		blended_saveXY($dims,$pdf);
		$pdf->writeHTMLCell($columnsWidth/2-$markSize,0,'','',$option,$borderOption,0,false);
		blended_saveWH($dims,$pdf);
		$coord=$dims->coords[(int)(count($dims->coords)-1)];
		$renderedY=$coord->Y+$coord->H;
		$maxY=max($maxY,$renderedY);
		if ($i%$numcols==1)
		{
			$pdf->SetY($maxY);
			$pdf->SetX($Xorigin);
		}
		$i++;
	}

	//register global dimensions
	$coord=new stdClass();
	$coord->X= $dims->coords[$question->id]->X;
	$coord->Y= $dims->coords[$question->id]->Y;
	$coord->W= $columnsWidth;

	$a = $dims->coords[count($dims->coords)-1]->H;
	$b = $dims->coords[count($dims->coords)-3]->H;
	$maxH = max($a,$b);
	$coord->H= $dims->coords[count($dims->coords)-1]->Y+$maxH-$coord->Y;
	$dims->coords[$question->id]=$coord;
	switch($ln)
	{
		case 0: $pdf->SetXY($coord->X+$coord->W,$coord->Y);
		break;
		case 1: $margins=$pdf->getMargins();
		$pdf->SetXY($margins['left'],$coord->Y+$coord->H);
		break;
		case 2: $pdf->SetXY($coord->X+$coord->W,$coord->Y,$coord->Y+$coord->H);
		break;
	}
	if ($border) $pdf->Rect($coord->X,$coord->Y,$coord->W,$coord->H);
	$pdf->setLastH($coord->H);

}

/**
 * Draws a simple question on the TCPDF object.
 * It relies on the current X,Y pointer of the TCPDF context.
 *
 * Generates some metrics about the layout.
 * retuns an object with the following structure:
 * object->coords[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *        ->marks[]->X
 *                 ->Y
 *                 ->H
 *                 ->W
 *
 * @param $pdf
 * @param $question
 * @param $columnsWidth
 * @param $ln  Indicates where the current position should go after the call. Possible values are:
 *				0: to the right
 *				1: to the beginning of the next line
 *				2: below
 * @param $markSize
 * @param $fillMarks
 * @param $numcols
 * @param $border draws a border
 * @return object with a coords and marks properties and TCPDF object.
 */
function blended_draw_description_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=4, $fillMarks=false, $numcols=2,$border=true)
{

	$borderDescription= true;
	$borderOption= false;
	$Xorigin=$pdf->GetX();
	$maxY=0;
	//$lastH=$pdf->getLastH();
	$pdf->setLastH(1);

	//output question
	blended_saveXY($dims,$pdf,$question->id);
	$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->questiontext  ,$borderDescription, 0 ,0,false);
	blended_saveWH($dims,$pdf,$question->id);
	$pdf->Ln();
	$pdf->SetX($Xorigin);
	$name=$question->id;


	blended_saveXY($dims,$pdf);
	$pdf->Cell( $columnsWidth, 40,'', $borderOption, 0, '');
	blended_saveWH($dims,$pdf);
	$coord=$dims->coords[(int)(count($dims->coords)-1)];
	$renderedY=$coord->Y+$coord->H;

	//register global dimensions
	$coord=new stdClass();
	$coord->X= $dims->coords[$question->id]->X;
	$coord->Y= $dims->coords[$question->id]->Y;
	$coord->W= $columnsWidth;
	$desc = $coord + 40;
	$coord->H= $desc;

	$dims->coords[$question->id]=$coord;
	switch($ln)
	{
		case 0: $pdf->SetXY($coord->X+$coord->W,$coord->Y);
		break;
		case 1: $margins=$pdf->getMargins();
		$pdf->SetXY($margins['left'],$coord->Y+$coord->H);
		break;
		case 2: $pdf->SetXY($coord->X+$coord->W,$coord->Y,$coord->Y+$coord->H);
		break;
	}
	if ($border) $pdf->Rect($coord->X,$coord->Y,$coord->W,$coord->H);
	$pdf->setLastH($coord->H);

}


function blended_get_question_formulation_and_controls($question, $state, $cmoptions, $options) {
	global $CFG;
	global $QTYPES;
	$quest= new StdClass;
	$quest->id=$question->id;
	switch ($question->qtype)
	{
		case "truefalse":
			$quest->answers=$question->options->answers;
			break;
		case "description":
			$quest->questiontext = $question->questiontext;
			break;
		case "multichoice":
			$quest->answers=$question->options->answers;
			break;
		case "essay":
			$quest->questiontext=$question->questiontext;
			break;
		case "random":
			$wrappedQuestion=$state->options->question;
			if ($wrappedQuestion->qtype!="multichoice" && $wrappedQuestion->qtype!="essay" &&
				$wrappedQuestion->qtype!="truefalse" && $wrappedQuestion->qtype!="description")
				{
					error("Unsuported Question Type.");
				}

			$quest->answers=$wrappedQuestion->options->answers;//aqui esta el estado real de la question
			$question->questiontext=$wrappedQuestion->questiontext;
			$question->questiontextformat=$wrappedQuestion->questiontextformat;
			$question->options=$wrappedQuestion->options;
			$question->qtype = $wrappedQuestion->qtype;
			break;
		default:
			error("Unsuported Question Type.");
	}


	$formatoptions = new stdClass;
	$formatoptions->noclean = true;
	$formatoptions->para = false;

	// Print formulation
	$quest->questiontext = format_text($question->questiontext,	$question->questiontextformat,	$formatoptions, $cmoptions->course);
	if ($question->qtype == "multichoice")
	{
		$quest->order=$state->options->order;
		$quest->correctanswers = $QTYPES[$question->qtype]->get_correct_responses($question, $state);
			
		$single=$question->options->single;
		$quest->answerprompt = ($single==true) ? get_string('singleanswer', 'quiz') :
		get_string('multipleanswers', 'quiz');

		// Print each answer in a separate row
		foreach ($state->options->order as $key => $aid)
		{
			$answer = $quest->answers[$aid];

			$a = new stdClass;
			$a->id   = $question->name_prefix . $aid;

			$a->text = format_text($answer->answer, FORMAT_MOODLE, $formatoptions, $cmoptions->course);

		}
		$quest->anss[] = clone($a);

	}

	return $quest;
}

function blended_new_page (&$pdf1)
{
	$margins=$pdf1->getMargins();//JPC: temporaly gets default margins. Later margins comes with the request

	//set margins
	$pdf1->SetMargins($margins['left'],$margins['top'], $margins['right']);
	$pdf1->SetHeaderMargin(0);//PDF_MARGIN_HEADER);
	$pdf1->SetFooterMargin(0);//PDF_MARGIN_FOOTER);
	$margins=$pdf1->getMargins();
	//set auto page breaks
	$pdf1->SetAutoPageBreak(FALSE, $margins['bottom']);

	//set image scale factor
	$pdf1->setImageScale(3.5);


	// set font
	$pdf1->SetFont('courier', '', 6);

	// add a page
	$pdf1->AddPage();
}

/**
 * 
 * Creates a new PDF with the questions of a quiz's attempt.
 * 
 * @param unknown_type $attempt record with the attempt info
 * @param unknown_type $quiz
 * @param unknown_type $pdf1
 * @param unknown_type $blended
 * @param unknown_type $options array with formatting options
 * @param unknown_type $pdfFile
 * @throws PDFError
 */
function blended_generate_quiz ($attempt, $quiz, $pdf1, $blended, $options, $pdfFile)
{
	global $QTYPES;
	global $CFG;
	global $COURSE;
	$uniqueid = $attempt->id;
	$activity_code = $uniqueid;
	
	$identifyLabel=$options['identifyLabel'];	
/*
 	switch($identifyLabel)
	{
		case 'id':
			$idText= $activity_code;
			break;
		case 'none': $idText='';
		break;
	}
*/
	$markSize=3;
	$quizname= $quiz->name;
	
	$images->ok_marks='<img src="'.$CFG->wwwroot.'/mod/blended/images/ok_marks.png" height="10" />';
	$images->ko_marks='<img src="'.$CFG->wwwroot.'/mod/blended/images/ko_marks.png" height="10" />';
//	$images->ok_marks='<img src="mod/blended/images/ok_marks.png" height="10" />';
//	$images->ko_marks='<img src="mod/blended/images/ko_marks.png"  height="10"/>';
	$howToMark=get_string('howToMarkInstructions','blended',$images);
	$instructions = $quiz->intro.' '.$howToMark;
	
	$fullname="nombre persona";
	
	$style = array(
    'position' => 'S',
    'border' => false,
    'padding' => 1,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'courier',
    'fontsize' => $options['fontsize'],
    'stretchtext' => 4
	);
	$headeroptions=new stdClass();
	$headeroptions->rowHeight=6;
	$headeroptions->logoWidth=30;
	$headeroptions->codebarWidth=40;
	$headeroptions->textStyle=$style;
	
if (isset($options['logourl']))
{
	$headeroptions->logo_url=$options['logourl'];
}
else
{
$headeroptions->logo_url=$CFG->dirroot . '/mod/blended/pix/UVa_logo.jpg';
}
	$headeroptions->cellHtmlText= get_string('modulename','quiz').':'.$quizname;//Nombre:
	$headeroptions->cellHtmlDate= '';
	$headeroptions->cellHtmlUser= get_string('Student','blended').':';// Alumno:
	$headeroptions->cellCourseName= $COURSE->fullname;
	$headeroptions->evaluationmarksize=3; // if null evaluation marks are not included in header
	$headeroptions->marksName='EVAL';
	$headeroptions->codebarType = $blended->codebartype;
	$headeroptions->identifyLabel=$identifyLabel; // show readable text for codebars 'none' if not to be shown
	$headeroptions->instructions=$instructions;

	
/**
 * Give precedence to the selected number of columns in the $options
 */
if (isset($options['columns']))
{
	$numcols=$options['columns'];
}
else
	if (!isset($blended->numcols) || $blended->numcols == 0)
	{
		$numcols = 2;
	}
	else
	{
		$numcols=$blended->numcols;
	}
	
	unset ($quiz->questionsinuse);
	unset($QTYPES["random"]->catrandoms);

	$pagelist = quiz_questions_on_page($attempt->layout, 0);
	$pagequestions = explode(',', $pagelist);
	$questionlist = quiz_questions_in_quiz($attempt->layout);
	if (!$questionlist)
	{
		throw new PDFError("Quiz layout is empty",PDFError::QUIZ_IS_EMPTY);
	}
	$sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}question q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";
	if (!$questions = get_records_sql($sql))
	{
		throw new PDFError("Questions not found. ", PDFError::QUESTIONS_NOT_FOUND);
	}
	//Carga las preguntas con sus opciones
	if (!get_question_options($questions))
	{
		throw new PDFError("Could not load question options", PDFError::COULD_NOT_LOAD_QUESTION_OPTIONS);
	}
	$quiz_userid = 4;
	$acode = $attempt->id;

	if(!$attemptnumber = (int)get_field_sql('SELECT MAX(attempt)+1 FROM ' .
            "{$CFG->prefix}quiz_attempts WHERE quiz = '{$quiz->id}' AND " .
            "userid = '{$quiz_userid}' AND timefinish > 0 AND preview != 1")) 
		{
		$attemptnumber = 1;
        }
             
            $quiz_uniqueid=$attempt->attempt;
            $timenow = time();
            $quiz_attempt = create_new_attempt($quiz, $attemptnumber,$quiz_userid,$acode,$quiz_uniqueid,$timenow);
            // Save the attempt
            // if (!insert_record('quiz_attempts', $quiz_attempt)) {
            //     error('Could not create new attempt');
            //}
            if (!$states = get_question_states($questions, $quiz, $quiz_attempt, false))
            {
            	throw new PDFError("Could not restore question sessions",PDFError::COULD_NOT_RESTORE_QUESTION_SESSIONS);
            }

          
            // TODO save question states someway in question_states

            foreach ($questions as $i => $question) {
            	save_question_session($questions[$i], $states[$i]);
            }

            //	global $QTYPES;
          
            //$question = reset($questions);
          
            //print("state answer question $question->id: ".$QTYPES[$question->qtype]->get_question_options($question)); 

            $quests = new stdClass;
            $quests = array();


            foreach ($pagequestions as $i)
            {
            	$options = quiz_get_renderoptions($quiz->review, $states[$i]);
            	$quest=blended_get_question_formulation_and_controls($questions[$i], $states[$i] , $quiz, $options);
            	
            	if (isset ($quest->answers))
            	{
            		foreach($quest->answers as $c=>$v)
            		{
            			$text = $v->answer;
            			$quest->answers[$c]->answer = blended_image_path ($text);
            		}
            	}
            	$quests[] = $quest;
            }

           
            $idText = '';


            $original = new stdClass;
            $original->question = array();
            $question=$quests;

            $num = 0;
            $a = 0;

            //foreach ($questions as $m)//esto es redundante: crea un array igual
            foreach ($pagequestions as $i)
            {
            	$questions[$i]->questiontext = $quests[$a]->questiontext;
            	if( isset ($questions[$i]->questiontext))
            	{
            		$questions[$i]->questiontext = blended_image_path ($questions[$i]->questiontext);

            	}

            	$question[$a] = $questions[$i];
            	if ($question[$a]->qtype == "multichoice") //TODO test with truefalse
            	{
            		$question[$a]->anss= array();
            		foreach ($states[$questions[$i]->id]->options->order as $j=>$aid)
            		{
            			$question[$a]->anss[$j] = $questions[$i]->options->answers[$aid];
            			$num++;
            		}
            	}
            	$question[$a]->id = "q".$question[$a]->id;
            	$a++;
            }
            foreach ($question as $quest)
            {
            	$original->question[] = $quest;
            }
/**
 * Start PDF printing
 */            
            
// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	blended_new_page ($pdf);

	$margins=$pdf->getMargins();
	$columnsWidth=($pdf->getPageWidth()- $margins['left']  - $margins['right'])/$numcols;
/**
 * Print all questions with TPDF to calculate exact dimensions of each block
 */
 $pdf->SetFont($headeroptions->textStyle['font'],'',$headeroptions->textStyle['fontsize']);
            
            for ($key=0; $key < count($original->question); $key++)
            {
				$dims_borrador=new stdClass();
				$dims_borrador->coords=array();
            	$original->question[$key]->height = blended_print_draft ($pdf, $key, $original, $dims_borrador, $columnsWidth, $markSize,$headeroptions);
            }
/**
 * Print final PDF
 */
// Array to hold sizes of elements			
	$dimsn = array();
	
// Print content starting at abscisa $y
$pdf1 = blended_print_quiz($pdf1,$numcols,$columnsWidth, $margins, $original,$dimsn, $markSize, $headeroptions, $uniqueid, $pdfFile);
unset($pdf);
return $pdf1;
}
/**
 * Create a new Attempt in a quiz.
 * 
 * @param unknown_type $quiz record identifying the quiz in which the attempt will be created.
 * @param unknown_type $attemptnumber
 * TODO check uniqueid usage
 * TODO refactor this to a middle-layer library.
 */
function blended_create_attempt($quiz, $attemptnumber)
{

	global $USER, $CFG;

	// we are not building on last attempt so create a new attempt
	$attempt->quiz = $quiz->id;
	//$attempt->userid = $USER->id;
	$attempt->preview = 0;
	if ($quiz->shufflequestions)
	{
		$attempt->layout = quiz_repaginate($quiz->questions, $quiz->questionsperpage, true);
	}
	else
	{
		$attempt->layout = $quiz->questions;
	}
	$timenow = time();
	$attempt->attempt = $attemptnumber;
	$attempt->userid = $USER->id;
	$attempt->sumgrades = 0.0;
	$attempt->timestart = $timenow;
	$attempt->timefinish = 0;
	$attempt->timemodified = $timenow;
	//$attempt->uniqueid = question_new_attempt_uniqueid('blended');
	//$attempt->id = $attempt->uniqueid;
	return $attempt;
}
/**
 * Start a new attempt and initialize the question sessions
 * @param unknown_type $attemptnumber
 * @param unknown_type $quiz
 * @param unknown_type $USER
 * @param unknown_type $timestamp
 */
function blended_generate_attempt ($attemptnumber, $quiz, $USER, $timestamp)
{
	
	//set_field('blended_attempts', 'quiz', $quiz->id, 'userid', $USER->id);
	$attempt = blended_create_attempt($quiz, $attemptnumber);
	$attempt->status = JOB_STATE_WAITING ;
	$attempt->preview = 1;
	$attempt->quiz = $quiz->id;
	$attempt->timestamp = $timestamp;

	if (!$attempt->id = insert_record('blended_attempts', $attempt))
	{
		error('Could not create new attempt');
	}
	return $attempt;
}

function blended_print_2Dbarcode ($dims, $pdf, $headeroptions, $activity_code, $page, $margins, $style, $identifyLabel)
{
	$html='';
	$codebarW=min($headeroptions->codebarWidth/2,$headeroptions->rowHeight*3);
	$activity_codepage = $activity_code.$page;
// QR code
	$pdf->SetFontSize(6);
	
	$codebarX=$pdf->getPageWidth()-$margins['right']-$codebarW;
	$codebarY=$margins['top']+$margins['header']-1;
	$pdf->setXY($codebarX,$codebarY);
	blended_saveXY($dims,$pdf);
	$pdf->write2DBarcode($activity_codepage,'DATAMATRIX',$codebarX,$codebarY,
											$codebarW,$headeroptions->rowHeight*3);		
	$pdf->setXY($codebarX+$codebarW,$pdf->GetY());
	blended_saveWH($dims,$pdf);
	blended_saveBarCode($dims,"TEMPLATEFIELD");
	
	// Task id barcode
	// Readable Textual info
	$pdf->SetXY($pdf->getPageWidth()-$margins['right']-$codebarW*2,
				$margins['top']+$margins['header']+$headeroptions->rowHeight*1.5);
	blended_saveXY($dims,$pdf);

	$pdf->Rect($pdf->getPageWidth()-$margins['right']-$codebarW*2,
				$margins['top']+$margins['header'], $codebarW, $headeroptions->rowHeight*3);

	blended_saveWH($dims,$pdf);
	if($identifyLabel!='none')
	{
		$pdf->writeHTMLCell($codebarW*2,$codebarW,$pdf->getPageWidth()-$margins['right']-$codebarW*2,
		$margins['top']+$margins['header'],"ID:".$activity_codepage,0,0);
	}
	/*
	$pdf->Rect($pdf->getPageWidth()-$margins['right']-$codebarW*2,
	$margins['top']+$margins['header'],
	$codebarW*2,$codebarW);
	*/
	
}

function blended_print_1Dbarcode ($dims, $pdf, $headeroptions, $activity_code, $page, $margins, $style)
{
	blended_saveXY($dims,$pdf);
	//TODO: ¿es necesario a�adir checksum al EAN13?
	// $usercode=blended_gen_ean($usercode);

	$activity_codepage = $activity_code.$page;
	$pdf->write1DBarcode($activity_codepage, $headeroptions->codebarType,
	$pdf->getPageWidth()-$margins['right']-$headeroptions->codebarWidth,
	$margins['top']+$margins['header'],
	$headeroptions->codebarWidth,
	$headeroptions->rowHeight*3,
	0.5,
	$style,
							"T");

	blended_saveWH($dims,$pdf);
	blended_saveBarCode($dims,"USER");

	// Task id barcode
	$pdf->SetXY($pdf->getPageWidth()-$margins['right']-$headeroptions->codebarWidth,
	$margins['top']+$margins['header']+$headeroptions->rowHeight*1.5);
	blended_saveXY($dims,$pdf);

	$pdf->Rect(	$pdf->getPageWidth()-$margins['right']-$headeroptions->codebarWidth,
	$margins['top']+$margins['header']+$headeroptions->rowHeight*3,
	$headeroptions->codebarWidth,
	$headeroptions->rowHeight*0);
	blended_saveWH($dims,$pdf);
}
/**
 * Filter text Trying to make a simpler HTML and to change local HTTP url by local file paths.
 * @param $text
 */
function blended_image_path ($text)
{
	global $CFG;

	$text = strip_tags($text , '<marker/><a><b><blockquote><dd><del><div><dl><dt><em><font><h1><h2><h3><h4><h5><h6><hr><i><img><li><ol><p><pre><small><span><strong><sub><sup><table><td><th><tr><tt><u><ul>');

	$cadena = $text;

	$pattern= '{(<img[^>]*src=\")('.preg_quote($CFG->wwwroot).'/file.php[\/?])([^\s\"]+)(\"[^>]*>)}';

	$subst= "$1".$CFG->dataroot."/$3$4";

	$resultado = preg_replace($pattern, $subst, $cadena);

	return $resultado;
}

function blended_print_draft ($pdf, $key, $original, $dims_borrador, $columnsWidth, $markSize,$headeroptions)
{
	$pdf->SetFont($headeroptions->textStyle['font'],'',$headeroptions->textStyle['fontsize']);
	blended_draw_question($pdf,$dims_borrador,$original->question[$key],$columnsWidth,0,$markSize);
	$height = $dims_borrador->coords[$original->question[$key]->id]->H;
	return $height;
}


/**
* Prints the quiz in the PDF object and writes the template
 * 
 * @param unknown_type $pdf1
 * @param unknown_type $numcols
 * @param unknown_type $columnsWidth
 * @param unknown_type $margins
 * @param unknown_type $original
 * @param unknown_type $dimsn
 * @param unknown_type $markSize
 * @param unknown_type $headeroptions
 * @param unknown_type $uniqueid
 * @param unknown_type $pdfFile
 */
function blended_print_quiz ( $pdf1,$numcols,$columnsWidth, $margins, $original,$dimsn, $markSize, $headeroptions, $uniqueid, $pdfFile)
{	
	$ordinal=1;
	$i=0;
	
	$item = new stdClass;
	
	$Height=($pdf1->getPageHeight() - $margins['bottom']);
	$page= 1;

while(count($original->question)>0)
{
	$dims=new stdClass();
    $dims->coords=array();
	$dimsn[$page]=$dims;
debugging("<p>Create page $page</p>");            
	blended_new_page ($pdf1);
	// Print page Header
	blended_print_page_header($pdf1,$dimsn[$page],null, $uniqueid, $headeroptions, $page);
	$topY=$pdf1->GetY();
	for($cols=0; $cols< $numcols; $cols++)
	{
debugging("<p>Filling up column $cols<p>");
		$i = 0;
		$pending = new stdClass;
		$pending->question = array();

		$x = $cols*$columnsWidth + $margins['left'];
		$pdf1->SetY($topY);
		foreach($original->question as $question_original)
		{	
			$question=clone $question_original;
			 	
			if ($pdf1->GetY() + $question->height < $Height)
			{ // There is room for this Question block
				$pdf1->SetX($x);
			//avoid a new line if there is a paragraph mark
				$question->questiontext=trim($question->questiontext);
				$paragraphmark='<p align="justify">'; // TODO filter this with regular expressions
			$par_pos=strpos($question->questiontext,$paragraphmark);
			if ($par_pos!==false && $par_pos==0)
					{
					$question->questiontext="$paragraphmark$ordinal)".substr($question->questiontext,strlen($paragraphmark));	
					}
				else
					{
			// add an ordinal to the question for easy identification on the page
					$question->questiontext="$ordinal)".$question->questiontext;
					}
					
				$ordinal++;		
			//Print a question block
				$pdf1->SetFont($headeroptions->textStyle['font'],'',$headeroptions->textStyle['fontsize']);
				blended_draw_question($pdf1,$dimsn[$page],$question,$columnsWidth,0,$markSize);
				$pdf1->ln();
			debugging("<p>Item $question->id printed</p>");
			}
			else// reserve this question block for next column (or next page)
			{
				$pending->question[]= $question;
				debugging("<p>Item $question->id discarted, passed to next column.</p>");
				unset ($question);
			}
		}// iteration over questions
		
	// Start a new column
		unset ($original);
		$original = $pending;
		//unset ($pending);
		debugging("<p>There are ".count($original->question)." questions to print.</p>");
	}// iterate over columns
	debugging("<p>Columns or questions exhausted. Ending page.</p>");
// save OMR information for this page
	blended_generate_omrfile($pdf1,$dimsn[$page], $page, $uniqueid, $pdfFile);
// Start a new page
	$page++;
	debugging("<p>There are ".count($original->question)." questions to print.</p>");
	
}//while(true) pages
	return $pdf1;
}
/**
 * Implode an array with the key and value pair giving
 * a glue, a separator between pairs and the array
 * to implode.
 * @param string $glue The glue between key and value
 * @param string $separator Separator between pairs
 * @param array $array The array to implode
 * @return string The imploded array
 */
function array_implode( $glue, $separator, $array ) {
    if ( ! is_array( $array ) ) return $array;
    $string = array();
    foreach ( $array as $key => $val ) {
        if ( is_array( $val ) )
            $val = implode( ',', $val );
        $string[] = "{$key}{$glue}{$val}";
       
    }
    return implode( $separator, $string );
   
}
function array_explode( $glue, $separator, $string )
{
$parts=explode($separator, $string);
$array=array();
foreach ($parts as $part)
{
	$keyvalue=explode($glue, $part);
	$array[$keyvalue[0]]=$keyvalue[1];
}	
return $array;
}
?>
