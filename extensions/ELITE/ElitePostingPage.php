<?php

BackbonePage::register('ElitePostingPage', 'ELITE', 'network-tools', dirname(__FILE__));

$wgHooks['ToolboxLinks'][] = 'ElitePostingPage::createToolboxLinks';

class ElitePostingPage extends BackbonePage {
    
    function isListed(){
        return false;
    }
    
    function userCanExecute($user){
        return true;
    }
    
    function getTemplates(){
        return array('Backbone/*',
                     'elitepostings',
                     'eliteposting',
                     'eliteposting_edit',
                     'elite_admin',
                     'elitepostings_admin',
                     'eliteprofiles_admin');
    }
    
    function getViews(){
        global $wgOut;
        $departments = json_encode(array_values(Person::getAllDepartments()));
        
        $wgOut->addScript("<script type='text/javascript'>
            var allDepartments = $departments;
            isAllowedToCreateElitePostings = ".json_encode(ElitePosting::isAllowedToCreate()).";
        </script>");
    
        return array('Backbone/*',
                     'ElitePostingsView',
                     'ElitePostingView',
                     'ElitePostingEditView',
                     'EliteAdminView',
                     'ElitePostingsAdminView',
                     'EliteProfilesAdminView');
    }
    
    function getModels(){
        return array('Backbone/*',
                     'ElitePosting',
                     'EliteProfile');
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        /*if($me->isLoggedIn()){
            $toolbox['Postings']['links'][] = TabUtils::createToolboxLink("Jobs/Internships", "$wgServer$wgScriptPath/index.php/Special:ElitePostingPage");
        }*/
        return true;
    }

}

?>