<?php

class EvalOverviewReportItem extends AbstractReportItem {

	function render(){
	    global $wgOut;
        $details = $this->getTableHTML();
        $item = "$details";
        $item = $this->processCData($item);
		$wgOut->addHTML($item);
        $this->setSeenOverview();
	}
	
	function renderForPDF(){
	    global $wgOut;
        $details = $this->getTableHTML();
        $item = "$details";
        $item = $this->processCData($item);
		$wgOut->addHTML($item);
	}
	
	function getTableHTML(){
        global $wgUser;
        $type = $this->getAttr('subType', 'PNI');
	    $person = Person::newFromId($this->personId);
        if($type == "PNI"){
	       $subs = $person->getEvaluatePNIs();
        }
        else if($type == "CNI"){
           $subs = $person->getEvaluateCNIs();
        }
        else if($type == "Project"){
            $subs = $person->getEvaluateProjects();
        }

	    $radio_questions = array(EVL_OVERALLSCORE, EVL_CONFIDENCE, EVL_EXCELLENCE, EVL_HQPDEVELOPMENT, EVL_NETWORKING, EVL_KNOWLEDGE, EVL_MANAGEMENT, EVL_REPORTQUALITY);
        $stock_comments = array(0,0, EVL_EXCELLENCE_COM, EVL_HQPDEVELOPMENT_COM, EVL_NETWORKING_COM, EVL_KNOWLEDGE_COM, EVL_MANAGEMENT_COM, EVL_REPORTQUALITY_COM);
	    $text_question = EVL_OTHERCOMMENTS;
        
        $jscript =<<<EOF
            <style type='text/css'>
                div.details_sub{
                    margin-top: 20px;
                    display: none;
                }
                div.overview_table_heading {
                    text-decoration: underline;
                    font-size: 16px;
                    padding: 10px 0;
                }
                .qtipStyle{
                    font-size: 14px;
                    line-height: 120%;
                    padding: 5px;
                }
                tr.purple_row{
                    background-color: #F3EBF5;
                }
            </style>
            <script type='text/javascript'>
                $('span.q8_tip').qtip({
                    position: {
                        corner: {
                            target: 'center',
                            tooltip: 'center'
                        }
                    }, 
                    style: {
                        classes: 'qtipStyle'
                    }
                });
                $('.comment_dialog').dialog( "destroy" );
                $('.comment_dialog').dialog({ autoOpen: false, width: 400, height: 200 });
                
                function openDialog(ev_id, sub_id, num){
                    $('#dialog'+num+'-'+ev_id+'-'+sub_id).dialog("open");
                }

                function expandSubDetails(sub_id){
                    $('#overview_table tr').removeClass('purple_row');
                    $('#row-'+sub_id).addClass('purple_row');
                    
                    $('.details_sub').hide();
                    $('#details_sub-'+sub_id).show();
                    //$('html, body').animate({
                    //    scrollTop: $('#details_sub-'+sub_id).offset().top
                    //}, 400);
                }
            </script>
EOF;

        $html =<<<EOF
        <div class="overview_table_heading"></div>
        <table id="overview_table" class="dashboard" style="width:100%;background:#ffffff;border-style:solid; text-align:center;" cellspacing="1" cellpadding="3" frame="box" rules="all">
EOF;
       

        $html .=<<<EOF
        	<tr>
        	<th width="20%" align="left">NI Name</th>
            <th width="10%">Q8 (Comments)</th>
        	<th width="10%">Q7</th>
        	<th width="10%">Q9</th>
        	<th style="border-left: 5px double #8C529D;">Q1</th>
        	<th>Q2</th>
        	<th>Q3</th>
        	<th>Q4</th>
        	<th>Q5</th>
        	<th>Q6</th>
        	</tr>
EOF;
        $sub_details = "";

        foreach($subs as $sub){
            $sub_id = $sub->getId();
            if($type == "PNI" || $type == "CNI"){
                $sub_name = $sub->getReversedName();
                $sub_name_straight = $sub->getFirstName(). " " .$sub->getLastName();
                $evals = $sub->getEvaluators($type);
            }
            else if($type == "Project"){
                $sub_name = $sub_name_straight = $sub->getName();
                $evals = $sub->getEvaluators();
            }
            
            $sub_table = "";
            $incomplete = false;
            foreach($evals as $ev){
                $sub_row = "";
            	$ev_id = $ev->getId();
                
            	$ev_name = $ev->getReversedName();
                $ev_name_straight = $ev->getFirstName(). " " .$ev->getLastName();

            	$sub_row .= "<tr id='row-{$sub_id}'>";
                if($wgUser->getId() != $ev_id){
            	   $sub_row .= "<td rowspan='3' align='left'>{$ev_name}</td></tr>";
                }else{
                    $sub_row .= "<td rowspan='3' align='left'><a href='#details_sub-{$sub_id}' onclick='expandSubDetails(\"{$sub_id}\"); return false;' >{$sub_name}</a></td></tr>";
                }

                //Actual Answers
                //foreach(array(0,20) as $add){
                $q8 = $this->blobValue(BLOB_TEXT, $ev_id, $text_question, $sub_id);
                
                $sub_row .= "<tr><td>";
                $sub_row2 = "<tr><td>";
                if(!empty($q8) && is_array($q8)){
                    $q8_O = (isset($q8['original']))? $q8['original'] : "";
                    $q8_R = (isset($q8['revised']))? $q8['revised'] : "";

                    if(!empty($q8_O)){
                        $q8_O = nl2br($q8_O);
                        $sub_row .= "<a href='#' onclick='openDialog(\"{$ev_id}\", \"{$sub_id}\", 1); return false;'>Original</a><div id='dialog1-{$ev_id}-{$sub_id}' class='comment_dialog' title='Original Comment by {$ev_name_straight} on {$sub_name_straight}'>{$q8_O}</div><br />";
                    }
                    else{
                        $sub_row .= "Original";
                    }

                    if(!empty($q8_R)){
                        $q8_R = nl2br($q8_R);
                        $sub_row2 .= "<a href='#' onclick='openDialog(\"{$ev_id}\", \"{$sub_id}\", 2); return false;'>Revised</a><div id='dialog2-{$ev_id}-{$sub_id}' class='comment_dialog' title='Revised Comment by {$ev_name_straight} on {$sub_name_straight}'>{$q8_R}</div><br />";
            	    }
                    else{
                        $sub_row2 .= "Revised";
                    }
                }
                else{
                    $sub_row .= "Original";
                    $sub_row2 .= "Revised";
                    if($wgUser->getId() == $ev_id){ //Only set it for myself
                        $incomplete = true;
                    }
                }

                $sub_row .= "</td>";
                $sub_row2 .= "</td>";
                
                $i = 0;   
                foreach ($radio_questions as $blobItem){
                    $comm = "";
                    $comm_short = array();

                    $comm2 = "";
                    $comm_short2 = array();

                    if($i>1){
                        $comm = $this->blobValue(BLOB_ARRAY, $ev_id, $stock_comments[$i], $sub_id);
                        $comm2 = (isset($comm['revised']))? $comm['revised'] : "";
                        $comm = (isset($comm['original']))? $comm['original'] : "";

                        if(!empty($comm)){
                            foreach($comm as $key=>$c){
                                if(strlen($c)>1){
                                    $comm_short[] = substr($c, 0, 1);
                                }
                            }
                        }
                        if(!empty($comm2)){
                            foreach($comm2 as $key=>$c){
                                if(strlen($c)>1){
                                    $comm_short2[] = substr($c, 0, 1);
                                }
                            }
                        }
                    }
                    $comm_short = implode(", ", $comm_short);
                    $comm_short2 = implode(", ", $comm_short2);

                    $response = $this->blobValue(BLOB_ARRAY, $ev_id, $blobItem, $sub_id);
                    $response_orig = (isset($response['original']))? $response['original'] : "";
                    $response_rev = $response2 = (isset($response['revised']))? $response['revised'] : "";
                    $response = $response_orig;
            		
                    $double_border = '';
                    if($i==2){
                        $double_border = ' style="border-left: 5px double #8C529D;"';
                    }
                    
                    if($response_orig){
            			$response = substr($response, 0, 1);
                        if(!empty($comm)){
                            $response .= "; ".$comm_short;
                            $comm = implode("<br />", $comm);
                        } 
            		    $sub_row .= "<td{$double_border}><span class='q8_tip' title='{$response_orig}<br />{$comm}'><a>{$response}</a></span></td>";
                    }else{
            			$response = "";
                        $sub_row .= "<td{$double_border}>{$response}</td>";
                        if($wgUser->getId() == $ev_id){
                            $incomplete = true;
                        }
            		}

                    if($response_rev){
                        $response2 = substr($response2, 0, 1);
                        if(!empty($comm2)){
                            $response2 .= "; ".$comm_short2;
                            $comm2 = implode("<br />", $comm2);
                        } 
                        $sub_row2 .= "<td{$double_border}><span class='q8_tip' title='{$response_rev}<br />{$comm2}'><a>{$response2}</a></span></td>";
                    }else{
                        $response2 = "";
                        $sub_row2 .= "<td{$double_border}>{$response2}</td>";
                    }

            		
                    $i++;
            	
                }
            	$sub_row .= "</tr>";
                $sub_row2 .= "</tr>";

                if($wgUser->getId() == $ev_id){
                    $html .= $sub_row;
                    $html .= $sub_row2;
                }else{
                    $sub_table .= $sub_row;
                    $sub_table .= $sub_row2;
                }
        	}

            $sub_table_html =<<<EOF
                <div id='details_sub-{$sub_id}' class='details_sub'>
                <div class='overview_table_heading'></div>
                <table class="dashboard" style="width:100%;background:#ffffff;border-style:solid;text-align:center;" cellspacing="1" cellpadding="3" frame="box" rules="all">
                <thead>
                    <tr>
                    <th width="20%" align='left'>Evaluator Name</th>
                    <th width="10%">Q8 (Comments)</th>
                    <th width="10%">Q7</th>
                    <th width="10%">Q9</th>
                    <th style="border-left: 5px double #8C529D;">Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                    <th>Q5</th>
                    <th>Q6</th>
                    </tr>
                </thead>
EOF;

            if($incomplete){
                $sub_table_html .=<<<EOF
                <tbody>
                <tr class='purple_row'><td colspan='10'>Please complete your review of {$sub_name_straight} before you can see the feedback from other evaluators.</td></tr>
                </tbody>
                </table>
                </div>
EOF;
            }
            else if(empty($sub_table)){
                $sub_table_html .=<<<EOF
                <tbody>
                <tr class='purple_row'><td colspan='10'>There are no other reviewers assigned to review {$sub_name_straight}</td></tr>
                </tbody>
                </table>
                </div>
EOF;
            }
            else{
                $sub_table_html .=<<<EOF
                <tbody>
                {$sub_table}
                </tbody>
                </table>
                </div>
EOF;
            }
            $sub_details .= $sub_table_html;
        }

        $html .= "</table>";
        $html .= $sub_details;
        $html .= $jscript;

        return $html;
	}

	function blobValue($blob_type, $evaluator_id, $blobItem, $blobSubItem){
        $project_id = 0;
        if($this->getReport()->reportType == RP_EVAL_PROJECT){
            $project_id = $blobSubItem;
        }
 		$blob = new ReportBlob($blob_type, $this->getReport()->year, $evaluator_id, $project_id);
	    $blob_address = ReportBlob::create_address($this->getReport()->reportType, SEC_NONE, $blobItem, $blobSubItem);
		$blob->load($blob_address);
	    $blob_data = $blob->getData();
        //$addr = "BlobType=".$blob_type."; Year=". $this->getReport()->year ."; PersonID=". $evaluator_id."; ProjectID=". $this->projectId."<br />";
        //$addr .= "ReportType=".$this->getReport()->reportType."; Section=". SEC_NONE ."; BlobItem=". $blobItem ."; SubItem=". $blobSubItem ."<br /><br>";
        //echo $addr;
	    return $blob_data;
	}

    function setSeenOverview(){
        global $wgUser, $wgImpersonating;
        if($wgImpersonating){
            return;
        }
        
        $evaluator_id = $this->personId;
        $type = $this->getAttr('subType', 'PNI');
        $person = Person::newFromId($evaluator_id);

        $questions = array();
        if($type == "PNI"){
            $subs = $person->getEvaluatePNIs();
            $questions = array(EVL_OVERALLSCORE, EVL_CONFIDENCE, EVL_EXCELLENCE, EVL_HQPDEVELOPMENT, EVL_NETWORKING, EVL_KNOWLEDGE, EVL_MANAGEMENT, EVL_REPORTQUALITY, EVL_OTHERCOMMENTS);
        }
        else if($type == "CNI"){
            $subs = $person->getEvaluateCNIs();
            $questions = array(EVL_OVERALLSCORE, EVL_CONFIDENCE, EVL_EXCELLENCE, EVL_HQPDEVELOPMENT, EVL_NETWORKING, EVL_KNOWLEDGE, EVL_MANAGEMENT, EVL_REPORTQUALITY, EVL_OTHERCOMMENTS);
        }
        else if($type == "Project"){
            $subs = $person->getEvaluateProjects();
            $questions = array(EVL_OVERALLSCORE, EVL_CONFIDENCE, EVL_EXCELLENCE, EVL_HQPDEVELOPMENT, EVL_NETWORKING, EVL_KNOWLEDGE, EVL_REPORTQUALITY, EVL_OTHERCOMMENTS);
        }

         //Determine if own review was completed.
        $complete = true;
        foreach ($subs as $sub){
            $sub_id = $sub->getId();
            foreach($questions as $q){
                $val = $this->blobValue(BLOB_ARRAY, $evaluator_id, $q, $sub_id);
                if(empty($val['original'])){
                    $complete = false;
                    break;
                }
            }
            if(!$complete){
                break;
            }
        }

        if($complete){
            $blob = new ReportBlob(BLOB_TEXT, $this->getReport()->year, $evaluator_id, 0);
            $blob_address = ReportBlob::create_address($this->getReport()->reportType, SEC_NONE, EVL_SEENOTHERREVIEWS, 0);
            $data = "Yes";
            $blob->store($data, $blob_address);
        }
        /*
        $blob->load($blob_address);
        $data = $blob->getData();
        if(!empty($data)){
            return;
        }
        */

        //$data = "Yes";
        //$blob->store($data, $blob_address);
        
        /*
        $person = Person::newFromId($this->personId);
        $subs = $person->getEvaluatePNIs();
        foreach($subs as $sub){
            $sub_id = $sub->getId();
            $blob = new ReportBlob(BLOB_TEXT, $this->getReport()->year, $evaluator_id, $this->projectId);
            $blob_address_from = ReportBlob::create_address($this->getReport()->reportType, SEC_NONE, EVL_OTHERCOMMENTS, $sub_id);
            $blob->load($blob_address_from);

            if($orig_data = $blob->getData()){    
                $blob_address_to = ReportBlob::create_address($this->getReport()->reportType, SEC_NONE, EVL_OTHERCOMMENTSAFTER, $sub_id);
                $blob->store($orig_data, $blob_address_to);
            }
        }
        */
    }
}
?>
