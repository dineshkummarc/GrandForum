<?php

class TextareaReportItem extends AbstractReportItem {

	function render(){
		global $wgOut;
		$item = $this->getHTML();
		if($this->getlimit() > 0){
		    $item .= "<script type='text/javascript'>
		        $(document).ready(function(){
                    var strlen = $('textarea[name={$this->getPostId()}]').val().length;
                    changeColor{$this->getPostId()}($('textarea[name={$this->getPostId()}]'), strlen);
                });
		    </script>";
		}
		$item = $this->processCData($item);
		$wgOut->addHTML($item);
	}
	
	function getHTML(){
	    $value = $this->getBlobValue();
        $rows = $this->getAttr('rows', 5);
        $width = $this->getAttr('width', '100%');
        $height = $this->getAttr('height', '200px');
        $limit = $this->getLimit();
        $item = "";
        if($limit > 0){
            $recommended = $this->getAttr('recommended', false);
	        if($recommended){
	            $type = "recommended";
	        }
	        else{
	            $type = "maximum of";
	        }
	        
            $item =<<<EOF
            <p id='limit_{$this->getPostId()}'><span class="pdf_hide inlineMessage">(currently <span id="{$this->getPostId()}_chars_left">0</span> characters out of a {$type} {$limit})</span></p>
            <script type='text/javascript'>
                function changeColor{$this->getPostId()}(element, strlen){
                    if(strlen > $limit){
                        $('#limit_{$this->getPostId()} > span').addClass('inlineError');
                        $('#limit_{$this->getPostId()} > span').removeClass('warningError');
                    }
                    else if(strlen == 0){
                        $('#limit_{$this->getPostId()} > span').addClass('inlineWarning');
                        $('#limit_{$this->getPostId()} > span').removeClass('inlineError');
                    }
                    else{
                        $('#limit_{$this->getPostId()} > span').removeClass('inlineError');
                        $('#limit_{$this->getPostId()} > span').removeClass('inlineWarning');
                    }
                }
                $(document).ready(function(){
                    $('textarea[name={$this->getPostId()}]').off('limit');
                    $('textarea[name={$this->getPostId()}]').off('keyup');
                    $('textarea[name={$this->getPostId()}]').off('keypress');
                    $('textarea[name={$this->getPostId()}]').limit(1000000000000, '#{$this->getPostId()}_chars_left');
                    
                    $('textarea[name={$this->getPostId()}]').keypress(function(){
                        changeColor{$this->getPostId()}(this, $(this).val().length);
                    });
                    $('textarea[name={$this->getPostId()}]').keyup(function(){
                        changeColor{$this->getPostId()}(this, $(this).val().length);
                    });
                });
            </script>
EOF;
        }
		$item .= <<<EOF
			<textarea rows='$rows' style="height:{$height};width:{$width};" 
                        name="{$this->getPostId()}">$value</textarea>
EOF;
        return $item;
	}
	
	function getLimit(){
	    return $this->getAttr('limit', 0);
	}
	
	function getNChars(){
	    $blobValue = str_replace("\r", "", $this->getBlobValue());
	    return min($this->getLimit(), strlen($blobValue));
	}
	
	function getActualNChars(){
	    $blobValue = str_replace("\r", "", $this->getBlobValue());
	    return strlen($blobValue);
	}
	
	function renderForPDF(){
	    global $wgOut;
	    $limit = $this->getLimit();
	    $html = "";
	    $blobValue = str_replace("\r", "", $this->getBlobValue());
	    $recommended = $this->getAttr('recommended', false);
	    $length = strlen($blobValue);
	    $class = "inlineMessage";
	    if($limit > 0){
	        if(!$recommended){
	            $type = "maximum of";
	            $blobValue1 = substr($blobValue, 0, $limit);
	            $blobValue2 = substr($blobValue, $limit);
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
	            if($blobValue2 != ""){
	                $class = "inlineError";
	            }
	            else if($blobValue1 == ""){
	                $class = "inlineWarning";
	            }
	        }
	        else{
	            $type = "recommended";
	        }
	        $plural = "s";
	        if($length == 1){
	            $plural = "";
	        }
	        $html .= "<span class='$class'><small>(<i>currently {$length} character{$plural} out of a {$type} {$limit}</i>)</small></span>";
	    }
	    $html .= nl2br("<p>{$blobValue}</p>");
	    $item = $this->processCData($html);
		$wgOut->addHTML($item);
	}
}

?>
