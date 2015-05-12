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
 * 
 *
 * Library of functions and constants for module blended
 *
 *********************************************************************************/

global $CFG;
require_once($CFG->dirroot . '/lib/weblib.php');

require_once('OMRError.php');
require_once('ResultsError.php');
require_once('correctionlib.php');


function start_recognition_process($data,$course)
{
	global $CFG;
	global $USER;    
	
    $timestamp = time();
	$escaneo->scan_name = $data->scanJobFile;
	$escaneo->blended = $data->blended;
	$escaneo->userid = $USER->id;
	$escaneo->timestamp = $timestamp;
	$escaneo->status = JOB_STATE_WAITING;	
	$escaneo->course = $course->id;
	
	if (get_records($table='blended_scans', $field='scan_name', $value=$data->scanJobFile, $sort='', $fields='*') == false)
	{
		if (!$escaneo->id = insert_record('blended_scans', $escaneo)) 
			{
				error('Could not create new scan record');
			}	
	}
	else
	{
		error('El trabajo que intenta guardar ya existe. Para volver a crearlo borre el trabajo anterior o guarde el trabajo con un nombre diferente.');
	}	
	return;
}		

/**
 * @return true if there are any scan job waiting
 * Enter description here ...
 * @param unknown_type $currentpage
 * @param unknown_type $context
 * @param unknown_type $blended
 */
function show_scan_jobs($currentpage,$context,$blended)
{
	global $CFG;
	global $USER;
	global $DB,$OUTPUT;


	$userid = $USER->id;
	$areWaiting=false;
	
if (has_capability('mod/blended:managealljobs', $context))
// {	if (($scans_view = get_records_select($table="blended_scans", $select="blended=$blended->id")) == false)
 {	if (($scans_view = $DB->get_records_select('blended_scans', $select="blended=$blended->id")) == false)
{
echo $OUTPUT->box("There are no scan jobs in the course.");
	}
}
else 
if (has_capability('mod/blended:viewscannedjobs', $context))
{
// if (($scans_view = get_records_select($table="blended_scans", $select= "userid=$userid and blended=$blended->id")) == false)
 if (($scans_view = $DB->get_records_select('blended_scans', $select= "userid=$userid and blended=$blended->id")) == false)
	{
echo $OUTPUT->box("There are no scan jobs created by the current user in the course.");
	}
}	


if ($scans_view!= false)
{
	$i = 0;
	$scans=array();
	
	foreach ($scans_view as $scan)
	{
		$jobname = $scan -> scan_name;
		$jobid = $scan -> id;

		if (has_capability('mod/blended:deletescanjob', $context))
		{
			$imgdelete="<img src=\"delete.gif\"/>";	
			$link =  "<a href=\"deletescanjob.php?scanjobid=$scan->id&a=$blended->id\">$imgdelete</a>";	
			$delete = $link;
		}
		
		$fecha = date("d-m-y h:i:s", $scan->timestamp);

		$scanrow = array();
		
		$filesUrl = create_url($scan->scan_name,$scan->course);
		$scannedJobUrl="scannedJob.php?&a=$blended->id&jobid=$jobid";
		
		$scanrow["name"]='';
		$scanrow["view"]='';
		
		$scanrow["view"] = "";
		$scanrow["date"]= $fecha;
		$scanrow["status"]='';
		$scanrow['userid']='';
		$scanrow['delete']='';
		$scanrow['relaunch']='';
		
		if (($scan -> status !== JOB_STATE_WAITING))
		{
			$statuslink = "<a href=\"jobstatus.php?page=$currentpage&a=$blended->id&jobid=$jobid\">".get_string($scan->status,'blended')."</a>";
			$scanrow["status"]= $statuslink;
		}
		else
			$scanrow["status"]= get_string($scan->status,'blended');
		
		$view=get_string('scannedJob','blended');
			
		if ($scan->status == JOB_STATE_FINISHED)
		{
			
			$scanrow["name"] = "<a href=\"$scannedJobUrl\">$scan->scan_name</a>";
		}
		else 
		{
			$scanrow["name"] = $scan->scan_name;
		}
		
		if ($scan->status == JOB_STATE_WAITING || $scan->status == JOB_STATE_BUSY)
		{
			$areWaiting=true;
		}
		$scanrow["view"] = "<a href=\"$filesUrl\">$view</a>";
			
		$user_reg = get_record($table='user', $field='id', $value=$scan->userid);
		$fullname=fullname($user_reg);
		
		$scanrow["userid"]= $fullname;
		$scanrow["delete"]= $delete;
		if (  ($scan->status== JOB_STATE_FINISHED || $scan->status == JOB_STATE_IN_ERROR)
				 && has_capability('mod/blended:createscannedjob', $context))
		{
			$relaunchUrl="launchScanJob.php?jobid=$scan->id&a=$blended->id";
			$scanrow['relaunch'] = "<a href=\"$relaunchUrl\" >".get_string('launchJob','blended')."</a>";	 
		}
		$scans[$i]=$scanrow;
		$i++;
	
		
	}
	//tabla de escaneos
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array(	get_string('correction','blended'),
							$view,
							get_string('date'),
							get_string('jobstatus','blended'),
							get_string('user'),
							get_string('delete'));
	$align = "left";
	$table->align = array ($align, $align, $align, $align, $align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $scans;
	print_table($table);
	}	
	
	return $areWaiting;
}

/**
 * show a list of quizzes that are generated by this blended instance
 * @param unknown_type $currentpage
 * @param unknown_type $context
 * @param unknown_type $blended
 */
function show_quizzes($currentpage,$context,$blended)
{
	global $CFG;
	global $USER;
	global $DB,$OUTPUT;


	$userid = $USER->id;

if (has_capability('mod/blended:managealljobs', $context))
{ 
// if (($quizzes_jobs = get_records_select($table="blended_jobs", $select="blended=$blended->id group by quiz")) == false)
if (($quizzes_jobs = $DB->get_records_select('blended_jobs', $select="blended=$blended->id group by quiz")) == false)
	{
echo $OUTPUT->box("There are no quiz jobs in the course.");
	}	
}else
if (has_capability('mod/blended:viewscannedjobs', $context))
{ 
// if (($quizzes_jobs = get_records_select($table="blended_jobs", $select= "blended=$blended->id group by quiz")) == false)
if (($quizzes_jobs = $DB->get_records_select('blended_jobs', $select= "blended=$blended->id group by quiz")) == false)
	{
echo $OUTPUT->box("There are no quiz jobs created by the current user in the course.");
	}
}
	

if ($quizzes_jobs!= false)
{
	$i = 0;
	$rows=array();
	
	foreach ($quizzes_jobs as $quiz_job)
	{
		$row = array();
	
		
		$quiz= get_quiz_module($quiz_job->quiz);
		
		$closeTime = $quiz->timeclose==0?get_string('pending','blended'):date("d-m-y h:i:s", $quiz->timeclose);
		$quizJobUrl="quizJob.php?&a=$blended->id&quizid=$quiz->id";
		
		$row["name"]='<a href="'.$quizJobUrl.'">'.$quiz->name.'</a>';
		$row["numPublished"]=count_records('blended_attempts','quiz',$quiz->id);
		$row["numCompleted"]=count_records('blended_attempts','quiz',$quiz->id,'status',JOB_STATE_FINISHED);
		$row["date"]= $closeTime;
		
		$rows[]=$row;
		
	}
	//tabla de escaneos
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array(	get_string('modulename','quiz'),
							get_string('numquiz','blended'),
							get_string('jobstatus','blended'),
							get_string('date')
							);
	$align = "left";
	$table->align = array ($align, $align, $align, $align, $align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $rows;
	print_table($table);
	}
	
	return false;
}
/**
 * Get ScanJob id by name
 * Enter description here ...
 * @param unknown_type $jobname
 */
function get_jobid($jobname)
{
	$record=get_record($table='blended_scans', $field='scan_name', $value=$jobname, $sort='', $fields='id');
	$jobid=$record->id;
	return $jobid;
}

function update_blended_images ($job)
{
		$imgout = $job -> imgout;
		$acode = $job->activitycode;
		//print $imgout;
		$image=get_record($table='blended_images','id', $job->id);
		if (!$image)
		error("Fatal error. Image $job->imgsrc is not in database. Send this information to the administrator.");

		$image->activitycode = $acode;		
		$image->pageindez=$job->pageindex;
		if(update_record('blended_images', $image) == false)
		error(" No se pudo actualizar la base de datos blended_images");
		
		//echo "Se ha actualizado la base de datos blended_images";	
		
	return;
}

function show_images_table($blended,$jobid,$course,$context)
{
	global $CFG;
	$currentpage = 'scannedJob.php';

	if (!$images_view = get_records($table="blended_images",  $field='jobid', $value=$jobid, $sort='activitycode,pageindex'))
	{
	print_box(get_string('ErrorScannedImageNotFound','blended'));
	}
	
	else
	{
	
	$i = 0;
	$images=array();
	$displayed_codes = array();
$iconWarning='<img src="images/warning.png" width="32" alt="'.get_string('MarkWarning', 'blended').'"/>';
echo "<form action=\"evaluate.php\" method=\"POST\" >";
echo '<input type="hidden" name="a" value="'.$blended->id.'"/>';
echo '<input type="hidden" name="jobid" value="'.$jobid.'"/>';

	foreach ($images_view as $result)
	{
		$acode=$result->activitycode;
		$imgout=$result->imgout;
		$pageindex = $result -> pageindex;
	
		if ($acode)
		{
		$quiz_already_displayed = in_array($acode,$displayed_codes);
		
		if ($quiz_already_displayed == false)
		{
		
		$imagerow=array();

		$id_member = find_userid($acode,$jobid);
		$user_reg = blended_get_user($id_member,$blended);
	
		$verresultados = get_string('modulename','quiz').$acode; 
		$quiz_in_course = check_quiz_course($acode,$course);	 
		$showdetailslink="<a href=\"showdetails.php?&a=$blended->id&acode=$acode&jobid=$result->jobid\">$verresultados</a>";
		$attempts=get_record('blended_attempts', 'id',$acode);
		
		$index=$i+1;
		if ($user_reg!=null && $attempts!=false)
			$checkbox ="<input type=\"checkbox\" name=\"selectedActivity$index\" value=\"$acode\" />";
		else
			$checkbox="<input type=\"checkbox\" name=\"selectedActivity$index\" disabled=\"true\" value=\"$acode\" />";
		$imagerow["index"]=$checkbox.$index;
		$imagerow["correctionlink"]= $showdetailslink;
		$imagerow["activitycode"]= $result->activitycode.get_string('page','blended').$pageindex;
		$imagerow["alumno"] = '';

		if ($user_reg !== null)
			$imagerow["alumno"]=print_user_picture($user_reg,$course->id,null,null,true). fullname($user_reg);
			else
			$imagerow["alumno"]=get_string('ErrorUserIDEmpty','blended');
		$imagerow["pasar"]='';
		$imagerow["status"]='';
		$status='';
		
		$warnings=count_doubtfull_marks($acode,$jobid);
			if ($warnings>0)
			{
				$warn=$iconWarning."Hay $warnings marcas dudosas!!";
			}
			else
			{
				$warn='';
			}
			
		$imagerow["activitycode"]=$warn.'<br/>'.$imagerow["activitycode"];

		if ($quiz_in_course)
		{
			if (has_capability('mod/blended:evaluatequiz', $context))
			{
			if ($result->status==IMAGE_STATUS_PENDING)
				{
				$evaluate=get_string('blendedPassToQuiz','blended');
				$link="<a href=\"evaluate.php?&a=$blended->id&acode=$acode&jobid=$jobid\">$evaluate</a>";
				$imagerow["pasar"]=$link;
				$status=get_string('NotYet','blended');	
				}
			else
				{
				$imagerow["pasar"]=get_string('blendedPassedToQuiz','blended');
				$link="<a href=\"evaluate.php?a=$blended->id&acode=$acode&jobid=$jobid\">".get_string('blendedPassAgainToQuiz','blended')."</a>";
				$status = $link;
				}
			}
			else
			{
			$imagerow["pasar"] = "Permission Denied".
			$status = ""; 
			}
		}
		
		$imagerow["status"]=$status;

		if (has_capability('mod/blended:deletequiz', $context))
		{
		$imgdelete = "<img src=\"delete.gif\"/>";
		$link =  "<a href=\"delete_quiz.php?page=$currentpage&acode=$acode&jobid=$jobid&a=$blended->id\">$imgdelete</a>";
		$delete = $link;
		$imagerow["delete"]=$delete;
		}
		
		if ($attempts==false)
		{ // attempt not found in blended.
		/*	echo"<center>";
			mtrace("El cuestionario que ha procesado pertenece a otro curso o no existe en la base de datos de este servidor.");
			echo"<center><br><br>";*/
			$scan=blended_getOMRScanJob($result->jobid);
			$resultname=$scan->scan_name."-".($result->id);
			
			$correctionlink="<a href=\"activitycode.php?&a=$blended->id&jobid=$result->jobid&resultid=$result->id\">$resultname</a>";
			$imagerow["correctionlink"]= $correctionlink;
			//$imagerow["correctionlink"]='';
			$imagerow["activitycode"]= "Erróneo.".$result->activitycode.' '.get_string('pages','blended').$pageindex;
			$imagerow["alumno"] = '';
			$imagerow["pasar"] = '<img align="left" src="images/warning.png" width="32"/>'.get_string('ErrorActivityCodeNotFound','blended',$acode);
			$status = "";
		
			$key="$acode$pageindex";
			$images[$key]=$imagerow;	
		}
		else 
		{
			$displayed_codes[$acode]=$acode;// avoid using another rows for different pages of the same activity
			$images[$acode]=$imagerow;
		}
			$i++;
		}
		else // complement page set
		{
			$images[$acode]["activitycode"].=", ".$pageindex;
		}
	
	} // $acode!=null

	}
	echo '<input type="hidden" name="numActivities" value="'.$index.'" />';

	/**
	*  Add results with no $acode detected
	***/
	foreach ($images_view as $result)
	{
		$scan=blended_getOMRScanJob($result->jobid);
		$acode=$result->activitycode;
		$resultname=$scan->scan_name."-".($result->id);
		
		$page = $result -> page;
		
		if ($acode==null)
		{	
			
		$imagerow=array();
		
		$imagerow["index"]="";
		
		$correctionlink="<a href=\"activitycode.php?&a=$blended->id&jobid=$result->jobid&resultid=$result->id\">$resultname</a>";
		$imagerow["correctionlink"]= $correctionlink;
		$imagerow["activitycode"]= get_string('UnclassifiedPage','blended');
		$imagerow["alumno"]='';
		$imagerow["pasar"]='';
		$imagerow["status"]=get_string('NotYet','blended');

		if (has_capability('mod/blended:deletescanjob', $context))
		{
		$link =  "<a href=\"delete_quiz.php?page=$currentpage&acode=$acode&jobid=$jobid&a=$blended->id\">$imgdelete</a>";
		$delete = $link;
		
		$imagerow["delete"]=$delete;
		}
		
		$images[]=$imagerow;
		$i++;
	}

	}
	//tabla de resultados
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array('Seleccionar','Ver Resultados','ActivityCode','Alumno','Pasar a QUIZ','Evaluado en Moodle','Borrar');
	$align = "left";
	$table->align = array ($align,$align,$align,$align,$align,$align,$align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $images;

	print_table($table);
	
	echo '<center><input type="submit" name="manyActivities" value="'.get_string('blendedPassSelectedToQuiz','blended').'"/></center>';
	echo "</form>";
   }
	return;
}
/**
 * Construct a table view of all results related to a quiz
 * 
 */
function show_quiz_results_table($blended,$quiz,$course,$context)
{
	global $CFG;
	$currentpage = 'quizJob.php';

	if (!$images_view = get_records($table="blended_images",  $field='jobid', $value=$jobid, $sort='activitycode,pageindex'))
	{
	print_box(get_string('ErrorScannedImageNotFound','blended'));
	}
	
	else
	{
	
	$i = 0;
	$images=array();
	$displayed_codes = array();
$iconWarning='<img src="images/warning.png" width="32" alt="'.get_string('MarkWarning', 'blended').'"/>';
echo "<form action=\"evaluate.php\" method=\"POST\" >";
echo '<input type="hidden" name="a" value="'.$blended->id.'"/>';
echo '<input type="hidden" name="jobid" value="'.$jobid.'"/>';

	foreach ($images_view as $result)
	{
		$acode=$result->activitycode;
		$imgout=$result->imgout;
		$pageindex = $result -> pageindex;
	
		if ($acode)
		{
		$quiz_already_displayed = in_array($acode,$displayed_codes);
		
		if ($quiz_already_displayed == false)
		{
		
		$imagerow=array();

		$id_member = find_userid($acode,$jobid);
		$user_reg = blended_get_user($id_member,$blended);
	
		$verresultados = get_string('modulename','quiz').$acode; 
		$quiz_in_course = check_quiz_course($acode,$course);	 
		$showdetailslink="<a href=\"showdetails.php?&a=$blended->id&acode=$acode&jobid=$result->jobid\">$verresultados</a>";
		$attempts=get_record('blended_attempts', 'id',$acode);
		
		$index=$i+1;
		if ($user_reg!=null && $attempts!=false)
			$checkbox ="<input type=\"checkbox\" name=\"selectedActivity$index\" value=\"$acode\" />";
		else
			$checkbox="<input type=\"checkbox\" name=\"selectedActivity$index\" disabled=\"true\" value=\"$acode\" />";
		$imagerow["index"]=$checkbox.$index;
		$imagerow["correctionlink"]= $showdetailslink;
		$imagerow["activitycode"]= $result->activitycode.get_string('page','blended').$pageindex;
		$imagerow["alumno"] = '';

		if ($user_reg !== null)
			$imagerow["alumno"]=print_user_picture($user_reg,$course->id,null,null,true). fullname($user_reg);
			else
			$imagerow["alumno"]=get_string('ErrorUserIDEmpty','blended');
		$imagerow["pasar"]='';
		$imagerow["status"]='';
		$status='';
		
		$warnings=count_doubtfull_marks($acode,$jobid);
			if ($warnings>0)
			{
				$warn=$iconWarning."Hay $warnings marcas dudosas!!";
			}
			else
			{
				$warn='';
			}
			
		$imagerow["activitycode"]=$warn.'<br/>'.$imagerow["activitycode"];

		if ($quiz_in_course)
		{
			if (has_capability('mod/blended:evaluatequiz', $context))
			{
			if ($result->status==IMAGE_STATUS_PENDING)
				{
				$evaluate=get_string('blendedPassToQuiz','blended');
				$link="<a href=\"evaluate.php?&a=$blended->id&acode=$acode&jobid=$jobid\">$evaluate</a>";
				$imagerow["pasar"]=$link;
				$status=get_string('NotYet','blended');	
				}
			else
				{
				$imagerow["pasar"]=get_string('blendedPassedToQuiz','blended');
				$link="<a href=\"evaluate.php?a=$blended->id&acode=$acode&jobid=$jobid\">".get_string('blendedPassAgainToQuiz','blended')."</a>";
				$status = $link;
				}
			}
			else
			{
			$imagerow["pasar"] = "Permission Denied".
			$status = ""; 
			}
		}
		
		$imagerow["status"]=$status;

		if (has_capability('mod/blended:deletequiz', $context))
		{
		$imgdelete = "<img src=\"delete.gif\"/>";
		$link =  "<a href=\"delete_quiz.php?page=$currentpage&acode=$acode&jobid=$jobid&a=$blended->id\">$imgdelete</a>";
		$delete = $link;
		$imagerow["delete"]=$delete;
		}
		
		if ($attempts==false)
		{ // attempt not found in blended.
		/*	echo"<center>";
			mtrace("El cuestionario que ha procesado pertenece a otro curso o no existe en la base de datos de este servidor.");
			echo"<center><br><br>";*/
			$scan=blended_getOMRScanJob($result->jobid);
			$resultname=$scan->scan_name."-".($result->id);
			
			$correctionlink="<a href=\"activitycode.php?&a=$blended->id&jobid=$result->jobid&resultid=$result->id\">$resultname</a>";
			$imagerow["correctionlink"]= $correctionlink;
			//$imagerow["correctionlink"]='';
			$imagerow["activitycode"]= "Erróneo.".$result->activitycode.' '.get_string('pages','blended').$pageindex;
			$imagerow["alumno"] = '';
			$imagerow["pasar"] = '<img align="left" src="images/warning.png" width="32"/>'.get_string('ErrorActivityCodeNotFound','blended',$acode);
			$status = "";
		
			$key="$acode$pageindex";
			$images[$key]=$imagerow;	
		}
		else 
		{
			$displayed_codes[$acode]=$acode;// avoid using another rows for different pages of the same activity
			$images[$acode]=$imagerow;
		}
			$i++;
		}
		else // complement page set
		{
			$images[$acode]["activitycode"].=", ".$pageindex;
		}
	
	} // $acode!=null

	}
	echo '<input type="hidden" name="numActivities" value="'.$index.'" />';

	/**
	*  Add results with no $acode detected
	***/
	foreach ($images_view as $result)
	{
		$scan=blended_getOMRScanJob($result->jobid);
		$acode=$result->activitycode;
		$resultname=$scan->scan_name."-".($result->id);
		
		$page = $result -> page;
		
		if ($acode==null)
		{	
			
		$imagerow=array();
		
		$imagerow["index"]="";
		
		$correctionlink="<a href=\"activitycode.php?&a=$blended->id&jobid=$result->jobid&resultid=$result->id\">$resultname</a>";
		$imagerow["correctionlink"]= $correctionlink;
		$imagerow["activitycode"]= get_string('UnclassifiedPage','blended');
		$imagerow["alumno"]='';
		$imagerow["pasar"]='';
		$imagerow["status"]=get_string('NotYet','blended');

		if (has_capability('mod/blended:deletescanjob', $context))
		{
		$link =  "<a href=\"delete_quiz.php?page=$currentpage&acode=$acode&jobid=$jobid&a=$blended->id\">$imgdelete</a>";
		$delete = $link;
		
		$imagerow["delete"]=$delete;
		}
		
		$images[]=$imagerow;
		$i++;
	}

	}
	//tabla de resultados
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array('Seleccionar','Ver Resultados','ActivityCode','Alumno','Pasar a QUIZ','Evaluado en Moodle','Borrar');
	$align = "left";
	$table->align = array ($align,$align,$align,$align,$align,$align,$align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $images;

	print_table($table);
	
	echo '<center><input type="submit" name="manyActivities" value="'.get_string('blendedPassSelectedToQuiz','blended').'"/></center>';
	echo "</form>";
   }
	return;
}	

function show_details_table($message)
{
	
	$details=array();
	$detailsrow=array();
		
	$detailsrow["form"] = $message;
	$detailsrow["img"]= "";

	$details[]=$detailsrow;
	
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array(get_string('reviewdetailspagedesc','blended'),'Imagen escaneada');
	$align = "center";
	$table->align = array ($align, "left");
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $details;

	print_table($table);	

	
	return;

}

function create_dir_path($filename,$course)
{
	global $CFG;
	
	$pos = strripos($filename,"/");
	$dir = substr($filename,0,$pos);
	
	$path="$CFG->wwwroot/files/index.php?id=$course->id&wdir=%2F".$dir;
	
	return $path;
}


function select_activitycode($activitycode)
{
	 //get_records($table, $field='', $value='', $sort='', $fields='*') 
	$act=get_records($table='blended_images', $field='activitycode', $value='', $sort='', $fields='*');
	return $act;
}


function display_details($mform,$jobid,$acode,$course,$a,$currentpage)
{
	global $CFG;
	show_details_table('');
	
	echo "<table border=1  align=center><tr><td>";
	$text=$mform->display();
	echo "</td><td valign=\"top\" >";
	display_imagesintable($jobid, $acode,$course,$a,$currentpage);
	echo "</td></table>";
	return;
}

function display_imagesintable($jobid,$acode,$course,$a,$currentpage)
{
	global $CFG;

	if(($results=get_records_select($table='blended_images', "jobid='$jobid' AND activitycode='$acode'", $sort='pageindex')) == false)
	{
		error('Error al cargar los trabajos para mostrar');
	}
 	else
 	{
 	blended_display_results_images($results);
 	}
	return;
}
function display_orphan_imageintable($jobid,$resultid)
{
	if(($results=get_records($table='blended_images','id',$resultid)) == false)
	{
		error('Error al cargar los trabajos para mostrar');
	}
 	else
 	{	
 	blended_display_results_images($results);
 	}
	return;
}
/**
 * generate a table with images and a useful magnifying image viewer
 * @param unknown_type $results
 * @param result if true return the contents instead of printing it out
 */
function blended_display_results_images($results, $return=false)
{
		$output= '<table border="0">';
 		foreach ($results as $record)
 		{
 		
 		$src = get_image_src($record->imgout);
 		$pageindex = $record -> pageindex; 			
 	/**
	* Magnifying glass
	*/
$magnifiedImg='<div style="float:right" onmouseover="zoom_on(event,700,990,\''.$src.'\');" onmousemove="zoom_move(event);" onmouseout="zoom_off();"><A HREF="'.$src.'" rel="thumbnail"><img src="'.$src.'" alt="Scanned Quiz" style="padding:0;margin:0;border:0" /></A></div><div style="clear:both;"></div>';
$magnifiedImg3='<div>
<img src="'.$src.'" style="width:700px; height: 990px;" onmouseover="TJPzoom(this);">
</div>';
$img="<A HREF=\"$src\" rel=\"thumbnail\"><img src=\"$src\" width=\"700\" /></A>";

			$img=$magnifiedImg3;
//			$img->fullImage="<img src=\"$src\" height=\"1020\" width=\"800\" />";			
// 			if ($i == 0)
// 			{
 			//print($img0);
/* 			echo "<td><A HREF=\"$CFG->wwwroot/mod/blended/image.php?&a=$a&acode=$acode
 			&jobid=$jobid&navpage=$currentpage&pageindex=$pageindex\" >$img0</td></tr>";*/
 			$output.= "<tr><td>$img</td></tr>";
// 			}
// 			
// 			else
// 			{
// 			
// 			$image2=$img->$name;
 		
/*			echo "</tr><td></td><td><A HREF=\"$CFG->wwwroot/mod/blended/image.php?&a=$a&acode=$acode
 			&jobid=$jobid&navpage=$currentpage&pageindex=$pageindex\" >$image2</A></td>";*/
//			echo "</tr><td></td><td><A HREF=\"$src\" rel=\"thumbnail\">$image2</A></td>";
//			
// 			}
// 			
// 			$i=$i+1;	
 	
 		}//foreach
 		$output.= '</table>';
 		if ($return)
 		 return $output;
 		 else
 		 echo $output;
}
function get_image_src($imgout)
{
	global $CFG;
	
	$pos = strpos($imgout,$CFG->dataroot);
	if ($pos==-1)
	{
		error('Bad image path. Contact administrator.');
	}
	$image=substr($imgout,$pos+strlen($CFG->dataroot));
	
	$filephp="$CFG->wwwroot/file.php/";//$course->id/";
	$src = $filephp.$image;
	return $src;
}

/**
 * 
 * Enter description here ...
 * @param unknown_type $data
 */
function delete_quiz($data)
{
	$acode = $data -> acode;
	
	delete_records($table='blended_results', $field='activitycode',$value = $acode);
	
	delete_records($table='blended_images', $field='activitycode', $value = $acode);
	
	
	// Habilitar únicamente en el caso de que también se quiera borrar la evaluación
	//$uniqueid=get_field($table='blended_attempts', $retvalue='attempt', $field='id',$value = $acode); 
	//delete_records($table='question_states', $field='attempt',$value = $uniqueid);
	//delete_records($table='question_sessions', $field='attemptid',$value = $uniqueid);
	//delete_records($table='quiz_attempts', $field='id',$value = $uniqueid);
	//delete_records($table='question_attempts', $field='id',$value = $uniqueid);
		
	delete_records($table='blended_attempts', $field='id',$value = $acode);
	
	return;
}
/**
 * Remove all kind of results of a scanjob
 * @param unknown_type $data
 */
function blended_delete_scan_job($scan)
{
	$jobid = $scan->id;
	
//	$timestamp = get_timestamp_attempts($scan);
//	if ($timestamp !== null)
//	{
	//delete_records($table='blended_attempts', $field='timestampt',$value = $timestamp);
	$scanjobdir=blended_getOMRTargetPath($scan);
	
	// Remove results file
	blended_delete_log_file($scan);
	/**
	 * Remove previous result records.
	 */
	delete_records($table='blended_results', $field='jobid',$value = $jobid);
	blended_delete_scan_results($scan);
	// remove unregistered files in this directory
	$mask = $scanjobdir.'/omr_result[*].txt';
   	array_map( "unlink", glob( $mask ) );
   	debugging("Deleted all omr_result[*].txt");
   	
	delete_records($table='blended_images', $field='jobid', $value = $jobid);

	/**
	 * Remove debug folder
	 */
	$debugdir=$scanjobdir.'/output';
	if (file_exists($debugdir))
	{
		
	$mask = $debugdir.'/*.*';
   	array_map( "unlink", glob( $mask ) );
	rmdir($debugdir);
	debugging("Removed debug files.");
	}
	else 
	{
		debugging("There is no $debugdir to remove.");
	}
//	}
	/**
	 * remove records of the scanjob
	 */
	delete_records($table='blended_scans', $field='id', $value = $jobid);
	return;
}
/**
 * 
 * Delete logfile log.txt from outputscandir
 * @param unknown_type $scan
 */
function blended_delete_log_file($scan)
{
	$logFilePath= blended_getOMRInputLogFilePath($scan);
	
	if (file_exists($logFilePath))
	{
		debugging("<p>Removing $logFilePath</p>");
		unlink($logFilePath);
	}
}
function blended_delete_scan_results($scan)
{
	/**
	 * Remove output images and results
	 */
	$images= get_records('blended_images','jobid',$scan->id);
	if ($images)
		blended_cleanupImageResults($images);
	delete_records('blended_images','jobid',$scan->id);
}
/**
 * 
 * Remove images, omr results files
 * @param unknown_type $images
 */
function blended_cleanupImageResults($images)
{
foreach($images as $image)
	{
		if (file_exists($image->imgout))
		{
		debugging("<p>Removing $image->imgout</p>");
		unlink($image->imgout);
		}
		if (file_exists($image->results))
		{
			debugging("<p>Removing $image->results</p>");
			unlink($image->results);
		}
	}
}
/**
 * 
 * Remove registered results of a sccanjob and an activity
 * @param unknown_type $data
 */
function delete_scan_result($scan,$acode)
{
	$jobid = $scan->id;
	/**
	 * Remove previous result records.
	 */
	delete_records($table='blended_results', $field='jobid',$value = $jobid,'activitycode',$acode);
	/**
	 * Remove output images
	 */
	$images= get_records('blended_images','jobid',$jobid,'activitycode',$acode);
	
	foreach($images as $image)
	{
		debugging("Removing $image->imgout");
		unlink($image->imgout);
	}
	delete_records($table='blended_images', $field='jobid', $value = $jobid,'activitycode',$acode);
//	}
}
function delete_image($data)
{
	$imgout = $data -> imgout;
	$jobid = $data -> jobid;

	delete_records($table='blended_images', $field='imgout', $value = $imgout, $field2='jobid', $value2=$jobid);
	
	return;
}
/**
 * 
 *Devuelve el timestamp de la primera imagen que corresponde a un quiz
 *¿¿¿¿¿Para que es esto???
 * @param unknown_type $data
 */
function get_timestamp_attempts($data)
{
	$jobid = $data->id;

	if($records = get_records ($table='blended_images', $field='jobid',$value = $jobid))
	{
		foreach ($records as $record)
		{
			if (($record -> activitycode) !== 0)
			{
				$acode = $record -> activitycode;
				break;
			}
		}
		$timestamp = get_field($table='blended_attempts',$retvalue = 'timestamp', $field='id',$value = $acode);
	}
	else
	{//print 'else';
		$timestamp = null;
	}
	//print "devuelvo: ".$timestamp;
	return $timestamp;
}

function is_owner ($acode = 0, $jobid = 0)
{
	global $USER;
	
	$userid = $USER -> id;
	$owner = 0;
	
	if ($acode !== 0)
	{
		$useridvalue = get_field($table='blended_attempts', $retvalue = 'userid', $field='id', $value=$acode);
				
		if ($userid == $useridvalue)
		$owner = 1;

	}
	
	if ($jobid !== 0)
	{
		$useridvalue = get_field($table='blended_scans', $retvalue = 'userid', $field='id', $value=$jobid);
		
		if ($userid == $useridvalue)
		$owner = 1;
		
	}
	
	return $owner;
}
/**
 * 
 * get a scan jobname
 * @param unknown_type $jobid
 */
function get_jobname($scanid)
{
	$jobname = get_field($table='blended_scans', $retvalue = 'scan_name', $field='id', $value=$scanid);
		
	return $jobname;
}
/**
 * Load a scanjob record
 */
function blended_getOMRScanJob($scanjobid)
{
	return get_record('blended_scans', 'id', $scanjobid);
}
/**
 * URL for browsing files
 * @param unknown_type $filename
 * @param unknown_type $courseid
 */
function create_url($filename,$courseid)
{
	global $CFG;
	
	$pos = strripos($filename,"/");
	$dir = substr($filename,0,$pos);
	
	$dir = urlencode($dir);
		
	$url="$CFG->wwwroot/files/index.php?id=$courseid&wdir=%2F$dir";
	
	return $url;
}

function get_status_message ($jobid)
{
	$message = get_field($table = 'blended_scans', $return = 'infostatus', $field = 'id', $value = $jobid);
	
	return $message;
}
function get_details_message ($jobid)
{
	$message = get_field($table = 'blended_scans', $return = 'infodetails', $field = 'id', $value = $jobid);
	
	return $message;
}

function show_status_message($jobid, $message, $context, $blended, $page)
{
	$status=array();
	
	$statusrow["jobid"] = $jobid;
	$statusrow["message"] = $message;
	
	if (has_capability('mod/blended:viewstatusdetails', $context))
		{
			$detalles = 'Detalles';
			$detailslink = "<a href=\"statusdetails.php?page=$page&a=$blended->id&jobid=$jobid\">$detalles</a>";
			
		
			if(get_details_message($jobid))
			{
				$statusrow["details"]= $detailslink;
			}
		
			else
				$statusrow["details"]= "No hay detalles para mostrar";
		
		}
	else
		{
			$statusrow["details"]= "No tiene suficientes permisos para ver los detalles del estado.";
		}

	$status[0] = $statusrow;
	//tabla de resultados
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array('Job ID','Mensaje de Estado','Detalles');
	$align = "left";
	$table->align = array ($align,'center',$align);
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $status;
	print_table($table);
	return;
}

function show_details_message($message)
{
	$status=array();
	
	$statusrow["message"] = $message;
	
	$status[0] = $statusrow;
	
	//tabla de resultados
	$table = new stdClass();
	$table->class = 'mytable';
	$table->head  = array('Mensaje detallado de Estado');
	$align = "left";
	$table->align = array ('center');
	$tablealign = "center";
	$table->tablealign = $tablealign;
	$table->rowclasses = array();
	$table->data = $status;

	print_table($table);
	return;
}

function check_quiz_course($acode,$course)
{
	
	$checkvalue = 0;
	
	$quizid = get_field('blended_attempts','quiz','id',$acode);
	
	$courseid = get_field('quiz','course','id',$quizid);

	if ($courseid == $course->id)
		return true;
	else
		return false;
}

function create_image_url($imgpath,$course)
{
	global $CFG;
	 
	$blendeddir = $CFG->wwwroot."/file.php/".$course->id."/moddata/blended/";
	//print "<BR> Esto es blendeddir: ".$blendeddir;
	
	$pos = strpos($imgpath, "blended");
	$relative_path = substr($imgpath,$pos+8);
	//print '<BR> Esto es relativepath: '.$relative_path;
	
	$pos2 = strpos($relative_path,'/');
	$dir = substr($relative_path,0,$pos2);
	$pathend = substr($relative_path,$pos2);
	//print '<BR>'.$dir;
	
	$encodeddir = rawurlencode($dir);
		
	$url=$blendeddir.$encodeddir.$pathend;
	
	return $url;
}
?>
