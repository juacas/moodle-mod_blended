<?php
require_once("$CFG->libdir/formslib.php");
 
class configureScanningProcessForm extends moodleform {
 
    function definition() {
        global $CFG;
 
        $mform =& $this->_form; // Don't forget the underscore! 
 
                
        $mform->addElement('choosecoursefile', 'scanJobFile', get_string('location'), null, array('maxlength' => 255, 'size' => 48));
   //   $mform->setHelpButton('scanJobFile', array('scanJobFile',get_string('SCANJOB', 'blended'),'blended'));
        $mform->addHelpButton('scanJobFile', 'SCANJOB', 'blended');        

        
       // $mform->setDefault('scanJobFile', $CFG->resource_defaulturl);  TODO check if a the default url is needed

        
        $referencegrprules = array();
        $referencegrprules['value'][] = array(get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $referencegrprules['value'][] = array(null, 'required', null, 'client');
        $mform->addGroupRule('scanJobFile', $referencegrprules);
        $mform->addRule('scanJobFile', null, 'required', null, 'client');
    //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('reset'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
        
        
        
    }                           // Close the function
   
}                               // Close the class
