<?php

    //This is the "graphical" structure of the blended mod:
    //
    //                   	   	  blended
    //                  	  	(CL,pk->id)             
    //                    	   	 	|
    //                     			|·····································
    //                        		|					   				 |	
    //                   	  blended_jobs				  		  blended_scans
    //                 (UL,pk->id, fk->blended)				 (UL,pk->id, fk->blended)
    //								|						   		     |	
    //                    ··········· 						··························							
    //                    |									|	                     |
    //            blended_attempts                   blended_images           blended_results
    //       (UL,pk->id, fk->attemptid)			 (UL,pk->id, fk->jobid)    (UL,pk->id, fk->jobid)
    //
    //
    // Meaning: pk->primary key field of the table
    //          fk->foreign key to link with parent
    //          nt->nested field (recursive data)
    //          CL->course level info
    //          UL->user level info
    //          files->table may have files)
    //
    //-----------------------------------------------------------

require_once("$CFG->dirroot/mod/quiz/backuplib.php");


//This php script contains all the stuff to backup/restore
    //blended mods
    
function blended_backup_mods($bf,$preferences) {

        global $CFG;

        $status = true;

        //Iterate over blended table
        $blendeds = get_records ("blended","course",$preferences->backup_course,"id");
        if ($blendeds) {
            foreach ($$blendeds as $blended) {
                if (backup_mod_selected($preferences,'blended',$blended->id)) {
                    $status = blended_backup_one_mod($bf,$preferences,$blended);
                    // backup files happens in backup_one_mod now too.
                }
            }
        }
        return $status;  
    }

function blended_backup_one_mod($bf,$preferences,$blended) {
        
        global $CFG;
    
        if (is_numeric($blended)) {
            $blended = get_record('blended','id',$blended);
        }
    
        $status = true;

        //Start mod
        fwrite ($bf,start_tag("MOD",3,true));
        //Print blended data
        fwrite ($bf,full_tag("ID",4,false,$blended->id));
        fwrite ($bf,full_tag("MODTYPE",4,false,"blended"));
        fwrite ($bf,full_tag("NAME",4,false,$blended->name));
        fwrite ($bf,full_tag("DESCRIPTION",4,false,$blended->description));
        fwrite ($bf,full_tag("IDMETHOD",4,false,$blended->idmethod));
        fwrite ($bf,full_tag("IDTYPE",4,false,$blended->idtype));
        fwrite ($bf,full_tag("CODEBARTYPE",4,false,$blended->codebartype));
        fwrite ($bf,full_tag("LENGTHUSERINFO",4,false,$blended->lengthuserinfo));
        fwrite ($bf,full_tag("TEAMMETHOD",4,false,$blended->teammethod));
        fwrite ($bf,full_tag("NUMTEAMS",4,false,$blended->numteams));
        fwrite ($bf,full_tag("NUMMEMBERS",4,false,$blended->nummembers));
        fwrite ($bf,full_tag("ASSIGNMENT",4,false,$blended->assignment));
        fwrite ($bf,full_tag("RANDOMKEY",4,false,$blended->randomkey));
    

        //if we've selected to backup users info, then execute backup_blended_jobs and
        //backup_blended_scans
        if (backup_userdata_selected($preferences,'blended',$blendedt->id)) {
            $status = backup_blended_jobs($bf,$preferences,$blended);
            if ($status) {
                $status = backup_blended_scans($bf,$preferences,$blended->id);
            }
  
            //Si se quiere guardar el quiz_attempts, question_session y question_states en algún momento habría
            //que hacer una llamada a backup_quiz_attempts($bf,$preferences,$quiz); para cada $quiz.
        }
        //End mod
        $status =fwrite ($bf,end_tag("MOD",3,true));

        return $status;
    }
    
function backup_blended_jobs ($bf,$preferences,$blended) 
    {

        global $CFG;

        $status = true;

        $blended_jobs = get_records("blended_jobs","blended",$blended->id,"id");
        
        //If there is blended_jobs
        if ($blended_jobs) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_JOBS",4,true));

            //Iterate over each blended_job
            foreach ($blended_jobs as $blended_job) {
                
            	//Start blended_job
                $status =fwrite ($bf,start_tag("BLENDED_JOB",5,true));
                //Print blended_jobcontents
                fwrite ($bf,full_tag("ID",6,false,$blended_job->id));
                fwrite ($bf,full_tag("BLENDED",6,false,$blended_job->blended));
                fwrite ($bf,full_tag("QUIZ",6,false,$blended_job->quiz));
                fwrite ($bf,full_tag("QUIZ_NAME",6,false,$blended_job->quiz_name));
                fwrite ($bf,full_tag("USERID",6,false,$blended_job->userid));
                fwrite ($bf,full_tag("ATTEMPT_ID",6,false,$blended_job->attempt_id));
                fwrite ($bf,full_tag("TIMESTAMP",6,false,$blended_job->timestamp));
                fwrite ($bf,full_tag("STATUS",6,false,$blended_job->status));
               
                
                $status = backup_blended_attempts($bf,$preferences,$blended_job->attempt_id);
                
                
                //End blended_job
                $status =fwrite ($bf,end_tag("BLENDED_JOB",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_JOBS",4,true));
        }
        
        return $status;
}
    
function backup_blended_attempts ($bf,$preferences,$acode) 
    {

        global $CFG;

        $status = true;

        $blended_attempts = get_records("blended_attempts","id",$acode,"id");
        
        //If there is blended_attempts
        if ($blended_attempts) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_ATTEMPTS",6,true));

            //Iterate over each blended_attempt
            foreach ($blended_attempts as $blended_attempt) {
                
            	//Start blended_attempt
                $status =fwrite ($bf,start_tag("BLENDED_ATTEMPT",7,true));
                //Print blended_attempt contents
                fwrite ($bf,full_tag("ID",8,false,$blended_attempt->id));
                fwrite ($bf,full_tag("ATTEMPT",8,false,$blended_attempt->attempt));
                fwrite ($bf,full_tag("QUIZ",8,false,$blended_attempt->quiz));
                fwrite ($bf,full_tag("USERID",8,false,$blended_attempt->userid));
                fwrite ($bf,full_tag("LAYOUT",8,false,$blended_attempt->layout));
                fwrite ($bf,full_tag("STATUS",8,false,$blended_attempt->status));
                fwrite ($bf,full_tag("TIMESTAMP",8,false,$blended_attempt->timestamp));
                
                //End blended_attempt
                $status =fwrite ($bf,end_tag("BLENDED_ATTEMPT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_ATTEMPTS",6,true));
        }
        
        return $status;
    }
    
function backup_blended_scans ($bf,$preferences,$blended) 
    {

        global $CFG;

        $status = true;

        $blended_scans = get_records("blended_scans","blended",$blended->id,"id");
        
        //If there is blended_scans
        if ($blended_scan) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_SCANS",4,true));

            //Iterate over each blended_scan
            foreach ($blended_scans as $blended_scan) {
                
            	//Start blended_scan
                $status =fwrite ($bf,start_tag("BLENDED_SCAN",5,true));
                //Print blended_jscan contents
                fwrite ($bf,full_tag("ID",6,false,$blended_scan->id));
                fwrite ($bf,full_tag("BLENDED",6,false,$blended_job->BLENDED));
                fwrite ($bf,full_tag("SCAN_NAME",6,false,$blended_scan->scan_name));
                fwrite ($bf,full_tag("USERID",6,false,$blended_scan->userid));
                fwrite ($bf,full_tag("TIMESTAMP",6,false,$blended_scan->timestamp));
                fwrite ($bf,full_tag("STATUS",6,false,$blended_scan->status));
                fwrite ($bf,full_tag("COURSE",6,false,$blended_scan->course));
                fwrite ($bf,full_tag("TIMESTATUS",6,false,$blended_scan->timestatus));
                fwrite ($bf,full_tag("INFOSTATUS",6,false,$blended_scan->infostatus));
                fwrite ($bf,full_tag("INFODETAILS",6,false,$blended_scan->infodetails));
               
                
                $status = backup_blended_images($bf,$preferences,$blended_scan->attempt_id);
                
                $status = backup_blended_results($bf,$preferences,$blended_scan->attempt_id);
                
                //End blended_scan
                $status =fwrite ($bf,end_tag("BLENDED_SCAN",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_SCANS",4,true));
        }
        
        return $status;
}   

function backup_blended_images ($bf,$preferences,$jobid) 
    {

        global $CFG;

        $status = true;

        $blended_images = get_records("blended_images","jobid",$jobid,"id");
        
        //If there is blended_images
        if ($blended_images) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_IMAGES",6,true));

            //Iterate over each blended_image
            foreach ($blended_images as $blended_image) {
                
            	//Start blended_image
                $status =fwrite ($bf,start_tag("BLENDED_IMAGE",7,true));
                //Print blended_image contents
                fwrite ($bf,full_tag("ID",8,false,$blended_image->id));
                fwrite ($bf,full_tag("JOBID",8,false,$blended_image->jobid));
                fwrite ($bf,full_tag("IMGSRC",8,false,$blended_image->imgsrc));
                fwrite ($bf,full_tag("PAGEINDEX",8,false,$blended_image->pageindex));
                fwrite ($bf,full_tag("IMGOUT",8,false,$blended_image->imgout));
                fwrite ($bf,full_tag("RESULTS",8,false,$blended_image->results));
                fwrite ($bf,full_tag("USERID",8,false,$blended_image->userid));
                fwrite ($bf,full_tag("PAGE",8,false,$blended_image->page));
                fwrite ($bf,full_tag("STATUS",8,false,$blended_image->status));
                fwrite ($bf,full_tag("ACTIVITYCODE",8,false,$blended_image->activitycode));
                
                //End blended_image
                $status =fwrite ($bf,end_tag("BLENDED_IMAGE",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_IMAGES",6,true));
        }
        
        return $status;
    }
    
function backup_blended_results ($bf,$preferences,$jobid) 
    {

        global $CFG;

        $status = true;

        $blended_results = get_records("blended_results","jobid",$jobid,"id");
        
        //If there is blended_results
        if ($blended_results) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_RESULTS",6,true));

            //Iterate over each blended_result
            foreach ($blended_results as $blended_result) {
                
            	//Start blended_result
                $status =fwrite ($bf,start_tag("BLENDED_RESULT",7,true));
                //Print blended_result contents
                fwrite ($bf,full_tag("ID",8,false,$blended_result->id));
                fwrite ($bf,full_tag("JOBID",8,false,$blended_result->jobid));
                fwrite ($bf,full_tag("USERID",8,false,$blended_result->userid));
                fwrite ($bf,full_tag("LABEL",8,false,$blended_result->label));
                fwrite ($bf,full_tag("VALUE",8,false,$blended_result->value));
                fwrite ($bf,full_tag("QUESTIONTYPE",8,false,$blended_result->questiontype));
                fwrite ($bf,full_tag("INVALID",8,false,$blended_result->invalid));                 
                fwrite ($bf,full_tag("PAGE",8,false,$blended_result->page));
                fwrite ($bf,full_tag("ACTIVITYCODE",8,false,$blended_result->activitycode));
                
                //End blended_result
                $status =fwrite ($bf,end_tag("BLENDED_RESULT",7,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_RESULTS",6,true));
        }
        
        return $status;
    }
    
function backup_blended_grade($bf,$preferences,$assignment) 
    {

        global $CFG;

        $status = true;

        $blended_grades = get_records("blended_grade","itemid",$assignmnent->id,"id");
        
        //If there is blended_grades
        if ($blended_grades) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_GRADES",4,true));

            //Iterate over each blended_grade
            foreach ($blended_grades as $blended_grade) {
                
            	//Start blended_grade
                $status =fwrite ($bf,start_tag("BLENDED_GRADE",5,true));
                //Print blended_gradecontents
                fwrite ($bf,full_tag("ID",6,false,$blended_grade->id));
                fwrite ($bf,full_tag("ITEMID",6,false,$blended_grade->itemid));
                fwrite ($bf,full_tag("ITEMID",6,false,$blended_grade->itemid));
                fwrite ($bf,full_tag("ID_TEAM",6,false,$blended_grade->id_team));
                fwrite ($bf,full_tag("GRADE",6,false,$blended_grade->grade));
                fwrite ($bf,full_tag("REWRITE",6,false,$blended_grade->rewrite));
              
                //End blended_grade
                $status =fwrite ($bf,end_tag("BLENDED_GRADE",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_GRADES",4,true));
        }
        
        return $status;
}
    
function backup_blended_team ($bf,$preferences,$assignment) 
    {

        global $CFG;

        $status = true;

        $blended_teams = get_records("blended_team","itemid",$assignmnent->id,"id");
        
        //If there is blended_teams
        if ($blended_teams) {

            //Write start tag
            $status =fwrite ($bf,start_tag("BLENDED_TEAMS",4,true));

            //Iterate over each blended_team
            foreach ($blended_teams as $blended_team) {
                
            	//Start blended_team
                $status =fwrite ($bf,start_tag("BLENDED_TEAM",5,true));
                //Print blended_team contents
                fwrite ($bf,full_tag("ID",6,false,$blended_team->id));
                fwrite ($bf,full_tag("ID_TEAM",6,false,$blended_team->name_team));
                fwrite ($bf,full_tag("USERID_LEADER",6,false,$blended_team->userid_leader));          
                //End blended_team
                $status =fwrite ($bf,end_tag("BLENDED_TEAM",5,true));
            }
            //Write end tag
            $status =fwrite ($bf,end_tag("BLENDED_TEAMS",4,true));
        }
        
        return $status;
}



    //Backup BLENDED files because we've selected to backup user info
    //and files are user info's level
   function backup_blended_files($bf,$preferences) {

        global $CFG;

        $status = true;

        //First we check to moddata exists and create it as necessary
        //in temp/backup/$backup_code  dir
        $status = check_and_create_moddata_dir($preferences->backup_unique_code);
        //Now copy the questournament dir
        if ($status) {

            if (is_dir($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/blended")) 
            {
            	$dir = "$CFG->dataroot/temp/backup/$preferences->backup_unique_code/moddata/blended";
            	check_dir_exists($dir,true);
            	
                $status = backup_copy_file($CFG->dataroot."/".$preferences->backup_course."/".$CFG->moddata."/blended",
                                           $CFG->dataroot."/temp/backup/".$preferences->backup_unique_code."/moddata/blended");
            }

        }

        return $status;

    }
     function blended_encode_content_links ($content,$preferences) 
    {

        global $CFG;

        $base = preg_quote($CFG->wwwroot,"/");

        //Link to the list of quests
        $buscar="/(".$base."\/mod\/blended\/index.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@BLENDEDINDEX*$2@$',$content);

        //Link to quest view by moduleid
        $buscar="/(".$base."\/mod\/blended\/view.php\?id\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@BLENDEDVIEWBYID*$2@$',$result);
        
         //Link to quest view by questid
        $buscar="/(".$base."\/mod\/blended\/view.php\?a\=)([0-9]+)/";
        $result= preg_replace($buscar,'$@BLENDEDVIEWBYA*$2@$',$result);

        return $result;
    }

function blended_check_backup_mods($course,$user_data=false,$backup_unique_code,$instances=null) {

        if (!empty($instances) && is_array($instances) && count($instances)) {
            $info = array();
            foreach ($instances as $id => $instance) {
                $info += blended_check_backup_mods_instances($instance,$backup_unique_code);
            }
            return $info;
        }
        //First the course data
        $info[0][0] = get_string("modulenameplural","blended");
        if ($ids = blended_ids ($course)) {
            $info[0][1] = count($ids);
        } else {
            $info[0][1] = 0;
        }
/*
        //Now, if requested, the user_data
        if ($user_data) {
            $info[1][0] = get_string("messages","chat");
            if ($ids = chat_message_ids_by_course ($course)) { 
                $info[1][1] = count($ids);
            } else {
                $info[1][1] = 0;
            }
        }
*/
        return $info;
}

  function blended_check_backup_mods_instances($instance,$backup_unique_code) {
        //First the course data
        $info[$instance->id.'0'][0] = '<b>'.$instance->name.'</b>';
        $info[$instance->id.'0'][1] = '';
/*
        //Now, if requested, the user_data
        if (!empty($instance->userdata)) {
            $info[$instance->id.'1'][0] = get_string("messages","chat");
            if ($ids = chat_message_ids_by_instance ($instance->id)) { 
                $info[$instance->id.'1'][1] = count($ids);
            } else {
                $info[$instance->id.'1'][1] = 0;
            }
        }
*/
        return $info;
    }


 function blended_ids ($course) {

        global $CFG;

        return get_records_sql ("SELECT q.id, q.course
                                 FROM {$CFG->prefix}blended q
                                 WHERE q.course = '$course'");
    }
?>