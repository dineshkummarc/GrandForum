<?php

class DeviceAPI extends PaperAPI{
	
	function DeviceAPI($update=false){
	    parent::PaperAPI($update, "Device/Machine", "Artifact");
	    $this->addPOST("description",false,"The description of the artifact","My Description");
	    $this->addPOST("date",false,"The date this artifact was published","2010-10-15");
	    $this->addPOST("status",false,"The status of the artifact.  Can be either 'Peer Reviewed' or 'Not Peer Reviewed'","Peer Reviewed");
	}
}

?>
