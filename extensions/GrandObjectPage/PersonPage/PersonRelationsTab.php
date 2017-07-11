<?php

class PersonRelationsTab extends AbstractTab {

    var $person;
    var $visibility;

    function PersonRelationsTab($person, $visibility){
        parent::AbstractTab("Supervisors");
        $this->person = $person;
        $this->visibility = $visibility;
    }

    function generateBody(){
        $this->showRelations($this->person, $this->visibility);
        return $this->html;
    }
    
    /**
     * Displays all of the user's relations
     */
    function showRelations($person, $visibility){
        global $wgUser, $wgOut, $wgScriptPath, $wgServer;
        if($wgUser->isLoggedIn() && ($visibility['edit'] || (!$visibility['edit'] && (count($person->getRelations('public')) > 0 || count($person->getSupervisors(true)) > 0 || ($visibility['isMe'] && count($person->getRelations()) > 0))))){
            if($person->isRoleAtLeast(HQP) || ($person->isRole(INACTIVE) && $person->wasLastRoleAtLeast(HQP))){
                if(count($person->getSupervisors(true)) > 0){
                    if($visibility['edit'] && $visibility['isSupervisor']){
                        $this->html .= "<a class='button' href='$wgServer$wgScriptPath/index.php/Special:ManagePeople'>Manage HQP</a>";
                    }
                    $this->html .= "<table id='relations_table' class='wikitable' width='100%' cellspacing='1' cellpadding='2' rules='all' frame='box'>
                                    <thead><tr><th>Start Date</th><th>End Date</th><th>Position</th><th>Last Name</th><th>First Name</th></tr></thead><tbody>";
                    foreach($person->getSupervisors(true) as $supervisor){
                        // TODO: These loops are a little inneficient, it should probably be extracted to a function, and optimized
                        $relations = array_merge($supervisor->getRelations(SUPERVISES, true), $supervisor->getRelations(CO_SUPERVISES, true));
                        foreach($relations as $r){
                            $hqp = $r->getUser2();
                            if($hqp->getId() == $person->getId()){
                                $start_date = substr($r->getStartDate(), 0, 10);
                                $end_date = substr($r->getEndDate(), 0, 10);
                                $end_date = ($end_date == '0000-00-00')? "Current" : $end_date;
                                
                                if($r->getEndDate() != "0000-00-00 00:00:00"){
                                    $position = $hqp->getUniversityDuring($r->getEndDate(), $r->getEndDate());
                                }
                                else{
                                    $position = $hqp->getUniversity();
                                }
                                $position = $position['position'];

                                $this->html .= 
                                "<tr><td>$start_date</td><td>$end_date</td><td>$position</td>
                                <td><a href='{$supervisor->getUrl()}'>{$supervisor->getLastName()}</a></td>
                                <td><a href='{$supervisor->getUrl()}'>{$supervisor->getFirstName()}</a></td></tr></tbody>";
                            }
                        }
                    }
                    
                    $this->html .= "</table><script type='text/javascript'>$('#relations_table').dataTable({autoWidth: false});</script>";
                }
            }
        }
        if($wgUser->isLoggedIn()){
            if($this->html == ""){
                if($visibility['isMe'] && ($this->person->isRole(HQP) || $this->person->isRole(HQP.'-Candidate'))){
                    $this->html .= "Contact your supervisor in order be added as their student";
                }
                else if($visibility['isSupervisor']){
                    $this->html .= "<a class='button' href='$wgServer$wgScriptPath/index.php/Special:ManagePeople'>Manage HQP</a>";
                }
                else{
                    $this->html .= "This user has no relations";
                }
            }
        }
    }
}
?>
