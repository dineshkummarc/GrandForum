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
                     'edit_role_projects',
                     'edit_projects',
                     'edit_projects_row',
                     'edit_themes',
                     'edit_themes_row',
                     'edit_universities',
                     'edit_universities_row',
                     'edit_relations',
                     'edit_relations_row',
                     'edit_subroles',
                     'edit_alumni');
    }
    
    function getViews(){
        global $wgOut;
        $universities = new Collection(University::getAllUniversities());
        $uniNames = $universities->pluck('name');
        $positions = json_encode(array_values(Person::getAllPositions()));

        $departments = json_encode(array_values(Person::getAllDepartments()));
        $organizations = array_unique($uniNames);
        sort($organizations);
        
        $organizations = json_encode($organizations);
        
        $wgOut->addScript("<script type='text/javascript'>
            var allUniversities = $organizations;
            var allPositions = $positions;
            var allDepartments = $departments;
        </script>");
        
        return array('Backbone/*',
                     'ManagePeopleView',
                     'ManagePeopleRowView',
                     'ManagePeopleEditRolesView',
                     'ManagePeopleEditProjectsView',
                     'ManagePeopleEditUniversitiesView',
                     'ManagePeopleEditRelationsView',
                     'ManagePeopleEditSubRolesView',
                     'ManagePeopleEditThemesView',
                     'ManagePeopleEditAlumniView');
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
