<?php
require_once('./baseclass.php');
class contacts extends baseclass {

	public $contactSubType, $customFields, $groups, $tags, $contactRow, $contactParams, $contactIdentifier;

	public function __construct() {
		echo "Start module: Contacts - ".date("h:i:s")." \r\n";
		parent::baseclass();
		$this->fetchCustom();
		$this->migration();
		echo "End module: Contacts - ".date("h:i:s")." \r\n";
	}

	public function fetchCustom() {
		/* Fetch all custom fields and data */
		try {
			// Subcontact-types
			$this->contactSubType 			= new stdClass();
			$this->contactSubType->expert 	= civicrm_api3('ContactType', 'getsingle', array("name" => "Expert"));
			$this->customFields 			= new stdClass();
			// Additional Information
			$this->customFields->additionalGroup	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Additional_Data"));
			$this->customFields->prins_unid			= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_Unique_ID", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->prins_shortname	= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_Shortname", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->initials			= civicrm_api3('CustomField', 'getsingle', array("name" => "Initials", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->marital_status		= civicrm_api3('CustomField', 'getsingle', array("name" => "Marital_Status", "custom_group_id" => $this->customFields->additionalGroup['id']));
			// Bank Information
			$this->customFields->bankGroup		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Bank_Information"));
			$this->customFields->b_number		= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Account_Number", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accname		= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accadd		= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_address", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acczip		= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_postal_code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccity		= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_city", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccountry	= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_country", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iso			= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_ISO_Country_Code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_bic			= civicrm_api3('CustomField', 'getsingle', array("name" => "BIC_Swiftcode", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iban			= civicrm_api3('CustomField', 'getsingle', array("name" => "IBAN_nummer", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_name			= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_city			= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_City", "custom_group_id" => $this->customFields->bankGroup['id']));
			// Passport Information
			$this->customFields->passportGroup			= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Passport_Information"));
			$this->customFields->pp_firstname			= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_partner_lastname	= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name_Partner_Last_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_number				= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Number", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_expire_date			= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Valid_until", "custom_group_id" => $this->customFields->passportGroup['id']));
			// Medical Information
			$this->customFields->medicalGroup	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Medical_Information"));
			$this->customFields->medCompany		= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Company", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medNumber		= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracticer	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Name", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracAddress	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Address", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPostal	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Postal_Code", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCity	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_City", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCountry	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Country", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPhone	= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Phone_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			// Languages
			$this->customFields->languageGroup	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Languages"));
			$this->customFields->language		= civicrm_api3('CustomField', 'getsingle', array("name" => "Language", "custom_group_id" => $this->customFields->languageGroup['id']));			
			// Workhistory
			$this->customFields->workHistoryGroup							= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Workhistory"));
			$this->customFields->name_of_organisation						= civicrm_api3('CustomField', 'getsingle', array("name" => "Name_of_Organisation", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->city										= civicrm_api3('CustomField', 'getsingle', array("name" => "City", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->country									= civicrm_api3('CustomField', 'getsingle', array("name" => "Country", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->years_from									= civicrm_api3('CustomField', 'getsingle', array("name" => "Yeard_From", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->to											= civicrm_api3('CustomField', 'getsingle', array("name" => "To", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->job_title									= civicrm_api3('CustomField', 'getsingle', array("name" => "Job_Title", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->description								= civicrm_api3('CustomField', 'getsingle', array("name" => "Description", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->competences_used_in_this_job				= civicrm_api3('CustomField', 'getsingle', array("name" => "Competences_used_in_this_job", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->responsibilities							= civicrm_api3('CustomField', 'getsingle', array("name" => "Responsibilities", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->countries_visited_in_relation_to_the_job	= civicrm_api3('CustomField', 'getsingle', array("name" => "Countries_visited_in_relation_to_the_job", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			// Expertdata
			$this->customFields->expertDataGroup				= civicrm_api3('CustomGroup', 'getsingle', array("name" => "expert_data"));
			$this->customFields->expert_status					= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_start_date		= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_start_date", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_end_date			= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_end_date", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_reason			= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_reason", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->generic_skills					= civicrm_api3('CustomField', 'getsingle', array("name" => "generic_skills", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->side_activities				= civicrm_api3('CustomField', 'getsingle', array("name" => "side_activities", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->membership_serviceclub			= civicrm_api3('CustomField', 'getsingle', array("name" => "membership_serviceclub", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->name_serviceclub				= civicrm_api3('CustomField', 'getsingle', array("name" => "name_serviceclub", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->first_contact_with_PUM_via		= civicrm_api3('CustomField', 'getsingle', array("name" => "First_contact_with_PUM_via", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->other_contact_with_PUM			= civicrm_api3('CustomField', 'getsingle', array("name" => "Other_contact_with_PUM", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->code_conduct					= civicrm_api3('CustomField', 'getsingle', array("name" => "code_conduct", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->CV_in_Mutation					= civicrm_api3('CustomField', 'getsingle', array("name" => "CV_in_Mutation", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			// Groups
			$this->groups					= new stdClass();
			$this->groups->active			= civicrm_api3('Group', 'getsingle', array("title" => "Active Expert"));
			$this->groups->former			= civicrm_api3('Group', 'getsingle', array("title" => "Former Expert"));
			$this->groups->candidate		= civicrm_api3('Group', 'getsingle', array("title" => "Candidate Expert"));
			$this->groups->magazine			= civicrm_api3('Group', 'getsingle', array("title" => "PUM magazine"));
			$this->groups->representatives	= civicrm_api3('Group', 'getsingle', array("title" => "Representatives"));
			// Tags
			$this->tags																	= new stdClass();
			$this->tags->horticulture_vegetables_fruits_green_glasshouses 				= civicrm_api3('Tag', 'getsingle' array("title" => "Horticulture: vegetables & fruits, green- & glasshouses"));
			$this->tags->horticulture_vegetables_fruits					 				= civicrm_api3('Tag', 'getsingle' array("title" => "Horticulture: vegetables & fruits"));
			$this->tags->agriculture_arable_farming					 					= civicrm_api3('Tag', 'getsingle' array("title" => "Agriculture: arable farming"));
			$this->tags->agriculture_tropical_products					 				= civicrm_api3('Tag', 'getsingle' array("title" => "Agriculture: tropical products"));
			$this->tags->horticulture_flowers_ornamental_plants							= civicrm_api3('Tag', 'getsingle' array("title" => "Horticulture: flowers and ornamental plants"));
			$this->tags->building_materials_supplies_systems							= civicrm_api3('Tag', 'getsingle' array("title" => "Building Materials: supplies & systems"));
			$this->tags->building_development_architecture_design_engineering			= civicrm_api3('Tag', 'getsingle' array("title" => "Building Development: architecture, design & engineering"));
			$this->tags->building_management_contracting_execution_installation			= civicrm_api3('Tag', 'getsingle' array("title" => "Building Management: contracting, execution & installation"));
			$this->tags->business_consultancy_financial_support_services_accountancy	= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: financial support services & accountancy"));
			$this->tags->business_consultancy_ict										= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: ICT"));
			$this->tags->business_consultancy_management_consultancy					= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: management consultancy"));
			$this->tags->business_consultancy_HRM_consultancy							= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: HRM consultancy"));
			$this->tags->business_consultancy_communications_marketing_consultancy		= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: communications & marketing consultancy"));
			$this->tags->business_consultancy_legal_consultancy							= civicrm_api3('Tag', 'getsingle' array("title" => "Business Consultancy: legal consultancy"));
			$this->tags->Business_support_organizations_chambers_associations			= civicrm_api3('Tag', 'getsingle' array("title" => "Business Support Organizations (chambers/associations)"));
		} catch (Exception $e) {
			die ($e);
		}
	}
	
	public function migration() {
		$this->resultSet = $this->dbAdapter->query("SELECT * FROM pum_conversie_person WHERE `status` NOT IN ('Exit', 'Rejected')");
		if(!$this->resultSet) die("Query failed! - ".$this->dbAdapter->error);
		while($this->contactRow = $this->resultSet->fetch_assoc()) {
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
			
			var_dump($this->contactParams);
			continue;
			
			/* Register bank account for contact */
			$this->registerBankAccount();
			/* Register bank account for contact */
			$this->registerPassportInformation();
			/* Register medical information */
			$this->registerMedicalInformation();
			/* Register expert data information */
			$this->registerExpertdata();
			// Insert contact 
			if($this->CIVIAPI->Contact->Create($this->contactParams)) {
				// Set contact identifier
				$this->contactIdentifier = $this->CIVIAPI->lastResult->id;
				/* Register address if available */
				if(!empty($this->contactRow['addresshome']) AND !empty($this->contactRow['cityhome'])) $this->registerAddress();
				/* Register E-mailadresses */
				$this->registerEmail();
				/* Register Telephones */
				$this->registerTelephone();
				/* Register all language skills for contact */
				$this->registerLanguages();
			}	
		}
	}
	
	private function determineContactSubType() {
		/* This function determines if a contact should have a contact sub type */
		$_expert		= $this->dbAdapter->query("SELECT 1 FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND `role` REGEXP 'Expert'");
		if($_expert->num_rows > 0) {
			return $this->contactSubType->expert['name'];
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
		if(!empty($this->contactRow['lastname'])) $this->contactParams['custom_'.$this->customFields->pp_partner_lastname['id']] = $this->contactRow['last_name'];
	}
	
	private function registerExpertdata() {
		/* This function registers all expert data */
		if(!empty($this->contactRow['TODO_FIRST_CONTACT_PUM'])) $this->contactParams['custom_'.$this->customFields->first_contact_with_PUM_via['id']] = $this->contactRow['TODO_FIRST_CONTACT_PUM'];
		if(!empty($this->contactRow['TODO_SIDE_ACTIVITIES'])) $this->contactParams['custom_'.$this->customFields->side_activities['id']] = $this->contactRow['TODO_SIDE_ACTIVITIES'];
		if(!empty($this->contactRow['TODO_PRINS_EXPERT_STATUS'])) {
			if(in_array(array("Temporarily inactive","Active"), $this->contactRow['TODO_PRINS_EXPERT_STATUS'])) {
				$this->contactParams['custom_'.$this->customFields->expert_status['id']] = $this->contactRow['TODO_EXPERT_STATUS'];
			} else if(in_array(array("For one project exclusively","Special occasions"), $this->contactRow['TODO_PRINS_EXPERT_STATUS'])) {
				$this->contactParams['custom_'.$this->customFields->expert_status['id']] = "Active";
				try { civicrm_api3('Activity','Create',array("activity_type_id" => 62, "subject" => $this->contactRow['TODO_PRINS_EXPERT_STATUS'], "status_id" => 2)); } catch (Exception $e) { echo "Creating activity failed for contact: ".$this->contactRow['unid']	}
			}
		}
		
	}
	
	private function registerAddress() {
		/* This function registers the contact address */
		$this->addressParams = array(
			'contact_id' 		=> $this->contactIdentifier,
			'location_type_id' 	=> 1,
			'is_primary' 		=> 1,
			'street_address' 	=> $this->contactRow['addresshome'],
			'city' 				=> $this->contactRow['cityhome'],
			'postal_code' 		=> $this->contactRow['ziphome'],
			'country_id' 		=> $this->determineCountry()
		);
		if(!$this->CIVIAPI->Address->Create($this->addressParams)) {
			echo "Saving address for contact ".$this->contactRow['unid']." failed\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";						
		}
	}
	
	private function determineCountry() {
		/* This function determines the CIVICRM country identifier for the given country name */
		$_countryObject = $this->cdbAdapter->query("SELECT * FROM `civicrm_country` WHERE `name` LIKE '%".$this->contactRow['countryhome']."%'");
		if($_countryObject && !is_null($_countryObject) && is_object($_countryObject) && $_countryObject->num_rows == 1) {
			$_countryIdentifier = $_countryObject->fetch_assoc();
			return $_countryIdentifier['id'];
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
	
	private function registerLanguages() {
		/* This function registers all languages with reading level fair and above */
		$_languageRecords = $this->dbAdapter->query("
			SELECT *  FROM `pum_conversie_cv_language` 
			WHERE `person_unid` = '".$this->contactRow['unid']."'
			AND (`speakingskill` REGEXP 'Fair|Good|Very Good|')
			GROUP BY `language`
		");
		if(is_object($_languageRecords) AND $_languageRecords->num_rows > 0) {
			while($_langRecord = $_languageRecords->fetch_assoc()) {
				if($_langRecord['language'] == "English") {
					try {
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Speaking", "custom_".$this->customFields->level['id'] => trim($_langRecord['speakingskill'])));
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Writing", "custom_".$this->customFields->level['id'] => trim($_langRecord['writingskill'])));
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => "English Reading", "custom_".$this->customFields->level['id'] => trim($_langRecord['readingskill'])));					
					} catch (Exception $e) {
						echo "English language skill failed to save for contact ".$this->contactRow['unid']." \r\n";
						echo $e."\r\n";
					}
				} else {
					try {
						civicrm_api3('CustomValue', 'create', array("id" => -1, "entity_id" => $this->contactIdentifier, "custom_".$this->customFields->language['id'] => $_langRecord['language'], "custom_".$this->customFields->level['id'] => trim($_langRecord['speakingskill'])));
					} catch (Exception $e) {
						echo $_langRecord['language']." language skill failed to save for contact ".$this->contactRow['unid']." \r\n";
						echo $e."\r\n";
					}
				}
			}
		}
	}
	
	private function registerSectors() {
		/* This function registers all languages with reading level fair and above */
		$_sectorRecords = $this->dbAdapter->query("
			SELECT `cluster_code` FROM `pum_conversie_mainsector` 
			WHERE `unid` = '".$this->contactRow['unid']."'
		");
		if(is_object($_sectorRecords) AND $_sectorRecords->num_rows > 0) {
			while($_sectorRecord = $_sectorRecords->fetch_assoc()) {
				switch($_sectorRecord['cluster_code']) {
					case "": break;
				}
				try {
					
				} catch (Exception $e) {
					echo "";
				}
			}
		}
	}
	
	private function registerWorkHistory() {
		/* This function registers all languages with reading level fair and above */
		$_workHistoryRecords = $this->dbAdapter->query("
			SELECT * FROM `pum_conversie_job_experience` 
			WHERE `person_unid` = '".$this->contactRow['unid']."'
		");
		if(is_object($_workHistoryRecords) AND $_workHistoryRecords->num_rows > 0) {
			while($_workHistoryRecord = $_workHistoryRecords->fetch_assoc()) {
				try {
					civicrm_api3('CustomValue', 'create', array(
						"id" => -1, "entity_id" => $this->contactIdentifier, 
						"custom_".$this->customFields->name_of_organisation['id'] => $_workHistoryRecord['companyname'], 
						"custom_".$this->customFields->city['id'] => $_workHistoryRecord['city'], 
						"custom_".$this->customFields->country['id'] => $_workHistoryRecord['country'], 
						"custom_".$this->customFields->years_from['id'] => $_workHistoryRecord['from'], 
						"custom_".$this->customFields->to['id'] => $_workHistoryRecord['till'], 
						"custom_".$this->customFields->description['id'] => $_workHistoryRecord['description'], 
						"custom_".$this->customFields->job_title['id'] => $_workHistoryRecord['position'], 
					));
				} catch (Exception $e) {
					echo $e;
				}
			}
		}
	}
	
	private function registerContactGroups() {
	
	}
	
	private function addContactToGroup() {
		
	}
	
}

new contacts;