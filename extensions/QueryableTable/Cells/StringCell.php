<?php

class StringCell extends Cell{
    
    function StringCell($cellType, $params, $cellValue, $rowN, $colN, $table){
        $this->value = $cellValue;
    }
    
    function rasterize(){
        return array(STRING, $this);
    }
    
    function toString(){
        return $this->value;
    }
    
    function render(){
        return $this->value;
    }
}

?>
