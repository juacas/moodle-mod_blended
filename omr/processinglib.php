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

require_once("omrlib.php");
require_once("recognitionprocess.php");
require_once('correctionlib.php');


/**
 * REmove previous log.txt, omr_result[*].txt
 * Enter description here ...
 * @param record $scan @see table blended_scans
 * @throws OMRError
 */
function omrprocess($scan)
{
	global $CFG;
	global $scansfoldername;
	
	mtrace("Entering OMR phase.");	
	$scan->status= JOB_STATE_BUSY;
	set_field('blended_scans','status',$scan->status,'id',$scan->id);
	$scan->timestatus = time();
	set_field('blended_scans','timestatus',$scan->timestatus,'id',$scan->id);
	//update_record('blended_scans', $scan);
	
	
	$strresultlink	= get_string('resultlink','blended');
	
	
	$rutaorigen=blended_getOMRSourcePath($scan);

	$rutafieldset = blended_getOMRFieldsetDir($scan);
	$rutadestino = blended_getOMRTargetPath($scan);
	$logfile = blended_getOMRInputLogFilePath($scan);
	
	mtrace("Processing Scan: $scan->scan_name");
	mtrace("     located at: $rutaorigen");	
	mtrace("    fieldset at: $rutafieldset");
	mtrace("      target at: $rutadestino \n");
try{
	
/**
 * Clean previous results in case of a previous error and a relaunch
 */	
	blended_delete_log_file($scan);
	blended_delete_scan_results($scan);
/**
 * Execute the recognition process
 */
	list($status,$output)=blended_execute_omr_recognition($rutaorigen,$rutadestino,$rutafieldset);
	mtrace ("Command executed. Status Code =");
	print_r($status);
	if ($status!='OK')
	{		
		$error=new OMRError($status,OMRError::OMRPROCESS_FAILED);
		throw $error;	
	}
	else
	{
		$statusMsg = "Command executed. Status Code = 0";
		print_object($output);
		
		register_status($scan->id, $statusMsg,$output,JOB_STATE_FINISHED);
		//register_scannedjob($rutascandir,$jobid,$scan->course);
		$scan->status=JOB_STATE_FINISHED;
		register_scannedjob($scan);
	}
	
} catch (OMRError $e)
{
	mtrace ("Fatal OMR Error: ".$e->getMessage());
	
	register_exception ($e,$scan->id);

}	
catch( ResultsError $e)
{
	mtrace ("ResultsError: ".$e->getMessage());		

	register_exception ($e,$scan->id);

}	

return;
}

/**
 * Execute the recognition process
 * @return list($status,$output) $status is "OK" if no error
 */
function  blended_execute_omr_recognition($rutaorigen,$rutadestino,$rutafieldset)
{
global $CFG;
$commandpath = $CFG->blended_omr_path;
$params = " -i \"$rutaorigen\" -o \"$rutadestino\" -id1 USERID -d \"$rutafieldset\"";

$OmrCommand = " $commandpath $params 2>&1"; // redirect error output  for capturing it
if (PHP_OS=='Linux' || PHP_OS=='Darwin')
{
$OmrCommand = "LANG=\"en_US.UTF8\" ". $OmrCommand;
}

// Si la cadena contiene espacios no funciona el comando, sustituímos los espacios por el caracter ?.
//$OmrCommand = str_replace(" ","?",$OmrCommand);

mtrace("Executing $OmrCommand\n");

$output = array();
$status = array ();
	
exec($OmrCommand,$output,$system_error);
mtrace( "System error code $system_error\n");
//mtrace('Command done. Status='.$system_error);
if ($system_error != 0)
	{
		if ($system_error == 126)
		{
		$status[]="El módulo está mal configurado, contacte con su administrador";	
			mtrace("Maybe you have forgotten to set execution permissions to the command: $commandpath ?");
		}
		else
		{
		$status[]="Ha ocurrido un error en el reconocimiento. Contacte con su administrador";
		
		mtrace("ERROR: output was:");
		mtrace("==========================================");
		print_r($output);
		mtrace("==========================================");
		}
	}
	else
	{ // OK
	$status="OK";	
	}
return array($status,$output);
}
/**
Process Job's result as logged in a text file.
*/
//function register_scannedjob($rutascandir,$jobid,$courseid)
/**
 * Process Job's results logged in a text file.
 * 
 * @param array $scan from table blended_scans
 */
function register_scannedjob($scan)
{
	global $CFG;
	global $scansfoldername;
	$jobid = $scan->id;
	$fieldspath=blended_getOMRFieldsetDir($scan);
	$logfile = blended_getOMRInputLogFilePath($scan);   

	try{
		if ($logfile!='null')
			$logelements=read_log_file($logfile);
		if (!isset($logelements) || count($logelements) == 0)
		{
		throw new OMRError("Log file is empty",OMRError::LOG_FILE_IS_EMPTY);
		}
	}catch (OMRError $e)
	{
		throw $e;
	}
	
	// open a transaction
	begin_sql();
	
	foreach ($logelements as $logelement)
	{
		try
		{
			//cada elemento es un registro de blended_images.
			
			$image_result=parse_log_elements($logelement);
			$image_result->jobid=$jobid;
				
			register_image($image_result);
			$acode=$image_result->activitycode;
			if ($acode!= null)
			if ($acode=='Undetected')
				{
				mtrace("Undetected activity code for result:".$logelement);
				}else
				{
				mtrace('<br>REGISTERING FIELDS...');
				register_template_fields($image_result,$fieldspath);
				mtrace('<br>REGISTERING RESULTS...');
				register_result_files($image_result,$fieldspath);
				mtrace('<br>CHECKING VALIDITY...');
				check_invalid_results($image_result);
				}	
		}
		//catch (OMRError $e)
		catch (Exception $e)
		{
			mtrace ('OMRError: '.$e->getMessage());
		
			register_exception($e,$jobid);
			
			$errorcode = $e -> getCode(); 
			if ($errorcode == 5 or $errorcode == 6)
			{
				//print_object($e);
				//throw $e;
				continue; // process next result
			}
		}
	
	}
	mtrace('<br>UPDATING SCANJOB QUEUE...');
	update_record('blended_scans', $scan);
	// End the transaction
	commit_sql();
	return;
}

/**
*Parse logfile and process each line in this logging block.
*Example:
*
*SourceFile=zip:/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/JobName.zip#JobName/JobName (14).jpg
*PageIndex=2
*OutputImagePath=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/OMR_original_marked4098364334116568820.jpg
*ActivityCode=749
*ParsedResults=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/omr_result[749].txt
*@return array 
*	$element->imgsrc
*	$element->index = $indice;
*	$element->pagenumber= $indice;
*	$element->imgout
*	$element->results // parsed results
*	$element->activitycode
*	$element->pagenumber
*/
function parse_log_elements($logelement)
{
	$elementarray=explode("\n",$logelement);
	
	$element = new stdClass();
	
	$source="SourceFile=";
	$index="PageIndex=";
	$output="OutputImagePath=";
	$resultado="ParsedResults=";
	$estado="ResultCode=";
	$activitycode="ActivityCode=";
	$page="PageNumber=";
	
	//print_object($elementarray);
	foreach ($elementarray as $singledata)
	{
		
		if (($origen = find_element($singledata,$source)) !== 0)
		{
		$element->imgsrc = $origen;
		// print_object ($element);
		}
		
		
		if (($indice = find_element($singledata,$index)) !== 0)
		{
			$element->index = $indice;
			$element->pagenumber= $indice; // TODO check if this is redundant
		}

		if (($imgdst = find_element($singledata,$output)) !== 0)
		{
			$element->imgout = $imgdst;
		}
		
	if (($result = find_element($singledata,$resultado)) !== 0)
		{
			$element->results = $result;
		}
	
	//if (($status = find_element($singledata,$estado)) !== 0)
		//{
			//$element->status = $status;
		//}
		
	if (($actcode = find_element($singledata,$activitycode)) !== 0)
		{
		$element->activitycode = $actcode;
		}
		//else
		//$element->activitycode = 0;
	
	if (($pagenumber = find_element($singledata,$page)) !== 0)
		{
		$element->pagenumber = $pagenumber;
		}
		else
		$element->pagenumber = 0;

	}

	return $element;
}


function find_element($singledata,$text)
{
	//print_object($singledata);
	//echo "llamada a element value ".$text;
		$pos=strpos($singledata,$text);
		if ($pos !== false)
		{	
			$out=substr($singledata,strlen($text));
		}
		else
		{
			$out=0;
		}
	return $out;	
}

/**
Store in the database the output images for reporting.
*/
function register_image($image_result)
{
	global $USER; 
	
	
	$length=strlen($image_result->imgout);
	
	
	if ($length == 0)
	{
		throw new OMRError("No output image. Can't continue the process.",OMRError::NO_OUTPUT_IMAGE);
	}

	$image->userid = $USER->username;
	$image->imgsrc = $image_result->imgsrc;
	$image->pageindex = $image_result->index;
	$image->imgout= $image_result->imgout;
	$image->results=$image_result->results;
	
	$image->page = $image_result->pagenumber;
	$image->status = IMAGE_STATUS_PENDING;
	$image->jobid=$image_result->jobid;
	if ($image_result->activitycode=='Undetected')
	{
		$image->activitycode = null;
	}
	else
	{
		$image->activitycode = $image_result->activitycode;
	}
	mtrace ("\nRegistro la imagen: ".$image->imgout);
	
	//impedimos que haya registros duplicados		
	delete_records($table='blended_images',	
				$f='imgout', $v=$image_result->imgout,
				$f4 = 'activitycode',$v4=$image_result->activitycode,
				$field2='imgsrc', $value2=$image_result->imgsrc,
				$field3='jobid', $value3=$image_result->jobid);
	if (!$image->id = insert_record('blended_images', $image)) 
		{
				throw new ResultsError('Could not create new image record',ResultsError::COULD_NOT_CREATE_IMAGE_RECORD);
		}	

	return $image;
}
/**
* @param $element has the values of the different lines of a Job block
* example:
* $element->jobid
* $element->SourceFile=zip:/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/JobName.zip#JobName/JobName (14).jpg
* $element->PageIndex=2
* $element->OutputImagePath=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/OMR_original_marked4098364334116568820.jpg
* $element->activitycode=749
* $element->ParsedResults=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/omr_result[749].txt
*/
function register_template_fields($element,$fieldspath)
{
	$field = new stdClass();
	$activitycode = $element->activitycode;
	$jobid = $element -> jobid;
	
	//avoid previous records
	delete_records($table = 'blended_results', $field1 = 'activitycode', $value1 = $element->activitycode,
	$field2 = 'jobid', $value2 = $jobid);
	
	$fieldsfile=$fieldspath."/fieldset_OMR[$activitycode].fields";
	
	// array, cada elemento es una página del fieldset.
	try{
	
	$telements=read_templatefield_file($fieldsfile);
	if (count($telements) == 0)
		{
		throw new OMRError("Templatefield file is empty",OMRError::FIELDS_FILE_IS_EMPTY);
		}
	}catch (OMRError $e)
	{
		throw $e;
	}
		//print_object($telements);
			
	//cada fieldelement es una página del archivo fieldset
	//JPC: WARNING: May not be some pages in the log file (i.e. unrecongnized labels)
	foreach ($telements as $fieldelement)
	{
		if (substr($fieldelement, 0,10)=='TemplateId')
			continue; // ignore field outside page definition
			
		$labels=analyse_fields_elements($fieldelement);
		//print "labels: ";
		//print_object($labels);
		
		$field->activitycode = $activitycode;
		$field->jobid = $jobid;
		$field->page=$labels['PageNumber']->value;
		
		try{
		register_fields($field,$labels);
		}catch (ResultsError $e)
		{
			print ('ResultsError: '.$e->getMessage().'<BR>');
		}
	}	
	
	return;
}

function analyse_fields_elements($fieldelements)
{
	$label=array();
	$elementarray=explode("\n",$fieldelements);

	$reference="[";
	//print "analizando:";
	//print_object($elementarray);
	
	foreach ($elementarray as $singledata)
	{
	$labeli=find_label($singledata,$reference);
	if($labeli)	
		{
			$label[$labeli->label]=$labeli;
		}

	}
	
	return $label;
}

function find_label($singledata,$reference)
{
	$label = new stdClass();
	//look for page number fragment NNN]
	if (strpos($singledata,']')==strlen($singledata)-1)
	{
		$pagenumber=substr($singledata, 0,strlen($singledata)-1);
		$label->label='PageNumber';
		$label->value=$pagenumber;
		return $label;
	}
	
	$pos=strpos($singledata,$reference);
		if ($pos !== false)
		{	
			$label->label = substr($singledata,0,$pos);
			$label->questiontype = find_questiontype($singledata,$pos);
			return $label;
		}
		else
		{
			return false;
		}
}

function find_questiontype($singledata,$pos)
{
	$reference = "]";
	$endpos = strpos($singledata,$reference);
	
	$substring= substr($singledata,0,$endpos);
	$questiontype = substr($substring,$pos+1);
	 
	return $questiontype;
}

function register_fields($field,$labels)
{
	global $USER;
	$result = new stdClass();
	
	foreach ($labels as $label)
	{
		if ($label->label=="PageNumber")
			continue;
		if ($label->label=="Align")
			continue;
		$result->userid = $USER->id;
		if ($field->activitycode == 'Undetected')
			$result->activitycode=0;
			else
			$result->activitycode = $field->activitycode;
		$result->label = $label-> label;
		$result->questiontype = $label -> questiontype;
		$result->jobid = $field->jobid;
		$result->page = $field->page;
		
	//print_object($result);

	if (!$result->id = insert_record('blended_results', $result)) 
			{
				throw new ResultsError('Could not create new result record',ResultsError::COULD_NOT_CREATE_RESULT_RECORD);
			}	

	}
		
	return;
}

function register_result_files($image_result,$fieldspath)
{
	$resultfile = $image_result->results;
	$acode = $image_result->activitycode;
	try{
		
		mtrace ("Opening for reading: ".$resultfile."\n");
		if ($resultfile !='null')
		{
			$page_elements=read_result_file_pages($resultfile);
		}
		else
		{
		throw new OMRError("Results file is not found.",OMRError::RESULTS_FILE_IS_EMPTY);
		}

		if (count($page_elements) == 0)
		{
		throw new OMRError("Result file ".$resultfile." is empty.",OMRError::RESULTS_FILE_IS_EMPTY);
		}	

	foreach ($page_elements as $resultPageElement)
	{
		$results=analyse_result_page_elements($resultPageElement);
		//print "results: ";
		$resultsarray=(array)($results);
		//$resultsarray=array_slice($array,1);
		//print_object($array);
		
		$image_result->page = $results->PageNumber;
		
		//print_object ($element);
		//cada vez que llamo a esta funcion se registra una pagina completa de resultados	
		register_results($image_result,$resultsarray);
	}
		
	}	
	catch (ResultsError $e)
		{
			//print "He lanzado una excepcion OMRError. ";
			mtrace('ResultsError: '.$e->getMessage().'\n');
			throw $e;
		}
	return;
}

function analyse_result_page_elements($resultelement)
{
	$readings = new stdClass();
	$aux = new stdClass();
	
	$elementarray=explode("\n",$resultelement);

	//print("elemento a analizar:<BR>");
	//print_object($elementarray);
	
	$reference = "=";
	
	foreach ($elementarray as $singledata)
	{
	//look for page number fragment NNN]
	if (strpos($singledata,']')==strlen($singledata)-1)
	{
		$pagenumber=substr($singledata, 0,strlen($singledata)-1);
		$readings->PageNumber=$pagenumber;
		continue;
	}
		if(($singledata))
		{
		$aux = get_grades($singledata,$reference);
	
		if(isset($aux->value))
		{
		$label=$aux->label;
		$value=$aux->value;
		$readings->$label=$value;
		}
		
		}
	}
	
	//print_object($readings);
	return $readings;
}

function get_grades ($singledata,$reference)
{
	$aux=new stdClass();
	
	//print_object($singledata);
	if(($labeli=find_label($singledata,$reference)) !== 0 )	
	//print_object($labeli);
	$label=$labeli->label;
	$aux->label=$label;
	$text=$label.'=';
	
	
	//print $aux->label;
	if (($valuei = find_element($singledata,$text)) !== 0)
	{	
		if($label !== 'USERID' and $label !== 'TEMPLATEFIELD' and $label !== 'Align' and $label !== 'Filename')
		if($valuei == 'true' or $valuei == 'marked' or $valuei == 1)
		{
			$valuei = 1;
		}
		else
		if ($valuei=='?')
			$valuei='?';
		else
			$valuei = 0;

		if ($valuei == null)
			$valuei = 0;
			
	$aux->value=$valuei;
	}
	
	return $aux;
}
/**
 * Store the values recognized in blended_results.
 * Doubtful marks are also stored with invalid=2
 * @param scanjobrecord $scannedpage 
 * @param array $resultarray list of recognized fields in scanjob
 * @throws ResultsError
 */
function register_results($scannedpage, $resultarray)
{
	
	$acode = $scannedpage->activitycode;
	$page = $scannedpage->page;
		
	mtrace ("Registering results for quiz number ".$acode." page [".$page."]...");
	
	foreach ($resultarray as $key=>$result)
	{
		//print ("key:".$key."<BR>");
	
		//mtrace ("Label: ".$key." has result ". $result." with page ".$page. "\n");
		if ($result!='')
		{
			if ((set_field($table='blended_results', 
					$field='value', $value=$result, 
						$field1='label', $value1=$key, 
						$field2='activitycode', $value2=$acode, 
						$field3='page', $value3=$page) == false)) 
						{
							throw new ResultsError('Could not update blended_results',ResultsError::COULD_NOT_UPDATE_BLENDED_RESULTS);
						}
			if ($result=='?')
			{
				set_field($table='blended_results', 
					 	'invalid',2, 
						$field1='label', $value1=$key, 
						$field2='activitycode', $value2=$acode, 
						$field3='page', $value3=$page);
			}
		}
	}
	
	return;
}
/**
Read log.txt and slices it with the [Job] delimiter.
Example:
[Job]
SourceFile=zip:/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/JobName.zip#JobName/JobName (13).jpg
PageIndex=1
OutputImagePath=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/OMR_original_marked1787011610192228588.jpg
ActivityCode=749
ParsedResults=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/omr_result[749].txt
[Job]
SourceFile=zip:/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/JobName.zip#JobName/JobName (14).jpg
PageIndex=2
OutputImagePath=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/OMR_original_marked4098364334116568820.jpg
ActivityCode=749
ParsedResults=/var/WebApps/moodle_prod/moodledata/259/moddata/blended/Folder-2011-10-25-22-30-07-33/Scans/omr_result[749].txt

will return an array with two strings.
*/
function read_log_file($logfile)
{
	$log=read_file($logfile);
	if ($log == -1)
	{
		throw new OMRError("Processinglib: Log file does not exist. OMR process has failed.",OMRError::LOG_FILE_DOES_NOT_EXIST);
	}

	$logarray=explode("[Job]",$log);
	//print_object($logarray);
	$logelements=array_slice($logarray,1);
	//print_object ($logelements);
	return $logelements;
	
}
function read_templatefield_file($fieldsfile)
{
	$template=read_file($fieldsfile);
	if ($template == -1)
	{
		throw new OMRError("Fieldset file does not exist: ".$fieldsfile,OMRError::FIELDS_FILE_DOES_NOT_EXIST);
	}
	$array=explode("[Page",$template);
	//$templateelements=array_slice($templatearray,1);
	return $array;
	
}
function read_result_file_pages($resultfile)
{
	$result=read_file($resultfile);
	if ($result == -1)
	{
		throw new OMRError("Results file does not exist: ".$resultfile,OMRError::RESULTS_FILE_DOES_NOT_EXIST);
	}
	$resarray=explode("[Page",$result);
	$resarray=array_slice($resarray,1);
	return $resarray;
		
}
function read_file($filename)
{
	if(!$handle = fopen($filename, "r"))
	return -1;
	
	$contents = fread($handle, filesize($filename));
	fclose($handle);
	return $contents;
}

function check_invalid_results($element)
{
	$acode = $element -> activitycode;
	$jobid = $element -> jobid;
	
	$labels=get_labels($acode,$jobid);
	
	//layout es un array de preguntas
	$layout=get_layout($acode);
	array_pop($layout);
	
	
	foreach ($layout as $numberofquestion)
	{
		$idqs=select_question($labels,$numberofquestion,$acode);

		$defaults=get_question_values($idqs,$acode,$jobid);

		
		
		if($defaults !== null)
		{
			$sum = array_sum($defaults);
		
			//print('Suma :'.$sum); 
		
		if ($sum > 1)
		{
		foreach ($idqs as $key => $params)
		{
			if(($params -> questiontype == 'CIRCLE'))
			{
			print ('actualizo '.$params->label);		
			set_field($table = 'blended_results', $field = 'invalid', $value = 1, $field2 = 'activitycode',
			$value2 = $acode, $field3 = 'jobid', $value3 = $jobid, $field4 = 'label', $value4 = $params -> label);
			set_field($table = 'blended_results', $field = 'value', $value = 0, $field2 = 'activitycode',
			$value2 = $acode, $field3 = 'jobid', $value3 = $jobid, $field4 = 'label', $value4 = $params -> label);
			}		
		}
		}
		
		}
	}
		
	
	return;
}

function register_status($jobid, $infostatus, $details, $newStatus)
{
		mtrace ("<BR>REGISTERING STATUS...");
		if (is_array($infostatus))
		{
			$infostatus= implode("<BR>",$infostatus);
		}	
		if (is_array($details))
		{
		//	print_object($details);
		//	$detailschunk = array_chunk($details,50);
		//	print_object ($detailschunk);
			$details= implode("<BR>",$details);//chunk[0]);
		}	
		
		$details = addslashes($details);
		
		$scan=get_record('blended_scans', 'id', $jobid);
		$scan->infostatus=$infostatus;
		$scan->infodetails=$details;
		if ($newStatus)
			$scan->status=$newStatus;	
		update_record('blended_scans', $scan);
		
		return;
}

function register_exception(OMRError $e, $jobid)
{
			mtrace ("<BR><BR>REGISTERING EXCEPTION...");
		
			$e->loadInfoStatus($jobid);
			$e->loadDetails($jobid);
			
			$e->addStatus($e->getMessage());
			$e->addDetails($e->getMessage());			
		
			$infostatus = $e->getStatus();
			$details = $e->getDetails();
			
			register_status($jobid,$infostatus,$details,JOB_STATE_IN_ERROR);
			
			return;
}
