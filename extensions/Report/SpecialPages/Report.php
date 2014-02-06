<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['Report'] = 'Report'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['Report'] = $dir . 'Report.i18n.php';
$wgSpecialPageGroups['Report'] = 'reporting-tools';

$wgHooks['SkinTemplateContentActions'][] = 'Report::showTabs';

class Report extends AbstractReport{
    
    function Report(){
        $report = @$_GET['report'];
        $topProjectOnly = false;
        if(isset($_GET['project']) && ($report == "NIReport" || $report == "HQPReport")){
            $topProjectOnly = true;
        }
        $this->AbstractReport(dirname(__FILE__)."/../ReportXML/$report.xml", -1, false, $topProjectOnly);
    }

    static function createTab(){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $person = Person::newFromId($wgUser->getId());
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
        
        $selected = "";
        if($wgTitle->getText() == "Report"){
            $selected = "selected";
        }
        
        echo "<li class='top-nav-element $selected'>\n";
        echo "    <span class='top-nav-left'>&nbsp;</span>\n";
        echo "    <a id='lnk-my_report' class='top-nav-mid' href='$wgServer$wgScriptPath/index.php/Special:$page' class='new'>My Reports</a>\n";
        echo "    <span class='top-nav-right'>&nbsp;</span>\n";
        echo "</li>";
    }
    
    static function showTabs(&$content_actions){
        global $wgTitle, $wgUser, $wgServer, $wgScriptPath, $special_evals;
        if($wgTitle->getText() == "Report"){
            $content_actions = array();
            $person = Person::newFromId($wgUser->getId());
            
            // Individual Report
            if($person->isRoleDuring(HQP, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRoleAtLeast(MANAGER)){
                $class = @($wgTitle->getText() == "Report" && ($_GET['report'] == "HQPReport")) ? "selected" : false;
                $text = HQP;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => $text,
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=HQPReport",
                        );
            }
            if($person->isRoleDuring(CNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRoleDuring(PNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRoleAtLeast(MANAGER)){
                $class = @($wgTitle->getText() == "Report" && ($_GET['report'] == "NIReport")) ? "selected" : false;
                $text = "Individual";
                if($person->isRoleDuring(PNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END))
                    $text = PNI;
                else if($person->isRoleDuring(CNI, REPORTING_CYCLE_START, REPORTING_CYCLE_END))
                    $text = CNI;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => $text,
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=NIReport",
                        );
            }
            
            // Project Leader Report
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
                        @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "$type" && $_GET['project'] == $project->getName()) ? "selected" : false;
                        $content_actions[] = array (
                                 'class' => $class,
                                 'text'  => "{$project->getName()}",
                                 'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=$type&project={$project->getName()}",
                                );
                    }
                }
            }
            
            // Evaluator Opt Report
            if(in_array($person->getId(), $special_evals)){
                // Needs to be changed in EvalOptReport.xml as well
                @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "EvalOptReport") ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "Evaluator",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=EvalOptReport",
                        );
            }
            else if($person->isEvaluator()){
                // Evaluator Report
                @$class = ($wgTitle->getText() == "Report" && ($_GET['report'] == "EvalReport" || $_GET['report'] == "EvalOptReport")) ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "Evaluator",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=EvalReport",
                        );
            }
            
            // ISAC Review
            if($person->isRole(ISAC) || $person->isRoleAtLeast(MANAGER)){
                @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "ISACReview") ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "ISAC",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=ISACReview",
                        );
            }
            if($person->isRole(ISAC) || $person->isRoleAtLeast(MANAGER) || $person->getId() == 11){ 
                // Check if the person is ISAC, MANAGER or K.S.B, which is super ugly, but was requested last minute, so no time to do it any better
                @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "ISACMaterials") ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "ISAC Reviews",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=ISACMaterials",
                        );
            }

            //LOI Evaluation
            if($person->isRoleAtLeast(RMC)){
                @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "EvalLOIReport") ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "LOI",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=EvalLOIReport",
                        );
                
                @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "EvalRevLOIReport") ? "selected" : false;
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => "Revised LOI",
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=EvalRevLOIReport",
                        );
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
                            @$class = ($wgTitle->getText() == "Report" && $_GET['report'] == "ChampionReport" && $_GET['project'] == $project->getName()) ? "selected" : false;
                            $content_actions[] = array (
                                'class' => $class,
                                'text'  => "Champion ({$project->getName()})",
                                'href'  => "$wgServer$wgScriptPath/index.php/Special:Report?report=ChampionReport&project={$project->getName()}",
                            );
                        }
                    }
                }
            }
        }
        return true;
    }
}

?>
