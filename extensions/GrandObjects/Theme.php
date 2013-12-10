<?php

class Theme {
    
    var $id;
    var $acronym;
    var $name;
    var $description;
    var $phase;
    
    static function newFromId($id){
        $data = DBFunctions::select(array('grand_themes'),
                                    array('*'),
                                    array('id' => EQ($id)));
        return new Theme($data);
    }
    
    static function newFromName($name){
        $data = DBFunctions::select(array('grand_themes'),
                                    array('*'),
                                    array('acronym' => EQ($name)));
        return new Theme($data);
    }
    
    static function getAllThemes($phase="%"){
        $data = DBFunctions::select(array('grand_themes'),
                                    array('*'),
                                    array('phase' => LIKE($phase)));
        $themes = array();
        foreach($data as $row){
            $themes[] = new Theme(array($row));
        }
        return $themes;
    }
    
    function Theme($data){
        if(count($data) > 0){
            $this->id = $data[0]['id'];
            $this->acronym = $data[0]['acronym'];
            $this->name = $data[0]['name'];
            $this->description = $data[0]['description'];
            $this->phase = $data[0]['phase'];
        }
    }
    
    function getId(){
        return $this->id;
    }
    
    function getAcronym(){
        return $this->acronym;
    }
    
    function getName(){
        return $this->name;
    }
    
    function getDescription(){
        return $this->description;
    }
    
    function getPhase(){
        return $this->phase;
    }

}

?>
