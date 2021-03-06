<?php

class ProjectAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam('id') != ""){
            $project = Project::newFromId($this->getParam('id'));
            if($this->getParam('id') == "-1"){
                $project->name = "Other";
            }
            if($project == null || $project->getName() == ""){
                $project = Project::newFromName($this->getParam('id'));
                if($project == null || $project->getName() == ""){
                    $this->throwError("This project does not exist");
                }
            }
            return $project->toJSON();
        }
        else{
            $projects = new Collection(Project::getAllProjectsEver(true));
            return $projects->toJSON();
        }
    }
    
    function doPOST(){
        
    }
    
    function doPUT(){

    }
    
    function doDELETE(){

    }
	
}

?>
