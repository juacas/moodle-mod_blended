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
$string['codebartype'] = 'Codbar types';
$string['codebartype_help'] = 'Select the type of codebar images to be used as visual identifiers for users and forms.';
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
$string['assignmentpage'] = 'Generate Assignment page';
$string['blendedquizzes'] = 'Blended Quizzes';
$string['pluginadministration'] = 'Blended Configuration';
$string['labelspage'] = 'Generate Page with stickers';
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
$string['defaultassignment'] = 'Assignment: ';
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
$string['duedate'] = 'Due date';
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
$string['rewritegrades'] = '<center>Overwrite?<br>Individual grade<br>is different from group grade.</center>';
$string['grade'] = 'Grade';
$string['nograded'] = '-';
$string['teamnotgraded'] = 'No graded';
$string['teamempty'] = 'Empty team';
$string['deleteemptyteams'] = 'Remove teams if empty';


//introteams.php
$string['introteamspage'] = 'Assign grouping to assigment';
$string['introteams'] = 'Team management for ';
$string['creationmethod'] = 'Team creation method:';
$string['byhand'] = 'Manual';
$string['randomly'] = 'Random';
$string['studentsselection'] = 'Students selection:';
$string['activestudents'] = 'Only active';
$string['allstudents'] = 'All';
$string['nummembers'] = 'Number of members in a team:';
$string['resetgroups'] = 'Create a new grouping';
$string['select_grouping'] = 'Choose the grouping to be used in the activity';
$string['change_groups_button'] = 'Change grouping';
$string['introteams'] = 'Team management for ';
$string['warning_previous_grades']='There are grades assigned by configuration of teams. If you change this organization, every grade must be re-entered!';

//saveteamsgrades.php
$string['user_regraded'] = '{$a->userlink} regraded from "{$a->prev_grade}" to "{$a->new_grade}".';


//createTeams.php
$string['newgroupingnotify'] = 'Created a new grouping {$a->grouping_name} with {$a->num_teams} empty teams.'; 
$string['selectgroupingnotify'] = 'Selected a new grouping {$a->grouping_name} for {$a->item}.';


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
