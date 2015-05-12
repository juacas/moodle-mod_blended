<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Define all the backup steps that will be used by the backup_blended_activity_task
 *
 * @package   mod_blended
 * @author	   Jéssica Olano López, Juan Pablo de Castro Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
*/

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete choice structure for backup, with file and id annotations
 *
  * @package   mod_blended
 * @author	   Jéssica Olano López, Juan Pablo de Castro Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
*/


class backup_blended_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure for the assign activity
     * @return void
     */
    protected function define_structure() {

        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $blended = new backup_nested_element('blended', array('id'),
                                            array('name',
                                            	  'intro',
                                                  'introformat',
                                                  'idmethod',
                                                  'idtype',
                                                  'codebartype',
                                                  'lengthuserinfo',
                                                  'teammethod',
                                                  'numteams',
                                                  'nummembers',
                                                  'assignment',
                                                  'randomkey'));
        
        
       
  
        $teams=new backup_nested_element('teams');
        $team= new backup_nested_element('team',array('id'),array('id_team','itemid','name_team','userid_leader'));
        
        $members=new backup_nested_element('members');
        $member=new backup_nested_element('member',array('id'),array('userid','id_member','id_team','leader'));
        
       
        //Build the tree
    
        $blended->add_child($teams);
       		$teams->add_child($team);
        		$team->add_child($members);
        			$members->add_child($member);
      
        
        // Define sources
        $blended->set_source_table('blended', array('id' => backup::VAR_ACTIVITYID));
 //TODO remove blended_member estructure
        $member->set_source_table('blended_member', array('id_team' => backup::VAR_PARENTID));
        //$team->set_source_table('blended_team', array('blendedid' => backup::VAR_PARENTID));
        //$assign_grouping->set_source_table('blended_assign_grouping', array('blendedid' => backup::VAR_PARENTID));
        //$member->set_source_table('blended_member', array('blendedid' => backup::VAR_PARENTID));
        //$grade->set_source_table('blended_grade', array('blendedid' => backup::VAR_PARENTID));
        
        
        //Anotate ids
        $team->annotate_ids('group', 'id_team');
        $team->annotate_ids('user', 'userid_leader');
        $member->annotate_ids('user', 'userid');
        $member->annotate_ids('group', 'id_team');
        
        
        // This file area hasn't itemid.
        $blended->annotate_files('mod_blended', 'intro', null);
       
        // Return the root element (blended), wrapped into standard activity structure.
        return $this->prepare_activity_structure($blended);
        
    }
}
