<?php

require_once("../../../config.php");
//require_once("../tcpdf/tcpdf.php");
require_once("../omrlib.php");
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

$q = optional_param("q",8,PARAM_INTEGER);

$usercode = $USER->id;



$quiz = get_record("quiz", "id", $q);
$attemptnumber = 2;
// Start a new attempt and initialize the question sessions
$attempt = quiz_create_attempt($quiz, $attemptnumber);

$uniqueid = $attempt->uniqueid;

$activity_code = "4";

$pdf1 = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->AddPage();

new_page ($pdf1);

$margins=$pdf1->getMargins();

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

   


$markSize=5;
$numcols=2;
$quizname="Formulario de prueba";
$fullname="nombre persona";
$columnsWidth=($pdf1->getPageWidth()- $margins['left']  - $margins['right'])/$numcols;
$headeroptions=new stdClass();
$headeroptions->rowHeight=6;
$headeroptions->logoWidth=30;
$headeroptions->codebarWidth=40;
$headeroptions->logo_url="../pix/UVa_logo.jpg";
$headeroptions->cellHtmlText= 'nombre:'.$quizname;//get_string('QuizName','blended').':<b>'.$quizname.'</b>';
$headeroptions->cellHtmlDate= '';
$headeroptions->cellHtmlUser= 'alumno:'.$fullname;//get_string('StudentName','blended').':'.$fullname;
//$headeroptions->cellCourseName= 'curso'.$coursename;//get_string('CourseName','blended').':'.$coursename;
$headeroptions->cellCourseName= "NOMBRECURSO";
$headeroptions->marksize=5;
$headeroptions->marksName='EVAL';

$headeroptions->codebarType = 'EAN13';

$dims=new stdClass();
$dims->coords=array();

$dims1=new stdClass();
$dims1->coords=array();

$dimsn = new stdClass();
$dimsn = array();
$dimsn[0]->coords=array();

$page = optional_param('page', 0, PARAM_INT);
$pagelist = quiz_questions_on_page($attempt->layout, $page);
$pagequestions = explode(',', $pagelist);
$questionids = optional_param('questionids', '');
$questionlist = quiz_questions_in_quiz($attempt->layout);
if ($questionids) {
        $questionlist .= ','.$questionids;
    }

    if (!$questionlist) {
        print_error('noquestionsfound', 'quiz', 'view.php?q='.$quiz->id);
    }

    //TODO sustituir esto por una llamada a una función prefereiblemente del modulo QUIZ
$sql = "SELECT q.*, i.grade AS maxgrade, i.id AS instance".
           "  FROM {$CFG->prefix}question q,".
           "       {$CFG->prefix}quiz_question_instances i".
           " WHERE i.quiz = '$quiz->id' AND q.id = i.question".
           "   AND q.id IN ($questionlist)";

$questions = get_records_sql($sql);
 

$lastattemptid = false;



$number = quiz_first_questionnumber($attempt->layout, $pagelist);

$quests = new stdClass;
$quests = array();
/// Print all the questions


// LA PAGINACION YA NO LA HACE MOODLE SINO BLENDED ASI QUE EL ARRAY PAGEQUESTIONS NO TIENE SENTIDO
//    foreach ($pagequestions as $i) {
//        $options = quiz_get_renderoptions($quiz->review, $states[$i]);
//        $questions[$i]
//        $quests[$i] = get_question_formulation_and_controls($questions[$i], $states[$i], $quiz, $options);
//        $number += $questions[$i]->length;
//    }

//Carga las preguntas con sus opciones
 if (!get_question_options(&$questions)) 
	 	{
	        error('Could not load question options');
	    }
$states = get_question_states(&$questions, $quiz, $attempt, $lastattemptid);

   foreach ($questions as $id=>$question) 
   	{
   		
	$options = quiz_get_renderoptions($quiz->review, $states[$id]);
    $quest=get_question_formulation_and_controls(&$question, &$states[$id] , $quiz, $options);
    //$question->anss=$quest->anss;
    $quests[] = $quest;
    $number += $question->length; // NO ENTIENDO QUE ACUMULA ESTO
    }



$item = new stdClass;

$descartados = new stdClass;
$descartados->question = array();

$borrado = new stdClass;
$borrado->question = array();

$original = new stdClass;
$original->question = array();

$question=$quests;
//$question = new stdClass;
//$question = array();

$num = 0;
$a = 0;
foreach ($questions as $m)//esto es redundante: crea un array igual
{
	$question[$a] = $m;
	if ($question[$a]->qtype == "multichoice")
    {
		$question[$a]->anss= array();
		foreach ($states[$m->id]->options->order as $i=>$aid)
		//foreach ($m->options->answers as $qi) //TODO: Deben copiarse en el orden
		{	
			$question[$a]->anss[$i] = $m->options->answers[$aid];
			$num++;
		}
    }
	$question[$a]->id = "q".$question[$a]->id;
	$a++;
}

$a = 0;

foreach ($question as $quest)
{
	
	$original->question[] = $quest;
	$a++;
}


$i=0;

/**
 * Simulate a PDF print to measure actual Height of every question block
 */
for ($key=0; $key < count($original->question); $key++)
{
	draw_question($pdf,$dims,$original->question[$key],$columnsWidth,0,$markSize);
	$original->question[$key]->height = $dims->coords[$original->question[$key]->id]->H;
}

/**
 * Start first page and measure what room is available for question blocks
 */
$page= 0;
blended_print_page_header($pdf1,$dimsn[$page],null,$activity_code,$headeroptions);
$y = $pdf1->GetY();
//$RoomHeight=($pdf1->getPageHeight() - $margins['bottom'] - $margins['top']- $headeroptions->rowHeight); //TODO check this
$RoomHeight=($pdf1->getPageHeight() - $margins['bottom']);
/**
 * Iterate over the columns printing the question's block if there are room for it.
 */
for($cols=0; $cols< $numcols; $cols++)
{
	$control = 0;
	$pdf1->SetY($y);
	$x = $cols*$columnsWidth + $margins['left'];
	$n_quest = count($original->question);
	for($key = 0; $key < $n_quest ; $key++)
	{
		$item = $original->question[$key];
// if there is room 
		if ($pdf1->GetY() + $original->question[$key]->height < $RoomHeight)
		{
			$pdf1->SetX($x);
			draw_question($pdf1,$dimsn[$page],$item,$columnsWidth,0,$markSize);
			$pdf1->ln();
		}
		else
// reserve this question block for next column (or next page)
		{
			$descartados->question[$i]= $original->question[$key];
			$i++;
			unset ($original->question[$key]);
			$control = 1;
		}
	}
	if ($control == 0)
	{
		illustrate_layout($pdf1,$dimsn[$page], $page, $uniqueid);
		break;
	}
	unset ($original);
	$original = $descartados;
	unset ($descartados);
	$i = 0;
	
	if(count($original)==0 && count($descartados)==0)
	{	
		illustrate_layout($pdf1,$dimsn[$page], $page, $uniqueid);
		break;
	}
	else
	{
		if ($cols == $numcols - 1)
		{
			illustrate_layout($pdf1,$dimsn[$page], $page, $uniqueid);
			$page++;
			$dimsn[$page]->coords=array();
			new_page ($pdf1);
			blended_print_page_header($pdf1,$dimsn[$page],null,$activity_code,$headeroptions);
			$cols = -1;
			$x = 0;
		}
	}
}

$pdf1->Output('paperquiz.pdf', 'I');

?>