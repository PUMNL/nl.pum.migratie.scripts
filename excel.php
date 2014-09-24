<?php
require_once('./baseclass.php'); // BASECLASS
class excel extends baseclass {
	
	public $resultSet, $excelRow, $organizationParams, $currentOrganization, $employeeParams; // GLOBALS
	
	public function __construct() {
		
		/* Initialize module */
		echo "Start module: Excel - ".date("h:i:s")." \r\n";
		parent::baseclass(); // Fetch dependencies
		$this->resultSet = $this->edbAdapter->query("SELECT * FROM `contacten`"); // Fetch contacts
		$this->fetchCustom();
		$this->migration();
		echo "End module: Excel - ".date("h:i:s")." \r\n";
		
	}
	
	public function fetchCustom() {
		
		/* Fetch all custom fields and data */
		try {
			// Subcontact-types
			$this->contactSubType 						= new stdClass();
			$this->contactSubType->corporate_relation	= civicrm_api3('ContactType', 'getsingle', array("name" => "Corporate_Relation"));
			$this->customFields 						= new stdClass();
			$this->relationships 						= new stdClass();
			$this->relationships->employee				= civicrm_api3('RelationshipType', 'getsingle', array("name_a_b" => "Employee of", "name_b_a" => "Employer of"));
		} catch (Exception $e) {
			die ($e);
		}

	}
	
	public function migration() {
		
		/* This is where the magic happens! */
		
		// Check if we have valid query data
		if(!$this->resultSet || is_null($this->resultSet)) die("Query failed - Exit script \r\n");
		
		// Start loop!
		while($this->excelRow = $this->resultSet->fetch_assoc()){
			
			// Check if we have valid organization data, if so, create organization with contact-sub-type "Corporate Relation"
			// Requirements to be met is a valid organization name in column "F1"
			if(strlen($this->excelRow['F1']) > 1) $this->registerOrganization();
			
			// Check if we have valid contact data, if so, create contact of type individual
			// Requirements to bet is a valid first name or initials
			if(!empty($this->excelRow['F28']) || !empty($this->excelRow['F29'])) $this->registerContact();
			
		}
		
	}
	
	private function registerOrganization() {
				
		$this->organizationParams = array(
			'organization_name' => utf8_encode($this->excelRow['F1']),
			'contact_type' 		=> 'organization',
			'contact_sub_type'	=> $this->contactSubType->corporate_relation['name']
		);
		
		if(!$this->CIVIAPI->Contact->Create($this->organizationParams)) {
			
			echo "Saving contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
			
		} else {
			
			// If organization creation was successful, then process address, website and phone (if available)
			$this->organizationIdentifier 	= $this->CIVIAPI->lastResult->id;
			if(!empty($this->excelRow['F6']) && !empty($this->excelRow['F7']) && !empty($this->excelRow['F8']) && !empty($this->excelRow['F9']))$this->registerAddress();
			if(preg_match("@^(http\:\/\/|https\:\/\/)?([a-z0-9][a-z0-9\-]*\.)+[a-z0-9][a-z0-9\-]*$@i", $this->excelRow['F2'])) $this->registerWebsite();
			if(strlen($this->excelRow['F14']) > 5) $this->registerTelephone();
			if(strlen($this->excelRow['F15']) > 5) $this->registerEmail();
			//if(!empty($this->excelRow['F18'])) $this->forgeAccHolderPumRelationship($_contactName)
			
		}
		
	}
	
	private function registerAddress() {
		
		$_addressParams = array(
			'contact_id' 		=> $this->organizationIdentifier,
			'location_type_id' 	=> 2, // Work
			'is_primary' 		=> 1, // First address registration will be the primary
			'street_address' 	=> $this->excelRow['F6'].$this->excelRow['F7'],
			'postal_code' 		=> $this->excelRow['F8'],
			'city' 				=> ucfirst(strtolower($this->excelRow['F9'])),
			'country_id' 		=> 1152
		);
		
		if(!$this->CIVIAPI->Address->Create($_addressParams)) {
			echo "Saving address for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		}
		
	}
	
	private function registerWebsite () {
	
		$_websiteParams = array(
			'contact_id' 		=> $this->organizationIdentifier,
			'location_type_id' 	=> 2, // Work
			'url' 				=> $this->excelRow['F2']
		);
		
		if(!$this->CIVIAPI->Website->Create($_websiteParams)) {
			echo "Saving website for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		}
	
	}
	
	private function registerTelephone() {
	
		$_telephoneParams = array(
			'contact_id' 		=> $this->organizationIdentifier,
			'location_type_id' 	=> 2, // Work
			'phone' 			=> $this->excelRow['F14']
		);
		
		if(!$this->CIVIAPI->Phone->Create($_telephoneParams)) {
			echo "Saving telephone for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		}
	
	}
	
	private function registerEmail() {
	
		$_emailParams = array(
			'contact_id' 		=> $this->organizationIdentifier,
			'location_type_id' 	=> 2, // Work
			'email' 			=> $this->excelRow['F15']
		);
		
		if(!$this->CIVIAPI->Email->Create($_emailParams)) {
			echo "Saving e-mail address for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		}
	
	}
	
	private function registerContact() {
		
		$_firstname = (empty($this->excelRow['F29'])) ? $this->excelRow['F28'] : $this->excelRow['F29'];
		$_gender 	= ($this->excelRow['F25'] == "mevrouw") ? 1 : 2;
		
		$this->organizationParams = array(
			'first_name' 		=> utf8_encode($_firstname),
			'middle_name' 		=> utf8_encode($this->excelRow['F30']),
			'last_name' 		=> utf8_encode($this->excelRow['F31']),
			'contact_type' 		=> 'individual',
			'gender'			=> $_gender
		);
		
		if($this->CIVIAPI->Contact->Create($this->organizationParams)) {
			
			$this->forgeEmployeeRelationship($this->CIVIAPI->lastResult->id);
			
		} else {
		
			echo "Saving contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
			
		}
		
	}

	private function forgeEmployeeRelationship($_contactID) {
		
		$_relationshipParams = array(
			'contact_id_a' => $_contactID,
			'contact_id_b' => $this->organizationIdentifier,
			'relationship_type_id' => $this->relationships->employee['id'],
			'description' => $this->excelRow['F34']
		);
		
		if(!$this->CIVIAPI->Relationship->Create($_relationshipParams)){
		
			echo "Forging relationship-employee for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		
		}
		
	}
	
	private function forgeAccHolderPumRelationship($_contactName) {
		
		/*
		try {
		
			$_contact = civicrm_api3('Contact','getsingle',array());
			
		} 
		
		$_relationshipParams = array(
			'contact_id_a' => $_contactID,
			'contact_id_b' => $this->organizationIdentifier,
			'relationship_type_id' => $this->relationships->accHolderPum['id']
		);
		
		if(!$this->CIVIAPI->Relationship->Create($_relationshipParams)){
		
			echo "Forging relationship-accholderpum for contact ".$this->excelRow['id']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		
		}
		*/
	
	}
	
}

new excel;