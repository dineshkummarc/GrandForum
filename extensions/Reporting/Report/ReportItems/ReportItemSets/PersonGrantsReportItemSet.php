<?php

class PersonGrantsReportItemSet extends ReportItemSet {
    
    function getData(){
        $phase = $this->getAttr("phase");
        $data = array();
        $person = Person::newFromId($this->personId);
        $start = $this->getAttr('start', REPORTING_CYCLE_START);
        $end = $this->getAttr('end', REPORTING_CYCLE_END);
        $grants = $person->getGrantsBetween($start, $end);
        if(is_array($grants)){
            foreach($grants as $grant){
                $tuple = self::createTuple();
                $tuple['project_id'] = $grant->id;
                $data[] = $tuple;
            }
        }
        return $data;
    }

}

?>
