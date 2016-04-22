<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['CAPSRegister'] = 'CAPSRegister'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['CAPSRegister'] = $dir . 'CAPSRegister.i18n.php';
$wgSpecialPageGroups['CAPSRegister'] = 'network-tools';

$wgHooks['OutputPageParserOutput'][] = 'CAPSRegister::onOutputPageParserOutput';

function runCAPSRegister($par) {
    CAPSRegister::execute($par);
}

class CAPSRegister extends SpecialPage{

    static function onOutputPageParserOutput(&$out, $parseroutput){
        global $wgServer, $wgScriptPath, $config, $wgTitle;
        
        $me = Person::newFromWgUser();
        if($wgTitle->getText() == "Main Page" && $wgTitle->getNsText() == ""){ // Only show on Main Page
            if(!$me->isLoggedIn()){
                $parseroutput->mText .= "<h2>Membership Registration</h2><p>If you would like to apply to become a member in {$config->getValue('networkName')} then please fill out the <a href='$wgServer$wgScriptPath/index.php/Special:CAPSRegister'>registration form</a>.</p>";
            }
            /*else if($me->isRole(HQP.'-Candidate')){
                $parseroutput->mText .= "<h2>HQP Application</h2><p>To apply to become an Affiliate HQP in {$config->getValue('networkName')} then please fill out the <a href='{$me->getUrl()}?tab=hqp-profile'>HQP Application form</a>.</p>";
            }*/
        }
        return true;
    }

    function CAPSRegister() {
        SpecialPage::__construct("CAPSRegister", null, false, 'runCAPSRegister');
    }
    
    function userCanExecute($user){
        $person = Person::newFromUser($user);
        return !$person->isLoggedIn();
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
        if(!isset($_POST['submit'])){
            CAPSRegister::generateFormHTML($wgOut);
        }
        else{
            CAPSRegister::handleSubmit($wgOut);
            return;
        }
    }
    
    function createForm(){
        $formContainer = new FormContainer("form_container");
        $formTable = new FormTable("form_table");
        
        $firstNameLabel = new Label("first_name_label", "First Name", "The first name of the user (cannot contain spaces)", VALIDATE_NOT_NULL);
        $firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOSPACES);
        $firstNameRow = new FormTableRow("first_name_row");
        $firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
        
        $lastNameLabel = new Label("last_name_label", "Last Name", "The last name of the user (cannot contain spaces)", VALIDATE_NOT_NULL);
        $lastNameField = new TextField("last_name_field", "Last Name", "", VALIDATE_NOSPACES);
        $lastNameRow = new FormTableRow("last_name_row");
        $lastNameRow->append($lastNameLabel)->append($lastNameField->attr('size', 20));
        $lastNameField->registerValidation(new UniqueUserValidation(VALIDATION_POSITIVE, VALIDATION_ERROR));
        
        $emailLabel = new Label("email_label", "Email", "The email address of the user", VALIDATE_NOT_NULL);
        $emailField = new EmailField("email_field", "Email", "", VALIDATE_NOT_NULL);
        $emailRow = new FormTableRow("email_row");
        $emailRow->append($emailLabel)->append($emailField);

        $roleLabel = new Label("role_label", "Role", "The role of the user", VALIDATE_NOT_NULL);
        $roleField = new SelectBox("role_field", "Role", "",array("Physician", "Pharmacist", "Other (Specify)"), VALIDATE_NOT_NULL);
        $roleRow = new FormTableRow("role_row");
        $roleRow->append($roleLabel)->append($roleField);

        $otherRoleLabel = new Label("other_role_label", "Specify Role", "The role of the user", VALIDATE_NOT_NULL);
        $otherRoleField = new TextField("other_role_field", "other_Role", "", VALIDATE_NOT_NULL);
        $otherRoleRow = new FormTableRow("other_role_row");
	$otherRoleRow->attr("style","display:none");
        $otherRoleRow->append($otherRoleLabel)->append($otherRoleField);

        $languageLabel = new Label("language_label", "Language", "The language of the user", VALIDATE_NOT_NULL);
        $languageField = new SelectBox("language_field", "Language", "",array("English", "French"), VALIDATE_NOT_NULL);
        $languageRow = new FormTableRow("language_row");
        $languageRow->append($languageLabel)->append($languageField);
       
        $postalcodeLabel = new Label("postalcode_label", "Postal Code", "The postalcode of the user", VALIDATE_NOT_NULL);
        $postalcodeField = new TextField("postalcode_field", "Postal Code", "", VALIDATE_NOT_NULL);
	$postalcodeRow = new FormTableRow("postalcode_row");
        $postalcodeRow->append($postalcodeLabel)->append($postalcodeField->attr('size', 20));

        $cityLabel = new Label("city_label", "City", "The city of the user", VALIDATE_NOT_NULL);
        $cityField = new TextField("city_field", "City", "", VALIDATE_NOT_NULL);
	$cityRow = new FormTableRow("city_row");
        $cityRow->append($cityLabel)->append($cityField->attr('size', 20));

        $provinceLabel = new Label("province_label", "Province", "The province of the user", VALIDATE_NOT_NULL);
        $provinceField = new TextField("province_field", "Province", "", VALIDATE_NOT_NULL);
	$provinceRow = new FormTableRow("province_row");
        $provinceRow->append($provinceLabel)->append($provinceField->attr('size', 20));
        
	$clinicLabel = new Label("clinic_label", "Clinic/Hospital Name", "The clinic of the user", VALIDATE_NOT_NULL);
        $clinicField = new TextField("clinic_field", "Clinic/Hospital Name", "", VALIDATE_NOT_NULL);
        $clinicRow = new FormTableRow("clinic_row");
	$clinicRow->attr('style','display:none');
        $clinicRow->append($clinicLabel)->append($clinicField->attr('size', 20));

        $specialtyLabel = new Label("specialty_label", "Specialty", "The specialty of the user", VALIDATE_NOTHING);
        $specialtyField = new SelectBox("specialty_field", "Specialty", "",array("Family Physician/General Practitioner",
											     "Obstetrician/Gynecologist",
											     "Pediatrician",
											     "Other (Specify)"), VALIDATE_NOTHING);
        $specialtyRow = new FormTableRow("specialty_row");
	$specialtyRow->attr('style','display:none');
        $specialtyRow->append($specialtyLabel)->append($specialtyField);

        $otherSpecialtyLabel = new Label("other_specialty_label", "Specify Specialty", "The specialty of the user", VALIDATE_NOT_NULL);
        $otherSpecialtyField = new TextField("other_specialty_field", "other_Specialty", "", VALIDATE_NOT_NULL);
        $otherSpecialtyRow = new FormTableRow("other_specialty_row");
        $otherSpecialtyRow->attr("style","display:none");
        $otherSpecialtyRow->append($otherSpecialtyLabel)->append($otherSpecialtyField);
 
        $yearsLabel = new Label("years_label", "Years in Practice", "The years of practice of the user", VALIDATE_NOTHING);
        $yearsField = new TextField("years_field", "Years of Practice", "", VALIDATE_NOTHING);
        $yearsRow = new FormTableRow("years_row");
        $yearsRow->attr('style','display:none');
        $yearsRow->append($yearsLabel)->append($yearsField->attr('size',5));

        $provisionLabel = new Label("provision_label", "Prior Provision of<br>Abortion Services", "The prior provision of medical or surgical abortion services of the user", VALIDATE_NOTHING);
        $provisionField = new HorizontalRadioBox("provision_field", "Prior Provision of Abortion Services", array("provision_fieldyes","provision_fieldno"), array("Yes","No"), VALIDATE_NOTHING);
        $provisionRow = new FormTableRow("provision_row");
        $provisionRow->attr('style','display:none');
        $provisionRow->append($provisionLabel)->append($provisionField);

	$disclosureLabelRow = new FormTableRow("disclosureLabel");
	$disclosureLabelRow->attr("style","display:none");
	$disclosureRow = new FormTableRow("disclosure");
	$disclosureRow->attr("style","display:none;");
	$emptyElement = new EmptyElement();
	$disclosureLabel = new CustomElement("disclosure", "disclosure", "disclosure", "<td colspan=2><div id='disclosure_div'style='background: #f0f0f0;'>
											This community provides Mifepristone trained physicians with a way<br />
											to locate the nearest trained pharmacist.<br /> 
											Do you agree to disclose the name and location of your pharmacy for this map?</td>");
	$disclosureField = new HorizontalRadioBox("disclosure", "disclosure",array("disclosure"), array("I agree", "I disagree"));
	$disclosureLabelRow->append($disclosureLabel);
	$disclosureRow->append($emptyElement)->append($disclosureField)->attr("id","disclosure");

	$pharmacyNameLabel = new Label("pharmacy_name_label", "Pharmacy Name", "Pharmacy Name", VALIDATE_NOT_NULL);
	$pharmacyField = new TextField("pharmacy_name_field", "Pharmacy Name", "", VALIDATE_NOT_NULL);
	$pharmacyRow = new FormTableRow("pharmacy_name_row");
	$pharmacyRow->append($pharmacyNameLabel)->append($pharmacyField);
        $pharmacyRow->attr("style","display:none");

        $pharmacyAddressLabel = new Label("pharmacy_address_label", "Pharmacy Address", "Pharmacy Address", VALIDATE_NOT_NULL);
        $pharmacyAddressField = new TextField("pharmacy_address_field", "Pharmacy Address", "", VALIDATE_NOT_NULL);
        $pharmacyAddressRow = new FormTableRow("pharmacy_address_row");
	$pharmacyAddressRow->attr("style","display:none");
        $pharmacyAddressRow->append($pharmacyAddressLabel)->append($pharmacyAddressField);

        $fileLabel = new Label("file_label", "Proof of Certification", "The prior file of medical or surgical abortion services of the user", VALIDATE_NOTHING);
        $fileField = new FileField("file_field", "Proof of Certification", "", VALIDATE_NOTHING);
        $fileRow = new FormTableRow("file_row");
        $fileRow->append($fileLabel)->append($fileField);

        $captchaLabel = new Label("captcha_label", "Enter Code", "Enter the code you see in the image", VALIDATE_NOT_NULL);
        $captchaField = new Captcha("captcha_field", "Captcha", "", VALIDATE_NOT_NULL);
        $captchaRow = new FormTableRow("captcha_row");
        $captchaRow->append($captchaLabel)->append($captchaField);
        
        $submitCell = new EmptyElement();
        $submitField = new SubmitButton("submit", "Submit Request", "Submit Request", VALIDATE_NOTHING);
        $submitRow = new FormTableRow("submit_row");
        $submitRow->append($submitCell)->append($submitField);
        
        $formTable->append($firstNameRow)
                  ->append($lastNameRow)
                  ->append($emailRow)
		  ->append($languageRow)
		  ->append($postalcodeRow)
                  ->append($cityRow)
                  ->append($provinceRow)
                  ->append($roleRow)
		  ->append($otherRoleRow)
		  ->append($clinicRow)
		  ->append($specialtyRow)
		  ->append($otherSpecialtyRow)
		  ->append($yearsRow)
		  ->append($provisionRow)
                  ->append($disclosureLabelRow)
		  ->append($disclosureRow)
		  ->append($pharmacyRow)
		  ->append($pharmacyAddressRow)
		  ->append($fileRow)
                  ->append($captchaRow)
                  ->append($submitRow);
        
        $formContainer->append($formTable);
        return $formContainer;
    }
    
     function generateFormHTML($wgOut){
        global $wgUser, $wgServer, $wgScriptPath, $wgRoles, $config;
        $user = Person::newFromId($wgUser->getId());
        $wgOut->addHTML("Each submitted form is reviewed by an administrator. You will be contacted by email with your login details when your submission has been approved. You may need to check your spam/junk mail for the registration email.  If you do not get an email after a few business days, please contact <a href='mailto:{$config->getValue('supportEmail')}'>{$config->getValue('supportEmail')}</a>.<br /><br />");
        $wgOut->addHTML("<form action='$wgScriptPath/index.php/Special:CAPSRegister' method='post' enctype='multipart/form-data'>\n");
        $form = self::createForm();
        $wgOut->addHTML($form->render());
	$wgOut->addScript("<script type='text/javascript'>
				$(document).ready(function () {
    				    toggleFields();
    				    $('#role_field').change(function () {
        			        toggleFields();
    				    });
				    $('#disclosure0').click(function (){
					$('#pharmacy_address_label').parent().parent().show();
                                        $('#pharmacy_name_label').parent().parent().show();
				    });
                                    $('#disclosure1').click(function (){
 					$('#pharmacy_address_label').parent().parent().hide();
                                        $('#pharmacy_name_label').parent().parent().hide();
				    });
                                    $('#specialty_field').change(function () {
                                        specialtySpecify();
                                    });
				});

				function toggleFields() {
    				    if ($('#role_field').val() == 'Physician'){
        				$('#specialty_label').parent().parent().show();
                                        $('#years_label').parent().parent().show();
                                        $('#provision_label').parent().parent().show();
					$('#clinic_label').parent().parent().show();
				    }
				    else{
                                        $('#specialty_label').parent().parent().hide();
                                        $('#years_label').parent().parent().hide();
                                        $('#provision_label').parent().parent().hide();
                                        $('#clinic_label').parent().parent().hide();
				    }
				    if ($('#role_field').val() == 'Pharmacist'){
					$('#disclosure_div').parent().parent().show();
                                        $('input[name=disclosure]').parent().parent().show();

				    }
				    else{
                                        $('#disclosure_div').parent().parent().hide();
                                        $('input[name=disclosure]').parent().parent().hide();

				    }
                                    if ($('#role_field').val() == 'Other (Specify)'){
                                        $('#other_role_label').parent().parent().show();
				    }
				    else{
					$('#other_role_label').parent().parent().hide();
				    }
				}
                                function specialtySpecify() {
                                    if ($('#specialty_field').val() == 'Other (Specify)'){
                                        $('#other_specialty_label').parent().parent().show();
                                    }
                                    else{
                                        $('#other_specialty_label').parent().parent().hide();
                                    }
				}
		</script>");	
        $wgOut->addHTML("</form>");
    }
    
    function handleSubmit($wgOut){
        global $wgServer, $wgScriptPath, $wgMessage, $wgGroupPermissions;
        $form = self::createForm();
        $status = $form->validate();
        if($status){
            $firstname = $form->getElementById('first_name_field')->setPOST('wpFirstName');
            $lastname = $form->getElementById('last_name_field')->setPOST('wpLastName');
ST('wpEmail');
            $email = $form->getElementById('email_field')->setPOST('wpEmail');
            $role = $form->getElementById('role_field')->setPOST('wpRole');;
	    $language = $form->getElementById('language_field')->setPOST('wpLanguage');;
	    $postalcode = $form->getElementById('postalcode_field')->setPOST('wpPostalCode');;
	    $specialty = $form->getElementById('specialty_field')->setPOST('wpSpecialty');
	    $years = $form->getElementById('years_field')->setPOST('wpYears');;
	    $provision = $form->getElementById('provision_field')->setPOST('wpProvision');;
	    $file = $_FILES['file_field']['tmp_name'];
            $file_size= filesize($file);
            $handle = fopen($file, "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));
            $uid = md5(uniqid(time()));
            $name = basename($file);

            // header
            $header = "From: ".$firstname." ".$lastname." <".$email.">\r\n";
            $header .= "Reply-To: ".$email."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

            // message & attachment
            $nmessage = "--".$uid."\r\n";
            $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
            $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $nmessage .= $_POST['wpSpecialty']."\r\n\r\n";
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: application/octet-stream; name=\""."credentials"."\"\r\n";
            $nmessage .= "Content-Transfer-Encoding: base64\r\n";
            $nmessage .= "Content-Disposition: attachment; filename=\""."credentials"."\"\r\n\r\n";
            $nmessage .= $content."\r\n\r\n";
            $nmessage .= "--".$uid."--";

            if (mail("rdejesus@ualberta.ca", "hi", $nmessage, $header)) {
		print_r("true");
                return true; // Or do something here
            } else {
		print_r("false");
              return false;
            }

/*
            $_POST['wpFirstName'] = ucfirst($_POST['wpFirstName']);
            $_POST['wpLastName'] = ucfirst($_POST['wpLastName']);
            $_POST['wpRealName'] = "{$_POST['wpFirstName']} {$_POST['wpLastName']}";
            $_POST['wpName'] = ucfirst(str_replace("&#39;", "", strtolower($_POST['wpFirstName']))).".".ucfirst(str_replace("&#39;", "", strtolower($_POST['wpLastName'])));
            $_POST['wpUserType'] = HQP;
            $_POST['wpSendMail'] = "true";
            $_POST['candidate'] = "1";
            
            if(!preg_match("/^[À-Ÿa-zA-Z\-]+\.[À-Ÿa-zA-Z\-]+$/", $_POST['wpName'])){
                $wgMessage->addError("This User Name is not in the format 'FirstName.LastName'");
                
            }
            else{
                $wgGroupPermissions['*']['createaccount'] = true;
                GrandAccess::$alreadyDone = array();
                $result = APIRequest::doAction('CreateUser', false);
                $wgGroupPermissions['*']['createaccount'] = false;
                GrandAccess::$alreadyDone = array();
                if($result){
                    $form->reset();
                    $wgMessage->addSuccess("A randomly generated password for <b>{$_POST['wpName']}</b> has been sent to <b>{$_POST['wpEmail']}</b>");
                    redirect("$wgServer$wgScriptPath");
                }
            }*/
        }
        CAPSRegister::generateFormHTML($wgOut);
    }

}

?>
