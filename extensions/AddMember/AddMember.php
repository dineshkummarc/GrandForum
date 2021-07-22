<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['AddMember'] = 'AddMember'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['AddMember'] = $dir . 'AddMember.i18n.php';
$wgSpecialPageGroups['AddMember'] = 'network-tools';

$wgHooks['ToolboxLinks'][] = 'AddMember::createToolboxLinks';

autoload_register('AddMember/Validations');

class AddMember extends SpecialPage{

    function AddMember() {
        parent::__construct("AddMember", NI.'+', true);
    }

    function execute($par){
        global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
        $user = Person::newFromId($wgUser->getId());
        if(isset($_GET['action']) && $_GET['action'] == "view" && $user->isRoleAtLeast(STAFF)){
            if(isset($_POST['submit']) && $_POST['submit'] == "Accept"){
                $request = UserCreateRequest::newFromId($_POST['id']);
                /*$sendEmail = "false";
                if(isset($_POST['wpEmail']) && $_POST['wpEmail'] != ""){
                    $sendEmail = "true";
                }
                $_POST['wpSendMail'] = "$sendEmail";*/
                $result = APIRequest::doAction('CreateUser', false);
                if(strstr($result, "already exists") === false){
                    $request->acceptRequest();
                }
            }
            else if(isset($_POST['submit']) && $_POST['submit'] == "Ignore"){
                $request = UserCreateRequest::newFromId($_POST['id']);
                $request->ignoreRequest();
            }
            AddMember::generateViewHTML($wgOut);
        }
        else if(!isset($_POST['submit'])){
            // Form not entered yet
            AddMember::generateFormHTML($wgOut);
        }
        else{
            $form = self::createForm();
            $status = $form->validate();
            if($status){
                $form->getElementById('first_name_field')->setPOST('wpFirstName');
                $form->getElementById('middle_name_field')->setPOST('wpMiddleName');
                $form->getElementById('last_name_field')->setPOST('wpLastName');
                $form->getElementById('email_field')->setPOST('wpEmail');
                $_POST['wpSendEmail'] = (count(@$_POST['sendEmail_field']) > 0) ? implode("", $_POST['sendEmail_field']) : "false";
                $form->getElementById('role_field')->setPOST('wpUserType');
                $form->getElementById('project_field')->setPOST('wpNS');
                $form->getElementById('nationality_field')->setPOST('nationality');
                $form->getElementById('employment_field')->setPOST('employment');
                for($i = 0; $i < 3; $i++){
                    $form->getElementById("university_field{$i}")->setPOST("university{$i}");
                    $form->getElementById("dept_field{$i}")->setPOST("department{$i}");
                    $form->getElementById("position_field{$i}")->setPOST("position{$i}");
                    $form->getElementById("end_field{$i}")->setPOST("end_date{$i}");
                    $form->getElementById("start_field{$i}")->setPOST("start_date{$i}");
                }
                
                $_POST['university'] = "{$_POST["university0"]}\n{$_POST["university1"]}\n{$_POST["university2"]}";
                $_POST['department'] = "{$_POST["department0"]}\n{$_POST["department1"]}\n{$_POST["department2"]}";
                $_POST['position'] = "{$_POST["position0"]}\n{$_POST["position1"]}\n{$_POST["position2"]}";
                $_POST['end_date'] = "{$_POST["end_date0"]}\n{$_POST["end_date1"]}\n{$_POST["end_date2"]}";
                $_POST['start_date'] = "{$_POST["start_date0"]}\n{$_POST["start_date1"]}\n{$_POST["start_date2"]}";
                
                $form->getElementById('cand_field')->setPOST('candidate');
                
                if(isset($_POST['wpNS'])){
                    $nss = implode(", ", array_unique($_POST['wpNS']));
                }
                else{
                    $nss = "";
                }
                if(isset($_POST['wpUserType'])){
                    $types = implode(", ", $_POST['wpUserType']);
                }
                else{
                    $types = "";
                }

                $_POST['wpRealName'] = "{$_POST['wpFirstName']} {$_POST['wpLastName']}";
                $_POST['wpName'] = str_replace(" ", "", str_replace("&#39;", "", $_POST['wpFirstName']).".".str_replace("&#39;", "", $_POST['wpLastName']));
                $_POST['user_name'] = $user->getName();
                $_POST['wpUserType'] = $types;
                $_POST['wpNS'] = $nss;
                $result = APIRequest::doAction('RequestUser', false);
                if($result){
                    $form->reset();
                }
            }
            AddMember::generateFormHTML($wgOut);
            return;
        }
    }
    
    function generateViewHTML($wgOut){
        global $wgScriptPath, $wgServer, $config, $wgEnableEmail;
        $history = false;
        if(isset($_GET['history']) && $_GET['history'] == true){
            $history = true;
        }
        $hqpType = "";
        if(count($config->getValue('subRoles')) > 0 && !$history){
            $hqpType = "<th>".Inflect::pluralize($config->getValue('subRoleTerm'))."</th>";
        }
        if($history){
            $wgOut->addHTML("<a href='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view'>View New Requests</a><br /><br />
                        <table id='requests' style='display:none;background:#ffffff;text-align:center;' cellspacing='1' cellpadding='3' frame='box' rules='all'>
                        <thead><tr bgcolor='#F2F2F2'>
                            <th>Requesting User</th>
                            <th>User Name</th>
                            <th>Timestamp</th>
                            <th>Staff</th>
                            <th>User Type</th>
                            <th>Projects</th>
                            <th>Institution</th>
                            <th>Candidate</th>
                            <th>Action</th>
                        </tr></thead><tbody>\n");
        }
        else{
            $wgOut->addHTML("<a href='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view&history=true'>View History</a><br /><br />
                        <table id='requests' style='display:none;background:#ffffff;text-align:center;' cellspacing='1' cellpadding='3' frame='box' rules='all'>
                        <thead><tr bgcolor='#F2F2F2'>
                            <th>Requesting User</th>
                            <th>User Name</th>
                            <th>Timestamp</th>
                            <th>User Type</th>
                            <th>Projects</th>
                            <th>Institution</th>
                            {$hqpType}
                            <th>Candidate</th>
                            <th>Action</th>
                        </tr></thead><tbody>\n");
        }
    
        $requests = UserCreateRequest::getAllRequests($history);
        foreach($requests as $request){
            $req_user = $request->getRequestingUser();
            $projects = $req_user->getProjects();
            $projs = array();
            if(count($projects) > 0){
                foreach($projects as $project){
                    if(!$project->isSubProject()){
                        $subprojs = array();
                        foreach($project->getSubProjects() as $subproject){
                            if($req_user->isMemberOf($subproject)){
                                $subprojs[] = "<a href='{$subproject->getUrl()}'>{$subproject->getName()}</a>";
                            }
                        }
                        $subprojects = "";
                        if(count($subprojs) > 0){
                            $subprojects = "(".implode(", ", $subprojs).")";
                        }
                        $projs[] = "<a href='{$project->getUrl()}'>{$project->getName()}</a> $subprojects";
                    }
                }
            }
            $roles = array();
            if($req_user->getRoles() != null){
                foreach($req_user->getRoles() as $role){
                    $roles[$role->getRole()] = $role->getRole();
                }
            }
            $wgOut->addHTML("<tr><form action='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view' method='post'>
                        <td align='left'>
                            <a target='_blank' href='{$req_user->getUrl()}'><b>{$req_user->getName()}</b></a> (".implode(",", $roles).")<br /><a onclick='$(\"#{$request->id}\").slideToggle();$(this).remove();' style='cursor:pointer;'>Show Projects</a>
                            <div id='{$request->id}' style='display:none;padding-left:15px;'>".implode("<br />", $projs)."</div>
                        </td>");
            if($history && $request->isCreated()){
                $user = Person::newFromName($request->getName());
                $wgOut->addHTML("<td align='left'><a target='_blank' href='{$user->getUrl()}'>{$request->getName()}</a></td>");
            }
            else{
                $wgOut->addHTML("<td align='left'>{$request->getName()}<br />{$request->getEmail()}</td>");
            } 
            $wgOut->addHTML("<td>".str_replace(" ", "<br />", $request->getLastModified())."</td>");
            if($history){
                $wgOut->addHTML("<td><a target='_blank' href='{$request->getAcceptedBy()->getUrl()}'>{$request->getAcceptedBy()->getName()}</a></td>");
            }
            $wgOut->addHTML("<td>{$request->getRoles()}</td>
                             <td align='left'>{$request->getProjects()}</td>
                             <td>".str_replace("\n", ", ", trim($request->getUniversity()))."<br />
                                 ".str_replace("\n", ", ", trim($request->getDepartment()))."<br />
                                 ".str_replace("\n", ", ", trim($request->getPosition()))."</td> ");
            if(count($config->getValue('subRoles')) > 0 && !$history){
                $wgOut->addHTML("<td align='left' style='white-space:nowrap;'>");
                foreach($config->getValue('subRoles') as $subRole => $fullSubRole){
                    $wgOut->addHTML("<input type='checkbox' name='subtype[]' value='{$subRole}' />{$fullSubRole}<br />");
                }
                $wgOut->addHTML("</td>");
            }
            $wpSendMail = ($wgEnableEmail) ? $request->getSendEmail() : "false";
            $wgOut->addHTML("
                        <td>{$request->getCandidate(true)}</td>
                            <input type='hidden' name='id' value='{$request->getId()}' />
                            <input type='hidden' name='wpName' value='{$request->getName()}' />
                            <input type='hidden' name='wpEmail' value='{$request->getEmail()}' />
                            <input type='hidden' name='wpRealName' value='{$request->getRealName()}' />
                            <input type='hidden' name='wpFirstName' value='{$request->getFirstName()}' />
                            <input type='hidden' name='wpMiddleName' value='{$request->getMiddleName()}' />
                            <input type='hidden' name='wpLastName' value='{$request->getLastName()}' />
                            <input type='hidden' name='wpUserType' value='{$request->getRoles()}' />
                            <input type='hidden' name='wpNS' value='{$request->getProjects()}' />
                            <input type='hidden' name='candidate' value='{$request->getCandidate()}' />
                            <input type='hidden' name='nationality' value='".str_replace("'", "&#39;", $request->getNationality())."' />
                            <input type='hidden' name='employment' value='".str_replace("'", "&#39;", $request->getEmployment())."' />
                            <input type='hidden' name='university' value='".str_replace("'", "&#39;", $request->getUniversity())."' />
                            <input type='hidden' name='department' value='".str_replace("'", "&#39;", $request->getDepartment())."' />
                            <input type='hidden' name='position' value='".str_replace("'", "&#39;", $request->getPosition())."' />
                            <input type='hidden' name='start_date' value='".str_replace("'", "&#39;", $request->getStartDate())."' />
                            <input type='hidden' name='end_date' value='".str_replace("'", "&#39;", $request->getEndDate())."' />
                            <input type='hidden' name='wpSendMail' value='$wpSendMail' />");
            if($history){
                if($request->isCreated()){
                    $wgOut->addHTML("<td>Accepted</td>");
                }
                else{
                    $wgOut->addHTML("<td>Ignored</td>");
                }
            }
            else{
                $wgOut->addHTML("<td><input type='submit' name='submit' value='Accept' /><br /><input style='margin-top:2px;' type='submit' name='submit' value='Ignore' /></td>");
            }
            $wgOut->addHTML("</form>
                    </tr>");
        }
        $wgOut->addHTML("</tbody></table><script type='text/javascript'>
                                            $('#requests').dataTable({'autoWidth': false}).fnSort([[2,'desc']]);
                                            $('#requests').css('display', 'table');
                                         </script>");
    }
    
    function createForm(){
        global $wgRoles, $wgUser, $config;
        $me = Person::newFromUser($wgUser);
        $committees = $config->getValue('committees');
        $aliases = $config->getValue('roleAliases');
        
        $formContainer = new FormContainer("form_container");
        $formTable = new FormTable("form_table");
        
        $firstNameLabel = new Label("first_name_label", "First Name", "The first name of the user", VALIDATE_NOT_NULL);
        $firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOT_NULL);
        $firstNameRow = new FormTableRow("first_name_row");
        $firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
        
        $middleNameLabel = new Label("middle_name_label", "Middle Name", "The middle name of the user", VALIDATE_NOTHING);
        $middleNameField = new TextField("middle_name_field", "Middle Name", "", VALIDATE_NOTHING);
        $middleNameRow = new FormTableRow("middle_name_row");
        $middleNameRow->append($middleNameLabel)->append($middleNameField->attr('size', 20));
        
        $lastNameLabel = new Label("last_name_label", "Last Name", "The last name of the user", VALIDATE_NOT_NULL);
        $lastNameField = new TextField("last_name_field", "Last Name", "", VALIDATE_NOT_NULL);
        $lastNameField->registerValidation(new SimilarUserValidation(VALIDATION_POSITIVE, VALIDATION_WARNING));
        $lastNameField->registerValidation(new UniqueUserValidation(VALIDATION_POSITIVE, VALIDATION_ERROR));
        $lastNameRow = new FormTableRow("last_name_row");
        $lastNameRow->append($lastNameLabel)->append($lastNameField->attr('size', 20));
        
        $emailLabel = new Label("email_label", "Email", "The email address of the user", VALIDATE_NOT_NULL);
        $emailField = new EmailField("email_field", "Email", "", VALIDATE_NOT_NULL);
        $emailField->registerValidation(new UniqueEmailValidation(VALIDATION_POSITIVE, VALIDATION_WARNING));
        $emailRow = new FormTableRow("email_row");
        $emailRow->append($emailLabel)->append($emailField);
        
        $sendEmailLabel = new CustomElement("sendEmail_label", "", "", "");
        $sendEmailField = new VerticalCheckBox("sendEmail_field", "Email", array("true"), array("Send Registration Email?" => "true"), VALIDATE_NOTHING);
        $sendEmailRow = new FormTableRow("sendEmail_row");
        $sendEmailRow->append($sendEmailLabel)->append($sendEmailField);
        
        $roleValidations = VALIDATE_NOT_NULL;
        if($me->isRoleAtLeast(STAFF)){
            $roleValidations = VALIDATE_NOTHING;
        }
        $roleOptions = array();
        foreach($me->getAllowedRoles() as $role){
            $roleOptions[$config->getValue('roleDefs', $role)] = $role;
        }
        ksort($roleOptions);
        $rolesLabel = new Label("role_label", "Roles", "The roles the new user should belong to", $roleValidations);
        $rolesLabel->attr('style', 'width:160px;');
        $rolesField = new VerticalCheckBox("role_field", "Roles", array(), $roleOptions, $roleValidations);
        $rolesRow = new FormTableRow("role_row");
        $rolesRow->append($rolesLabel)->append($rolesField);

        $projects = Project::getAllProjects();
        foreach($projects as $key => $project){
            if($project->getStatus() == "Proposed"){
                unset($projects[$key]);
            }
        }

        $universities = array_merge(array(""), Person::getAllUniversities());
        $departments = array_merge(array(""), Person::getAllDepartments());
        $positions = array("", "Graduate Student - Master's", "Graduate Student - Doctoral", "Post-Doctoral Fellow", "Research Associate", "Research Assistant", "Technician", "Professional End User", "Summer Student", "Undergraduate Student");
        
        $candLabel = new Label("cand_label", "Candidate?", "Whether or not this user should be a candidate (not officially in the network yet)", VALIDATE_NOTHING);
        $candField = new VerticalRadioBox("cand_field", "Roles", "No", array("0" => "No", "1" => "Yes"), VALIDATE_NOTHING);
        $candRow = new FormTableRow("cand_row");
        $candRow->append($candLabel)->append($candField);
        if(!$me->isRoleAtLeast(STAFF)){
            $candRow->attr('style', 'display:none;');
        }
               
        $projectsLabel = new Label("project_label", "Associated Projects", "The projects the user is a member of", VALIDATE_NOTHING);
        $projectsField = new ProjectList("project_field", "Associated Projects", array(), $projects, VALIDATE_NOTHING);
        $projectsRow = new FormTableRow("project_row");
        $projectsRow->append($projectsLabel)->append($projectsField);
        
        $nationalityLabel = new Label("nationality_label", "Nationality", "The nationality of this user (only required for HQP)", VALIDATE_NOTHING);
        $nationalityField = new SelectBox("nationality_field", "Nationality", "", array("" => "---", 
                                                                                        "Canadian" => "Canadian/Landed Immigrant", 
                                                                                        "Foreign"), VALIDATE_NOTHING);
        $nationalityField->attr("style", "width: 260px;");
        $nationalityRow = new FormTableRow("nationality_row");
        $nationalityRow->append($nationalityLabel)->append($nationalityField);
        $nationalityRow->attr('id', 'nationality_row');
        
        $employmentLabel1 = new Label("employment_label1", "Please select institution type of employment (if applicable)", "", VALIDATE_NOTHING);
        $employmentLabel1->colspan = 2;
        $employmentLabel1->attr('style', 'text-align:left;max-width:400px;');
        $employmentField = new SelectBox("employment_field", "Please select institution type of employment (if applicable)", "", array("", "University", "Industry", "Government", "Hospital", "Other"), VALIDATE_NOTHING);
        $employmentLabel2 = new Label("employment_label2", "Employment", "Please select institution type of employment (if applicable)", VALIDATE_NOTHING);
        $employmentField->attr("style", "width: 260px;");
        $employmentRow1 = new FormTableRow("employment_row1");
        $employmentRow1->append($employmentLabel1);
        $employmentRow1->attr('id', 'employment_row1');
        $employmentRow2 = new FormTableRow("employment_row2");
        $employmentRow2->append($employmentLabel2)->append($employmentField);
        $employmentRow2->attr('id', 'employment_row2');

        $submitCell = new EmptyElement();
        $submitField = new SubmitButton("submit", "Submit Request", "Submit Request", VALIDATE_NOTHING);
        $submitRow = new FormTableRow("submit_row");
        $submitRow->append($submitCell)->append($submitField);
        
        $formTable->append($firstNameRow)
                  ->append($middleNameRow)
                  ->append($lastNameRow)
                  ->append($emailRow)
                  ->append($sendEmailRow)
                  ->append($rolesRow)
                  ->append($projectsRow)
                  ->append($nationalityRow);
        for($i = 0; $i < 3; $i++){
            $extraText = "";
            if($i == 0 && $config->getValue("networkName") == "MtS"){
                $year = date('Y', time() - 3*30);
                $nextYear = $year+1;
                $extraText = "If applicable, please list the start and expected end-date of educational or fellowship programs personnel is (1) currently pursuing, and/or (2) will begin within this fiscal year, and/or (3) will end this fiscal year (March {$year}-{$nextYear}):<br />";
            }
            $programLabel = new Label("program_label{$i}", "{$extraText}Program ".($i+1)." (can leave blank if N/A)", "", VALIDATE_NOTHING);
            $programLabel->colon = "";
            $programLabel->colspan = 2;
            $programLabel->attr('style', 'text-align:left;max-width:400px;');
            $programRow = new FormTableRow("program_row{$i}");
            $programRow->append($programLabel);
            $programRow->attr('id', "program_row$i");
            
            $defaultUniversity = ($i == 0) ? $me->getUni() : "";
            $universityLabel = new Label("university_label$i", "Institution", "The intitution that the user is a member of", VALIDATE_NOTHING);
            $universityField = new ComboBox("university_field$i", "Instutution", $defaultUniversity, $universities, VALIDATE_NOTHING);
            $universityField->attr("style", "width: 250px;");
            $universityRow = new FormTableRow("university_row$i");
            $universityRow->append($universityLabel)->append($universityField);
            $universityRow->attr('id', "university_row$i");
            
            $defaultDepartment = ($i == 0) ? $me->getDepartment() : "";
            $deptLabel = new Label("dept_label$i", $config->getValue('deptsTerm'), "The ".strtolower($config->getValue('deptsTerm'))." of this user", VALIDATE_NOTHING);
            $deptField = new ComboBox("dept_field$i", $config->getValue('deptsTerm'), $defaultDepartment, $departments, VALIDATE_NOTHING);
            $deptField->attr("style", "width: 250px;");
            $deptRow = new FormTableRow("dept_row$i");
            $deptRow->append($deptLabel)->append($deptField);
            $deptRow->attr('id', "dept_row$i");
            
            $positionLabel = new Label("position_label$i", "Position", "The academic title of this user (only required for HQP)", VALIDATE_NOTHING);
            $positionField = new SelectBox("position_field$i", "Position", "", $positions, VALIDATE_NOTHING);
            $positionField->attr("style", "width: 260px;");
            $positionRow = new FormTableRow("position_row$i");
            $positionRow->append($positionLabel)->append($positionField);
            $positionRow->attr('id', "position_row$i");
            
            $startLabel = new Label("start_label$i", "Start Date", "When the member's role, project, institution should take effect", VALIDATE_NOTHING);
            $startField = new CalendarField("start_field$i", "Start Date", date('Y-m-d'), VALIDATE_NOTHING);
            $startRow = new FormTableRow("start_row$i");
            $startRow->append($startLabel)->append($startField);
            $startRow->attr('id', "start_row$i");
            
            $endLabel = new Label("end_label$i", "End Date", "When the member's role, project, institution should end (if currently active, just leave blank.)", VALIDATE_NOTHING);
            $endField = new CalendarField("end_field$i", "End Date", "", VALIDATE_NOTHING);
            $endRow = new FormTableRow("end_row$i");
            $endRow->append($endLabel)->append($endField);
            $endRow->attr('id', "end_row$i");

            $formTable->append($programRow)
                      ->append($universityRow)
                      ->append($deptRow)
                      ->append($positionRow)
                      ->append($startRow)
                      ->append($endRow);
        }
        $formTable->append($employmentRow1)
                  ->append($employmentRow2)
                  ->append($candRow)
                  ->append($submitRow);
                  
        if(!$me->isRoleAtLeast(STAFF)){
            $formTable->getElementById("cand_row")->attr('style', 'display:none;');
        }
        
        $formContainer->append($formTable);
        return $formContainer;
    }
    
    function generateFormHTML($wgOut){
        global $wgUser, $wgServer, $wgScriptPath, $wgRoles;
        $user = Person::newFromId($wgUser->getId());
        if($user->isRoleAtLeast(STAFF)){
            $wgOut->addHTML("<b><a href='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view'>View Requests</a></b><br /><br />");
        }
        $wgOut->addHTML("Adding a member to the forum will allow them to access content relevant to the user roles and projects which are selected below.  By selecting projects, the user will be automatically added to the projects on the forum, and subscribed to the project mailing lists.  The new user's email must be provided as it will be used to send a randomly generated password to the user.  After pressing the 'Submit Request' button, an administrator will be able to accept the request.  If there is a problem in the request (ie. there was an obvious typo in the name), then you may be contacted by the administrator about the request.<br /><br />");
        $wgOut->addHTML("<form action='$wgScriptPath/index.php/Special:AddMember' method='post'>\n");
        
        $form = self::createForm();
        $wgOut->addHTML($form->render());
        $wgOut->addHTML("<script type='text/javascript'>
            var fn = function(){
                var found = false;
                var otherFound = false;
                $.each($('input[name=\"role_field[]\"]:checked'), function(id, el){
                    found = (found || $(el).val() == '".HQP."');
                    otherFound = (otherFound || $(el).val() != '".HQP."');
                });
                if(found){
                    // HQP
                    $('#program_row0').show();
                    $('#program_row1').show();
                    $('#program_row2').show();
                    
                    $('#position_row0').show();
                    $('#position_row1').show();
                    $('#position_row2').show();
                    
                    $('#university_row1').show();
                    $('#university_row2').show();
                    
                    $('#dept_row1').show();
                    $('#dept_row2').show();
                    
                    $('#start_row1').show();
                    $('#start_row2').show();
                    
                    $('#end_row1').show();
                    $('#end_row2').show();
                    
                    $('#nationality_row').show();
                    
                    $('#employment_row1').show();
                    $('#employment_row2').show();
                }
                else{
                    // Not HQP
                    $('#program_row0').hide();
                    $('#program_row1').hide();
                    $('#program_row2').hide();
                    
                    $('#position_row0').hide();
                    $('#position_row1').hide();
                    $('#position_row2').hide();
                    
                    $('#university_row1').hide();
                    $('#university_row2').hide();
                    
                    $('#dept_row1').hide();
                    $('#dept_row2').hide();
                    
                    $('#start_row1').hide();
                    $('#start_row2').hide();
                    
                    $('#end_row1').hide();
                    $('#end_row2').hide();
                    
                    $('#nationality_row').hide();
                    
                    $('#employment_row1').hide();
                    $('#employment_row2').hide();
                }
                $('#roleWarning').remove();
                if(found && otherFound){
                    $('#role_label').after('<div id=\'roleWarning\' style=\'width:156px;\' class=\'inlineWarning\'>HQP should not be selected with any other role.  Are you sure you want to proceed?</div>');
                }
            }
            $('input[name=\"role_field[]\"]').change(fn);
            fn();
        </script>");
        $wgOut->addHTML("</form>");
    }
    
    static function createToolboxLinks(&$toolbox){
        global $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        if($me->isRoleAtLeast(NI)){
            $toolbox['People']['links'][-1] = TabUtils::createToolboxLink("Add Member", "$wgServer$wgScriptPath/index.php/Special:AddMember");
        }
        return true;
    }
}

?>
