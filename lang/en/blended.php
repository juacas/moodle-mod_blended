<?PHP 
$string['modulename']       = 'Blended';
$string['modulenameplural'] = 'Blendeds';

$string['studentOptions']   = 'Students\' Options';
$string['header1']           = 'HEADER1';
$string['header2']           = 'HEADER2';
$string['pluginadministration']           = 'Blended configuration';
$string['managementGenerateTaskSheet']           = 'This tool can create templates for your face-to-face activities in your classroom.';
$string['managementTeamGrading']           = 'Here you can grade the classroom tasks using the teams made for face-to-face work. The grades are inserted in the gradebook taking into account the configured teams.';
$string['managementTeamCreation']           = 'Creation and management of the teams for presential tasks. The teams can be made by the teacher of by the students themselves.';
//mod_form.php
$string['name']               = 'Name';
$string['description']        = 'Descripction';
$string['idmethod']           = 'Identification method';
$string['idmethod_help']           = 'Users can be identified by means of: internal number, user ID field of their profile and any personalized field created by Moodle Administrator';
$string['coded']              = 'Coded';
$string['plain']              = 'Clear';
$string['idtype']             = 'Identifier';
$string['idtype_help'] = 'Types: <ul><li>Internal Id: <i>Moodle</i> internal id for each user.</li>
        <li>User ID: ID Number field from the user\'s profile.</li>
<br>    <li>Personalized ID: profile field created by the administrators.</li>';
$string['username']           = 'User name';
$string['idnumber']           = 'Moodle\'s id-number';
$string['userid']             = 'User internal ID';
$string['byteacher']          = 'Custom field lenght';
$string['teammethod']         = 'Team creation';
$string['teammethod_help']         = 'Select if you allow or not the students to manage their workteam composition.';
$string['bystudents']         = 'by the students';
$string['bystudentswithleaders']= 'by the students with a leader';
$string['byteacher']          = 'bu the teacher';
$string['defaultassignmentf'] = 'Default assignment for creating the teams by the students';
$string['any']                = 'any';
$string['defaultnumteams']    = 'Default team number';
$string['defaultnummembers']  = 'Default member number';
$string['defaultnummembers_help']    = 'Set the default maximum number of members allowed in a workteam.';
$string['required']           = 'Required';
$string['userinfo']           = 'Customized information';
$string['lengthuserinfo']     = 'Length of the Customized ID';
$string ['lengthuserinfo_help'] = 'If you have selected an Customized Identifier, this is the number of significant characters used for identification.';
$string['numbercols']     	  = 'Number of columns';
$string['numbercols_help']     	  = 'Number of columns of the quiz form in printed page';

$string['codebartype']        = 'Codebar types';
$string['codebartype_help']        = 'Select the type of codebar images to be used as visual identifiers for users and forms.';
$string['OMRenable']        = 'Enable OMR sub-module.';
$string['OMRenableLabel']        = 'OMR enabled';

//view.php
$string['mainpage_help'] = "Blended is a module that helps to undertake presential activities in Moodle.";
$string['mainpage'] = "Blended is a module that helps to undertake presential activities in Moodle.";
$string['labelspage']          = 'Generate a page with stickers.';  
$string['labelsdesc']		   = '<b>$a</b> to identify the students. Stick them in the quizzes header in the ID field. <br>
								  Configure the number of labels to generate and choose the students of whom the labels will be generated. <p align=center></p> ';
$string['labelsGenerateStickersdesc']		   = 'Print codebar stickers to identify the students. Stick them in the quizzes header in the ID field. <br>
								  Configure the number of labels to generate and choose the students of whom the labels will be generated. <p align=center></p> ';
$string['blendedquizzes']	   = 'Blended Quizzes';
$string['blendedquizzesdesc']  = '<table border=\"0\" valign=\"top\"><tr><td><b>$a->l5:</b></td><td valign=\"top\"> Generate a PDF file with a set of quizes generated from a standard Moodle quiz.</td></tr><tr><td>
								  <b>$a->l6:</b></td><td valign=\"top\"> Upload and select a PDF or ZIP file with scanned tests to be processed by the marks detector.<p align=center></p></td></tr><tr><td>
								  <br>
								 <b>$a->l7:</b></td><td valign=\"top\"> Review, manually modify the recognized results and pass grades of each quiz to Moodle.
								 <p align=center></p></td></tr><tr><td align=right>
								 <b>Test Revision:</b>.</td><td valign=\"top\"> Review correction and grades of a quiz. (Only enabled for students).
								 <p align=center>$a->l8</p> </td></tr></table>';
$string['blendedquizPrintPDFdesc']  = 'Generate a PDF file with a set of quizes generated from a standard Moodle quiz.';
$string['blendedquizUploadScansdesc']  = 'Upload and select a PDF or ZIP file with scanned tests to be processed by the marks detector.';
$string['blendedquizReviewScansdesc']  = 'Review, manually modify the recognized results and pass grades of each quiz to Moodle.';
$string['blendedquizReviewResultsdesc']  = 'Review correction and grades of a quiz. (Only enabled for students).';

$string['management']		   = 'Team and assignment management.';
$string['managementdesc']	   = '<b>$a->l2:</b> Generate a PDF file from a Moodle assignment. <p align=center></p>
								  <br>
								  <b>$a->l3:</b> Grade a selected assignment. Team grading feature included.
							  <p align=center></p>
								  <br>
								 <b>$a->l4a:</b> Create teams for each assignment. Manual or random creating method.
								 <p align=center></p>';	
$string['student']			   = "Blended for students.";
$string['studentdesc']	   		= '<b>$a->l1</b> with your ID to stick on the quizzes.
								  You will be able to choose the number of labels you want to print by row and column in a page. <p align=center></p>
								  <br><br><b>$a->l2:</b> Generate a PDF file from a Moodle assignment.  
							  	  <p align=center></p>
								  <br><br><b>$a->l4b:</b> Join a team to complete an assignment. 
								  <p align=center></p>
								  <br><br><b>$a->l8:</b>. Review correction and gradin of a quiz. If you notice mistakes, mismatches or any error please contact your teacher.
								  <p align=center></p>';
$string['assignmentpage']      = 'Generate assignment page';
$string['studentlabelsGenerateStickers'] = 'Generate stickers to be attached to your activity sheets. You can configure the number of stickers per page.';
$string['studentmanagementGenerateTaskSheet'] = 'If an activity needs to the done in the classroom, you can obtain here a template to ease the submission in Moodle.';
$string['studentJoinAGroup']	= 'You can join a workteam to undertake a classroom assignment.'; 
$string['gradepage']           = 'Grade assignmnent';
$string['introgradepage']      = 'Insert grades';  
$string['teamsmanagementpage'] = 'Manage teams';
$string['signupteampage']      = 'Sign up team';
$string['edituserinfo']        = 'Profile ID Number field required to access';
$string['edituserinfo2']       = 'Enter your profile Personalized ID to acsess';
$string['nostudentsincourse']  = 'Links disabled:<br><br>! There are no students in the course ¡';
$string['noneisactive']        = '¡ There are no active students in the course !';
$string['studentisnotactive']  = '¡ There are inactive students in the course !';
$string['noidnumberview']      = '¡ Student has not filled in DNI profile field !';
$string['nouserinfodataview']  = '¡ Student has not filled in Personalized ID profile field !';
$string['generatepaperquiz']   	 = 'Generate batch of questionnaires in paper';
$string['scan'] 			     = 'Process scanned quizzes';
$string['correction'] 		     = 'Supervise the state of the questionnaires';
$string['revision'] 		     = 'Test revision';
//$string['header1'] 		  	   = 'Módulo de gestión de etiquetas y creacion de grupos';
//$string['header2'] 		   	   = 'Módulo para integración de cuestionarios en papel';
$string['scannedJob']	    	  = 'View Scanned Job';
$string['imagepage']	    	  = 'View Image';
$string['reviewdetails']	      = 'Review Quiz';
$string['showdetailspage']	      = 'Quiz Details';
$string['evaluatepage']	      = 'Evaluate';
$string['deletescanjob']		= 'Delete Job';
$string['deletequiz']			= 'Delete Quiz';
$string['jobstatus'] 			= 'Job Status';
$string['statusdetails']		= 'Detailled Report';
$string['viewalerts']		= 'Alert Messages';


//Descripciones de pagina
$string['assignmentpagedescr']     = 'Select the assignment page to print ';
$string['assignmentpagedescr2']    = 'Assignment page: ';
$string['teamsmanagementpagedesc'] = 'Create teams for each assignment, manual or randomly.';
$string['gradepagedescr']          = 'Select the assignment to grade';
$string['signupteampagedesc']      = 'Inscribase en un equipo para dicha tarea. Sólo el que crea el equipo puede eliminar miembros del mismo.';
$string['scanpagedesc']            = 'Upload or select a file to be processed by the OMR detector. 
<BR> The source file must be a PDF file or a ZIP file containing JPG or TIF formatted images.
<BR> The jobs will be automatically processed by Moodle. This will take a few minutes.  
You can check the results in the \"Supervise the state of the questionnaires\" section.';
$string['correctionpagedesc']      = 'Select a job to show.';
$string['imagepagedesc']      	   = 'Selected Image';
$string['scannedjobpagedesc']      = 'Results of the job <a href=\"$a->href\">$a->hrefText</a>';
$string['revisionpagedesc']        = 'These are the evaluated quizzes found in this course.';
$string['evaluatepagedesc']        = 'Your quiz has been graded. Evaluation report: <a href=\"$a->href\">$a->hrefText</a>.
									  <BR>To refresh the grade, use the regrade link if needed.';
$string['showdetailspagedesc']     = 'Results of the quiz number $a->acode perteneciente al trabajo <a href=\"$a->href\">$a->hrefText</a>';
$string['reviewdetailspagedesc']     = 'Details of the selected quiz';

$string['deletescanjobdesc'] 		= 'WARNING: you are about to delete the job $a->jobname. This will erase any quiz or image  
										belonging to the processing detection of the job, so as every evaluation result.
										<BR> Original PDF, uploaded files an moodle archive files will NOT be deleted.
										<BR><BR> If you really want to proceed, press Delete button. ';


$string['deletequizdesc'] 		= 'WARNING: you are about to delete the quiz number $a->acode. Any correction or evaluation data will also be deleted.
									<BR><BR> If you really want to proceed, press Delete button.';

$string['deleteimgdesc'] 		= 'WARNING: you are about to delete the image $a->imgout. This image may be an unrecognized page of a quiz you probably want to evaluate. <BR><BR> If you really want to proceed, press Delete button.';

$string['jobstatusdesc'] = 'Estado del trabajo seleccionado. Para ver los detalles pulse el enlace correspondiente.<BR> (Sólo administradores o usuarios con permisos)';
$string['viewalertsdesc'] 	= 'Alert Messages';
$string['activitycodepagedesc'] 	= 'Cuestionario con activitycode no detectado o erróneo.';

//Labelspage.php
$string['labelspagedescr1']      = 'Select the student the number of labels by row and column that will be printed in a page';
$string['labelspagedescr2']      = 'Select the number of labels by row and column that will be printed in a page';
$string['numrows']               = 'Number of columns: ';
$string['numcolumns']            = 'Number of rows: ';
$string['noselected']            = 'Select: ';
$string['noactiveuser']          = '¡ With (*) the students are not active in the course !';
$string['noidnumber']            = '¡ With (**) the students have not introduced their DNI number !';
$string['nouserinfodata']        = '¡ With (#) the students that have not introduced their PERSONALIZED ID !';
$string['printlabels']           = 'Print Labels';
$string['cantprintlabel']        = ' Unable to print labels until the student inserts DNI field.';
$string['cantprintlabel2']       = ' Unable to print labels until the student inserts PERSONALIZED ID field.';
$string['numrowsnotselected']    = 'Number of rows not selected';
$string['numcolumnsnotselected'] = 'Number of columns not selected';
$string['pageformat']			 = 'Page Format';
$string['margin_top_mm']		 = 'Margin Top mm (Measure carefully in your stickers sheet)';
$string['margin_bottom_mm']		 = 'Margin Bottom mm (Measure carefully in your stickers sheet)';
$string['margin_right_mm']		 = 'Margin Right mm (Measure carefully in your stickers sheet)';
$string['margin_left_mm']		 = 'Margin Left mm (Measure carefully in your stickers sheet)';
$string['printforone']			 = 'Print for one student.';
$string['layoutmethod']			 = 'Methods for layout labels';
$string['oneforeachactive']		 = 'One label for each active student in the course.';
$string['oneforeachenrolled']	 = 'One label for each student enrolled in the course.';
$string['fullpages']			 = 'Full pages of labels for the students selected in the list on the left.';
$string['labelsformat']			 = 'Course labels format';
$string['identifyforhumans']	 = 'Identify labels for humans';
$string['donotidentify']		 = 'Do not identify';
$string['showreadableid']		 = 'Show Human-readable id';
$string['showfullname']			 = 'Show Fullname';

//Assignmentpage.php
$string['assignments']              = 'Select activity: ';
$string['defaultassignment']        = 'Assignmnent: ';
$string['user']                     = 'Student: ';
$string['assignment']               = 'Assignment (if not in the list): ';
$string['printassignmentpage']      = 'Print assignment page';
$string['assignmentnotselected1']   = 'Assignment not selected and empty assignment name.';
$string['usernotselected']          = 'User not selected.';
$string['noassignments']            = 'There are no assignments created in this course.';
$string['cantprintassignmentpage']  = ' Student ID required to print assignment page!';
$string['cantprintassignmentpage2'] = ' Student PERSONALIZED ID required to print assignment page!';
$string['noidnumber2']              = '¡ Missing ID !';

//Grades.php
$string['groupingnotselected'] = 'Set of teams to grade has not been selected.';
$string['gradeassignments']       = 'Assignment to grade:';
$string['teamassignment']         = 'Assignment from which extract the teams:';
$string['numteams']               = 'Number of teams:';
$string['teams_from']            = 'teams taken from';
$string['deleteemptyteams']       = 'Remove teams if empty';
$string['resetgroups']             = 'Reset or create a set og teams';
$string['nummembers']             = 'Number of members in a team:';
$string['gradeassignment']        = 'Grade assignment';

//Introgrades.php
$string['search']               = 'Search identifier:';
$string['idteam']               = 'Team:';
$string['idmembers']            = 'Identifiers:';
$string['grade']                = 'Grade:';
$string['nograded']             = '-';
$string['sendgrades']           = 'Save grades';
$string['teamsfromassignment']  = 'Teams created for activity: ';
$string['rewritegrades']        = '<center>¿Overwrite?<br>Individual grade<br>is different from group grade.</center>';
$string['confirmrewritegrades'] = 'Make sure you want to overwrite individual grade different from group grade. Procceed?';
$string['checkbox']             = 'Uncheck the corresponding checkbox: ';
$string['existinglinkedteams']  = 'There are existing teams for the assignment: $a already linked to another assignment.<br>The assignment: $a cannot be linked to the assignment: ';
$string['existingteams']        = 'There are existing teams for the assignment: $a.<br> Unable to use for the assignment: $a the created teams for the assignment:  ';
$string['alertgrade']   =  'The individual grade "{$a->grade}" differs from the team grade. This individual grade will be overwritten by team\'s grade.';

//Save.php
$string['save']       = 'Save';
$string['inserted']   = '<center>Success!!</center>';
$string['noinserted'] = '<center>No operation has been done.</center>';

//Teamsmanagement.php
$string['creationmethod']    = 'Team creation method: ';
$string['byhand']            = 'Manual';
$string['randomly']          = 'Random';
$string['name']              = 'Assignment name';
$string['assignmenttype']    = 'Assignment type';
$string['duedate']           = 'Due date: ';
$string['teams']             = 'Number of teams';
$string['graded']            = 'Graded';
$string['user_regraded']     = '{$a->userlink} regraded from "{$a->prev_grade}" to "{$a->new_grade}".';
$string['resetteams']       = 'Reset teams';
$string['createteams2']      = 'Create/Modify/Delete teams';
$string['rewriteteams']      = 'Delete link <br> and create teams';
$string['confirmrewrite']    = '¿Esta seguro de que desea eliminar la vinculación con la tarea y crear nuevos equipos?';
$string['linked']            = '  linked to the asignment:  ';
$string['linked2']           = '  linked to the assignment:  ';
$string['no']                = 'No';
$string['yes']               = 'Yes';
$string['partially']         = 'Partially';
$string['studentsselection'] = 'Students selection:';
$string['activestudents']    = 'Only active';
$string['allstudents']       = 'All';
$string['newgroupingnotify'] = 'Created a new grouping {$a->grouping_name} with {$a->num_teams} empty teams.';
$string['selectgroupingnotify'] = 'Selected a new grouping {$a->grouping_name} for {$a->item}.';
//Introteams.php
$string['gradeit']             = 'Grade teams';
$string['introteams']          = 'Team management for ';
$string['sendteams']           = 'Save teams';
$string['withoutidnumber']     = '<br>Without ID number.';
$string['withoutuserinfodata'] = '<br>Without Personalized ID.';
$string['select_grouping']	   = 'Choose the set of groups to be used in the activity';

//Signupteam.php
$string['teamscount']             = 'Teams ({$a})';
$string['teammembers']            = 'Team members';
$string['membercount']            = 'Number of members';
$string['newteam']                = 'Create a new team';
$string['nameteam']               = 'Team name';
$string['signupteampage2']        = 'Teams for the assignment: ';
$string['deletemember']           = 'Delete members';
$string['selectassignpage']       = 'Sign up team';
$string['signupteam']             = 'Sign up team';
$string['nodefinedteams']       ='There are still no teams defined.';
$string['teamisfull']       = 'Team already full. You can select a new team';
$string['assignmentnotselected3'] = 'Assignment not selected to sign up a team';
$string['userisnotmember'] = 'User {$a} is not member of the team';
$string['userremovedfromteam']='User {$a->username} removed from {$a->teamname}';
$string['teamremoved']='Team {$a->name} removed';
$string['userenrolledtoteam']='{$a->username} has been enrolled in team {$a->teamname}';
$string['userpromotedtoleader']='User {$a->username} promoted to leader of team {$a->teamname}';

//edit_paperquiz.php
$string['paperquiz'] 			  = 'Generate questionnaires in paper';
$string['paperquizdescr']		  = 'Select the questionnaire that you want to build the batch.
									 It will generate a PDF file in the root directory of your course with the generated documents';
$string['paperquizformat']	      = 'Paperquiz Format';
$string['selectquiz']	    	  = 'Select the quiz';
$string['numquiz']	    	  	  = 'Number of copies';
$string['later']	    	  	  = 'Generate later';
$string['labelformat']	    	  = 'Labels format';
$string['identify']	    	 	  = 'Identify labels for humans';
$string['notidentify']	    	  = 'Do not identify';
$string['readable']	    	  	  = 'Show human-readable id';
$string['table']	    	  	  = '';
$string['noquizzes']			  = 'No hay cuestionarios creados en el curso.';

//scan.php, correction.php,scannedJob
$string['resultlink']	    	  = 'Result';
$string['activitycode']			  = 'ACTIVITYCODE';	
$string['ScannedFolderName']	  = 'Scans';
$string['idlabel']			 	  = 'USERID';	
$string['accept']				  = 'Accept'; 
$string['omrPath']                = 'Path of the OMR tool';

//help files
$string['EVAL']				  = 'Eval fields'; 
$string['PREGUNTA']				  = 'Questions'; 
$string['SCANJOB']				  = 'Scanned job';
$string['pagehelp']			   = 'Page help contents'; 
$string['processcancelled']		  = '<CENTER>Process cancelled.</CENTER><BR>';	

// Mensajes de error
$string['alert_error_1']  = '- Students with IDs:  ';
$string['alert_error_2']  = '- The student with ID:  ';
$string['alert_error_3']  = '  added $a to the team: ';
$string['alert_error_4']  = '<br>Found a problem trying to search:<br><br> $a <br>';
$string['alert_error_5']  = '<br>Unrolled in the course:<br><br> $a <br>';
$string['alert_error_6']  = '<br>Unrolled in the course:<br><br> $a <br>';
$string['alert_error_7']  = '<br>Already signed up to another team for this assignment:<br><br> $a <br>';
$string['alert_error_8']  = '<br>Already signed up to another team for this assignment:<br><br> $a <br>';
$string['alert_error_9']  = '<br><br>These students have not been inserted.';
$string['alert_error_10'] = '<br><br>This student has not been inserted.';

$string['PDFgeneratedMessage']="<p>PDF file generated. You can find it in the Files section of your course, under the ". 
								'<a href=\"$a->href\">$a->hrefText</a> directory.</p>'.
								'<p>Direct download link: <a href=\"$a->directLinkhref\">$a->directLinkText</a></p>';
?>
