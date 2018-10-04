<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['EditMember'] = 'EditMember'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['EditMember'] = $dir . 'EditMember.i18n.php';
$wgSpecialPageGroups['EditMember'] = 'network-tools';

$wgHooks['ToolboxLinks'][] = 'EditMember::createToolboxLinks';

function runEditMember($par) {
  EditMember::execute($par);
}

class EditMember extends SpecialPage{

    function EditMember() {
        SpecialPage::__construct("EditMember", STAFF.'+', true, 'runEditMember');
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage, $config;
        $me = Person::newFromWgUser();
        $date = date("Y-m-d");
        $wgOut->addScript("<script type='text/javascript'>
                            $(document).ready(function(){
                                $('#tabs').tabs({
                                    cookie: {
                                        expires: 1
                                    }
                                });
                            });
                           </script>");
        if(!isset($_POST['submit'])){
            // Form not entered yet
            if(isset($_GET['next']) || isset($_POST['next']) || isset($_GET['name'])){
                if(!isset($_GET['next']) && isset($_POST['next'])){
                    $_GET['next'] = $_POST['next'];
                }
                if(!isset($_GET['name']) && isset($_POST['name'])){
                    $_GET['name'] = $_POST['name'];
                }
                $person = @Person::newFromName($_GET['name']);
                $roles = $person->getRoles();
                if(!isset($_GET['name'])){
                    $wgMessage->addError("A user was not provided.");
                    EditMember::generateMain();
                    return;
                }
                else if(!$me->isAllowedToEdit($person)){ // Handles RMC-GOV
                    $wgMessage->addError("You do not have permissions to edit this user.");
                    EditMember::generateMain();
                    return;
                }
                EditMember::generateEditMemberFormHTML($wgOut);
            }
            else{
                EditMember::generateMain();
            }
        }
        else{
            // The Form has been entered
            $person = @Person::newFromName(str_replace(" ", ".", $_POST['name']));

            $me = Person::newFromId($wgUser->getId());

            $wgOut->addHTML("<a href='$wgServer$wgScriptPath/index.php/Special:EditMember'>Click Here</a> to continue Editing Members.");
            
            // Theme Leadership Changes
            $tl = array();
            if(isset($_POST['tl'])){
                foreach($_POST['tl'] as $value){
                    $tl[$value] = Theme::newFromId($value);
                }
            }
        
            $currentTL = array();
            // Removing Theme Leaders
            foreach($person->getLeadThemes() as $theme){
                if(!isset($tl[$theme->getId()])){
                    // Remove Theme Leadership
                    $_POST['coordinator'] = 'False';
                    $_POST['co_lead'] = 'False';
                    $_POST['theme'] = $theme->getId();
                    $_POST['name'] = $person->getName();
                    $_POST['comment'] = @str_replace("'", "", $_POST["tl_comment"][$theme->getId()]);
                    $_POST['effective_date'] = $_POST["tl_datepicker"][$theme->getId()];
                    APIRequest::doAction('DeleteThemeLeader', true);
                    $wgMessage->addSuccess("<b>{$person->getReversedName()}</b> is no longer a ".strtolower($config->getValue('roleDefs', TL))." of {$theme->getAcronym()}");
                }
                $currentTL[$theme->getId()] = $theme->getId();
            }
            
            // Adding Theme Leaders
            foreach($tl as $theme){
                if(!isset($currentTL[$theme->getId()])){
                    // Add Theme Leadership
                    $_POST['coordinator'] = 'False';
                    $_POST['co_lead'] = 'False';
                    $_POST['theme'] = $theme->getId();
                    $_POST['name'] = $person->getName();
                    APIRequest::doAction('AddThemeLeader', true);
                    $wgMessage->addSuccess("<b>{$person->getReversedName()}</b> is now a ".strtolower($config->getValue('roleDefs', TL))." of {$theme->getAcronym()}");
                }
            }
            
            // Theme Coordinator Changes
            $tc = array();
            if(isset($_POST['tc'])){
                foreach($_POST['tc'] as $value){
                    $tc[$value] = Theme::newFromId($value);
                }
            }
        
            $currentTC = array();
            // Removing Theme Coordinators
            foreach($person->getCoordThemes() as $theme){
                if(!isset($tc[$theme->getId()])){
                    // Remove Theme Coordinator
                    $_POST['coordinator'] = 'True';
                    $_POST['co_lead'] = 'False';
                    $_POST['theme'] = $theme->getId();
                    $_POST['name'] = $person->getName();
                    $_POST['comment'] = @str_replace("'", "", $_POST["tc_comment"][$theme->getId()]);
                    $_POST['effective_date'] = @$_POST["tc_datepicker"][$theme->getId()];
                    APIRequest::doAction('DeleteThemeLeader', true);
                    $wgMessage->addSuccess("<b>{$person->getReversedName()}</b> is no longer a ".strtolower($config->getValue('roleDefs', TC))." of {$theme->getAcronym()}");
                }
                $currentTC[$theme->getId()] = $theme->getId();
            }
            
            // Adding Theme Coordinators
            foreach($tc as $theme){
                if(!isset($currentTC[$theme->getId()])){
                    // Add Theme Coordinator
                    $_POST['coordinator'] = 'True';
                    $_POST['co_lead'] = 'False';
                    $_POST['theme'] = $theme->getId();
                    $_POST['name'] = $person->getName();
                    APIRequest::doAction('AddThemeLeader', true);
                    $wgMessage->addSuccess("<b>{$person->getReversedName()}</b> is now a ".strtolower($config->getValue('roleDefs', TC))." of {$theme->getAcronym()}");
                }
            }
        }
    }
    
    function generateMain(){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle;
        $me = Person::newFromWgUser();
        $allPeople = Person::getAllCandidates('all');
        $i = 0;
        $names = array();
        foreach($allPeople as $person){
            if(!$me->isAllowedToEdit($person)){ 
                // User does not have permission for this person
                continue;
            }
            $names[] = $person->getName();
        }
        
        $wgOut->addHTML("This page can be used to edit the roles and projects of members on the forum.<br />
                         Select a user from the list below, and then click the 'Next' button.<table>
                            <tr><td>
                            <form action='$wgServer$wgScriptPath/index.php/Special:EditMember' method='post'>
                                <select data-placeholder='Choose a Person...' id='names' name='name' size='10' style='width:100%'>");
        foreach($allPeople as $person){
            if(!$me->isAllowedToEdit($person)){
                // User does not have permission for this person
                continue;           
            }
            $wgOut->addHTML("<option value=\"{$person->getName()}\">{$person->getNameForForms()}</option>\n");
        }
        $wgOut->addHTML("</select>
                </td></tr>
                <tr><td>
            <input id='button' type='submit' name='next' value='Next' disabled='disabled' />
        </form></td></tr></table>
        <script type='text/javascript'>
	        $('#names').chosen();
	        $(document).ready(function(){
	            $('#names').change(function(){
	                var page = $('#names').val();
	                if(page != ''){
	                    $('#button').prop('disabled', false);
	                }
	            });
	        });
	    </script>");
    }
    
    function generateEditMemberFormHTML($wgOut){
        global $wgServer, $wgScriptPath, $wgUser, $config;
        $me = Person::newFromId($wgUser->getId());
        $person = Person::newFromName(str_replace(" ", ".", $_GET['name']));
        $wgOut->addHTML("<form id='editMember' action='$wgServer$wgScriptPath/index.php/Special:EditMember?project' method='post'>
        <p>Select the Roles and Projects to which <b>{$person->getNameForForms()}</b> should be a member of.  Deselecting a role or project will prompt further questions, relating to the reason why they are leaving that role.</p>");
        $wgOut->addHTML("<div id='tabs'>
                    <ul>
                        <li><a id='ThemesTab' href='#tabs-3'>{$config->getValue('projectThemes')} Leaders</a></li>");
        $wgOut->addHTML("
                    </ul>");

        $wgOut->addHTML("<div id='tabs-3'>");
                            EditMember::generateTLFormHTML($wgOut);
        $wgOut->addHTML("</div>");

        $wgOut->addHTML("</div>");
        $wgOut->addHTML("<br />
                         <input type='hidden' name='name' value='{$_GET['name']}' />
                         <input type='submit' name='submit' value='Submit Request' onSubmit />
                         </form>");
    }
    
    function generateTLFormHTML($wgOut){
        global $wgUser, $wgServer, $wgScriptPath, $config;
        if(!isset($_GET['name'])){
            return;
        }
        $person = Person::newFromName(str_replace(" ", ".", $_GET['name']));
        $wgOut->addHTML("<h2>{$config->getValue('roleDefs', TL)}</h2>");
        $wgOut->addHTML("<table border='0' cellspacing='2'>");
        $leadThemes = $person->getLeadThemes();
        $themes = Theme::getAllThemes(PROJECT_PHASE);
        foreach($themes as $theme){
            $themeId = $theme->getId();
            $isLead = false;
            foreach($leadThemes as $t){
                if($t->getId() == $themeId){
                    $isLead = true;
                    break;
                }
            }
            if($isLead){
                $wgOut->addHTML("<tr><td style='min-width:150px;' valign='top'><input type='checkbox' name='tl[]' value='$themeId' checked='checked' class='already' onChange='addComment(this, false);' />{$theme->getAcronym()}<div style='display:none; padding-left:30px;'><fieldset><legend>Reasoning</legend><p>Date Effective:<input type='text' class='datepicker' id='tl_datepicker{$themeId}' name='tl_datepicker[$themeId]' /></p>Additional Comments:<br /><textarea name='tl_comment[$themeId]' cols='15' rows='4' style='height:auto;'></textarea></fielset></div><br /></td></tr>\n");
            }
            else {
                $wgOut->addHTML("<tr><td style='min-width:150px;' valign='top'><input type='checkbox' name='tl[]' value='$themeId' />{$theme->getAcronym()}</td></tr>\n");
            }
        }
        $wgOut->addHTML("</table>");
        $wgOut->addHTML("<h2>{$config->getValue('roleDefs', TC)}</h2>");
        $wgOut->addHTML("<table border='0' cellspacing='2'>");
        $coordThemes = $person->getCoordThemes();
        $themes = Theme::getAllThemes(PROJECT_PHASE);
        foreach($themes as $theme){
            $themeId = $theme->getId();
            $isLead = false;
            foreach($coordThemes as $t){
                if($t->getId() == $themeId){
                    $isLead = true;
                    break;
                }
            }
            if($isLead){
                $wgOut->addHTML("<tr><td style='min-width:150px;' valign='top'><input type='checkbox' name='tc[]' value='$themeId' checked='checked' class='already' onChange='addComment(this, false);' />{$theme->getAcronym()}<div style='display:none; padding-left:30px;'><fieldset><legend>Reasoning</legend><p>Date Effective:<input type='text' class='datepicker' id='tc_datepicker{$themeId}' name='tc_datepicker[$themeId]' /></p>Additional Comments:<br /><textarea name='tc_comment[$themeId]' cols='15' rows='4' style='height:auto;'></textarea></fielset></div><br /></td></tr>\n");
            }
            else {
                $wgOut->addHTML("<tr><td style='min-width:150px;' valign='top'><input type='checkbox' name='tc[]' value='$themeId' />{$theme->getAcronym()}</td></tr>\n");
            }
        }
        $wgOut->addHTML("</table>");
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        if($me->isRoleAtLeast(STAFF)){
            $toolbox['People']['links'][1] = TabUtils::createToolboxLink("Edit Roles", "$wgServer$wgScriptPath/index.php/Special:EditMember");
        }
        return true;
    }
}

?>
