<?php
require_once('./baseclass.php');

class excel extends baseclass {
	
	public $groups, $fields, $tags, $currentRow, $previousRow, $organizationIdentifier, $contactIdentifier;
	
	public function __construct() {
		parent::baseclass();
		echo "Start of Excel migration at ".date("H:i")."\r\n";
		$this->fetchCustom();
		$this->migration();
		echo "End of Excel migration at ".date("H:i")."\r\n";
	}
	
	private function fetchCustom() {
		$this->groups 					= new stdClass;
		$this->groups->magazine 		= civicrm_api3('Group', 'getsingle', array("title" => "PUM magazine"));
		$this->fields 					= new stdClass;
		$this->fields->additionalGroup 	= civicrm_api3('CustomGroup', 'getsingle', array("name" => "Additional_Data"));
		$this->fields->initials			= civicrm_api3('CustomField', 'getsingle', array("name" => "Initials", "custom_group_id" => $this->fields->additionalGroup['id']));
		$this->tags						= new stdClass;
		$this->tags->verzendlijst		= civicrm_api3('Tag', 'getsingle', array("name" => "Verzendlijst"));
	}

	private function migration() {
		$resultSet = $this->dbAdapter->query("SELECT * FROM `verzendlijst` ORDER BY `organization_name`, `street_address`");
		if(!$resultSet) die("Query failure! Terminating application. \r\n"."Exit error: ".$this->dbAdapter->error);
		if($resultSet->num_rows) {
			while($this->currentRow = $resultSet->fetch_assoc()) {
				// Contact only
				if($this->currentRow['organization_name'] == "nvt") {
					$this->organizationIdentifier = NULL;
					$this->registerContact();
				}
				// Same organization, different address
				elseif (
					trim($this->currentRow['organization_name']) == trim($this->previousRow['organization_name'])
						AND
					trim($this->currentRow['street_address']) != trim($this->previousRow['street_address'])
				) {
					$this->registerOrganization();
					$this->registerContact();
				}
				// Identical organization, new contact
				elseif (
					trim($this->currentRow['organization_name']) == trim($this->previousRow['organization_name'])
						AND
					trim($this->currentRow['street_address']) == trim($this->previousRow['street_address'])
				) {
					$this->registerContact();
				}
				// New organization
				else {
					$this->registerOrganization();
					$this->registerContact();
				}
				// Set previous row
				$this->previousRow = $this->currentRow;
			}
		} else {
			echo "No records were found, prehaps wrong where clause in query? \r\n";
		}
	}
	
	private function registerOrganization() {
		if($this->CIVIAPI->Contact->Create(array(
			'organization_name' => $this->currentRow['organization_name'],
			'contact_type' 		=> 'organization'
		))) {
			$this->organizationIdentifier = $this->CIVIAPI->lastResult->id;
			$this->registerAddress($this->organizationIdentifier, "organization");
			$this->tagContact($this->organizationIdentifier, $this->tags->verzendlijst);
		} else {
			echo "Failed to register organization ".$this->currentRow['id']."\r\n";
			echo $this->CIVIAPI->errorMsg()."\r\n";
		}
	}
	
	private function registerContact() {
		if(!empty($this->currentRow['first_name']) OR !empty($this->currentRow['last_name'])) {
			$gender = ($this->currentRow['gender'] == "heer") ? 1 : 2;
			$prefix = ($this->currentRow['gender'] == "heer") ? 3 : 1;
			if($this->CIVIAPI->Contact->Create(array(
				'prefix_id'									=> $prefix,
				'first_name' 								=> $this->currentRow['first_name'],
				'middle_name' 								=> $this->currentRow['middle_name'],
				'last_name' 								=> $this->currentRow['last_name'],
				'contact_type' 								=> 'individual',
				'gender'									=> $gender,
				'custom_'.$this->fields->initials['id'] 	=> $this->currentRow['initials'],
				'employer_id'								=> $this->organizationIdentifier
			))) {
				$this->contactIdentifier = $this->CIVIAPI->lastResult->id;
				$this->registerAddress($this->contactIdentifier, 'individual');
				$this->addToGroup($this->contactIdentifier, $this->groups->magazine);
				$this->tagContact($this->contactIdentifier, $this->tags->verzendlijst);
			} else {
				echo "Failed to register individual ".$this->currentRow['id']."\r\n";
				echo $this->CIVIAPI->errorMsg()."\r\n";
			}
		}
	}
	
	private function registerAddress($contactIdentifier, $type) {
		try {
			if($type == "organization") {
				civicrm_api3('Address', 'Create', array(
					'contact_id'		=> $contactIdentifier,
					'location_type_id'	=> 2,
					'primary'			=> 1,
					'street_address' 	=> $this->currentRow['street_address'],
					'postal_code' 		=> substr($this->currentRow['postcode'], 0 , 12),
					'city' 				=> $this->currentRow['city'],
					'country_id'		=> $this->determineCountry($this->currentRow['country'])
				));
			} else {
				civicrm_api3('Address', 'Create', array(
					'contact_id'		=> $contactIdentifier,
					'location_type_id'	=> 1,
					'primary'			=> 1,
					'street_address' 	=> $this->currentRow['i_street_address'],
					'postal_code' 		=> substr($this->currentRow['i_postcode'], 0 , 12),
					'city' 				=> $this->currentRow['i_city'],
					'country_id'		=> $this->determineCountry($this->currentRow['i_country'])
				));
			}
		} catch(Exception $e) {
			echo "Failed to register address for contact: ".$contactIdentifier."\r\n";
			echo $e."\r\n";
		}
	}
	
	private function determineCountry($country) {
		switch($country) {
			case "The Netherlands": return 1152; break;
			case "South Africa": 	return 1196; break;
			case "Belgium": 		return 1020; break;
			case "Italy": 			return 1107; break;
			case "Kenia": 			return 1112; break;
			default:				echo "Country $country is outside of switch scope\r\n"; break;
		}
	}
	
	private function addToGroup($contact_id, $group) {
		try{
			civicrm_api3('GroupContact', 'create', array("group_id" => $group['id'], "contact_id" => $contact_id));
		} catch (Exception $e) {
			echo "Failed to add $contact_id to ".$group['label']." \r\n";
			echo $e."\r\n";
		}
	}
	
	private function tagContact($contact_id, $tag) {
		try{
			civicrm_api3('EntityTag', 'create', array('entity_table' => 'civicrm_contact', 'entity_id' => $contact_id, 'tag_id' => $tag['id']));
		} catch (Exception $e) {
			echo "Failed to tag $contact_id with tag ".$tag['label']." \r\n";
			echo $e."\r\n";
		}
	}
	
}

new excel;
?>