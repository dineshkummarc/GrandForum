<?php

$wgHooks['ToolboxLinks'][] = 'ManagePeople::createToolboxLinks';
BackbonePage::register('ManagePeople', 'ManagePeople', 'network-tools', dirname(__FILE__));

class ManagePeople extends BackbonePage {
    
    function isListed(){
        return false;
    }
    
    function userCanExecute($user){
        $me = Person::newFromWgUser();
        return $me->isRoleAtLeast(NI);
    }
    
    function getTemplates(){
        return array('Backbone/*',
                     'manage_people',
                     'manage_people_row',
                     'edit_roles',
                     'edit_roles_row',
                     'edit_projects',
                     'edit_projects_row');
    }
    
    function getViews(){
        return array('Backbone/*',
                     'ManagePeopleView',
                     'ManagePeopleRowView',
                     'ManagePeopleEditRolesView',
                     'ManagePeopleEditProjectsView');
    }
    
    function getModels(){
        return array('Backbone/*');
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath, $wgUser;
        if(self::userCanExecute($wgUser)){
            $toolbox['People']['links'][] = TabUtils::createToolboxLink("Manage People", "$wgServer$wgScriptPath/index.php/Special:ManagePeople");
        }
        return true;
    }

}

?>
