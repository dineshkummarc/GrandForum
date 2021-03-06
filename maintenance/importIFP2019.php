<?php

require_once('commandLine.inc');

$contents = explode("\n", file_get_contents("applicants.csv"));
$wgUser = User::newFromId(1);

function saveBlobValue($blobSection, $blobItem, $person, $value){
    $year = 2019;
    $personId = $person->getId();
    $projectId = 0;
    
    $blb = new ReportBlob(BLOB_TEXT, $year, $personId, $projectId);
    $addr = ReportBlob::create_address("RP_IFP_APPLICATION", $blobSection, $blobItem, 0, $personId);
    $blb->store($value, $addr);
}

function addUserUniversity($person, $uni, $dept, $pos){
    $_POST['university'] = $uni;
    $_POST['department'] = $dept;
    $_POST['position'] = $pos;
    $_POST['startDate'] = date('Y-m-d');
    $_POST['endDate'] = '0000-00-00 00:00:00';
    $api = new PersonUniversitiesAPI();
    $api->params['id'] = $person->getId();
    $api->doPOST();
}

foreach($contents as $line){
    $csv = str_getcsv($line);
    if(count($csv) == 32){
        $fileNumber = trim($csv[0]);
        $fundingLevel = trim(str_replace("’", "'", str_replace("Fellowship – ", "", $csv[1])));
        $firstName = trim($csv[2]);
        $lastName = trim($csv[3]);
        $gender = trim($csv[4]);
        $formOfAddress = trim($csv[6]);
        $designation = trim($csv[7]);
        $citizenship = trim($csv[8]);
        $country = trim($csv[9]);
        $city = trim($csv[10]);
        $province = trim($csv[11]);
        $currentCountry = trim($csv[12]);
        $phone = trim($csv[13]);
        $email = trim($csv[14]);
        $discipline = trim($csv[15]);
        $institution = trim($csv[17]);
        
        $supFirstName = trim($csv[18]);
        $supLastName = trim($csv[19]);
        $supFormOfAddress = trim($csv[20]);
        $supDesignation = trim($csv[21]);
        $supTitle = trim($csv[22]);
        $supPhone = trim($csv[23]);
        $supEmail = trim($csv[24]);
        $supInstitution = trim($csv[25]);
        $partnerOrganization = trim($csv[27]);
        $projectTitle = trim($csv[28]);
        $theme = trim($csv[29]);
        
        $username = str_replace(" ", "", $firstName).".".str_replace(" ", "", $lastName);
        $hqp = Person::newFromName($username);
        if($hqp == null || $hqp->getId() == 0){
            // Check with email
            $hqp = Person::newFromEmail($email);
        }
        echo "HQP: $username\n";
        if($hqp == null || $hqp->getId() == 0){
            // HQP Doesn't exist yet
            $user = User::createNew($username, array('real_name' => "$firstName $lastName", 
                                                     'password' => User::crypt(mt_rand()), 
                                                     'email' => $email));
            Person::$cache = array();
            Person::$namesCache = array();
            $hqp = Person::newFromUser($user);
        }
        
        if(!$hqp->isRole(HQP) && !$hqp->isRole(HQP."-Candidate")){
            $role = new Role(array());
            $role->user = $hqp->getId();
            $role->role = HQP;
            $role->startDate = date('Y-m-d');
            $role->create();
        }
        
        if(count(DBFunctions::select(array('grand_role_subtype'),
                                     array('user_id'),
                                     array('user_id' => $hqp->getId(),
                                           'sub_role' => "IFP2019Applicant"))) == 0){
            DBFunctions::insert('grand_role_subtype',
                                    array('user_id' => $hqp->getId(),
                                          'sub_role' => "IFP2019Applicant"));
        }
        
        $username = str_replace(" ", "", $supFirstName).".".str_replace(" ", "", $supLastName);
        $sup = Person::newFromName($username);
        if($sup == null || $sup->getId() == 0){
            // Check with email
            $sup = Person::newFromEmail($supEmail);
        }
        if($sup == null || $sup->getId() == 0){
            // Supervisor doesn't exist yet
            $user = User::createNew($username, array('real_name' => "$supFirstName $supLastName", 
                                                     'password' => User::crypt(mt_rand()), 
                                                     'email' => $supEmail));
            Person::$cache = array();
            Person::$namesCache = array();
            $sup = Person::newFromUser($user);
            
            DBFunctions::update('mw_user',
                            array('candidate' => 1),
                            array('user_id' => $sup->getId()));
        }
        
        if(!$sup->relatedTo($hqp, SUPERVISES)){
            $relationship = new Relationship(array());
            $relationship->user1 = $sup->getId();
            $relationship->user2 = $hqp->getId();
            $relationship->type = SUPERVISES;
            $relationship->startDate = date('Y-m-d');
            $relationship->create();
        }
        
        if(count($hqp->getUniversities()) == 0){
            $pos = "";
            switch($fundingLevel){
                case "PhD student":
                    $pos = "Graduate Student - Doctoral";
                    break;
                case "Master's student":
                    $pos = "Graduate Student - Master's";
                    break;
                case "Postdoctoral or working professional":
                    $pos = "Post-Doctoral Fellow";
                    break;
            }
            addUserUniversity($hqp, trim($institution), trim($discipline), trim($pos));
        }
        
        DBFunctions::update('mw_user',
                            array('user_gender' => $gender,
                                  'candidate' => 1),
                            array('user_id' => $hqp->getId()));
        
        saveBlobValue("INTENT", "NUMBER", $hqp, $fileNumber);
        saveBlobValue("INTENT", "LEVEL", $hqp, $fundingLevel);
        saveBlobValue("INTENT", "FIRST_NAME", $hqp, $firstName);
        saveBlobValue("INTENT", "LAST_NAME", $hqp, $lastName);
        saveBlobValue("INTENT", "DESIGNATIONS", $hqp, $designation);
        saveBlobValue("INTENT", "DISCIPLINE", $hqp, $discipline);
        saveBlobValue("INTENT", "ROLE", $hqp, $institution);
        saveBlobValue("INTENT", "EMAIL", $hqp, $email);
        saveBlobValue("INTENT", "GENDER", $hqp, $gender);
        saveBlobValue("INTENT", "CITIZENSHIP", $hqp, $citizenship);
        saveBlobValue("INTENT", "SUP_FIRST_NAME", $hqp, $supFirstName);
        saveBlobValue("INTENT", "SUP_LAST_NAME", $hqp, $supLastName);
        saveBlobValue("INTENT", "SUP_DESIGNATIONS", $hqp, $supDesignation);
        saveBlobValue("INTENT", "SUP_DISCIPLINE", $hqp, $supDesignation);
        saveBlobValue("INTENT", "SUP_ROLE", $hqp, $supTitle);
        saveBlobValue("INTENT", "SUP_EMAIL", $hqp, $supEmail);
        saveBlobValue("INTENT", "SUP_INSTITUTION", $hqp, $supInstitution);
        saveBlobValue("INTENT", "PARTNER", $hqp, $partnerOrganization);
        saveBlobValue("INTENT", "TITLE", $hqp, $projectTitle);
        saveBlobValue("INTENT", "THEME", $hqp, $theme);
    }
}

?>
