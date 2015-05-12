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
 * This file keeps track of upgrades to the clusterer module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installtion to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in
 * lib/ddllib.php
 *
 * @package   mod-clusterer
 * @copyright 2009 Your Name
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * xmldb_clusterer_upgrade
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_blended_upgrade($oldversion=0) {

    global $CFG, $THEME, $DB;
    
    $dbman = $DB->get_manager();
    $result=true;
  

/// And upgrade begins here. For each one, you'll need one
/// block of code similar to the next one. Please, delete
/// this comment lines once this file start handling proper
/// upgrade code.

/// if ($result && $oldversion < YYYYMMDD00) { //New version in version.php
///     $result = result of "/lib/ddllib.php" function calls
/// }

/// Lines below (this included)  MUST BE DELETED once you get the first version
/// of your module ready to be installed. They are here only
/// for demonstrative purposes and to show how the clusterer
/// iself has been upgraded.

/// For each upgrade block, the file clusterer/version.php
/// needs to be updated . Such change allows Moodle to know
/// that this file has to be processed.

/// To know more about how to write correct DB upgrade scripts it's
/// highly recommended to read information available at:
///   http://docs.moodle.org/en/Development:XMLDB_Documentation
/// and to play with the XMLDB Editor (in the admin menu) and its
/// PHP generation posibilities.

/// First example, some fields were added to the module on 20070400
//    if ($result && $oldversion < 2007040100) {
//
//    /// Define field course to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $field = new XMLDBField('course');
//        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'id');
//    /// Launch add field course
//        $result = $result && add_field($table, $field);
//
//    /// Define field intro to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $field = new XMLDBField('intro');
//        $field->setAttributes(XMLDB_TYPE_TEXT, 'medium', null, null, null, null, null, null, 'name');
//    /// Launch add field intro
//        $result = $result && add_field($table, $field);
//
//    /// Define field introformat to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $field = new XMLDBField('introformat');
//        $field->setAttributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'intro');
//    /// Launch add field introformat
//        $result = $result && add_field($table, $field);
//    }
//
///// Second example, some hours later, the same day 20070401
///// two more fields and one index were added (note the increment
///// "01" in the last two digits of the version
//    if ($result && $oldversion < 2007040101) {
//
//    /// Define field timecreated to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $field = new XMLDBField('timecreated');
//        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'introformat');
//    /// Launch add field timecreated
//        $result = $result && add_field($table, $field);
//
//    /// Define field timemodified to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $field = new XMLDBField('timemodified');
//        $field->setAttributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'timecreated');
//    /// Launch add field timemodified
//        $result = $result && add_field($table, $field);
//
//    /// Define index course (not unique) to be added to clusterer
//        $table = new XMLDBTable('clusterer');
//        $index = new XMLDBIndex('course');
//        $index->setAttributes(XMLDB_INDEX_NOTUNIQUE, array('course'));
//    /// Launch add index course
//        $result = $result && add_index($table, $index);
//    }
//
///// Third example, the next day, 20070402 (with the trailing 00), some inserts were performed, related with the module
//    if ($result && $oldversion < 2007040200) {
//    /// Add some actions to get them properly displayed in the logs
//        $rec = new stdClass;
//        $rec->module = 'clusterer';
//        $rec->action = 'add';
//        $rec->mtable = 'clusterer';
//        $rec->filed  = 'name';
//    /// Insert the add action in log_display
//        $result = insert_record('log_display', $rec);
//    /// Now the update action
//        $rec->action = 'update';
//        $result = insert_record('log_display', $rec);
//    /// Now the view action
//        $rec->action = 'view';
//        $result = insert_record('log_display', $rec);
//    }
/*@var $field XMLDBField*/
     if ($result && $oldversion < 2011111402) {
     	$table = new XMLDBTable('blended_jobs');
        $field = new XMLDBField('timestampt');
        $field->setAttributes(XMLDB_TYPE_INTEGER,10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $result = $result & rename_field($table,$field,'timestamp'); 
    
     	$table = new XMLDBTable('blended_scans');
         $field = new XMLDBField('timestampt');
        $field->setAttributes(XMLDB_TYPE_INTEGER,10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $result = $result & rename_field($table,$field,'timestamp');
        
        $table = new XMLDBTable('blended_attempts');
         $field = new XMLDBField('timestampt');
        $field->setAttributes(XMLDB_TYPE_INTEGER,10, XMLDB_UNSIGNED, XMLDB_NOTNULL);
        $result = $result & rename_field($table,$field,'timestamp'); 
     }
 if ($result && $oldversion < 2011111800) {
     	$table = new XMLDBTable('blended_images');
        $field = new XMLDBField('activitycode');
        $field->setAttributes(XMLDB_TYPE_INTEGER,10, XMLDB_UNSIGNED, !XMLDB_NOTNULL);
        $result = $result & change_field_notnull($table,$field); 
    
     }
if ($result && $oldversion < 2011112800) {
     	$table = new XMLDBTable('blended_jobs');
        $field = new XMLDBField('identifylabel');
        $field->setAttributes(XMLDB_TYPE_CHAR,255, XMLDB_UNSIGNED, !XMLDB_NOTNULL);
        $result = $result & change_field_precision($table, $field);
        $result = $result & rename_field($table,$field,'options'); 
     }
	
if ($result && $oldversion < 2014071406) {
     	$table = new xmldb_table('blended_assign_grouping');
     	$table->add_field('id',XMLDB_TYPE_INTEGER,'10',null,XMLDB_NOTNULL,XMLDB_SEQUENCE,null);
     	$table->add_field('id_assign',XMLDB_TYPE_INTEGER,'10',null,XMLDB_NOTNULL,null,null,'id');
     	$table->add_field('id_grouping',XMLDB_TYPE_INTEGER,'10',null,XMLDB_NOTNULL,null,null,'id_assign');
     	$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
      	if(!$dbman->table_exists($table)){
     		$dbman->create_table($table);
      	}
      	
      	$table1=new xmldb_table('blended_team');
      	$field=new xmldb_field('id_team',XMLDB_TYPE_INTEGER,'4',null,XMLDB_NOTNULL,null,'0','id');
      	if(!$dbman->field_exists($table1, $field)){
      		$dbman->add_field($table1, $field);
      	}
      	upgrade_mod_savepoint(true, '2014071406', 'blended');
            	
     }
 if ($result && $oldversion<2015022300)
 {
     $table = new xmldb_table('blended');
     $field = new xmldb_field('omrenabled',XMLDB_TYPE_INTEGER,'1',true,XMLDB_NOTNULL,false,0,'introformat');
     $dbman->add_field($table,$field);
     upgrade_mod_savepoint(true, '2015022300', 'blended');
      
 }
/// Final return of upgrade result (true/false) to Moodle. Must be
/// always the last line in the script
    return $result;
}
