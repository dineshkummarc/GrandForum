<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['Report'] = 'Report'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['Report'] = $dir . 'Report.i18n.php';
$wgSpecialPageGroups['Report'] = 'reporting-tools';

$wgHooks['TopLevelTabs'][] = 'Report::createTab';
$wgHooks['SubLevelTabs'][] = 'Report::createSubTabs';

class Report extends AbstractReport{
    
    function Report(){
        $report = @$_GET['report'];
        $topProjectOnly = false;
        if(isset($_GET['project']) && ($report == "NIReport" || $report == "HQPReport")){
            $topProjectOnly = true;
        }
        $this->AbstractReport(dirname(__FILE__)."/../ReportXML/$report.xml", -1, false, $topProjectOnly);
    }

    static function createTab($tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $person = Person::newFromWgUser();
        $page = "Report";
        if($person->isRoleDuring(HQP, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
            $page = "Report?report=HQPReport";
        }
        else if($person->isRoleDuring(CNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || 
                $person->isRoleDuring(PNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || 
                $person->isRoleAtLeast(MANAGER)){
            $page = "Report?report=NIReport";
        }
        else if(count($person->leadership()) > 0){
            $projects = $person->leadership();
            $project = $projects[0];
            if(!$project->isSubProject()){
                if($project->getPhase() < PROJECT_PHASE || $project->isDeleted() && substr($project->getEffectiveDate(), 0, 4) == REPORTING_YEAR){
                    $page = "Report?report=ProjectFinalReport&project={$project->getName()}";
                }
                else if(!$project->isDeleted()){
                    $page = "Report?report=ProjectReport&project={$project->getName()}";
                }
            }
        }
        else if(in_array($person->getId(), $special_evals)){
            $page = "Report?report=EvalOptReport";
        }
        else if($person->isEvaluator()){
            $page = "Report?report=EvalReport";
        }
        else if($person->isRoleAtLeast(RMC)){
            $page = "Report?report=EvalLOIReport";
        }
        else if($person->isRole(ISAC)){
            $page = "Report?report=ISACReview";
        }
        else if($person->isRole(CHAMP)){
            $projects = Project::getAllProjects();
            foreach($projects as $project){
                if($project->getPhase() == PROJECT_PHASE){
                    if($person->isChampionOf($project, REPORTING_RMC_MEETING)){
                        $page = "Report?report=ChampionReport&project={$project->getName()}";
                        break;
                    }
                    foreach($project->getSubProjects() as $sub){
                        if($person->isChampionOfOn($sub, REPORTING_RMC_MEETING)){
                            $page = "Report?report=ChampionReport&project={$project->getName()}";
                            break;
                        }
                    }
                }
            }
        }
        $tabs["Reports"] = TabUtils::createTab("My Reports");
        
        return true;
    }
    
    static function createSubTabs($tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $person = Person::newFromWgUser();
        $url = "$wgServer$wgScriptPath/index.php/Special:Report?report=";
        
        // HQP Report
        if($person->isRoleDuring(HQP, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || 
           $person->isRoleAtLeast(MANAGER)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "HQPReport")) ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("HQP", "{$url}HQPReport", $selected);
        }
        
        // NI Report
        if($person->isRoleDuring(CNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || 
           $person->isRoleDuring(PNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || 
           $person->isRoleAtLeast(MANAGER)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "NIReport")) ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("NI", "{$url}NIReport", $selected);
        }
        
        // Project Leader Reports
        $leadership = $person->leadership();
        if(count($leadership) > 0){
            $projectDone = array();
            foreach($leadership as $project){
                if(!$project->isSubProject()){
                    if(isset($projectDone[$project->getName()])){
                        continue;
                    }
                    $projectDone[$project->getName()] = true;
                    if($project->getPhase() < PROJECT_PHASE || ($project->isDeleted() && substr($project->getEffectiveDate(), 0, 4) == REPORTING_YEAR)){
                        $type = "ProjectFinalReport";
                    }
                    else if(!$project->isDeleted()){
                        $type = "ProjectReport";
                    }
                    else{
                        continue;
                    }
                    $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "$type" && $_GET['project'] == $project->getName()) ? "selected" : false;
                    $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab($project->getName(), "{$url}$type&project={$project->getName()}", $selected);
                }
            }
        }
        
        // Evaluator Opt Report
        if(in_array($person->getId(), $special_evals)){
            // Needs to be changed in EvalOptReport.xml as well
            $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "EvalOptReport") ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Evaluator", "{$url}EvalOptReport", $selected);
        }
        else if($person->isEvaluator()){
            // Evaluator Report
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "EvalReport" || $_GET['report'] == "EvalOptReport")) ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Evaluator", "{$url}EvalReport", $selected);
        }
        
        // ISAC Review
        if($person->isRole(ISAC) || $person->isRoleAtLeast(MANAGER)){
            $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "ISACReview") ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("ISAC", "{$url}ISACReview", $selected);
        }
        if($person->isRole(ISAC) || $person->isRoleAtLeast(MANAGER) || $person->getId() == 11){ 
            // Check if the person is ISAC, MANAGER or K.S.B, which is super ugly, but was requested last minute, so no time to do it any better
            $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "ISACMaterials") ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("ISAC Reviews", "{$url}ISACMaterials", $selected);
        }
        
        //LOI Evaluation
        if($person->isRoleAtLeast(RMC)){
            $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "EvalLOIReport") ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("LOI", "{$url}EvalLOIReport", $selected);
            
            $selected = @($wgTitle->getText() == "Report" && $_GET['report'] == "EvalRevLOIReport") ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Revised LOI", "{$url}EvalRevLOIReport", $selected);
        }
        
        // Champion Report
        if($person->isRole(CHAMP)){
            $projects = Project::getAllProjects();
            foreach($projects as $project){
                if($project->getPhase() == PROJECT_PHASE){
                    $showTab = false;
                    if($person->isChampionOfOn($project, REPORTING_RMC_MEETING)){
                        $showTab = true;
                    }
                    else{
                        foreach($project->getSubProjects() as $sub){
                            if($person->isChampionOfOn($sub, REPORTING_RMC_MEETING)){
                                $showTab = true;
                                break;
                            }
                        }
                    }
                    if($showTab){
                        $selected = ($wgTitle->getText() == "Report" && $_GET['report'] == "ChampionReport" && $_GET['project'] == $project->getName()) ? "selected" : false;
                        $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Champion ({$project->getName()})", "{$url}ChampionReport&project={$project->getName()}", $selected);
                    }
                }
            }
        }
        return true;
    }
}

?>
