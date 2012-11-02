<?php

class ManualAPI extends PaperAPI{
	
	function ManualAPI($update=false){
	    parent::PaperAPI($update, "Manual", "Publication");
	    $this->addPOST("abstract",false,"The abstract of the publication","My Abstract");
	    $this->addPOST("date",false,"The date this publication was published, in the form YYYY-MM-DD","2010-10-15");
	    $this->addPOST("status",false,"The status of the publication.  Can be either Submitted,Under Revision,Published,Rejected","Submitted");
	    $this->addPOST("pages",false,"The page numbers where this publication is located","183-194");
	    $this->addPOST("volume",false,"The volume of the manual","3");
	    $this->addPOST("edition",false,"The edition of the manual","2");
	    $this->addPOST("series",false,"The series of the manual","Computing Explained");
	    $this->addPOST("publisher",false,"The name of the publisher","My Publishing Company");
	    $this->addPOST("isbn",false,"The ISBN of the publication","90-70002-34-5");
	    $this->addPOST("issn",false,"The ISSN of the publication","90-70002-34-5");
	    $this->addPOST("doi",false,"The doi of the publication","10.1000/182");
      $this->addPOST("url",false,"Link to a copy of the publication","http://mySite.org/myPaper.pdf");
      $this->addPOST("peer_reviewed",false,"Whether or not the publication is peer reviewed","Yes");
	}	
}
?>
