<?php
require_once('./baseclass.php'); // BASECLASS
class education extends baseclass {
	
	public $resultSet, $educatorRow, $educatorParams, $relationParams, $argv; // GLOBALS
	
	public function __construct($argv) {
	
		/* Initialize module */
		echo "Start module: Education - ".date("h:i:s")." \r\n";
		parent::baseclass(); // Fetch dependencies
		$this->argv = $argv;
		$this->resultSet = $this->dbAdapter->query("
			SELECT `pce`.*, `pcp`.`status`
			FROM `pum_conversie_education` as `pce`
			LEFT JOIN `pum_conversie_person` as `pcp` ON `pce`.`person_unid` = `pcp`.`unid`
			WHERE `pcp`.`status` NOT IN ('Exit', 'Rejected')
			LIMIT ".$argv[2].", 2500
		");
		$this->fetchCustom();
		$this->migration();
		echo "End module: Education - ".date("h:i:s")." \r\n";
		
	}
	
	public function fetchCustom() {
		
		/* Fetch all custom fields and data */
		try {
			/* Subcontact-types */
			$this->contactSubType 					= new stdClass();
			$this->contactSubType->educator			= civicrm_api3('ContactType', 'getsingle', array("name" => "Educator"));
			/* Relationship-types  */
			$this->relationshipTypes				= new stdClass();
			$this->relationshipTypes->education		= civicrm_api3('RelationshipType', 'getsingle', array("name_a_b" => "Educated", "name_b_a" => "Education"));
			/* Customfields */
			$this->customFields 					= new stdClass();
			// Additional Information
			$this->customFields->additionalGroup	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Additional_Information"));
			$this->customFields->prins_unid			= civicrm_api3('CustomField', 'getsingle', array("name" => "Prins_UNID", "custom_group_id" => $this->customFields->additionalGroup['id']));
			// Education Relationship
			$this->customFields->educationGroup		= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Education_Information"));
			$this->customFields->edu_city			= civicrm_api3('CustomField', 'getsingle', array("name" => "City", "custom_group_id" => $this->customFields->educationGroup['id']));
			$this->customFields->edu_country		= civicrm_api3('CustomField', 'getsingle', array("name" => "Country", "custom_group_id" => $this->customFields->educationGroup['id']));
			$this->customFields->edu_level			= civicrm_api3('CustomField', 'getsingle', array("name" => "Level", "custom_group_id" => $this->customFields->educationGroup['id']));
			$this->customFields->edu_study			= civicrm_api3('CustomField', 'getsingle', array("name" => "Study", "custom_group_id" => $this->customFields->educationGroup['id']));
		} catch (Exception $e) {
			die ($e);
		}

	}
	
	public function migration() {
		
		/* This is where the magic happens! */
		
		if($this->argv[1] == "create") {
		
			// Create Educators
			$this->createEducators();
			
		} else {
			
			// Check if we have valid query data
			if(!$this->resultSet || is_null($this->resultSet)) die("Query failed - Exit script \r\n");
			
			// Loop trough all the relationships
			while($this->educatorRow = $this->resultSet->fetch_assoc()) {
				
				// Fetch educator and educator
				try {
					
					$_contact_a = civicrm_api3('Contact', 'getsingle', array("organization_name" => trim($this->educatorRow['institutionname'])));
					$_contact_b = civicrm_api3('Contact', 'getsingle', array("custom_".$this->customFields->prins_unid['id'] => $this->educatorRow['person_unid']));
					
					// Generate relationship parameter array
					$this->relationParams = array(
						'contact_id_a' => $_contact_a['id'],
						'contact_id_b' => $_contact_b['id'],
						'relationship_type_id' => $this->relationshipTypes->education['id'],
						'start_date' => $this->educatorRow['from']."-01-01",
						'end_date' => $this->educatorRow['until']."-01-01",
						'custom_'.$this->customFields->edu_city['id'] => trim($this->educatorRow['city']),
						'custom_'.$this->customFields->edu_country['id'] => trim($this->educatorRow['country']),
						'custom_'.$this->customFields->edu_level['id'] => trim($this->educatorRow['educationlevel']),
						'custom_'.$this->customFields->edu_study['id'] => trim($this->educatorRow['study'])
					);
					
					// Forge a relationship between educator and student
					if(!$this->CIVIAPI->Relationship->Create($this->relationParams)){
					
						// Forging relationship failed
						echo "Relationship failed to forge between ".$_contact_a['organization_name']." and ".$_contact_b['display_name']."\r\n";
						echo $this->CIVIAPI->errorMsg()."\r\n";
						
					}
					
				} catch(Exception $e) {
				
					// Educator or student not found
					echo "educator or student not found for student record: ".$this->educatorRow['person_unid']." -> ".$this->educatorRow['institutionname']."\r\n";
					echo $e."\r\n";
					
				}
		
			}
		
		}
		
	}

	private function createEducators() {
	
		/* This functions creates every unique named educator */
		
		// Fetch all educators
		$_educators = $this->dbAdapter->query("
			SELECT `pce`.`institutionname`, `pcp`.`status`
			FROM `pum_conversie_education` as `pce`
			LEFT JOIN `pum_conversie_person` as `pcp` ON `pce`.`person_unid` = `pcp`.`unid`
			WHERE `pcp`.`status` NOT IN ('Exit', 'Rejected')
			GROUP BY `pce`.`institutionname`
			LIMIT ".$this->argv[2].", 2500
		");
		
		// Check if we have a query result
		if(!$_educators || is_null($_educators)) die("Educators query failed - Exit script \r\n");
		
		// Start loop!
		while($_educator = $_educators->fetch_assoc()){
			
			// Check if organization is not empty
			if(!empty($_educator['institutionname']) && strlen($_educator['institutionname']) > 3) {
			
				// Store organization data in array
				$this->educatorParams = array(
					'contact_type' 		=> 'organization',
					'contact_sub_type' 	=> $this->contactSubType->educator['name'],
					'organization_name' => trim($_educator['institutionname'])
				);
				
				// Insert organization 
				if(!$this->CIVIAPI->Contact->Create($this->educatorParams)) {
				
					// Creating organization failed
					echo "Saving organization ".$_educator['institutionname']." failed\r\n";
					echo $this->CIVIAPI->errorMsg()."\r\n";
					
				}
			
			}
			
		}
	
	}
	
}

new education($argv);