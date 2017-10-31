<?php

class AssignReviewersAPI extends API{

    var $errors = "";
    var $typeSet = false;

    function AssignReviewersAPI(){
        $this->addPOST("users", true, "The user names of the users involved with this Material, separated by commas","First1.Last1, First2.Last2, First3.Last3");
        $this->addPOST("student_id", true, "The user names of the users involved with this Material, separated by commas","First1.Last1, First2.Last2, First3.Last3");
    }

    function processParams($params){
        $users = explode(",", $_POST['users']);
        $_POST['users'] = array();
        foreach($users as $user){
            $person = Person::newFromName(trim($user))->getId();
            if($person != null && $person->getName() != null){
                $_POST['users'][] = $person->getId();
            }
            else{
                $_POST['users'][] = trim($user);
            }           
        }
        $_POST['student_id'] = $_POST['student_id'];
    }

    function doAction($noEcho=false){
	global $wgRequest, $wgUser, $wgServer, $wgScriptPath, $wgMessage;
        $me = Person::newFromId($wgUser->getId());
        if(!$me->isRoleAtLeast(MANAGER)){
            return;
        }
        if(!isset($_POST['users']) || count($_POST['users']) == 0){
            $_POST['users'] = array();
        }
        $ids = array();
        foreach($_POST['users'] as $name){
            $reviewer = Person::newFromName($name);
            $ids[] = $reviewer->getId();
        }
        $student = $_POST['student_id'];
        $sql = "DELETE FROM grand_eval WHERE sub_id = '$student'";
        $status = DBFunctions::execSQL($sql, true);
        foreach($ids as $reviewer_id){
            $year = YEAR;
            $sql = "INSERT INTO grand_eval VALUES ('$reviewer_id', '$student', 'sop', '$year')";
            $status = DBFunctions::execSQL($sql, true);
            if($status){
                                DBFunctions::commit();
            }

        }
        return true;
    }
	
	
    function isLoginRequired(){
		return true;
    }
}

?>
