<?php

class ProjectMilestonesTab extends AbstractEditableTab {
    // This should be converted into a Backbone Page in the future most likely

    var $project;
    var $visibility;
    var $nYears = 3;

    function ProjectMilestonesTab($project, $visibility){
        parent::AbstractTab("Milestones");
        $this->project = $project;
        $this->visibility = $visibility;
        
        if($this->canEdit() && (isset($_GET['edit']) || isset($_POST['edit']))){
            $this->visibility['edit'] = 1;
        }
    }
    
    function handleEdit(){
        global $config;
        $startDate = $this->project->getCreated();
        $startYear = substr($startDate, 0, 4);
        $startMonth = substr($startDate, 5, 2);
        //$startYear = @substr($config->getValue('projectPhaseDates', PROJECT_PHASE), 0, 4);
        $me = Person::newFromWgUser();
        
        $_POST['user_name'] = $me->getName();
        $_POST['project'] = $this->project->getName();
        
        foreach($_POST['milestone_activity'] as $activityId => $activity){
            foreach($_POST['milestone_title'][$activityId] as $milestoneId => $title){
                $quarters = array();
                if(isset($_POST['milestone_q'][$activityId][$milestoneId])){
                    foreach($_POST['milestone_q'][$activityId][$milestoneId] as $year => $qs){
                        foreach($qs as $qId => $q){
                            $quarters[] = ($year).":$qId";
                        }
                    }
                }
                
                if(isset($_POST['milestone_leader'])){
                    $_POST['leader'] = $_POST['milestone_leader'][$activityId][$milestoneId];
                }
                $_POST['activity'] = $activity;
                $_POST['activity_id'] = $activityId;
                $_POST['milestone'] = $_POST['milestone_old'][$activityId][$milestoneId];
                $_POST['title'] = $_POST['milestone_old'][$activityId][$milestoneId];
                $_POST['new_title'] = $title;
                $_POST['problem'] = "";
                $_POST['description'] = "";
                $_POST['assessment'] = "";
                $_POST['status'] = $_POST['milestone_status'][$activityId][$milestoneId];
                $_POST['people'] = $_POST['milestone_people'][$activityId][$milestoneId];
                $_POST['end_date'] = ($startYear+2)."-12-31 00:00:00";
                $_POST['quarters'] = implode(",", $quarters);
                $_POST['comment'] = str_replace(">", "&gt;", str_replace("<", "&lt;", $_POST['milestone_comment'][$activityId][$milestoneId]));
                $_POST['id'] = $milestoneId;
                
                $milestoneApi = new ProjectMilestoneAPI(true);
                $milestoneApi->doAction(true);
                
                if(isset($_POST['milestone_delete'][$activityId][$milestoneId]) &&
                   $_POST['milestone_delete'][$activityId][$milestoneId] == 'delete'){
                    $milestone = Milestone::newFromId($milestoneId);
                    if($this->canEditMilestone($milestone)){
                        DBFunctions::update('grand_milestones',
                                            array('status' => 'Deleted'),
                                            array('id' => $milestone->getId()));
                    }
                }
            }
        }
        
        if(isset($_POST['new_activity_title']) && $_POST['new_activity_title'] != "" && $this->canEditMilestone(null)){
            DBFunctions::insert('grand_activities',
                                array('name' => $_POST['new_activity_title'],
                                      'project_id' => $this->project->getId()));
            
            // Still show the edit interface
            redirect("{$this->project->getUrl()}?tab=milestones&edit");
        }
        if(isset($_POST['new_milestone_activity']) && isset($_POST['new_milestone_title']) &&
           $_POST['new_milestone_activity'] != "" && $_POST['new_milestone_title'] != "" && 
           $this->canEditMilestone(null)){
            $activity = Activity::newFromId($_POST['new_milestone_activity']);
            $_POST['leader'] = "";
            $_POST['activity'] = $activity->getName();
            $_POST['activity_id'] = $_POST['new_milestone_activity'];
            $_POST['milestone'] = "";
            $_POST['title'] = $_POST['new_milestone_title'];
            $_POST['new_title'] = $_POST['new_milestone_title'];
            $_POST['problem'] = "";
            $_POST['description'] = "";
            $_POST['assessment'] = "";
            $_POST['status'] = "New";
            $_POST['people'] = "";
            $_POST['end_date'] = ($startYear+2)."-12-31 00:00:00";
            $_POST['quarters'] = "";
            $_POST['comment'] = "";
            $_POST['id'] = "";
            unset($_POST['id']);
            unset($_POST['activity_id']);
            
            $milestoneApi = new ProjectMilestoneAPI(false);
            $milestoneApi->doAction(true);
            
            // Still show the edit interface 
            redirect("{$this->project->getUrl()}?tab=milestones&edit");
        }
        Messages::addSuccess("'Milestones' updated successfully.");
        redirect("{$this->project->getUrl()}?tab=milestones");
    }
    
    function canEdit(){
        $me = Person::newFromWgUser();
        if($this->project->isFeatureFrozen(FREEZE_MILESTONES)){
            return false;
        }
        if($me->isLoggedIn()){
            $milestones = $this->project->getMilestones(true);
            foreach($milestones as $milestone){
                if($milestone->getLeader()->getId() == $me->getId()){
                    return true;
                }
            }
            return $this->project->userCanEdit();
        }
        return false;
    }
    
    function canEditMilestone($milestone=null){
        $me = Person::newFromWgUser();
        if($milestone != null && $milestone->getLeader()->getId() == $me->getId()){
            return true;
        }
        return $this->project->userCanEdit();
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
    
    function showYearsHeader(){
        $html = "";
        for($y=1; $y <= $this->nYears; $y++){
            $html .= "<th colspan='4' class='left_border'>Year {$y}</th>";
        }
        return $html;
    }
    
    function showQuartersHeader(){
        $html = "";
        for($y=1; $y <= $this->nYears; $y++){
            $html .= "<th class='left_border'>Q1</th>
                        <th>Q2</th>
                        <th>Q3</th>
                        <th>Q4</th>";
        }
        return $html;
    }
    
    function showQuartersCells($milestone, $activityId){
        $startDate = $this->project->getCreated();
        $startYear = substr($startDate, 0, 4);
        $quarters = $milestone->getQuarters();
        for($y=$startYear; $y < $startYear+$this->nYears; $y++){
            for($q=1;$q<=4;$q++){
                $class = ($q == 1) ? "class='left_border'" : "";
                $color = @Milestone::$statuses[$milestone->getStatus()];

                $assessment = str_replace("'", "&#39;", $milestone->getAssessment());
                $checkbox = "";
                if($this->visibility['edit'] == 1 && $this->canEditMilestone($milestone)){
                    $checked = "";
                    if(isset($quarters[$y][$q])){
                        $checked = "checked='checked'";
                    }
                    $checkbox = "<input class='milestone' type='checkbox' name='milestone_q[$activityId][{$milestone->getMilestoneId()}][$y][$q]' $checked />";
                }
                if(isset($quarters[$y][$q])){
                    $this->html .= "<td style='background:$color;text-align:center;' title='{$assessment}' $class>$checkbox</td>";
                }
                else{
                    $this->html .= "<td style='text-align:center;' $class>$checkbox</td>";
                }
            }
        }
    }
    
    function showMilestones($pdf=false, $year=false){
        global $wgServer, $wgScriptPath, $wgUser, $wgOut, $config;
        $me = Person::newFromWgUser();
        $project = $this->project;
        $startDate = $this->project->getCreated();
        $startYear = substr($startDate, 0, 4);
        $startMonth = substr($startDate, 5, 2);
        //$startYear = @substr($config->getValue('projectPhaseDates', $project->getPhase()), 0, 4);
        
        $activities = array();
        $activityNames = array();
        if($year === false){
            $milestones = $project->getMilestones(true);
        }
        else{
            $milestones = $project->getMilestonesCreated(substr($year, 0, 4).'-03-31');
        }
        
        foreach($project->getActivities() as $activity){
            $activities[$activity->getId()] = array();
            $activityNames[$activity->getId()] = $activity->getName();
        }
        
        foreach($milestones as $milestone){
            if($year !== false){
                $milestone = $milestone->getRevisionByDate($year);
            }
            if($milestone == null){
                continue;
            }
            $activities[$milestone->getActivity()->getId()][] = $milestone;
            $activityNames[$milestone->getActivity()->getId()] = $milestone->getActivity()->getName();
        }
        
        $this->html .= "<style type='text/css' rel='stylesheet'>
            .left_border {
                border-left: 2px solid #555555;
            }
            
            .top_border {
                border-top: 2px solid #555555;
            }
            
            #milestones_table input[type=text], #milestones_table select {
                box-sizing: border-box;
                margin: 0;
                width: 100%;
                height: 24px;
            }
        </style>";
        $commentsHeader = "";
        $statusHeader = "";
        $statusColspan = 2;
        if($this->visibility['edit'] == 1){
            $activityNames = array();
            foreach($project->getActivities() as $activity){
                $activityNames[$activity->getId()] = $activity->getName();
            }
            $activityBox = new SelectBox("new_milestone_activity", "new_milestone_activity", "", $activityNames);
            $activityBox->forceKey= true;
            $activityText = $activityBox->render();
            if($this->canEditMilestone(null)){
                $this->html .= "<div title='Add Activity' id='addActivityDialog' style='display:none;'>
                                    <table>
                                        <tr>
                                            <td align='right'><b>Title:</b></td>
                                            <td><input type='text' name='new_activity_title' /></td>
                                        </tr>
                                    </table>
                                </div>
                                <div title='Add Milestone' id='addMilestoneDialog' style='display:none;'>
                                    <table>
                                        <tr>
                                            <td align='right'><b>Activity:</b></td>
                                            <td>{$activityText}</td>
                                        </tr>
                                        <tr>
                                            <td align='right'><b>Title:</b></td>
                                            <td><input type='text' name='new_milestone_title' /></td>
                                        </tr>
                                    </table>
                                </div>
                                <a class='button' id='addActivity'>Add Activity</a>&nbsp;
                                <a class='button' id='addMilestone'>Add Milestone</a><br /><br />";
            
                $statusHeader = "<th>Status</th>";
                if($me->isRoleAtLeast(STAFF)){
                    $statusHeader .= "<th width='1%'>Delete?</td>";
                }
            }
            else{
                $statusHeader = "<th>Status</th>";
            }
            $statusColspan++;
            if(!$this->canEditMilestone(null)){
                $this->html .= "<p class='milestone_info1'>If there any new milestones or activities, please contact the project leader.  If there are any changes to the milestones, leave comments by clicking the <img src='$wgServer$wgScriptPath/skins/icons/gray_light/comment_stroke_16x14.png' /> icon.</p>";
            }
            else {
                $this->html .= "<p class='milestone_info2'>If a milestone was mistakenly added, then contact someone on staff to delete it.  If a milestone was planned, but was abandoned, then select the 'Abandoned' status.</p>";
            }
            if($me->isRoleAtLeast(STAFF)){
                $statusColspan++;
            }
        }
        if(!$pdf){
            $commentsHeader = "<th></th>";
        }
        else{
            $commentsHeader = "<th>Comments</th>";
        }
        $statusColspan++;
        
        $header = " <tr>
                        <th colspan='1'></th>
                        {$this->showYearsHeader()}
                        <th colspan='{$statusColspan}' class='left_border'></th>
                    </tr>
                    <tr>
                        <th class='milestone_header'>Milestone</th>
                        {$this->showQuartersHeader()}
                        <th class='left_border'>Leader</th>
                        <th>Personnel</th>
                        {$commentsHeader}
                        {$statusHeader}
                    </tr>";
        
        $this->html .= "<p>
                            <span class='milestones_note'><b>Please Note:</b> Year 1, Quarter 1 starts on {$startYear}/{$startMonth}.<br /></span>
                            <span class='new_milestones_message'>New Milestones have titles in bold.</span>
                        </p>
                        <table id='milestones_table' frame='box' rules='all' cellpadding='2' class='smallest dashboard milestones' style='width:100%; border: 2px solid #555555;'>
                        <thead>{$header}</thead>
                        <tbody>";

        foreach($activities as $activityId => $milestones){
            if($year !== false && count($milestones) == 0){
                continue;
            }
            $count = max(1, count($milestones));
            $activity = $activityNames[$activityId];
            if($this->visibility['edit'] == 1 && $this->canEditMilestone(null)){
                $activityTitle = str_replace("'", "&#39;", $activity);
                $activity = "<input type='text' name='milestone_activity[$activityId]' style='font-weight:bold;' value='$activityTitle' />";
            }
            else if($this->visibility['edit'] == 1){
                $activityTitle = str_replace("'", "&#39;", $activity);
                $activity = "<input type='hidden' name='milestone_activity[$activityId]' value='$activityTitle' /><b>$activity</b>";
            }
            $this->html .= "<tr class='top_border' data-id='$activityId'>
                                <td style='background:#555555;color:white;font-weight:bold;' colspan='".($statusColspan+1+($this->nYears*4))."'>$activity</td>
                            </tr>";
            $this->html .= str_replace("<tr", "<tr data-activity='{$activityId}' style='display:none;'", str_replace("<th", "<th style='background:#CCCCCC;color:black;font-weight:bold;'", $header));
            if(count($milestones) == 0){
                $this->html .= "<tr><td colspan='".($statusColspan+1+($this->nYears*4))."'></td>";
            }
            foreach($milestones as $key => $milestone){
                    $this->html .= "<tr>";
                if($this->visibility['edit'] == 1 && $this->canEditMilestone(null)){
                    $milestoneTitle = str_replace("'", "&#39;", $milestone->getTitle());
                    $title = "<input type='hidden' name='milestone_old[$activityId][{$milestone->getMilestoneId()}]' value='{$milestoneTitle}' />
                              <input type='text' name='milestone_title[$activityId][{$milestone->getMilestoneId()}]' value='{$milestoneTitle}' />";
                }
                else if($this->visibility['edit'] == 1 && $this->canEditMilestone($milestone)){
                    $milestoneTitle = str_replace("'", "&#39;", $milestone->getTitle());
                    $title = "<input type='hidden' name='milestone_old[$activityId][{$milestone->getMilestoneId()}]' value='{$milestoneTitle}' />
                              <input type='hidden' name='milestone_title[$activityId][{$milestone->getMilestoneId()}]' value='{$milestoneTitle}' />";
                    if($milestone->isNew()){
                        $title .= "<b>$milestoneTitle</b>";
                    }
                    else{
                        $title .= $milestoneTitle;
                    }
                }
                else{
                    $title = $milestone->getTitle();
                    if($milestone->isNew()){
                        $title = "<b>$title</b>";
                    }
                }
                $height = "";
                if($pdf){
                    $height = "height:".(DPI_CONSTANT*10)."px;";
                }
                $this->html .= "<td style='$height'>{$title}</td>";
                $this->showQuartersCells($milestone, $activityId);
                
                $comment = str_replace("'", "&#39;", $milestone->getComment());
                $doubleEscapeComment = nl2br(str_replace("&", "&amp;", $comment));
                $commentIcon = ($comment != "" || $this->visibility['edit'] == 1) ? "<img src='$wgServer$wgScriptPath/skins/icons/gray_light/comment_stroke_16x14.png' title='{$doubleEscapeComment}' />" : "";
                $leader = $milestone->getLeader();
                $peopleText = $milestone->getPeopleText();
                $leaderText = ($leader->getName() != "") ? "<a href='{$leader->getUrl()}'>{$leader->getNameForForms()}</a>" : "";
                
                if($this->visibility['edit'] == 1 && $this->canEditMilestone($milestone)){
                    $members = $project->getAllPeople();
                    $peopleNames = array();
                    foreach($members as $person){
                        $peopleNames[$person->getNameForForms()] = $person->getNameForForms();
                    }
                    if($this->canEditMilestone(null)){
                        $selectBox = new SelectBox("milestone_leader[$activityId][{$milestone->getMilestoneId()}]", "leader", $leader->getNameForForms(), $peopleNames);
                        $leaderText = $selectBox->render();
                    }
                    else{
                        $leaderText = "<input type='hidden' name='milestone_leader[$activityId][{$milestone->getMilestoneId()}]' value='{$leader->getNameForForms()}' />$leaderText";
                    }
                    $commentIcon = "<div style='cursor:pointer;' class='comment'>{$commentIcon}</div><div title='Edit Comment' class='comment_dialog' style='display:none;'><textarea style='width:400px;height:150px;' name='milestone_comment[$activityId][{$milestone->getMilestoneId()}]'>{$comment}</textarea></div>";
                    $personnel = str_replace("'", "&#39;", $milestone->getPeopleText());
                    $peopleText = "<input type='text' class='milestone_people' name='milestone_people[$activityId][{$milestone->getMilestoneId()}]' value='{$personnel}' />";
                }
                $this->html .= "<td class='left_border' align='center'>{$leaderText}</td>";
                $this->html .= "<td class='left_comment' align='center'>{$peopleText}</td>";
                if(!$pdf){
                    $this->html .= "<td class='comment' align='center'>{$commentIcon}</td>";
                }
                else{
                    $this->html .= "<td class='comment'>".nl2br($comment)."</td>";
                }
                if($this->visibility['edit'] == 1 && $this->canEditMilestone($milestone)){
                    $statuses = array();
                    foreach(Milestone::$statuses as $status => $color){
                        $statuses[$status] = $status;
                    }
                    
                    $selectBox = new SelectBox("milestone_status[$activityId][{$milestone->getMilestoneId()}]", "status", $milestone->getStatus(), $statuses);
                    $statusText = $selectBox->render();
                    $this->html .= "<td id='status' class='left_comment' align='center'>$statusText</td>";
                    if($me->isRoleAtLeast(STAFF)){
                        $this->html .= "<td align='center'><input type='checkbox' name='milestone_delete[$activityId][{$milestone->getMilestoneId()}]' value='delete' /></td>";
                    }
                }
                else if($this->visibility['edit'] && !$this->canEditMilestone($milestone)){
                    $this->html .= "<td id='status' class='left_comment' align='center'></td>";
                }
                $this->html .= "</tr>";
            }
        }
        $this->html .= "</tbody>
                        </table>";
        if(!$pdf){
            $this->html .= "<table style='float:right;'>";
        }
        else{
            $this->html .= "<table>";
        }
        $this->html .= "<tr>
                            <th>Legend</th>
                        </tr>";
        foreach(Milestone::$statuses as $status => $color){
            $this->html .= "<tr>
                                <td class='smallest'><div style='text-align:center;padding:1px 3px;background:{$color};border:1px solid #555555;white-space:nowrap;'>$status</div></td>
                            </tr>";
        }
        $this->html .= "</table>";
        $hqp = new Collection(array_values($this->project->getAllPeople(HQP)));
        if(!$pdf){
            $this->html .= "<script type='text/javascript'>
                var colors = ".json_encode(Milestone::$statuses).";
                
                setInterval(function(){
                    $('table.milestones thead').hide();
                    $('table.milestones').css('overflow-anchor', 'none');
                    $('table.milestones').each(function(i, table){
                        var found = false;
                        var toHide = [];
                        var toShow = [];
                        $('tr.top_border', table).each(function(i, el){
                            var activityId = $(el).attr('data-id');
                            if(!found && (($(el).offset().top - window.scrollY) > - $(el).height() || $('tr.top_border', table).length-1 == i)){
                                toShow.push($('tr[data-activity=' + activityId + ']', table));
                                found = true;
                            }
                            else{
                                toHide.push($('tr[data-activity=' + activityId + ']', table));
                            }
                        });
                        $(toShow).each(function(){ $(this).show(); });
                        $(toHide).each(function(){ $(this).hide(); });
                    });
                }, 50);
                
                $('#milestones_table td').qtip();
                $('#milestones_table td.comment img').qtip({
                    position: {
                        my: 'topRight',
                        at: 'bottomLeft'
                    }
                });
                
                $('#milestones_table div.comment').click(function(){
                    var that = $(this);
                    $('.comment_dialog', $(this).parent()).dialog({
                        width: 'auto',
                        resizable: false,
                        buttons: {
                            'Done': function(){
                                $(this).dialog('close');
                            }
                        },
                        close: function(event, ui){
                            $(this).parent().prependTo(that.parent());
                        }
                    });
                });
                
                var changeColor = function(){
                    var checked = $(this).is(':checked');
                    if(checked){
                        var status = $('td#status select', $(this).parent().parent()).val();
                        var color = colors[status];
                        $(this).parent().css('background', color);
                    }
                    else{
                        $(this).parent().css('background', '#FFFFFF');
                    }
                };
                
                $('#milestones_table td input.milestone[type=checkbox]').change(changeColor);
                $('#milestones_table td input.milestone[type=checkbox]').each(changeColor);
                $('#milestones_table td#status select').change(function(){
                    var status = $(this).val();
                    var color = colors[status];
                    $('input.milestone:checked', $(this).parent().parent()).parent().css('background', color);
                });
                
                $('#addActivity').click(function(){
                    $('#addActivityDialog').dialog({
                        width: 'auto',
                        resizable: false,
                        buttons: {
                            'Add Activity': function(){
                                $(this).parent().prependTo($('#milestones'));
                                $(this).dialog('close');
                                $('input[value=\"Save Milestones\"]').click();
                            },
                            Cancel: function(){
                                $(this).dialog('close');
                            }
                        }
                    });
                });
                
                $('#addMilestone').click(function(){
                    $('#addMilestoneDialog').dialog({
                        width: 'auto',
                        resizable: false,
                        buttons: {
                            'Add Milestone': function(){
                                $('#addFESMilestoneDialog').remove();
                                $(this).parent().prependTo($('#milestones'));
                                $(this).dialog('close');
                                $('input[value=\"Save Milestones\"]').click();
                            },
                            Cancel: function(){
                                $(this).dialog('close');
                            }
                        }
                    });
                });
                
                $(document).ready(function(){
                    var hqp = ".$hqp->toJSON().";
                    $('.milestone_people').tagit({
                        allowSpaces: true,
                        removeConfirmation: true,
                        availableTags: _.pluck(hqp, 'fullName'),
                        tagSource: function(search, showChoices) {
                            var filter = search.term.toLowerCase();
                            var choices = $.grep(this.options.availableTags, function(element) {
                               // Only match autocomplete options that begin with the search term.
                               // (Case insensitive.)
                               //return (element.toLowerCase().indexOf(filter) === 0);
                               return (element.toLowerCase().match(filter) !== null);
                            });
                            showChoices(this._subtractArray(choices, this.assignedTags()));
                        }
                    });
                });
                
            </script>";
            if($config->getValue('networkName') == "GlycoNet" && 
               $this->canEditMilestone() && 
               !$this->visibility['edit']){
                $this->showMilestoneReport();
            }
        }
    }

    function showMilestoneReport(){
        global $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        $this->html .= "<p><a class='button' href='$wgServer$wgScriptPath/index.php/Special:Report?report=ProjectMilestonesReport&project={$this->project->getName()}'>Edit Milestone Report</a>";
        $report = new DummyReport("ProjectMilestonesReport", $me, $this->project);
        $pdfData = $report->getLatestPDF();
        if(count($pdfData) > 0){
            $pdf = PDF::newFromToken($pdfData[0]['token']);
            $this->html .= "&nbsp;<a class='button' href='{$pdf->getUrl()}'>Download Report PDF</a>";
        }
        $this->html .= "</p>";
    }

}    
    
?>
