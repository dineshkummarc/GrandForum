<?php

BackbonePage::register('ElitePostingPage', 'ELITE', 'network-tools', dirname(__FILE__));

$wgHooks['TopLevelTabs'][] = 'ElitePostingPage::createTab';
$wgHooks['SubLevelTabs'][] = 'ElitePostingPage::createSubTabs';

class ElitePostingPage extends BackbonePage {
    
    function isListed(){
        return false;
    }
    
    function userCanExecute($user){
        return true;
    }
    
    function getTemplates(){
        return array('Backbone/*',
                     'eliteposting',
                     'eliteposting_phd',
                     'eliteposting_edit',
                     'eliteposting_phd_edit',
                     'elite_host',
                     'elite_host_postings',
                     'elite_host_profiles',
                     'elite_admin',
                     'elite_admin_postings',
                     'elite_admin_profiles');
    }
    
    function getViews(){
        global $wgOut, $wgServer, $wgScriptPath;
        $departments = json_encode(array_values(Person::getAllDepartments()));
        $wgOut->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/ELITE/cities.js'></script>");
        $wgOut->addScript("<script type='text/javascript'>
            var allDepartments = $departments;
            isAllowedToCreateElitePostings = ".json_encode(ElitePosting::isAllowedToCreate()).";
        </script>");
    
        return array('Backbone/*',
                     'EliteHostView',
                     'ElitePostingView',
                     'ElitePostingEditView',
                     'EliteAdminView');
    }
    
    function getModels(){
        return array('Backbone/*',
                     'ElitePosting',
                     'EliteProfile');
    }
    
    static function createTab(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle, $special_evals;
        $tabs["ELITEHost"] = TabUtils::createTab("Host Panel");
        $tabs["ELITEAdmin"] = TabUtils::createTab("Admin Panel");
        return true;
    }
    
    static function createSubTabs(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle;
        $person = Person::newFromWgUser();
        if($person->isRole(EXTERNAL)){
            // Host
            $selected = @($wgTitle->getText() == "ElitePostingPage" && ($_GET['page'] == 'host' || $_GET['page'] == "")) ? "selected" : false;
            $tabs["ELITEHost"]['subtabs'][] = TabUtils::createSubTab("Host Panel", "{$wgServer}{$wgScriptPath}/index.php/Special:ElitePostingPage?page=host", $selected);
        }
        if($person->isRole(ADMIN)){
            // Admin
            $selected = @($wgTitle->getText() == "ElitePostingPage" && $_GET['page'] == 'admin') ? "selected" : false;
            $tabs["ELITEAdmin"]['subtabs'][] = TabUtils::createSubTab("Admin Panel", "{$wgServer}{$wgScriptPath}/index.php/Special:ElitePostingPage?page=admin#/admin", $selected);
        }
        
        return true;
    }

}

?>
