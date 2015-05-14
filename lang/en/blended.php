<?PHP 
$string['modulename']       = 'Blended';
$string['modulenameplural'] = 'Blendeds';

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
$string['selectassignment'] = 'Select activity'; // Verificar si no coincide con ['assignments


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
$string['name'] = 'Assignment name';
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


//introgrades.php
$string['introgrades'] = 'Introduce the grades'; // Verificar
$string['sendgrades'] = 'Save grades';
$string['teamsfromassignment'] = 'Teams created for activity: ';
$string['existinglinkedteams'] = 'There are existing teams for the assignment: $a already linked to another assignment.<br>The assignment: $a cannot be linked to the assignment: ';
$string['existingteams'] = 'There are existing teams for the assignment: $a.<br> Unable to use for the assignment: $a the created teams for the assignment:  ';
$string['teams_from'] = 'teams taken from';
$string['idteam'] = 'Team:';
$string['idmembers'] = 'Identifiers:';
$string['viewalertsdesc'] = 'Alert Messages';
$string['rewritegrades'] = '<center>żOverwrite?<br>Individual grade<br>is different from group grade.</center>';
$string['grade'] = 'Grade:';
$string['nograded'] = '-';
$string['deleteemptyteams'] = 'Remove teams if empty';


//introteams.php
$string['introteams'] = 'Team management for ';
$string['creationmethod'] = 'Team creation method: ';
$string['byhand'] = 'Manual';
$string['randomly'] = 'Random';
$string['studentsselection'] = 'Students selection:';
$string['activestudents'] = 'Only active';
$string['allstudents'] = 'All';
$string['nummembers'] = 'Number of members in a team:';
$string['introgradepage'] = 'Insert grades';  
$string['resetgroups'] = 'Reset or create a set of teams';
$string['select_grouping'] = 'Choose the set of groups to be used in the activity';
$string['introteams'] = 'Team management for ';

//saveteamsgrades.php
$string['user_regraded'] = '{$a->userlink} regraded from "{$a->prev_grade}" to "{$a->new_grade}".';


//createTeams.php
$string['newgroupingnotify'] = 'Created a new grouping {$a->grouping_name} with {$a->num_teams} empty teams.'; 
$string['selectgroupingnotify'] = 'Selected a new grouping {$a->grouping_name} for {$a->item}.';


//labels.php
$string['labels'] = 'Labels'; //Verificar texto
$string['labelspagedescr1'] = 'Select the student the number of labels by row and column that will be printed in a page';
$string['labelspagedescr2'] = 'Select the number of labels by row and column that will be printed in a page';
$string['numrows'] = 'Number of columns: ';
$string['numcolumns'] = 'Number of rows: ';
$string['printlabels'] = 'Print Labels';
$string['pageformat'] = 'Page Format';
$string['printforone'] = 'Print for one student.';
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


$string['selectitems'] = 'Only show selected items. (All if none selected)';
$string['selectitems_help'] = 'If you want to use Blended only for grading a number of items, you can select here which ones are activated.';


//Files help

?>
