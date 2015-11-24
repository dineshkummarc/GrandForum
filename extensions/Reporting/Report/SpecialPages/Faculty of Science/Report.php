<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['Report'] = 'Report'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['Report'] = $dir . 'Report.i18n.php';
$wgSpecialPageGroups['Report'] = 'reporting-tools';

$wgHooks['TopLevelTabs'][] = 'Report::createTab';
$wgHooks['SubLevelTabs'][] = 'Report::createSubTabs';
$wgHooks['ToolboxLinks'][] = 'Report::createToolboxLinks';

class Report extends AbstractReport{
    
    function Report(){
        global $config;
        $report = @$_GET['report'];
        $topProjectOnly = false;
        if(isset($_GET['project']) && ($report == "NIReport" || $report == "HQPReport" || $report == "SABReport")){
            $topProjectOnly = true;
        }
        $this->AbstractReport(dirname(__FILE__)."/../../ReportXML/{$config->getValue('networkName')}/$report.xml", -1, false, $topProjectOnly);
    }

    static function createTab(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $tabs["Reports"] = TabUtils::createTab("My Annual Report");
        $tabs["Recommendations"] = TabUtils::createTab("My Recommendations");
        return true;
    }
    
    static function createSubTabs(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle;
        $person = Person::newFromWgUser();
        $url = "$wgServer$wgScriptPath/index.php/Special:Report?report=";
	if($person->isRole(NI)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FEC")) ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Annual Report", "{$url}FEC", $selected);
        }
        if($person->isRole(ISAC)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FECReview")) ? "selected" : false;
            $tabs["Recommendations"]['subtabs'][] = TabUtils::createSubTab("{$person->getDepartment()}", "{$url}FECReview", $selected);
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FECStats")) ? "selected" : false;
            $tabs["Recommendations"]['subtabs'][] = TabUtils::createSubTab("FOS", "{$url}FECStats", $selected);
	}

        return true;
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath, $config;
        return true;
    }
}

?>
