<?php
//
// Capability definitions for the assignment module.
//
// The capabilities are loaded into the database table when the module is
// installed or updated. Whenever the capability definitions are updated,
// the module version number should be bumped up.
//
// The system has four possible values for a capability:
// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
//
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
// [mod/block]/<component_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
// moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
// block/recent_activity:view
// moodle/site:deleteuser
//
// The variable name for the capability definitions array follows the format
// $<componenttype>_<component_name>_capabilities
//
// For the core capabilities, the variable is $moodle_capabilities.

$capabilities = array (

		'mod/blended:addinstance' => array (
		
		'riskbitmask' => RISK_XSS,

		'captype' => 'write',
		'contextlevel' => CONTEXT_COURSE,
		'archetypes' => array (
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
		),
		'clonepermissionsfrom' => 'moodle/course:manageactivities'
),

		'mod/blended:view' => array(

		'captype' => 'read',
		'contextlevel' => CONTEXT_MODULE,
		'archetypes' => array(
				'student' => CAP_ALLOW,
				'frontpage' => CAP_ALLOW,
				'teacher' => CAP_ALLOW,
				'editingteacher' => CAP_ALLOW,
				'manager' => CAP_ALLOW
		)
),
	
		'mod/blended:blended' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:rolelinks' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:printlabels' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:printassignmentpage' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' 			=> CAP_PREVENT,
						'student' 			=> CAP_ALLOW,
						'teacher' 			=> CAP_ALLOW,
						'editingteacher' 	=> CAP_ALLOW,
						'manager' 			=> CAP_ALLOW 
				) 
		),
		
		'mod/blended:selectoneamongallstudents' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:introgrades' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:introteams' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:signupteam' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_ALLOW,
						'teacher' => CAP_PREVENT,
						'editingteacher' => CAP_PREVENT,
						'manager' => CAP_PREVENT 
				) 
		),
		
		'mod/blended:printquizes' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:deletejob' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:launchjob' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:createscannedjob' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:viewscannedjobs' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:managealljobs' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:deletescanjob' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:deletequiz' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:deleteall' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_PREVENT,
						'editingteacher' => CAP_PREVENT,
						'manager' => CAP_ALLOW 
				) 
		),
		'mod/blended:editresults' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:evaluatequiz' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:reviewresults' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:viewimage' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:viewstatus' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:viewstatusdetails' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_PREVENT,
						'editingteacher' => CAP_PREVENT,
						'manager' => CAP_ALLOW 
				) 
		),
		
		'mod/blended:viewalerts' => array (
				
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'legacy' => array (
						'guest' => CAP_PREVENT,
						'student' => CAP_PREVENT,
						'teacher' => CAP_ALLOW,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW 
				) 
		)
		
		 
);

?>