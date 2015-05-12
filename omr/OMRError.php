<?php

class OMRError extends Exception {
	
	const LOG_FILE_DOES_NOT_EXIST = 1;
	const LOG_FILE_IS_EMPTY = 2;	
	const RESULTS_FILE_DOES_NOT_EXIST = 3;
	const RESULTS_FILE_IS_EMPTY = 4;
	const FIELDS_FILE_DOES_NOT_EXIST = 5;
	const FIELDS_FILE_IS_EMPTY = 6;	
	const NO_OUTPUT_IMAGE = 7;
	const OMRPROCESS_FAILED = 8;
	const FIELDS_FILE_ALREADY_EXISTS = 9;
	
	var $status=array();
	var $details=array();
	
	function __construct ($status, $code)
	{	
		$this->addStatus($status);
		parent::__construct(join('<br>',$this->getStatus()), $code);
	}
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
		$this->status[]=$statusline;
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