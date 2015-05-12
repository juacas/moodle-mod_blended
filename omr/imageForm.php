<?php
require_once("$CFG->libdir/formslib.php");


class imageForm extends moodleform {
 
    function definition() {
      
    $mform =& $this->_form; // Don't forget the underscore! 
    //normally you use add_action_buttons instead of this code

   
    $buttonarray=array();
    $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('back'));
        //$buttonarray[] = &$mform->createElement('reset', 'resetbutton', get_string('reset'));
        //$buttonarray[] = &$mform->createElement('cancel');
    $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    $mform->closeHeaderBefore('buttonar');
        
        
        
    }                           // Close the function
   
}                               // Close the class

?>