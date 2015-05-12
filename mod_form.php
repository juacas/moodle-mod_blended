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
*********************************************************************************/

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot . '/lib/dml/moodle_database.php');
require_once($CFG->dirroot . '/mod/blended/lib.php');

class mod_blended_mod_form extends moodleform_mod {

    function definition() {
        global $CFG,$DB,$COURSE;
        $mform =& $this->_form;

        $key=mt_rand(0xFFF,0x7FFFFFFF); 
        $mform->addElement('hidden', 'randomkey', $key);
        $mform->setType('randomkey', PARAM_INT);
        $mform->setDefault('randomkey', $key); 

        $strrequired = get_string('required', 'blended');

        // Cabecera --------------------------------------------------------------
        $mform->addElement('header','general',get_string('general', 'form'));
        
        // Nombre ----------------------------------------------------------------
        $mform->addElement('text','name', get_string('name', 'blended'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', $strrequired, 'required', null, 'client');
        
        // Descripción -----------------------------------------------------------
       //$mform->addElement('editor', 'intro', get_string('description', 'blended'), 'wrap="virtual" rows="15" cols="75"');
        //$mform->setType('intro', PARAM_RAW); 
        //$mform->addRule('intro', $strrequired, 'required', null, 'client'); 
       // $mform->setDefault('intro', ' ');
        //JAV:se a�ade el campo intro est�ndar en todos los m�dulos de moodle (permitir� mostrar la descripci�n de este m�dulo en la pantalla principal del curso)
		
		$this->add_intro_editor(true, get_string('description', 'blended'));
        
        
         //Metodo de identificación ----------------------------------------------
        $idmethodoptions = array(0 => get_string('coded','blended'), 1 => get_string('plain','blended'));
        $mform->addElement('select', 'idmethod', get_string("idmethod", "blended"), $idmethodoptions);
		$mform->addHelpButton('idmethod','idmethod', 'blended');
	    $mform->setDefault('idmethod', 0);

        // Tipo Codigo de barras  ----------------------------------------------
        $idcodeoptions = array('QR2D'=>'QR 2D','C39' => 'C39','EAN13'=>'EAN13');
        $mform->addElement('select', 'codebartype', get_string("codebartype", "blended"), $idcodeoptions);
	$mform->addHelpButton('codebartype', 'codebartype', 'blended');
        $mform->setDefault('codebartype', 'QR2D');
    	
	    // enable/disable OMR part
	   
	    $mform->addElement('checkbox','omrenabled', get_string('OMRenable','blended'),get_string('OMRenableLabel','blended'));
	   
	    
        // Número de columnas del cuestionario --------------------------------------
        $numbercols = array_combine(range(1, 4), range(1, 4));
        $mform->addElement('select', 'numcols', get_string("numbercols", "blended"), $numbercols);
        $mform->addHelpButton('numcols','numbercols', 'blended');
        $mform->setDefault('numcols', 2);
        
        // Identificador ---------------------------------------------------------
        $options1 = array('userid' => get_string('userid','blended'), 
        		  'idnumber' => get_string('idnumber','blended'));
        $options2 = array(); 
        if (!$options = $DB->get_records_menu("user_info_field",null,"name", "id, name")) {
            //print('No info user fields found!');
        }
        else
        foreach ($options as $id => $name) {
            //$fieldid = $DB->get_field("user_info_field","id",array("name"=>$name));
            $options2["2".$id] = get_string("userinfo", "blended").$name;
        }    
        $idtypeoptions =   $options1 + $options2;
        //$idtypeoptions = array_merge($options1, $options2);
        $mform->addElement('select', 'idtype', get_string("idtype", "blended"), $idtypeoptions);
        $mform->addHelpButton('idtype', 'idtype', 'blended');
        //$mform->setDefault('idtype', "0");        
        
        // Longitud de identificacion de usuario ----------------------------------------
        $lengthuserinfooptions = array_combine(range(2, 12), range(2, 12));
        $mform->addElement('select', 'lengthuserinfo', get_string("lengthuserinfo", "blended"), $lengthuserinfooptions);
        $mform->addHelpButton('lengthuserinfo','lengthuserinfo', 'blended');
        $mform->setDefault('lengthuserinfo', 8);
        
        // Creación de los equipos -----------------------------------------------
        $teammethodoptions = array(0 => get_string('byteacher','blended'), 1 => get_string('bystudents','blended'), 2=> get_string('bystudentswithleaders','blended'));
        $mform->addElement('select', 'teammethod', get_string("teammethod", "blended"), $teammethodoptions);
        $mform->addHelpButton('teammethod','teammethod', 'blended');
        $mform->setDefault('teammethod', 0);
        
        // Tarea por defecto -----------------------------------------------------
     
      //  $courseid=$this->_cm->course;
   		$courseid=$COURSE->id;
        if ($courseid!='')
        if ($modules=get_coursemodules_in_course("assignment", $courseid)) 
        {
            foreach ($modules as $mod_instance) {
                $options[$mod_instance->instance] = $mod_instance->name;
            }   
            $assignmentoptions = array(0 => get_string('any', 'blended')) + $options;
            $mform->addElement('select', 'assignment', get_string("defaultassignment", "blended"), $assignmentoptions);
 			$mform->addHelpButton('assignment', 'defaultassignment', 'blended');
            $mform->setDefault('assignment', 0);
        }  
        
        // Numero de equipos por defecto ----------------------------------------
        $teamsoptions = array_combine(range(1, TEAMS_MAX_ENTRIES),range(1, TEAMS_MAX_ENTRIES));
        $mform->addElement('select', 'numteams', get_string("defaultnumteams", "blended"), $teamsoptions);
        $mform->setDefault('numteams', 10);
        
        // Numero de miembros por defecto ----------------------------------------
        $nummembersoptions = array_combine(range(1, MEMBERS_MAX_ENTRIES),range(1, MEMBERS_MAX_ENTRIES));
        $mform->addElement('select', 'nummembers', get_string("defaultnummembers", "blended"), $nummembersoptions);
		$mform->addHelpButton('nummembers','defaultnummembers', 'blended');
        $mform->setDefault('nummembers', 4);
        
      
        
        // Otras caracteristicas -------------------------------------------------
        $features = new stdClass;
        $features->groups = false;
        $features->groupings = true;
        $features->groupmembersonly = true;
        $this->standard_coursemodule_elements($features);
		
		
		

        // buttons
        $this->add_action_buttons();
    }


}
?>
