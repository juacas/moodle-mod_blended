<?php

class PDFError extends Exception {
	
	const QUIZ_IS_EMPTY = 1;
	const QUESTIONS_NOT_FOUND = 2;	
	const COULD_NOT_LOAD_QUESTION_OPTIONS = 3;	
	const COULD_NOT_RESTORE_QUESTION_SESSIONS = 4;	

}
?>