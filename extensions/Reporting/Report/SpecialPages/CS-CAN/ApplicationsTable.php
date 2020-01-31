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

    var $allPeople;

    function ApplicationsTable() {
        SpecialPage::__construct("ApplicationsTable", null, false, 'runApplicationsTable');
    }
    
    function userCanExecute($user){
        $person = Person::newFromUser($user);
        return ($person->isRoleAtLeast(STAFF) ||
                $person->getId() == 734 ||
                $person->getId() == 246 ||
                $person->getId() == 1068 ||
                $person->getId() == 250 ||
                $person->getId() == 237 ||
                $person->getId() == 261);
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
        ApplicationsTable::generateHTML($wgOut);
    }
    
    function initArrays(){
        $this->allPeople = array_merge(Person::getAllPeople());
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
        
        if($me->isRoleAtLeast(STAFF) ||
           $me->getId() == 734 ||
           $me->getId() == 246 ||
           $me->getId() == 1068 ||
           $me->getId() == 250 ||
           $me->getId() == 237 ||
           $me->getId() == 261){
            $links[] = "<a href='$wgServer$wgScriptPath/index.php/Special:ApplicationsTable?program=survey'>Department Survey</a>";
        }
        
        $wgOut->addHTML("<h1>Report Tables:&nbsp;".implode("&nbsp;|&nbsp;", $links)."</h1><br />");
        if(!isset($_GET['program'])){
            return;
        }
        $program = $_GET['program'];
        
        $this->initArrays();
        
        if($program == "survey"){
            $this->generateDepartmentSurvey();
        }
        return;
    }
    
    function generateDepartmentSurvey(){
        global $wgOut;
        $tabbedPage = new InnerTabbedPage("reports");
        
        $q = array();
        for($i = 0; $i < 9; $i++){
            if($i != 1){
                $q["Q".($i+1)] = new RadioReportItem();
                $q["Q".($i+1)]->setBlobType(BLOB_TEXT);
                $q["Q".($i+1)]->setBlobItem($i+1);
                $q["Q".($i+1)]->setBlobSection("SURVEY");
            }
        }
        
        $tabbedPage->addTab(new ApplicationTab('RP_DEPT_SURVEY', $this->allPeople, 2020, "2020", $q));
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
