<?php

class RadioReportItem extends AbstractReportItem {

	function render(){
		global $wgOut;
        $options = $this->parseOptions();
        $value = $this->getBlobValue();
        $items = array();
		foreach($options as $option){
		    $checked = "";
		    if($value == $option){
		        $checked = "checked='checked'";
		    }
		    $items[] = "<input type='radio' name='{$this->getPostId()}' value='{$option}' $checked />&nbsp;{$option}";
		}

        $output = "";
        if($this->attributes['orientation'] == 'vertical'){
            $output = implode("<br />\n", $items);
        }
        else if($this->attributes['orientation'] == 'horizontal'){
            $output = implode("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $items);
        }
        if($this->getBlobValue() == ""){
            $output = "<input type='hidden' name='{$this->getPostId()}' value='' />".$output; 
        }
        $output = $this->processCData("<div>{$output}</div>");
		$wgOut->addHTML($output);
	}
	
	function parseOptions(){
	    $options = @explode("|", $this->attributes['options']);
	    return $options;
	}
	
	function renderForPDF(){
	    global $wgOut;
	    $item = $this->processCData($this->getBlobValue());
		$wgOut->addHTML($item);
	}
}

?>
