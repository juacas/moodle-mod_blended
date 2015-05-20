<?php // $Id: version.php,v 1.3 2006/08/28 16:41:20 mark-nielsen Exp $
/**
 * Code fragment to define the version of blended
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @author 
 * @version $Id: version.php,v 1.3 2006/08/28 16:41:20 mark-nielsen Exp $
 * @package blended
 **
*/
defined('MOODLE_INTERNAL') || die;

$module->version  = 2015052001;    // The current module version (Date: YYYYMMDDXX).
$module->requires = 2013051407.00;    // Requires this Moodle version.2013111801.11
$module->component = 'mod_blended'; // Full name of the plugin (used for diagnostics)
$module->cron     = 0;          // Period for cron to check this module (secs)

?>