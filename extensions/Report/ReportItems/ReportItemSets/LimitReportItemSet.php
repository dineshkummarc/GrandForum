<?php

class LimitReportItemSet extends ReportItemSet {
    
    function getData(){
        $data = array();
        $tuple = self::createTuple();
        $data[] = $tuple;
        return $data;
    }
    
    function getLimit(){
        return $this->getAttr('limit', 0);
    }
    
    function getNChars(){
        $textareas = $this->getTextareas();
        $nChars = 0;
        foreach($textareas as $textarea){
            $blobValue = str_replace("\r", "", $textarea->getBlobValue());
            $nChars += strlen($blobValue);
        }
        return min($this->getLimit(), $nChars);
    }
    
    function getActualNChars(){
        $textareas = $this->getTextareas();
        $nChars = 0;
        foreach($textareas as $textarea){
            $blobValue = str_replace("\r", "", $textarea->getBlobValue());
            $nChars += strlen($blobValue);
        }
        return $nChars;
    }
    
    function getExceedingFields(){
        if($this->getActualNChars() > $this->getLimit()){
            return 1;
        }
        return 0;
    }
    
    function getEmptyFields(){
        if($this->getActualNChars() == 0){
            return 1;
        }
        return 0;
    }
    
    function getTextareas($it=null){
        if($it == null){
            $it = $this;
        }
        $textareas = array();
        foreach($it->items as $item){
            if($item instanceof ReportItemSet){
                $textareas = array_merge($textareas, $this->getTextareas($item));
            }
            else if($item instanceof TextareaReportItem){
                $textareas[] = $item;
            }
        }
        return $textareas;
    }
    
    function getNTextareas(){
        return 1;
    }
    
    function render(){
        global $wgOut;
        $limit = $this->getLimit();
        $nChars = $this->getActualNChars();
        $textareas = $this->getTextareas();
        $noun = $this->getAttr("noun", "Project");
        $pluralNoun = strtolower($noun)."s";
        $recommended = $this->getAttr('recommended', false);
        if($recommended){
            $type = "recommended";
            $rec = 'true';
        }
        else{
            $type = "maximum of";
            $rec = 'false';
        }
        $wgOut->addHTML("<p id='limit_{$this->getPostId()}'><span class='pdf_hide inlineMessage'>(Reported By {$noun} - currently <span id='{$this->getPostId()}_chars_left'>{$nChars}</span> characters out of an overall {$type} {$limit} accross all $pluralNoun)</span>&nbsp;<a style='font-style:italic; font-size:11px; font-weight:bold;cursor:pointer;' onClick='popup{$this->getPostId()}();'><i>Preview</i></a><div id='preview_{$this->getPostId()}' style='display:none;'></div></p>
        <div id='div_{$this->getPostId()}'>");
        foreach($this->items as $item){
            $item->render();
        }
        $wgOut->addHTML("</div>");
        // Scripts
        $wgOut->addHTML("<script type='text/javascript'>
            var textareas = Array();\n");
        foreach($textareas as $textarea){
            $postId = $textarea->getId();
            $wgOut->addHTML("textareas.push($('textarea[name={$textarea->getPostId()}]'));\n");
        }
        $wgOut->addHTML("$('#div_{$this->getPostId()}').multiLimit($limit, $('#{$this->getPostId()}_chars_left'), textareas);
        $('#preview_{$this->getPostId()}').dialog({ autoOpen: false, width: '700', height: '450'});
        
        function popup{$this->getPostId()}(){
            $('#preview_{$this->getPostId()}').html($('#div_{$this->getPostId()}').html());
            var limit = {$limit};
            var recommended = {$rec};
            var blobValues = Array();
            
            $.each($('#div_{$this->getPostId()} textarea'), function(index, value){
                if(!recommended){
                    var blobValue = '';
                    var blobValue1 = $(value).val().substr(0, limit);
                    var blobValue2 = $(value).val().substr(limit);
                    if(blobValue2 != ''){
	                    blobValue = blobValue1 + '<s style=\"color:red;\">' + blobValue2 + '</s>';
	                }
	                else{
	                    blobValue = blobValue1;
	                }
                    limit -= blobValue1.length;
                }
                else{
                    var blobValue = $(value).val();
                }
                blobValues.push(blobValue);
            });
            
            $('#preview_{$this->getPostId()} .autocomplete').css('display', 'none');
            
            $.each($('#preview_{$this->getPostId()} textarea'), function(index, value){
                $(value).replaceWith('<span>' + blobValues[index].replace(/\\n/g, '<br />') + '</span>');
            });
            
            $('#preview_{$this->getPostId()} .pdfnodisplay').css('display', 'none');
            
            $('#preview_{$this->getPostId()}').dialog('open');
        }
        </script>");
    }
    
    function renderForPDF(){
        global $wgOut;
        $limit = $this->getLimit();
	    $length = $this->getActualNChars();
	    $textareas = $this->getTextareas();
	    $text = "";
	    $html = "";
	    $noun = $this->getAttr("noun", "Project");
        $pluralNoun = strtolower($noun)."s";
	    $recommended = $this->getAttr('recommended', false);
        if($recommended){
            $type = "recommended";
        }
        else{
            $type = "maximum of";
        }
	    if($limit > 0){
	        $class = "inlineMessage";
	        foreach($textareas as $textarea){
	            $blobValue = str_replace("\r", "", $textarea->getBlobValue());
	            if(!$recommended){
	                $blobValue1 = substr($blobValue, 0, $limit);
	                $blobValue2 = substr($blobValue, $limit);
	                $limit -= strlen($blobValue1);
	                if($blobValue2 != ""){
	                    if(isset($_GET['preview'])){
	                        $blobValue = "{$blobValue1}<s style='color:red;'>{$blobValue2}</s>";
	                    }
	                    else{
	                        $blobValue = "$blobValue1...";
	                    }
	                }
	                else{
	                    $blobValue = $blobValue1;
	                }
	            }
	            $text .= $textarea->processCData($blobValue);
	            if($length > $this->getLimit()){
	                $class = "inlineError";
	            }
	            else if($length == ""){
	                $class = "inlineWarning";
	            }
	        }
	        $html .= "<span class='$class'><small>(<i>Reported By {$noun} - currently {$length} chars out of a {$type} {$this->getLimit()} accross all {$pluralNoun}.</i>)</small></span>";
	        $html .= nl2br("<p>{$text}</p>");
	    }
	    $wgOut->addHTML($html);
    }
}

?>
