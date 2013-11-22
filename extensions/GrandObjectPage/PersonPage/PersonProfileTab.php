<?php

class PersonProfileTab extends AbstractEditableTab {

    var $person;
    var $visibility;

    function PersonProfileTab($person, $visibility){
        parent::AbstractEditableTab("Profile");
        $this->person = $person;
        $this->visibility = $visibility;
    }

    function generateBody(){
        global $wgUser;
        $this->showProfile($this->person, $this->visibility);
        if($wgUser->isLoggedIn()){
            $this->showEthics($this->person, $this->visibility);
        }
        return $this->html;
    }
    
    function generateEditBody(){
        $this->showEditProfile($this->person, $this->visibility);
        $this->showEditEthics($this->person, $this->visibility);
    }
    
    function canEdit(){
        return ($this->visibility['isMe'] || 
                $this->visibility['isSupervisor']);
    }
    
    function handleEdit(){
        $_POST['user_name'] = $this->person->getName();
        $_POST['type'] = "public";
        $_POST['profile'] = str_replace("'", "&#39;", $_POST['public_profile']);
        $_POST['profile'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['profile'])));
        APIRequest::doAction('UserProfile', true);
        $_POST['type'] = "private";
        $_POST['profile'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['private_profile'])));
        APIRequest::doAction('UserProfile', true);
        if($this->person->isHQP()){
            APIRequest::doAction('UserEthics', true);
        }
        Person::$cache = array();
        Person::$namesCache = array();
        Person::$idsCache = array();
        $this->person = Person::newFromId($this->person->getId());
    }
    
    /*
     * Displays the profile for this user
     */
    function showProfile($person, $visibility){
        global $wgUser;
        $this->html .= nl2br($person->getProfile($wgUser->isLoggedIn()));
    }
    
    function showEditProfile($person, $visibility){
        $this->html .= "<table>
                            <tr>
                                <td align='right' valign='top'><b>GRAND Website:</b></td>
                                <td><textarea style='width:600px; height:150px;' name='public_profile'>{$person->getProfile(false)}</textarea></td>
                            </tr>
                            <tr>
                                <td align='right' valign='top'><b>GRAND Forum:</b></td>
                                <td><textarea style='width:600px; height:150px;' name='private_profile'>{$person->getProfile(true)}</textarea></td>
                            </tr>
                        </table>";
    }

    /*
     * Displays the profile for this user
     */
    function showEthics($person, $visibility){
        global $wgUser, $wgServer, $wgScriptPath;
        
        $ethics = $person->getEthics();
        $completed_tutorial = ($ethics['completed_tutorial'])? "Yes" : "No";
        $date = ($ethics['date'] == '0000-00-00')? "" : $ethics['date'];
        $ethics_str = "<h3>Ethics: Have not completed the TCPS2 tutorial.</h3>";
        if($completed_tutorial == "Yes"){
            $ethics_str = "<table><tr>
            <td><img style='vertical-align:bottom;' width='100px' src='$wgServer$wgScriptPath/skins/cavendish/ethical_btns/ethical_button.jpg' /></td>
            <td>&nbsp;<h3>I have completed the TCPS2 tutorial on {$date}.</h3></td>
            <tr></table>";
        }
        else{
            $ethics_str = "<table><tr>
            <td><img style='vertical-align:bottom;' width='100px' src='$wgServer$wgScriptPath/skins/cavendish/ethical_btns/ethical_button_not.jpg' /></td>
            <td>&nbsp;<h3>I have not completed the TCPS2 tutorial.</h3></td>
            <tr></table>";
        }
        if($person->isHQP()){
            $this->html .=<<<EOF
            {$ethics_str}
EOF;
        }
        else if($person->isCNI() || $person->isPNI()){
            $relations = $person->getRelations("Supervises");
            $total_hqp = 0;
            $ethical_hqp = 0;
            foreach($relations as $r){
                $hqp =  $r->getUser2();
                $ethics = $hqp->getEthics();
                if($ethics['completed_tutorial']){
                    $ethical_hqp++;
                }
                $total_hqp++;
            }
            $perc = 0;
            if($total_hqp >0 ){
                $perc = $ethical_hqp/$total_hqp;
            //$perc = floor($perc / 0.25)*0.25;
            }
            $perc = round($perc*100);
            if($ethical_hqp == 0){
                $perc = "";
                $button = "ethical_button_not.jpg";
            }
            else{
                $perc .= "%";
                $button = "ethical_button_ni.jpg";
            }

            $this->html .=<<<EOF
            <style>
            span.supervisor_lbl{
                text-align: center;
                color: #8C529D;
                bottom: 0px;
                left: 7px;
                display: block;
                font-size: 15px;
                font-weight: bold;
            }
            span.percent_lbl{
                text-align: center;
                color: #8C529D;
                top: 3px;
                right: 25px;
                display: block;
                font-size: 12px;
                font-weight: bold;
            }
            </style>
            <table><tr>
            <td style='position:relative; padding:0;'>
                <span class='percent_lbl'>{$perc}</span>
                <img style='vertical-align:bottom;' width='100px' src='$wgServer$wgScriptPath/skins/cavendish/ethical_btns/{$button}' />
                <span class='supervisor_lbl'>Supervisor</span>
            </td>
            <td style='padding-left:15px;'><h3>{$ethical_hqp} of my {$total_hqp} students have completed the TCPS2 Tutorial.</h3></td>
            <tr></table>
EOF;
        }

    }
    
    function showEditEthics($person, $visibility){

        $ethics = $person->getEthics();

        if($ethics['completed_tutorial']){
            $completed_tutorial_y = "checked='checked'";
            $completed_tutorial_n = "";
        }
        else{
            $completed_tutorial_n = "checked='checked'";
            $completed_tutorial_y = "";
        }

        $date = ($ethics['date'] == '0000-00-00')? "" : $ethics['date'];
        if($person->isHQP()){
            $this->html .=<<<EOF
            <script>
            $(function() {
                $( "#datepicker" ).datepicker( { dateFormat: "yy-mm-dd" } );
            });
            </script>
            <br /><br />
            <table border='0' cellpadding='5' cellspacing='0' width='70%'>
            <tr>
            <td>
            <i>
            <p>All GRAND HQP are required to complete the TCPS2 tutorial <b>Course on Research Ethics (CORE)</b>.  This interactive online tutorial can be completed in approximately two hours and provides an essential orientation to the Tri Council Policy Statement.</p>
            <p>Please note, the current version of the ethics module was released February 2011. If you completed a previous version (i.e. the one that HQP were asked to complete when GRAND started), you are still required to complete the most recent version.</p>
            </i>
            </td>
            </tr>
            </table>
            <table border='0' cellpadding='5' cellspacing='0'>
            <tr><th align='right' style='padding-right:15px;'>I have completed the TCPS2 tutorial:<br />
                <a target='_blank' href="http://grand-nce.ca/resource/tcps2-core">http://grand-nce.ca/resource/tcps2-core</a></th>
                <td valign='top'>
                    Yes <input type='radio' value='1' name='completed_tutorial' {$completed_tutorial_y} />&nbsp;&nbsp;
                    No <input type='radio' value='0' name='completed_tutorial' {$completed_tutorial_n} />
                </td>
            </tr>
            <tr>
                <th align='right' style='padding-right:15px;'>Date: </th>
                <td width='10%'>
                    <input id='datepicker' name='date' type='text' value='{$date}' />
                </td>
            </tr>
            </table>
EOF;
        }
    }
    
}
?>
