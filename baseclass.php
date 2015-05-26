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
		$this->dbAdapter = new mysqli("SERVER", "USER", "PASS", "DB");
		if ($this->dbAdapter->connect_errno) die("Connection with PUM-Data-Database failed!");
		$this->dbAdapter->set_charset("utf8");

		// Database connector CiviCRM Database
		$this->cdbAdapter = new mysqli("132.160.150.23", "USER", "PASS", "DB");
		if ($this->cdbAdapter->connect_errno) die("Connection with PUM-CiviCRM-Database failed");
		$this->cdbAdapter->set_charset("utf8");
		
	}
	
}
