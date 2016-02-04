<?php

# This file was automatically generated by the MediaWiki installer.
# If you make manual changes, please keep track in case you need to
# recreate them later.
#
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# http://www.mediawiki.org/wiki/Manual:Configuration_settings

# If you customize your file layout, set $IP to the directory that contains
# the other MediaWiki files. It will be used as a base to locate files.
if(PHP_SAPI != 'cli'){
    session_start();
    if(phpversion() < 5.4){
        error_reporting(E_ALL);
    }
    else{
        error_reporting(E_ALL ^ E_STRICT);
    }
    ini_set("display_errors", 1);

    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
}

date_default_timezone_set('America/Edmonton');
if( defined( 'MW_INSTALL_PATH' ) ) {
	$IP = MW_INSTALL_PATH;
} else {
	$IP = dirname( __FILE__ );
}

if(!defined('TESTING')){
    if(file_exists("$IP/test.tmp")){
        define("TESTING", true);
    }
    else{
        define("TESTING", false);
    }
}

$path = array( $IP, "$IP/includes", "$IP/languages" );
set_include_path( implode( PATH_SEPARATOR, $path ) . PATH_SEPARATOR . get_include_path() );

require_once( "$IP/includes/DefaultSettings.php" );
require_once( "$IP/config/Config.php" );
require_once( "$IP/Classes/Inflect/Inflect.php" );

## Path settings
$wgSitename         = $config->getValue("siteName");
$wgScriptPath       = $config->getValue("path");

## Database settings
$wgDBtype           = $config->getValue("dbType");
$wgDBserver         = $config->getValue("dbServer");
$wgDBname           = $config->getValue("dbName");
$wgTestDBname       = $config->getValue("dbTestName");

## Database credentials
$wgDBuser           = $config->getValue("dbUser");
$wgDBpassword       = $config->getValue("dbPassword");

$wgDBadminuser           = $config->getValue("dbUser");
$wgDBadminpassword       = $config->getValue("dbPassword");

## From MediaWiki manual:
##   "This value is used to generate a persistent cookie
##    for authentication that is resilient to spoofing."
$wgSecretKey = "7b32642dd51dcddf7a65fa3bea2757256caebc0220154c52ec8aebea1b87d7bf";

## Mailing List settings
$wgListAdmins           = $config->getValue("listAdmins");
$wgListAdminPassword    = $config->getValue("listAdminPassword");

$wgFavicon          = "$wgServer$wgScriptPath/favicon.ico";

if(TESTING && !defined('INIT_TESTING')){
    $wgDBname = $wgTestDBname;
}

# If PHP's memory limit is very low, some operations may fail.
//ini_set( 'memory_limit', '20M' );

if ( $wgCommandLineMode ) {
	if ( isset( $_SERVER ) && array_key_exists( 'REQUEST_METHOD', $_SERVER ) ) {
		die( "This script must be run from the command line\n" );
	}
}
## Uncomment this to disable output compression

# $wgDisableOutputCompression = true;

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs please see:
## http://www.mediawiki.org/wiki/Manual:Short_URL

$wgScriptExtension  = ".php";

## UPO means: this is also a user preference option

$wgEnableEmail      = true;
$wgEnableUserEmail  = true; # UPO

$wgEmergencyContact = $config->getValue('supportEmail');
$wgPasswordSender = $config->getValue('supportEmail');

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEmailAuthentication = true;

if(TESTING){
    $wgEnableEmail      = false;
    $wgEnableUserEmail  = false; # UPO
    $wgEmailAuthentication = false;
    $wgEnotifUserTalk = false; # UPO
    $wgEnotifWatchlist = false; # UPO
}

# MySQL specific settings
$wgDBprefix         = "mw_";

# MySQL table options to use during installation or update
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Experimental charset support for MySQL 4.1/5.0.
$wgDBmysql5 = true;

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = array();
$wgDisableCounters = true;
$wgJobRunRate = 0.01;
$wgSessionsInObjectCache = true;
$wgEnableSidebarCache = true;
if($config->getValue('localizationCache') != ""){
    if(!file_exists($config->getValue('localizationCache')) && 
       is_writable($config->getValue('localizationCache'))){
        mkdir($config->getValue('localizationCache'));
    }
    if(file_exists($config->getValue('localizationCache'))){
        $wgCacheDirectory = $config->getValue('localizationCache');
        $wgUseLocalMessageCache = true;
    }
}

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads       = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";
$wgCopyUploadsFromSpecialUpload = true;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "en_US.utf8";

## If you want to use image uploads under safe mode,
## create the directories images/archive, images/thumb and
## images/temp, and make them all writable. Then uncomment
## this, if it's not already uncommented:
# $wgHashedUploadDirectory = false;

## If you have the appropriate support software installed
## you can enable inline LaTeX equations:
$wgUseTeX           = false;

$wgLocalInterwiki   = strtolower( $wgSitename );

$wgLanguageCode = "en";

## Please edit Credentials.php to configure $wgSecretKey.
#$wgSecretKey = "";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'standard', 'nostalgia', 'cologneblue', 'monobook':
require_once "$IP/skins/cavendish/cavendish.php";
$wgDefaultSkin = 'cavendish';
$wgAllowUserSkin = false;

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
# $wgEnableCreativeCommonsRdf = true;
$wgRightsPage = ""; # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = "";
$wgRightsText = "";
$wgRightsIcon = "";
# $wgRightsCode = ""; # Not yet used

$wgDiff3 = "/usr/bin/diff3";

# When you make changes to this configuration file, this will make
# sure that cached pages are cleared.
$wgCacheEpoch = max( $wgCacheEpoch, gmdate( 'YmdHis', @filemtime( __FILE__ ) ) );
/*
require_once("$IP/Swift/lib/swift_required.php");

require_once("$IP/extensions/Widgets/Widgets.php");
$wgGroupPermissions['sysop']['editwidgets'] = true;
*/

require_once("$IP/extensions/AnnokiControl/AnnokiControl.php");

$wgRestrictDisplayTitle = false;
$wgLocalTZoffset = date_default_timezone_set("MST") / 60;
$wgRawHtml = true;
$wgEnableUploads       = true;
$wgMaxUploadSize = 1024*1024*50;
ini_set('upload_max_filesize', $wgMaxUploadSize);
$wgFileExtensions = array( 'png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'svg', 'psd', 'pdf', 'ppt', 'pptx', 'doc', 'docx', 'xls', 'xlsx', 'tgz', 'zip', 'rar', 'flv', 'mov', 'avi', 'mpeg', 'ogv', 'mp4', 'mkv', 'm4v', 'mp3', 'flac', 'ogg', 'wmv', 'wav', 'txt');
$wgVerifyMimeType = false;
$wgAllowCopyUploads = true;
$wgAllowTitlesInSVG = true;
$wgMaxShellMemory = 402400;
$wgPasswordReminderResendTime = 0.1666; // ~ 10 minutes
$wgEditPageFrameOptions = 'SAMEORIGIN';
$wgImpersonating = false;
$wgDelegating = false;
$wgRealUser;

if (($key = array_search('application/zip', $wgMimeTypeBlacklist)) !== false) {
    unset($wgMimeTypeBlacklist[$key]);
}

// Whether or not to show custom deprication notices
define("DEBUG", true);

//Define the switch to prevent any editing by Users to indicate the end of reporting period.
define("FROZEN", false);

$wgRoleValues = array(INACTIVE => 0,
                      HQP => 1,
                      PS => 1,
                      EXTERNAL => 2,
                      NI => 5,
                      AR => 5,
                      CI => 6,
                      CHAMP => 7,
                      PARTNER => 7,
                      PL => 9,
                      'PL' => 9,
                      TL => 11,
                      'TL' => 11,
                      TC => 11,
                      APL => 11,
                      TC => 11,
                      EVALUATOR => 12,
                      ASD => 13,
                      SD => 13,
                      STAFF => 16,
                      MANAGER => 17,
                      ADMIN => 100);

$wgRoles = ($config->hasValue('wgRoles')) ? 
    $config->getValue('wgRoles') : 
    array(HQP, PS, EXTERNAL, AR, CI, CHAMP, PARTNER, ASD, SD, STAFF, MANAGER, ADMIN);

$wgAllRoles = ($config->hasValue('wgAllRoles')) ? 
    $config->getValue('wgAllRoles') :
    array(HQP, PS, STUDENT, EXTERNAL, AR, CI, PL, APL, TL, TC, EVALUATOR, CHAMP, PARTNER, ASD, SD, STAFF, MANAGER, ADMIN);

foreach($config->getValue('committees') as $role => $roleDef){
    define($role, $role);
    
    $wgRoleValues[$role] = 11;
    $wgRoles[] = $role;
    $wgAllRoles[] = $role;
}

$config->setValue('roleDefs', array_merge($config->getValue('roleDefs'), $config->getValue('committees')));

function unaccentChars($str){
    return strtolower(strtr(utf8_decode($str), 
                      utf8_decode('àáâãäåšçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÅŠÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 
                                  'aaaaaasceeeeiiiinooooouuuuyyAAAAAASCEEEEIIIINOOOOOUUUUY'));
}

// Encodes a large json object (usually arrays)
// It still returns a string, but constructs it incrementally
function large_json_encode($data){
    $string = "";
    if(is_object($data)){
        $string .= "{";
    }
    else{
        $string .= "[";
    }
    $first = true;
    foreach($data as $key => $item){
	    if ($first) {
		    $first = false;
	    } else {
		    $string .= ",";
	    }
	    if(is_object($data)){
            $string .= "$key:";
        }
	    $string .= json_encode($item);
    }
    if(is_object($data)){
        $string .= "}";
    }
    else{
        $string .= "]";
    }
    return $string;
}

function array_clean(array $haystack){
    foreach ($haystack as $key => $value) {
        if (is_array($value)) {
            $haystack[$key] = array_clean($value);
        } elseif (is_string($value)) {
            $value = trim($value);
        }

        if (!$value) {
            unset($haystack[$key]);
        }
    }

    return $haystack;
}

function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function str_replace_last($search, $replace, $subject) {
    $pos = strrpos($subject, $search);
    if($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function str_replace_every_other($needle, $replace, $haystack, &$count=null, $replace_first=true) {
    $count = 0;
    $offset = strpos($haystack, $needle);
    //If we don't replace the first, go ahead and skip it
    if (!$replace_first) {
        $offset += strlen($needle);
        $offset = strpos($haystack, $needle, $offset);
    }
    while ($offset !== false) {
        $haystack = substr_replace($haystack, $replace, $offset, strlen($needle));
        $count++;
        $offset += strlen($replace);
        $offset = strpos($haystack, $needle, $offset);
        if ($offset !== false) {
            $offset += strlen($needle);
            $offset = strpos($haystack, $needle, $offset);
        }
    }
    return $haystack;
}

function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

/**
 * Returns a 'human readable' date from the given string
 * @param string $time The time in db timestamp format 'YYYY-MM-DD hh-mm-ss'
 * @param strin $format How to format the data (defaults to 'F j, Y')
 * @return string The date in the format 'F j, Y'
 */
function time2date($time, $format='F j, Y'){
    $strtime = strtotime($time);
    return date($format, $strtime);
}

/**
 * Returns a HTML comment with the elapsed time since request.
 * This method has no side effects.
 * @return string
 */
function wfReportTimeOld() {
	global $wgRequestTime, $wgShowHostnames;

	$now = wfTime();
	$elapsed = $now - $wgRequestTime;
    $mem = memory_get_peak_usage(true);
    $bytes = array(1 => 'B', 2 => 'KiB', 3 => 'MiB', 4 => 'GiB');
    $ind = 1;
    while ($mem > 1024 && $ind < count($bytes)) {
	    $mem = $mem / 1024;
	    $ind++;
    }
	

	return $wgShowHostnames
		? sprintf( "<!-- Served by %s in %01.3f secs (%01.1f %s used). -->", wfHostname(), $elapsed, $mem, $bytes[$ind] )
		: sprintf( "<!-- Served in %01.3f secs (%01.1f %s used). -->", $elapsed, $mem, $bytes[$ind] );
}
