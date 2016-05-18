<?php

class FileField extends UIElement {

    function FileField($id, $name, $value, $validations=VALIDATE_NOTHING){
        parent::UIElement($id, $name, $value, $validations);
        $this->attr('size', 40);
        $this->value = @$_FILES[$this->id]['name'];
    }

    function render(){
        return "<input type='file' {$this->renderAttr()} name='{$this->id}' />";
    }

}


?>
