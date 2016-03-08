<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['ApproveStory'] = 'ApproveStory'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['ApproveStory'] = $dir . 'ApproveStory.i18n.php';

require_once("ApproveStoryAdmin.php");

function runApproveStory($par) {
  ApproveStory::execute($par);
}

class ApproveStory extends SpecialPage{

    function ApproveStory() {
            parent::__construct("ApproveStory", STAFF.'+', true);
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
        $user = Person::newFromId($wgUser->getId());
        if(isset($_GET['action']) && $_GET['action'] == "view" && $user->isRoleAtLeast(STAFF)){
            if(isset($_POST['submit']) && $_POST['submit'] == "Accept"){
                $result = APIRequest::doAction('ApproveStory', false);
            }
            ApproveStory::generateViewHTML($wgOut);
        }
        else{
	    permissionError();
        }
    }
    
    function generateViewHTML($wgOut){
        global $wgScriptPath, $wgServer, $config, $wgEnableEmail;
        $wgOut->addHTML("<table id='requests' style='display:none;background:#ffffff;text-align:center;' cellspacing='1' cellpadding='3' frame='box' rules='all'>
                        <thead><tr bgcolor='#F2F2F2'>
                            <th>Requesting User</th>
                            <th>Timestamp</th>
                            <th>Story Title</th>
                            <th>Action</th>
                        </tr></thead><tbody>\n");
   //for loop adding here 
        $requests = Story::getAllUnapprovedStories();
        foreach($requests as $request){
            $req_user = $request->getUser();
            $wgOut->addHTML("<tr><form action='$wgServer$wgScriptPath/index.php/Special:ApproveStory?action=view' method='post'>
                        <td align='left'>
                            <a target='_blank' href='{$req_user->getUrl()}'><b>{$req_user->getName()}</b></a>
                        </td>");
            $wgOut->addHTML("<td>".str_replace(" ", "<br />", $request->getDateSubmitted())."</td>");
            $wgOut->addHTML("<td align='right'><a target='_blank' href='{$request->getUrl()}'>{$request->getTitle()}</a></td>
			        <input type='hidden' name='id' value='{$request->getId()}' />");
            $wgOut->addHTML("<td><input type='submit' name='submit' value='Accept' /></td>");
            $wgOut->addHTML("</form>
                    </tr>");
        }
        $wgOut->addHTML("</tbody></table><script type='text/javascript'>
                                            $('#requests').dataTable({'autoWidth': false}).fnSort([[2,'desc']]);
                                            $('#requests').css('display', 'table');
                                         </script>");
    }

}
?>
