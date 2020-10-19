<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['Report'] = 'Report'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['Report'] = $dir . 'Report.i18n.php';
$wgSpecialPageGroups['Report'] = 'reporting-tools';

$wgHooks['TopLevelTabs'][] = 'Report::createTab';
$wgHooks['SubLevelTabs'][] = 'Report::createSubTabs';

require_once("GraduateStudents.php");
require_once("AnnualReportTable.php");

class Report extends AbstractReport{
    
    function Report(){
        global $config;
        $report = @$_GET['report'];
        $topProjectOnly = false;
        $this->AbstractReport(dirname(__FILE__)."/../../ReportXML/{$config->getValue('networkName')}/$report.xml", -1, false, $topProjectOnly);
    }

    static function createTab(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        if($wgUser->isLoggedIn()){
            $tabs["Reports"] = TabUtils::createTab("My Annual Report");
            $tabs["TimeUseReports"] = TabUtils::createTab("Time Use Report");
            $tabs["SupervisorReports"] = TabUtils::createTab("Supervisor Report");
            $tabs["StudentReports"] = TabUtils::createTab("Student Report");
            $tabs["ReportArchive"] = TabUtils::createTab("Report Archive");
            $tabs["Chair"] = TabUtils::createTab("Chair");
            $tabs["Dean"] = TabUtils::createTab("Dean");
            $tabs["FEC"] = TabUtils::createTab("FEC");
            $tabs["ATSEC"] = TabUtils::createTab("ATSEC");
            $tabs["CV"] = TabUtils::createTab("My QA CV");
        }
        return true;
    }
    
    static function createSubTabs(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle;
        $person = Person::newFromWgUser();
        $url = "$wgServer$wgScriptPath/index.php/Special:Report?report=";
        if($person->isRole(NI) || $person->isRole("ATS")){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FEC")) ? "selected" : false;
            $tabs["Reports"]['subtabs'][] = TabUtils::createSubTab("Annual Report", "{$url}FEC", $selected);
            
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "QACV")) ? "selected" : false;
            $tabs["CV"]['subtabs'][] = TabUtils::createSubTab("QA CV", "{$url}QACV", $selected);
        }

        if($person->isRole(ISAC) || $person->isRoleDuring(ISAC, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRole(IAC) || $person->isRole(ACHAIR)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "ChairTable")) ? "selected" : false;
            $tabs["Chair"]['subtabs'][] = TabUtils::createSubTab("Annual Reports", "{$url}ChairTable", $selected);
        }
        
        if($person->isRole(DEAN) || $person->isRoleDuring(DEAN, REPORTING_CYCLE_START, REPORTING_CYCLE_END) || $person->isRole(DEANEA)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "ChairTable")) ? "selected" : false;
            $tabs["Dean"]['subtabs'][] = TabUtils::createSubTab("Annual Reports", "{$url}ChairTable", $selected);
        }/*
        if($person->isRole(DEAN) || $person->isRole(VDEAN) || $person->isRole(HR) || $person->isSubRole("FEC")){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FECTable")) ? "selected" : false;
            $tabs["FEC"]['subtabs'][] = TabUtils::createSubTab("Annual Reports", "{$url}FECTable", $selected);
        }
        if($person->isSubRole("ATSEC")){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "FECTable")) ? "selected" : false;
            $tabs["ATSEC"]['subtabs'][] = TabUtils::createSubTab("Annual Reports", "{$url}FECTable", $selected);
        }*/
        if($person->isRole(ISAC) || $person->isRole(IAC)){
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "DepartmentPublications")) ? "selected" : false;
            $tabs["Chair"]['subtabs'][] = TabUtils::createSubTab("Publications", "{$url}DepartmentPublications", $selected);
        }
        
        $graddbs = GradDBFinancial::getAllFromHQP($person->getId());
        foreach($graddbs as $graddb){
            $supervisor = $graddb->getSupervisor();
            $md5 = $graddb->getMD5();
            foreach($graddb->getTerms() as $termyear){
                $term = str_replace("/", "", substr($termyear, 0, -4));
                $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "TimeUseReport{$term}") && $_GET['project'] == "GradDB:{$md5}") ? "selected" : false;
                $tabs["TimeUseReports"]['subtabs'][] = TabUtils::createSubTab("{$termyear}/{$supervisor->getLastName()}", "{$url}TimeUseReport{$term}&project=GradDB:{$md5}", $selected);
            }
        }
        
        if($person->isRole(HQP) || $person->isRole(NI)){
            // TODO: Limit to just certain types of Students?
            $selected = @($wgTitle->getText() == "Report" && ($_GET['report'] == "StudentReport")) ? "selected" : false;
            $tab = TabUtils::createSubTab("Report", "{$url}StudentReport", $selected);
            if($person->isRole(HQP)){
                $tabs["StudentReports"]['subtabs'][] = $tab;
            }
            else if($person->isRole(NI)){
                $tabs["SupervisorReports"]['subtabs'][] = $tab;
            }
        }
        
        return true;
    }
}

?>
