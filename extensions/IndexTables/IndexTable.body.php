<?php

require_once("InactiveUsers.php");

$indexTable = new IndexTable();

$wgHooks['OutputPageParserOutput'][] = array($indexTable, 'generateTable');
$wgHooks['userCan'][] = array($indexTable, 'userCanExecute');

class IndexTable {

	var $text = "";
	
	function userCanExecute(&$title, &$user, $action, &$result){
	    global $wgOut, $wgServer, $wgScriptPath;
	    if($title->getNSText() == "GRAND"){
	        $me = Person::newFromUser($user);
	        $text = $title->getText();
	        switch ($title->getText()) {
	            case 'ALL '.HQP:
				case 'Multimedia Stories':
				    $result = $me->isLoggedIn();
	                break;
				case 'Forms':
				    $result = $me->isRoleAtLeast(MANAGER);
				    break;
	        }
	    }
	    return true;
	}

	function generateTable($out, $parseroutput){
		global $wgTitle, $wgOut, $wgUser;
		$me = Person::newFromId($wgUser->getId());
		
		if($wgTitle != null && $wgTitle->getNsText() == "GRAND" && !$wgOut->isDisabled()){
		    $result = true;
		    $this->userCanExecute($wgTitle, $wgUser, "read", $result);
		    if(!$result){
	            $wgOut->loginToUse();
		        $wgOut->output();
		        $wgOut->disable();
			    return true;
	        }
		    $wgOut->addScript("<script type='text/javascript'>
                $(document).ready(function(){
                    $('.indexTable').css('display', 'table');
                    $('.dataTables_filter').css('float', 'none');
                    $('.dataTables_filter').css('text-align', 'left');
                    $('.dataTables_filter input').css('width', 250);
                });
            </script>");
			switch ($wgTitle->getText()) {
			    case 'ALL '.HQP:
			        $wgOut->setPageTitle("Highly Qualified People");
				    $this->generatePersonTable(HQP);
				    break;
			    case 'ALL '.PNI:
			        $wgOut->setPageTitle("Principal Network Investigators");
				    $this->generatePersonTable(PNI, 1);
				    break;
				case 'ALL '.CNI:
			        $wgOut->setPageTitle("Collaborating Network Investigators");
				    $this->generatePersonTable(CNI);
				    break;
				case 'ALL '.ISAC:
			        $wgOut->setPageTitle("ISAC Members");
				    $this->generatePersonTable(ISAC);
				    break;
				case 'ALL '.EXTERNAL:
			        $wgOut->setPageTitle("External Members");
				    $this->generatePersonTable(EXTERNAL);
				    break;
				case 'ALL '.NCE:
			        $wgOut->setPageTitle("NCE Reps");
				    $this->generatePersonTable(NCE);
				    break;
				case 'ALL '.RMC:
			        $wgOut->setPageTitle("Research Management Committee");
				    $this->generateRMCTable();
				    break;
				case 'Multimedia Stories':
				    $wgOut->setPageTitle("Multimedia Stories");
				    $this->generateMaterialsTable();
				    break;
				case 'Forms':
				    if($me->isRoleAtLeast(MANAGER)){
				        $wgOut->setPageTitle("Forms");
				        $this->generateFormsTable();
				    }
				    break;
			    case 'Projects':
			        $wgOut->setPageTitle("Current Projects");
				    $this->generateProjectsTable('Active');
				    break;
				case 'CompletedProjects':
			        $wgOut->setPageTitle("Completed Projects");
				    $this->generateProjectsTable('Ended');
				    break;
			    case 'Themes':
			        $wgOut->setPageTitle("Themes");
				    $this->generateThemesTable();
				    break;
			    default:
				    return true;
			}
			TabUtils::clearActions();
			$wgOut->addHTML($this->text);
			$wgOut->output();
			$wgOut->disable();
		}
		return true;
	}
	
	/*
	 * Generates the Table for the projects
	 * Consists of the following columns
	 * Acronym | Name 
	 */
	private function generateProjectsTable($status){
		global $wgScriptPath, $wgServer, $wgOut, $wgUser;
        $me = Person::newFromId($wgUser->getId());
        $idHeader = "";
        if($me->isRoleAtLeast(MANAGER)){
            $idHeader = "<th>Project Id</th>";
        }
        $data = Project::getAllProjectsEver();
	    $this->text .= "
            <table class='indexTable' style='display:none;' frame='box' rules='all'>
            <thead>
            <tr><th>Acronym</th><th>Name</th>$idHeader</tr></thead><tbody>";
	    foreach($data as $proj){
	        if($proj->getStatus() == $status){
	            $this->text .= "
                    <tr>
                    <td align='left'><a href='{$proj->getUrl()}'>{$proj->getName()}</a></td>
                    <td align='left'>{$proj->getFullName()}</td>";
                if($me->isRoleAtLeast(MANAGER)){
                    $this->text .= "<td>{$proj->getId()}</td>\n";
                }
                $this->text .= "</tr>\n";
            }
	    }
	    $this->text .= "</tbody></table>";
		$this->text .= "</div><script type='text/javascript'>$('.indexTable').dataTable({'iDisplayLength': 100});</script>";

		return true;
	}
	
	/*
	 * Generates the Table for the themes
	 * Consists of the following columns
	 * Theme | Name 
	 */
	private function generateThemesTable(){
		global $wgScriptPath, $wgServer;
		$this->text .=
"<table class='indexTable' style='display:none;' frame='box' rules='all'>
<thead><tr><th>Themes</th><th>Name</th></tr></thead><tbody>
";
        $themes = Theme::getAllThemes(PROJECT_PHASE);
		foreach($themes as $theme){
			$this->text .= <<<EOF
<tr>
<td align='left'>
<a href='{$wgServer}{$wgScriptPath}/index.php/GRAND:{$theme->getAcronym()} - {$theme->getName()}'>{$theme->getAcronym()}</a>
</td><td align='left'>
{$theme->getName()}
</td></tr>
EOF;
		}
		$this->text .= "</tbody></table><script type='text/javascript'>$('.indexTable').dataTable({'iDisplayLength': 100});</script>";

		return true;
	}
	
	/**
	 * Generates the Table for the Network Investigators, Collaborating
	 * Researchers, or Highly-Qualified People, depending on parameter
	 * #table.
	 * Consists of the following columns
	 * User Page | Projects | Twitter
	 */
	private function generatePersonTable($table){
		global $wgServer, $wgScriptPath, $wgUser, $wgOut;
		$me = Person::newFromId($wgUser->getId());
		$data = Person::getAllPeople($table);
		$idHeader = "";
        if($me->isRoleAtLeast(MANAGER)){
            $idHeader = "<th width='0%' style='white-space: nowrap;'>User Id</th>";
        }
        $this->text .= "Below are all the current $table in GRAND.  To search for someone in particular, use the search box below.  You can search by name, project or university.<br /><br />";
		$this->text .= "<table class='indexTable' style='display:none;' frame='box' rules='all'>
<thead><tr><th width='15%' style='white-space: nowrap;'>Name</th><th width='65%' style='white-space: nowrap;'>Projects</th><th width='20%' style='white-space: nowrap;'>University</th>$idHeader</tr></thead><tbody>
";
		foreach($data as $person){
		    $projects = $person->getProjects();
			$this->text .= "
<tr>
<td align='left' style='white-space: nowrap;'>
<a href='{$person->getUrl()}'>{$person->getReversedName()}</a>
</td>
<td align='left'>
";
            $projs = array();
			foreach($projects as $project){
			    if(!$project->isSubProject() && ($project->getPhase() == PROJECT_PHASE)){
				    $subprojs = array();
				    foreach($project->getSubProjects() as $subproject){
				        if($person->isMemberOf($subproject)){
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
			$this->text .= implode("<br />", $projs);
            $this->text .= "</td><td align='left'>";
            $university = $person->getUniversity();
            $this->text .= $university['university'];
			$this->text .= "</td>";
			if($me->isRoleAtLeast(MANAGER)){
			    $this->text .= "<td>{$person->getId()}</td>";
			}
			$this->text .= "</tr>";
		}
		$this->text .= "</tbody></table><script type='text/javascript'>$('.indexTable').dataTable({'iDisplayLength': 100, 'bAutoWidth': false});</script>";

		return true;
	}
	
	private function generateRMCTable(){
		global $wgServer, $wgScriptPath, $wgUser, $wgOut;
		$data = Person::getAllPeople(RMC);

        $this->text .= "Below are all the current ".RMC." in GRAND.  To search for someone in particular, use the search box below.  You can search by name, project or university.<br /><br />";
		$this->text .= "<table class='indexTable' style='display:none;' frame='box' rules='all'>
<thead><tr><th>Name</th><th>Roles</th></tr></thead><tbody>";
		foreach($data as $person){
		    $roles = $person->getRoles();
			$this->text .= "
<tr>
<td align='left'>
<a href='{$person->getUrl()}'>{$person->getReversedName()}</a>
</td>
<td align='left'>
";
            foreach($roles as $role){
				$this->text .= "{$role->getRole()}, ";
			}
			if(count($person->getRoles()) > 0){
				$pos = strrpos($this->text, ", ");
				$this->text = substr($this->text, 0, $pos);
			}
			$this->text .= "</tr>";
		}
		$this->text .= "</tbody></table><script type='text/javascript'>$('.indexTable').dataTable({'iDisplayLength': 100});</script>";
		return true;
	}

	function generateMaterialsTable(){
	    global $wgServer, $wgScriptPath;
	    $this->text = "<table class='indexTable' style='display:none;' frame='box' rules='all'>
<thead><tr><th>Date</th><th style='min-width:300px;'>Title</th><th>Type</th><th>People</th><th>Projects</th></tr></thead><tbody>";
        $materials = Material::getAllMaterials();
        foreach($materials as $material){
            $this->text .= "<tr><td>{$material->getDate()}</td><td><a href='{$material->getUrl()}'>{$material->getTitle()}</a></td><td>{$material->getHumanReadableType()}</td>";
            $projs = array();
            foreach($material->getProjects() as $project){
                $projs[] = "<a href='{$project->getUrl()}'>{$project->getName()}</a>";
            }
            $personLinks = array();
            foreach($material->getPeople() as $person){
                if($person->getType() != ""){
                    $personLinks[] = "<a href='{$person->getUrl()}'>{$person->getNameForForms()}</a>";
                }
                else{
                    $personLinks[] = "{$person->getName()}";
                }
            }
            $this->text .= "<td>".implode(", ", $personLinks)."</td>";
            $this->text .= "<td>".implode(", ", $projs)."</td>";
            $this->text .= "</tr>";
        }
        $this->text .= "</tbody></table>";
        $this->text .= "<script type='text/javascript'>
	        $(document).ready(function(){
	            $('.indexTable').dataTable({'iDisplayLength': 100});
	            $('.indexTable').dataTable().fnSort([[0,'desc']]);
	        });
	    </script>";
	    return true;
	}
	
	function generateFormsTable(){
	    global $wgServer, $wgScriptPath;
	    $this->text = "<table class='indexTable' style='display:none;' frame='box' rules='all'>
<thead><tr><th>Date</th><th style='min-width:300px;'>Title</th><th>Person</th><th>University</th><th>Project</th></tr></thead><tbody>";
        $forms = Form::getAllForms();
        foreach($forms as $form){
            $personName = "";
            $person = $form->getPerson();
            if($person != null && $person->getName() != ""){
                $personName = "<a href='{$person->getType()}'>{$person->getReversedName()}</a>";
            }

            $university = $form->getUniversity();

            $projectName = "";
            $project = $form->getProject();
            if($project != null && $project->getName() != ""){
                $projectName = "<a href='{$project->getUrl()}'>{$project->getName()}</a>";
            }
            $this->text .= "<tr><td>{$form->getDate()}</td><td><a href='$wgServer$wgScriptPath/index.php/Form:{$form->getId()}'>{$form->getTitle()}</a></td><td>{$personName}</td><td>{$university}</td><td>{$projectName}</td>";
            
            $this->text .= "</tr>";
        }
        $this->text .= "</tbody></table>";
        $this->text .= "<script type='text/javascript'>
	        $(document).ready(function(){
	            $('.indexTable').dataTable({'iDisplayLength': 100});
	            $('.indexTable').dataTable().fnSort([[0,'desc']]);
	        });
	    </script>";
	    return true;
	}
}

?>
