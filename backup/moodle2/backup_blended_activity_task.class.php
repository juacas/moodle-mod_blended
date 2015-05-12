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
 * This file contains the backup activity for the blended module
 *
  * @package   mod_blended
 * @author	   Jéssica Olano López, Juan Pablo de Castro Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
*/

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/blended/backup/moodle2/backup_blended_stepslib.php');
require_once($CFG->dirroot . '/mod/blended/backup/moodle2/backup_blended_settingslib.php');

/**
 * blended backup task that provides all the settings and steps to perform one complete backup of the activity
 *
  * @package   mod_blended
 * @author	   Jéssica Olano López, Juan Pablo de Castro Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
*/
class backup_blended_activity_task extends backup_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
    	$this->add_step(new backup_blended_activity_structure_step('blended_structure', 'blended.xml'));
    	 
         }

    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     * @param string $content
     * @return string
     */
    static public function encode_content_links($content) {
     //   global $CFG;

//         $base = preg_quote($CFG->wwwroot, "/");

//         $search="/(".$base."\/mod\/assign\/index.php\?id\=)([0-9]+)/";
//         $content= preg_replace($search, '$@ASSIGNINDEX*$2@$', $content);

//         $search="/(".$base."\/mod\/assign\/view.php\?id\=)([0-9]+)/";
//         $content= preg_replace($search, '$@ASSIGNVIEWBYID*$2@$', $content);

        return $content;
    }

}

