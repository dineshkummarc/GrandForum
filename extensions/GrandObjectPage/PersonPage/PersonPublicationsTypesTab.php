<?php

class PersonPublicationsTypesTab extends PersonPublicationsTab {

    function PersonPublicationsTypesTab($person, $visibility, $category='all', $startRange=CYCLE_START, $endRange=CYCLE_END){
        parent::PersonPublicationsTab($person, $visibility, $category, $startRange=CYCLE_START, $endRange=CYCLE_END);
    }

    function generateBody(){
        global $wgUser, $wgServer, $wgScriptPath;
        if(!$wgUser->isLoggedIn()){
            return "";
        }
        $this->html .= "<div id='{$this->id}'>
                        <table>
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td><input type='datepicker' name='startRange' value='{$this->startRange}' size='10' /></td>
                                <td><input type='datepicker' name='endRange' value='{$this->endRange}' size='10' /></td>
                                <td><input type='button' value='Update' /></td>
                            </tr>
                        </table>
                        <script type='text/javascript'>
                            $('div#{$this->id} input[type=datepicker]').datepicker({
                                dateFormat: 'yy-mm-dd',
                                changeMonth: true,
                                changeYear: true,
                                yearRange: '1900:".(date('Y')+3)."'
                            });
                            $('div#{$this->id} input[type=button]').click(function(){
                                var startRange = $('div#{$this->id} input[name=startRange]').val();
                                var endRange = $('div#{$this->id} input[name=endRange]').val();
                                document.location = '{$this->person->getUrl()}?tab={$this->id}&startRange=' + startRange + '&endRange=' + endRange;
                            });
                        </script>
                        </div>";
        $structures = Product::structure();
        $types = $structures['categories'][$this->category]['types'];
        foreach($types as $type => $data){
            $this->html .= "<h3>{$type}</h3>";
            $this->html .= $this->showTable($this->person, $this->visibility, $type);
        }
        if($this->visibility['isMe'] || $this->visibility['isSupervisor']){
            $this->html .= "<br /><input type='button' onClick='window.open(\"$wgServer$wgScriptPath/index.php/Special:ManageProducts#/".urlencode($this->category)."\");' value='Manage Outputs' />";
        }
    }

}    
?>