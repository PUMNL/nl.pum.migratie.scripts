<?php
require_once('./baseclass.php'); // BASECLASS
class contacts extends baseclass {
	
	public $resultSet, $contactRow, $contactParams, $addressParams, $contactIdentifier, $contactSubType; // GLOBALS
	
	public function __construct() {
		
		/* Initialize module */
		echo "Start module: Contacts - ".date("h:i:s")." \r\n";
		parent::baseclass(); // Fetch dependencies
		$this->resultSet = $this->dbAdapter->query("SELECT * FROM pum_conversie_person WHERE `status` NOT IN ('Exit', 'Rejected')"); // Fetch contacts
		$this->fetchCustom();
		$this->migration();
		echo "End module: Contacts - ".date("h:i:s")." \r\n";
		
	}
	
	public function fetchCustom() {
		
		/* Fetch all custom fields and data */
		try {
			// Subcontact-types
			$this->contactSubType 					= new stdClass();
			$this->contactSubType->expert 			= civicrm_api3('ContactType', 'getsingle', array("name" => "Expert"));
			$this->contactSubType->staffmember 		= civicrm_api3('ContactType', 'getsingle', array("name" => "Staffmember"));
			$this->customFields 					= new stdClass();
			// Additional Information
			$this->customFields->additionalGroup	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Additional_Data"));
			$this->customFields->prins_unid			= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_Unique_ID", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->prins_shortname	= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_Shortname", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->initials			= civicrm_api3('CustomField', 'getsingle', array("name" => "Initials", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->marital_status		= civicrm_api3('CustomField', 'getsingle', array("name" => "Marital_Status", "custom_group_id" => $this->customFields->additionalGroup['id']));
			// Bank Information
			$this->customFields->bankGroup			= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Bank_Information"));
			$this->customFields->b_number			= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Account_Number", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accname			= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accadd			= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_address", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acczip			= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_postal_code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccity			= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_city", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccountry		= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_country", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iso				= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_ISO_Country_Code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_bic				= civicrm_api3('CustomField', 'getsingle', array("name" => "BIC_Swiftcode", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iban				= civicrm_api3('CustomField', 'getsingle', array("name" => "IBAN_nummer", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_name				= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_city				= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_City", "custom_group_id" => $this->customFields->bankGroup['id']));
			// Passport Information
			$this->customFields->passportGroup		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Passport_Information"));
			$this->customFields->pp_firstname		= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_number			= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Number", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_expire_date		= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Valid_until", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_nationality		= civicrm_api3('CustomField', 'getsingle', array("name" => "Nationality", "custom_group_id" => $this->customFields->passportGroup['id']));
			// Medical Information
			$this->customFields->medicalGroup		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Medical_Information"));
			$this->customFields->medCompany			= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Company", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medNumber			= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracticer		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Name", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracAddress		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Address", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPostal		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Postal_Code", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCity		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_City", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCountry		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Country", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPhone		= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Phone_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			// Languages
			$this->customFields->languageGroup		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Languages"));
			$this->customFields->language			= civicrm_api3('CustomField', 'getsingle', array("name" => "Language", "custom_group_id" => $this->customFields->languageGroup['id']));
			$this->customFields->level				= civicrm_api3('CustomField', 'getsingle', array("name" => "Level", "custom_group_id" => $this->customFields->languageGroup['id']));
			// Relationship-types
			$this->relationshipTypes				= new stdClass();
			$this->relationshipTypes->cc			= civicrm_api3('RelationshipType', 'getsingle', array("name_a_b" => "Case Coordinator is", "name_b_a" => "Case Coordinator"));
			// Groups
			$this->groups							= new stdClass();
			$this->groups->active					= civicrm_api3('Group', 'getsingle', array("title" => "Group Active"));
			$this->groups->restriction				= civicrm_api3('Group', 'getsingle', array("title" => "Subgroup restriction"));
			$this->groups->former					= civicrm_api3('Group', 'getsingle', array("title" => "Group former"));
			$this->groups->candidate				= civicrm_api3('Group', 'getsingle', array("title" => "Group Candidate"));
			$this->groups->magazine					= civicrm_api3('Group', 'getsingle', array("title" => "Group PUM Magazine"));
		} catch (Exception $e) {
			die ($e);
		}

	}
	
	public function migration() {
		
		/* This is where the magic happens! */
		
		// Check if we have valid query data
		if(!$this->resultSet || is_null($this->resultSet)) die("Query failed - Exit script \r\n");
		
		// Start loop!
		while($this->contactRow = $this->resultSet->fetch_assoc()){
			
			// Store contact data in array
			$this->contactParams = array(
				'contact_type' 											=> 'individual',
				'contact_sub_type' 										=> $this->determineContactSubType(),
				'first_name' 											=> $this->contactRow['firstname'],
				'middle_name' 											=> $this->contactRow['infix'],
				'last_name' 											=> $this->contactRow['surname'],
				'gender_id' 											=> ($this->contactRow['gender'] == "M") ? "2" : "1",
				'birth_date' 											=> ($this->contactRow['datebirth'] != "0000-00-00") ? $this->contactRow['datebirth'] : NULL,
				'custom_'.$this->customFields->prins_unid['id'] 		=> $this->contactRow['unid'],
				'custom_'.$this->customFields->prins_shortname['id'] 	=> $this->contactRow['shortname'],
				'custom_'.$this->customFields->initials['id'] 			=> $this->contactRow['initials'],
				'custom_'.$this->customFields->marital_status['id'] 	=> $this->contactRow['maritalstatus']
			);

			/* Register bank account for contact */
			$this->registerBankAccount();
			
			/* Register bank account for contact */
			$this->registerPassportInformation();
			
			/* Register medical information */
			$this->registerMedicalInformation();
			
			// Insert contact 
			if($this->CIVIAPI->Contact->Create($this->contactParams)) {
				
				// Set contact identifier
				$this->contactIdentifier = $this->CIVIAPI->lastResult->id;
				
				/* Register address if available */
				if(!empty($this->contactRow['addresshome']) AND !empty($this->contactRow['cityhome'])) $this->registerAddress();
				
				/* Register all language skills for contact */
				$this->registerLanguages();
				
				/* Register E-mailadresses */
				$this->registerEmail();
				
				/* Register Telephones */
				$this->registerTelephone();
				
				/* Forge CC Relationship */
				$this->forgeRelationCC();
				
				/* If contact is expert then add to proper group */
				if($this->contactParams['contact_sub_type'] == $this->contactSubType->expert['name']) $this->addToGroup();
				
				
			} else {
				
				// Creating contact failed
				echo "Saving contact ".$this->contactRow['unid']." failed\r\n";
				echo $this->CIVIAPI->errorMsg()."\r\n";
				
			}
			
		}
		
	}
	
	private function determineContactSubType() {

		/* This function determines if a contact should have a contact sub type */
		
		// Staffmember and Expert query
		$_staffmember	= $this->dbAdapter->query("SELECT 1 FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND `role` REGEXP 'CEO|PR|CFO|P&O|FA|M&C|MGT|Secr|PrOf|ProfTrain|IT'");
		$_expert		= $this->dbAdapter->query("SELECT 1 FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND `role` REGEXP 'Expert|SC|CC|LR'");
		
		if($_staffmember->num_rows > 0 && $_expert->num_rows > 0) {
			// Staffmember and Expert
			return array($this->contactSubType->staffmember['name'], $this->contactSubType->expert['name']);
		} else if($_staffmember->num_rows > 0) {
			// Staffmember only
			return $this->contactSubType->staffmember['name'];
		} else if($_expert->num_rows > 0) {
			// Expert only
			return $this->contactSubType->expert['name'];
		} else {
			// No roles found
			return null;
		}
	}
	
	private function registerBankAccount() {
		
		/* This function registers the bank accounts */
		
		if(!empty($this->contactRow['BankAccountNo'])) $this->contactParams['custom_'.$this->customFields->b_number['id']] = $this->contactRow['BankAccountNo'];
		if(!empty($this->contactRow['BankAccountholderName'])) $this->contactParams['custom_'.$this->customFields->b_accname['id']] = $this->contactRow['BankAccountholderName'];
		if(!empty($this->contactRow['BankAccountholderAddress'])) $this->contactParams['custom_'.$this->customFields->b_accadd['id']] = $this->contactRow['BankAccountholderAddress'];
		if(!empty($this->contactRow['BankAccountholderZIP'])) $this->contactParams['custom_'.$this->customFields->b_acczip['id']] = $this->contactRow['BankAccountholderZIP'];
		if(!empty($this->contactRow['BankAccountholderCity'])) $this->contactParams['custom_'.$this->customFields->b_acccity['id']] = $this->contactRow['BankAccountholderCity'];
		if(!empty($this->contactRow['BankAccountholderCountry'])) $this->contactParams['custom_'.$this->customFields->b_acccountry['id']] = $this->contactRow['BankAccountholderCountry'];
		if(!empty($this->contactRow['BankCountryISOcode'])) $this->contactParams['custom_'.$this->customFields->b_iso['id']] = $this->contactRow['BankCountryISOcode'];
		if(!empty($this->contactRow['BicSwift'])) $this->contactParams['custom_'.$this->customFields->b_bic['id']] = $this->contactRow['BicSwift'];
		if(!empty($this->contactRow['IBAN'])) $this->contactParams['custom_'.$this->customFields->b_iban['id']] = $this->contactRow['IBAN'];
		if(!empty($this->contactRow['BankName'])) $this->contactParams['custom_'.$this->customFields->b_name['id']] = $this->contactRow['BankName'];
		if(!empty($this->contactRow['BankCity'])) $this->contactParams['custom_'.$this->customFields->b_city['id']] = $this->contactRow['BankCity'];
		
	}	
	
	private function registerMedicalInformation() {
		
		/* This function registers the medical information for contact */
		
		if(!empty($this->contactRow['HealthInsurance'])) $this->contactParams['custom_'.$this->customFields->medCompany['id']] = $this->contactRow['HealthInsurance'];
		if(!empty($this->contactRow['PolicyNumber'])) $this->contactParams['custom_'.$this->customFields->medNumber['id']] = $this->contactRow['PolicyNumber'];
		if(!empty($this->contactRow['GPName'])) $this->contactParams['custom_'.$this->customFields->medPracticer['id']] = $this->contactRow['GPName'];
		if(!empty($this->contactRow['GPAddress'])) $this->contactParams['custom_'.$this->customFields->medPracAddress['id']] = $this->contactRow['GPAddress'];
		if(!empty($this->contactRow['GPZipCode'])) $this->contactParams['custom_'.$this->customFields->medPracPostal['id']] = $this->contactRow['GPZipCode'];
		if(!empty($this->contactRow['GPCity'])) $this->contactParams['custom_'.$this->customFields->medPracCity['id']] = $this->contactRow['GPCity'];
		if(!empty($this->contactRow['GPCountry'])) $this->contactParams['custom_'.$this->customFields->medPracCountry['id']] = $this->contactRow['GPCountry'];
		if(!empty($this->contactRow['GPTelNumber'])) $this->contactParams['custom_'.$this->customFields->medPracPhone['id']] = "+".$this->contactRow['GPTelCountry'].$this->contactRow['GPTelArea'].$this->contactRow['GPTelNumber'];
		
		
	}
	
	private function registerPassportInformation() {
		
		/* This function registers the contact passport information */
		
		if(!empty($this->contactRow['passportname'])) $this->contactParams['custom_'.$this->customFields->pp_firstname['id']] = $this->contactRow['passportname'];
		if(!empty($this->contactRow['PassportNumber'])) $this->contactParams['custom_'.$this->customFields->pp_number['id']] = $this->contactRow['PassportNumber'];
		if(!empty($this->contactRow['PassportExpiration']) && ($this->contactRow['PassportExpiration'] == "0000-00-00 00:00:00" || $this->contactRow['PassportExpiration'] == "0001-00-00 00:00:00")) $this->contactParams['custom_'.$this->customFields->pp_expire_date['id']] = $this->contactRow['PassportExpiration'];
		if(!empty($this->contactRow['nationality'])) $this->contactParams['custom_'.$this->customFields->pp_nationality['id']] = $this->contactRow['nationality'];
		
	}
	
	private function registerLanguages() {
		
		/* This function registers the contact language skills */
		
		// Fetch all language records
		$_languageRecords = $this->dbAdapter->query("
			SELECT *  FROM `pum_conversie_cv_language` 
			WHERE `person_unid` = '".$this->contactRow['unid']."'
			AND (
					(`speakingskill` REGEXP 'Good|Very Good')
					AND
					(`readingskill` REGEXP 'Good|Very Good' OR `writingskill` REGEXP 'Good|Very Good')
			)
			GROUP BY `language`
		");
		
		// Check if we have any records
		if(is_object($_languageRecords) AND $_languageRecords->num_rows > 0) {
			
			// Loop trough the records
			while($_langRecord = $_languageRecords->fetch_assoc()) {
				
				// For English we have to divide the language into three separate language records
				if($_langRecord['language'] == "English") {
				
					try {
						
						// Register all English language skill levels
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Speaking", "custom_".$this->customFields->skill_level['id'] => trim($_langRecord['speakingskill'])));
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Writing", "custom_".$this->customFields->skill_level['id'] => trim($_langRecord['writingskill'])));
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Reading", "custom_".$this->customFields->skill_level['id'] => trim($_langRecord['readingskill'])));					
						
					} catch (Exception $e) {
						
						// Something went wrong, report
						echo "English language skill failed to save for contact ".$this->contactRow['unid']." \r\n";
						echo $e."\r\n";
						
					}
				
				} else {
				
					try {
						
						// Register other than English language with general skill of good
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => $_langRecord['language'], "custom_".$this->customFields->skill_level['id'] => "Good"));
					
					} catch (Exception $e) {
						
						// Something went wrong, report
						echo $_langRecord['language']." language skill failed to save for contact ".$this->contactRow['unid']." \r\n";
						echo $e."\r\n";
						
					}
				
				}
				
			}
			
		}
		
	}
	
	private function registerAddress() {
		
		// Store address data in array
		$this->addressParams = array(
			'contact_id' 		=> $this->contactIdentifier,
			'location_type_id' 	=> 1,
			'is_primary' 		=> 1,
			'street_address' 	=> $this->contactRow['addresshome'],
			'city' 				=> $this->contactRow['cityhome'],
			'postal_code' 		=> $this->contactRow['ziphome'],
			'country_id' 		=> $this->determineCountry()
		);
		
		// Insert contact address
		if(!$this->CIVIAPI->Address->Create($this->addressParams)) {
			
			// Create address for contact
			echo "Saving address for contact ".$this->contactRow['unid']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";						
			
		}
		
	}
	
	private function determineCountry() {
	
		/* This function determines the CIVICRM country identifier for the given country name */
		
		// Fetch CIVICRM country
		$_countryObject = $this->cdbAdapter->query("SELECT * FROM `civicrm_country` WHERE `name` LIKE '%".$this->contactRow['countryhome']."%'");
		
		// Check if we have an object
		if($_countryObject && !is_null($_countryObject) && is_object($_countryObject) && $_countryObject->num_rows == 1) {
			
			// Convert object to single array
			$_countryIdentifier = $_countryObject->fetch_assoc();
			
			// Return identifier
			return $_countryIdentifier['id'];
			
		} else {
			
			// No country found
			return null;
			
		}
	
	}
	
	private function registerEmail() {
		
		/* This function registers the e-mailaddress of a contact */
		
		try {
		
			if(!empty($this->contactRow['Email1'])) civicrm_api3('Email','Create', array('contact_id' => $this->contactIdentifier, 'email' => $this->contactRow['Email1'], 'location_type_id' => 1, 'is_primary' => 1));
			if(!empty($this->contactRow['Email2'])) civicrm_api3('Email','Create', array('contact_id' => $this->contactIdentifier, 'email' => $this->contactRow['Email2'], 'location_type_id' => 2));
		
		} catch (Exception $e) {
		
			echo "E-mail registration failed for contact ".$this->contactIdentifier."\r\n";
			echo $e;
		
		}
		
	}
	
	private function registerTelephone() {
		
		/* This function registers the telephone numbers of a contact */
		
				
		try {
		
			if(!empty($this->contactRow['PhoneCountry1']) && !empty($this->contactRow['PhoneArea1']) && !empty($this->contactRow['PhoneNumber1'])) civicrm_api3('Phone','Create', array('contact_id' => $this->contactIdentifier, 'phone' => "+".$this->contactRow['PhoneCountry1'].$this->contactRow['PhoneArea1'].$this->contactRow['PhoneNumber1'], 'location_type_id' => 1, 'is_primary' => 1, 'phone_type_id' => 1));
			if(!empty($this->contactRow['PhoneCountry2']) && !empty($this->contactRow['PhoneArea2']) && !empty($this->contactRow['PhoneNumber2'])) civicrm_api3('Phone','Create', array('contact_id' => $this->contactIdentifier, 'phone' => "+".$this->contactRow['PhoneCountry2'].$this->contactRow['PhoneArea2'].$this->contactRow['PhoneNumber2'], 'location_type_id' => 1, 'phone_type_id' => 1));
			if(!empty($this->contactRow['MobileCountry1']) && !empty($this->contactRow['MobileArea1']) && !empty($this->contactRow['MobileNumber1'])) civicrm_api3('Phone','Create', array('contact_id' => $this->contactIdentifier, 'phone' => "+".$this->contactRow['MobileCountry1'].$this->contactRow['MobileArea1'].$this->contactRow['MobileNumber1'], 'location_type_id' => 1, 'phone_type_id' => 2));
			if(!empty($this->contactRow['MobileCountry2']) && !empty($this->contactRow['MobileArea2']) && !empty($this->contactRow['MobileNumber2'])) civicrm_api3('Phone','Create', array('contact_id' => $this->contactIdentifier, 'phone' => "+".$this->contactRow['MobileCountry2'].$this->contactRow['MobileArea2'].$this->contactRow['MobileNumber2'], 'location_type_id' => 1, 'phone_type_id' => 2));
			
		
		} catch (Exception $e) {
		
			echo "E-mail registration failed for contact ".$this->contactIdentifier."\r\n";
			echo $e;
		
		}
		
	}	
	
	private function forgeRelationCC() {
	
		/* This function forges a relation ship for the contact based on CC records */
		
		// Fetch CC records
		$_ccRecords = $this->dbAdapter->query("SELECT `country` FROM `pum_conversie_person_cc_country` WHERE `person_unid` = '".$this->contactRow['unid']."'");
		
		// Check if contact has a cc records
		if(is_object($_ccRecords) AND $_ccRecords->num_rows > 0) {
			
			// Loop trough all CC records
			while($_ccRecord = $_ccRecords->fetch_assoc()) {
				
				try {
					
					// Fetch country contact
					$_country_contact = civicrm_api3('Contact','getsingle', array('organization_name' => trim($_ccRecord['country'])));
					
					// Forge relationship between country and case coordinate
					civicrm_api3('Relationship','Create',array('contact_id_a' => $_country_contact['id'], 'contact_id_b' => $this->contactIdentifier, 'relationship_type_id' => $this->relationshipTypes->cc['id']));
				
				} catch (Exception $e) {
				
					echo "Couldn't find country or relationship forging failed for country ".trim($_ccRecord['country'])."\r\n";
					echo $e;
				
				}
			
			}
		
		}
		
	}
	
	private function addToGroup() {
	
		/* This function adds the person via status to the proper group */

		try {

			switch($this->contactRow['status']) {
			
				case "Active": 
				case "Temporarily inactive": 
				case "Active for one proje": 
				case "Special occasions on": 
						civicrm_api3('GroupContact','Create', array('group_id' => $this->groups->restriction['id'], 'contact_id' => $this->contactIdentifier));
						civicrm_api3('GroupContact','Create', array('group_id' => $this->groups->active['id'], 'contact_id' => $this->contactIdentifier));
						$this->cdbAdapter->query("UPDATE `civicrm_subscription_history` SET `date` = '".($this->contactRow['ActivationDate'] != "0000-00-00") ? $this->contactRow['ActivationDate'] : "2011-01-01"."' WHERE `contact_id` = '".$this->contactIdentifier."' AND `group_id` = '".$this->groups->active['id']."'");
						break;
				case "New": 
						civicrm_api3('GroupContact','Create', array('group_id' => $this->groups->candidate['id'], 'contact_id' => $this->contactIdentifier));
						break;
				case "Exit but interested": 
						civicrm_api3('GroupContact','Create', array('group_id' => $this->groups->former['id'], 'contact_id' => $this->contactIdentifier));
						civicrm_api3('GroupContact','Create', array('group_id' => $this->groups->magazine['id'], 'contact_id' => $this->contactIdentifier));
						break;
						
			
			}
		
		} catch (Exception $e) {
			
			// Adding contact to group failed
			echo "Adding contact ".$this->contactIdentifier." to group failed\r\n";
			echo $e."\r\n";
		
		}
	
	}
	
}

new contacts;