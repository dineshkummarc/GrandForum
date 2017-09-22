<?php

$wgHooks['ToolboxLinks'][] = 'GrantPage::createToolboxLinks';
BackbonePage::register('GrantPage', 'Grant Accts', 'network-tools', dirname(__FILE__));

class GrantPage extends BackbonePage {
    
    function isListed(){
        return false;
    }
    
    function userCanExecute($user){
        return true;
    }
    
    function getTemplates(){
        return array('grant',
                     'grants',
                     'edit_grant');
    }
    
    function getViews(){
        return array('GrantView',
                     'GrantsView',
                     'EditGrantView');
    }
    
    function getModels(){
        return array();
    }
    
    static function createToolboxLinks(&$toolbox){
	    global $wgServer, $wgScriptPath, $config, $wgUser;
	    $me = Person::newFromWgUser();
	    if($me->isRoleAtLeast(NI)){
	        $toolbox['Products']['links'][] = TabUtils::createToolboxLink("Manage Funding", 
	                                                                      "$wgServer$wgScriptPath/index.php/Special:GrantPage");
	    }
	    return true;
	}

}

?>
