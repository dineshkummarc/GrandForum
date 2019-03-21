<?php

class ProjectAddress extends Address {

    var $project;
    var $phone;
    var $fax;
    var $email;
    var $twitter;
    var $facebook;
    var $linkedin;
    var $youtube;

    /**
     * Returns a new Address from the given id
     * @param integer $id The id of the entry in the DB
     * @return Address The Address from the given id
     */
    static function newFromId($id){
        $data = DBFunctions::select(array('grand_project_contact'),
                                    array('*'),
                                    array('id' => EQ($id)));
        return new ProjectAddress($data);
    }
    
    function ProjectAddress($data){
        if(count($data) > 0){
            $this->id = $data[0]['id'];
            $this->project = Project::newFromId($data[0]['proj_id']);
            $this->type = $data[0]['type'];
            $this->line1 = $data[0]['line1'];
            $this->line2 = $data[0]['line2'];
            $this->line3 = $data[0]['line3'];
            $this->line4 = $data[0]['line4'];
            $this->line5 = $data[0]['line5'];
            $this->city = $data[0]['city'];
            $this->province = $data[0]['province'];
            $this->country = $data[0]['country'];
            $this->code = $data[0]['code'];
            $this->start_date = $data[0]['start_date'];
            $this->end_date = $data[0]['end_date'];
            $this->primary = $data[0]['primary_indicator'];
            $this->phone = $data[0]['phone'];
            $this->fax = $data[0]['fax'];
            $this->email = $data[0]['email'];
            $this->twitter = $data[0]['twitter'];
            $this->facebook = $data[0]['facebook'];
            $this->linkedin = $data[0]['linkedin'];
            $this->youtube = $data[0]['youtube'];
        }
    }

    /**
     * Returns the Project that this Address is for
     * @return Project The Project that this Address is for
     */
    function getProject(){
        return $this->project;
    }
    
    function getPhone(){
        return $this->phone;
    }
    
    function getFax(){
        return $this->fax;
    }
    
    function getEmail(){
        return $this->email;
    }
    
    function getTwitter(){
        return $this->twitter;
    }
    
    function getFacebook(){
        return $this->facebook;
    }
    
    function getLinkedIn(){
        return $this->linkedin;
    }
    
    function getYoutube(){
        return $this->youtube;
    }
    
}

?>
