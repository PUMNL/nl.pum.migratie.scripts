<?php
class baseclass {

	public $CIVIAPI, $dbAdapter, $cdbAdapter; // GLOBALS
	
	public function baseclass() {
	
		// CIVICRM API Connector
		require_once('../sites/all/modules/civicrm/api/class.api.php');
		$this->CIVIAPI = new civicrm_api3(array(
			'conf_path' => '../sites/default'
		));
		
		// Database connector PUM-Database
		$this->dbAdapter = new mysqli("localhost", "user", "pass", "pum_data");
		if ($this->dbAdapter->connect_errno) die("Connection with PUM-Database failed!");
		
		// Database connector CiviCRM Database
		$this->cdbAdapter = new mysqli("localhost", "user", "pass", "pum_civicrm");
		if ($this->cdbAdapter->connect_errno) die("Connection with PUM-CiviCRM-Database failed");
		
	}
	
}