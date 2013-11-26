<?php

class ProjectMainTab extends AbstractEditableTab {

    var $project;
    var $visibility;

    function ProjectMainTab($project, $visibility){
        parent::AbstractTab("Main");
        $this->project = $project;
        $this->visibility = $visibility;
    }
    
    function generateBody(){
        global $wgUser, $wgServer, $wgScriptPath;
        $project = $this->project;
        $me = Person::newFromId($wgUser->getId());
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        
        if($wgUser->isLoggedIn() && $me->isMemberOf($project)){
            $this->html .="<h3><a href='$wgServer$wgScriptPath/index.php/{$project->getName()}:Mail_Index'>{$project->getName()} Mailing List</a></h3>";
        }
        $bigbet = ($this->project->isBigBet()) ? "Yes" : "No";
        $title = "";
        if($edit){
            $fullNameField = new TextField("fullName", "New Title", $this->project->getFullName());
            $title = "<tr><td><b>New Title:</b></td><td>{$fullNameField->render()}</td></tr>";
        }
        $this->html .= "<table>
                            $title
                            <tr><td><b>Type:</b></td><td>{$this->project->getType()}</td></tr>";
        if(!$this->project->isSubProject()){
            $this->html .= "<tr><td><b>Big-Bet:</b></td><td>{$bigbet}</td></tr>";
        }
        $this->html .= "<tr><td><b>Status:</b></td><td>{$this->project->getStatus()}</td></tr>
                        </table>";
        $this->showChallenge();
        $this->showChampions();
        $this->showPeople();
        $this->showDescription();

        if(!$project->isSubProject()){
            $this->showProblem();
            $this->showSolution();
        }
        
        return $this->html;
    }
    
    function handleEdit(){
        global $wgOut;
        $_POST['project'] = $this->project->getName();
        $_POST['fullName'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['fullName']));
        $_POST['description'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['description']));
        $_POST['problem'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['problem']));
        $_POST['solution'] = @str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['solution']));
        if($_POST['description'] != $this->project->getDescription() ||
           $_POST['problem'] != $this->project->getProblem() ||
           $_POST['solution'] != $this->project->getSolution() ||
           $_POST['fullName'] != $this->project->getFullName()){

            APIRequest::doAction('ProjectDescription', true);
            Project::$cache = array();
            $this->project = Project::newFromId($this->project->getId());
            $wgOut->setPageTitle($this->project->getFullName()." (Phase ".$this->project->getPhase().")");
        }

        if(isset($_POST['challenge_id'])){
            APIRequest::doAction('ProjectChallenge', true);
        }
        
        if(isset($_POST['champ_name'])){
            foreach($_POST['champ_name'] as $key => $name){
                $_POST['project'] = $this->project->getName();
                $champ = Person::newFromName($name);
                $_POST['champion_id'] = $champ->getId();
                if(isset($_POST['champ_del'][$key]) && $_POST['champ_del'][$key] == "true"){
                    APIRequest::doAction('DeleteProjectChampions', true);
                }
                else{
                    APIRequest::doAction('ProjectChampions', true);
                }
            }
        }
        
        $form = $this->champForm();
        if($form->validate()){
            foreach($_POST['new_champ_name2'] as $key => $name){
                $_POST['project'] = $this->project->getName();
                $champ = Person::newFromName($name);
                $_POST['champion_id'] = $champ->getId();
                APIRequest::doAction('ProjectChampions', true);
            }
        }
        else{
            return "The champions were not added";
        }
        
        if(isset($_POST['pl'])){
            $leaderName = ($this->project->getLeader() != null) ? $this->project->getLeader()->getName() : "";
            if($_POST['pl'] != $leaderName){
                $_POST['role'] = $this->project->getName();
                $_POST['user'] = $leaderName;
                $_POST['comment'] = "Automatic Removal";
                APIRequest::doAction('DeleteProjectLeader', true);
                
                $_POST['user'] = $_POST['pl'];
                $_POST['manager'] = 'False';
                $_POST['co_lead'] = 'False';
                APIRequest::doAction('AddProjectLeader', true);
            }
        }
    }
    
    function generateEditBody(){
        $this->generateBody();
    }
    
    function canEdit(){
        return $this->visibility['isLead'];
    }
    
    function showThemes(){
        global $wgServer, $wgScriptPath;
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        
        $this->html .= "<h2><span class='mw-headline'>Theme Distribution</span></h2>";
        $themes = $this->project->getThemes();
        $i = 1;
        
        $this->html .= "<table><tr>";
        foreach($themes['values'] as $theme){
            if($i > 1){
                $this->html .= "<td><ul><li></li></ul></td>";
            }
            $this->html .= "<td align='right'>";
            if($edit){
                $this->html .= "{$themes['names'][$i]}";
            }
            else{
                $this->html .= "<a href='{$wgServer}{$wgScriptPath}/index.php/Grand:Theme{$i}_-_".IndexTable::getThemeFullName($i).
                               "'>" . $themes['names'][$i] . "</a>";
            }
            $this->html .= "</td><td>";
            
            if($edit){
                $this->html .= "<input id='t{$i}' onKeyUp='stripAlphaChars(this.id)' type='text' size='2' name='t$i' value='{$themes['values'][$i]}' /> %";
            }
            else{
                $this->html .= "{$themes['values'][$i]}%";
            }
            $this->html .= "</td>";
            
            $i++;
        }
        $this->html .= "</tr></table>";
    }

    function showChallenge(){
        global $wgServer, $wgScriptPath;
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        
        $this->html .= "<h2><span class='mw-headline'>Primary Challenge</span></h2>";
        $challenge = $this->project->getChallenge();
        
        $challenges = DBFunctions::execSQL("SELECT id, name FROM grand_challenges");
        $chlg_opts = "<option value='0'>Not Specified</option>";
        foreach ($challenges as $chlg){
            $cid = $chlg['id'];
            $cname = $chlg['name'];
            $selected = ($cname == $challenge)? "selected='selected'" : "";
            $chlg_opts .= "<option value='{$cid}' {$selected}>{$cname}</option>";
        }
        if($edit){
            $this->html .=<<<EOF
            <select name="challenge_id">{$chlg_opts}</select>
EOF;
        }
        else{
            $this->html .= "<h4>{$challenge}</h4>";
        }   
    }
    
    function champForm(){
        $project = $this->project;
        $champions = $project->getChampions();
    
        $names = array("");
        $people = Person::getAllPeople(CHAMP);
        foreach($people as $person){
            $skip = false;
            foreach($champions as $champ){
                if($champ['user']->getId() == $person->getId()){
                    $skip = true;
                    break;
                }
            }
            if(!$skip){
                $names[$person->getName()] = $person->getNameForForms();
            }
        }
        asort($names);
        $champPlusMinus = new PlusMinus("new_champ_plusminus2");
        $champTable = new FormTable("champ_table2");
        $champTable->append(new ComboBox("new_champ_name2[]", "Name", "", $names, VALIDATE_CHAMPION));
        $champPlusMinus->append($champTable);
        return $champPlusMinus;
    }
    
    function showChampions(){
        global $wgUser, $wgServer, $wgScriptPath;
        
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        $project = $this->project;

        $champions = $project->getChampions();
        $this->html .= "<h2><span class='mw-headline'>Champions</span></h2>";

        if(!$edit){
            if(count($champions) == 0){
                $this->html .= "<strong>N/A</strong>";
            }
            else{
                foreach($champions as $champion){
                    $this->html .= <<<EOF
                    <h3><a href='{$champion['user']->getUrl()}'>{$champion['user']->getNameForForms()}</a></h3>
                    <table cellspacing="0" cellpadding="2" style='margin-left:15px;'>
                        <tr><td><strong>Email:</strong></td><td>{$champion['user']->getEmail()}</td></tr>
                        <tr><td><strong>Title:</strong></td><td>{$champion['title']}</td></tr>
                        <tr><td><strong>Organization:</strong></td><td>{$champion['org']}</td></tr>
                    </table>
EOF;
                }
            }
        }
        else{
            $i = 0;
            foreach($champions as $champion){
                $this->html .= <<<EOF
                    <div id='champ_div_{$champion['user']->getId()}'>
                        <fieldset style='display: inline; min-width: 500px;'>
                            <legend>{$champion['user']->getNameForForms()}</legend>
                            <input type='hidden' name='champ_name[]' value='{$champion['user']->getName()}' />
                            <table cellspacing="0" cellpadding="2" style='margin-left:15px;'>
                                <tr>
                                    <td align='right' valign='top'><b>Delete?</b></td>
                                    <td valign='top'><input type="checkbox" name="champ_del[$i]" value="true" /></td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
EOF;
                $i++;
            }
            
            $form = $this->champForm();
            $this->html .= $form->render();
        }
    }

    function showPeople(){
        global $wgUser, $wgServer, $wgScriptPath;
        
        $me = Person::newFromWgUser();
        
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        $project = $this->project;
        
        $leaders = $project->getLeaders(true); //only get id's
        $coleaders = $project->getCoLeaders(true);
        $pnis = $project->getAllPeople(PNI);
        $cnis = $project->getAllPeople(CNI);
        $ars = $project->getAllPeople(AR);
        $hqps = $project->getAllPeople(HQP);
        
        $names = array("");
        if($project->isSubProject()){
            $people = array_merge($project->getParent()->getAllPeople(), $project->getAllPeople());
            foreach($people as $person){
                if($person->isRoleAtLeast(CNI)){
                    $names[$person->getName()] = $person->getNameForForms();
                }
            }
            if($project->getLeader() != null && !isset($names[$project->getLeader()->getName()])){
                $names[$project->getLeader()->getName()] = $project->getLeader()->getNameForForms();
            }
            
            asort($names);
        }
        
        $this->html .= "<h2><span class='mw-headline'>Leaders</span></h2>";
        $this->html .= "<table>";
        if(!empty($leaders)){
            foreach($leaders as $leader_id){
                $leader = Person::newFromId($leader_id);
                $this->html .= "<tr>";
                $leaderType = "Leader";
                if($leader->managementOf($project->getName())){
                    $leaderType = "Manager";
                }
                
                if(!$edit || !$me->leadershipOf($project->getParent())){
                    $this->html .= "<td align='right'><b>{$leaderType}:</b></td><td><a href='{$leader->getUrl()}'>{$leader->getReversedName()}</a></td></tr>";
                }
                else if($me->leadershipOf($project->getParent())){
                    $plRow = new FormTableRow("pl_row");
                    $plRow->append(new Label("pl_label", "Project Leader", "The leader of this Project.  The person should be a valid person on this project.", VALIDATE_NOTHING));
                    $plRow->append(new ComboBox("pl", "Project Leader", $leader->getName(), $names, VALIDATE_NI));
                    $this->html .= $plRow->render();
                }
            }    
        }
        else if($edit && $me->leadershipOf($project->getParent())){
            $plRow = new FormTableRow("pl_row");
            $plRow->append(new Label("pl_label", "Project Leader", "The leader of this Project.  The person should be a valid person on this project.", VALIDATE_NOTHING));
            $plRow->append(new ComboBox("pl", "Project Leader", "", $names, VALIDATE_NI));
            $this->html .= $plRow->render();
        }
        if(!empty($coleaders)){
            foreach($coleaders as $leader_id){
                $leader = Person::newFromId($leader_id);
                $this->html .= "<tr>";
                $leaderType = "co-Leader";
                if($leader->managementOf($project->getName())){
                    $leaderType = "Manager";
                }
                
                if(!$edit && !$me->leadershipOf($project->getParent())){
                    $this->html .= "<td align='right'><b>{$leaderType}:</b></td><td><a href='{$leader->getUrl()}'>{$leader->getReversedName()}</a></td></tr>";
                }
                else if($me->leadershipOf($project->getParent())){
                    
                }
            }    
        }
        $this->html .= "</table>";
        if(!$edit){
            $this->html .= "<table width='100%'><tr><td valign='top' width='50%'>";
            if($edit || !$edit && count($pnis) > 0){
                $this->html .= "<h2><span class='mw-headline'>PNIs</span></h2>";
            }
            $this->html .= "<ul>";
            foreach($pnis as $pni){
                if((!empty($leaders) && in_array($pni->getId(), $leaders)) || (!empty($coleaders) && in_array($pni->getId(), $coleaders))){
                    continue;
                }
                $target = "";
                if($edit){
                    $target = " target='_blank'";
                }
                $this->html .= "<li><a href='{$pni->getUrl()}'$target>{$pni->getReversedName()}</a></li>";
            }
            
            $this->html .= "</ul>";
            if($edit || !$edit && count($cnis) > 0){
                $this->html .= "<h2><span class='mw-headline'>CNIs</span></h2>";
            }
            $this->html .= "<ul>";
            foreach($cnis as $cni){
                if((!empty($leaders) && in_array($cni->getId(), $leaders)) || (!empty($leaders) && in_array($cni->getId(), $leaders))){
                    continue;
                }
                $target = "";
                if($edit){
                    $target = " target='_blank'";
                }
                $this->html .= "<li><a href='{$cni->getUrl()}'$target>{$cni->getReversedName()}</a></li>";
            }
            $this->html .= "</ul>";
            if($edit || !$edit && count($ars) > 0){
                $this->html .= "<h2><span class='mw-headline'>Associated Researchers</span></h2>";
            }
            $this->html .= "<ul>";
            foreach($ars as $ar){
                if((!empty($leaders) && in_array($ar->getId(), $leaders)) || (!empty($coleaders) && in_array($ar->getId(), $coleaders))){
                    continue;
                }
                $target = "";
                if($edit){
                    $target = " target='_blank'";
                }
                $this->html .= "<li><a href='{$ar->getUrl()}'$target>{$ar->getReversedName()}</a></li>";
            }
            $this->html .= "</ul></td>";
            if($wgUser->isLoggedIn()){
                $this->html .= "<td width='50%' valign='top'>";
                if($edit || !$edit && count($hqps) > 0){
                    $this->html .= "<h2><span class='mw-headline'>HQP</span></h2>";
                }
                $this->html .= "<ul>";
                foreach($hqps as $hqp){
                    $target = ""; 
                    if($edit){
                        $target = " target='_blank'";
                    }
                    $this->html .= "<li><a href='{$hqp->getUrl()}'$target>{$hqp->getReversedName()}</a></li>";
                }
                $this->html .= "</ul></td>";
            }
            $this->html .= "</tr></table>";
        }
    }
    
    function showDescription(){
        global $wgServer, $wgScriptPath;
        
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        $project = $this->project;
        
        if($edit || !$edit && $project->getDescription() != ""){
            $this->html .= "<h2><span class='mw-headline'>Description</span></h2>";
        }
        if(!$edit){
            $this->html .= "<p>" . $this->sandboxParse($project->getDescription()) . "</p>";
        }
        else{
            $this->html .= "<textarea name='description' style='height:500px;'>{$project->getDescription()}</textarea>";
        }
    }

    function showProblem(){
        global $wgServer, $wgScriptPath;
        
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        $project = $this->project;
        
        if($edit || !$edit && $project->getProblem() != ""){
            $this->html .= "<h2><span class='mw-headline'>Problem Summary</span></h2>";
        }
        if(!$edit){
            $this->html .= "<p>" . $this->sandboxParse($project->getProblem()) . "</p>";
        }
        else{
            $this->html .= "<textarea name='problem' style='height:500px;'>{$project->getProblem()}</textarea>";
        }
    }

    function showSolution(){
        global $wgServer, $wgScriptPath;
        
        $edit = (isset($_POST['edit']) && !isset($this->visibility['overrideEdit']));
        $project = $this->project;
        
        if($edit || !$edit && $project->getSolution() != ""){
            $this->html .= "<h2><span class='mw-headline'>Proposed Solution Summary</span></h2>";
        }
        if(!$edit){
            $this->html .= "<p>" . $this->sandboxParse($project->getSolution()) . "</p>";
        }
        else{
            $this->html .= "<textarea name='solution' style='height:500px;'>{$project->getSolution()}</textarea>";
        }
    }

    function sandboxParse($wikiText) {
        global $wgTitle, $wgUser;
        $myParser = new Parser();
        $myParserOptions = ParserOptions::newFromUser($wgUser);
        $result = $myParser->parse($wikiText, $wgTitle, $myParserOptions);
        return $result->getText();
    }

}    
    
?>
