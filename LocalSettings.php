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
error_reporting(E_ALL);
ini_set("display_errors", 1);

if( defined( 'MW_INSTALL_PATH' ) ) {
	$IP = MW_INSTALL_PATH;
} else {
	$IP = dirname( __FILE__ );
}

$path = array( $IP, "$IP/includes", "$IP/languages" );
set_include_path( implode( PATH_SEPARATOR, $path ) . PATH_SEPARATOR . get_include_path() );

require_once( "$IP/includes/DefaultSettings.php" );
require_once( "$IP/Credentials.php" );
require_once( "$IP/Path.php" );


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

## Please edit Path.php for configuring $wgScriptPath
#$wgSitename         = "Grand Forum (nabinger)";
#$wgScriptPath       = "/~nabinger/grand_forum/";
$wgScriptExtension  = ".php";

## UPO means: this is also a user preference option

$wgEnableEmail      = true;
$wgEnableUserEmail  = true; # UPO

$wgEmergencyContact = "support@forum";
$wgPasswordSender = "support@forum";

$wgEnotifUserTalk = true; # UPO
$wgEnotifWatchlist = true; # UPO
$wgEmailAuthentication = true;

## Database settings
#$wgDBtype           = "";
#$wgDBserver         = "";
#$wgDBname           = "";

## Please edit Credentials.php for configuring $wgDBuser, $wgDBpassword,
## $wgDBadminuser, and $wgDBadminpassword.
#$wgDBuser           = "";
#$wgDBpassword       = "";
#
#$wgDBadminuser      = "";
#$wgDBadminpassword  = "";

# MySQL specific settings
$wgDBprefix         = "mw_";

# MySQL table options to use during installation or update
$wgDBTableOptions   = "ENGINE=InnoDB, DEFAULT CHARSET=binary";

# Experimental charset support for MySQL 4.1/5.0.
$wgDBmysql5 = true;

## Shared memory settings
$wgMainCacheType = CACHE_NONE;
$wgMemCachedServers = array();

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads       = true;
$wgUseImageMagick = true;
$wgImageMagickConvertCommand = "/usr/bin/convert";

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

$wgLocalTZoffset = date_default_timezone_set("MST") / 60;
$wgRawHtml = true;
$wgEnableUploads       = true;
$wgMaxUploadSize = 1024*1024*20;
ini_set('upload_max_filesize', $wgMaxUploadSize);
$wgFileExtensions = array( 'png', 'jpg', 'jpeg', 'gif', 'tif', 'tiff', 'svg', 'psd', 'pdf', 'ppt', 'pptx', 'tgz', 'zip', 'rar', 'flv', 'mov', 'avi', 'mpeg', 'ogv', 'mp4', 'mkv', 'm4v', 'mp3', 'flac', 'ogg', 'wmv', 'wav', 'txt');
$wgVerifyMimeType = false;
$wgAllowCopyUploads = true;
$wgAllowTitlesInSVG = true;
$wgMaxShellMemory = 402400;
$key = array_search("application/zip", $wgMimeTypeBlacklist);
$wgPasswordReminderResendTime = 0.5;
$wgImpersonating = false;
$wgRealUser;

if($key !== false){
	unset($wgMimeTypeBlacklist[$key]);
}

// Whether or not to show custom deprication notices
define("DEBUG", false);

//Define the switch to prevent any editing by Users to indicate the end of reporting period.
define("FROZEN", false);

// Names of User Roles
define("INACTIVE", "Inactive"); // This is an implied role.
define("HQP", "HQP");
define("EXTERNAL", "External");
define("ISAC", "ISAC");
define("CNI", "CNI");
define("PNI", "PNI");
define("AR", "Associated Researcher");
define("LOI", "LOI Member");
define("COPL", "Project Co-Leader"); // This is a special role.
define("PL", "Project Leader"); // This is a special role.
define("PM", "Project Manager"); // This is a special role.
define("COTL", "Theme Co-Leader"); // This is a special role.
define("TL", "Theme Leader"); // This is a special role.
define("RMC", "RMC");
define("EVALUATOR", "Evaluator");
define("BOD", "BOD");
define("CHAMP", "Champion");
define("GOV", "Gov");
define("STAFF", "Staff");
define("MANAGER", "Manager");
#define("PNIA", "PNI-Admin");

$wgRoleValues = array(INACTIVE => 0,
                      HQP => 1,
                      EXTERNAL => 2,
                      ISAC => 3,
                      CNI => 5,
                      PNI => 6,
                      AR => 7,
                      LOI => 7,
                      COPL => 8,
                      'COPL' => 8,
                      PL => 9,
                      'PL' => 9,
                      PM => 10,
                      'PM' => 10,
                      COTL => 11,
                      TL => 11,
                      RMC => 12,
                      EVALUATOR => 12,
                      BOD => 12,
                      CHAMP => 12,
                      GOV => 13,
                      STAFF => 16,
                      MANAGER => 17);
                     
$wgRoles = array(HQP, EXTERNAL, ISAC, CNI, PNI, AR, LOI, RMC, BOD, CHAMP, GOV, STAFF, MANAGER);

// Defining Custom Namespace Constants
define("NS_GRAND_PROJ", 122);
define("NS_GRAND_PROJ_TALK", 123);
define("NS_GRAND_NI", 124);
define("NS_GRAND_NI_TALK", 125);
define("NS_GRAND_CR", 126);
define("NS_GRAND_CR_TALK", 127);
define("NS_STUDENT", 128);
define("NS_STUDENT_TALK", 129);
define("NS_STUDENT_COMM", 206);
define("NS_PAPER", 216);    # David's: 276
define("NS_BOOK", 218);        # David's: 278
define("NS_POSTER", 134);
