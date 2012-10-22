<?php

class GrandAccess {

    static function setupGrandAccess($user, &$aRights){
        global $wgRoleValues;
	    $me = Person::newFromId($user->getId());
	    $i = 1000;
	    if(count($me->getProjects()) > 0){
	        foreach($me->getProjects() as $project){
	            $aRights[$i++] = $project->getName();
	        }
	    }
	    if($me->isRole(MANAGER) || $me->name == "Admin"){
	        $aRights[$i++] = RMC;
	        $aRights[$i++] = PL;
	        $aRights[$i++] = COPL;
	    }
	    if(count($me->leadership()) > 0){
	        $aRights[$i++] = "Leadership";
	        $aRights[$i++] = "Leadership+";
	    }
	    if($me->isEvaluator()){
	        $aRights[$i++] = "Evaluator";
	        $aRights[$i++] = "Evaluator+";
	    }
	    if($me->isPNI() || $me->isCNI()){
	        $aRights[$i++] = "Researcher";
	        $aRights[$i++] = "Researcher+";
	    }
	    if($me->isHQP() && $me->isPendingInactivation()){
	        $aRights[$i++] = "HQPPendingInactivation";
	    }
	    foreach(array_keys($wgRoleValues) as $role){
	        if($me->isRoleAtLeast($role)){
	            $aRights[$i++] = $role.'+';
	            if(($role == STAFF || $role == MANAGER) && array_search('Leadership+', $aRights) === false){
	                $aRights[$i++] = 'Leadership+';
	            }
	            if(($role == STAFF || $role == MANAGER) && array_search('Evaluator+', $aRights) === false){
	                $aRights[$i++] = 'Evaluator+';
	            }
	            if(($role == STAFF || $role == MANAGER) && array_search('Researcher+', $aRights) === false){
	                $aRights[$i++] = 'Researcher+';
	            }
	        }
	    }
	    if(count($me->getRoles()) > 0){
	        foreach($me->getRoles() as $role){
	            $aRights[$i++] = $role->getRole();
	            $user->mGroups[] = $role->getRole().'_Wiki';
	        }
	    }
	    if($user->isLoggedIn()){
	        $user->mGroups[] = "Poster";
	        $user->mGroups[] = "Presentation";
	    }
	    return true;
	}
	
}

?>
