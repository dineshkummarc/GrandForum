<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['ReferAColleague'] = 'ReferAColleague'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['ReferAColleague'] = $dir . 'ReferAColleague.i18n.php';
$wgSpecialPageGroups['ReferAColleague'] = 'network-tools';

$wgHooks['ToolboxLinks'][] = 'ReferAColleague::createToolboxLinks';

class ReferAColleague extends SpecialPage{

    function ReferAColleague() {
        parent::__construct("ReferAColleague", NI.'+', true);
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage, $wgLang;
        $form = $this->createForm();
        if(isset($_POST['submit'])){
            $this->handleSubmit($form);
        }
        $wgOut->addHTML("<form action='$wgServer$wgScriptPath/index.php/Special:ReferAColleague' method='post'>\n");
        $wgOut->addHTML($form->render());
        $wgOut->addHTML("</form>");
    }
    
    function handleSubmit($form){
        global $wgOut, $config, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage, $wgLang, $wgMessage;
        $status = $form->validate();
        if($status){
            // Send Email
            $haders = array();
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=UTF-8';
            $headers[] = "From: {$config->getValue('networkName')} Support <{$config->getValue('supportEmail')}>";
            mail($_POST['email_field'], "Welcome to CAPS", $this->getMessage(), implode("\r\n", $headers));
            $wgMessage->addSuccess("Referral email sent to {$_POST['first_name_field']} {$_POST['last_name_field']} ({$_POST['email_field']})");
            $form->reset();
            redirect("$wgServer$wgScriptPath/index.php/Special:ReferAColleague");
        }
    }
    
    function getMessage(){
        if($_POST['language_field'] == "en"){
            switch($_POST['role_field']){
                case "Physician":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                            <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform!  You are receiving this email because a colleague of yours has referred you.</p>
<p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for potential AND experienced physician medical abortion providers, like yourself. Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources like “Ask an expert” rapid response, “Find a pharmacy” that stocks mifepristone, and a confidential communication platform for mifepristone providers. We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
                case "Pharmacist":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                    <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform!  You are receiving this email because a colleague of yours has referred you.</p>
                    <p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for potential AND experienced pharmacist medical abortion dispensers, like yourself. Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources “Ask an expert” rapid response, “Find a pharmacy” that stocks mifepristone, and a confidential communication platform for mifepristone providers. We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
                case "Facility Staff":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                    <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform! You are receiving this email because a colleague of yours has referred you.</p>
                    <p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for facility staff  like you, who support physician and pharmacist medical abortion providers and dispensers across the country.  Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources like “Ask an expert” rapid response and “Find a pharmacy” that stocks mifepristone.  Specifically for facility staff, we are always adding materials under our Helpful Resources tab which we hope you will take advantage of.  We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
            }
        }
        else if($_POST['language_field'] == "fr"){
            switch($_POST['role_field']){
                case "Physician":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                            <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform!  You are receiving this email because a colleague of yours has referred you.</p>
<p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for potential AND experienced physician medical abortion providers, like yourself. Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources like “Ask an expert” rapid response, “Find a pharmacy” that stocks mifepristone, and a confidential communication platform for mifepristone providers. We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
                case "Pharmacist":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                    <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform!  You are receiving this email because a colleague of yours has referred you.</p>
                    <p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for potential AND experienced pharmacist medical abortion dispensers, like yourself. Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources “Ask an expert” rapid response, “Find a pharmacy” that stocks mifepristone, and a confidential communication platform for mifepristone providers. We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
                case "Facility Staff":
                    return "<p>Hello {$_POST['first_name_field']} {$_POST['last_name_field']}</p>
                    <p>Welcome to The Canadian Abortion Providers Support (CAPS-CPCA) online community platform! You are receiving this email because a colleague of yours has referred you.</p>
                    <p>CAPS-CPCA is hosted by Contraception Access Research Team-Groupe de recherche sur l’accessibilité à la contraception (CART-GRAC) at the University of British Columbia (UBC) and is partnered with:</p>
                    <ul>
                        <li>The College of Family Physicians Canada (CFPC)</li>
                        <li>The Society of Obstetricians and Gynaecologists of Canada (SOGC),</li>
                        <li>The Canadian Pharmacists Association (CPhA)</li>
                    </ul>
                    <p>The CAPS-CPCA online platform provides resources for facility staff  like you, who support physician and pharmacist medical abortion providers and dispensers across the country.  Our website offers informative resources like clinical practice guidelines, current literature and patient resources for medical abortion, as well as interactive and supportive resources like “Ask an expert” rapid response and “Find a pharmacy” that stocks mifepristone.  Specifically for facility staff, we are always adding materials under our Helpful Resources tab which we hope you will take advantage of.  We encourage you to explore the CAPS-CPCA online community today!</p>
                    <p>To find out more, please join our website: <a href='$wgServer$wgScriptPath'>CAPS CPCA Forum</a></p>";
                    break;
            }
        }
    }
    
    function createForm(){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage, $wgLang;
        $formContainer = new FormContainer("form_container");
        $formTable = new FormTable("form_table");
        
        if($wgLang->getCode() == "en"){
            $firstNameLabel = new Label("first_name_label", "First Name", "The first name of the user", VALIDATE_NOT_NULL);
            $firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOT_NULL);
            $firstNameRow = new FormTableRow("first_name_row");
            $firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
            
            $lastNameLabel = new Label("last_name_label", "Last Name", "The last name of the user", VALIDATE_NOT_NULL);
            $lastNameField = new TextField("last_name_field", "Last Name", "", VALIDATE_NOT_NULL);
            $lastNameRow = new FormTableRow("last_name_row");
            $lastNameRow->append($lastNameLabel)->append($lastNameField->attr('size', 20));
            
            $emailLabel = new Label("email_label", "Email", "The email address of the user", VALIDATE_NOT_NULL);
            $emailField = new EmailField("email_field", "Email", "", VALIDATE_NOT_NULL);
            $emailRow = new FormTableRow("email_row");
            $emailRow->append($emailLabel)->append($emailField);

            $roleLabel = new Label("role_label", "Role", "The role of the user", VALIDATE_NOT_NULL);
            $roleField = new SelectBox("role_field", "Role", "", array("Physician" => "Physician", "Pharmacist" => "Pharmacist", "Facility Staff" => "Facility Staff"), VALIDATE_NOT_NULL);
            $roleRow = new FormTableRow("role_row");
            $roleRow->append($roleLabel)->append($roleField);
            
            $languageLabel = new Label("language_label", "Language", "The language of the user", VALIDATE_NOT_NULL);
            $languageField = new SelectBox("language_field", "Language", "", array("en" => "English", "fr" => "French"), VALIDATE_NOT_NULL);
            $languageRow = new FormTableRow("language_row");
            $languageRow->append($languageLabel)->append($languageField);
            
            $submitCell = new EmptyElement();
            $submitField = new SubmitButton("submit", "Send Referral", "Send Referral", VALIDATE_NOTHING);
            $submitRow = new FormTableRow("submit_row");
            $submitRow->append($submitCell)->append($submitField);
            $submitRow->attr("align","right");
        }
        else if($wgLang->getCode() == "fr"){
            $firstNameLabel = new Label("first_name_label", "Prénom", "Le premier nom de l'utilisateur (ne peut pas contenir des espaces)", VALIDATE_NOT_NULL);
            $firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOSPACES);
            $firstNameRow = new FormTableRow("first_name_row");
            $firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
            
            $lastNameLabel = new Label("last_name_label", "Nom de famille", "Le nom de l'utilisateur (ne peut pas contenir des espaces)", VALIDATE_NOT_NULL);
            $lastNameField = new TextField("last_name_field", "Last Name", "", VALIDATE_NOSPACES);
            $lastNameRow = new FormTableRow("last_name_row");
            $lastNameRow->append($lastNameLabel)->append($lastNameField->attr('size', 20));
            
            $emailLabel = new Label("email_label", "Email", "L'adresse email de l'utilisateur", VALIDATE_NOT_NULL);
            $emailField = new EmailField("email_field", "Email", "", VALIDATE_NOT_NULL);
            $emailRow = new FormTableRow("email_row");
            $emailRow->append($emailLabel)->append($emailField);

            $roleLabel = new Label("role_label", "Rôle", "Le rôle de l'utilisateur", VALIDATE_NOT_NULL);
            $roleField = new SelectBox("role_field", "Role", "", array("Physician" => "Médecin", "Pharmacist" => "Pharmacien", "Facility Staff" => "Personnel de l'installation"), VALIDATE_NOT_NULL);
            $roleRow = new FormTableRow("role_row");
            $roleRow->append($roleLabel)->append($roleField);
            
            $languageLabel = new Label("language_label", "La langue", "La langue de l'utilisateur", VALIDATE_NOT_NULL);
            $languageField = new SelectBox("language_field", "Language", "", array("en" => "Englais", "fr" => "Français"), VALIDATE_NOT_NULL);
            $languageRow = new FormTableRow("language_row");
            $languageRow->append($languageLabel)->append($languageField);
            
            $submitCell = new EmptyElement();
            $submitField = new SubmitButton("submit", "Envoyer une Référence", "Envoyer une Référence", VALIDATE_NOTHING);
            $submitRow = new FormTableRow("submit_row");
            $submitRow->append($submitCell)->append($submitField);
            $submitRow->attr("align","right");
        }
        
        $formTable->append($firstNameRow)
                  ->append($lastNameRow)
                  ->append($roleRow)
                  ->append($languageRow)
                  ->append($emailRow)
                  ->append($submitRow);
        
        $formContainer->append($formTable);
        return $formContainer;
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath, $wgLang;
        $me = Person::newFromWgUser();
	    $title = "Refer a Colleague";
	    if($wgLang->getCode() == "fr"){
	        $title = "Référer un Collègue";
	    }
        if($me->isRoleAtLeast(NI)){
            $toolbox['People']['links'][4] = TabUtils::createToolboxLink($title, "$wgServer$wgScriptPath/index.php/Special:ReferAColleague");
        }
        return true;
    }
}

?>
