<?php

class DeansPeopleReportItemSet extends ReportItemSet {
    
    function getData(){
        $data = array();
        $roles = explode("|",$this->getAttr("roles", ""));
        $start = $this->getAttr("start", REPORTING_CYCLE_START);
        $end = $this->getAttr("end", REPORTING_CYCLE_END);
        $allPeople = Person::getAllPeople();

        $fecTypes = array();
        foreach($allPeople as $person){
            $found = false;
            foreach($roles as $role){
                if($person->isRoleDuring($role, $start, $end)){
                    $found = true;
                    break;
                }
            }
            if($found){
                $tuple = self::createTuple();
                
                $fecType = $person->getFECType();
                @$fecTypes[$fecType]++;
                $tuple['person_id'] = $person->getId();
                $tuple['extra'] = $person->getFECType().str_pad($fecTypes[$fecType], 2, "0", STR_PAD_LEFT);
                $data[] = $tuple;
            }
        }
        
        usort($data, function($a, $b){
            $A = Person::newFromId($a['person_id']);
            $B = Person::newFromId($b['person_id']);
            $A->getFecPersonalInfo();
            $B->getFecPersonalInfo();
            return ($a['extra'].$A->dateOfAppointment > $b['extra'].$B->dateOfAppointment);
        });
        return $data;
    }
}

?>
