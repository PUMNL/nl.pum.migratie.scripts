<?php
include('./baseclass.php');

class phone extends baseclass {
    
    private $counter = 0;
    
    public function __construct() {
        parent::baseclass();
        $persons = $this->dbAdapter->query("SELECT * FROM `pum_conversie_person`");
        while($personRow = $persons->fetch_assoc()) {
            /* Find the person */
            $contact_id = $this->findPerson($personRow['shortname']);
            if($contact_id) {
                /* Phone 1 */
                if((empty($personRow['PhoneCountry1']) OR empty($personRow['PhoneArea1'])) AND !empty($personRow['PhoneNumber1'])) {
                    $phoneNumber = $personRow['PhoneCountry1'].$personRow['PhoneArea1'].$personRow['PhoneNumber1'];
                    if(strlen($phoneNumber) > 5) {
                        $phoneNumber = preg_replace("/[^0-9,.]/", "", $phoneNumber);
                        if(!$this->phoneExists($contact_id, $phoneNumber)) {
                            $phoneNumber = (!empty($personRow['PhoneCountry1'])) ? "+".$phoneNumber : $phoneNumber;
                            $this->registerPhone($contact_id, $phoneNumber, 1, 1);
                        }
                    }
                }
                 /* Phone 2 */
                if((empty($personRow['PhoneCountry2']) OR empty($personRow['PhoneArea2'])) AND !empty($personRow['PhoneNumber2'])) {
                    $phoneNumber = $personRow['PhoneCountry2'].$personRow['PhoneArea2'].$personRow['PhoneNumber2'];
                    if(strlen($phoneNumber) > 5) {
                        $phoneNumber = preg_replace("/[^0-9,.]/", "", $phoneNumber);
                        if(!$this->phoneExists($contact_id, $phoneNumber)) {
                            $phoneNumber = (!empty($personRow['PhoneCountry2'])) ? "+".$phoneNumber : $phoneNumber;
                            $this->registerPhone($contact_id, $phoneNumber, 1, 1);
                        }
                    }
                }
                 /* Mobile 1 */
                if((empty($personRow['MobileCountry1']) OR empty($personRow['MobileArea1'])) AND !empty($personRow['MobileNumber1'])) {
                    $phoneNumber = $personRow['MobileCountry1'].$personRow['MobileArea1'].$personRow['MobileNumber1'];
                    if(strlen($phoneNumber) > 5) {
                        $phoneNumber = preg_replace("/[^0-9,.]/", "", $phoneNumber);
                        if(!$this->phoneExists($contact_id, $phoneNumber)) {
                            $phoneNumber = (!empty($personRow['MobileCountry1'])) ? "+".$phoneNumber : $phoneNumber;
                            $this->registerPhone($contact_id, $phoneNumber, 1, 2);
                        }
                    }
                }
                 /* Mobile 2 */
                if((empty($personRow['MobileCountry2']) OR empty($personRow['MobileArea2'])) AND !empty($personRow['MobileNumber2'])) {
                    $phoneNumber = $personRow['MobileCountry2'].$personRow['MobileArea2'].$personRow['MobileNumber2'];
                    if(strlen($phoneNumber) > 5) {
                        $phoneNumber = preg_replace("/[^0-9,.]/", "", $phoneNumber);
                        if(!$this->phoneExists($contact_id, $phoneNumber)) {
                            $phoneNumber = (!empty($personRow['MobileCountry2'])) ? "+".$phoneNumber : $phoneNumber;
                            $this->registerPhone($contact_id, $phoneNumber, 1, 2);
                        }
                    }
                }
            }
        }
        echo "Phone numbers created: ".$this->counter;
    }
    
    private function findPerson($shortname) {
        try {
            $person = civicrm_api3("Contact", "Getsingle", array("custom_14" => $shortname));
            return $person['id'];
        } catch (Exception $e) {
            return false;    
        }
    }

    private function phoneExists($contact_id, $phoneNumber) {
        try {
            civicrm_api3("Phone", "Getsingle", array("contact_id" => $contact_id, "phone_numeric" => $phoneNumber));
            return true;
        } catch (Exception $e) {
            return false;    
        }
    }
    
    private function registerPhone($contact_id, $phoneNumber, $location_type_id, $phone_type_id) {
        try {
            civicrm_api3("Phone", "Create", array("contact_id" => $contact_id, "phone" => $phoneNumber, "location_type_id" => $location_type_id, "phone_type_id" => $phone_type_id));
            $this->counter++;
            return true;
        } catch (Exception $e) {
            echo "Failed to create phone record (".$phoneNumber.") at contact ".$contact_id." \r\n";  
        }
    }
    
    
}

new phone;