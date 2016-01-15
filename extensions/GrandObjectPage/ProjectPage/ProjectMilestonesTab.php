<?php

class ProjectMilestonesTab extends AbstractEditableTab {

    var $project;
    var $visibility;

    function ProjectMilestonesTab($project, $visibility){
        parent::AbstractTab("Milestones");
        $this->project = $project;
        $this->visibility = $visibility;
    }
    
    function handleEdit(){
        
    }
    
    function canEdit(){
        return false;
    }
    
    function generateBody(){
        global $wgUser, $wgOut, $wgServer, $wgScriptPath;
        if($wgUser->isLoggedIn()){
            $project = $this->project;
            $me = Person::newFromId($wgUser->getId());
            if($me->isMemberOf($project) || $me->isRoleAtLeast(STAFF)){
                $this->showMilestones();
                return $this->html;
            }
        }
    }
    
    function generateEditBody(){
        global $wgUser, $wgOut, $wgServer, $wgScriptPath;
        if($wgUser->isLoggedIn()){
            $project = $this->project;
            $this->showMilestones();
            return $this->html;
        }
    }
    
    function showMilestones(){
        global $wgServer, $wgScriptPath, $wgUser, $wgOut, $config;
        $project = $this->project;
        
        $startYear = @substr($config->getValue('projectPhaseDates', $project->getPhase()), 0, 4);
        
        $activities = array();
        $milestones = $project->getMilestones(true);
        if(count($milestones) == 0){
            return;
        }
        foreach($milestones as $milestone){
            $activities[$milestone->getActivity()->getName()][] = $milestone;
        }
        $this->html .= "<style type='text/css' rel='stylesheet'>
            .left_border {
                border-left: 2px solid #555555;
            }
            
            .top_border {
                border-top: 2px solid #555555;
            }
        </style>";
        $this->html .= "<table id='milestones_table' frame='box' rules='all' cellpadding='2' class='smallest dashboard' style='width:100%; border: 2px solid #555555;' >";
        $this->html .= "<thead>
                        <tr>
                            <th colspan='2' width='33%'></th>
                            <th colspan='4' class='left_border'>".($startYear)."</th>
                            <th colspan='4' class='left_border'>".($startYear+1)."</th>
                            <th colspan='4' class='left_border'>".($startYear+2)."</th>
                            <th colspan='3' class='left_border' width='33%'></th>
                        </tr>
                        <tr>
                            <th>Activity</th>
                            <th>Milestone</th>
                            <th class='left_border'>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th class='left_border'>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th class='left_border'>Q1</th>
                            <th>Q2</th>
                            <th>Q3</th>
                            <th>Q4</th>
                            <th class='left_border'>Leader</th>
                            <th>Personnel</th>
                        </tr>
                        </thead>
                        <tbody>";
        foreach($activities as $activity => $milestones){
            $count = count($milestones);
            /*if(isset($this->visibility['edit'])){
                $activity = "<input type='text' name='activity' value='{$activity}' />";
            }*/
            $this->html .= "<tr class='top_border'>
                                <td rowspan='$count'>$activity</td>";
            foreach($milestones as $key => $milestone){
                if($key != 0){
                    $this->html .= "<tr>";
                }
                /*if(isset($this->visibility['edit'])){
                    $title = "<input type='text' name='title[{$milestone->getId()}]' value='{$milestone->getTitle()}' />";
                }
                else{*/
                    $title = $milestone->getTitle();
                //}
                $this->html .= "<td>{$title}</td>";
                $quarters = $milestone->getQuarters();
                for($y=$startYear; $y < $startYear+3; $y++){
                    for($q=1;$q<=4;$q++){
                        $class = ($q == 1) ? "class='left_border'" : "";
                        $color = "#BBBBBB";
                        switch($milestone->getStatus()){
                            case "Abandoned":
                                $color = "#FF8888";
                                break;
                            case "Completed":
                                $color = "#88FF88";
                                break;
                        }
                        $assessment = str_replace("'", "&#39;", $milestone->getAssessment());
                        if(isset($quarters[$y][$q])){
                            $this->html .= "<td style='background:$color;text-align:center;' title='{$assessment}' $class></td>";
                        }
                        else{
                            $this->html .= "<td $class></td>";
                        }
                    }
                }
                $comment = str_replace("'", "&#39;", $milestone->getComment());
                $commentIcon = ($comment != "") ? "<img style='float:right;padding-top:2px;' src='../skins/icons/gray_light/comment_stroke_16x14.png' title='{$comment}' />" : "";
                $leader = $milestone->getLeader();
                $people = $milestone->getPeople();
                //if(!isset($this->visibility['edit'])){
                    $leaderText = ($leader->getName() != "") ? "<a href='{$leader->getUrl()}'>{$leader->getNameForForms()}</a>" : "";
                /*}
                else{
                    $members = $project->getAllPeople();
                    $peopleNames = array();
                    foreach($members as $person){
                        $peopleNames[] = $person->getNameForForms();
                    }
                    $selectBox = new SelectBox("leader[{$milestone->getId()}]", "leader", $leader->getNameForForms(), $peopleNames);
                    $leaderText = $selectBox->render();
                }*/
                $peopleText = array();
                foreach($people as $person){
                    $peopleText[] = "<a href='{$person->getUrl()}'>{$person->getNameForForms()}</a>";
                }
                $peopleText = implode(", ", $peopleText);
                $this->html .= "<td class='left_border'>{$leaderText}</td>";
                $this->html .= "<td class='left_comment'>{$commentIcon}{$peopleText}</td>";
                $this->html .= "</tr>";
            }
        }
        $this->html .= "</tbody>
                        </table>";
        $this->html .= "<script type='text/javascript'>
            $('#milestones_table td').qtip();
            $('#milestones_table td.left_comment img').qtip({
                position: {
                    my: 'topRight',
                    at: 'bottomLeft'
                }
            });
        </script>";
    }

}    
    
?>
