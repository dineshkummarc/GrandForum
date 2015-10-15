<?php

class PersonGradStudentsTab extends AbstractTab {

    var $person;
    var $visibility;

    function PersonGradStudentsTab($person, $visibility){
        parent::AbstractTab("Students");
        $this->person = $person;
        $this->visibility = $visibility;
    }

    function generateBody(){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $config;

		$wgOut->addScript(
                "<script type='text/javascript'>
                $(document).ready(function(){
                    $('#supervisorAccordion').accordion({autoHeight: false, collapsible: true});
                    $('.ui-accordion .ui-accordion-header a.accordion_hdr_lnk').click(function() {
                      window.location = $(this).attr('href');
                      return false;
                   });
                });


                </script>"
            );
            $wgOut->addHTML(
                "<style type='text/css'>
                    .ui-accordion .ui-accordion-header a{
                        display: inline !important;
                    }
                    .ui-accordion .ui-accordion-header a.accordion_hdr_lnk{
                        color: blue !important;
                        padding-left: 0 !important;
                    }
                    .ui-accordion .ui-accordion-header a.accordion_hdr_lnk:hover{
                        text-decoration: underline;
                    }
                </style>"
            );

	$this->html = "
		<div id='supervisorAccordion'>
		     <h3><a href='#'>Supervision</a></h3>
		     <div>
		     {$this->showSupervisorRelations($this->person, $this->visibility)}
		     </div>
		     <h3><a href='#'>Examining Commitee Membership</a></h3>
		     <div>
                     {$this->showCommiteeRelations($this->person, $this->visibility)}		     
		     </div>
		</div>";
		     
        return $this->html;
    }
    
    /**
     * Displays all of the user's relations
     */
    function showSupervisorRelations($person, $visibility){
        global $wgUser, $wgOut, $wgScriptPath, $wgServer;
        $html = "";
	if($wgUser->isLoggedIn() && ($visibility['edit'] || (!$visibility['edit'] && (count($person->getRelations('public')) > 0 || count($person->getSupervisors(true)) > 0 || ($visibility['isMe'] && count($person->getRelations()) > 0))))){
            if($person->isRoleAtLeast(HQP) || ($person->isRole(INACTIVE) && $person->wasLastRoleAtLeast(HQP))){
                    $html .= "<table width='100%'><tr>";
                    if(count($person->getRelationsAll()>0)){
                        $html .= "<td style='width:100%;' valign='top'>";
                        $html .= "<table id='relations_table' class='wikitable sortable' width='100%' cellspacing='1' cellpadding='2' rules='all' frame='box'>
                                    <thead><tr>
					        <th>Name</th>
					        <th>Position</th>
					        <th style='white-space: nowrap;'>Start Date</th>
						<th style='white-space: nowrap;'>End Date</th>
						<th style='white-space: nowrap;'>Research Area</th>
						<th>Completion Milestones</th>
						<th>Co-Supervisors & Committees</th>
						<th>Role</th>
				    </tr></thead><tbody>";
                        $relations = $person->getRelationsAll();
                        $students = array();
			foreach($relations as $r){
                            $hqp =  $r->getUser2();
			    if(in_array($hqp->getNameForForms(), $students)){
				continue;
 			    }
		            else{
				$students[] = $hqp->getNameForForms();
			    }
                            $start_date = substr($r->getStartDate(), 0, 10);
                            $end_date = substr($r->getEndDate(), 0, 10);
                            $end_date = ($end_date == '0000-00-00')? "Current" : $end_date;
                            $position = $hqp->getUniversity();
			    $research_area = $position['research_area'];
                            $position = $position['position'];
			    $role = $r->getType();
			    if($role == "Supervises"){
				$role= "Supervisor";
			    }
			    else{
				continue;
			    }
			    $names = array();
                            $rel = array_merge($hqp->getSupervisors(), $hqp->getCommittee());
			    foreach($rel as $rels){
				if(count($rel) == 1){
				   break;
				}
				$names[] = "<a href='{$rels->getUrl()}'>{$rels->getNameForForms()}</a>";
			    } 
                            $html .= 
                            "<tr>
				<td style='white-space: nowrap;'><a href='{$hqp->getUrl()}'>{$hqp->getNameForForms()}</a></td>
                             	<td>$position</td>
			        <td style='white-space: nowrap;'>$start_date</td>
				<td style='white-space: nowrap;'>$end_date</td>
				<td>$research_area</td>
				<td></td><td>".implode(", ",$names)."</td>
				<td style='white-space: nowrap;'>$role</td>";
                            $html .= "</tr>";
                        }
                        $html .= "</tbody></table><script type='text/javascript'>$('#relations_table').dataTable()</script>";
                    	$html .= "</td></tr></table>";
		    }
            }

        }
        if($wgUser->isLoggedIn()){
            if($html == ""){
                if($visibility['isMe'] || $visibility['isSupervisor']){
                    $html .= "<input type='button' onClick='window.open(\"$wgServer$wgScriptPath/index.php/Special:EditRelations\");' value='Edit Relations' />";
                }
                else{
                    $html .= "This user has no relations";
                }
            }
      }
	return $html;
    }


    /**
     * Displays all of the user's relations
     */
    function showCommiteeRelations($person, $visibility){
        global $wgUser, $wgOut, $wgScriptPath, $wgServer;
        $html = "";
        if($wgUser->isLoggedIn() && ($visibility['edit'] || (!$visibility['edit'] && (count($person->getRelations('public')) > 0 || count($person->getSupervisors(true)) > 0 || ($visibility['isMe'] && count($person->getRelations()) > 0))))){
            if($person->isRoleAtLeast(HQP) || ($person->isRole(INACTIVE) && $person->wasLastRoleAtLeast(HQP))){
                    $html .= "<table width='100%'><tr>";
                    if(count($person->getRelationsAll()>0)){
                        $html .= "<td style='width:100%;' valign='top'>";
                        $html .= "<table id='relations_table2' class='wikitable sortable' width='100%' cellspacing='1' cellpadding='2' rules='all' frame='box'>
                                    <thead><tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th style='white-space: nowrap;'>Start Date</th>
                                                <th style='white-space: nowrap;'>End Date</th>
                                                <th style='white-space: nowrap;'>Research Area</th>
                                                <th>Completion Milestones</th>
                                                <th>Co-Supervisors & Committees</th>
                                                <th>Role</th>
                                    </tr></thead><tbody>";
                        $relations = $person->getRelationsAll();
                        $students = array();
			foreach($relations as $r){
                            $hqp =  $r->getUser2();
			    if(in_array($hqp->getNameForForms(), $students)){
                                $role = $r->getType();
				continue;
                            }
                            else{
                                $students[] = $hqp->getNameForForms();
                            }

			    $start_date = substr($r->getStartDate(), 0, 10);
                            $end_date = substr($r->getEndDate(), 0, 10);
                            $end_date = ($end_date == '0000-00-00')? "Current" : $end_date;
                            $position = $hqp->getUniversity();
                            $research_area = $position['research_area'];
                            $position = $position['position'];
                            $role = $r->getType();
                            if($role == "Supervises"){
                            	continue;
			    }
                            $names = array();
                            $rel = array_merge($hqp->getSupervisors(), $hqp->getCommittee());
                            foreach($rel as $rels){
                                if(count($rel) == 1){
                                   break;
                                }
                                $names[] = "<a href='{$rels->getUrl()}'>{$rels->getNameForForms()}</a>";
                            }
                            $html .=
                            "<tr>
                                <td style='white-space: nowrap;'><a href='{$hqp->getUrl()}'>{$hqp->getNameForForms()}</a></td>
                                <td>$position</td>
                                <td style='white-space: nowrap;'>$start_date</td>
                                <td style='white-space: nowrap;'>$end_date</td>
                                <td>$research_area</td>
                                <td></td><td>".implode(", ",$names)."</td>
                                <td style='white-space: nowrap;'>$role</td>";
                            $html .= "</tr>";
                        }
                        $html .= "</tbody></table><script type='text/javascript'>$('#relations_table2').dataTable()</script>";
                        $html .= "</td></tr></table>";
                    }
            }

        }
        if($wgUser->isLoggedIn()){
            if($html == ""){
                if($visibility['isMe'] || $visibility['isSupervisor']){
                    $html .= "<input type='button' onClick='window.open(\"$wgServer$wgScriptPath/index.php/Special:EditRelations\");' value='Edit Relations' />";
                }
                else{
                    $html .= "This user has no relations";
                }
            }
      }
        return $html;
    }



}
?>
