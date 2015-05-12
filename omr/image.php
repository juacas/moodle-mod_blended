<?php
 require_once("../../config.php");
 require_once("lib.php");
 require_once('recognitionprocess.php');

 
 
$id =			 optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  =		 	 optional_param('a',  0, PARAM_INT); // Blended ID
//$imagepath  = 	 optional_param('imagepath',  0, PARAM_TEXT); 
$jobid  =		 optional_param('jobid',  0, PARAM_INT); 
$acode = 		 optional_param('acode',0,PARAM_INT);
$navigationpage = optional_param('navpage',  0, PARAM_TEXT); 
$pageindex = optional_param('pageindex',  0, PARAM_TEXT); 

if ($id) {
	if (! $cm = get_coursemodule_from_id('blended', $id)){
		error("Course Module ID was incorrect");
	}

	if (! $course = get_record("course", "id", $cm->course)) {
		error("Course is misconfigured");
	}

	if (! $blended = get_record("blended", "id", $cm->instance)) {
		error("Course module is incorrect");
	}
	if (! $user = get_record("user", "id", $USER->id) ) {
		error("No such user in this course");
	}
} else {
	if (! $blended = get_record("blended", "id", $a)) {
		error("Course module is incorrect");
	}
	if (! $course = get_record("course", "id", $blended->course)) {
		error("Course is misconfigured");
	}
	if (! $cm = get_coursemodule_from_instance("blended", $blended->id, $course->id)) {
		error("Course Module ID was incorrect");
	}
	if (! $user = get_record("user", "id", $USER->id) ) {
		error("No such user in this course");
	}
}
   // Log ---------------------------------------------------------------------------

    add_to_log($course->id, "blended", "scannedJob", "scannedJob.php?a=$blended->id", "$blended->id");

// Capabilities ----------------------------------------------------- 
        
    require_login($cm->course, false,$cm);
    
    $context_course = context_course::instance($cm->course);
    if(!get_role_users(5, $context_course, false, 'u.id, u.lastname, u.firstname')) {
        error("No students in this course");   
    }
    
    $context = context_module::instance($cm->id);
    require_capability('mod/blended:viewimage', $context);

// Get the strings --------------------------------------------------------------- 
	
    $strcorrectionpage    = get_string('correction','blended');
    $strscannedJobpage    = get_string('scannedJob','blended');
    $strimagepage		  = get_string("imagepage","blended");
    $strtable				= get_string("table","blended");
    
    $strrevisionpage     = get_string('revision','blended');
    $strreviewdetailspage    = get_string('reviewdetails','blended');
// Print the page header ---------------------------------------------------------

    if ($navigationpage == "showdetails.php")
    {
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strcorrectionpage,'link'=>"../../mod/blended/correction.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strscannedJobpage,'link'=>"../../mod/blended/scannedJob.php?a=$blended->id&jobid=$jobid", 'type'=>'misc'),
                                        array('name' => $strimagepage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strscannedJobpage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strscannedJobpage), 
                  navmenu($course, $cm));
    }
    if ($navigationpage == "reviewdetails.php")
    {
    $navigation = build_navigation(array(array('name' => $blended->name,'link'=>"../../mod/blended/view.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strrevisionpage,'link'=>"../../mod/blended/revision.php?a=$blended->id", 'type'=>'misc'),
 								array('name' => $strreviewdetailspage,'link'=>"../../mod/blended/reviewdetails.php?a=$blended->id", 'type'=>'misc'),
                                        array('name' => $strimagepage,'link'=>null, 'type'=>'misc')));
    print_header("$course->shortname: $blended->name: $strreviewdetailspage", "$course->shortname",$navigation, 
                  "", "", true, update_module_button($cm->id, $course->id, $blended->name, $strreviewdetailspage), 
                  navmenu($course, $cm));
    
    }
print_spacer(20);
print_box(format_text($strtable), 'generalbox', 'intro');
print_spacer(20);


// Print the main part of the page ----------------------------------   
    print_spacer(20);
    print_heading(format_string(get_string('correction', 'blended')));
    print_box(format_text(get_string('imagepagedesc', 'blended')), 'generalbox', 'intro');
    print_spacer(20);

//print $imagepath;

$imgout = get_field('blended_images','imgout','jobid',$jobid,'activitycode',$acode,'pageindex',$pageindex);
//print "<BR>IMGOUT".$imgout;
$imagepath = get_image_src($imgout);    

//echo "<BR>IMGPATHNEW";
//print $imagepath;

show_image($imagepath,$course);


if ($navigationpage == "showdetails.php")
{
$volver ="$CFG->wwwroot/mod/blended/showdetails.php?id=$course->id&acode=$acode&jobid=$jobid";
}

if ($navigationpage == "reviewdetails.php")
{
$volver ="$CFG->wwwroot/mod/blended/reviewdetails.php?id=$course->id&acode=$acode&jobid=$jobid";
}

print_continue($volver);

	echo "<BR><BR><center>";
    helpbutton($page='image', get_string('pagehelp','blended'), $module='blended', $image=true, $linktext=true, $text='', $return=false,$imagetext='');
    echo "</center>";

print_footer($course);

function show_image($imagepath,$course)
{
	//print $imagepath;
	
	$src = create_image_url($imagepath,$course);
	//print '<BR> Encoded: '.$src;
	echo "<center><img src=$src align=CENTER/></center>";
	
	
	return;
}



?>