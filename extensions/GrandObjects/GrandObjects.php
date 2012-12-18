<?php
// The purpose of this file is to simply include the other datastructures
require_once("Addressing.php");
require_once("Blob.php");
define("WORKS_WITH", 'Works With');
define("SUPERVISES", 'Supervises');

autoload_register('GrandObjects');
autoload_register('GrandObjects/API');
$wgHooks['BeforePageDisplay'][] = 'createModels';

global $apiRequest;
// Person
$apiRequest->addAction('Hidden','person', new PersonAPI());
$apiRequest->addAction('Hidden','person/:id', new PersonAPI());
$apiRequest->addAction('Hidden','person/:id/projects', new PersonProjectsAPI());
$apiRequest->addAction('Hidden','person/:id/roles', new PersonRolesAPI());
$apiRequest->addAction('Hidden','person/:id/products', new PersonProductAPI());
$apiRequest->addAction('Hidden','person/:id/products/:productId', new PersonProductAPI());
// Role
$apiRequest->addAction('Hidden','role', new RoleAPI());
$apiRequest->addAction('Hidden','role/:id', new RoleAPI());
// Project
$apiRequest->addAction('Hidden','project', new ProjectAPI());
$apiRequest->addAction('Hidden','project/:id', new ProjectAPI());
// Product
$apiRequest->addAction('Hidden','product', new ProductAPI());
$apiRequest->addAction('Hidden','product/:id/authors', new PersonProductAPI());
$apiRequest->addAction('Hidden','product/:id/authors/:personId', new PersonProductAPI());

function createModels($out, $skin){
    global $wgServer, $wgScriptPath;
    $out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/RelationModel.js'></script>");
    $out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/RangeCollection.js'></script>");
    
    $out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/Person.js'></script>");
    $out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/Role.js'></script>");
	$out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/Project.js'></script>");
    $out->addScript("<script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GrandObjects/BackboneModels/Product.js'></script>");
    
    return true;
}
?>
