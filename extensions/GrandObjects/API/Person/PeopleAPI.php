<?php

class PeopleAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam('role') != ""){
            $university = "";
            $department = "";
            if($this->getParam('university') != ""){
                $university = $this->getParam('university');
            }
            if($this->getParam('department') != ""){
                $department = $this->getParam('department');
            }
            $exploded = explode(",", $this->getParam('role'));
            $finalPeople = array();
            foreach($exploded as $role){
                $role = trim($role);
                if($role == 'all'){
                    // Get All people (including candidates)
                    $people = array_merge(Person::getAllPeople(), Person::getAllCandidates());
                }
                else{
                    // Get the specific role
                    $people = Person::getAllPeople($role);
                }
                foreach($people as $person){
                    if($university == "" && $department == ""){
                        $finalPeople[$person->getReversedName()] = $person;
                    }
                    else {
                        $unis = $person->getCurrentUniversities();
                        foreach($unis as $uni){
                            if($uni['university'] == $university){
                                if($department == "" || $department == $uni['department']){
                                    $finalPeople[$person->getReversedName()] = $person;
                                }
                            }
                        }
                    }
                }
            }
            ksort($finalPeople);
            $finalPeople = new Collection(array_values($finalPeople));
            return $finalPeople->toJSON();
        }
        else{
            $people = new Collection(Person::getAllPeople('all'));
            return $people->toJSON();
        }
    }
    
    function doPOST(){
        return false;
    }
    
    function doPUT(){
        return false;
    }
    
    function doDELETE(){
        return false;
    }

}

?>
