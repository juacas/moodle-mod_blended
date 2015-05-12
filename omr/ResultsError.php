<?php

class ResultsError extends Exception {
	
	const COULD_NOT_CREATE_IMAGE_RECORD = 1;
	const COULD_NOT_CREATE_RESULT_RECORD = 2;
	const COULD_NOT_UPDATE_BLENDED_RESULTS = 3;
	const TABLE_BLENDED_RESULTS_IS_EMPTY = 4;
	const BLENDED_ATTEMPT_REG_DOES_NOT_EXIST = 5;

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