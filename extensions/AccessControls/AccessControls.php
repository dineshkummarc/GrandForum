<?php

autoload_register('AccessControls');

require "Management.php";
require "AnnokiNamespaces.php";
require "AccessControls.body.php";
require "CustomSpecialSearch.php";
require "ProtectedChangesList.php";
require "CustomSearchEngine.php";
require "EditPermissions.php";
require "GrandAccess.php";
require "UserLogin.php";

/** Extension configuration **/
$egAnnokiNamespaces = new AnnokiNamespaces();
$egNamespaceAllowUsersWithoutNamespaces = true;
$egNamespaceAllowPagesInMainNS = false;
$egAnProtectUploads = true; //Setting this to false will expose all existing protected uploads (until reenabled).

//Without this uploads will not be accessible
$wgUploadPath = "$wgScriptPath/AnnokiUploadAuth.php";

//this is needed in order for users to be able to protect pages in their namespace or to delete pages
$wgGroupPermissions['user']['protect']          = true;
$wgGroupPermissions['user']['delete'] = true;
$wgGroupPermissions['user']['browsearchive'] = true;
$wgGroupPermissions['user']['undelete'] = true;
$wgGroupPermissions['user']['deletedhistory'] = true;
$wgGroupPermissions['user']['createaccount'] = true;
$wgGroupPermissions['user']['upload_by_url'] = true;
$wgGroupPermissions['*']['createaccount'] = false;

$wgExtensionFunctions[] = "initializeAccessControls";
$wgExtensionFunctions[] = "UploadProtection::initUploadFiles";

//$wgHooks['ParserAfterTidy'][] = 'showQueryCounter';
$wgHooks['userCan'][] = 'onUserCan';
$wgHooks['SpecialPageBeforeExecute'][] = 'onUserCanExecute';
$wgHooks['AbortMove'][] = 'onAbortMove';
$wgHooks['AbortLogin'][] = 'onAbortLogin';
$wgHooks['TitleMoveComplete'][] = 'onTitleMoveComplete';
$wgHooks['FetchChangesList'][] = 'onFetchChangesList';
$wgHooks['UnknownAction'][] = 'listStragglers';
$wgHooks['EditFilter'][] = 'preventUnauthorizedTransclusionsOnSave';
$wgHooks['ParserBeforeStrip'][] = 'preventUnauthorizedTransclusionOnPreview';
$wgHooks['ParserAfterTidy'][] = 'checkPublicSections';
$wgHooks['UserGetRights'][] = 'GrandAccess::setupGrandAccess';
$wgHooks['UserGetRights'][] = 'GrandAccess::changeGroups';
$wgHooks['isValidEmailAddr'][] = 'isValidEmailAddr';
$wgHooks['UserSetCookies'][] = 'userSetCookies';

//$wgHooks['WatchArticle'][] = 'preventUnauthorizedWatching'; //This doesn't work anyway.  Users can still add pages to their watchlist through the raw editor.

if ($egAnProtectUploads){
  require "UploadProtection.php";
  $wgHooks['UploadForm:initial'][] = 'UploadProtection::buildUploadForm';
  $wgHooks['UploadForm:BeforeProcessing'][] = 'UploadProtection::storeNamespace';
  $wgHooks['UploadComplete'][] = 'UploadProtection::buildUploadDBEntry';
  $wgHooks['ArticleViewHeader'][] = 'UploadProtection::addNsInfoToImagePage';
  $wgHooks['UploadVerification'][] = 'UploadProtection::preventUnauthorizedOverwrite';
 }

define("EX_ACCESS_CONTROLS", true);

//Disable parser and page caches for maximum security
$wgEnableParserCache = false;
$wgCachePages = false;

$dir = dirname(__FILE__) . '/';
$wgAutoloadClasses['ProtectedRSSFeed'] = $dir.'ProtectedRSSFeed.php';
$wgAutoloadClasses['ProtectedAtomFeed'] = $dir.'ProtectedAtomFeed.php';
$wgFeedCacheTimeout = 0; //This is slow, but the only way to make sure people don't get the cached feed generated by more privilidged people
$wgFeedClasses = array(
		       'rss' => 'ProtectedRSSFeed',
		       'atom' => 'ProtectedAtomFeed',
		       );
/* Uncomment the following code to completely disable feeds */
/* $wgFeed = false;
$wgFeedClasses = array();
*/

$wgExtensionCredits['specialpage'][] = array(
				       'name' => 'AccessControls',
				       'author' =>'UofA: SERL', 
				       //'url' => 'http://www.mediawiki.org/wiki/User:JDoe', 
				       'description' => 'Limits access to pages based on membership in namespaces.'
				       );
				       
function permissionError(){
    global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle;
    if($wgTitle == null){
        // Depending on when this function is called, the title may not be created yet, so make an empty one
        $wgTitle = new Title();
    }
    $wgOut->setPageTitle("Permission error");
    $wgOut->addHTML("<p>You are not allowed to execute the action you have requested.</p>");
    if(!$wgUser->isLoggedIn()){
        $wgOut->addHTML("<p>Login to your account or return to <a href='$wgServer$wgScriptPath/index.php/Main_Page'>Main Page</a>.</p>");
    }
    else{
        $wgOut->addHTML("<p>Return to <a href='$wgServer$wgScriptPath/index.php/Main_Page'>Main Page</a>.</p>");
    }
    $wgOut->output();
    $wgOut->disable();
    exit;
}

function isValidEmailAddr($addr, &$result){
    $result = filter_var(unaccentChars($addr), FILTER_VALIDATE_EMAIL);
    return false;
}

function userSetCookies($user, $session, $cookies){
    global $wgCookiePrefix;
    foreach($cookies as $name => $value){
        $_COOKIE[$wgCookiePrefix . $name] = $value;
    }
    return true;
}
?>
