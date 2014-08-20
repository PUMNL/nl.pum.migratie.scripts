<?php
require_once('./baseclass.php'); // BASECLASS
class fillLists extends baseclass {
	
	public function __construct() {
	
		/* Initialize module */
		echo "Start module: Fill Lists - ".date("h:i:s")." \r\n";
		parent::baseclass(); // Fetch dependencies
		$this->fetchCustom();
		//$this->nationalityList();
		//$this->languageList();
		$this->countryList();
		echo "End module: Fill Lists - ".date("h:i:s")." \r\n";
	}
	
	public function fetchCustom() {
		
		/* Fetch all custom fields and data */
		try {
			// STD Classes
			$this->customFields 							= new stdClass();
			$this->optionGroups 							= new stdClass();
			$this->contactSubType 							= new stdClass();
			// Passport Information
			$this->customFields->passportGroup				= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Passport_Information"));
			$this->customFields->pp_nationality				= civicrm_api3('CustomField', 'getsingle', array("name" => "Nationality", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->optionGroups->nationalities				= civicrm_api3('OptionValue', 'get', array("option_group_id" => $this->customFields->pp_nationality['option_group_id']));
			// Languages
			$this->customFields->languageGroup				= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Languages"));
			$this->customFields->language					= civicrm_api3('CustomField', 'getsingle', array("name" => "Language", "custom_group_id" => $this->customFields->languageGroup['id']));
			$this->optionGroups->languages					= civicrm_api3('OptionValue', 'get', array("option_group_id" => $this->customFields->language['option_group_id']));
			// Subcontact-types
			$this->contactSubType->country 					= civicrm_api3('ContactType', 'getsingle', array("name" => "Country"));
		} catch (Exception $e) {
			die ($e);
		}
		
	}
	
	public function nationalityList() {
				
		/* This function truncates and then fills the list of nationalities */
		
		// Delete all options from the nationality list
		$this->cdbAdapter->query("DELETE FROM `civicrm_option_value` WHERE `option_group_id` = ".$this->customFields->pp_nationality['option_group_id']);	
		
		// Fetch all unique nationalities
		$_nationalities = $this->dbAdapter->query("SELECT DISTINCT(`nationality`) FROM `pum_conversie_person`");
		
		// Check if we did find any nationalities
		if(is_object($_nationalities) AND $_nationalities->num_rows > 0){
			
			// Loop trough all nationalities
			while($_nationality = $_nationalities->fetch_assoc()) {
			
				// Register all nationality options
				if(!empty($_nationality['nationality'])) civicrm_api3('OptionValue', 'create', array("option_group_id" => $this->customFields->pp_nationality['option_group_id'], "name" => ucfirst($_nationality['nationality']), "label" => ucfirst($_nationality['nationality']), "value" => ucfirst($_nationality['nationality'])));
				
			}
			
		}
		
	}
	
	public function languageList() {
				
		/* This function truncates and then fills the list of languages */
		
		// Delete all options from the language list
		$this->cdbAdapter->query("DELETE FROM `civicrm_option_value` WHERE `option_group_id` = ".$this->customFields->language['option_group_id']);	
		
		// Fetch all unique languages
		$_languages = $this->dbAdapter->query("SELECT DISTINCT(`language`) FROM `pum_conversie_cv_language`");
		
		// Check if we did find any languages
		if(is_object($_languages) AND $_languages->num_rows > 0){
			
			// Loop trough all languages
			while($_language = $_languages->fetch_assoc()) {
			
				// Exception for English language
				if($_language['language'] == "English") {
				
					// Register 3 variants from the English language
					civicrm_api3('OptionValue', 'create', array("option_group_id" => $this->customFields->language['option_group_id'], "name" => "English Speaking", "label" => "English Speaking", "value" => "English Speaking"));
					civicrm_api3('OptionValue', 'create', array("option_group_id" => $this->customFields->language['option_group_id'], "name" => "English Reading", "label" => "English Reading", "value" => "English Reading"));
					civicrm_api3('OptionValue', 'create', array("option_group_id" => $this->customFields->language['option_group_id'], "name" => "English Writing", "label" => "English Writing", "value" => "English Writing"));
				
				} else {
					
					// Register all languages
					if(!empty($_language['language'])) civicrm_api3('OptionValue', 'create', array("option_group_id" => $this->customFields->language['option_group_id'], "name" => ucfirst($_language['language']), "label" => ucfirst($_language['language']), "value" => ucfirst($_language['language'])));
					
				}
				
			}
			
		}
		
	}

	public function countryList() {

		/* This function creates all countries as contact */
		
		// Fetch all countries
		$_countryList = $this->cdbAdapter->query("SELECT * FROM `civicrm_country`");
		
		// Loop trough all countries
		while($_country = $_countryList->fetch_assoc()) {

		try {
			
				// Register country
				if(strlen(trim($_country['name'])) > 3) civicrm_api3('Contact','Create',array('contact_type' => 'organization', 'contact_sub_type' => $this->contactSubType->country['name'], 'organization_name' => $_country['name']));
				
			} catch (Exception $e) {

				// Country registration failed
				die($e);
			
			}			
			
		}
		
	}	
		
}
new fillLists;