<?php

 	//This php script contains all the stuff to backup/restore
    //blended mods


    //This function executes all the restore procedure about this mod
    function blended_restore_mods($mod,$restore) {

        global $CFG;

        $status = true;

        //Get record from backup_ids
        $data = backup_getid($restore->backup_unique_code,$mod->modtype,$mod->id);

        if ($data) {
            //Now get completed xmlized object   
            $info = $data->info;

            //traverse_xmlize($info);                                                                     //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug
            // if necessary, write to restorelog and adjust date/time fields
            if ($restore->course_startdateoffset) {
                restore_log_date_changes('Blended', $restore, $info['MOD']['#'], array('TIMEOPEN', 'TIMECLOSE'));
            }
            //Now, build the BLENDED record structure
            $blended->course = $restore->course_id;
            $blended->name = backup_todb($info['MOD']['#']['NAME']['0']['#']);
            $blended->description = backup_todb($info['MOD']['#']['DESCRIPTION']['0']['#']);
            $blended->idmethod = backup_todb($info['MOD']['#']['IDMETHOD']['0']['#']);
            $blended->idtype = backup_todb($info['MOD']['#']['IDTYPE']['0']['#']);
            $blended->codebartype = backup_todb($info['MOD']['#']['CODEBARTYPE']['0']['#']);
            $blended->lengthuserinfo = backup_todb($info['MOD']['#']['LENGTHUSERINFO']['0']['#']);
            $blended->teammethod= backup_todb($info['MOD']['#']['TEAMMETHOD']['0']['#']);
            $blended->numteams = backup_todb($info['MOD']['#']['NUMTEAMS']['0']['#']);
            $blended->nummembers = backup_todb($info['MOD']['#']['MUMMEMBERS']['0']['#']);
            $blended->assignment = backup_todb($info['MOD']['#']['ASSIGNMENT']['0']['#']);
            $blended->randomkey= backup_todb($info['MOD']['#']['RANDOMKEY']['0']['#']);
            

            //The structure is equal to the db, so insert the chat
            $newid = insert_record ("blended",$blended);

            //Do some output     
            if (!defined('RESTORE_SILENTLY')) {
                echo "<li>".get_string("modulename","chat")." \"".format_string(stripslashes($blended->name),true)."\"</li>";
            }
            backup_flush(300);

            if ($newid) {
                //We have the newid, update backup_ids
                backup_putid($restore->backup_unique_code,$mod->modtype,
                             $mod->id, $newid);
                //Now check if want to restore user data and do it.
                if (restore_userdata_selected($restore,'blended',$mod->id)) {
                    //Restore chat_messages
                    $status = blended_jobs_restore_mods ($newid,$info,$restore);
                }
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        return $status;
    }
    
      //This function restores the blended_jobs
    function blended_jobs_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_jobs = $info['MOD']['#']['BLENDED_JOBS']['0']['#']['BLENDED_JOB'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_jobs); $i++) {
            $job_info = $blended_jobs[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($job_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($job_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_JOBS record structure
            $job = new object();
            $job->blended = $blendedid;
            $job->quiz = backup_todb($job_info['#']['QUIZ']['0']['#']);
            $job->quiz_name = backup_todb($job_info['#']['QUIZNAME']['0']['#']);
            $job->userid = backup_todb($job_info['#']['USERID']['0']['#']);
            $job->attempt_id = backup_todb($job_info['#']['ATTEMPT_ID']['0']['#']);
            $job->timestamp = backup_todb($job_info['#']['TIMESTAMP']['0']['#']);
            $job->status = backup_todb($job_info['#']['STATUS']['0']['#']);

            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $job->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_jobs",$job);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_jobs",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
    function blended_scans_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_scans = $info['MOD']['#']['BLENDED_SCANS']['0']['#']['BLENDED_SCAN'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_scans); $i++) {
            $scn_info = $blended_scans[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($scn_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($scn_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_SCANS record structure
            $scan = new object();
            $scan->blended = $blendedid;
            $scan->scan_name = backup_todb($scn_info['#']['SCAN_NAME']['0']['#']);
            $scan->userid = backup_todb($scn_info['#']['USERID']['0']['#']);
            $scan->timestamp = backup_todb($scn_info['#']['TIMESTAMP']['0']['#']);
			$scan->status = backup_todb($scn_info['#']['STATUS']['0']['#']);
            $scan->course = backup_todb($scn_info['#']['COURSE']['0']['#']);
			$scan->timestatus = backup_todb($scn_info['#']['TIMESTATUS']['0']['#']);
			$scan->infostatus = backup_todb($scn_info['#']['INFOSTATUS']['0']['#']);
     		$scan->infodetails = backup_todb($scn_info['#']['INFODETAILS']['0']['#']);
            
           
            
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $scan->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_scans",$scan);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_scans",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
    function blended_attempts_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_attempts = $info['MOD']['#']['BLENDED_ATTEMPTS']['0']['#']['BLENDED_ATTEMPT'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_attempts); $i++) {
            $att_info = $blended_attempts[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($att_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($att_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_JOBS record structure
            $attempt = new object();
            //$attempt->blended = $blendedid;
            $attempt->attempt = backup_todb($att_info['#']['ATTEMPT']['0']['#']);
            $attempt->userid = backup_todb($att_info['#']['USERID']['0']['#']);
            $attempt->quiz = backup_todb($att_info['#']['QUIZ']['0']['#']);
            $attempt->layout = backup_todb($att_info['#']['LAYOUT']['0']['#']);
            $attempt->status = backup_todb($att_info['#']['STATUS']['0']['#']);
            $attempt->timestamp = backup_todb($att_info['#']['TIMESTAMP']['0']['#']);
            
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $attempt->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_attempts",$attempt);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_attempts",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }

    function blended_images_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_images = $info['MOD']['#']['BLENDED_IMAGES']['0']['#']['BLENDED_IMAGE'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_images); $i++) {
            $img_info = $blended_images[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($img_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($img_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_SCANS record structure
            $image = new object();
            $image->blended = $blendedid;
            $image->jobid = backup_todb($img_info['#']['JOBID']['0']['#']);
            $image->imgsrc = backup_todb($img_info['#']['IMGSRC']['0']['#']);
			$image->pageindex = backup_todb($img_info['#']['PAGEINDEX']['0']['#']);
     		$image->imgout = backup_todb($img_info['#']['IMGOUT']['0']['#']);
            $image->results = backup_todb($img_info['#']['RESULTS']['0']['#']);
            $image->userid = backup_todb($img_info['#']['USERID']['0']['#']);
            $image->page = backup_todb($img_info['#']['PAGE']['0']['#']);
			$image->status = backup_todb($img_info['#']['STATUS']['0']['#']);
            $image->activitycode = backup_todb($img_info['#']['ACTIVITYCODE']['0']['#']);
			
           
            
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $image->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_images",$image);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_images",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
    function blended_results_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_results = $info['MOD']['#']['BLENDED_RESULTS']['0']['#']['BLENDED_RESULT'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_results); $i++) {
            $rst_info = $blended_results[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($rst_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($rst_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_RESULTS record structure
            $result = new object();
            $result->blended = $blendedid;
            $result->jobid = backup_todb($rst_info['#']['JOBID']['0']['#']);
            $result->userid = backup_todb($rst_info['#']['USERID']['0']['#']);
            $result->label = backup_todb($rst_info['#']['LABEL']['0']['#']);
			$result->value= backup_todb($rst_info['#']['VALUE']['0']['#']);
     		$result->questiontype = backup_todb($rst_info['#']['QUESTIONTYPE']['0']['#']);
            $result->invalid = backup_todb($rst_info['#']['INVALID']['0']['#']);         
            $result->page = backup_todb($rst_info['#']['PAGE']['0']['#']);
            $result->activitycode = backup_todb($rst_info['#']['ACTIVITYCODE']['0']['#']);
			       
            
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $result->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_results",$result);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_results",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
     function blended_member_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_members = $info['MOD']['#']['BLENDED_MEMBERS']['0']['#']['BLENDED_MEMBER'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_members); $i++) {
            $mbr_info = $blended_members[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb($mbr_info['#']['ID']['0']['#']);
            $olduserid = backup_todb($mbr_info['#']['USERID']['0']['#']);

            //Now, build the BLENDED_RESULTS record structure
            $member = new object();
            $member->blended = $blendedid;
            $member->userid = backup_todb($mbr_info['#']['USERID']['0']['#']);
            $member->id_member = backup_todb($mbr_info['#']['ID_MEMBER']['0']['#']);
            $member->id_team = backup_todb($mbr_info['#']['ID_TEAM']['0']['#']);
			$member->leader = backup_todb($mbr_info['#']['LEADER']['0']['#']);
     	
            
            //We have to recode the userid field
            $user = backup_getid($restore->backup_unique_code,"user",$olduserid);
            if ($user) {
                $member->userid = $user->new_id;
            }

            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_member",$member);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_member",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
     function blended_team_restore_mods($blendedid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_teams = $info['MOD']['#']['BLENDED_TEAMS']['0']['#']['BLENDED_TEAM'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_teams); $i++) {
            $tem_info = $blended_teams[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb( $tem_info['#']['ID']['0']['#']);
        
            //Now, build the BLENDED_TEAMS record structure
            $team = new object();
            $team->blended = $blendedid;
            $team->id_team = backup_todb( $tem_info['#']['ID_TEAM']['0']['#']);
            $team->userid_leader = backup_todb( $tem_info['#']['USERID_LEADER']['0']['#']);
     	
            
             //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_team",$team);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_team",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
    function blended_grade_restore_mods($assignmentid,$info,$restore) {

        global $CFG;

        $status = true;

        //Get the messages array 
        $blended_grades = $info['MOD']['#']['BLENDED_GRADES']['0']['#']['BLENDED_GRADE'];

        //Iterate over messages
        for($i = 0; $i < sizeof($blended_teams); $i++) {
            $grd_info = $blended_teams[$i];
            //traverse_xmlize($mes_info);                                                                 //Debug
            //print_object ($GLOBALS['traverse_array']);                                                  //Debug
            //$GLOBALS['traverse_array']="";                                                              //Debug

            //We'll need this later!!
            $oldid = backup_todb( $grd_info['#']['ID']['0']['#']);
       
            //Now, build the BLENDED_RESULTS record structure
            $grade = new object();
          //  $grade->blended = $assignmentid;
            $grade->id_assignment = backup_todb( $grd_info['#']['ID_ASSIGNMENT']['0']['#']);
            $grade->id_assignment_0 = backup_todb( $grd_info['#']['ID_ASSIGNMENT_0']['0']['#']);
            $grade->id_team = backup_todb( $grd_info['#']['ID_TEAM']['0']['#']);
			$grade->rewrite = backup_todb( $grd_info['#']['REWRITE']['0']['#']);
     	
            
            //The structure is equal to the db, so insert the chat_message
            $newid = insert_record ("blended_grade",$grade);

            //Do some output
            if (($i+1) % 50 == 0) {
                if (!defined('RESTORE_SILENTLY')) {
                    echo ".";
                    if (($i+1) % 1000 == 0) {
                        echo "<br />";
                    }
                }
                backup_flush(300);
            }
        
          if ($newid) 
             {
	            backup_putid($restore->backup_unique_code,"blended_grade",$oldid,
	                            $newid);
	            } 
	            else 
	            {
	                $status = false;
	            }
        }

        return $status;
    }
    
    
 	//Return a content decoded to support interactivities linking. Every module
    //should have its own. They are called automatically from
    //blended_decode_content_links_caller() function in each module
    //in the restore process
	 function blended_decode_content_links ($content,$restore) {
            
        global $CFG;
            
        $result = $content;
                
        //Link to the list of blendeds
                
        $searchstring='/\$@(BLENDEDINDEX)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$content,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course id)
                $rec = backup_getid($restore->backup_unique_code,"course",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(BLENDEDINDEX)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/blended/index.php?id='.$rec->new_id,$result);
                } else { 
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/blended/index.php?id='.$old_id,$result);
                }
            }
        }

        //Link to blended view by moduleid

        $searchstring='/\$@(BLENDEDVIEWBYID)\*([0-9]+)@\$/';
        //We look for it
        preg_match_all($searchstring,$result,$foundset);
        //If found, then we are going to look for its new id (in backup tables)
        if ($foundset[0]) {
            //print_object($foundset);                                     //Debug
            //Iterate over foundset[2]. They are the old_ids
            foreach($foundset[2] as $old_id) {
                //We get the needed variables here (course_modules id)
                $rec = backup_getid($restore->backup_unique_code,"course_modules",$old_id);
                //Personalize the searchstring
                $searchstring='/\$@(BLENDEDVIEWBYID)\*('.$old_id.')@\$/';
                //If it is a link to this course, update the link to its new location
                if($rec->new_id) {
                    //Now replace it
                    $result= preg_replace($searchstring,$CFG->wwwroot.'/mod/blended/view.php?id='.$rec->new_id,$result);
                } else {
                    //It's a foreign link so leave it as original
                    $result= preg_replace($searchstring,$restore->original_wwwroot.'/mod/blended/view.php?id='.$old_id,$result);
                }
            }
        }

        return $result;
    }
    
	//This function makes all the necessary calls to xxxx_decode_content_links()
	//function in each module, passing them the desired contents to be decoded
	//from backup format to destination site/course in order to mantain inter-activities
	//working in the backup/restore process. It's called from restore_decode_content_links()
	//function in restore process
	function blended_decode_content_links_caller($restore) {
        global $CFG;
        $status = true;
        
        if ($chats = get_records_sql ("SELECT c.id, c.intro
                                   FROM {$CFG->prefix}blended c
                                   WHERE c.course = $restore->course_id")) {
                                               //Iterate over each chat->intro
            $i = 0;   //Counter to send some output to the browser to avoid timeouts
            foreach ($blendeds as $blended) {
                //Increment counter
                $i++;
                $content = $blended->description;
                $result = restore_decode_content_links_worker($content,$restore);
                if ($result != $content) {
                    //Update record
                    $blended->description = addslashes($result);
                    $status = update_record("blended",$blended);
                    if (debugging()) {
                        if (!defined('RESTORE_SILENTLY')) {
                            echo '<br /><hr />'.s($content).'<br />changed to<br />'.s($result).'<hr /><br />';
                        }
                    }
                }
                //Do some output
                if (($i+1) % 5 == 0) {
                    if (!defined('RESTORE_SILENTLY')) {
                        echo ".";
                        if (($i+1) % 100 == 0) {
                            echo "<br />";
                        }
                    }
                    backup_flush(300);
                }
            }
        }

        return $status;
    }
    
 function blended_restore_logs($restore,$log) {

     //   $status = false;
 }    
?>