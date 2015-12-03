<?php

/**
 * @package GrandObjects
 */

class Role extends BackboneModel {

    static $cache = array();
    static $projectCache = null;

	var $id;
	var $user;
    var $role;
    var $title;
    var $startDate;
    var $endDate;
    var $comment;
    var $projects = null;
	
	// Returns a new Role from the given id
	static function newFromId($id){
	    if(isset(self::$cache[$id])){
	        return self::$cache[$id];
	    }
	    $data = DBFunctions::select(array('grand_roles'),
	                                array('*'),
	                                array('id' => $id));
		$role = new Role($data);
        self::$cache[$role->id] = &$role;
		return $role;
	}
	
	static function generateProjectsCache(){
	    if(self::$projectCache == null){
	        $data = DBFunctions::select(array('grand_role_projects'),
	                                    array('*'));
	        self::$projectCache = array();
	        foreach($data as $row){
	            self::$projectCache[$row['role_id']][] = $row['project_id'];
	        }
	    }
	}
	
	// Constructor
	function Role($data){
		if(count($data) > 0){
			$this->id = $data[0]['id'];
			$this->user = $data[0]['user_id'];
			$this->role = $data[0]['role'];
			$this->title = $data[0]['title'];
			$this->startDate = $data[0]['start_date'];
			$this->endDate = $data[0]['end_date'];
			$this->comment = $data[0]['comment'];
		}
	}
	
	function toArray(){
	    $json = array('id' => $this->getId(),
	                  'userId' => $this->user,
	                  'name' => $this->getRole(),
	                  'fullName' => $this->getRoleFullName(),
	                  'title' => $this->getTitle(),
	                  'comment' => $this->getComment(),
	                  'startDate' => $this->getStartDate(),
	                  'endDate' => $this->getEndDate());
	    return $json;
	}
	
	function create(){
	    $me = Person::newFromWgUser();
	    $person = $this->getPerson();
	    MailingList::unsubscribeAll($this->getPerson());
	    $status = DBFunctions::insert('grand_roles',
	                                  array('user_id'    => $this->user,
	                                        'role'       => $this->getRole(),
	                                        'start_date' => $this->getStartDate(),
	                                        'end_date'   => $this->getEndDate(),
	                                        'comment'    => $this->getComment()),
	                                  array('id' => EQ($this->getId())));
	    Cache::delete("personRolesDuring".$this->getPerson()->getId(), true);
	    Role::$cache = array();
	    Person::$rolesCache = array();
	    $this->getPerson()->roles = null;
	    if($status){
            $data = DBFunctions::select(array('grand_roles'),
                                        array('id'),
                                        array('user_id' => EQ($this->user),
                                              'role' => EQ($this->getRole())),
                                        array('id' => 'DESC'));
            if(count($data) > 0){
                $id = $data[0]['id'];
                $this->id = $id;
                Notification::addNotification($me, $person, "Role Added", "Effective {$this->getStartDate()} you assume the role '{$this->getRole()}'", "{$person->getUrl()}");
                $supervisors = $person->getSupervisors();
                if(count($supervisors) > 0){
                    foreach($supervisors as $supervisor){
                        Notification::addNotification($me, $supervisor, "Role Added", "Effective {$this->getStartDate()} {$person->getReversedName()} assumes the role '{$this->getRole()}'", "{$person->getUrl()}");
                    }
                }
            }
        }
        
        MailingList::subscribeAll($this->getPerson());
	    return $status;
	}
	
	function update(){
	    MailingList::unsubscribeAll($this->getPerson());
	    $status = DBFunctions::update('grand_roles',
	                                  array('role'       => $this->getRole(),
	                                        'start_date' => $this->getStartDate(),
	                                        'end_date'   => $this->getEndDate(),
	                                        'comment'    => $this->getComment()),
	                                  array('id' => EQ($this->getId())));
	    Cache::delete("personRolesDuring".$this->getPerson()->getId(), true);
	    Role::$cache = array();
	    Person::$rolesCache = array();
	    $this->getPerson()->roles = null;
	    
        MailingList::subscribeAll($this->getPerson());
	    return $status;
	}
	
	function delete(){
	    $me = Person::newFromWgUser();
	    $person = $this->getPerson();
	    MailingList::unsubscribeAll($this->getPerson());
	    $status = DBFunctions::delete('grand_roles',
	                                  array('id' => EQ($this->getId())));
	    Cache::delete("personRolesDuring".$this->getPerson()->getId(), true);
	    Role::$cache = array();
	    Person::$rolesCache = array();
	    $this->getPerson()->roles = null;
	    if($status){
	        Notification::addNotification($me, $person, "Role Removed", "You are no longer '{$this->getRole()}'", "{$person->getUrl()}");
	        $supervisors = $person->getSupervisors();
            if(count($supervisors) > 0){
                foreach($supervisors as $supervisor){
                    Notification::addNotification($me, $supervisor, "Role Removed", "{$person->getReversedName()} is no longer '{$this->getRole()}'", "{$person->getUrl()}");
                }
            }
	    }
        MailingList::subscribeAll($this->getPerson());
	    return false;
	}
	
	function exists(){
	
	}
	
	function getCacheId(){
	
	}
	
	// Returns all distinct roles
	static function getDistinctRoles(){
	    global $config;
	    return $config->getValue('roleDefs');
	}

	// Returns whether this Role is still active or not
	function isStillActive(){
	    return($this->startDate > $this->endDate);
	}
	
	// Returns the Person who this Role belongs to
	function getUser(){
	    return Person::newFromId($this->user);
	}
	
	// Alias for getUser()
	function getPerson(){
	    return $this->getUser();
	}
	
	// Returns the name of this Role
	function getRole(){
	    return $this->role;
	}
	
	// Returns the full name of this Role
	function getRoleFullName(){
	    global $config;
	    return $config->getValue('roleDefs', $this->getRole());
	}
	
	function getTitle(){
	    return $this->title;
	}
	
	// Returns the startDate for this Role
	function getStartDate(){
	    return $this->startDate;
	}
	
	// Returns the endDate for this Role
	function getEndDate(){
	    return $this->endDate;
	}
	
	// Returns the comment for this Role
	function getComment(){
	    return $this->comment;
	}
	
	function getProjects(){
	    if($this->projects == null){
	        self::generateProjectsCache();
	        $this->projects = array();
	        if(isset(self::$projectCache[$this->getId()])){
	            foreach(self::$projectCache[$this->getId()] as $project){
	                $this->projects[] = Project::newFromId($project);
	            }
	        }
	    }
	    return $this->projects;
	}
	
	function hasProject($project){
	    $projects = $this->getProjects();
	    if(count($projects) == 0){
	        return true;
	    }
	    foreach($projects as $proj){
	        if($proj->getId() == $project->getId()){
	            return true;
	        }
	    }
	    return false;
	}
}
?>
