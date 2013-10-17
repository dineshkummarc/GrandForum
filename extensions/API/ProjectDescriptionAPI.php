<?php

class ProjectDescriptionAPI extends API{

    function ProjectDescriptionAPI(){
        $this->addPOST("project",true,"The name of the project","MEOW");
	    $this->addPOST("description",true,"The description for this project","MEOW is great");
        $this->addPOST("problem",true,"The description for this projects problem","Here is what we need to solve");
        $this->addPOST("solution",true,"The description for this projects proposed solution","Here is how we are going to solve it");
	    $this->addPOST("themes",false,"The theme distribution of this project", "10,20,30,20,20");
	    $this->addPOST("fullName",false,"The full name of the project", "Media Enabled Organizational Workflow");
    }

    function processParams($params){
        if(isset($_POST['description']) && $_POST['description'] != ""){
            $_POST['description'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['description'])));
        }
        if(isset($_POST['problem']) && $_POST['problem'] != ""){
            $_POST['problem'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['problem'])));
        }
        else{
            $_POST['problem'] = "";
        }
        if(isset($_POST['solution']) && $_POST['solution'] != ""){
            $_POST['solution'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['solution'])));
        }else{
            $_POST['solution'] = "";
        }
        if(isset($_POST['project']) && $_POST['project'] != ""){
            $_POST['project'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['project'])));
        }
        if(isset($_POST['fullName']) && $_POST['fullName'] != ""){
            $_POST['fullName'] = @addslashes(str_replace("<", "&lt;", str_replace(">", "&gt;", $_POST['fullName'])));
        }
    }

	function doAction($noEcho=false){
		$project = Project::newFromName($_POST['project']);
		if(!$noEcho){
		    if($project == null || $project->getName() == null){
		        echo "A valid project must be provided\n";
		        exit;
		    }
		    $person = Person::newFromName($_POST['user_name']);
            $isLead = false;
            foreach($me->getLeadProjects() as $p){
                if($p->getId() == $project->getId()){
                    $isLead = true;
                    break;
                }
            }
            if(!$isLead){
                echo "You must be logged in as a project leader\n";
                exit;
            }
		}
		
		if(isset($_POST['themes'])){
		    $themes = explode(",", $_POST['themes']);
		}
		else{
		    $themes = array($project->getTheme(1), $project->getTheme(2), $project->getTheme(3), $project->getTheme(4), $project->getTheme(5));
		}
		
		if(isset($_POST['fullName'])){
		    $fullName = $_POST['fullName'];
		}
		else{
		    $fullName = @addslashes($project->getFullName());
		}
        DBFunctions::begin();
        $sql = "UPDATE grand_project_descriptions
                SET `end_date` = CURRENT_TIMESTAMP
                WHERE project_id = '{$project->getId()}' AND id = '{$project->getLastHistoryId()}'";
        DBFunctions::execSQL($sql, true);
        $sql = "INSERT INTO grand_project_descriptions (`project_id`,`evolution_id`,`full_name`,`themes`,`description`,`problem`,`solution`,`start_date`)
                VALUES ('{$project->getId()}','{$project->evolutionId}','{$fullName}','{$themes[0]}\n{$themes[1]}\n{$themes[2]}\n{$themes[3]}\n{$themes[4]}','{$_POST['description']}','{$_POST['problem']}','{$_POST['solution']}',CURRENT_TIMESTAMP)";
        DBFunctions::execSQL($sql, true);
        if(!$noEcho){
            echo "Project description updated\n";
        }
	}
	
	function isLoginRequired(){
		return true;
	}
}
?>
