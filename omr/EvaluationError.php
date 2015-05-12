<?php

class EvaluationError extends Exception {

	const USERID_IS_EMPTY = 1;
	const USER_NOT_IN_THIS_COURSE = 2;
	const CREATE_QUIZ_ATTEMPT_ERROR = 3;
	const FINISH_QUIZ_ATTEMPT_ERROR = 4;
	const MISSING_BLENDED_ATTEMPT = 5;
	const QUIZ_DOES_NOT_EXIST = 6;
	
	var $status=array();
	var $details=array();
	
	function loadInfoStatus ($jobid)
	{
		$status = get_field($table='blended_scans', $retvalue = 'infostatus', $field='id', $value=$jobid);
		
		$this->status[] = $status;
	}
	function loadDetails ($jobid)
	{
		$details = get_field($table='blended_scans', $retvalue = 'infodetails', $field='id', $value=$jobid);
		
		$this->details[] = $details;
	}
	/**
	 * 
	 * @param $statusline string or array of strings
	 */
	function addStatus($statusline)
	{
		if (is_array($statusline))
		{
			array_merge($this->status,$statusline);
		}
		else
		$this->status[]='<BR>'.$statusline;
	}
	/**
	 * 
	 * @param $detail string or array of strings
	 */
	function addDetails($detail)
	{
		if (is_array($detail))
		{
			array_merge($this->details,$detail);
		}
		else
		$this->details[]='<BR>'.$detail;
	}
	
	function getStatus()
	{
		return $this->status;
	}
	
	function getDetails()
	{
		return $this->details;
	}

}
?>