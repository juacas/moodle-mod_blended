<?php
if (PHP_OS=='Linux') {
    $default_pathOmr   = "/usr/bin/omr/blendedOmr.sh";
    

} else if (PHP_OS=='Darwin') {
    // most likely needs a fink install (fink.sf.net)
    $default_pathOmr     = "/sw/bin/omr/blendedOmr.sh";
   

} else if (PHP_OS=='WINNT' or PHP_OS=='WIN32' or PHP_OS=='Windows') {
    
    $default_pathOmr    = "\"c:\\Program Files\\blended\\blendedOmr.bat\" ";
   

} else {
   $default_pathOmr  = "";
}
$settings->add(new admin_setting_configexecutable('blended_omr_path', get_string('omrPath', 'blended'), '', $default_pathOmr));
     