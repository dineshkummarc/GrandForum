<?php

class FECReportItemSet extends ReportItemSet {
    
    static $people = array();
    
    static function generateFECCache(){
        if(count(self::$people) == 0){
            $people = DBFunctions::select(array('grand_role_subtype'),
                                          array('user_id'),
                                          array('sub_role' => EQ('FEC')));
            foreach($people as $row){
                self::$people[] = Person::newFromId($row['user_id']);
            }
        }
    }
    
    function getData(){
        $data = array();
        self::generateFECCache();
        foreach(self::$people as $person){
            $tuple = self::createTuple();
            $tuple['person_id'] = $person->getId();
            $data[] = $tuple;
        }
        return $data;
    }
}

?>
