<?php

class DepartmentPeopleReportItemSet extends ReportItemSet {
    
    function getData(){
        $data = array();
        $dept = $this->getAttr("department", "");
        $uni = $this->getAttr("university", "University of Alberta");
        $start = $this->getAttr("start", REPORTING_CYCLE_START);
        $end = $this->getAttr("end", REPORTING_CYCLE_END);
        $includeDeansPeople = (strtolower($this->getAttr("includeDeansPeople", "false")) == "true");
        $excludeMe = (strtolower($this->getAttr("excludeMe", "false")) == "true");
        $me = Person::newFromWgUser();

        if(!$me->isRole(ISAC) && !$me->isRole(IAC) && !$me->isRoleDuring(ISAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
            // Person isn't a Chair/EA, so don't return anyone
            return $data;
        }
        
        $allPeople = Person::getAllPeopleDuring(NI, $start, $end);
        foreach($allPeople as $person){
            if($person->getCaseNumber($this->getReport()->year) == ""){
                continue;
                // Don't show if no case number
            }
            $found = false;
            if($includeDeansPeople){
                $found = ($person->isSubRole("DD") || 
                          $person->isSubRole("DA") ||
                          $person->isSubRole("DR"));
            }
            if(($dept == "") || 
               ($person->isInDepartment($dept, $uni, $start, $end)) || 
               ($found)){
                if($excludeMe && $person->isMe()){
                    // Should not see themselves in recommendations
                    continue;
                }
                if($person->isRoleDuring(DEAN, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRole(DEAN) || $person->isSubRole("VPR")){
                    // Dean should not be in recommendations
                    continue;
                }
                if($me->isRoleDuring(IAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END) && ($person->isRoleDuring(ISAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRole(ISAC))){
                    // EA should not get to see Chair's Information
                    continue;
                }
                if(($person->isRoleDuring(ISAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRole(ISAC)) && !$person->isSubRole("CR")){
                    // Chairs should not show up, unless they have an explicit Chair's Recommendation
                    continue;
                }
                if($me->isRoleDuring(ISAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END) && !$me->isRole(ISAC) && !$person->isSubRole("CR")){
                    // Previous Chair should not see any people except for those who have an explicit Chair's Recommendation
                    continue;
                }
                // SPECIAL CASES BELOW
                if(($me->getName() == "Ioanis.Nikolaidis" || $me->getName() == "CS.ExecutiveAssistant") && $person->getName() == "Eleni.Stroulia"){
                    continue;
                }
                if(($me->getName() == "Anthony.Singhal" || $me->getName() == "PSYCH.ExecutiveAssistant" || $me->getName() == "Jannie.Boulter") && $person->getName() == "Deanna.Singhal"){
                    continue;
                }
                $tuple = self::createTuple();
                $tuple['person_id'] = $person->getId();
                $tuple['extra'] = $person->getCaseNumber($this->getReport()->year);
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
