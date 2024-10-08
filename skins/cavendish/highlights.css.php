<?php

header('Content-Type: text/css');
if(file_exists("../../test.tmp")){
    define("TESTING", true);
}
else{
    define("TESTING", false);
}
require_once("../../config/Config.php");

$ti = $config->getValue("topInverted");
$si = $config->getValue("sideInverted");
$th = $config->getValue("topHeaderColor");
$sc = $config->getValue("sideColor");
$hl = $config->getValue("highlightColor");
$hlFontColor = $config->getValue("highlightFontColor");
$hlc = $config->getValue("hyperlinkColor");
$bc = $config->getValue("mainBorderColor");
$hc = $config->getValue("headerColor");
$iconPath = $config->getValue("iconPath");
$iconPathHighlighted = $config->getValue("iconPathHighlighted");


echo <<<EOF

/* General */

.highlights-text {
    color: $hl !important;
}

.highlights-text-hover:hover {
    color: $hl !important;
}

.highlights-background {
    background: $hl !important;
    color: $hlFontColor !important;
}

.highlights-background-hover:hover {
    background: $hl !important;
    color: $hlFontColor !important;
}

h2, h3, h4, h5, h6, h7 {
    color: $hc !important;
}

h1 {
    color: $th !important;
}

/* Layout */

#topheader {
    background: $th;
    border-bottom: 1px solid $th;
}

#outerHeader {
    background: $sc;
}

#header {
    background: $sc;
}

#submenu, #submenu li:not(.action) {
    background: $sc !important;
}

#submenu ul a, #header ul a {
    color: #FFFFFF;
}

#submenu li.action a {
    color: $sc !important;
}

#submenu li.action a:hover {
    color: #FFFFFF !important;
    background: $sc;
}

#sideToggle, #allTabs {
    background: $sc;
    color: #FFFFFF;
    border-top: 3px solid $sc;
}

#sideToggle:hover, #allTabs:hover {
    color: $hl;
    background: $sc;
    border-top: 3px solid $hl;
}

#side, #nav, #sideFooter {
    background: $sc;
    color: #ccc;
}

#sideFooter a {
    color: $hl;
}

#bodyContent {
    border-color: $bc;
}

#globalSearchResults {
    border-color: $th;
}

#showMoreResults:hover {
    color: $hlc !important;
}

.selected .highlights-tab, .highlights-tab:hover {
    border-width: 3px 0 0 0;
    border-style: solid;
    border-color: $hl;
    color: $hl !important;
}

#allTabsDropdown a:hover {
    color: $th !important;
}

/* Input */

input:focus, textarea:focus {
    outline: none;
    border: 1px solid $hlc !important;
}

.tagit-new input:focus, input.dark:focus, input#globalSearchInput:focus {
    border: none !important;
}

input[type=button], input[type=submit], .button, .button:visited, .dt-button, .ui-button, .button:link , :not(.mce-btn):not(.mce-window-head) > button:not(#cboxPrevious):not(#cboxNext):not(#cboxSlideshow):not(#cboxClose):not([disabled]) {
    color:#606060 !important;
    fill:#606060 !important;
}

input[type=button]:hover, input[type=submit]:hover, .button:hover,  .dt-button:hover, .ui-button:hover, :not(.mce-btn):not(.mce-window-head) > button:not(#cboxPrevious):not(#cboxNext):not(#cboxSlideshow):not(#cboxClose):not([disabled]):hover {
    color: $hlc !important;
    fill: $hlc !important;
}

input[type=button]:active, input[type=submit]:active, .button:active, .dt-button, .ui-button:active, .ui-state button:not(#cboxPrevious):not(#cboxNext):not(#cboxSlideshow):not(#cboxClose):not([disabled]):active {
    color: $hlc !important;
    fill: $hlc !important;
}

input[disabled] , input[disabled]:hover , input[disabled]:active, select[disabled], button[disabled], a.disabledButton, a.disabledButton:hover, a.disabledButton:active {
    color:#606060 !important;
    fill:#606060 !important;
}

.ui-widget-header a .ui-icon {
    background-image: url(../smoothness/images/ui-icons_ffffff_256x240.png);
}

.ui-widget-header a:hover .ui-icon {
    background-image: url(../smoothness/images/ui-icons_222222_256x240.png);
}

/* Icons */
.edit-icon {
    cursor: pointer;
    display: inline-block;
    vertical-align: bottom;
    width: 20px;
    height: 17px;
    margin-left: 1px;
    margin-right:1px;
    background: url("../../{$iconPath}glyphicons_150_edit_small.png");
}

.edit-icon:hover {
    background: url("../../{$iconPathHighlighted}glyphicons_150_edit_small.png");
}

.copy-icon {
    cursor: pointer;
    display: inline-block;
    vertical-align: bottom;
    width: 17px;
    height: 17px;
    margin-left: 1px;
    margin-right:1px;
    background: url("../../{$iconPath}glyphicons_154_more_windows.png");
}

.copy-icon:hover {
    background: url("../../{$iconPathHighlighted}glyphicons_154_more_windows.png");
}

.delete-icon {
    cursor: pointer;
    display: inline-block;
    vertical-align: bottom;
    width: 16px;
    height: 16px;
    margin-left: 1px;
    margin-right:1px;
    background: url("../../{$iconPath}glyphicons_207_remove_2.png");
}

.delete-icon:hover {
    background: url("../../{$iconPathHighlighted}glyphicons_207_remove_2.png");
}

/* JQuery UI */

.ui-tabs .ui-tabs-nav li.ui-tabs-selected a{
	color: $hlc;
}

.ui-datepicker {
    border: 1px solid $th !important;
}

.ui-progressbar .ui-progressbar-value {
    background: $th;
}

.ui-tabs .ui-tabs-nav li:hover:not(.ui-state-disabled) a{
    color: $hlc;
}

.ui-widget-content a {
    color: $hlc;
}

.ui-dialog .ui-dialog-titlebar {
	background: $th;
}

li.ui-menu-item:hover > a, li.ui-menu-item > a#ui-active-menuitem, li.ui-menu-item > a.ui-state-focus {
    background: $hlc !important;
}

/* Other */

.purpleInfo a, .purpleInfo a:visited, .inlinePurpleInfo a, .inlinePurpleInfo a:visited {
    color: $hl;
}

.small_card:hover, .small_card_hover {
    background: $hlc !important;
    color: #FFFFFF !important;
}

.qtip-light .qtip-content a:hover {
    color: $hlc;
    text-decoration: none;
}

ul.tagit li.tagit-choice {
    background: white !important;
    color: $hlc !important;
    border-color: $hlc !important;
}

ul.tagit li.tagit-choice .tagit-label:not(a) {
    color: $hlc;
}

ul.tagit li.tagit-choice .tagit-close .text-icon {
    color: $hlc;
}

ul.tagit li.tagit-choice.remove {
    background: #DDDDDD !important;
}

#bodyContent a.extiw,#bodyContent a.extiw:active {
    color: $hlc;
    background: none;
    padding: 0;
}

#bodyContent a.external {
    color: $hlc;
}

a {
    color: $hlc;
}

a:hover {
    color: $hlc;
}

a:visited {
    color: $hlc;
}

a:active {
    color: $hlc;
}

.clicktooltip:hover {
    color: $hl;
}

/* Carousel */

.carouselPrev, .carouselNext {
    background: $sc;
}

.carouselPrev:hover, .carouselNext:hover {
    background: $th;
}

EOF;

if($config->getValue("topInverted")){
    echo <<<EOF
    
    #topheader {
        background: #FFFFFF;
        color: {$ti};
        border-bottom: 1px solid rgba(0,0,0,0.10);
    }
    
    #topheader a {
        color: {$ti};
    }
    
    #globalSearchInput {
        background: {$ti};
        color: #FFFFFF;
    }
    
    #globalSearchThrobber {
        background: {$ti};
        color: #FFFFFF;
    }
    
    #globalSearchButton {
        background: {$ti} !important;
        color: #FFFFFF;
    }
    
EOF;
}

if($config->getValue("sideInverted")){
    echo <<<EOF
    #submenu ul a, #header ul a {
        color: {$hl};
    }
    
    #side, #nav, #sideFooter {
        color: #888888;
    }
    
    #sideToggle, #allTabs {
        color: {$hl};
    }
    
    #nav li a {
        color: {$hl};
    }
    
    #nav li > span {
        color: {$si} !important;
    }
    
    #submenu li.action a {
        color: {$hl} !important;
    }
    
    #submenu li.action a:hover {
        color: #FFFFFF;
        background: {$hl};
    }
    
    input.dark {
        border: 1px solid #CCC;
    }
    
    input.dark:focus {
        border: 1px solid {$hl} !important;
    }
EOF;
}

?>
