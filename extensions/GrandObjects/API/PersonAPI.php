<?php

class PersonAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam('id') != ""){
            $person = Person::newFromId($this->getParam('id'));
            if($person == null || $person->getName() == ""){
                $this->throwError("This user does not exist");
            }
            return $person->toJSON();
        }
        else{
            $people = new Collection(Person::getAllPeople('all'));
            return $people->toJSON();
        }
    }
    
    function doPOST(){
        $person = new Person(array());
        $person->email = $this->POST('email');
        $person->name = $this->POST('name');
        $person->twitter = $this->POST('twitter');
        $person->gender = $this->POST('gender');
        $person->publicProfile = $this->POST('publicProfile');
        $person->privateProfile = $this->POST('privateProfile');
        $person->nationality = $this->POST('nationality');
        if($person->exists()){
            $this->throwError("A user by the name of <i>{$person->getName()}</i> already exists");
        }
        $status = $person->create();
        if(!$status){
            $this->throwError("The user <i>{$person->getName()}</i> could not be created");
        }
        $person = Person::newFromName($person->getName());
        return $person->toJSON();
    }
    
    function doPUT(){
        $person = Person::newFromId($this->getParam('id'));
        if($person == null || $person->getName() == ""){
            $this->throwError("This user does not exist");
        }
        $person->name = $this->POST('name');
        $person->realname = $this->POST('realName');
        $person->email = $this->POST('email');
        $person->name = $this->POST('name');
        $person->twitter = $this->POST('twitter');
        $person->gender = $this->POST('gender');
        $person->publicProfile = $this->POST('publicProfile');
        $person->privateProfile = $this->POST('privateProfile');
        $person->nationality = $this->POST('nationality');
        $status = $person->update();
        if(!$status){
            $this->throwError("The user <i>{$person->getName()}</i> could not be updated");
        }
        $person = Person::newFromId($this->getParam('id'));
        return $person->toJSON();
    }
    
    function doDELETE(){
        $person = Person::newFromId($this->getParam('id'));
        if($person == null || $person->getName() == ""){
            $this->throwError("This user does not exist");
        }
        $status = $person->delete();
        if(!$status){
            $this->throwError("The user <i>{$person->getName()}</i> could not be deleted");
        }
    }
}

class PersonProjectsAPI extends RESTAPI {

    function doGET(){
        $person = Person::newFromId($this->getParam('id'));
        $json = array();
        $projects = $person->getProjects(true);
        foreach($projects as $project){
            $json[] = array('projectId' => $project->getId(),
                            'personId' => $person->getId(),
                            'startDate' => $project->getJoinDate($person),
                            'endDate' => $project->getEndDate($person));
        }
        return json_encode($json);
    }
    
    function doPOST(){
        return doGET();
    }
    
    function doPUT(){
        return doGET();
    }
    
    function doDELETE(){
        return doGET();
    }
}

class PersonRolesAPI extends RESTAPI {

    function doGET(){
        $person = Person::newFromId($this->getParam('id'));
        $json = array();
        $roles = $person->getRoles(true);
        foreach($roles as $role){
            $json[] = array('roleId' => $role->getId(),
                            'personId' => $person->getId(),
                            'startDate' => $role->getStartDate(),
                            'endDate' => $role->getEndDate());
        }
        return json_encode($json);
    }
    
    function doPOST(){
        return doGET();
    }
    
    function doPUT(){
        return doGET();
    }
    
    function doDELETE(){
        return doGET();
    }
}

class PersonProductAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam(0) == "person"){
            $person = Person::newFromId($this->getParam('id'));
            $json = array();
            $products = $person->getPapersAuthored("all", false, false, false); 
            foreach($products as $product){
                $json[] = array('productId' => $product->getId(), 
                                'personId'=>$this->getParam('id'),
                                'startDate' => $product->getDate(),
                                'endDate' => $product->getDate());
            }
            return json_encode($json);
        }
        else if($this->getParam(0) == "product"){
            $product = Paper::newFromId($this->getParam('id'));
            $json = array();
            $authors = $product->getAuthors(); 
            foreach($authors as $author){
                if($author->getId()){
                    $json[] = array('productId' => $this->getParam('id'), 
                                    'personId' => $author->getId(),
                                    'startDate' => $product->getDate(),
                                    'endDate' => $product->getDate());
                }
            }
            return json_encode($json);
        }
        return null;
    }
    
    function doPOST(){
       
    }
    
    function doPUT(){
        
    }
    
    function doDELETE(){
       
    }
    
}


?>
