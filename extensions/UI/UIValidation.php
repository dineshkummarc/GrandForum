<?php

abstract class UIValidation {
    
    var $negation;
    var $result;
    var $warning;
    
    function UIValidation($neg=false, $warning=false){
        $this->negation = $neg;
        $this->warning = $warning;
    }
    
    function validate($value){
        $this->result = $this->validateFn($value);
        if($this->negation){
            $this->result = !$this->result;
        }
        return $this->result;
    }
    
    function getMessage($name){
        if($this->neg){
            if($this->warning){
                return array('warning' => $this->warningNegMessage($name));
            }
            else{
                return array('error' => $this->failNegMessage($name));
            }
        }
        else{
            if($this->warning){
                return array('warning' => $this->warningMessage($name));
            }
            else{
                return array('error' => $this->failMessage($name));
            }
        }
    }
    
    abstract function validateFn($value);
    
    abstract function failMessage($name);
    
    abstract function failNegMessage($name);
    
    abstract function warningMessage($name);
    
    abstract function warningNegMessage($name);
}

?>
