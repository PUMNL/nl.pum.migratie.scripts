<?php
require_once('./baseclass.php');
class contacts extends baseclass {

	public $contactSubType, $customFields, $groups, $tags, $optionGroups, $contactRow, $contactParams, $contactIdentifier;

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
			$this->contactSubType 														= new stdClass();
			$this->contactSubType->expert 												= civicrm_api3('ContactType', 'getsingle', array("name" => "Expert"));
			// Additional Information
			$this->customFields 														= new stdClass();
			$this->customFields->additionalGroup										= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Additional_Data"));
			$this->customFields->prins_unid												= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_Unique_ID", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->prins_shortname										= civicrm_api3('CustomField', 'getsingle', array("name" => "Shortname", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->initials												= civicrm_api3('CustomField', 'getsingle', array("name" => "Initials", "custom_group_id" => $this->customFields->additionalGroup['id']));
			$this->customFields->marital_status											= civicrm_api3('CustomField', 'getsingle', array("name" => "Marital_Status", "custom_group_id" => $this->customFields->additionalGroup['id']));
			// Bank Information
			$this->customFields->bankGroup												= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Bank_Information"));
			$this->customFields->b_number												= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Account_Number", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accname												= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_accadd												= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_address", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acczip												= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_postal_code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccity												= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_city", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_acccountry											= civicrm_api3('CustomField', 'getsingle', array("name" => "Accountholder_country", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iso													= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Country_ISO_Code", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_bic													= civicrm_api3('CustomField', 'getsingle', array("name" => "BIC_Swiftcode", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_iban													= civicrm_api3('CustomField', 'getsingle', array("name" => "IBAN_nummer", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_name													= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_Name", "custom_group_id" => $this->customFields->bankGroup['id']));
			$this->customFields->b_city													= civicrm_api3('CustomField', 'getsingle', array("name" => "Bank_City", "custom_group_id" => $this->customFields->bankGroup['id']));
			// Passport Information
			$this->customFields->passportGroup											= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Passport_Information"));
			$this->customFields->pp_firstname											= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_lastname											= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name_Last_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_partner_lastname									= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Name_Partner_Last_Name", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_number												= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Number", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_expire_date											= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Valid_until", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_issue_city											= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Place_of_Issue", "custom_group_id" => $this->customFields->passportGroup['id']));
			$this->customFields->pp_issue_date											= civicrm_api3('CustomField', 'getsingle', array("name" => "Passport_Issue_Date", "custom_group_id" => $this->customFields->passportGroup['id']));
			// Nationality
			$this->customFields->nationalityGroup										= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Nationality"));
			$this->customFields->nationality											= civicrm_api3('CustomField', 'getsingle', array("name" => "Nationality", "custom_group_id" => $this->customFields->nationalityGroup['id']));
			// Medical Information
			$this->customFields->medicalGroup											= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Medical_Information"));
			$this->customFields->medCompany												= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Company", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medNumber												= civicrm_api3('CustomField', 'getsingle', array("name" => "Health_Insurance_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracticer											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Name", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracAddress											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Address", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPostal											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Postal_Code", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCity											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_City", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracCountry											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Country", "custom_group_id" => $this->customFields->medicalGroup['id']));
			$this->customFields->medPracPhone											= civicrm_api3('CustomField', 'getsingle', array("name" => "General_Practitioner_Phone_Number", "custom_group_id" => $this->customFields->medicalGroup['id']));
			// Languages
			$this->customFields->languageGroup											= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Languages"));
			$this->customFields->language												= civicrm_api3('CustomField', 'getsingle', array("name" => "Language", "custom_group_id" => $this->customFields->languageGroup['id']));			
			$this->customFields->level													= civicrm_api3('CustomField', 'getsingle', array("name" => "Level", "custom_group_id" => $this->customFields->languageGroup['id']));			
			// Workhistory
			$this->customFields->workHistoryGroup										= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Workhistory"));
			$this->customFields->work_name_of_organisation								= civicrm_api3('CustomField', 'getsingle', array("name" => "Name_of_Organisation", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_city												= civicrm_api3('CustomField', 'getsingle', array("name" => "City", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_country											= civicrm_api3('CustomField', 'getsingle', array("name" => "Country", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_years_from										= civicrm_api3('CustomField', 'getsingle', array("name" => "Yeard_From", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_to												= civicrm_api3('CustomField', 'getsingle', array("name" => "To", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_job_title											= civicrm_api3('CustomField', 'getsingle', array("name" => "Job_Title", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_description										= civicrm_api3('CustomField', 'getsingle', array("name" => "Description", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_competences_used_in_this_job						= civicrm_api3('CustomField', 'getsingle', array("name" => "Competences_used_in_this_job", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_responsibilities									= civicrm_api3('CustomField', 'getsingle', array("name" => "Responsibilities", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			$this->customFields->work_countries_visited_in_relation_to_the_job			= civicrm_api3('CustomField', 'getsingle', array("name" => "Countries_visited_in_relation_to_the_job", "custom_group_id" => $this->customFields->workHistoryGroup['id']));
			// Education
			$this->customFields->educationHistoryGroup	                        		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Education"));
			$this->customFields->name_of_institution	                       			= civicrm_api3('CustomField', 'getsingle', array("name" => "Name_of_Institution", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->field_of_study_major	                        		= civicrm_api3('CustomField', 'getsingle', array("name" => "Field_of_study_major", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->city					                        		= civicrm_api3('CustomField', 'getsingle', array("name" => "City", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->country				                        		= civicrm_api3('CustomField', 'getsingle', array("name" => "Country", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->years_from												= civicrm_api3('CustomField', 'getsingle', array("name" => "Years_From", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->to														= civicrm_api3('CustomField', 'getsingle', array("name" => "To", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			$this->customFields->diploma_degree											= civicrm_api3('CustomField', 'getsingle', array("name" => "Diploma_Degree", "custom_group_id" => $this->customFields->educationHistoryGroup['id']));
			// Expertdata
			$this->customFields->expertDataGroup										= civicrm_api3('CustomGroup', 'getsingle', array("name" => "expert_data"));
			$this->customFields->expert_status											= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_start_date								= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_start_date", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_end_date									= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_end_date", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->expert_status_reason									= civicrm_api3('CustomField', 'getsingle', array("name" => "expert_status_reason", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->generic_skills											= civicrm_api3('CustomField', 'getsingle', array("name" => "generic_skills", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->side_activities										= civicrm_api3('CustomField', 'getsingle', array("name" => "side_activities", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->membership_serviceclub									= civicrm_api3('CustomField', 'getsingle', array("name" => "membership_serviceclub", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->name_serviceclub										= civicrm_api3('CustomField', 'getsingle', array("name" => "name_serviceclub", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->first_contact_with_PUM_via								= civicrm_api3('CustomField', 'getsingle', array("name" => "First_contact_with_PUM_via", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->other_contact_with_PUM									= civicrm_api3('CustomField', 'getsingle', array("name" => "Other_contact_with_PUM", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->code_conduct											= civicrm_api3('CustomField', 'getsingle', array("name" => "code_conduct", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			$this->customFields->CV_in_Mutation											= civicrm_api3('CustomField', 'getsingle', array("name" => "CV_in_Mutation", "custom_group_id" => $this->customFields->expertDataGroup['id']));
			// In case of emergency
			$this->customFields->in_case_of_emergencyGroup								= civicrm_api3('CustomGroup', 'getsingle', array("name" => "in_case_of_emergency_contact"));
			$this->customFields->relationship_with_contact		                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Relationship_with_contact", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->Other_relationship				                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Other_relationship", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->First_name_contact				                        = civicrm_api3('CustomField', 'getsingle', array("name" => "First_name_contact", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->Last_name_contact				                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Last_name_contact", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->Phone_number_1					                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Phone_number_1", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->Phone_number_2					                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Phone_number_2", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			$this->customFields->Email_Address					                        = civicrm_api3('CustomField', 'getsingle', array("name" => "Email_Address", "custom_group_id" => $this->customFields->in_case_of_emergencyGroup['id']));
			// Groups
			$this->groups					                                            = new stdClass();
			$this->groups->active			                                            = civicrm_api3('Group', 'getsingle', array("title" => "Active Expert"));
			$this->groups->BC				                                            = civicrm_api3('Group', 'getsingle', array("title" => "Business Link Coördinators"));
			$this->groups->CC				                                            = civicrm_api3('Group', 'getsingle', array("title" => "Country Coordinators"));
			$this->groups->CV				                                            = civicrm_api3('Group', 'getsingle', array("title" => "CV Intake"));
			$this->groups->LR				                                            = civicrm_api3('Group', 'getsingle', array("title" => "Representatives"));
			$this->groups->SC				                                            = civicrm_api3('Group', 'getsingle', array("title" => "Sector Coordinators"));
			$this->groups->senior			                                            = civicrm_api3('Group', 'getsingle', array("title" => "Ex-Staffvolunteers"));
			$this->groups->magazine			                                            = civicrm_api3('Group', 'getsingle', array("title" => "PUM magazine"));
			$this->groups->candidate		                                            = civicrm_api3('Group', 'getsingle', array("title" => "Candidate Expert"));
			$this->groups->representatives	                                            = civicrm_api3('Group', 'getsingle', array("title" => "Representatives"));
			$this->groups->AM				                                            = civicrm_api3('Group', 'getsingle', array("title" => "Project Officers"));
			// Tags
			$this->tags																	= new stdClass();
			$this->tags->horticulture_vegetables_fruits_green_glasshouses 				= civicrm_api3('Tag', 'getsingle', array("name" => "Horticulture: vegetables & fruits, green- & glasshouses"));
			$this->tags->horticulture_vegetables_fruits					 				= civicrm_api3('Tag', 'getsingle', array("name" => "Horticulture: vegetables & fruits"));
			$this->tags->agriculture_arable_farming					 					= civicrm_api3('Tag', 'getsingle', array("name" => "Agriculture: arable farming"));
			$this->tags->agriculture_tropical_products					 				= civicrm_api3('Tag', 'getsingle', array("name" => "Agriculture: tropical products"));
			$this->tags->horticulture_flowers_and_ornamental_plants							= civicrm_api3('Tag', 'getsingle', array("name" => "Horticulture: flowers and ornamental plants"));
			$this->tags->building_materials_supplies_systems							= civicrm_api3('Tag', 'getsingle', array("name" => "Building Materials: supplies & systems"));
			$this->tags->building_development_architecture_design_engineering			= civicrm_api3('Tag', 'getsingle', array("name" => "Building Development: architecture, design & engineering"));
			$this->tags->building_management_contracting_execution_installation			= civicrm_api3('Tag', 'getsingle', array("name" => "Building Management: contracting, execution & installation"));
			$this->tags->business_consultancy_financial_support_services_accountancy	= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: financial support services & accountancy"));
			$this->tags->business_consultancy_ict										= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: ICT"));
			$this->tags->business_consultancy_management_consultancy					= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: management consultancy"));
			$this->tags->business_consultancy_hrm_consultancy							= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: HRM consultancy"));
			$this->tags->business_consultancy_communications_marketing_consultancy		= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: communications & marketing consultancy"));
			$this->tags->business_consultancy_legal_consultancy							= civicrm_api3('Tag', 'getsingle', array("name" => "Business Consultancy: legal consultancy"));
			$this->tags->business_support_organizations_chambers_associations			= civicrm_api3('Tag', 'getsingle', array("name" => "Business Support Organisations (chambers/associations)"));
			$this->tags->industrialproduct_design_consultancy 							= civicrm_api3('Tag', 'getsingle', array('name' => 'Industrial/Product Design Consultancy'));
			$this->tags->government_services 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Government Services'));
			$this->tags->unions 														= civicrm_api3('Tag', 'getsingle', array('name' => 'Unions'));
			$this->tags->chemical_pharmaceutical_herbal_and_cosmetic_products 			= civicrm_api3('Tag', 'getsingle', array('name' => 'Chemical: pharmaceutical, herbal and cosmetic products'));
			$this->tags->chemical_paints_ink_lacquer 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Chemical: paints, ink & lacquer'));
			$this->tags->chemical_polymers_composites_and_manmade_fibers 				= civicrm_api3('Tag', 'getsingle', array('name' => 'Chemical: polymers, composites and manmade fibers'));
			$this->tags->chemical_chemical_technology_fine_chemicals 					= civicrm_api3('Tag', 'getsingle', array('name' => 'Chemical: chemical technology & fine chemicals'));
			$this->tags->chemical_inorganic_materials_industrial_glass_ceramics 		= civicrm_api3('Tag', 'getsingle', array('name' => 'Chemical: inorganic materials, industrial glass & ceramics'));
			$this->tags->electro_industrial 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Electro: industrial'));
			$this->tags->electro_electronics 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Electro: electronics'));
			$this->tags->electro_domestic_appliances 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Electro: domestic appliances'));
			$this->tags->electro_lighting 												= civicrm_api3('Tag', 'getsingle', array('name' => 'Electro: lighting'));
			$this->tags->electro_telecommunications_it 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Electro: telecommunications & IT'));
			$this->tags->automotive_technology 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Automotive Technology'));
			$this->tags->food_processing 												= civicrm_api3('Tag', 'getsingle', array('name' => 'Food Processing'));
			$this->tags->beverages_production 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Beverages Production'));
			$this->tags->edible_oil_production 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Edible Oil Production'));
			$this->tags->meat_processing 												= civicrm_api3('Tag', 'getsingle', array('name' => 'Meat Processing'));
			$this->tags->bakery_pastry_confectionary 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Bakery: Pastry & Confectionary'));
			$this->tags->banking_micro_finance 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Banking & Micro Finance'));
			$this->tags->insurance_services 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Insurance Services '));
			$this->tags->health_care_services 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Health Care Services'));
			$this->tags->transport_logistics 											= civicrm_api3('Tag', 'getsingle', array('name' => 'Transport & Logistics'));
			$this->tags->metal_metal_processing 										= civicrm_api3('Tag', 'getsingle', array('name' => 'Metal: metal processing'));
			$this->tags->metal_machine_engineering_construction 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Metal: machine engineering & construction'));
			$this->tags->metal_aircraft_maintenance_shipbuilding_repair 				= civicrm_api3('Tag', 'getsingle', array('name' => 'Metal: aircraft maintenance & shipbuilding, repair'));
			$this->tags->metal_metal_construction_maintenance_repair 					= civicrm_api3('Tag', 'getsingle', array('name' => 'Metal: metal construction, maintenance & repair'));
			$this->tags->energy_productionservices 										= civicrm_api3('Tag', 'getsingle', array('name' => 'Energy Production/Services'));
			$this->tags->water_supply_and_waste_water_treatment 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Water Supply and Waste Water Treatment'));
			$this->tags->waste_collection_treatment 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Waste Collection & Treatment'));
			$this->tags->environmental_matterscorporate_social_responsibility 			= civicrm_api3('Tag', 'getsingle', array('name' => 'Environmental Matters/Corporate Social Responsibility'));
			$this->tags->packaging 														= civicrm_api3('Tag', 'getsingle', array('name' => 'Packaging'));
			$this->tags->printing_cross_media_and_publishing 							= civicrm_api3('Tag', 'getsingle', array('name' => 'Printing: cross media and publishing'));
			$this->tags->ruminant_cattle_sheep_goats_camels_horses_etc_farming 			= civicrm_api3('Tag', 'getsingle', array('name' => 'Ruminant (cattle, sheep, goats, camels, horses, etc.) Farming'));
			$this->tags->pig_farming 													= civicrm_api3('Tag', 'getsingle', array('name' => 'Pig Farming'));
			$this->tags->dairy_processing_products 										= civicrm_api3('Tag', 'getsingle', array('name' => 'Dairy Processing & Products'));
			$this->tags->poultry_farming 												= civicrm_api3('Tag', 'getsingle', array('name' => 'Poultry Farming'));
			$this->tags->fisheries_aquaculture_and_fish_processing 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Fisheries: Aquaculture and Fish Processing'));
			$this->tags->beekeeping 													= civicrm_api3('Tag', 'getsingle', array('name' => 'Beekeeping'));
			$this->tags->vocational_education_training 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Vocational Education & Training'));
			$this->tags->hospitality_large_hotels_25fte 								= civicrm_api3('Tag', 'getsingle', array('name' => 'Hospitality: large hotels (>25fte)'));
			$this->tags->hospitality_catering_restaurants_events 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Hospitality: catering, restaurants & events'));
			$this->tags->hospitality_tourism_recreational_services 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Hospitality: tourism & recreational services'));
			$this->tags->hospitality_small_hotels_25fte 								= civicrm_api3('Tag', 'getsingle', array('name' => 'Hospitality: small hotels (<25fte)'));
			$this->tags->textile_industry_and_consumer_products 						= civicrm_api3('Tag', 'getsingle', array('name' => 'Textile Industry and Consumer Products'));
			$this->tags->leather_industry_and_consumer_goods 							= civicrm_api3('Tag', 'getsingle', array('name' => 'Leather Industry and Consumer Goods'));
			$this->tags->retail_business_to_consumer 									= civicrm_api3('Tag', 'getsingle', array('name' => 'Retail: business to consumer'));
			$this->tags->wholesale_business_to_business 								= civicrm_api3('Tag', 'getsingle', array('name' => 'Wholesale: business to business'));
			$this->tags->personnel_services_crafts 										= civicrm_api3('Tag', 'getsingle', array('name' => 'Personnel Services &amp; Crafts'));
			$this->tags->timber_processing 												= civicrm_api3('Tag', 'getsingle', array('name' => 'Timber Processing'));
			$this->tags->furniture_manufacturing_shopfitting 							= civicrm_api3('Tag', 'getsingle', array('name' => 'Furniture Manufacturing & Shopfitting'));
			/* Option groups */
			$this->optionGroups 														= new stdClass();
			$this->optionGroups->nationalities											= civicrm_api3('OptionGroup', 'get', array("name" => "nationalities"));
			$this->optionGroups->First_contact_with_PUM_via								= civicrm_api3('OptionGroup', 'getsingle', array("name" => "first_contact_with_pum_via_20141103154142"));
			$this->optionGroups->pum_magazine											= civicrm_api3('OptionValue', 'getsingle', array("label" => "(PUM) Magazine", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->pum_website											= civicrm_api3('OptionValue', 'getsingle', array("label" => "(PUM) Website", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->advertisement											= civicrm_api3('OptionValue', 'getsingle', array("label" => "Advertisement", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->colleague												= civicrm_api3('OptionValue', 'getsingle', array("label" => "Colleague", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->email_newsletter										= civicrm_api3('OptionValue', 'getsingle', array("label" => "Email/Newsletter", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->facebook												= civicrm_api3('OptionValue', 'getsingle', array("label" => "Facebook", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->family_friend											= civicrm_api3('OptionValue', 'getsingle', array("label" => "Family/Friend", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->linked_in												= civicrm_api3('OptionValue', 'getsingle', array("label" => "LinkedIn", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->newspaper_article										= civicrm_api3('OptionValue', 'getsingle', array("label" => "Newspaper article", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->other													= civicrm_api3('OptionValue', 'getsingle', array("label" => "Other", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->trade_business_association								= civicrm_api3('OptionValue', 'getsingle', array("label" => "Trade business association", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->twitter												= civicrm_api3('OptionValue', 'getsingle', array("label" => "Twitter", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->youtube												= civicrm_api3('OptionValue', 'getsingle', array("label" => "YouTube", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			$this->optionGroups->expert													= civicrm_api3('OptionValue', 'getsingle', array("label" => "PUM expert", "option_group_id" => $this->optionGroups->First_contact_with_PUM_via['id']));
			/* Location types */
			$this->locationTypes														= new stdClass();
		} catch (Exception $e) {
			die ($e);
		}
	}
	
	public function migration() {
		$this->resultSet = $this->dbAdapter->query("
			SELECT * FROM pum_conversie_person 
			WHERE `status` NOT IN ('Exit', 'Rejected')
		");
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
				'custom_'.$this->customFields->marital_status['id'] 	=> $this->determine_marital_status()
			);
	
			/* Register bank account for contact */
			$this->registerBankAccount();
			/* Register bank account for contact */
			$this->registerPassportInformation();
			/* Register medical information */
			if($this->isExpert()) $this->registerMedicalInformation();
			/* Register in case of emergency contact information */
			if($this->isExpert()) $this->registerInCaseOfEmergencyContact();
			
			// Insert contact 
			if($this->CIVIAPI->Contact->Create($this->contactParams)) {
				// Set contact identifier
				$this->contactIdentifier = $this->CIVIAPI->lastResult->id;
				/* If expert has status 'new' add to group */
				if($this->contactRow['status'] == "New") $this->addContactToGroup($this->contactIdentifier, $this->groups->candidate['id']); 
				/* Register address if available */
				if(!empty($this->contactRow['addresshome']) AND !empty($this->contactRow['cityhome'])) $this->registerAddress();
				/* Register E-mailadresses */
				$this->registerEmail();
				/* Register Telephones */
				$this->registerTelephone();
				/* Register all language skills for contact */
				$this->registerLanguages();
				/* Register all work history for contact */
				if($this->isExpert()) $this->registerWorkHistory();					
				/* Register all education history for contact */
				if($this->isExpert()) $this->registerEducationHistory();				
				/* Register expert data information */
				if($this->isExpert()) $this->registerExpertdata();
				/* Register all sectors for contact */
				$this->registerSectors();
				$this->registerSectorCordinator();				
				/* Add contact to all the groups */
				$this->registerContactGroups();
				/* Set group date */
				$this->setGroupDates();
			} else {
				echo $this->CIVIAPI->errorMsg();
			}			
		}
	}
	
	private function parseString(&$string) {
		$string = str_replace('[QUOTE]', '"', $string);
		$string = str_replace('[COMMA]', ',', $string);
		$string = str_replace('[CR][LF]', "\r\n", $string);
		$string = str_replace('[CR]', "\r", $string);
		$string = str_replace('[LF]', "\n", $string);
	}
	
	private function determineContactSubType() {
		/* This function determines if a contact should have a contact sub type */
		$_expert = $this->dbAdapter->query("SELECT 1 FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND `role` REGEXP 'Expert'");
		if($_expert->num_rows > 0) {
			return $this->contactSubType->expert['name'];
		}
	}
	
	private function isExpert() {
		/* This function determines if a contact should have a contact sub type */
		$_expert = $this->dbAdapter->query("SELECT 1 FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND `role` REGEXP 'Expert'");
		if($_expert->num_rows > 0) 	return 1;
	}
	
	private function determine_marital_status() {
		switch($this->contactRow['maritalstatus']) {
			case "Married": return 1; break;
			case "Single": return 2; break;
			case "Partner": return 3; break;
			case "Unknown": return 4; break;
			case "Widowed": return 5; break;
			default: return 4; break;
		}
	}	
	
	private function determine_job_title() {
		
	}
	
	private function registerBankAccount() {
		/* This function registers the bank accounts */
		if(!empty($this->contactRow['BankAccountNo'])) $this->contactParams['custom_'.$this->customFields->b_number['id']] = $this->contactRow['BankAccountNo'];
		if(!empty($this->contactRow['BankAccountholderName'])) $this->contactParams['custom_'.$this->customFields->b_accname['id']] = $this->contactRow['BankAccountholderName'];
		if(!empty($this->contactRow['BankAccountholderAddress'])) $this->contactParams['custom_'.$this->customFields->b_accadd['id']] = $this->contactRow['BankAccountholderAddress'];
		if(!empty($this->contactRow['BankAccountholderZIP'])) $this->contactParams['custom_'.$this->customFields->b_acczip['id']] = $this->contactRow['BankAccountholderZIP'];
		if(!empty($this->contactRow['BankAccountholderCity'])) $this->contactParams['custom_'.$this->customFields->b_acccity['id']] = $this->contactRow['BankAccountholderCity'];
		if(!empty($this->contactRow['BicSwift'])) $this->contactParams['custom_'.$this->customFields->b_bic['id']] = $this->contactRow['BicSwift'];
		if(!empty($this->contactRow['IBAN'])) $this->contactParams['custom_'.$this->customFields->b_iban['id']] = $this->contactRow['IBAN'];
		if(!empty($this->contactRow['BankName'])) $this->contactParams['custom_'.$this->customFields->b_name['id']] = $this->contactRow['BankName'];
		if(!empty($this->contactRow['BankCity'])) $this->contactParams['custom_'.$this->customFields->b_city['id']] = $this->contactRow['BankCity'];
		if(!empty($this->contactRow['BankCountryISOcode'])) {
			try { 
				$country = civicrm_api('Country', 'getsingle', array('version' => 3, 'iso_code' => $this->contactRow['BankCountryISOcode'])); 
				$this->contactParams['custom_'.$this->customFields->b_iso['id']] = $country['id'];	
			} catch(Exception $e) {}
		}
		if(!empty($this->contactRow['BankAccountholderCountry'])) {
			try { 
				$country = civicrm_api3('Country', 'getsingle', array('version' => 3, 'name' => $this->contactRow['BankAccountholderCountry']));
				if(isset($country['id'])) $this->contactParams['custom_'.$this->customFields->b_acccountry['id']] = $country['id'];	
			} catch(Exception $e) {
				try { 
					$country = civicrm_api3('Country', 'getsingle', array('version' => 3, 'iso_code' => $this->contactRow['BankCountryISOcode'])); 
					if(isset($country['id'])) $this->contactParams['custom_'.$this->customFields->b_acccountry['id']] = $country['id'];
				} catch (Exception $e) {}
			}
		}
	}	
	
	private function registerMedicalInformation() {
		/* This function registers the medical information for contact */
		if(!empty($this->contactRow['HealthInsurance'])) $this->contactParams['custom_'.$this->customFields->medCompany['id']] = $this->contactRow['HealthInsurance'];
		if(!empty($this->contactRow['PolicyNumber'])) $this->contactParams['custom_'.$this->customFields->medNumber['id']] = $this->contactRow['PolicyNumber'];
		if(!empty($this->contactRow['GPName'])) $this->contactParams['custom_'.$this->customFields->medPracticer['id']] = $this->contactRow['GPName'];
		if(!empty($this->contactRow['GPAddress'])) $this->contactParams['custom_'.$this->customFields->medPracAddress['id']] = $this->contactRow['GPAddress'];
		if(!empty($this->contactRow['GPZipCode'])) $this->contactParams['custom_'.$this->customFields->medPracPostal['id']] = $this->contactRow['GPZipCode'];
		if(!empty($this->contactRow['GPCity'])) $this->contactParams['custom_'.$this->customFields->medPracCity['id']] = $this->contactRow['GPCity'];
		if(!empty($this->contactRow['GPCountry'])) {
			try { 
				$country = civicrm_api('Country', 'getsingle', array('version' => 3, 'name' => $this->contactRow['GPCountry'])); 
				@$country_id = $country['id'];
			} catch(Exception $e) {
				$country_id = NULL;
			}
			$this->contactParams['custom_'.$this->customFields->medPracCountry['id']] = $country_id;	
		}
		if(!empty($this->contactRow['GPTelNumber'])) $this->contactParams['custom_'.$this->customFields->medPracPhone['id']] = "+".$this->contactRow['GPTelCountry'].$this->contactRow['GPTelArea'].$this->contactRow['GPTelNumber'];		
	}
	
	private function registerPassportInformation() {
		/* This function registers the contact passport information */
		if(!empty($this->contactRow['passportname'])) $this->contactParams['custom_'.$this->customFields->pp_firstname['id']] = $this->contactRow['passportname'];
		if(!empty($this->contactRow['surname'])) $this->contactParams['custom_'.$this->customFields->pp_lastname['id']] = trim($this->contactRow['infix'].' '.$this->contactRow['surname']);
		if(!empty($this->contactRow['PassportNumber'])) $this->contactParams['custom_'.$this->customFields->pp_number['id']] = $this->contactRow['PassportNumber'];
		if($this->contactRow['PassportExpiration'] != '0000-00-00') $this->contactParams['custom_'.$this->customFields->pp_expire_date['id']] = $this->contactRow['PassportExpiration'];
		if(!empty($this->contactRow['PassportCityIssued'])) $this->contactParams['custom_'.$this->customFields->pp_issue_city['id']] = $this->contactRow['PassportCityIssued'];
		if($this->contactRow['PassportDateIssued'] != '0000-00-00') $this->contactParams['custom_'.$this->customFields->pp_issue_date['id']] = $this->contactRow['PassportDateIssued'];
		/* Nationality */
		if(!empty($this->contactRow['nationality'])) {
			try {
				$nationalityCountry = ($this->contactRow['nationality'] == "Netherlands") ? "Dutch" : $this->contactRow['nationality'];
				$nationality = civicrm_api3('OptionValue', 'getsingle', array('option_group_id' => $this->optionGroups->nationalities['id'], 'label' => $nationalityCountry));
				$this->contactParams['custom_'.$this->customFields->nationality['id']] = $nationality['value'];
			} catch (Exception $e){ /* Ignore field */ }
		}
	}
	
	private function registerInCaseOfEmergencyContact() {
		/* This function registers the in case of emergency contact information */
		if(!empty($this->contactRow['PartnerFirstName'])) $this->contactParams['custom_'.$this->customFields->First_name_contact['id']] = $this->contactRow['PartnerFirstName'];
		if(!empty($this->contactRow['PartnerSurName'])) $this->contactParams['custom_'.$this->customFields->Last_name_contact['id']] = $this->contactRow['PartnerInfix'].' '.$this->contactRow['PartnerSurName'];
		if(!empty($this->contactRow['PartnerMobilePhone'])) $this->contactParams['custom_'.$this->customFields->Phone_number_1['id']] = $this->contactRow['PartnerMobilePhone'];
		if(!empty($this->contactRow['PartnerTelephoneNumber'])) $this->contactParams['custom_'.$this->customFields->Phone_number_2['id']] = $this->contactRow['PartnerTelephoneNumber'];
		if(!empty($this->contactRow['PartnerEmailAddress'])) $this->contactParams['custom_'.$this->customFields->Email_Address['id']] = $this->contactRow['PartnerEmailAddress'];
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
		try { @$_country = civicrm_api('Country', 'getsingle', array('version' => 3, 'name' => $this->contactRow['countryhome'])); @$_country_id = $_country['id']; } catch(Exception $e) { $_country_id = NULL; }
		return $_country_id;
	}
	
	private function registerEmail() {
		/* This function registers the e-mailaddress of a contact */
		try {
			$primary = 1;
			if(!empty($this->contactRow['EmailPUM'])) {
				civicrm_api3('Email','Create', array('contact_id' => $this->contactIdentifier, 'email' => $this->contactRow['EmailPUM'], 'location_type_id' => 2, 'is_primary' => $primary));
				$primary = 0;
			}
			if(!empty($this->contactRow['Email1'])) civicrm_api3('Email','Create', array('contact_id' => $this->contactIdentifier, 'email' => $this->contactRow['Email1'], 'location_type_id' => 1, 'is_primary' => $primary));
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
			if(!empty($this->contactRow['FaxNumber1'])) civicrm_api3('Phone','Create', array('contact_id' => $this->contactIdentifier, 'phone' => "+".$this->contactRow['FaxCountry1'].$this->contactRow['FaxArea1'].$this->contactRow['FaxNumber1'], 'location_type_id' => 1, 'phone_type_id' => 3));		
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
			AND (`speakingskill` REGEXP 'Fair|Good|Very Good')
			AND `language` IN ('English', 'French', 'Spanish', 'Portugese', 'Portuguese', 'Swahili', 'Russian', 'Chinese', 'Arabic', 'Behasa', 'Bahasa Indonesia', 'German')
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
	
	private function registerWorkHistory() {
		/* This function registers all work history */
		$_workHistoryRecords = $this->dbAdapter->query("
			SELECT * FROM `pum_conversie_job_experience` 
			WHERE `person_unid` = '".$this->contactRow['unid']."'
			GROUP BY `unid`
			ORDER BY `from` DESC
		");
		if(is_object($_workHistoryRecords) AND $_workHistoryRecords->num_rows > 0) {
			while($_workHistoryRecord = $_workHistoryRecords->fetch_assoc()) {
				array_walk($_workHistoryRecord, array($this, 'parseString'));
				try { @$_country = civicrm_api('Country', 'getsingle', array('version' => 3, 'name' => $_workHistoryRecord['country'])); @$_country_id = $_country['id']; } catch(Exception $e) { $_country_id = NULL; }					
				try {
					$workHistoryParams = array(
						"id" => -1, "entity_id" => $this->contactIdentifier, 
						"custom_".$this->customFields->work_name_of_organisation['id'] => $_workHistoryRecord['companyname'], 
						"custom_".$this->customFields->work_city['id'] => $_workHistoryRecord['city'], 
						"custom_".$this->customFields->work_country['id'] => $_country_id, 
						"custom_".$this->customFields->work_years_from['id'] => $_workHistoryRecord['from'], 
						"custom_".$this->customFields->work_to['id'] => $_workHistoryRecord['till'], 
						"custom_".$this->customFields->work_description['id'] => $_workHistoryRecord['description'], 
						"custom_".$this->customFields->work_job_title['id'] => $_workHistoryRecord['position'], 
					);
					civicrm_api3('CustomValue', 'create', $workHistoryParams);
				} catch (Exception $e) {
					echo "Creating job history entry failed: ".$e; 
				}
			}
		}
	}	
	
	private function registerEducationHistory() {
		/* This function registers all education with reading level fair and above */
		$_educationHistoryRecords = $this->dbAdapter->query("
			SELECT * FROM `pum_conversie_education` 
			WHERE `person_unid` = '".$this->contactRow['unid']."'
		");
		if(is_object($_educationHistoryRecords) AND $_educationHistoryRecords->num_rows > 0) {
			while($_educationHistoryRecord = $_educationHistoryRecords->fetch_assoc()) {
				array_walk($_educationHistoryRecord, array($this, 'parseString'));
				try { @$_country = civicrm_api('Country', 'getsingle', array('version' => 3, 'name' => $_educationHistoryRecord['country'])); @$_country_id = $_country['id']; } catch(Exception $e) { $_country_id = NULL; }					
				try {
					if(!empty($_educationHistoryRecord['institutionname'])) {
						$certificate = ($_educationHistoryRecord['certificate'] == "No") ? "No" : "Yes";
						civicrm_api3('CustomValue', 'create', array(
							"id" => -1, "entity_id" => $this->contactIdentifier, 
							"custom_".$this->customFields->name_of_institution['id'] => $_educationHistoryRecord['institutionname'], 
							"custom_".$this->customFields->field_of_study_major['id'] => $_educationHistoryRecord['study'], 
							"custom_".$this->customFields->city['id'] => $_educationHistoryRecord['city'], 
							"custom_".$this->customFields->country['id'] => $_country_id,
							"custom_".$this->customFields->years_from['id'] => $_educationHistoryRecord['from'], 
							"custom_".$this->customFields->to['id'] => $_educationHistoryRecord['until'], 
							"custom_".$this->customFields->diploma_degree['id'] => $certificate
						));
					}
				} catch (Exception $e) {
					var_dump(array(
							"id" => -1, "entity_id" => $this->contactIdentifier, 
							"custom_".$this->customFields->name_of_institution['id'] => $_educationHistoryRecord['institutionname'], 
							"custom_".$this->customFields->field_of_study_major['id'] => $_educationHistoryRecord['study'], 
							"custom_".$this->customFields->city['id'] => $_educationHistoryRecord['city'], 
							"custom_".$this->customFields->country['id'] => $_country_id,
							"custom_".$this->customFields->years_from['id'] => $_educationHistoryRecord['from'], 
							"custom_".$this->customFields->to['id'] => $_educationHistoryRecord['until'], 
							"custom_".$this->customFields->diploma_degree['id'] => $certificate
						));
					echo "Creating education history entry failed: ".$e; 
				}
			}
		}
	}
	
	private function registerExpertdata() {
		/* This function registers all expert data */
		try {
			if(!empty($this->contactRow['FirstContact'])) {
				$firstContact = trim(str_replace(" ","", $this->contactRow['FirstContact']));
				switch($firstContact) {
					case "Colleague": $ov = $this->optionGroups->colleague['value']; break;
					case "Friends, Family, Colleagues": $ov = $this->optionGroups->family_friend['value']; break;
					case "Job": $ov = $this->optionGroups->colleague['value']; break;
					case "Magazine": $ov = $this->optionGroups->pum_magazine['value']; break;
					case "Other": $ov = $this->optionGroups->other['value']; break;
					case "PUMEmployee": $ov = $this->optionGroups->other['value']; break;
					case "PUMExpert": $ov = $this->optionGroups->expert['value']; break;
					case "PUMWebsite": $ov = $this->optionGroups->pum_website['value']; break;
					case "Radio": $ov = $this->optionGroups->other['value']; break;
					case "Trade Business Association": $ov = $this->optionGroups->trade_business_association['value']; break;
					case "TradeBusiness Association": $ov = $this->optionGroups->trade_business_association['value']; break;
					case "TradeBusinessAssociation": $ov = $this->optionGroups->trade_business_association['value']; break;
					case "TradeShow": $ov = $this->optionGroups->trade_business_association['value']; break;
					default: $ov = NULL;
				}
				if(!is_null($ov)){
					civicrm_api3('CustomValue', 'create', array(
						"id" => -1, 
						"entity_id" => $this->contactIdentifier, 
						"custom_".$this->customFields->first_contact_with_PUM_via['id'] => $ov
					));
				}
			}
		} catch (Exception $e) { echo $e; }
		/* Side activities */
		$sideActivities = $this->dbAdapter->query("SELECT `further_job_information` FROM `pum_conversie_cv` WHERE `person_unid` = '".$this->contactRow['unid']."'");
		if($sideActivities) {
			$sideActivities = $sideActivities->fetch_assoc();
			if(!empty($sideActivities['further_job_information'])) {
				civicrm_api3('CustomValue', 'create', array(
					"id" => -1, "entity_id" => $this->contactIdentifier, 
					"custom_".$this->customFields->side_activities['id'] => $sideActivities['further_job_information']
				));
			}
		}
		if(!empty($this->contactRow['status'])) {
			if(in_array($this->contactRow['status'], array("Active for one project exclusively","Special occasions only"))) {
				try { 
					$activity = civicrm_api3('Activity','Create', array(
						"activity_type_id" => 62, 
						"subject" => $this->contactRow['status'], 
						"status_id" => 2, 
						"source_contact_id" => $this->contactIdentifier
					));
					civicrm_api3('ActivityContact','Create', array(
						"activity_id" => $activity['id'],
						"contact_id" => $this->contactIdentifier
					));
				} catch (Exception $e) { echo "Creating activity failed for contact: ".$this->contactRow['unid']; echo $e;	}
			}
		}
		try {
			if($this->contactRow['status'] == "Temporarily inactive") {
				civicrm_api3('CustomValue', 'create', array(
					"id" => -1, "entity_id" => $this->contactIdentifier, 
					"custom_".$this->customFields->expert_status['id'] => "Temporarily inactive"
				));					
			} else {
				civicrm_api3('CustomValue', 'create', array(
					"id" => -1, "entity_id" => $this->contactIdentifier, 
					"custom_".$this->customFields->expert_status['id'] => "Active"
				));				
			}
		} catch (Exception $e) {
			echo "Creating education history entry failed: ".$e; 
		}	
	}

	private function registerSectors() {
		/* This function registers all languages with reading level fair and above */
		switch(trim($this->contactRow['mainsector'])) {
			case "AH01": $this->tagContact($this->tags->horticulture_vegetables_fruits_green_glasshouses, $this->contactIdentifier); break;
			case "AH02": $this->tagContact($this->tags->horticulture_vegetables_fruits, $this->contactIdentifier); break;
			case "AH03": $this->tagContact($this->tags->agriculture_arable_farming, $this->contactIdentifier); break;
			case "AH05": $this->tagContact($this->tags->agriculture_tropical_products, $this->contactIdentifier); break;
			case "AH06": $this->tagContact($this->tags->horticulture_flowers_and_ornamental_plants, $this->contactIdentifier); break;
			case "AH07": $this->tagContact($this->tags->agriculture_arable_farming, $this->contactIdentifier); break;
			case "BC01": $this->tagContact($this->tags->building_materials_supplies_systems, $this->contactIdentifier); break;
			case "BC02": $this->tagContact($this->tags->building_development_architecture_design_engineering, $this->contactIdentifier); break;
			case "BC03": $this->tagContact($this->tags->building_management_contracting_execution_installation, $this->contactIdentifier); break;
			case "BS01": $this->tagContact($this->tags->business_consultancy_financial_support_services_accountancy, $this->contactIdentifier); break;
			case "BS02": $this->tagContact($this->tags->business_consultancy_ict, $this->contactIdentifier); break;
			case "BS03": $this->tagContact($this->tags->business_consultancy_financial_support_services_accountancy, $this->contactIdentifier); break;
			case "BS04": $this->tagContact($this->tags->business_consultancy_management_consultancy, $this->contactIdentifier); break;
			case "BS05": $this->tagContact($this->tags->business_consultancy_management_consultancy, $this->contactIdentifier); break;
			case "BS06": $this->tagContact($this->tags->business_consultancy_hrm_consultancy, $this->contactIdentifier); break;
			case "BS07": $this->tagContact($this->tags->business_consultancy_communications_marketing_consultancy, $this->contactIdentifier); break;
			case "BS08": $this->tagContact($this->tags->business_consultancy_legal_consultancy, $this->contactIdentifier); break;
			case "BS09": $this->tagContact($this->tags->business_consultancy_communications_marketing_consultancy, $this->contactIdentifier); break;
			case "BS10": $this->tagContact($this->tags->business_support_organizations_chambers_associations, $this->contactIdentifier); break;
			case "BS11": $this->tagContact($this->tags->industrialproduct_design_consultancy, $this->contactIdentifier); break;
			case "CG01": $this->tagContact($this->tags->government_services, $this->contactIdentifier); break;
			case "CG02": $this->tagContact($this->tags->government_services, $this->contactIdentifier); break;
			case "CG04": $this->tagContact($this->tags->unions, $this->contactIdentifier); break;
			case "CS01": $this->tagContact($this->tags->chemical_pharmaceutical_herbal_and_cosmetic_products, $this->contactIdentifier); break;
			case "CS02": $this->tagContact($this->tags->chemical_paints_ink_lacquer, $this->contactIdentifier); break;
			case "CS03": $this->tagContact($this->tags->chemical_polymers_composites_and_manmade_fibers, $this->contactIdentifier); break;
			case "CS04": $this->tagContact($this->tags->chemical_chemical_technology_fine_chemicals, $this->contactIdentifier); break;
			case "CS05": $this->tagContact($this->tags->chemical_inorganic_materials_industrial_glass_ceramics, $this->contactIdentifier); break;
			case "CS06": $this->tagContact($this->tags->chemical_inorganic_materials_industrial_glass_ceramics, $this->contactIdentifier); break;
			case "ET01": $this->tagContact($this->tags->electro_industrial, $this->contactIdentifier); break;
			case "ET02": $this->tagContact($this->tags->electro_electronics, $this->contactIdentifier); break;
			case "ET03": $this->tagContact($this->tags->electro_domestic_appliances, $this->contactIdentifier); break;
			case "ET04": $this->tagContact($this->tags->electro_lighting, $this->contactIdentifier); break;
			case "ET05": $this->tagContact($this->tags->electro_telecommunications_it, $this->contactIdentifier); break;
			case "ET06": $this->tagContact($this->tags->automotive_technology, $this->contactIdentifier); break;
			case "FB01": $this->tagContact($this->tags->food_processing, $this->contactIdentifier); break;
			case "FB02": $this->tagContact($this->tags->beverages_production, $this->contactIdentifier); break;
			case "FB03": $this->tagContact($this->tags->edible_oil_production, $this->contactIdentifier); break;
			case "FB04": $this->tagContact($this->tags->meat_processing, $this->contactIdentifier); break;
			case "FB05": $this->tagContact($this->tags->bakery_pastry_confectionary, $this->contactIdentifier); break;
			case "FB06": $this->tagContact($this->tags->bakery_pastry_confectionary, $this->contactIdentifier); break;
			case "FB07": $this->tagContact($this->tags->food_processing, $this->contactIdentifier); break;
			case "FI01": $this->tagContact($this->tags->banking_micro_finance, $this->contactIdentifier); break;
			case "FI02": $this->tagContact($this->tags->insurance_services, $this->contactIdentifier); break;
			case "HC01": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
			case "HC02": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
			case "HC03": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
			case "LT01": $this->tagContact($this->tags->transport_logistics, $this->contactIdentifier); break;
			case "LT02": $this->tagContact($this->tags->transport_logistics, $this->contactIdentifier); break;
			case "MI01": $this->tagContact($this->tags->metal_metal_processing, $this->contactIdentifier); break;
			case "MI02": $this->tagContact($this->tags->metal_machine_engineering_construction, $this->contactIdentifier); break;
			case "MI03": $this->tagContact($this->tags->metal_aircraft_maintenance_shipbuilding_repair, $this->contactIdentifier); break;
			case "MI04": $this->tagContact($this->tags->metal_metal_construction_maintenance_repair, $this->contactIdentifier); break;
			case "MI05": $this->tagContact($this->tags->metal_metal_construction_maintenance_repair, $this->contactIdentifier); break;
			case "PE01": $this->tagContact($this->tags->energy_productionservices, $this->contactIdentifier); break;
			case "PE02": $this->tagContact($this->tags->water_supply_and_waste_water_treatment, $this->contactIdentifier); break;
			case "PE03": $this->tagContact($this->tags->waste_collection_treatment, $this->contactIdentifier); break;
			case "PE04": $this->tagContact($this->tags->environmental_matterscorporate_social_responsibility, $this->contactIdentifier); break;
			case "PP01": $this->tagContact($this->tags->packaging, $this->contactIdentifier); break;
			case "PU01": $this->tagContact($this->tags->printing_cross_media_and_publishing, $this->contactIdentifier); break;
			case "PU02": $this->tagContact($this->tags->printing_cross_media_and_publishing, $this->contactIdentifier); break;
			case "SB01": $this->tagContact($this->tags->ruminant_cattle_sheep_goats_camels_horses_etc_farming, $this->contactIdentifier); break;
			case "SB02": $this->tagContact($this->tags->pig_farming, $this->contactIdentifier); break;
			case "SB03": $this->tagContact($this->tags->dairy_processing_products, $this->contactIdentifier); break;
			case "SB04": $this->tagContact($this->tags->poultry_farming, $this->contactIdentifier); break;
			case "SB05": $this->tagContact($this->tags->fisheries_aquaculture_and_fish_processing, $this->contactIdentifier); break;
			case "SB06": $this->tagContact($this->tags->beekeeping, $this->contactIdentifier); break;
			case "TE01": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
			case "TE03": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
			case "TE04": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
			case "TE05": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
			case "TE06": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
			case "TH01": $this->tagContact($this->tags->hospitality_large_hotels_25fte, $this->contactIdentifier); break;
			case "TH02": $this->tagContact($this->tags->hospitality_catering_restaurants_events, $this->contactIdentifier); break;
			case "TH03": $this->tagContact($this->tags->hospitality_tourism_recreational_services, $this->contactIdentifier); break;
			case "TH04": $this->tagContact($this->tags->hospitality_small_hotels_25fte, $this->contactIdentifier); break;
			case "TL01": $this->tagContact($this->tags->textile_industry_and_consumer_products, $this->contactIdentifier); break;
			case "TL02": $this->tagContact($this->tags->textile_industry_and_consumer_products, $this->contactIdentifier); break;
			case "TL03": $this->tagContact($this->tags->leather_industry_and_consumer_goods, $this->contactIdentifier); break;
			case "TL04": $this->tagContact($this->tags->leather_industry_and_consumer_goods, $this->contactIdentifier); break;
			case "TR01": $this->tagContact($this->tags->retail_business_to_consumer, $this->contactIdentifier); break;
			case "TR02": $this->tagContact($this->tags->wholesale_business_to_business, $this->contactIdentifier); break;
			case "TR03": $this->tagContact($this->tags->personnel_services_crafts, $this->contactIdentifier); break;
			case "WT01": $this->tagContact($this->tags->timber_processing, $this->contactIdentifier); break;
			case "WT02": $this->tagContact($this->tags->furniture_manufacturing_shopfitting, $this->contactIdentifier); break;
		}
	}
	
	private function registerSectorCordinator() {
		/* This function registers all languages with reading level fair and above */
		$_sectorRecords = $this->dbAdapter->query("
			SELECT CONCAT(`cluster_code`, `mainsector_code`) as `code` FROM `pum_conversie_mainsector` 
			WHERE `sc_unid` = '".$this->contactRow['unid']."'
		");
		if(is_object($_sectorRecords) AND $_sectorRecords->num_rows > 0) {
			while($_sectorRecord = $_sectorRecords->fetch_assoc()) {
				switch(trim($_sectorRecord['code'])) {
					case "AH01": $this->tagContact($this->tags->horticulture_vegetables_fruits_green_glasshouses, $this->contactIdentifier); break;
					case "AH02": $this->tagContact($this->tags->horticulture_vegetables_fruits, $this->contactIdentifier); break;
					case "AH03": $this->tagContact($this->tags->agriculture_arable_farming, $this->contactIdentifier); break;
					case "AH05": $this->tagContact($this->tags->agriculture_tropical_products, $this->contactIdentifier); break;
					case "AH06": $this->tagContact($this->tags->horticulture_flowers_and_ornamental_plants, $this->contactIdentifier); break;
					case "AH07": $this->tagContact($this->tags->agriculture_arable_farming, $this->contactIdentifier); break;
					case "BC01": $this->tagContact($this->tags->building_materials_supplies_systems, $this->contactIdentifier); break;
					case "BC02": $this->tagContact($this->tags->building_development_architecture_design_engineering, $this->contactIdentifier); break;
					case "BC03": $this->tagContact($this->tags->building_management_contracting_execution_installation, $this->contactIdentifier); break;
					case "BS01": $this->tagContact($this->tags->business_consultancy_financial_support_services_accountancy, $this->contactIdentifier); break;
					case "BS02": $this->tagContact($this->tags->business_consultancy_ict, $this->contactIdentifier); break;
					case "BS03": $this->tagContact($this->tags->business_consultancy_financial_support_services_accountancy, $this->contactIdentifier); break;
					case "BS04": $this->tagContact($this->tags->business_consultancy_management_consultancy, $this->contactIdentifier); break;
					case "BS05": $this->tagContact($this->tags->business_consultancy_management_consultancy, $this->contactIdentifier); break;
					case "BS06": $this->tagContact($this->tags->business_consultancy_hrm_consultancy, $this->contactIdentifier); break;
					case "BS07": $this->tagContact($this->tags->business_consultancy_communications_marketing_consultancy, $this->contactIdentifier); break;
					case "BS08": $this->tagContact($this->tags->business_consultancy_legal_consultancy, $this->contactIdentifier); break;
					case "BS09": $this->tagContact($this->tags->business_consultancy_communications_marketing_consultancy, $this->contactIdentifier); break;
					case "BS10": $this->tagContact($this->tags->business_support_organizations_chambers_associations, $this->contactIdentifier); break;
					case "BS11": $this->tagContact($this->tags->industrialproduct_design_consultancy, $this->contactIdentifier); break;
					case "CG01": $this->tagContact($this->tags->government_services, $this->contactIdentifier); break;
					case "CG02": $this->tagContact($this->tags->government_services, $this->contactIdentifier); break;
					case "CG04": $this->tagContact($this->tags->unions, $this->contactIdentifier); break;
					case "CS01": $this->tagContact($this->tags->chemical_pharmaceutical_herbal_and_cosmetic_products, $this->contactIdentifier); break;
					case "CS02": $this->tagContact($this->tags->chemical_paints_ink_lacquer, $this->contactIdentifier); break;
					case "CS03": $this->tagContact($this->tags->chemical_polymers_composites_and_manmade_fibers, $this->contactIdentifier); break;
					case "CS04": $this->tagContact($this->tags->chemical_chemical_technology_fine_chemicals, $this->contactIdentifier); break;
					case "CS05": $this->tagContact($this->tags->chemical_inorganic_materials_industrial_glass_ceramics, $this->contactIdentifier); break;
					case "CS06": $this->tagContact($this->tags->chemical_inorganic_materials_industrial_glass_ceramics, $this->contactIdentifier); break;
					case "ET01": $this->tagContact($this->tags->electro_industrial, $this->contactIdentifier); break;
					case "ET02": $this->tagContact($this->tags->electro_electronics, $this->contactIdentifier); break;
					case "ET03": $this->tagContact($this->tags->electro_domestic_appliances, $this->contactIdentifier); break;
					case "ET04": $this->tagContact($this->tags->electro_lighting, $this->contactIdentifier); break;
					case "ET05": $this->tagContact($this->tags->electro_telecommunications_it, $this->contactIdentifier); break;
					case "ET06": $this->tagContact($this->tags->automotive_technology, $this->contactIdentifier); break;
					case "FB01": $this->tagContact($this->tags->food_processing, $this->contactIdentifier); break;
					case "FB02": $this->tagContact($this->tags->beverages_production, $this->contactIdentifier); break;
					case "FB03": $this->tagContact($this->tags->edible_oil_production, $this->contactIdentifier); break;
					case "FB04": $this->tagContact($this->tags->meat_processing, $this->contactIdentifier); break;
					case "FB05": $this->tagContact($this->tags->bakery_pastry_confectionary, $this->contactIdentifier); break;
					case "FB06": $this->tagContact($this->tags->bakery_pastry_confectionary, $this->contactIdentifier); break;
					case "FB07": $this->tagContact($this->tags->food_processing, $this->contactIdentifier); break;
					case "FI01": $this->tagContact($this->tags->banking_micro_finance, $this->contactIdentifier); break;
					case "FI02": $this->tagContact($this->tags->insurance_services, $this->contactIdentifier); break;
					case "HC01": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
					case "HC02": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
					case "HC03": $this->tagContact($this->tags->health_care_services, $this->contactIdentifier); break;
					case "LT01": $this->tagContact($this->tags->transport_logistics, $this->contactIdentifier); break;
					case "LT02": $this->tagContact($this->tags->transport_logistics, $this->contactIdentifier); break;
					case "MI01": $this->tagContact($this->tags->metal_metal_processing, $this->contactIdentifier); break;
					case "MI02": $this->tagContact($this->tags->metal_machine_engineering_construction, $this->contactIdentifier); break;
					case "MI03": $this->tagContact($this->tags->metal_aircraft_maintenance_shipbuilding_repair, $this->contactIdentifier); break;
					case "MI04": $this->tagContact($this->tags->metal_metal_construction_maintenance_repair, $this->contactIdentifier); break;
					case "MI05": $this->tagContact($this->tags->metal_metal_construction_maintenance_repair, $this->contactIdentifier); break;
					case "PE01": $this->tagContact($this->tags->energy_productionservices, $this->contactIdentifier); break;
					case "PE02": $this->tagContact($this->tags->water_supply_and_waste_water_treatment, $this->contactIdentifier); break;
					case "PE03": $this->tagContact($this->tags->waste_collection_treatment, $this->contactIdentifier); break;
					case "PE04": $this->tagContact($this->tags->environmental_matterscorporate_social_responsibility, $this->contactIdentifier); break;
					case "PP01": $this->tagContact($this->tags->packaging, $this->contactIdentifier); break;
					case "PU01": $this->tagContact($this->tags->printing_cross_media_and_publishing, $this->contactIdentifier); break;
					case "PU02": $this->tagContact($this->tags->printing_cross_media_and_publishing, $this->contactIdentifier); break;
					case "SB01": $this->tagContact($this->tags->ruminant_cattle_sheep_goats_camels_horses_etc_farming, $this->contactIdentifier); break;
					case "SB02": $this->tagContact($this->tags->pig_farming, $this->contactIdentifier); break;
					case "SB03": $this->tagContact($this->tags->dairy_processing_products, $this->contactIdentifier); break;
					case "SB04": $this->tagContact($this->tags->poultry_farming, $this->contactIdentifier); break;
					case "SB05": $this->tagContact($this->tags->fisheries_aquaculture_and_fish_processing, $this->contactIdentifier); break;
					case "SB06": $this->tagContact($this->tags->beekeeping, $this->contactIdentifier); break;
					case "TE01": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
					case "TE03": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
					case "TE04": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
					case "TE05": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
					case "TE06": $this->tagContact($this->tags->vocational_education_training, $this->contactIdentifier); break;
					case "TH01": $this->tagContact($this->tags->hospitality_large_hotels_25fte, $this->contactIdentifier); break;
					case "TH02": $this->tagContact($this->tags->hospitality_catering_restaurants_events, $this->contactIdentifier); break;
					case "TH03": $this->tagContact($this->tags->hospitality_tourism_recreational_services, $this->contactIdentifier); break;
					case "TH04": $this->tagContact($this->tags->hospitality_small_hotels_25fte, $this->contactIdentifier); break;
					case "TL01": $this->tagContact($this->tags->textile_industry_and_consumer_products, $this->contactIdentifier); break;
					case "TL02": $this->tagContact($this->tags->textile_industry_and_consumer_products, $this->contactIdentifier); break;
					case "TL03": $this->tagContact($this->tags->leather_industry_and_consumer_goods, $this->contactIdentifier); break;
					case "TL04": $this->tagContact($this->tags->leather_industry_and_consumer_goods, $this->contactIdentifier); break;
					case "TR01": $this->tagContact($this->tags->retail_business_to_consumer, $this->contactIdentifier); break;
					case "TR02": $this->tagContact($this->tags->wholesale_business_to_business, $this->contactIdentifier); break;
					case "TR03": $this->tagContact($this->tags->personnel_services_crafts, $this->contactIdentifier); break;
					case "WT01": $this->tagContact($this->tags->timber_processing, $this->contactIdentifier); break;
					case "WT02": $this->tagContact($this->tags->furniture_manufacturing_shopfitting, $this->contactIdentifier); break;
				}
			}
		}
	}
	
	private function tagContact($tag, $contact_id) {
		try{
			civicrm_api3('EntityTag', 'create', array('entity_table' => 'civicrm_contact', 'entity_id' => $contact_id, 'tag_id' => $tag['id']));
		} catch (Exception $e) {
			echo $e;
			echo "Taggen van contact met is mislukt!\r\n";
		}
	}
	
	private function registerContactGroups() {
		$_roles = $this->dbAdapter->query("SELECT `role` FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."'");
		if($_roles->num_rows > 0) {
			while($_role = $_roles->fetch_assoc()) {
				switch(trim($_role['role'])) {
					case "AA": $this->addContactToGroup($this->contactIdentifier, $this->groups->active['id']); break;
					case "BC": $this->addContactToGroup($this->contactIdentifier, $this->groups->BC['id']); break;
					case "CC": $this->addContactToGroup($this->contactIdentifier, $this->groups->CC['id']); break;
					case "CV": $this->addContactToGroup($this->contactIdentifier, $this->groups->CV['id']); break;
					case "LR": $this->addContactToGroup($this->contactIdentifier, $this->groups->LR['id']); break;
					case "SC": $this->addContactToGroup($this->contactIdentifier, $this->groups->SC['id']); break;
					case "Expert": $this->addContactToGroup($this->contactIdentifier, $this->groups->active['id']); break;
					case "Senior": $this->addContactToGroup($this->contactIdentifier, $this->groups->senior['id']); break;
				}
			}
		}
		$pMagazine = $this->dbAdapter->query("SELECT `role` FROM `pum_conversie_person_role` WHERE `person_unid` = '".$this->contactRow['unid']."' AND REPLACE(REPLACE(role, '\r', ''), '\n', '') <> 'LR'");
		if($pMagazine->num_rows > 0) $this->addContactToGroup($this->contactIdentifier, $this->groups->magazine['id']);
	}
	
	private function addContactToGroup($contact_id, $group_id) {
		try{
			civicrm_api3('GroupContact', 'create', array("group_id" => $group_id, "contact_id" => $contact_id));
		} catch (Exception $e) {
			echo $e;
			echo "Koppeling contact met groep mislukt!\r\n";
		}
	}
	
	private function setGroupDates() {
		if($this->contactRow['ActivationDate'] != '0000-00-00') {
			$this->cdbAdapter->query("
				UPDATE `civicrm_subscription_history`
				SET `date` = '".$this->contactRow['ActivationDate']." 00:00:00' 
				WHERE `contact_id` = '".$this->contactIdentifier."' 
				AND `group_id` NOT IN ('".$this->groups->SC['id']."','".$this->groups->CC['id']."')
			");
		}
		if($this->contactRow['datacontractstart'] != '0000-00-00') {
			$this->cdbAdapter->query("
				UPDATE `civicrm_subscription_history` 
				SET `date` = '".$this->contactRow['datacontractstart']." 00:00:00' 
				WHERE `contact_id` = '".$this->contactIdentifier."' 
				AND `group_id` IN ('".$this->groups->SC['id']."','".$this->groups->CC['id']."','".$this->groups->LR['id']."')
			");
		}
	}
	
}

new contacts;