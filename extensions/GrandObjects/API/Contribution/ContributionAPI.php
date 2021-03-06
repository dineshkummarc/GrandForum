<?php

class ContributionAPI extends RESTAPI {
    
    function doGET(){
        if($this->getParam('id') != ""){
            if($this->getParam('rev_id') != ""){
                $contribution = Contribution::newFromRevId($this->getParam('rev_id'));
            } else {
                $contribution = Contribution::newFromId($this->getParam('id'));
            }
            if(!$contribution->isAllowedToEdit()){
                $this->throwError("This contribution does not exist");
            }
            return $contribution->toJSON();
        }
        else{
            $contributions = new Collection(Contribution::getAllContributions());
            return $contributions->toJSON();
        }
    }
    
    function doPOST(){
        $me = Person::newFromWgUser();
        if(!$me->isLoggedIn()){
            $this->throwError("You must be logged in to create a new Contribution");
        }
        $contribution = new Contribution(array());
        $contribution->name = $this->POST('name');
        $contribution->description = $this->POST('description');
        $contribution->institution = $this->POST('institution');
        $contribution->province = $this->POST('province');
        $contribution->people = $this->POST('authors');
        $contribution->projects = $this->POST('projects');
        $contribution->partners = $this->POST('partners');
        $contribution->start_date = $this->POST('start');
        $contribution->end_date = $this->POST('end');
        $contribution->create();
        return $contribution->toJSON();
    }
    
    function doPUT(){
        if($this->getParam('rev_id') != ""){
            $contribution = Contribution::newFromRevId($this->getParam('rev_id'));
        } else {
            $contribution = Contribution::newFromId($this->getParam('id'));
        }
        if(!$contribution->isAllowedToEdit()){
            $this->throwError("This contribution does not exist");
        }
        $contribution->name = $this->POST('name');
        $contribution->description = $this->POST('description');
        $contribution->institution = $this->POST('institution');
        $contribution->province = $this->POST('province');
        $contribution->people = $this->POST('authors');
        $contribution->projects = $this->POST('projects');
        $contribution->partners = $this->POST('partners');
        $contribution->start_date = $this->POST('start');
        $contribution->end_date = $this->POST('end');
        $contribution->update();
        return $contribution->toJSON();
    }
    
    function doDELETE(){
        if($this->getParam('rev_id') != ""){
            $contribution = Contribution::newFromRevId($this->getParam('rev_id'));
        } else {
            $contribution = Contribution::newFromId($this->getParam('id'));
        }
        if(!$contribution->isAllowedToEdit()){
            $this->throwError("This contribution does not exist");
        }
        $contribution->delete();
        return $contribution->toJSON();
    }
	
}

?>
