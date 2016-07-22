<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['CAPSRegister'] = 'CAPSRegister'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['CAPSRegister'] = $dir . 'CAPSRegister.i18n.php';
$wgSpecialPageGroups['CAPSRegister'] = 'network-tools';

function runCAPSRegister($par) {
    CAPSRegister::execute($par);
}

class CAPSRegister extends SpecialPage{

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
        global $wgLang;
        if($wgLang->getCode() == "en"){
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

            $otherRoleLabel = new Label("other_role_label", "Specify Role", "The role of the user", VALIDATE_NOTHING);
            $otherRoleField = new TextField("other_role_field", "other_Role", "", VALIDATE_NOTHING);
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
            
	    $clinicLabel = new Label("clinic_label", "Clinic/Hospital Name", "The clinic of the user", VALIDATE_NOTHING);
            $clinicField = new TextField("clinic_field", "Clinic/Hospital Name", "", VALIDATE_NOTHING);
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

            $otherSpecialtyLabel = new Label("other_specialty_label", "Specify Specialty", "The specialty of the user", VALIDATE_NOTHING);
            $otherSpecialtyField = new TextField("other_specialty_field", "other_Specialty", "", VALIDATE_NOTHING);
            $otherSpecialtyRow = new FormTableRow("other_specialty_row");
            $otherSpecialtyRow->attr("style","display:none");
            $otherSpecialtyRow->append($otherSpecialtyLabel)->append($otherSpecialtyField);
     
            $yearsLabel = new Label("years_label", "Years in Practice", "The years of practice of the user", VALIDATE_NOTHING);
            $yearsField = new TextField("years_field", "Years of Practice", "", VALIDATE_NOTHING);
            $yearsRow = new FormTableRow("years_row");
            $yearsRow->attr('style','display:none');
            $yearsRow->append($yearsLabel)->append($yearsField->attr('size',5));

            $provisionLabel = new Label("provision_label", "Prior Provision of<hr style='height:0pt; visibility:hidden;'/>Abortion Services", "The prior provision of medical or surgical abortion services of the user", VALIDATE_NOTHING);
            $provisionField = new VerticalRadioBox("provision_field", "Prior Provision of Abortion Services", array("provision_fieldyes","provision_fieldno"), array("Yes","No"), VALIDATE_NOTHING);
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
	    $disclosureField = new HorizontalRadioBox("disclosure_field", "disclosure_field",array("disclosure_fieldyes", "provision_fieldno"), array("I agree", "I disagree"));
	    $disclosureLabelRow->append($disclosureLabel);
	    $disclosureRow->append($emptyElement)->append($disclosureField);

	    $pharmacyNameLabel = new Label("pharmacy_name_label", "Pharmacy Name", "Pharmacy Name", VALIDATE_NOTHING);
	    $pharmacyField = new TextField("pharmacy_name_field", "Pharmacy Name", "", VALIDATE_NOTHING);
	    $pharmacyRow = new FormTableRow("pharmacy_name_row");
	    $pharmacyRow->append($pharmacyNameLabel)->append($pharmacyField);
            $pharmacyRow->attr("style","display:none");

            $pharmacyAddressLabel = new Label("pharmacy_address_label", "Pharmacy Address", "Pharmacy Address", VALIDATE_NOTHING);
            $pharmacyAddressField = new TextField("pharmacy_address_field", "Pharmacy Address", "", VALIDATE_NOTHING);
            $pharmacyAddressRow = new FormTableRow("pharmacy_address_row");
	    $pharmacyAddressRow->attr("style","display:none");
            $pharmacyAddressRow->append($pharmacyAddressLabel)->append($pharmacyAddressField);

           /* $fileLabel = new Label("file_label", "Proof of Certification:</div>
					          <div style='text-align:right; font-size:0.7em'>
					          <a href='#!' onclick='$(\"#fileUploadInfo\").dialog({width:\"221px\",position:{my: \"center\", at:\"center\", of: window}})'>[what is this?]</a>", "The prior file of medical or surgical abortion services of the user", VALIDATE_NOTHING, false);*/
            $fileLabel = new Label("file_label", "Proof of Certification:</div>
                                                  <div style='text-align:right; font-size:0.7em'>
                                                  <a href='#!' onclick='openDialog()'>[what is this?]</a>", "The prior file of medical or surgical abortion services of the user", VALIDATE_NOT_NULL, false);
            $fileField = new FileField("file_field", "Proof of Certification", "", VALIDATE_NOT_NULL);
            $fileRow = new FormTableRow("file_row");
	    $fileRow->attr('style','line-height: 10px;');
            $fileRow->append($fileLabel)->append($fileField);

            $referenceLabel = new Label("reference_label", "Name of Reference", "The physician or pharmacist who referred the user", VALIDATE_NOTHING);
            $referenceField = new TextField("reference_field", "Name of Reference", "", VALIDATE_NOTHING);
            $referenceRow = new FormTableRow("reference_row");
            $referenceRow->append($referenceLabel)->append($referenceField);

            $captchaLabel = new Label("captcha_label", "Enter Code", "Enter the code you see in the image", VALIDATE_NOT_NULL);
            $captchaField = new Captcha("captcha_field", "Captcha", "", VALIDATE_NOT_NULL);
            $captchaRow = new FormTableRow("captcha_row");
            $captchaRow->append($captchaLabel)->append($captchaField);
            $termsLabel = new Label("terms_label", "Terms and Conditions", "Terms and conditions", VALIDATE_NOTHING);
            $termsField = new CustomElement("terms", "terms", "terms", 
                                            
                                               " <div class='TermsOuterDiv'>
                                                This agreement establishes the terms and conditions under which the Canadian Abortion Providers’ Support - Communauté de pratique canadienne sur l'avortement (CAPS-CPCA) can acquire and use data from the other party. Either party may be a provider of data to the other, or a recipient of data from the other.
    The confidentiality of data pertaining to individuals will be protected as follows:<br />
    The data recipient will not release the names of individuals, or information that could be linked to an individual, nor will the recipient present the results of data analysis (including maps) in any manner that would reveal the identity of individuals.<br />
    The data recipient will not release individual addresses, nor will the recipient present the results of data analysis (including maps) in any manner that would reveal individual addresses.<br />
    Both parties shall comply with all Federal and State laws and regulations governing the confidentiality of the information that is the subject of this Agreement.
    The data recipient will not release data to a third party without prior approval from the data provider.<br />
    The data recipient will not share, publish, or otherwise release any findings or conclusions derived from analysis of data obtained from the data provider without prior approval from the data provider.<br />
    Data transferred pursuant to the terms of this Agreement shall be utilized solely for the purposes set forth in the “Partnership Agreement”.
    All data transferred to CAPS shall remain the property of CAPS and shall be returned to CAPS upon termination of the Agreements.
    Any third party granted access to data, as permitted under condition #2, above, shall be subject to the terms and conditions of this agreement. Acceptance of these terms must be provided in writing by the third party before data will be released.</div>
                                             
                                                                                            
                                             ");
            $termsAgree = new HorizontalCheckBox("terms_agree", "terms_agree", array(), array("I have read and agree to the terms and conditions"), VALIDATE_NOTHING);
            $termsRow = new FormTableRow("terms_row");
	    $termsAgree->attr("disabled","disabled")->attr("style","opacity:0.3");
            $termsRow->append($termsLabel)->append($termsField);
            $agreeRow = new FormTableRow("agree_row");
            $agreeCell = new EmptyElement();
            $agreeRow->append($agreeCell)->append($termsAgree);
            $agreeRow->attr("align","right")->attr("class","terms");
            $submitCell = new EmptyElement();
            $submitField = new SubmitButton("submit", "Submit Request", "Submit Request", VALIDATE_NOTHING);
	    $submitField->attr("disabled","disabled");
            $submitRow = new FormTableRow("submit_row");
            $submitRow->append($submitCell)->append($submitField);
	        $submitRow->attr("align","right");
        }
        else if($wgLang->getCode() == "fr"){
            $formContainer = new FormContainer("form_container");
            $formTable = new FormTable("form_table");
            
            $firstNameLabel = new Label("first_name_label", "Prénom", "Le premier nom de l'utilisateur (ne peut pas contenir des espaces)", VALIDATE_NOT_NULL);
            $firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOSPACES);
            $firstNameRow = new FormTableRow("first_name_row");
            $firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
            
            $lastNameLabel = new Label("last_name_label", "Nom de famille", "Le nom de l'utilisateur (ne peut pas contenir des espaces)", VALIDATE_NOT_NULL);
            $lastNameField = new TextField("last_name_field", "Last Name", "", VALIDATE_NOSPACES);
            $lastNameRow = new FormTableRow("last_name_row");
            $lastNameRow->append($lastNameLabel)->append($lastNameField->attr('size', 20));
            $lastNameField->registerValidation(new UniqueUserValidation(VALIDATION_POSITIVE, VALIDATION_ERROR));
            
            $emailLabel = new Label("email_label", "Email", "L'adresse email de l'utilisateur", VALIDATE_NOT_NULL);
            $emailField = new EmailField("email_field", "Email", "", VALIDATE_NOT_NULL);
            $emailRow = new FormTableRow("email_row");
            $emailRow->append($emailLabel)->append($emailField);

            $roleLabel = new Label("role_label", "Rôle", "Le rôle de l' utilisateur", VALIDATE_NOT_NULL);
            $roleField = new SelectBox("role_field", "Role", "",array("Médecin", "Pharmacien", "Autre (précisez)"), VALIDATE_NOT_NULL);
            $roleRow = new FormTableRow("role_row");
            $roleRow->append($roleLabel)->append($roleField);

            $otherRoleLabel = new Label("other_role_label", "Spécifiez Rôle", "Le rôle de l' utilisateur", VALIDATE_NOTHING);
            $otherRoleField = new TextField("other_role_field", "other_Role", "", VALIDATE_NOTHING);
            $otherRoleRow = new FormTableRow("other_role_row");
	    $otherRoleRow->attr("style","display:none");
            $otherRoleRow->append($otherRoleLabel)->append($otherRoleField);

            $languageLabel = new Label("language_label", "La langue", "La langue de l'utilisateur", VALIDATE_NOT_NULL);
            $languageField = new SelectBox("language_field", "Language", "",array("English", "Français"), VALIDATE_NOT_NULL);
            $languageRow = new FormTableRow("language_row");
            $languageRow->append($languageLabel)->append($languageField);
           
            $postalcodeLabel = new Label("postalcode_label", "Code Postal", "Le code postal de l'utilisateur", VALIDATE_NOT_NULL);
            $postalcodeField = new TextField("postalcode_field", "Postal Code", "", VALIDATE_NOT_NULL);
	    $postalcodeRow = new FormTableRow("postalcode_row");
            $postalcodeRow->append($postalcodeLabel)->append($postalcodeField->attr('size', 20));

            $cityLabel = new Label("city_label", "Ville", "Le ville de l'utilisateur", VALIDATE_NOT_NULL);
            $cityField = new TextField("city_field", "City", "", VALIDATE_NOT_NULL);
	    $cityRow = new FormTableRow("city_row");
            $cityRow->append($cityLabel)->append($cityField->attr('size', 20));

            $provinceLabel = new Label("province_label", "Province", "La province de l'utilisateur", VALIDATE_NOT_NULL);
            $provinceField = new TextField("province_field", "Province", "", VALIDATE_NOT_NULL);
	    $provinceRow = new FormTableRow("province_row");
            $provinceRow->append($provinceLabel)->append($provinceField->attr('size', 20));
            
	    $clinicLabel = new Label("clinic_label", "Clinique / Nom de l'hôpital", "La clinique de l'utilisateur", VALIDATE_NOTHING);
            $clinicField = new TextField("clinic_field", "Clinic/Hospital Name", "", VALIDATE_NOTHING);
            $clinicRow = new FormTableRow("clinic_row");
	    $clinicRow->attr('style','display:none');
            $clinicRow->append($clinicLabel)->append($clinicField->attr('size', 20));

            $specialtyLabel = new Label("specialty_label", "Spécialité", "La spécialité de l'utilisateur", VALIDATE_NOTHING);
            $specialtyField = new SelectBox("specialty_field", "Specialty", "",array("Médecin de famille/Médecin généraliste",
											         "Gynécologue/Obstétricien",
											         "Pédiatre",
											         "Autre (précisez)"), VALIDATE_NOTHING);
            $specialtyRow = new FormTableRow("specialty_row");
	    $specialtyRow->attr('style','display:none');
            $specialtyRow->append($specialtyLabel)->append($specialtyField);

            $otherSpecialtyLabel = new Label("other_specialty_label", "Spécifiez spéciaux", "Le spéciaux de l'utilisateur", VALIDATE_NOTHING);
            $otherSpecialtyField = new TextField("other_specialty_field", "other_Specialty", "", VALIDATE_NOTHING);
            $otherSpecialtyRow = new FormTableRow("other_specialty_row");
            $otherSpecialtyRow->attr("style","display:none");
            $otherSpecialtyRow->append($otherSpecialtyLabel)->append($otherSpecialtyField);
     
            $yearsLabel = new Label("years_label", "Années de pratique", "Le Années de pratique de l'utilisateur", VALIDATE_NOTHING);
            $yearsField = new TextField("years_field", "Years of Practice", "", VALIDATE_NOTHING);
            $yearsRow = new FormTableRow("years_row");
            $yearsRow->attr('style','display:none');
            $yearsRow->append($yearsLabel)->append($yearsField->attr('size',5));

            $provisionLabel = new Label("provision_label", "Fourniture Avant de<hr style='height:0pt; visibility:hidden;'/>Les services d'avortement", "La mise à disposition préalable des services d'avortement médical ou chirurgical de l'utilisateur", VALIDATE_NOTHING);
            $provisionField = new VerticalRadioBox("provision_field", "Prior Provision of Abortion Services", array("provision_fieldyes","provision_fieldno"), array("Yes","No"), VALIDATE_NOTHING);
            $provisionRow = new FormTableRow("provision_row");
            $provisionRow->attr('style','display:none');
            $provisionRow->append($provisionLabel)->append($provisionField);

	        $disclosureLabelRow = new FormTableRow("disclosureLabel");
	        $disclosureLabelRow->attr("style","display:none");
	        $disclosureRow = new FormTableRow("disclosure");
	        $disclosureRow->attr("style","display:none;");
	        $emptyElement = new EmptyElement();
	        $disclosureLabel = new CustomElement("disclosure", "disclosure", "disclosure", "<td colspan=2><div id='disclosure_div'style='background: #f0f0f0;'>
											        Cette communauté fournit des mifépristone médecins formés avec un moyen <br />
                                                    pour localiser le pharmacien formé le plus proche. <br />
                                                    Acceptez-vous de divulguer le nom et l'emplacement de votre pharmacie pour cette carte?>");
	        $disclosureField = new HorizontalRadioBox("disclosure_field", "disclosure_field",array("disclosure_fieldyes", "provision_fieldno"), array("I agree", "I disagree"));
	        $disclosureLabelRow->append($disclosureLabel);
	        $disclosureRow->append($emptyElement)->append($disclosureField);

	        $pharmacyNameLabel = new Label("pharmacy_name_label", "Pharmacie Nom", "Pharmacie Nom", VALIDATE_NOTHING);
	        $pharmacyField = new TextField("pharmacy_name_field", "Pharmacy Name", "", VALIDATE_NOTHING);
	        $pharmacyRow = new FormTableRow("pharmacy_name_row");
	        $pharmacyRow->append($pharmacyNameLabel)->append($pharmacyField);
            $pharmacyRow->attr("style","display:none");

            $pharmacyAddressLabel = new Label("pharmacy_address_label", "Pharmacie Adresse", "Pharmacie Adresse", VALIDATE_NOTHING);
            $pharmacyAddressField = new TextField("pharmacy_address_field", "Pharmacy Address", "", VALIDATE_NOTHING);
            $pharmacyAddressRow = new FormTableRow("pharmacy_address_row");
	        $pharmacyAddressRow->attr("style","display:none");
            $pharmacyAddressRow->append($pharmacyAddressLabel)->append($pharmacyAddressField);

           /* $fileLabel = new Label("file_label", "Proof of Certification:</div>
					          <div style='text-align:right; font-size:0.7em'>
					          <a href='#!' onclick='$(\"#fileUploadInfo\").dialog({width:\"221px\",position:{my: \"center\", at:\"center\", of: window}})'>[what is this?]</a>", "The prior file of medical or surgical abortion services of the user", VALIDATE_NOTHING, false);*/
            $fileLabel = new Label("file_label", "Preuve de la certification:</div>
                                                  <div style='text-align:right; font-size:0.7em'>
                                                  <a href='#!' onclick='openDialog()'>[Qu'est-ce que c'est?]</a>", "Le fichier avant des services d'avortement médical ou chirurgical de l'utilisateur", VALIDATE_NOT_NULL, false);
            $fileField = new FileField("file_field", "Preuve de certification", "", VALIDATE_NOT_NULL);
            $fileRow = new FormTableRow("file_row");
	        $fileRow->attr('style','line-height: 10px;');
            $fileRow->append($fileLabel)->append($fileField);

            $referenceLabel = new Label("reference_label", "Nom de référence", "Le médecin ou le pharmacien qui a renvoyé l' utilisateur", VALIDATE_NOTHING);
            $referenceField = new TextField("reference_field", "Name of Reference", "", VALIDATE_NOTHING);
            $referenceRow = new FormTableRow("reference_row");
            $referenceRow->append($referenceLabel)->append($referenceField);

            $captchaLabel = new Label("captcha_label", "Entrez le code", "Entrez le code que vous voyez dans l'image", VALIDATE_NOT_NULL);
            $captchaField = new Captcha("captcha_field", "Captcha", "", VALIDATE_NOT_NULL);
            $captchaRow = new FormTableRow("captcha_row");
            $captchaRow->append($captchaLabel)->append($captchaField);
            $termsLabel = new Label("terms_label", "Termes et conditions", "Termes et conditions", VALIDATE_NOTHING);
            $termsField = new CustomElement("terms", "terms", "terms", 
                                            
                                               " <div class='TermsOuterDiv'>
                                                Cet accord établit les modalités et les conditions dans lesquelles le soutien des fournisseurs d'avortement canadiens - Communauté de pratique canadienne sur l'avortement (CAPS-ACEP) peuvent acquérir et utiliser les données de l'autre partie. Chacune des parties peut être un fournisseur de données à l'autre, ou d'un destinataire des données de l'autre.
    La confidentialité des données relatives aux individus seront protégés comme suit: <br />
    Le destinataire des données ne sera pas communiqué les noms des personnes ou des informations qui pourraient être liés à un individu, ni ne le bénéficiaire présentera les résultats de l'analyse des données (y compris les cartes) de quelque manière qui révéleraient l'identité des personnes. <br />
    Le destinataire des données ne sera pas communiqué des adresses individuelles, ni ne le bénéficiaire présentera les résultats de l'analyse des données (y compris les cartes) de quelque manière qui pourrait révéler des adresses individuelles. <br />
    Les deux parties doivent se conformer à toutes les lois et règlements fédéraux et étatiques régissant la confidentialité de l'information qui fait l'objet du présent Accord.
    Le destinataire des données ne sera pas communiqué de données à un tiers sans l'autorisation préalable du fournisseur de données. <br />
    Le destinataire des données ne sera pas partager, publier, ou autrement libérer toutes les constatations ou conclusions tirées de l'analyse des données obtenues à partir du fournisseur de données sans l'approbation préalable du fournisseur de données. <br />
    Les données transférées conformément aux dispositions du présent Accord seront utilisés uniquement aux fins énoncées dans le 'accord de partenariat'.
    Toutes les données transférées à CAPS restent la propriété du CAPS et doivent être retournés à CAPS à la résiliation des accords.
    Tout tiers accordé l'accès aux données, comme autorisé par la condition n ° 2, ci-dessus, sont soumis aux termes et conditions de cet accord. L'acceptation de ces conditions doit être fournie par écrit par le tiers avant que les données seront publiées.</div>
                                             
                                                                                            
                                             ");
            $termsAgree = new HorizontalCheckBox("terms_agree", "terms_agree", array(), array("Je l'ai lu et accepté les termes et conditions"), VALIDATE_NOTHING);
            $termsRow = new FormTableRow("terms_row");
	    $termsAgree->attr("disabled","disabled")->attr("style","opacity:0.3");
            $termsRow->append($termsLabel)->append($termsField);
            $agreeRow = new FormTableRow("agree_row");
            $agreeCell = new EmptyElement();
            $agreeRow->append($agreeCell)->append($termsAgree);
            $agreeRow->attr("align","right")->attr("class","terms");
            $submitCell = new EmptyElement();
            $submitField = new SubmitButton("submit", "Envoyer la demande", "Envoyer la demande", VALIDATE_NOTHING);
	    $submitField->attr("disabled","disabled");
            $submitRow = new FormTableRow("submit_row");
            $submitRow->append($submitCell)->append($submitField);
	        $submitRow->attr("align","right");
        }
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
		          ->append($referenceRow)
                  ->append($captchaRow)
		          ->append($termsRow)
		          ->append($agreeRow)
                  ->append($submitRow);
        
        $formContainer->append($formTable);
        return $formContainer;
    }
    
     function generateFormHTML($wgOut){
        global $wgUser, $wgServer, $wgScriptPath, $wgRoles, $config, $wgLang;
        $user = Person::newFromId($wgUser->getId());
        if($wgLang->getCode() == "en"){
	        $wgOut->addHTML("<div id='fileUploadInfo' title='Proof of Certification' style='display:none'>Please upload a copy of your proof of certification from the Mifepristone training program.</div>");
            $wgOut->addHTML("Each submitted form is reviewed by an administrator. You will be contacted by email with your login details when your submission has been approved. You may need to check your spam/junk mail for the registration email.  If you do not get an email after a few business days, please contact <a href='mailto:{$config->getValue('supportEmail')}'>{$config->getValue('supportEmail')}</a>.<br /><br />");
        }
        else if($wgLang->getCode() == "fr"){
	        $wgOut->addHTML("<div id='fileUploadInfo' title='Proof of Certification' style='display:none'>S'il vous plaît télécharger une copie de votre preuve de certification du programme de formation mifépristone .</div>");
            $wgOut->addHTML("Chaque formulaire soumis est examiné par un administrateur . Vous serez contacté par email avec vos informations de connexion lorsque votre demande a été approuvée. Vous devrez peut-être vérifier votre courrier Spam / jonque pour l'e-mail d'inscription. Si vous ne recevez un courriel après quelques jours ouvrables , s'il vous plaît contacter
 <a href='mailto:{$config->getValue('supportEmail')}'>{$config->getValue('supportEmail')}</a>.<br /><br />");
        }
        $wgOut->addHTML("<form action='$wgScriptPath/index.php/Special:CAPSRegister' method='post' enctype='multipart/form-data'>\n");
        $form = self::createForm();
        $wgOut->addHTML($form->render());
	$wgOut->addScript("<script type='text/javascript'>
				$(document).ready(function () {
    				    toggleFields();
                        disclaimerFunction();
    				    $('#role_field').change(function () {
        			        toggleFields();
    				    });
				    $('#disclosure_field0').click(function (){
					$('#pharmacy_address_label').parent().parent().show();
                                        $('#pharmacy_name_label').parent().parent().show();
				    });
                                    $('#disclosure_field1').click(function (){
 					$('#pharmacy_address_label').parent().parent().hide();
                                        $('#pharmacy_name_label').parent().parent().hide();
				    });
                                    $('#specialty_field').change(function () {
                                        specialtySpecify();
                                    });
				     $(\"input[name='terms_agree[]']\").change(function(){
					checkSubmit();
				    });
				});

				function toggleFields() {
    				    if ($('#role_field').val() == 'Physician' || $('#role_field').val() == 'Médecin'){
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
                                        $('input[name=disclosure_field]').parent().parent().show();

				    }
				    else{
                                        $('#disclosure_div').parent().parent().hide();
                                        $('input[name=disclosure_field]').parent().parent().hide();

				    }
                                    if ($('#role_field').val() == 'Other (Specify)' || $('#role_field').val() == 'Autre (précisez)'){
                                        $('#other_role_label').parent().parent().show();
				    }
				    else{
					$('#other_role_label').parent().parent().hide();
				    }
				}
                                function specialtySpecify() {
                                    if ($('#specialty_field').val() == 'Other (Specify)' || $('#specialty_field').val() == 'Autre (précisez)'){
                                        $('#other_specialty_label').parent().parent().show();
                                    }
                                    else{
                                        $('#other_specialty_label').parent().parent().hide();
                                    }
				}
				function openDialog(){
					$('#fileUploadInfo').dialog({width:'200px',position:{my: 'center', at:'center', of: window},modal:true,resizable:false,     buttons: {
        'OK': function () {
            $(this).dialog('close')
        }
    }});

				}

function disclaimerFunction() {
            $('.TermsOuterDiv').scroll(function() {
		if ($(this).scrollTop() == $(this)[0].scrollHeight - $(this).height()-12) {
        	    $(\"input[name='terms_agree[]']\").removeAttr('disabled');
                    $(\"input[name='terms_agree[]']\").css('opacity','1');

    		}
		else{
                    $(\"input[name='terms_agree[]']\").attr('disabled', 'disabled');
                    $(\"input[name='terms_agree[]']\").css('opacity','0.3');
		}
            });
       }
function checkSubmit(){
	if($(\"input[name='terms_agree[]']\").is(':checked')){
            $(\"input[name='submit']\").removeAttr('disabled');
	}
	else{
            $(\"input[name='submit']\").attr('disabled','disabled');
	}
}
		</script>");	
        $wgOut->addHTML("</form>");
    }
    
    function handleSubmit($wgOut){
        global $wgServer, $wgScriptPath, $wgMessage, $wgGroupPermissions;
        $max_file_size = 20;
        $form = self::createForm();
        $status = $form->validate();
        if($status){
            $firstname = $form->getElementById('first_name_field')->setPOST('wpFirstName');
            $lastname = $form->getElementById('last_name_field')->setPOST('wpLastName');
            $email = $form->getElementById('email_field')->setPOST('wpEmail');
            $role = $form->getElementById('role_field')->setPOST('wpRole');
	    if(in_array($_POST['wpRole'], array("Physician", "Pharmacist"))){
	        $_POST['wpUserType'] = $_POST['wpRole'];
	    }
	    else{
		$_POST['wpUserType'] = "HQP";
	    }
        if($_POST['wpRole'] == "Other (Specify)"){
	        $role = $form->getElementById('other_role_field')->setPOST('wpRole');
        }
	    $language = $form->getElementById('language_field')->setPOST('wpLanguage');
	    $postalcode = $form->getElementById('postalcode_field')->setPOST('wpPostalCode');
        $city = $form->getElementById('city_field')->setPOST('wpCity');
	    $province = $form->getElementById('province_field')->setPOST('wpProvince');
        $reference = $form->getElementById('reference_field')->setPOST('wpReference');
        if($_POST['wpRole'] == "Physician"){
	        $clinic = $form->getElementById('clinic_field')->setPOST('wpClinic');
	        $provision = $form->getElementById('provision_field')->setPOST('wpProvision');
	        $specialty = $form->getElementById('specialty_field')->setPOST('wpSpecialty');
            if($_POST['wpSpecialty'] == "Other (Specify)"){
                $specialty = $form->getElementById('other_specialty_field')->setPOST('wpSpecialty');
            }
	        $years = $form->getElementById('years_field')->setPOST('wpYears');
        }
        if($_POST['wpRole'] == "Pharmacist"){
	        $provision = $form->getElementById('disclosure_field')->setPOST('wpDisclosure');
	        $pharmacy_name = $form->getElementById('pharmacy_name_field')->setPOST('wpPharmacyName');
	        $pharmacy_address = $form->getElementById('pharmacy_address_field')->setPOST('wpPharmacyAddress');
        }
        if($_FILES['file_field']['size'] < $max_file_size*1024*1024){
	        $file = $_FILES['file_field']['tmp_name'];           
            $content = chunk_split(base64_encode(file_get_contents($file)));
            $uid = md5(uniqid(time()));
            $name = basename($file);
            $msg = "First name: ".$_POST['wpFirstName']."\n";
            $msg .= "Last name: ".$_POST['wpLastName']."\n";
            $msg .= "Email: ".$_POST['wpEmail']."\n";
            $msg .= "Role: ".$_POST['wpRole']."\n";
            $msg .= "Postal Code: ".$_POST['wpPostalCode']."\n";
            $msg .= "City: ".$_POST['wpCity']."\n";
            $msg .= "Province: ".$_POST['wpProvince']."\n";
            if($_POST['wpRole'] == "Physician"){
                $msg .= "Specialty: ".$_POST['wpSpecialty']."\n";
                $msg .= "Clinic Name: ".$_POST['wpClinic']."\n";
                $msg .= "Provision: ".$_POST['wpProvision']."\n";
            }
            if($_POST['wpRole'] == "Pharmacist"){
                $msg .= "Share Pharmacy Agreement?: ".$_POST['wpDisclosure']."\n";
                if($_POST['wpDisclosure'] == "I agree"){
                    $msg .= "Pharmacy Name: ".$_POST['wpPharmacyName']."\n";
                    $msg .= "Pharmacy Address: ".$_POST['wpPharmacyAddress']."\n";
                }
            }
            $msg .= "Reference: ".$_POST['wpReference']."\n";
            // header
            $header = "From: ".$_POST['wpFirstName']." ".$_POST['wpLastName']." <".$_POST['wpEmail'].">\r\n";
            $header .= "Reply-To: ".$_POST['wpEmail']."\r\n";
            $header .= "MIME-Version: 1.0\r\n";
            $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";

            // message & attachment
            $nmessage = "--".$uid."\r\n";
            $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
            $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $nmessage .= $msg."\r\n\r\n";
            $nmessage .= "--".$uid."\r\n";
            $nmessage .= "Content-Type: ".$_FILES['file_field']['type']."; name=\""."credentials"."\"\r\n";
            $nmessage .= "Content-Transfer-Encoding: base64\r\n";
            $nmessage .= "Content-Disposition: attachment; filename=\""."credentials"."\"\r\n\r\n";
            $nmessage .= $content."\r\n\r\n";
            $nmessage .= "--".$uid."--";

           if (mail("rdejesus@ualberta.ca", "New CAPS registration", $nmessage, $header)) {

                $_POST['wpRealName'] = "{$_POST['wpFirstName']} {$_POST['wpLastName']}";
                $_POST['wpName'] = ucfirst(str_replace("&#39;", "", strtolower($_POST['wpFirstName']))).".".ucfirst(str_replace("&#39;", "", strtolower($_POST['wpLastName'])));
                $_POST['wpSendMail'] = "true";
                $_POST['candidate'] = "1";
                
                if(!preg_match("/^[À-Ÿa-zA-Z\-]+\.[À-Ÿa-zA-Z\-]+$/", $_POST['wpName'])){
                    $wgMessage->addError("This User Name is not in the format 'FirstName.LastName'");
                }
                else{
                    $result = APIRequest::doAction('RequestUser', false);
                    if($result){
                        $form->reset();
                        $wgMessage->addSuccess("A request has been sent.");
                        redirect("$wgServer$wgScriptPath");
                    }
                }             
            } 
            else {
              return false;
            }
        }
        else{
            $wgMessage->addError("The file cannot be larger than {$max_file_size}MB");
        }
        }
        CAPSRegister::generateFormHTML($wgOut);
    }

}

?>
