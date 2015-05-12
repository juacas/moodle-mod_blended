<?php
//require_once("../tcpdf/tcpdf.php");
//require_once("../omrlib.php");
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Blended Module');
$pdf->SetTitle('TCPDF Example 027');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->setPageOrientation('PORTRAIT',false,$margins['bottom']);

$margins=$pdf->getMargins();//JPC: temporaly gets default margins. Later margins comes with the request

//set margins
$pdf->SetMargins($margins['left'],$margins['top'], $margins['right']);
$pdf->SetHeaderMargin(0);//PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(0);//PDF_MARGIN_FOOTER);
$margins=$pdf->getMargins();
//set auto page breaks
$pdf->SetAutoPageBreak(FALSE, $margins['bottom']);

//set image scale factor
$pdf->setImageScale(8);


// set font
$pdf->SetFont('courier', '', 6);



$style = array(
    'position' => 'S',
    'border' => false,
    'padding' => 1,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'courier',
    'fontsize' => 8,
    'stretchtext' => 4
);




$question=new stdClass();
$question->id="Q1";
$question->textHtml='prueba de <b>html para formateo</b> de texto.<p><img src="../pix/500237488_3a39afcc9f_o.jpg" height="100" alt="Derivada parcial de Pikachu respecto de u"></p>
<p>No, no me he vuelto loco. Veamos la demostración:</p>
<p align="center"><img src="../pix/500282733_0ce33da0a6_o.jpg" alt="Demostración"></p>
<p>Bueno, igual los exámenes de mis alumnos y la falta de tiempo sí me están volviendo loco. Cuando la cosa se tranquilice volveré como en los mejores tiempos.</p>';
$question->options=array();
$question->options[]="<p>texto opcion uno</p>";
$question->options[]="texto opcion dos con breve texto";
$question->options[]='<p>texto opcion tres: con un poco de texto más largo.</p>';
$question->options[]="texto opcion cuatro Con un poco de texto";
$question->options[]="texto opcion cinco";

$question2=new stdClass();
$question2->id="Q2";
$question2->textHtml='Nunc hendrerit tortor eu metus. Vivamus in leo. Maecenas eu arcu. Etiam lobortis. Maecenas erat sapien, tincidunt quis, tincidunt non, commodo ac, eros. Nam arcu elit, blandit id, hendrerit quis, vehicula at, dolor. Mauris ornare neque eu justo. Vivamus commodo sagittis lacus. Sed odio sem, faucibus adipiscing, ultrices quis, rhoncus quis, ligula. Donec venenatis luctus ligula. Phasellus massa. Duis euismod nisi ac leo. Sed nunc nulla, viverra aliquam, scelerisque sed, viverra et, nisl. Sed adipiscing, ligula eget pellentesque posuere, dolor sem consectetur lacus, sit amet pharetra erat orci eu dolor. Sed a ante.';
$question2->options=array();
$question2->options[]="Nunc hendrerit tortor eu metus. ";
$question2->options[]='<p>texto opcion tres: con un poco de texto más largo.</p>';
$question2->options[]="Maecenas eu arcu";
$question2->options[]="Etiam lobortis.";

$question3=new stdClass();
$question3->id="Q3";
$question3->textHtml='Nunc hendrerit tortor eu metus. Vivamus in leo. Maecenas eu arcu. Etiam lobortis. Maecenas erat sapien, tincidunt quis, tincidunt non, commodo ac, eros. Nam arcu elit, blandit id, hendrerit quis, vehicula at, dolor. Mauris ornare neque eu justo. Vivamus commodo sagittis lacus. Sed odio sem, faucibus adipiscing, ultrices quis, rhoncus quis, ligula. Donec venenatis luctus ligula. Phasellus massa. Duis euismod nisi ac leo. Sed nunc nulla, viverra aliquam, scelerisque sed, viverra et, nisl. Sed adipiscing, ligula eget pellentesque posuere, dolor sem consectetur lacus, sit amet pharetra erat orci eu dolor. Sed a ante.';
$question3->options=array();
$question3->options[]="Nunc hendrerit tortor eu metus. ";
$question3->options[]='<p>texto opcion tres: con un poco de texto más largo.</p>';
$question3->options[]="Maecenas eu arcu";
$question3->options[]="Etiam lobortis.";


$markSize=5;
$numcols=2;
$quizname="Formulario de prueba";
$fullname="nombre persona";
$columnsWidth=($pdf->getPageWidth()- $margins['left']  - $margins['right'])/$numcols;
$headeroptions=new stdClass();
$headeroptions->rowHeight=6;
$headeroptions->logoWidth=30;
$headeroptions->codebarWidth=40;
$headeroptions->codebarType="QR2D";
$headeroptions->logo_url="../pix/UVa_logo.jpg";
$headeroptions->cellHtmlText= 'nombre:'.$quizname;//get_string('QuizName','blended').':<b>'.$quizname.'</b>';
$headeroptions->cellHtmlDate= '';
$headeroptions->cellHtmlUser= 'alumno:'.$fullname;//get_string('StudentName','blended').':'.$fullname;
$headeroptions->cellCourseName= 'curso'.$coursename;//get_string('CourseName','blended').':'.$coursename;
$headeroptions->marksize=5;
$headeroptions->marksName='EVAL';
$page_dims=array();

// add a page
$pdf->AddPage();
$dims=new stdClass();
$dims->coords=array();
$page_dims[]=$dims;

blended_print_page_header($pdf,$dims,"9901984610272","22222222201",$headeroptions);

$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->textHtml  ,$borderDescription, 0 ,0,false);

//$pdf->SetXY(10,10); // no importa el inicio en Y


draw_multichoice_question($pdf,$dims,$question,$columnsWidth,0,$markSize);
draw_multichoice_question($pdf,$dims,$question2,$columnsWidth,0,$markSize);
$pdf->SetY(
			max(
				$dims->coords[$question->id]->Y+$dims->coords[$question->id]->H,
				$dims->coords[$question2->id]->Y+$dims->coords[$question2->id]->H
				)
			);
//TODO:row
draw_multichoice_question($pdf,$dims,$question3,$columnsWidth,0,$markSize);


//Close and output PDF document

$pdf->setLastH(1); // affects the inter-line space
//illustrate_layout($pdf,$dims);

$text=generate_template($dims,$fieldname);

$pdf->writeHTMLCell('','','','',$text,1);

$pdf->Output('labels.pdf', 'I');




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
 * @return object with a coords and marks properties
 */


function draw_multichoice_question(TCPDF $pdf,&$dims,$question, $columnsWidth,$ln, $markSize=4, $fillMarks=false, $numcols=2,$border=true)
{
	
	$borderDescription=false;
	$borderOption=false;
	$Xorigin=$pdf->GetX();
	$maxY=0;
	//$lastH=$pdf->getLastH();
	$pdf->setLastH(1);
	
	//output question
	saveXY($dims,$pdf,$question->id);
	$pdf->writeHTMLCell($columnsWidth,1,'','',  $question->textHtml  ,$borderDescription, 0 ,0,false);
	saveWH($dims,$pdf,$question->id);
	$pdf->Ln();
	$pdf->SetX($Xorigin);
$name=$question->id;
	//oputput options
	for($i=0;$i<count($question->options);$i++)
	{

		$option=$question->options[$i];
		//circulo para marcar
		
		draw_mark($pdf,$markSize,$dims,$i,$name,$fillMarks);
		
	

		saveXY($dims,$pdf);
		$pdf->writeHTMLCell($columnsWidth/2-$markSize,0,'','',$option,$borderOption,0,false);
		saveWH($dims,$pdf);
		$coord=$dims->coords[(int)(count($dims->coords)-1)];
		$renderedY=$coord->Y+$coord->H;
		$maxY=max($maxY,$renderedY);

		if ($i%$numcols==1)
		{
			$pdf->SetY($maxY);
			$pdf->SetX($Xorigin);
		}
	}

	//register global dimensions
	$coord=new stdClass();
	$coord->X= $dims->coords[$question->id]->X;
	$coord->Y= $dims->coords[$question->id]->Y;
	$coord->W= $columnsWidth;
	$coord->H= $dims->coords[count($dims->coords)-1]->Y+$dims->coords[count($dims->coords)-1]->H-$coord->Y;
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
	/*
	 //circulo para marcar
	 saveXY(&$coords,$pdf);
	 draw_mark($pdf,$markSize);
	 saveWH(&$coords,$pdf);

	 saveXY(&$coords,$pdf);
	 $pdf->writeHTMLCell($columnsWidth/2-$markSize,'','','',$question->options[0],1,0,0,0,'L');
	 saveWH(&$coords,$pdf);

	 //circulo para marcar
	 saveXY(&$coords,$pdf);
	 draw_mark($pdf,$markSize);
	 saveWH(&$coords,$pdf);

	 saveXY(&$coords,$pdf);
	 $pdf->writeHTMLCell($columnsWidth/2-$markSize,'','','',$question->options[1],1,1,0,0,'L');
	 saveWH(&$coords,$pdf);

	 //circulo para marcar
	 saveXY(&$coords,$pdf);
	 draw_mark($pdf,$markSize);
	 saveWH(&$coords,$pdf);

	 saveXY(&$coords,$pdf);
	 $pdf->writeHTMLCell($columnsWidth/2-$markSize,'','','',$question->options[2],1,0,0,0,'L');
	 saveWH(&$coords,$pdf);

	 //circulo para marcar
	 saveXY(&$coords,$pdf);
	 draw_mark($pdf,$markSize);
	 saveWH(&$coords,$pdf);

	 saveXY(&$coords,$pdf);
	 $pdf->writeHTMLCell($columnsWidth/2-$markSize,'','','',$question->options[3],1,2,0,0,'L');
	 saveWH(&$coords,$pdf);
	 */
	//Close and output PDF document

}

?>
