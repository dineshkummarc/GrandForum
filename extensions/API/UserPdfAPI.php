<?php

class UserPdfAPI extends API{

    function UserPdfAPI(){
    }

    function processParams($params){
    }

    function doAction($noEcho=false){
        global $wgUser;
        $me = Person::newFromId($wgUser->getId());
        $user_id = $_GET["user"];
        $year = (isset($_GET["year"]) && $_GET["year"] != "") ? "_{$_GET["year"]}" : "";
        if($me->isRoleAtLeast(EVALUATOR) || $me->getId() == $user_id){
            $data = DBFunctions::select(array("grand_gsms{$year}"),
                                        array('pdf_contents'),
                                        array('user_id' => EQ($user_id)));
	        $pdf = @gzinflate($data[0]['pdf_contents']);
	        header('Content-type: application/pdf');
	        echo $pdf;
	    }
    }
    
    function isLoginRequired(){
        return true;
    }
}
?>