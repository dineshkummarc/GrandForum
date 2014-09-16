<?php

class ProjectGoalsReportItem extends AbstractReportItem {
    
    function render(){
        global $wgOut;
        $project = Project::newFromId($this->projectId);
        $year = $this->getAttr("year", REPORTING_YEAR);
        $max = $this->getAttr("max", 5);
        $milestones = $project->getGoalsDuring($year);
        $width = (isset($this->attributes['width'])) ? $this->attributes['width'] : "150px";
        $item = "<ol id='{$this->getPostId()}'></ol><a class='button' onClick=\"addMilestone{$this->getPostId()}('new', '', '', 'Current', '')\">Add Goal</a>";
        $item = $this->processCData($item);
        $wgOut->addHTML($item);
        $wgOut->addHTML(<<<EOF
<script type='text/javascript'>
    function addMilestone{$this->getPostId()}(id, title, description, status, assessment){
        var template = $("{$this->getTemplate()}");
        $("#milestone_id", template).val(id);
        $("#identifier", template).val(new Date().getTime());
        $("#title", template).val(title);
        $("#new_title", template).val(title);
        if(status == 'Current' ||
           status == 'Closed' ||
           status == 'Abandoned'){
            $("#status", template).val(status);
        }
        $("#description", template).val(description);
        $("#assessment", template).val(assessment);
        $("#{$this->getPostId()}").append(template);
    }
</script>
EOF
        );
        if(count($milestones) > 0){
            foreach($milestones as $milestone){
                $wgOut->addHTML("<script type='text/javascript'>
                    var milestone = ".json_encode(array('id' => $milestone->getMilestoneId(),
                                                        'title' => $milestone->getTitle(),
                                                        'description' => $milestone->getDescription(),
                                                        'status' => $milestone->getStatus(),
                                                        'assessment' => $milestone->getAssessment())).";
                    addMilestone{$this->getPostId()}(milestone.id, milestone.title, milestone.description, milestone.status, milestone.assessment);
                </script>");
            }
        }
    }
    
    function renderForPDF(){
        global $wgOut;
        $project = Project::newFromId($this->projectId);
        $year = $this->getAttr("year", REPORTING_YEAR);
        $max = $this->getAttr("max", 5);
        $milestones = $project->getGoalsDuring($year);
        $width = (isset($this->attributes['width'])) ? $this->attributes['width'] : "150px";
        $item = "";
        if(count($milestones) > 0){
            foreach($milestones as $milestone){
                $item .= $this->getTemplateForPDF($milestone->getTitle(),
                                                  $milestone->getStatus(),
                                                  $milestone->getDescription(),
                                                  $milestone->getAssessment());
            }
        }
        $item = $this->processCData($item);
        $wgOut->addHTML($item);
    }
    
    function getTemplate(){
        $tplt = "<li style='font-weight: bold; font-size: 2.0em;margin-bottom: 25px;'>
                    <input id='milestone_id' type='hidden' name='{$this->getPostId()}_milestone_id[]' />
                    <input id='title' type='hidden' name='{$this->getPostId()}_title[]' style='width:97%;' />
                    <input id='identifier' type='hidden' name='{$this->getPostId()}_identifier[]' />
                    <table width='100%' style='font-size:9pt;'>
                        <tr>
                            <td width='1%'><b>Title:</b></td><td><input id='new_title' type='text' name='{$this->getPostId()}_new_title[]' style='width:97%;' /></td>
                            <td width='50%'><b>Status:</b>&nbsp;<select id='status' style='vertical-align: middle;' name='{$this->getPostId()}_status[]'>
                                    <option selected>Current</option>
                                    <option>Closed</option>
                                    <option>Abandoned</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width='50%' colspan='2'><b>How it will be achieved:</b></td>
                            <td><b>Assessment:</b></td>
                        </tr>
                        <tr>
                            <td width='50%' colspan='2'><textarea id='description' name='{$this->getPostId()}_description[]' style='height: 80px;resize: none;'></textarea></td>
                            <td><textarea id='assessment' name='{$this->getPostId()}_assessment[]' style='height: 80px;resize: none;'></textarea></td>
                        </tr>
                    </table>
                    <hr />
            </li>";
        return trim(str_replace("\n", "", $tplt));
    }
    
    function getTemplateForPDF($title, $status, $description, $assessment){
        $tplt = "<div style='page-break-inside:avoid;'>
                    <h4>$title ({$status})</h4>
                    <div style='margin-left:50px;margin-bottom:25px;'>
                        <b>How it will be achieved:</b><br />
                        {$description}<br />
                        <b>Assessment:</b><br />
                        {$assessment}<br />
                    </div>
                 </div>";
        return $tplt;
    }
    
    // Overriden Functions
    function save(){
        $me = Person::newFromWgUser();
        $project = Project::newFromId($this->projectId);
        if(isset($_POST["{$this->getPostId()}_milestone_id"]) && count($_POST["{$this->getPostId()}_milestone_id"]) > 0){
            $milestone_ids = $_POST["{$this->getPostId()}_milestone_id"];
            $identifiers = $_POST["{$this->getPostId()}_identifier"];
            $titles = $_POST["{$this->getPostId()}_title"];
            $new_titles = $_POST["{$this->getPostId()}_new_title"];
            $descriptions = $_POST["{$this->getPostId()}_description"];
            $statuses = $_POST["{$this->getPostId()}_status"];
            $assessments = $_POST["{$this->getPostId()}_assessment"];
            foreach($milestone_ids as $key => $id){
                $_POST['title'] = $titles[$key];
                if($id == 'new'){
                    $_POST['identifier'] = $identifiers[$key];
                    $_POST['title'] = $new_titles[$key];
                }
                else{
                    $_POST['id'] = $id;
                }
                $_POST['new_title'] = $new_titles[$key];
                $_POST['description'] = $descriptions[$key];
                $_POST['status'] = $statuses[$key];
                $_POST['assessment'] = $assessments[$key];
                $_POST['project'] = $project->getName();
                $_POST['user_name'] = $me->getName();
                $_POST['comment'] = "";
                $_POST['end_date'] = $this->getAttr("year", REPORTING_YEAR)."-12";
                
                $api = new ProjectMilestoneAPI(($id != "new"));
                $api->doAction(true);
                unset($_POST['identifier']);
                unset($_POST['id']);
            }
        }
        return array();
    }
    
    function getNFields(){
        return 0;
    }
        
}

?>
