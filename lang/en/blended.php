<?PHP 
$string['modulename'] = 'Blended';
$string['pluginname'] = 'Blended';
$string['modulenameplural'] = 'Blendeds';

//update.php
$string['name'] = 'Name';
$string['idmethod'] = 'Identification method';
$string['idmethod_help'] = 'Users can be identified by means of: internal number, user ID field of their profile and any personalized field created by Moodle Administrator';
$string['required'] = 'Required';
$string['description'] = 'Descripction';
$string['coded'] = 'Coded';
$string['plain'] = 'Clear';
$string['codebartype'] = 'CodSelectebar types';
$string['codebartype_help'] = ' the type of codebar images to be used as visual identifiers for users and forms.';
$string['OMRenable']  = 'Enable OMR sub-module.';
$string['OMRenableLabel'] = 'OMR enabled';
$string['numbercols'] = 'Number of columns';
$string['numbercols_help'] = 'Number of columns of the quiz form in printed page';
$string['idnumber']  = 'Moodle\'s id-number';
$string['userid'] = 'User internal ID';
$string['idtype'] = 'Identifier';
$string['idtype_help'] = 'Types: <ul><li>Internal Id: <i>Moodle</i> internal id for each user.</li>
        <li>User ID: ID Number field from the user\'s profile.</li>
<br>    <li>Personalized ID: profile field created by the administrators.</li>';
$string['lengthuserinfo'] = 'Length of the Customized ID';
$string['lengthuserinfo_help'] = 'If you have selected an Customized Identifier, this is the number of significant characters used for identification.';
$string['byteacher'] = 'by the teacher';
$string['bystudents'] = 'by the students';
$string['bystudentswithleaders']= 'by the students with a leader';
$string['teammethod'] = 'Team creation';
$string['teammethod_help'] = 'Select if you allow or not the students to manage their workteam composition.';
$string['defaultnumteams'] = 'Default team number';
$string['defaultnummembers'] = 'Default member number';
$string['defaultnummembers_help'] = 'Set the default maximum number of members allowed in a workteam.';
$string['any'] = 'any';


//view.php
$string['mainpage_help'] = "Blended is a module that helps to undertake presential activities in Moodle.";
$string['mainpage'] = "Blended is a module that helps to undertake presential activities in Moodle.";
$string['assignmentpage'] = 'Generate assignment page';
$string['blendedquizzes'] = 'Blended Quizzes';
$string['pluginadministration'] = 'Blended Configuration';
$string['labelspage'] = 'Generate a page with stickers';
$string['management'] = 'Team and assignment management';
$string['studentOptions'] = 'Students\' Options';
$string['gradepage'] = 'Grade assignmnent';
$string['teamsmanagementpage'] = 'Manage teams';
$string['signupteampage'] = 'Sign up team';
$string['nostudentsincourse'] = 'Links disabled:<br><br>! There are not students in the course!';
$string['noneisactive'] = 'There are no active students in the course!';
$string['noidnumberview'] = 'Student has not filled in DNI profile field!';
$string['nouserinfodataview']  = 'Student has not filled in Personalized ID profile field !';
$string['studentisnotactive'] = 'There are inactive students in the course!';
$string['generatepaperquiz'] = 'Generate batch of questionnaires in paper';
$string['scan'] = 'Process scanned quizzes';
$string['correction'] = 'Supervise the state of the questionnaires';
$string['revision'] = 'Test revision';
$string['header1'] = 'HEADER1';
$string['header2'] = 'HEADER2';
$string['labelsGenerateStickersdesc'] = 'Print codebar stickers to identify the students. Stick them in the quizzes header in the ID field. <br>
				Configure the number of labels to generate and choose the students of whom the labels will be generated. <p align=center></p> ';
$string['managementGenerateTaskSheet'] = 'This tool can create templates for your face-to-face activities in your classroom.';
$string['managementTeamGrading'] = 'Here you can grade the classroom tasks using the teams made for face-to-face work. The grades are inserted in the gradebook taking into account the configured teams.';
$string['managementTeamCreation'] = 'Creation and management of the teams for presential tasks. The teams can be made by the teacher of by the students themselves.';
//Students
$string['studentlabelsGenerateStickers'] = 'Generate stickers to be attached to your activity sheets. You can configure the number of stickers per page.';
$string['studentmanagementGenerateTaskSheet'] = 'If an activity needs to the done in the classroom, you can obtain here a template to ease the submission in Moodle.';
$string['studentJoinAGroup'] = 'You can join a workteam to undertake a classroom assignment.'; 


//assignmentpage.php
$string['assignmentpagedescr'] = 'Select the assignment page to print';
$string['defaultassignment'] = 'Assignmnent: ';
$string['assignments'] = 'Select activity: ';
$string['assignment'] = 'Assignment (if not in the list): ';
$string['user'] = 'Student: ';
$string['noactiveuser'] = 'With (*) the students are not active in the course!';
$string['noidnumber'] = 'With (**) the students have not introduced their DNI number !';
$string['noidnumber2'] = 'Missing ID!';
$string['nouserinfodata']  = 'With (#) the students that have not introduced their PERSONALIZED ID !';
$string['noselected'] = 'Select: ';
$string['printassignmentpage'] = 'Print assignment page';
$string['noassignments'] = 'There are no assignments created in this course.';


//signupteam.php
$string['selectassignpage'] = 'Sign up team';
$string['signupteam'] = 'Sign up team';
$string['newteam'] = 'Create a new team';
$string['nameteam'] = 'Team name';
$string['deletemember'] = 'Delete members';
$string['nodefinedteams'] ='There are still no teams defined.';
$string['userenrolledtoteam'] = '{$a->username} has been enrolled in team {$a->teamname}';
$string['teamscount'] = 'Teams ({$a})';
$string['teammembers'] = 'Team members';
$string['membercount'] = 'Number of members';
$string['teamisfull'] = 'Team already full. You can select a new team';
$string['userremovedfromteam']='User {$a->username} removed from {$a->teamname}';
$string['teamremoved'] = 'Team {$a->name} removed';
$string['userpromotedtoleader'] = 'User {$a->username} promoted to leader of team {$a->teamname}';


//save.php
$string['inserted'] = '<center>Success!!</center>';
$string['noinserted'] = '<center>No operation has been done.</center>';


//grades.php
$string['nameassigment'] = 'Assignment name';
$string['gradeassignments'] = 'Assignment to grade:';
$string['duedate'] = 'Due date: ';
$string['graded'] = 'Graded';
$string['no'] = 'No';
$string['yes'] = 'Yes';
$string['partially'] = 'Partially';
$string['numteams'] = 'Number of teams:';
$string['teams'] = 'Number of teams';
$string['resetteams'] = 'Reset teams';
$string['createteams2'] = 'Create/Modify/Delete teams';
$string['teamsmanagementpagedesc'] = 'Create teams for each assignment, manual or randomly.';
$string['pagehelp'] = 'Page help contents';
$string['itemsforgrading'] = 'Items for grading';


//introgrades.php
$string['introgradespage'] = 'Insert grades';  
$string['introgrades'] = 'Introduce grades'; // Verificar
$string['sendgrades'] = 'Save grades';
$string['teamsfromassignment'] = 'Teams created for activity: ';
$string['existinglinkedteams'] = 'There are existing teams for the assignment: $a already linked to another assignment.<br>The assignment: $a cannot be linked to the assignment: ';
$string['existingteams'] = 'There are existing teams for the assignment: $a.<br> Unable to use for the assignment: $a the created teams for the assignment:  ';
$string['teams_from'] = 'teams taken from';
$string['idteam'] = 'Team';
$string['idmembers'] = 'Students';
$string['viewalertsdesc'] = 'Alert Messages';
$string['rewritegrades'] = '<center>�Overwrite?<br>Individual grade<br>is different from group grade.</center>';
$string['grade'] = 'Grade';
$string['nograded'] = '-';
$string['teamnotgraded'] = 'No graded';
$string['teamempty'] = 'Empty team';
$string['deleteemptyteams'] = 'Remove teams if empty';


//introteams.php
$string['introteamspage'] = 'Assign groups to assigment';
$string['introteams'] = 'Team management for ';
$string['creationmethod'] = 'Team creation method:';
$string['byhand'] = 'Manual';
$string['randomly'] = 'Random';
$string['studentsselection'] = 'Students selection:';
$string['activestudents'] = 'Only active';
$string['allstudents'] = 'All';
$string['nummembers'] = 'Number of members in a team:';
$string['resetgroups'] = 'Reset or create a set of teams';
$string['select_grouping'] = 'Choose the set of groups to be used in the activity';
$string['change_groups_button'] = 'Change to this set of groups.';
$string['introteams'] = 'Team management for ';


//saveteamsgrades.php
$string['user_regraded'] = '{$a->userlink} regraded from "{$a->prev_grade}" to "{$a->new_grade}".';


//createTeams.php
$string['newgroupingnotify'] = 'Created a new grouping {$a->grouping_name} with {$a->num_teams} empty teams.'; 


//labels.php
$string['labelspagedescr1'] = 'Select the student the number of labels by row and column that will be printed in a page';
$string['labelspagedescr2'] = 'Select the number of labels by row and column that will be printed in a page';
$string['numrows'] = 'Number of columns: ';
$string['numcolumns'] = 'Number of rows: ';
$string['printlabels'] = 'Print Labels';
$string['pageformat'] = 'Page Format';
$string['printforone'] = 'Print for one student';
$string['layoutmethod'] = 'Methods for layout labels';
$string['oneforeachactive'] = 'One label for each active student in the course.';
$string['oneforeachenrolled'] = 'One label for each student enrolled in the course.';
$string['fullpages'] = 'Full pages of labels for the students selected in the list on the left.';
$string['labelsformat'] = 'Course labels format';
$string['identifyforhumans'] = 'Identify labels for humans';
$string['donotidentify'] = 'Do not identify';
$string['showreadableid'] = 'Show human-readable id';
$string['showfullname'] = 'Show fullname';
$string['margin_top_mm'] = 'Margin Top mm (Measure carefully in your stickers sheet)';
$string['margin_bottom_mm'] = 'Margin Bottom mm (Measure carefully in your stickers sheet)';
$string['margin_right_mm'] = 'Margin Right mm (Measure carefully in your stickers sheet)';
$string['margin_left_mm'] = 'Margin Left mm (Measure carefully in your stickers sheet)';
$string['cantprintlabel'] = 'Unable to print labels until the student inserts DNI field.';
$string['cantprintlabel2'] = 'Unable to print labels until the student inserts Customized ID field.';


$string['selectitems'] = 'Only show selected items. (All if none selected)';
$string['selectitems_help'] = 'If you want to use Blended only for grading a number of items, you can select here which ones are activated.';
//Descripciones de pagina
$string['assignmentpagedescr']     = 'Select the assignment page to print ';
$string['assignmentpagedescr2']    = 'Assignment page: ';
$string['teamsmanagementpagedesc'] = 'Create teams for each assignment, manually or randomly.';
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
$string['resetgroups']             = 'Reset or create a set of teams';
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

//Permissions
$string['blended:blended'] = 'Blended';
$string['blended:createscannedjob'] = 'Create scanned job';
$string['blended:deleteall'] = 'Delete all';
$string['blended:deletejob'] = 'Delete job';
$string['blended:deletequiz'] = 'Delete assignment';
$string['blended:deletescanjob'] = 'Delete scanned job';
$string['blended:editresults'] = 'Edit results'; 
$string['blended:evaluatequiz'] = 'Evaluate assignment';
$string['blended:introgrades'] = 'Insert grades';
$string['blended:introteams'] = 'Insert teams';
$string['blended:launchjob'] = 'Launch job';
$string['blended:managealljobs'] = 'Manage all jobs'; 
$string['blended:printassignmentpage'] = 'Print assignment page';
$string['blended:printlabels'] = 'Prin labels';
$string['blended:printquizes'] = 'Print assignments';
$string['blended:reviewresults'] = 'Review results';
$string['blended:rolelinks'] = 'Role links'; 
$string['blended:selectoneamongallstudents'] = 'Select one among all students';
$string['blended:signupteam'] = 'Sign up team'; 
$string['blended:view'] = 'View';
$string['blended:viewalerts'] = 'View alerts'; 
$string['blended:viewimage'] = 'View image'; 
$string['blended:viewscannedjobs'] = 'View scanned jobs'; 
$string['blended:viewstatus'] = 'View status'; 
$string['blended:viewstatusdetails'] = 'View status details';


//Files help
$string['labelspage_help'] = 'This page enables an user to generate label sheets for the students in the course.
								<br>The students can choose the page format and what students want labels are made.';
$string['assignmentpage_help'] = 'This page allows you to select the activity and the list of students who want to generate the task page.';
$string['gradepage_help'] = 'This page shows a list of the course activities with their grouping and the status of their assessment. In addition, the teacher can also modify the assigned grouping to an activity.';
$string['introteamspage_help'] = 'This page allows the teacher to create (manual or randonmly) a new grouping for an activity.';
$string['introgradespage_help'] = 'This page allows the teacher to insert the grades of an activity for each team. In addition, the teacher also can create and modify the name and members of the teams.';
$string['selectassignmentpage_help'] = 'selectassignmentpage_help';
$string['signupteampage_help'] = 'signupteampage_help';
?>
