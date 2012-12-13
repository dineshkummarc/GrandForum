<?php

class ProjectAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam('id') != ""){
            $project = Project::newFromId($this->getParam('id'));
            if($project == null || $project->getName() == ""){
                $this->throwError("This project does not exist");
            }
            return $project->toJSON();
        }
        else{
            $projects = new Collection(Project::getAllProjects());
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
