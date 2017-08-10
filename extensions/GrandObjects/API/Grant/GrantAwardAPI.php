<?php

class GrantAwardAPI extends RESTAPI {
    
    function doGET(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = GrantAward::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant Award does not exist");
            }
            return $grant->toJSON();
        }
        else{
            $grants = new Collection(GrantAward::getAllGrantAwards());
            return $grants->toJSON();
        }
    }
    
    function doPOST(){
        $grant = new GrantAward(array());
        $grant->user_id = $this->POST('user_id');
        $grant->grant_id = $this->POST('grant_id');
        $grant->department = $this->POST('department');
        $grant->organization = $this->POST('organization');
        $grant->institution = $this->POST('institution');
        $grant->province = $this->POST('province');
        $grant->country = $this->POST('country');
        $grant->fiscal_year = $this->POST('fiscal_year');
        $grant->competition_year = $this->POST('competition_year');
        $grant->amount = $this->POST('amount');
        $grant->program_id = $this->POST('program_id');
        $grant->program_name = $this->POST('program_name');
        $grant->group = $this->POST('group');
        $grant->committee_code = $this->POST('committee_code');
        $grant->committee_name = $this->POST('committee_name');
        $grant->area_of_application_code = $this->POST('area_of_application_code');
        $grant->area_of_application_group = $this->POST('area_of_application_group');
        $grant->area_of_application = $this->POST('area_of_application');
        $grant->research_subject_code = $this->POST('research_subject_code');
        $grant->research_subject_group = $this->POST('research_subject_group');
        $grant->installment = $this->POST('installment');
        $grant->partie = $this->POST('partie');
        $grant->nb_partie = $this->POST('nb_partie');
        $grant->application_title = $this->POST('application_title');
        $grant->keyword = $this->POST('keyword');
        $grant->application_summary = $this->POST('application_summary');
        //$grant->coapplicants = $this->POST('coapplicants');
        $grant->create();
        return $grant->toJSON();
    }
    
    function doPUT(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = GrantAward::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant Award does not exist");
            }
            $grant->grant_id = $this->POST('grant_id');
            $grant->department = $this->POST('department');
            $grant->organization = $this->POST('organization');
            $grant->institution = $this->POST('institution');
            $grant->province = $this->POST('province');
            $grant->country = $this->POST('country');
            $grant->fiscal_year = $this->POST('fiscal_year');
            $grant->competition_year = $this->POST('competition_year');
            $grant->amount = $this->POST('amount');
            $grant->program_id = $this->POST('program_id');
            $grant->program_name = $this->POST('program_name');
            $grant->group = $this->POST('group');
            $grant->committee_code = $this->POST('committee_code');
            $grant->committee_name = $this->POST('committee_name');
            $grant->area_of_application_code = $this->POST('area_of_application_code');
            $grant->area_of_application_group = $this->POST('area_of_application_group');
            $grant->area_of_application = $this->POST('area_of_application');
            $grant->research_subject_code = $this->POST('research_subject_code');
            $grant->research_subject_group = $this->POST('research_subject_group');
            $grant->installment = $this->POST('installment');
            $grant->partie = $this->POST('partie');
            $grant->nb_partie = $this->POST('nb_partie');
            $grant->application_title = $this->POST('application_title');
            $grant->keyword = $this->POST('keyword');
            $grant->application_summary = $this->POST('application_summary');
            //$grant->coapplicants = $this->POST('coapplicants');
            $grant->update();
            return $grant->toJSON();
        }
        return $this->doGET();
    }
    
    function doDELETE(){
        $id = $this->getParam('id');
        if($id != ""){
            $grant = GrantAward::newFromId($id);
            if($grant == null || $grant->getId() == 0){
                $this->throwError("This Grant Award does not exist");
            }
            $grant->delete();
            return $grant->toJSON();
        }
        return $this->doGET();
    }
	
}

?>
