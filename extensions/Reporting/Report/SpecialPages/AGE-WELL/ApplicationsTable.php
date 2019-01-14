<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['ApplicationsTable'] = 'ApplicationsTable'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['ApplicationsTable'] = $dir . 'ApplicationsTable.i18n.php';
$wgSpecialPageGroups['ApplicationsTable'] = 'network-tools';

$wgHooks['SubLevelTabs'][] = 'ApplicationsTable::createSubTabs';

function runApplicationsTable($par) {
    ApplicationsTable::execute($par);
}

class ApplicationsTable extends SpecialPage{

    var $nis;
    var $fullHQPs;
    var $hqps;
    var $externals;
    var $wps;
    var $ccs;
    var $ihs;
    var $projects;

    function ApplicationsTable() {
        SpecialPage::__construct("ApplicationsTable", null, false, 'runApplicationsTable');
    }
    
    function userCanExecute($user){
        $person = Person::newFromUser($user);
        return ($person->isRoleAtLeast(SD) || count($person->getEvaluates('RP_SUMMER', 2015, "Person")) > 0 || $person->getName() == "Euson.Yeung" || $person->getName() == "Susan.Jaglal");
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
        ApplicationsTable::generateHTML($wgOut);
    }
    
    function initArrays(){
        $this->nis = array_merge(Person::getAllPeople(NI), 
                                 Person::getAllCandidates(NI),
                                 Person::getAllPeople(EXTERNAL),
                                 Person::getAllCandidates(EXTERNAL));
        
        $this->fullHQPs = Person::getAllPeople(HQP);
        
        $this->hqps = array_merge($this->fullHQPs,
                                  Person::getAllCandidates(HQP));
                                  
        $this->externals = array_merge(Person::getAllPeople(EXTERNAL),
                                       Person::getAllCandidates(EXTERNAL));
                                  
        $this->wps = Theme::getAllThemes();
        
        $this->ccs = array();
        $this->ihs = array();
        $this->projects = Project::getAllProjects();
        foreach($this->projects as $project){
            if($project->getType() == 'Administrative'){
                $this->ccs[] = $project;            
            }
            if($project->getType() == "Innovation Hub"){
                $this->ihs[] = $project;
            }
        }
    }
    
    function generateHTML($wgOut){
        global $wgUser, $wgServer, $wgScriptPath, $wgRoles, $config;
        
        $me = Person::newFromWgUser();
        
        $links = array();
        
        $wgOut->addHTML("<style type='text/css'>
            #bodyContent > h1:first-child {
                display: none;
            }
            
            #contentSub {
                display: none;
            }
        </style>");
        
        if($me->isRoleAtLeast(SD)){
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=sip'>SIP</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=cip'>CIP</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=crp'>CRP</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=access'>ACCESS</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=catalyst'>Catalyst</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=award'>Award</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=wp'>WP</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=cc'>CC</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=ih'>IH</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=project'>Project Evaluation</a>";
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=fellow'>Policy Challenge</a>";
        }
        if($me->isRoleAtLeast(SD) || count($me->getEvaluates('RP_SUMMER', 2015, "Person")) > 0 || $me->getName() == "Euson.Yeung" || $me->getName() == "Susan.Jaglal"){
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=summer'>Summer Institute</a>";
        }
        
        $wgOut->addHTML("<h1>Report Tables:&nbsp;".implode("&nbsp;|&nbsp;", $links)."</h1><br />");
        if(!isset($_GET['program'])){
            return;
        }
        $program = $_GET['program'];
        
        $this->initArrays();
        
        if($program == "sip" && $me->isRoleAtLeast(SD)){
            $this->generateSIP();
        }
        else if($program == "cip" && $me->isRoleAtLeast(SD)){
            $this->generateCIP();
        }
        else if($program == "crp" && $me->isRoleAtLeast(SD)){
            $this->generateCRP();
        }
        if($program == "access" && $me->isRoleAtLeast(SD)){
            $this->generateAccess();
        }
        else if($program == "catalyst" && $me->isRoleAtLeast(SD)){
            $this->generateCatalyst();
        }
        else if($program == "award" && $me->isRoleAtLeast(SD)){
            $this->generateAward();
        }
        else if($program == "summer" && ($me->isRoleAtLeast(SD) || count($me->getEvaluates('RP_SUMMER', 2015, "Person")) > 0 || $me->getName() == "Euson.Yeung" || $me->getName() == "Susan.Jaglal")){
            $this->generateSummer();
        }
        else if($program == "fellow" && $me->isRoleAtLeast(SD)){
            $this->generateFellow();
        }
        else if($program == "wp" && $me->isRoleAtLeast(SD)){
            $this->generateWP();
        }
        else if($program == "cc" && $me->isRoleAtLeast(SD)){
            $this->generateCC();
        }
        else if($program == "ih" && $me->isRoleAtLeast(SD)){
            $this->generateIH();
        }
        else if($program == "project" && $me->isRoleAtLeast(SD)){
            $this->generateProject();
        }
        return;
    }
    
    function generateSIP(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_ACC_2018_2', $this->nis, 2018, "Accelerator 4"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_ACC_2018', $this->nis, 2018, "Accelerator 3"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_ACC_09_2017', $this->nis, 2017, "Accelerator 2"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_ACC', $this->nis, 2017, "Accelerator"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_01_2017', $this->nis, 2015, "01-2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_10_2016', $this->nis, 2015, "10-2016"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_07_2016', $this->nis, 2015, "07-2016"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP_04_2016', $this->nis, 2015, "04-2016"));
        $tabbedPage->addTab(new ApplicationTab('RP_SIP', $this->nis, 2015, "01-2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateCIP(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_CIP', $this->nis, 2015, "2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateCRP(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_CRP', $this->nis, 2018, "2018"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateCatalyst(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_CAT', $this->nis, 2017, "2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_CAT', $this->nis, 2016, "2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_CAT', $this->nis, 2015, "2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateAccess(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_04_2019', $this->fullHQPs, 2019, "04-2019"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_01_2019', $this->fullHQPs, 2019, "01-2019"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_07_2018', $this->fullHQPs, 2018, "07-2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_04_2018', $this->fullHQPs, 2018, "04-2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_01_2018', $this->fullHQPs, 2018, "01-2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_10_2017', $this->fullHQPs, 2017, "10-2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_07_2017', $this->fullHQPs, 2017, "07-2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_04_2017', $this->fullHQPs, 2017, "04-2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_01_2017', $this->fullHQPs, 2017, "01-2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_ACCESS_10_2016', $this->fullHQPs, 2016, "10-2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateAward(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        
        $level = new CheckboxReportItem();
        $level->setBlobType(BLOB_ARRAY);
        $level->setBlobItem(HQP_APPLICATION_LVL);
        $level->setBlobSection(HQP_APPLICATION_FORM);
        $level->setId("level");
        
        $michael = new CheckboxReportItem();
        $michael->setBlobType(BLOB_ARRAY);
        $michael->setBlobItem("HQP_APPLICATION_MICHAEL");
        $michael->setBlobSection(HQP_APPLICATION_FORM);
        $michael->setId("MICHAEL");
             
        $bme = new CheckboxReportItem();
        $bme->setBlobType(BLOB_ARRAY);
        $bme->setBlobItem("HQP_APPLICATION_BME");
        $bme->setBlobSection(HQP_APPLICATION_FORM);
        $bme->setId("BME");
        
        $wbhi = new CheckboxReportItem();
        $wbhi->setBlobType(BLOB_ARRAY);
        $wbhi->setBlobItem("HQP_APPLICATION_WBHI");
        $wbhi->setBlobSection(HQP_APPLICATION_FORM);
        $wbhi->setId("WBHI");
        
        $mira = new CheckboxReportItem();
        $mira->setBlobType(BLOB_ARRAY);
        $mira->setBlobItem("HQP_APPLICATION_MIRA");
        $mira->setBlobSection(HQP_APPLICATION_FORM);
        $mira->setId("MIRA");
        
        $nbhrf = new CheckboxReportItem();
        $nbhrf->setBlobType(BLOB_ARRAY);
        $nbhrf->setBlobItem("HQP_APPLICATION_NBHRF");
        $nbhrf->setBlobSection(HQP_APPLICATION_FORM);
        $nbhrf->setId("NBHRF");
        
        $tabbedPage->addTab(new ApplicationTab(RP_HQP_APPLICATION, array_merge($this->hqps, $this->externals), 2018, "2018", array("Level" => $level,
                                                                                                                                   "Michael F. Harcourt" => $michael,
                                                                                                                                   "BME" => $bme,
                                                                                                                                   "WBHI" => $wbhi,
                                                                                                                                   "MIRA" => $mira,
                                                                                                                                   "NBHRF" => $nbhrf)));
        $tabbedPage->addTab(new ApplicationTab(RP_HQP_APPLICATION, array_merge($this->hqps, $this->externals), 2017, "2017"));
        $tabbedPage->addTab(new ApplicationTab(RP_HQP_APPLICATION, array_merge($this->hqps, $this->externals), 2016, "2016"));
        $tabbedPage->addTab(new ApplicationTab(RP_HQP_APPLICATION, array_merge($this->hqps, $this->externals), 2015, "2015"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateSummer(){
        global $wgOut;
        $me = Person::newFromWgUser();
        $summerHQPs = array();
        if($me->isRoleAtLeast(SD) || $me->getName() == "Euson.Yeung" || $me->getName() == "Susan.Jaglal"){
            $summerHQPs = $this->hqps;
        }
        else{
            foreach($this->hqps as $hqp){
                if($me->isEvaluatorOf($hqp, 'RP_SUMMER', 2015, "Person")){
                    $summerHQPs[] = $hqp;
                }
            }
        }
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_SUMMER', $summerHQPs, 2018, "2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_SUMMER', $summerHQPs, 2016, "2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_SUMMER', $summerHQPs, 2015, "2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateFellow(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_FELLOW', $this->hqps, 2018, "2018"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateWP(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->wps, 2017, "2017-18"));
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->wps, 2016, "2016-17"));
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->wps, 2015, "2015-16"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateCC(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->ccs, 2017, "2017-18"));
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->ccs, 2016, "2016-17"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateIH(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_WP_REPORT', $this->ihs, 2017, "2017-18"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    function generateProject(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        $tabbedPage->addTab(new ApplicationTab('RP_PROJ_EVALUATION', $this->projects, 2017, "2018"));
        $tabbedPage->addTab(new ApplicationTab('RP_PROJ_EVALUATION', $this->projects, 2016, "2017"));
        $tabbedPage->addTab(new ApplicationTab('RP_PROJ_EVALUATION', $this->projects, 2015, "2016"));
        $wgOut->addHTML($tabbedPage->showPage());
    }
    
    static function createSubTabs(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $person = Person::newFromWgUser();
        
        if(self::userCanExecute($wgUser)){
            $selected = @($wgTitle->getText() == "ApplicationsTable") ? "selected" : false;
            $tabs["Manager"]['subtabs'][] = TabUtils::createSubTab("Reports", "$wgServer$wgScriptPath/index.php/Special:ApplicationsTable", $selected);
        }
        return true;
    }

}

?>
