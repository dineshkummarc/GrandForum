<?php

class InternationalPressAPI extends PaperAPI{
	
	function InternationalPressAPI($update=false){
	    parent::PaperAPI($update, "International News", "Press");
	    $this->addPOST("description",false,"The description of the Press","My Description");
	    $this->addPOST("date",false,"The date of when this article was published","2010-10-10");
	    $this->addPOST("url",false,"The URL of the press article","http://www.edmontonsun.com/");
	}
}

?>
