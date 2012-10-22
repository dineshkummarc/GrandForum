<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['EmptyEmailList'] = 'EmptyEmailList'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['EmptyEmailList'] = $dir . 'EmptyEmailList.i18n.php';
$wgSpecialPageGroups['EmptyEmailList'] = 'other-tools';

function runEmptyEmailList($par) {
  EmptyEmailList::run($par);
}

class EmptyEmailList extends SpecialPage{

	function EmptyEmailList() {
		wfLoadExtensionMessages('EmptyEmailList');
		SpecialPage::SpecialPage("EmptyEmailList", CNI.'+', true, 'runEmptyEmailList');
	}

	function run($par){
		global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle;
	    $wgOut->addHTML("<table class='wikitable sortable' bgcolor='#aaaaaa' cellspacing='1' cellpadding='2' style='text-align:center;'>
<tr bgcolor='#F2F2F2'><th>Last Name</th><th>First Name</th><th>Type</th><th>Email</th></tr>");
        foreach(Person::getAllPeople('all') as $person){
            if(($person->getEmail() == "" || $person->getEmail() == "grand-support@forum.grand-nce.ca" || $person->getEmail() == "support@forum.grand-nce.ca") && 
               ($person->isRole(HQP) || $person->isRole(PNI) || $person->isRole(CNI))){
                $names = explode(".", $person->getName());
                $wgOut->addHTML("<tr bgcolor='#FFFFFF'>
                                    <td align='left'>
                                        <a href='{$person->getUrl()}'>{$names[1]}</a>
                                    </td>
                                    <td align='left'>
                                        <a href='{$person->getUrl()}'>{$names[0]}</a>
                                    </td>
                                    <td>{$person->getType()}</td>
                                    <td>{$person->getEmail()}</td>
                                 </tr>");
            }
        }
	    $wgOut->addHTML("</table>");                 
	}
}
?>
