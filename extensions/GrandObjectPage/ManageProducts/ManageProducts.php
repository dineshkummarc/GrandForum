<?php

$wgHooks['ToolboxLinks'][] = 'ManageProducts::createToolboxLinks';

BackbonePage::register('ManageProducts', 'Manage Products', 'network-tools', dirname(__FILE__));

class ManageProducts extends BackbonePage {
    
    function userCanExecute($user){
        return true;
    }
    
    function getTemplates(){
        return array('Backbone/*',
                     'manage_products',
                     'manage_products_row');
    }
    
    function getViews(){
        return array('Backbone/*',
                     'ManageProductsView');
    }
    
    function getModels(){
        return array('Backbone/*');
    }
    
    static function createToolboxLinks($toolbox){
	    global $wgServer, $wgScriptPath;
	    //$toolbox['Products']['links'][] = TabUtils::createToolboxLink("Manage Products", "$wgServer$wgScriptPath/index.php/Special:ManageProducts");
	    return true;
	}

}

?>
