<?php

$wgMessage = new Message();

class Message {
    
    var $errors = array();
    var $warnings = array();
    var $success = array();
    var $info = array();
    var $purpleInfo = array();
    var $errorIndex = 0;
    var $warningIndex = 0;
    var $successIndex = 0;
    var $infoIndex = 0;
    var $purpleIndex = 0;
    
    // Adds a (red) error message
    function addError($message, $index=false){
        if($index !== false){
            $this->errors[$index] = $message;
        }
        else{
            $this->errors[$this->errorIndex++] = $message;
        }
    }
    
    // Adds a (yellow) warning message
    function addWarning($message, $index=false){
        if($index !== false){
            $this->warnings[$index] = $message;
        }
        else{
            $this->warnings[$this->warningIndex++] = $message;
        }
    }
    
    // Adds a (green) success message
    function addSuccess($message, $index=false){
        if($index !== false){
            $this->success[$index] = $message;
        }
        else{
            $this->success[$this->successIndex++] = $message;
        }
    }
    
    // Adds a (blue) info message
    function addInfo($message, $index=false){
        if($index !== false){
            $this->info[$index] = $message;
        }
        else{
            $this->info[$this->infoIndex++] = $message;
        }
    }
    
    // Adds a (purple) info message
    function addPurpleInfo($message, $index=false){
        if($index !== false){
            $this->purpleInfo[$index] = $message;
        }
        else{
            $this->purpleInfo[$this->purpleIndex++] = $message;
        }
    }
    
    // Empties all error messages
    function clearError(){
        $this->errors = array();
        $this->errorIndex = 0;
    }
    
    // Empties all warning messages
    function clearWarning(){
        $this->warnings = array();
        $this->warningIndex = 0;
    }
    
    // Empties all success messages
    function clearSuccess(){
        $this->success = array();
        $this->successIndex = 0;
    }
    
    // Empties all info messages
    function clearInfo(){
        $this->info = array();
        $this->infoIndex = 0;
    }
    
    // Empties all purple info messages
    function clearPurpleInfo(){
        $this->purpleInfo = array();
        $this->purpleIndex = 0;
    }
    
    // Displays the messages
    function showMessages(){
        ksort($this->errors);
        ksort($this->warnings);
        $errors = implode("<br />\n", $this->errors);
        $warnings = implode("<br />\n", $this->warnings);
        $success = implode("<br />\n", $this->success);
        $info = implode("<br />\n", $this->info);
        $purpleInfo = implode("<br />\n", $this->purpleInfo);
        if($errors != ""){
            $errors = "<div class='error'><span style='display:inline-block;'>$errors</span></div>\n";
        }
        if($warnings != ""){
            $warnings = "<div class='warning'><span style='display:inline-block;'>$warnings</span></div>\n";
        }
        if($success != ""){
            $success = "<div class='success'><span style='display:inline-block;'>$success</span></div>\n";
        }
        if($info != ""){
            $info = "<div class='info'><span style='display:inline-block;'>$info</span></div>\n";
        }
        if($purpleInfo != ""){
            $purpleInfo = "<div class='purpleInfo'><span style='display:inline-block;'>$purpleInfo</span></div>\n";
        }
        echo $errors.$warnings.$success.$info.$purpleInfo;
        echo "<script type='text/javascript'>
            function closeParent(link){
                $(link).parent().fadeOut(250, function(){
                    $(this).remove();
                });
            }
            
            function addMessage(type, message){
                var isAnimated = $('#wgMessages .' + type).is(':animated');
                if($('#wgMessages .' + type).length && !isAnimated){
                    $('#wgMessages .' + type).append('<br />' + message);
                }
                else{
                    if(isAnimated){
                        $('#wgMessages .' + type).stop();
                    }
                    $('#wgMessages .' + type).remove();
                    $('#wgMessages').append('<div class=\"' + type + '\" style=\"display:none\"><span style=\"display:inline-block;\">' + message + '</span></div>');
                    addClose($('#wgMessages .' + type));
                    if(!isAnimated){
                        $('#wgMessages .' + type).fadeIn(300);
                    }
                    else{
                        $('#wgMessages .' + type).fadeIn(0);
                    }
                }
            }
            
            function clearMessage(type){
                $('#wgMessages .' + type).fadeOut(250, function(){
                    $(this).remove();
                });
            }
            
            function addError(message){
                addMessage('error', message);
            }
            
            function addSuccess(message){
                addMessage('success', message);
            }
            
            function addWarning(message){
                addMessage('warning', message);
            }
            
            function addInfo(message){
                addMessage('info', message);
            }
            
            function addPurpleInfo(message){
                addMessage('purpleInfo', message);
            }
            
            function clearError(){
                clearMessage('error');
            }
            
            function clearSuccess(){
                clearMessage('success');
            }
            
            function clearWarning(){
                clearMessage('warning');
            }
            
            function clearInfo(){
                clearMessage('info');
            }
            
            function clearPurpleInfo(){
                clearMessage('purpleInfo');
            }
            
            function addClose(messageBox){
                $(messageBox).append('<a class=\'error_box_close\' onClick=\'closeParent(this)\'>X</a>');
            }
            
            $(document).ready(function(){
                addClose($('.error, .warning, .success, .info, .purpleInfo').not('.notQuitable'));
            });
        </script>";
    }
}

?>
