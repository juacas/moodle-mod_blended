<?php
require_once("$CFG->libdir/formslib.php");
/**
 * 
 * Create a form with the following fields
 *  a = blended module id
 *  logourl = Url of the image to be used as logo in header
 *  columns = 1, 2 columns
 *  identifyLabel = kind of label identification: noidentify, readable, etc
 *  fontsize = size of the font in point: 6,8,11
 *  quizid = id of the quiz to print into the PDF
 *  quiznumber = number of instances to generate
 *  cron = boolean to pospone the generation of pdfs
 * @author juacas
 */
class PDFQuizzesForm extends moodleform {
	
 /**
  * (non-PHPdoc)
  * @see moodleform::definition()
  */
    function definition() {
        global $CFG;
//  $strpaperquiz           = get_string("paperquiz", "blended");     
//    $strassignmentpage      = get_string("assignmentpage", "blended");
//    $strpaperquizdescr	    = get_string("paperquizdescr","blended");
//    $strpaperquizformat	    = get_string("paperquizformat","blended");
//	$strselectquiz	    	= get_string("selectquiz","blended");
 //   $strnumquiz	    		= get_string("numquiz","blended");
//    $strlater	    		= get_string("later","blended");
//    $strlabelformat			= get_string("labelformat","blended");
//    $stridentify			= get_string("identify","blended");
//    $strlabelformat			= get_string("labelformat","blended");
//    $stridentify	    	= get_string("identify","blended");
//	$strnotidentify	    	= get_string("notidentify","blended");
//	$strreadable	    	= get_string("readable","blended");
//	$strtable				= get_string("table","blended");
        $mform =& $this->_form; // Don't forget the underscore! 
 
  $mform->addElement('hidden', 'a', $this->_customdata['id']);
      // FIELDSET paperquiz format    
  $mform->addElement('header', 'format_section', get_string('paperquizformat', 'blended'));
        
        /**
         * Choose logo
         */
        $mform->addElement('choosecoursefile', 'logourl', get_string('logofile','blended'), null, array('maxlength' => 255, 'size' => 48));
        $mform->setHelpButton('logourl', array('SCANJOB',get_string('SCANJOB', 'blended'),'blended'));    
        $mform->setDefault('logourl', DEFAULT_LOGO);
        $referencegrprules = array();
        $referencegrprules['value'][] = array(get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $referencegrprules['value'][] = array(null, 'required', null, 'client');
        $mform->addGroupRule('logourl', $referencegrprules);
        $mform->addRule('logourl', null, 'required', null, 'client');
/*
 * Labels format
 */
$radioarray=array();
$radioarray[] = &MoodleQuickForm::createElement('radio', 'identifyLabel', '', get_string("notidentify","blended"), 'none');
$radioarray[] = &MoodleQuickForm::createElement('radio', 'identifyLabel', '', get_string("readable","blended"), 'readable');
$mform->setDefault('identifyLabel','readable');
$mform->addGroup($radioarray, 'identifyLabel', get_string("identify","blended"), array(' '), false);
/*
 * Number of columns
 */
$radioarray=array();
$radioarray[] = &MoodleQuickForm::createElement('radio', 'columns', '', get_string("onecolumn","blended"), '1');
$radioarray[] = &MoodleQuickForm::createElement('radio', 'columns', '', get_string("twocolumns","blended"), '2');
$mform->setDefault('columns','2');
$mform->addGroup($radioarray, 'columns', get_string("numcolumns","blended"), array(' '), false);

/**
 * Font size
 */        
$sizes=array(6=>get_string('smallfont','blended'),
			8=>get_string('midsizefont','blended'),
			11=>get_string('largefont','blended'));
$mform->addElement('select', 'fontsize', get_string('fontsize', 'blended'), $sizes);
$mform->setDefault('fontsize', 8);

$mform->closeHeaderBefore('format_section');        
           // FIELDSET generation Job    
$mform->addElement('header', 'job_section', get_string('paperquiz', 'blended'));

// list of quizzes
        $quizzes= $this->_customdata['quizzes'];
       
        $element=$mform->addElement('select', 'quizid', get_string('selectquiz', 'blended'));
        //$element->addOption('select one',null,array('disabled'=>'disabled','selected'=>'selected'));
        foreach ($quizzes as $quizkey=>$quiztitle)
        {
        	 $element->addOption($quiztitle,$quizkey);
        }
        $mform->addRule('quizid',get_string('err_required'),'required',null,'client');
        
 // number of instances
        $mform->addElement('text','quiznumber',get_string('numquiz','blended'),array('size'=>3,'value'=>1));
// pospone to cron
        $element=$mform->addElement('selectyesno', 'cron', get_string("later","blended"));
        $element->setValue(true);
  $mform->closeHeaderBefore('job_section');      
    //normally you use add_action_buttons instead of this code
/*        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('reset'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
  */
$this->add_action_buttons('',get_string('generatepaperquiz','blended'));      
        
        
    }                           // Close the function
   
}                               // Close the class
