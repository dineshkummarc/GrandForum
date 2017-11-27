<?php

class AllSopReviewerReportItemSet extends ReportItemSet {

    function getData(){
        $data = array();
        $roles = explode(",",$this->getAttr("roles", ""));
	$projectName = $_GET['project'];
        if(strpos($projectName, 'Sop') !== false ){
            $sopString = explode(":",$projectName);
            $sopId = $sopString[1];
        } 
        $sop = SOP::newFromId($sopId);
	$reviewers = $sop->getReviewers();
	$allPeople = array();
	foreach($reviewers as $pId){
	     $allPeople[] = Person::newFromId($pId);
	}
        foreach($allPeople as $person){
            $tuple = self::createTuple();
            $tuple['person_id'] = $person->getId();
            $data[] = $tuple;
        }
        return $data;
    }
}

?>
