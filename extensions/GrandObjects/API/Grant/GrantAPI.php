<?php

class GrantAPI extends RESTAPI {
    
    function doGET(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = Grant::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant does not exist");
            }
            return $grant->toJSON();
        }
        else{
            $grants = new Collection(Grant::getAllGrants());
            return $grants->toJSON();
        }
    }
    
    function doPOST(){
        $grant = new Grant(array());
        $grant->user_id = $this->POST('user_id');
        $grant->project_id = $this->POST('project_id');
        $grant->sponsor = $this->POST('sponsor');
        $grant->total = $this->POST('total');
        $grant->funds_before = $this->POST('funds_before');
        $grant->funds_after = $this->POST('funds_after');
        $grant->title = $this->POST('title');
        $grant->description = $this->POST('description');
        $grant->role = $this->POST('role');
        $grant->seq_no = $this->POST('seq_no');
        $grant->prog_description = $this->POST('prog_description');
        $grant->request = $this->POST('request');
        $grant->start_date = $this->POST('start_date');
        $grant->end_date = $this->POST('end_date');
        $grant->contributions = $this->POST('contributions');
        $grant->create();
        return $grant->toJSON();
    }
    
    function doPUT(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = Grant::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant does not exist");
            }
            $grant->user_id = $this->POST('user_id');
            $grant->project_id = $this->POST('project_id');
            $grant->sponsor = $this->POST('sponsor');
            $grant->total = $this->POST('total');
            $grant->funds_before = $this->POST('funds_before');
            $grant->funds_after = $this->POST('funds_after');
            $grant->title = $this->POST('title');
            $grant->description = $this->POST('description');
            $grant->role = $this->POST('role');
            $grant->seq_no = $this->POST('seq_no');
            $grant->prog_description = $this->POST('prog_description');
            $grant->request = $this->POST('request');
            $grant->start_date = $this->POST('start_date');
            $grant->end_date = $this->POST('end_date');
            $grant->contributions = $this->POST('contributions');
            $grant->update();
            return $grant->toJSON();
        }
        return $this->doGET();
    }
    
    function doDELETE(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = Grant::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant does not exist");
            }
            $grant->delete();
            return $grant->toJSON();
        }
        return $this->doGET();
    }
	
}

?>