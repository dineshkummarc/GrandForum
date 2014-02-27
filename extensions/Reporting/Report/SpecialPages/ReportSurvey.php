<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['ReportSurvey'] = 'ReportSurvey'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['ReportSurvey'] = $dir . 'ReportSurvey.i18n.php';
$wgSpecialPageGroups['ReportSurvey'] = 'reporting-tools';

$wgHooks['SkinTemplateContentActions'][] = 'ReportSurvey::showTabs';
$wgHooks['TopLevelTabs'][] = 'ReportSurvey::createTab';

class ReportSurvey extends AbstractReport{
    
    function ReportSurvey(){
        $report = @$_GET['report'];
        $this->AbstractReport(dirname(__FILE__)."/../ReportXML/$report.xml", -1, false, false);
        wfLoadExtensionMessages("ReportSurvey");
        SpecialPage::SpecialPage("ReportSurvey", HQP.'+', true);
        $this->showInstructions = false;
    }

    static function createTab($tabs){
		global $wgServer, $wgScriptPath, $wgUser, $wgTitle;
		$person = Person::newFromId($wgUser->getId());
		if(!$wgUser->isLoggedIn()){
            return true;
        }
		if($person->isRoleAtLeast(HQP)){
		    $page = "ReportSurvey?report=MindTheGap";
		}
		if($person->isRoleAtLeast(MANAGER)){
		    $page = "ReportSurvey?report=MindTheGapManager";
		}
		
		$selected = "";
		if($wgTitle->getText() == "ReportSurvey"){
		    $selected = "selected";
		}
		if($page != null){
		    $tabs["Surveys"] = array('id' => "lnk-my_surveys",
                                     'href' => "$wgServer$wgScriptPath/index.php/Special:$page", 
                                     'text' => "Surveys", 
                                     'selected' => $selected);
		}
        return true;
	}

    static function showTabs(&$content_actions){
        global $wgTitle, $wgUser, $wgServer, $wgScriptPath;
        if($wgTitle->getText() == "ReportSurvey"){
            $content_actions = array();
            $person = Person::newFromId($wgUser->getId());
            
            // Individual Report
            if($person->isRoleAtLeast(MANAGER)){
                $class = @($wgTitle->getText() == "ReportSurvey" && ($_GET['report'] == "MindTheGapManager")) ? "selected" : false;
                $text = "Mind The Gap (Manager)";
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => $text,
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:ReportSurvey?report=MindTheGapManager",
                        );
            }
            if($person->isRoleAtLeast(HQP)){
                $class = @($wgTitle->getText() == "ReportSurvey" && ($_GET['report'] == "MindTheGap")) ? "selected" : false;
                $text = "Mind The Gap";
                $content_actions[] = array (
                         'class' => $class,
                         'text'  => $text,
                         'href'  => "$wgServer$wgScriptPath/index.php/Special:ReportSurvey?report=MindTheGap",
                        );
            }
        }
        return true;
    }
}

?>
