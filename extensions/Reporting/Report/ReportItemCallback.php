<?php

class ReportItemCallback {
    
    static $callbacks = 
        array(
            // Dates
            "2_years_ago" => "get2YearsAgo",
            "last_year" => "getLastYear",
            "this_year" => "getThisYear",
            "next_year" => "getNextYear",
            // Projects
            "project_id" => "getProjectId",
            "project_name" => "getProjectName",
            "project_full_name" => "getProjectFullName",
            "project_url" => "getProjectUrl",
            "project_status" => "getProjectStatus",
            "project_description" => "getProjectDescription",
            "project_theme" => "getProjectTheme",
            "project_leaders" => "getProjectLeaders",
            "project_problem" => "getProjectProblem",
            "project_solution" => "getProjectSolution",
            "project_nis" => "getProjectNIs",
            "project_champions" => "getProjectChampions",
            "project_evolved_from" => "getProjectEvolvedFrom",
            "project_evolved_into" => "getProjectEvolvedInto",
            // Milestones
            "milestone_id" => "getMilestoneId",
            "milestone_title" => "getMilestoneTitle",
            "milestone_oldtitle" => "getMilestoneOldTitle",
            "milestone_start_date" => "getMilestoneStartDate",
            "milestone_end_date" => "getMilestoneEndDate",
            "milestone_status" => "getMilestoneStatus",
            "milestone_changes" => "getMilestoneChanges",
            "milestone_description" => "getMilestoneDescription",
            "milestone_olddescription" => "getMilestoneOldDescription",
            "milestone_assessment" => "getMilestoneAssessment",
            "milestone_oldassessment" => "getMilestoneOldAssessment",
            "milestone_last_edited_by" => "getMilestoneLastEditedBy",
            "milestone_hqp_comments" => "getMilestoneHQPComments",
            "milestone_ni_comments" => "getMilestoneNIComments",
            "milestone_ni_summaries" => "getMilestoneNISummaries",
            // Reports
            "timestamp" => "getTimestamp",
            "post_id" => "getPostId",
            "report_name" => "getReportName",
            "report_xmlname" => "getReportXMLName",
            "section_name" => "getSectionName",
            "report_excellence_ni_comments" => "getReportExcellenceNIComments",
            "report_hqpdev_ni_comments" => "getReportHQPDevNIComments",
            "report_networking_ni_comments" => "getReportNetworkingNIComments",
            "report_ktee_ni_comments" => "getReportKTEENIComments",
            "report_future_ni_comments" => "getReportFutureNIComments",
            "report_sab_comments" => "getReportSABComments",
            "report_excellence_hqp_comments" => "getReportExcellenceHQPComments",
            "report_networking_hqp_comments" => "getReportNetworkingHQPComments",
            "report_ktee_hqp_comments" => "getReportKTEEHQPComments",
            "report_has_started" => "getReportHasStarted",
            // People
            "my_name" => "getMyName",
            "my_first_name" => "getMyFirstName",
            "my_last_name" => "getMyLastName",
            "parent_id" => "getParentId",
            "user_name" => "getUserName",
            "user_url" => "getUserUrl",
            "user_email" => "getUserEmail",
            "user_phone" => "getUserPhone",
            "user_gender" => "getUserGender",
            "user_reversed_name" => "getUserReversedName",
            "user_last_name" => "getUserLastName",
            "user_first_name" => "getUserFirstName",
            "user_id" => "getUserId",
            "user_roles" => "getUserRoles",
            "user_full_roles" => "getUserFullRoles",
            "user_level" => "getUserLevel",
            "user_dept" => "getUserDept",
            "user_uni" => "getUserUni",
            "user_nationality" => "getUserNationality",
            "user_supervisors" => "getUserSupervisors",
            "user_projects" => "getUserProjects",
            "user_project_end_date" => "getUserProjectEndDate",
            "user_phase1_projects" => "getUserPhase1Projects", // Hopefully temporary
            "user_phase2_projects" => "getUserPhase2Projects", // Hopefully temporary
            "user_tvn_file_number" => "getTVNFileNumber", // hard-coded strings
            "user_research_time" => "getUserResearchTime",
            "user_requested_budget" => "getUserRequestedBudget",
            "user_allocated_budget" => "getUserAllocatedBudget",
            "user_project_comment" => "getUserProjectComment",
            "user_project_future" => "getUserProjectFuture",
            "user_subproject_comments" => "getUserSubProjectComments",
            "user_subproject_champs" => "getUserSubProjectChamps",
            "user_mtg_music" => "getUserMTGMusic",
            "user_mtg_firstnations" => "getUserMTGFirstNations",
            "user_mtg_socialproblems" => "getUserMTGSocialProblems",
            "user_mtg_other" => "getUserMTGOther",
            // Champions
            "champ_org" => "getChampOrg",
            "champ_title" => "getChampTitle",
            "champ_subtitle" => "getChampSubTitle",
            "champ_subprojects" => "getChampSubProjects",
            "champ_full_project" => "getChampFullProject",
            "champ_is_still_champion" => "getChampIsStillChampion",
            "champ_has_started" => "getChampReportHasStarted",
            "champ_has_submitted" => "getChampReportHasSubmitted",
            "champ_represent" => "getChampRepresent",
            "champ_q1" => "getChampQ1",
            "champ_q2" => "getChampQ2",
            "champ_q3" => "getChampQ3",
            "champ_q4" => "getChampQ4",
            "champ_q5" => "getChampQ5",
            "champ_q6" => "getChampQ6",
            // Sub-PL (SPL)
            "spl_subprojects" => "getSPLSubProjects",
            // ISAC
            "isac_comment" => "getISACComment",
            // SAB
            "sab_strength" => "getSABStrength",
            "sab_weakness" => "getSABWeakness",
            "sab_ranking" => "getSABRanking",
            "sab_summary" => "getSABSummary",
            // RMC
            "rmc_project_rank" => "getRMCProjectRank",
            "rmc_project_confidence" => "getRMCProjectConfidence",
            "rmc_project_feedback" => "getRMCProjectFeedback",
            // HQP Application
            "hqp_application_uni" => "getHQPApplicationUni",
            "hqp_application_program" => "getHQPApplicationProgram", 
            // Products
            "product_id" => "getProductId",
            "product_title" => "getProductTitle",
            "product_url" => "getProductUrl",
            // Other
            "wgServer" => "getWgServer",
            "wgScriptPath" => "getWgScriptPath",
            "networkName" => "getNetworkName",
            "id" => "getId",
            "name" => "getName",
            "index" => "getIndex",
            "extraIndex" => "getExtraIndex",
            "getText" => "getText",
            "getNumber" => "getNumber",
            "getHTML" => "getHTML",
            "getArray" => "getArray",
            "getExtra" => "getExtra",
            "add" => "add",
            "subtract" => "subtract",
            "multiply" => "multiply",
            "divide" => "divide",
            "round" => "round"
        );
    
    var $reportItem;
    
    // Constructor
    function ReportItemCallback($reportItem){
        $this->reportItem = $reportItem;
    }
    
    function get2YearsAgo(){
        return REPORTING_YEAR-2;
    }
    
    function getLastYear(){
        return REPORTING_YEAR-1;
    }
    
    function getThisYear(){
        return REPORTING_YEAR;
    }
    
    function getNextYear(){
        return REPORTING_YEAR+1;
    }
    
    function getProjectId(){
        $project_id = 0;
        if($this->reportItem->projectId != 0){
            $project_id = $this->reportItem->projectId;
        }
        return $project_id;
    }
    
    function getProjectName(){
        $project_name = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_name = $project->getName();
        }
        $project_name = str_replace("<", "&lt;", $project_name);
        $project_name = str_replace(">", "&gt;", $project_name);
        return $project_name;
    }
    
    function getProjectFullName(){
        $project_name = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_name = $project->getFullName();
        }
        return $project_name;
    }
    
    function getProjectUrl(){
        $project_url = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_url = $project->getUrl();
        }
        return $project_url;
    }
    
    function getProjectStatus(){
        $project_stat = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_stat = $project->getStatus();
        }
        return $project_stat;
    }
    
    function getProjectDescription(){
        $project_desc = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_desc = $project->getDescription();
        }
        return $project_desc;
    }
    
    function getProjectTheme(){
        $project_theme = "";
        if($this->reportItem->projectId != 0){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_theme = $project->getChallenge()->getAcronym();
        }
        return $project_theme;
    }
    
    function getProjectLeaders(){
        $leads = array();
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $leaders = $project->getLeaders();
            foreach($leaders as $lead){
                $leads[$lead->getReversedName()] = "<a target='_blank' href='{$lead->getUrl()}'>{$lead->getNameForForms()}</a>";
            }
        }
        if(count($leads) == 0){
            $leads[] = "N/A";
        }
        return implode(", ", $leads);
    }
    
    function getProjectProblem(){
        $project_prob = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_prob = $project->getProblem();
        }
        return $project_prob;
    }
    
    function getProjectSolution(){
        $project_sol = "";
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $project_sol = $project->getSolution();
        }
        return $project_sol;
    }
    
    function getProjectNIs(){
        $nis = array();
        if($this->reportItem->projectId != 0){
            $project = Project::newFromId($this->reportItem->projectId);
            foreach($project->getAllPeopleDuring(null, REPORTING_YEAR."-01-01 00:00:00", REPORTING_YEAR."-12-31 23:59:59") as $ni){
                if(!$ni->leadershipOf($project) && ($ni->isRoleDuring(NI, REPORTING_CYCLE_START, REPORTING_CYCLE_END))){
                    $nis[] = "<a href='{$ni->getUrl()}' target='_blank'>{$ni->getNameForForms()}</a>";
                }
            }
        }
        if(count($nis) == 0){
            $nis[] = "N/A";
        }
        return implode(", ", $nis);
    }
    
    function getProjectChampions(){
        $champions = array();
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $champs = $project->getChampionsOn(($this->reportItem->getReport()->year+1).REPORTING_RMC_MEETING_MONTH);
            foreach($champs as $champ){
                $champions[] = "<a href='{$champ['user']->getUrl()}' target='_blank'>{$champ['user']->getNameForForms()}</a>";
            }
        }
        if(count($champions) == 0){
            $champions[] = "N/A";
        }
        return implode(", ", $champions);
    }
    
    function getProjectEvolvedInto(){
        $projects = array();
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $succs = $project->getSuccs();
            $people = $project->getAllPeople();
            foreach($people as $key => $person){
                if(!$person->isRole(NI)){
                    unset($people[$key]);
                }
            }
            foreach($succs as $succ){
                $count = 0;
                foreach($succ->getAllPeople() as $person){
                    if(isset($people[$person->getId()]) && $person->isRole(NI)){
                        $count++;
                    }
                }
                $projects[$count][] = $succ->getName()."($count)";
            }
        }
        ksort($projects);
        $projects = array_reverse($projects);
        $newProjects = array();
        foreach($projects as $projs){
            foreach($projs as $proj){
                $newProjects[] = $proj;
            }
        }
        return implode(", ", $newProjects);
    }
    
    function getProjectEvolvedFrom(){
        $projects = array();
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $preds = $project->getPreds();
            $people = $project->getAllPeople();
            foreach($people as $key => $person){
                if(!$person->isRole(NI)){
                    unset($people[$key]);
                }
            }
            foreach($preds as $pred){
                $count = 0;
                foreach($pred->getAllPeople() as $person){
                    if(isset($people[$person->getId()]) && $person->isRole(NI)){
                        $count++;
                    }
                }
                $projects[$count][] = $pred->getName()."($count)";
            }
        }
        ksort($projects);
        $projects = array_reverse($projects);
        $newProjects = array();
        foreach($projects as $projs){
            foreach($projs as $proj){
                $newProjects[] = $proj;
            }
        }
        return implode(", ", $newProjects);
    }
    
    function getMilestoneId(){
        return $this->reportItem->milestoneId;
    }
    
    function getMilestoneTitle(){
        $milestone_title = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_title = $milestone->getTitle();
        }
        return $milestone_title;
    }
    
    function getMilestoneOldTitle(){
        $milestone_title = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId)->getRevisionByDate((REPORTING_YEAR-1)."-12-00");
            if($milestone != null){
                $milestone_title = $milestone->getTitle();
            }
        }
        return $milestone_title;
    }
    
    function getMilestoneStartDate(){
        $milestone_start = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_start = new DateTime($milestone->getVeryStartDate());
            $milestone_start = date_format($milestone_start, 'F, Y');
        }
        return $milestone_start;
    }
    
    function getMilestoneEndDate(){
        $milestone_end = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_end = new DateTime($milestone->getProjectedEndDate());
            $milestone_end = date_format($milestone_end, 'F, Y');
        }
        return $milestone_end;
    }
    
    function getMilestoneStatus(){
        $milestone_title = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_title = $milestone->getStatus();
        }
        return $milestone_title;
    }
    
    function getMilestoneDescription(){
        $milestone_description = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_description = $milestone->getDescription();
        }
        return $milestone_description;
    }
    
    function getMilestoneOldDescription(){
        $milestone_description = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId)->getRevisionByDate((REPORTING_YEAR-1)."-12-00");
            if($milestone != null){
                $milestone_description = $milestone->getDescription();
            }
        }
        return $milestone_description;
    }
    
    function getMilestoneChanges(){
        $nChanges = 0;
        if($this->reportItem->milestoneId != 0 ){
            $currentMilestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone = $currentMilestone->getRevisionByDate((REPORTING_YEAR-1)."-12-00");
            if($milestone == null){
                $first = $currentMilestone;
                $milestone = $first;
                while($first != null){
                    $milestone = $first;
                    $first = $first->getParent();
                }
            }
            $parent = $currentMilestone;
            while($parent != null && $parent->getId() != $milestone->getId()){
                $nChanges++;
                $parent = $parent->getParent();
            }
        }
        return $nChanges;
    }
    
    function getMilestoneAssessment(){
        $milestone_assessment = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $milestone_assessment = $milestone->getAssessment();
        }
        return $milestone_assessment;
    }
    
    function getMilestoneOldAssessment(){
        $milestone_assessment = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId)->getRevisionByDate((REPORTING_YEAR-1)."-12-00");
            if($milestone != null){
                $milestone_assessment = $milestone->getAssessment();
            }
        }
        return $milestone_assessment;
    }
    
    function getMilestoneLastEditedBy(){
        $edited = "";
        if($this->reportItem->milestoneId != 0 ){
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            if($milestone->getEditedBy() != null && $milestone->getEditedBy()->getName() != ""){
                $edited = "<a href='{$milestone->getEditedBy()->getUrl()}'>{$milestone->getEditedBy()->getNameForForms()}</a>";
            }
        }
        return $edited;
    }
    
    function getMilestoneHQPComments(){
        if($this->reportItem->milestoneId != 0 ){
            $person = $this->reportItem->getReport()->person;
            $project = Project::newFromId($this->reportItem->projectId);
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $hqp_rep_addr = ReportBlob::create_address(RP_HQP, HQP_MILESTONES, HQP_MIL_CONTRIBUTIONS, 0);
            $m_id = $this->reportItem->milestoneId;
        }
        else{
            return;
        }
        //First get All HQPs that I'm supervising, then we'll fetch their comment on the milestone.
        $hqp_objs = $project->getAllPeopleDuring("HQP", REPORTING_YEAR."-01-01 00:00:00", REPORTING_YEAR."-12-31 23:59:59");
        
        $hqp_milestone_comments = "";
        $alreadyDone = array();
        foreach ($hqp_objs as $h){
            if(isset($alreadyDone[$h->getId()])){
                continue;
            }
            $alreadyDone[$h->getId()] = true;
            $h_sups = $h->getSupervisors(true);
            
            $supervisor = false;
            foreach ($h_sups as $s){
                if ( $s->getId() == $person->getId() ){
                    $supervisor =  true;
                    //echo "HQP: ".$h->getName()."<br />";
                    break;
                }
            }
            
            if($supervisor){
                $hqp_milestone_blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $h->getId(), $project->getId());
                $hqp_milestone_blob->load($hqp_rep_addr);
                $hqp_milestone_data = $hqp_milestone_blob->getData();
                
                if( isset($hqp_milestone_data[$m_id]) && isset($hqp_milestone_data[$m_id]['comment'])
                    && !empty($hqp_milestone_data[$m_id]['comment']) ){
                    $hqp_milestone_comments .= $h->getNameForForms() . ":<br /><i style='margin:10px;display:block;'>" . 
                        $hqp_milestone_data[$m_id]['comment'] . "</i><br />";

                }
            }
        }
        return $hqp_milestone_comments;
    }
    
    function getMilestoneNIComments(){
        if($this->reportItem->milestoneId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $milestone = Milestone::newFromId($this->reportItem->milestoneId);
            $ni_rep_addr = ReportBlob::create_address(RP_RESEARCHER, RES_MILESTONES, RES_MIL_CONTRIBUTIONS, 0);
            $m_id = $this->reportItem->milestoneId;
        }
        else{
            return;
        }
        $nis = $project->getAllPeopleDuring(NI, REPORTING_YEAR."-01-01 00:00:00", REPORTING_YEAR."-12-31 23:59:59");
        $ni_milestone_comments = "";
        $alreadyDone = array();
        foreach($nis as $ni){
            if(isset($alreadyDone[$ni->getId()])){
                continue;
            }
            $alreadyDone[$ni->getId()] = true;
            $ni_milestone_blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $ni->getId(), $project->getId());
            $ni_milestone_blob->load($ni_rep_addr);
            $ni_milestone_data = $ni_milestone_blob->getData();
            
            if( isset($ni_milestone_data[$m_id]) && isset($ni_milestone_data[$m_id]['comment'])
                && !empty($ni_milestone_data[$m_id]['comment']) ){
                $ni_milestone_comments .= $ni->getNameForForms() . ":<br /><i style='margin:10px;display:block;'>" . 
                    $ni_milestone_data[$m_id]['comment'] . "</i><br />";

            }
        }
        return $ni_milestone_comments;
    }
    
    function getMilestoneNISummaries(){
        if($this->reportItem->projectId != 0 ){
            $project = Project::newFromId($this->reportItem->projectId);
            $ni_rep_addr = ReportBlob::create_address(RP_RESEARCHER, RES_MILESTONES, RES_MIL_SUMMARY, 0);
        }
        else{
            return;
        }
        $nis = $project->getAllPeopleDuring(NI, REPORTING_YEAR."-01-01 00:00:00", REPORTING_YEAR."-12-31 23:59:59");
        $ni_milestone_comments = "";
        $alreadyDone = array();
        foreach($nis as $ni){
            if(isset($alreadyDone[$ni->getId()])){
                continue;
            }
            $alreadyDone[$ni->getId()] = true;
            $ni_milestone_blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $ni->getId(), $project->getId());
            $ni_milestone_blob->load($ni_rep_addr);
            $ni_milestone_data = $ni_milestone_blob->getData();
            if($ni_milestone_data != ""){
                $ni_milestone_comments .= $ni->getNameForForms() . ":<br /><i style='margin:10px;display:block;'>".$ni_milestone_data. "</i><br />";
            }
        }
        return $ni_milestone_comments;
    }
    
    function getReportExcellenceNIComments(){
        return $this->getReportNIComments(RES_RESACT_EXCELLENCE);
    }
    
    function getReportHQPDevNIComments(){
        return $this->getReportNIComments(RES_RESACT_HQPDEV);
    }
    
    function getReportNetworkingNIComments(){
        return $this->getReportNIComments(RES_RESACT_NETWORKING);
    }
    
    function getReportKTEENIComments(){
        return $this->getReportNIComments(RES_RESACT_KTEE);
    }
    
    function getReportFutureNIComments(){
        return $this->getReportNIComments(RES_RESACT_NEXTPLANS);
    }
    
    function getReportSABComments(){
        $ret = "";
        $sabs = Person::getAllPeopleDuring(ISAC, $this->reportItem->getReport()->year.'-01-01', $this->reportItem->getReport()->year.'-12-31');
        
        $index = 1;
        foreach($sabs as $sab){
            $strength = $this->getSABStrength($sab->getId());
            $weakness = $this->getSABWeakness($sab->getId());
            $ranking  = $this->getSABRanking($sab->getId());
            if($strength != "" || $weakness != ""){
                $ret .= "<h1>SAB Reviewer {$index}</h1>";
                $ret .= "<div style='margin-left:15px;'>";
                $ret .= "<h3>Project Strengths</h3>";
                $ret .= "<p>$strength</p>";
                $ret .= "<h3>Project Weaknesses</h3>";
                $ret .= "<p>$weakness</p>";
                $ret .= "<h3>Project Ranking</h3>";
                $ret .= "<p>$ranking</p>";
                $ret .= "</div>";
                $index++;
            }
        }
        $addr = ReportBlob::create_address(RP_SAB_REPORT, SAB_REPORT, SAB_REPORT_SUMMARY, 0);
        $blob = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, 0, $this->reportItem->projectId);
        $blob->load($addr);
        $data = $blob->getData();
        if($data != ""){
            $ret .= "<h1>Summary</h1>";
            $ret .= "<div style='margin-left:15px;'>";
            $ret .= "<p>$data</p>";
            $ret .= "</div>";
        }
        return $ret;
    }
    
    function getReportExcellenceHQPComments(){
        return $this->getReportHQPComments(HQP_RESACT_EXCELLENCE);
    }
    
    function getReportNetworkingHQPComments(){
        return $this->getReportHQPComments(HQP_RESACT_NETWORKING);
    }
    
    function getReportKTEEHQPComments(){
        return $this->getReportHQPComments(HQP_RESACT_KTEE);
    }
    
    function getReportHasStarted(){
        $report = $this->reportItem->getReport();
        if($report->hasStarted()){
            return "<span style='font-weight:bold;color:#008800;'>Yes</span>";
        }
        return "<span>No</span>";
    }
    
    function getChampReportHasStarted(){
        $project = Project::newFromId($this->reportItem->projectId);
        $person = Person::newFromId($this->reportItem->personId);
        
        $report = new DummyReport(RP_CHAMP, $person, $project, $this->reportItem->getReport()->year);
        if($report->hasStarted()){
            return "<span style='font-weight:bold;color:#008800;'>Yes</span>";
        }
        return "<span>No</span>";
    }
    
    function getChampReportHasSubmitted(){
        $project = Project::newFromId($this->reportItem->projectId);
        $person = Person::newFromId($this->reportItem->personId);
        
        $report = new DummyReport(RP_CHAMP, $person, $project, $this->reportItem->getReport()->year);
        if($report->isSubmitted()){
            return "<span style='font-weight:bold;color:#008800;'>Yes</span>";
        }
        return "<span>N/A</span>";
    }
    
    private function getReportNIComments($item){
        if($this->reportItem->projectId != 0){
            $project = Project::newFromId($this->reportItem->projectId);
            $ni_rep_addr = ReportBlob::create_address(RP_RESEARCHER, RES_RESACTIVITY, $item, 0);
        }
        else{
            return;
        }
        $nis = $project->getAllPeopleDuring(NI, REPORTING_YEAR."-01-01 00:00:00", REPORTING_YEAR."-12-31 23:59:59");
        $ni_comments = "";
        $alreadyDone = array();
        foreach($nis as $ni){
            if(isset($alreadyDone[$ni->getId()])){
                continue;
            }
            $alreadyDone[$ni->getId()] = true;
            $ni_blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $ni->getId(), $project->getId());
            $ni_blob->load($ni_rep_addr);
            $ni_data = $ni_blob->getData();
            if($ni_data != null){
                $ni_data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $ni_data);
                $ni_comments .= $ni->getReversedName() . ":<br /><i style='margin:10px;display:block;'>" . 
                        $ni_data . "</i><br />";
            }
        }
        return $ni_comments;
    }
    
    private function getReportHQPComments($item){
        if($this->reportItem->projectId != 0){
            $project = Project::newFromId($this->reportItem->projectId);
            $hqp_rep_addr = ReportBlob::create_address(RP_HQP, HQP_RESACTIVITY, $item, 0);
        }
        else{
            $project = null;
            $hqp_rep_addr = ReportBlob::create_address(RP_HQP, HQP_RESACTIVITY, $item, RES_RESACT_PHASE1);
        }
        $me = Person::newFromId($this->reportItem->personId);
        
        $hqps = $me->getHQPDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END);
        $hqp_comments = "";
        foreach($hqps as $hqp){
            if($this->reportItem->projectId == 0 || $hqp->isMemberOfDuring($project, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
                $hqp_blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $hqp->getId(), $this->reportItem->projectId);
                $hqp_blob->load($hqp_rep_addr);
                $hqp_data = $hqp_blob->getData();
                if($hqp_data != null){
                    $hqp_comments = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $hqp_comments);
                    $hqp_comments .= $hqp->getReversedName() . ":<br /><i style='margin:10px;display:block;'>" . 
                            $hqp_data . "</i><br />";
                }
            }
        }
        return $hqp_comments;
    }
    
    function getMyName(){
        $person = $this->reportItem->getReport()->person;
        return $person->getNameForForms();
    }
    
    function getMyFirstName(){
        $person = $this->reportItem->getReport()->person;
        return $person->getFirstName();
    }
    
    function getMyLastName(){
        $person = $this->reportItem->getReport()->person;
        return $person->getLastName();
    }
    
    function getUserUrl(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getUrl();
    }
    
    function getUserEmail(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getEmail();
    }
    
    function getUserPhone(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getPhoneNumber();
    }
    
    function getUserGender(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getGender();
    }
    
    function getParentId(){
        return $this->reportItem->getParent()->getParent()->personId;
    }
    
    function getUserName(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getNameForForms();
    }
    
    function getUserReversedName(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getReversedName();
    }
    
    function getUserLastName(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getLastName();
    }
    
    function getUserFirstName(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getFirstName();
    }
    
    function getUserId(){
        return $this->reportItem->personId;
    }
    
    function getUserRoles(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        $roles = $person->getRoles();
        $roleNames = array();
        foreach($roles as $role){
            if(!($role->getRole() == NI ||
                 $role->getRole() == AR ||
                 $role->getRole() == CI ||
                 $role->getRole() == HQP ||
                 $role->getRole() == EXTERNAL ||
                 $role->getRole() == CHAMP)){
                continue;  
            }
            if($project != null && $project->getId() != 0){
                if($role->hasProject($project)){
                    $roleNames[$role->getRole()] = $role->getRole();
                }
            }
            else{
                $roleNames[$role->getRole()] = $role->getRole();
            }
        }
        return implode(", ", $roleNames);
    }
    
    function getUserFullRoles(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        $roles = $this->getUserRoles();
        if($project != null && $project->getId() != 0){
            if($person->leadershipOf($project)){
                if($roles != ""){
                    $roles .= ", PL";
                }
                else{
                    $roles .= "PL";
                }
            }
        }
        else if($person->isProjectLeader()){
            if($roles != ""){
                $roles .= ", PL";
            }
            else{
                $roles .= "PL";
            }
        }
        return $roles;
    }
    
    function getUserLevel(){
        $person = Person::newFromId($this->reportItem->personId);
        $university = $person->getUniversity();
        return $university['position'];
    }
    
    function getUserDept(){
        $person = Person::newFromId($this->reportItem->personId);
        $university = $person->getUniversity();
        return $university['department'];
    }
    
    function getUserUni(){
        $person = Person::newFromId($this->reportItem->personId);
        $university = $person->getUniversity();
        return $university['university'];
    }
    
    function getUserNationality(){
        $person = Person::newFromId($this->reportItem->personId);
        $nationality = $person->getNationality();
        return $nationality;
    }
    
    function getUserSupervisors(){
        $supervisors = array();
        $person = Person::newFromId($this->reportItem->personId);
        $me = $person;
        foreach(Person::getAllPeople('all') as $person){
            foreach($person->getRelations(SUPERVISES, true) as $rel){
                $start = $rel->getStartDate();
                $end = $rel->getEndDate();
                if($rel->getUser2()->getId() == $me->getId()){
                    if((strcmp($start, REPORTING_CYCLE_START."00:00:00") <= 0 && (strcmp($end, REPORTING_CYCLE_START."00:00:00") >= 0 || strcmp($end, "0000-00-00 00:00:00") == 0)) ||
                       (strcmp($start, REPORTING_CYCLE_END."00:00:00") <= 0 && strcmp($start, REPORTING_CYCLE_START."00:00:00") >= 0) ||
                       (strcmp($end, REPORTING_CYCLE_END."00:00:00") <= 0 && strcmp($end, REPORTING_CYCLE_START."00:00:00") >= 0)){
                        $sup = $rel->getUser1();
                        $supervisors[$sup->getId()] = "<a target='_blank' href='{$sup->getUrl()}'>{$sup->getNameForForms()}</a>";
                    }
                }
            }
        }
        return implode(", ", $supervisors);
    }
    
    function getUserProjects(){
        $person = Person::newFromId($this->reportItem->personId);
        $projects = array();
        foreach($person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
            if(!$project->isSubProject()){
                $deleted = ($project->isDeleted()) ? " (Ended)" : "";
                $projects[] = "<a target='_blank' href='{$project->getUrl()}'>{$project->getName()}{$deleted}</a>";
            }
        }
        if(count($projects) > 0){
            return implode(", ", $projects);
        }
        return "N/A";
    }
    
    function getUserProjectEndDate(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        $date = $project->getEndDate($person);
        if($date != "0000-00-00 00:00:00"){
            return time2date($project->getEndDate($person));
        }
        return "";
    }
    
    function getUserPhase1Projects(){
        $person = Person::newFromId($this->reportItem->personId);
        $projects = array();
        foreach($person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
            if(!$project->isSubProject() && $project->getPhase() == 1){
                $projects[] = "<a target='_blank' href='{$project->getUrl()}'>{$project->getName()}</a>";
            }
        }
        if(count($projects) > 0){
            return implode(", ", $projects);
        }
        return "N/A";
    }
    
    function getUserPhase2Projects(){
        $person = Person::newFromId($this->reportItem->personId);
        $projects = array();
        foreach($person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
            if(!$project->isSubProject() && $project->getPhase() == 2){
                $projects[] = "<a target='_blank' href='{$project->getUrl()}'>{$project->getName()}</a>";
            }
        }
        if(count($projects) > 0){
            return implode(", ", $projects);
        }
        return "N/A";
    }
    
    function getTVNFileNumber(){
        $id = $this->reportItem->personId;
        
        $fileNumbers = array(
            1791    => "IFP2016-01",
            1790    => "IFP2016-04",
            249     => "IFP2016-06",
            1789    => "IFP2016-07",
            1788    => "IFP2016-08",
            1787    => "IFP2016-09",
            1786    => "IFP2016-10",
            1785    => "IFP2016-11",
            1784    => "IFP2016-12",
            456     => "IFP2016-14",
            1783    => "IFP2016-16",
            285     => "IFP2016-18",
            1782    => "IFP2016-20",
            1781    => "IFP2016-21",
            1780    => "IFP2016-22",
            1761    => "IFP2016-23",
            1779    => "IFP2016-24",
            1778    => "IFP2016-25",
            1777    => "IFP2016-28",
            230     => "IFP2016-30",
            1776    => "IFP2016-31",
            1775    => "IFP2016-32",
            1774    => "IFP2016-33",
            1773    => "IFP2016-34",
            1772    => "IFP2016-35"
        );
        
        if(isset($fileNumbers[$id])){
            return $fileNumbers[$id];
        }
        return "";
    }
    
    function getUserResearchTime(){
        $person = Person::newFromId($this->reportItem->personId);
        $total = 0;
        $rep_addr = ReportBlob::create_address(RP_RESEARCHER, RES_MILESTONES, RES_MIL_CONTRIBUTIONS, 0);
        foreach($person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
            $blob = new ReportBlob(BLOB_ARRAY, substr(REPORTING_CYCLE_START, 0, 4), $person->getId(), $project->getId());
            $blob->load($rep_addr);
            $data = $blob->getData();
            $total += (isset($data[0]) && $data[0]["time"])? $data[0]["time"] : 0;
        }
        return $total;
    }
    
    function getUserRequestedBudget(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $budget = $person->getRequestedBudget(REPORTING_YEAR);
        if($project != null && $project->getName() != ""){
            $budgetFirstCol = $budget->copy()->limitCols(0, 1);
            $budget = $budget->copy()->select(V_PROJ, array($project->getName()));
            $budget = $budgetFirstCol->join($budget);
            $budget->xls[0][1]->value = $person->getReversedName();
        }
        return $budget->render();
    }
    
    function getUserAllocatedBudget(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $budget = $person->getAllocatedBudget(REPORTING_YEAR);
        if($project != null && $project->getName() != ""){
            $budgetFirstCol = $budget->copy()->limitCols(0, 1);
            $budget = $budget->copy()->select(V_PROJ, array($project->getName()));
            $budget = $budgetFirstCol->join($budget);
            $budget->xls[0][1]->value = $person->getReversedName();
        }
        return $budget->render();
    }
    
    function getUserProjectComment(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $addr = ReportBlob::create_address(RP_LEADER, LDR_NICOMMENTS, LDR_NICOMMENTS_COMMENTS, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, 0, $project->getId());
        $blob->load($addr);
        $data = $blob->getData();
        if(isset($data[$person->id]['ni_comments'])){
            return $data[$person->id]['ni_comments'];
        }
        return "";
    }
    
    function getUserProjectFuture(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $addr = ReportBlob::create_address(RP_RESEARCHER, RES_RESACTIVITY, RES_RESACT_NEXTPLANS, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $person->getId(), $project->getId());
        $blob->load($addr);
        $data = $blob->getData();
        return $data;
    }
    
    function getUserSubProjectChamps(){
        $project = Project::newFromId($this->reportItem->projectId);
        
        $report = new DummyReport(RP_SUBPROJECT, new Person(array()), $project, $this->reportItem->getReport()->year);
        $item = $report->getSectionById("report")->getReportItemById("sub_project_champs");
        
        return $item->getHTMLForPDF();
    }
    
    function getUserSubProjectComments(){
        $project = Project::newFromId($this->reportItem->projectId);
        
        $report = new DummyReport(RP_SUBPROJECT, new Person(array()), $project, $this->reportItem->getReport()->year);
        $item = $report->getSectionById("report")->getReportItemById("sub_project_comments");
        
        return $item->getHTMLForPDF();
    }
    
    function getUserMTGMusic(){
        $person = Person::newFromId($this->reportItem->personId);
        $addr = ReportBlob::create_address(RP_MTG, MTG_MUSIC, MTG_MUSIC, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $person->getId(), 0);
        $blob->load($addr);
        $data = $blob->getData();
        return $data;
    }
    
    function getUserMTGFirstNations(){
        $person = Person::newFromId($this->reportItem->personId);
        $addr = ReportBlob::create_address(RP_MTG, MTG_FIRST_NATIONS, MTG_FIRST_NATIONS, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $person->getId(), 0);
        $blob->load($addr);
        $data = $blob->getData();
        return $data;
    }
    
    function getUserMTGSocialProblems(){
        $person = Person::newFromId($this->reportItem->personId);
        $addr = ReportBlob::create_address(RP_MTG, MTG_SOCIAL_PROBLEMS, MTG_SOCIAL_PROBLEMS, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $person->getId(), 0);
        $blob->load($addr);
        $data = $blob->getData();
        return $data;
    }
    
    function getUserMTGOther(){
        $person = Person::newFromId($this->reportItem->personId);
        $addr = ReportBlob::create_address(RP_MTG, MTG_OTHER, MTG_OTHER, 0);
        $blob = new ReportBlob(BLOB_TEXT, REPORTING_YEAR, $person->getId(), 0);
        $blob->load($addr);
        $data = $blob->getData();
        return $data;
    }
    
    function getChampOrg(){
        return $this->getUserUni();
    }
    
    function getChampTitle(){
        return $this->getUserLevel();
    }
    
    function getChampSubTitle(){
        $person = Person::newFromId($this->reportItem->personId);
        $org = $person->getUni();
        $title = $person->getPosition();
        if($org != "" && $title != ""){
            return "$org, $title";
        }
        else if($org != "" && $title == ""){
            return $org;
        }
        else if($org == "" && $title != ""){
            return $title;
        }
        return "";
    }
    
    function getChampSubProjects(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $subs = array();
        foreach($project->getSubProjects() as $sub){
            foreach($sub->getChampionsOn(($this->reportItem->getReport()->year+1).REPORTING_RMC_MEETING_MONTH) as $champ){
                if($champ['user']->getId() == $person->getId()){
                    $subs[] = "<a href='{$sub->getUrl()}' target='_blank'>{$sub->getName()}</a>";
                }
            }
        }
        return implode(", ", $subs);
    }
    
    function getChampFullProject(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        foreach($project->getSubProjects() as $sub){
            foreach($sub->getChampionsOn(($this->reportItem->getReport()->year+1).REPORTING_RMC_MEETING_MONTH) as $champ){
                if($champ['user']->getId() == $person->getId()){
                    return "";
                }
            }
        }
        return "Full Project";
    }
    
    function getChampIsStillChampion(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $result = $person->isChampionOfOn($project, ($this->reportItem->getReport()->year+1).REPORTING_RMC_MEETING_MONTH.' 23:59:59');
        if(!$result && !$project->isSubProject()){
            foreach($project->getSubProjects() as $sub){
                $result = ($result || $person->isChampionOfOn($sub, ($this->reportItem->getReport()->year+1).REPORTING_RMC_MEETING_MONTH.' 23:59:59'));
            }
        }
        return (!$result) ? "style='color:red;text-decoration:line-through;'" : "";
    }
    
    function getChampRepresent(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_REPRESENT, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        return $data;
    }
    
    function getChampQ1(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_ACTIVITY, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        $data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $data);
        return $data;
    }
    
    function getChampQ2(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_ORG, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        $data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $data);
        return $data;
    }
    
    function getChampQ3(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_BENEFITS, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        $data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $data);
        return $data;
    }
    
    function getChampQ4(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_SHORTCOMINGS, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        $data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $data);
        return $data;
    }
    
    function getChampQ5(){
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_CASH, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        $data = preg_replace("/@\[[^-]+-([^\]]*)]/", "<b>$1</b>$2", $data);
        return $data;
    }
    
    function getChampQ6(){
        $champion_html = "";
        $blb = new ReportBlob(BLOB_ARRAY, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $addr = ReportBlob::create_address(RP_CHAMP, CHAMP_REPORT, CHAMP_RESEARCHERS, 0);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if(count($data) > 0){
            foreach($data as $u_id => $message){
                if($message['q6'] != ""){
                    $user = Person::newFromId($u_id);
                    $champion_html .= "<h3>{$user->getReversedName()}</h3>{$message['q6']}";
                }
            }
        }
        return $champion_html;
    }
    
    function getSPLSubProjects(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        
        $subs = array();
        foreach($project->getSubProjects() as $sub){
            if($person->leadershipOf($sub)){
                $subs[] = "<a href='{$sub->getUrl()}' target='_blank'>{$sub->getName()}</a>";
            }
        }
        return implode(", ", $subs);
    }
    
    function getISACComment(){
        $addr = ReportBlob::create_address(RP_ISAC, ISAC_PHASE2, ISAC_PHASE2_COMMENT, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
           return $data;
        }
        return "";
    }
    
    function getSABStrength($personId=-1){
        $personId = ($personId != -1) ? $personId : $this->reportItem->personId;
        $addr = ReportBlob::create_address(RP_SAB_REVIEW, SAB_REVIEW, SAB_REVIEW_STRENGTH, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
           return $data;
        }
        return "";
    }
    
    function getSABWeakness($personId=-1){
        $personId = ($personId != -1) ? $personId : $this->reportItem->personId;
        $addr = ReportBlob::create_address(RP_SAB_REVIEW, SAB_REVIEW, SAB_REVIEW_WEAKNESS, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
           return $data;
        }
        return "";
    }
    
    function getSABRanking($personId=-1){
        $personId = ($personId != -1) ? $personId : $this->reportItem->personId;
        $addr = ReportBlob::create_address(RP_SAB_REVIEW, SAB_REVIEW, SAB_REVIEW_RANKING, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
           return $data;
        }
        return "";
    }
    
    function getSABSummary(){
        $addr = ReportBlob::create_address(RP_SAB_REPORT, SAB_REPORT, SAB_REPORT_SUMMARY, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, 0, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
           return $data;
        }
        return "";
    }
    
    function getRMCProjectRank(){
        $addr = ReportBlob::create_address(RP_EVAL_PROJECT, RMC_REVIEW, EVL_OVERALLSCORE, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
            if(isset($data['original'])){
                return $data['original'];
            }
            else if(isset($data['revised'])){
                return $data['revised'];
            }
        }
        return "";
    }
    
    function getRMCProjectConfidence(){
        $addr = ReportBlob::create_address(RP_EVAL_PROJECT, RMC_REVIEW, EVL_CONFIDENCE, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        $data = $blb->getData();
        if($data != null){
            if(isset($data['original'])){
                return $data['original'];
            }
            else if(isset($data['revised'])){
                return $data['revised'];
            }
        }
        return "";
    }
    
    function getRMCProjectFeedback(){
        $rp = RP_PROJ_REVIEW;
        $section = PROJ_REVIEW_FEEDBACK;
        $blobId = PROJ_FEEDBACK_COMM;
        $subId = "{$this->getUserId()}0{$this->getExtraIndex()}";
        $projectId = $this->getProjectId();
        $people = array_merge(Person::getAllPeople(RMC),
                              Person::getAllPeople(STAFF),
                              Person::getAllPeople(MANAGER),
                              Person::getAllPeople(ADMIN),
                              Person::getAllPeople(SD));
        $comments = array();
        foreach($people as $person){
            $personId = $person->getId();
            $comment = $this->getText($rp, $section, $blobId, $subId, $personId, $projectId);
            if($comment != ""){
                $comments[] = "<li>{$comment}</li>";
            }
        }
        return "<ul>".implode("\n", $comments)."</ul>";
    }
    
    function getHQPApplicationUni(){
        $addr = ReportBlob::create_address(RP_HQP_APPLICATION, HQP_APPLICATION_FORM, HQP_APPLICATION_UNI, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        return $blb->getData();
    }
    
    function getHQPApplicationProgram(){
        $addr = ReportBlob::create_address(RP_HQP_APPLICATION, HQP_APPLICATION_FORM, HQP_APPLICATION_PROGRAM, 0);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $this->reportItem->personId, $this->reportItem->projectId);
        $result = $blb->load($addr);
        return $blb->getData();
    }
    
    function getProductId(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getId();
    }
    
    function getProductTitle(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getTitle();
    }
    
    function getProductUrl(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getUrl();
    }
    
    function getWgServer(){
        global $wgServer;
        return $wgServer;
    }
    
    function getWgScriptPath(){
        global $wgScriptPath;
        return $wgScriptPath;
    }
    
    function getNetworkName(){
        global $config;
        return $config->getValue('networkName');
    }
    
    function getId(){
        $personId = $this->reportItem->personId;
        $projectId = $this->reportItem->projectId;
        $productId = $this->reportItem->productId;
        
        if($personId != 0){
            return $personId;
        }
        else if($projectId != 0){
            return $projectId;
        }
        else if($productId != 0){
            return $productId;
        }
    }
    
    function getName(){
        $personId = $this->reportItem->personId;
        $projectId = $this->reportItem->projectId;
        $productId = $this->reportItem->productId;
        
        if($personId != 0){
            $person = Person::newFromId($personId);
            return $person->getNameForForms();
        }
        else if($projectId != 0){
            $project = Project::newFromId($projectId);
            return $project->getName();
        }
        else if($productId != 0){
            $product = Product::newFromId($productId);
            return $product->getTitle();
        }
    }
    
    function getIndex(){
        $personId = $this->reportItem->personId;
        $projectId = $this->reportItem->projectId;
        $productId = $this->reportItem->productId;
        $milestoneId = $this->reportItem->milestoneId;
        $set = $this->reportItem->getSet();
        $i = 1;
        foreach($set->getData() as $item){
            if($item['milestone_id'] == $milestoneId &&
               $item['project_id'] == $projectId &&
               $item['person_id'] == $personId &&
               $item['product_id'] == $productId){
                return $i;
            }
            $i++;
        }
        return 0;
    }
    
    function getExtraIndex(){
        $set = $this->reportItem->getSet();
        foreach($set->getData() as $index => $item){
            if($item['extra'] == $this->reportItem->extra){
                return $index;
            }
        }
        return 0;
    }
    
    function getArray($rp, $section, $blobId, $subId, $personId, $projectId){
        $addr = ReportBlob::create_address($rp, $section, $blobId, $subId);
        $blb = new ReportBlob(BLOB_ARRAY, $this->reportItem->getReport()->year, $personId, $projectId);
        $result = $blb->load($addr);
        return $blb->getData();
    }
    
    function getText($rp, $section, $blobId, $subId, $personId, $projectId){
        $addr = ReportBlob::create_address($rp, $section, $blobId, $subId);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $personId, $projectId);
        $result = $blb->load($addr);
        return nl2br($blb->getData());
    }
    
    function getNumber($rp, $section, $blobId, $subId, $personId, $projectId){
        return (float) $this->getText($rp, $section, $blobId, $subId, $personId, $projectId);
    }
    
    function add($val1, $val2){
        return $val1 + $val2;
    }
    
    function subtract($val1, $val2){
        return $val1 - $val2;
    }
    
    function multiply($val1, $val2){
        return $val1*$val2;
    }
    
    function divide($val1, $val2){
        return $val1/max(1, $val2);
    }
    
    function round($val, $dec=0){
        return round($val, $dec);
    }
    
    function getHTML($rp, $section, $blobId, $subId, $personId, $projectId){
        $addr = ReportBlob::create_address($rp, $section, $blobId, $subId);
        $blb = new ReportBlob(BLOB_TEXT, $this->reportItem->getReport()->year, $personId, $projectId);
        $result = $blb->load($addr);
        $blobValue = $blb->getData();
        
        $blobValue = str_replace("</p>", "<br /><br style='font-size:1em;' />", $blobValue);
        $blobValue = str_replace("<p>", "", $blobValue);
        $blobValue = str_replace_last("<br /><br style='font-size:1em;' />", "", $blobValue);
        return "<div class='tinymce'>$blobValue</div>";
    }
    
    function getExtra($index){
        $set = $this->reportItem->extra;
        if(isset($set[$index])){
            return $set[$index];
        }
        return "";
    }
    
    function getPostId(){
        return $this->reportItem->getPostId();
    }
    
    function getTimestamp(){ 
        return date("Y-m-d H:i:s T", time()); 
    }
    
    function getReportName(){
        return $this->reportItem->getReport()->name;
    }
    
    function getReportXMLName(){
        return $this->reportItem->getReport()->xmlName;
    }
    
    function getReportSection(){
        return $this->reportItem->getSection()->name;
    }
}

?>
