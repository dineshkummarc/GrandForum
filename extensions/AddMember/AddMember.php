<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['AddMember'] = 'AddMember'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['AddMember'] = $dir . 'AddMember.i18n.php';
$wgSpecialPageGroups['AddMember'] = 'grand-tools';

function runAddMember($par) {
  AddMember::run($par);
}

autoload_register('AddMember/Validations');

class AddMember extends SpecialPage{

	function AddMember() {
		wfLoadExtensionMessages('AddMember');
		if(FROZEN){
		    SpecialPage::SpecialPage("AddMember", STAFF.'+', true, 'runAddMember');
	    }
	    else{
	        SpecialPage::SpecialPage("AddMember", CNI.'+', true, 'runAddMember');
	    }
	}

	function run($par){
		global $wgOut, $wgUser, $wgServer, $wgScriptPath, $wgTitle, $wgMessage;
		$user = Person::newFromId($wgUser->getId());
		if(isset($_GET['action']) && $_GET['action'] = "view" && $user->isRoleAtLeast(STAFF)){
			if(isset($_POST['submit']) && $_POST['submit'] == "Accept"){
			    $request = UserCreateRequest::newFromId($_POST['id']);
			    $sendEmail = "false";
			    if(isset($_POST['wpEmail']) && $_POST['wpEmail'] != ""){
			        $sendEmail = "true";
			    }
                $_POST['wpSendMail'] = "$sendEmail";
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
		        $form->getElementById('last_name_field')->setPOST('wpLastName');
		        $form->getElementById('email_field')->setPOST('wpEmail');
		        $form->getElementById('role_field')->setPOST('wpUserType');
		        $form->getElementById('project_field')->setPOST('wpNS');
		        
		        if(isset($_POST['wpNS'])){
			        $nss = implode(", ", $_POST['wpNS']);
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
		        
		        $_POST['wpFirstName'] = ucfirst($_POST['wpFirstName']);
		        $_POST['wpLastName'] = ucfirst($_POST['wpLastName']);
		        $_POST['wpName'] = ucfirst(strtolower($_POST['wpFirstName'])).".".ucfirst(strtolower($_POST['wpLastName']));
			    $_POST['wpRealName'] = "{$_POST['wpFirstName']} {$_POST['wpLastName']}";
			    $_POST['user_name'] = $user->getName();
			    $_POST['wpUserType'] = $types;
			    $_POST['wpNS'] = $nss;
			    
		        APIRequest::doAction('RequestUser', false);
		        
		        $form->reset();
		    }
		    AddMember::generateFormHTML($wgOut);
		    return;
		}
	}
	
	function generateViewHTML($wgOut){
		global $wgScriptPath, $wgServer;
		$history = false;
		if(isset($_GET['history']) && $_GET['history'] == true){
		    $history = true;
		}
		if($history){
		    $wgOut->addHTML("<a href='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view'>View New Requests</a><br /><br />
		                <table id='requests' style='display:none;background:#ffffff;text-align:center;' cellspacing='1' cellpadding='3' frame='box' rules='all'>
					    <thead><tr bgcolor='#F2F2F2'>
						    <th>Requesting User</th> <th>User Name</th> <th>Timestamp</th> <th>Staff</th> <th>Email</th> <th>User Type</th> <th>Projects</th> <th>Status</th>
					    </tr></thead><tbody>\n");
		}
		else{
		    $wgOut->addHTML("<a href='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view&history=true'>View History</a><br /><br />
		                <table id='requests' style='display:none;background:#ffffff;text-align:center;' cellspacing='1' cellpadding='3' frame='box' rules='all'>
					    <thead><tr bgcolor='#F2F2F2'>
						    <th>Requesting User</th> <th>User Name</th> <th>Timestamp</th> <th>Email</th> <th>User Type</th> <th>Projects</th> <th>Accept</th> <th>Ignore</th>
					    </tr></thead><tbody>\n");
		}
	
	    $requests = UserCreateRequest::getAllRequests($history);

		foreach($requests as $request){
		    $req_user = $request->getRequestingUser();
		    $projects = array();
		    $roles = array();
		    if($req_user->getProjects() != null){
		        foreach($req_user->getProjects() as $project){
		            $projects[] = $project->getName();
		        }
		    }
		    if($req_user->getRoles() != null){
		        foreach($req_user->getRoles() as $role){
		            $roles[] = $role->getRole();
		        }
		    }
			$wgOut->addHTML("<tr>
						<td align='left'><a target='_blank' href='{$req_user->getUrl()}'>{$req_user->getName()}</a><br />
						<b>Roles:</b> ".implode(",", $roles)."<br />
						<b>Projects:</b> ".implode(",", $projects)."</td> <td align='left'>{$request->getName()}</td> <td>{$request->getLastModified()}</td>");
			if($history){
			    $wgOut->addHTML("<td>{$request->getAcceptedBy()->getName()}</td>");
			}
			$wgOut->addHTML("<td align='left'> {$request->getEmail()}</td> <td>{$request->getRoles()}</td> <td align='left'>{$request->getProjects()}</td> 
						<form action='$wgServer$wgScriptPath/index.php/Special:AddMember?action=view' method='post'>
							<input type='hidden' name='id' value='{$request->getId()}' />
							<input type='hidden' name='wpName' value='{$request->getName()}' />
							<input type='hidden' name='wpEmail' value='{$request->getEmail()}' />
							<input type='hidden' name='wpRealName' value='{$request->getRealName()}' />
							<input type='hidden' name='wpUserType' value='{$request->getRoles()}' />
							<input type='hidden' name='wpNS' value='{$request->getProjects()}' />
                            <input type='hidden' name='wpSendMail' value='true' />");
			if($history){
		        if($request->isCreated()){
		            $wgOut->addHTML("<td>Accepted</td>");
		        }
		        else{
		            $wgOut->addHTML("<td>Ignored</td>");
		        }
		    }
		    else{
			    $wgOut->addHTML("<td><input type='submit' name='submit' value='Accept' /></td> <td><input type='submit' name='submit' value='Ignore' /></td>");
			}
			$wgOut->addHTML("</form>
					</tr>");
		}
		$wgOut->addHTML("</tbody></table><script type='text/javascript'>
		                                    $('#requests').dataTable().fnSort([[2,'desc']]);
		                                    $('#requests').css('display', 'table');
		                                 </script>");
	}
	
	function createForm(){
	    global $wgRoles, $wgUser;
	    $me = Person::newFromUser($wgUser);
	    $formContainer = new FormContainer("form_container");
		$formTable = new FormTable("form_table");
		
		$firstNameLabel = new Label("first_name_label", "First Name", "The first name of the user (cannot contain spaces)", VALIDATE_NOT_NULL);
		$firstNameField = new TextField("first_name_field", "First Name", "", VALIDATE_NOT_NULL);
		$firstNameRow = new FormTableRow("first_name_row");
		$firstNameRow->append($firstNameLabel)->append($firstNameField->attr('size', 20));
		
		$lastNameLabel = new Label("last_name_label", "Last Name", "The last name of the user (cannot contain spaces)", VALIDATE_NOT_NULL);
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
		
		$roleValidations = VALIDATE_NOT_NULL;
		if($me->isRoleAtLeast(MANAGER)){
		    $roleValidations = VALIDATE_NOTHING;
		}
		$roleOptions = array();
		foreach($wgRoles as $role){
            if($me->isRoleAtLeast($role) && $role != CHAMP){
                $roleOptions[] = $role;
            }
        }
        if($me->isRoleAtLeast(CNI)){
            $roleOptions[] = CHAMP;
        }
		$rolesLabel = new Label("role_label", "Roles", "The roles the new user should belong to", $roleValidations);
		$rolesField = new VerticalCheckBox("role_field", "Roles", array(), $roleOptions, $roleValidations);
		$rolesRow = new FormTableRow("role_row");
		$rolesRow->append($rolesLabel)->append($rolesField);
		
		$projects = Project::getAllProjects();
		$projectOptions = array();
		foreach($projects as $project){
		    $projectOptions[] = $project->getName();
		}
		$projectsLabel = new Label("project_label", "Associated Projects", "The projects the user is a member of", VALIDATE_NOTHING);
		$projectsField = new MultiColumnVerticalCheckBox("project_field", "Associated Projects", array(), $projectOptions, VALIDATE_NOTHING);
		$projectsRow = new FormTableRow("project_row");
		$projectsRow->append($projectsLabel)->append($projectsField);
		
		$submitCell = new EmptyElement();
		$submitField = new SubmitButton("submit", "Submit Request", "Submit Request", VALIDATE_NOTHING);
		$submitRow = new FormTableRow("submit_row");
		$submitRow->append($submitCell)->append($submitField);
		
		$formTable->append($firstNameRow)
		          ->append($lastNameRow)
		          ->append($emailRow)
		          ->append($rolesRow)
		          ->append($projectsRow)
		          ->append($submitRow);
		
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
	}
}

?>
