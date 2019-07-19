<?php

class ReportItemCallback {
    
    static $callbacks = 
        array(
            // Dates
            "startYear" => "getStartYear",
            "start_year" => "getStartYear",
            "2_years_ago" => "get2YearsAgo",
            "last_year" => "getLastYear",
            "this_year" => "getThisYear",
            "next_year" => "getNextYear",
            "endYear" => "getThisYear",
            // Courses
            "course_term" => "getCourseTerm",
            "course_start" => "getCourseStart",
            "course_end" => "getCourseEnd",
            "course_subject" => "getCourseSubject",
            "course_number" => "getCourseNumber",
            "course_title" => "getCourseTitle",
            "course_comp" => "getCourseComp",
            "course_section" => "getCourseSection",
            "course_enroll" => "getCourseEnroll",    
            "course_enroll_percent" => "getCourseEnrollPercent",
            // Student Relation
            "hqp_name" => "getHqpName",
            "hqp_reversed_name" => "getHqpReversedName",
            "hqp_position" => "getHqpPosition",
            "hqp_awards" => "getHqpAwards",
            "user_hqp_role" => "getUserHqpRole",
            "hqp_start_date" => "getHqpStartDate",
            "hqp_end_date" => "getHqpEndDate",
            "hqp_status" => "getHqpStatus",
            "hqp_research_area" => "getHqpResearchArea",
            // Grants
            "grant_id" => "getGrantId",
            "grant_title" => "getGrantTitle",
            "grant_description" => "getGrantDescription",
            "grant_sponsor" => "getGrantSponsor",
            "grant_project_id" => "getGrantProjectId",
            "grant_start_date" => "getGrantStartDate",
            "grant_end_date" => "getGrantEndDate",
            "grant_total" => "getGrantTotal",
            // Outputs
            "output_id" => "getOutputId",
            "output_title" => "getOutputTitle",
            // Reports
            "timestamp" => "getTimestamp",
            "post_id" => "getPostId",
            "report_name" => "getReportName",
            "report_xmlname" => "getReportXMLName",
            "section_name" => "getSectionName",
            "report_has_started" => "getReportHasStarted",
            // People
            "my_id" => "getMyId",
            "my_name" => "getMyName",
            "my_first_name" => "getMyFirstName",
            "my_last_name" => "getMyLastName",
            "my_dept" => "getMyDept",
            "my_roles" => "getMyRoles",
            "my_sub_roles" => "getMySubRoles",
            "parent_id" => "getParentId",
            "parent_name" => "getParentName",
            "parent_uni" => "getParentUni",
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
            "user_sub_roles" => "getUserSubRoles",
            "user_level" => "getUserLevel",
            "user_dept" => "getUserDept",
            "user_uni" => "getUserUni",
            "user_fec" => "getUserFEC",
            "user_case_number" => "getUserCaseNumber",
            "user_nationality" => "getUserNationality",
            "user_supervisors" => "getUserSupervisors",
            "user_product_count" => "getUserProductCount",
            "user_grad_count" => "getUserGradCount",
            "user_msc_count" => "getUserMscCount",
            "user_phd_count" => "getUserPhdCount",
            "user_fellow_count" => "getUserFellowCount",
            "user_tech_count" => "getUserTechCount",
            "user_ugrad_count" => "getUserUgradCount",
            "user_other_count" => "getUserOtherCount",
            "user_committee_count" => "getUserCommitteeCount",
            "user_courses_count" => "getUserCoursesCount",
            "user_grant_count" => "getUserGrantCount",
            "user_cv_grant_count" => "getUserCVGrantCount",
            "user_grant_total" => "getUserGrantTotal",
            "user_phd_year" => "getUserPhdYear",
            "user_phd_date" => "getUserPhDDate",
            "user_appointment_year" => "getUserAppointmentYear",
            "user_appointment_date" => "getUserAppointmentDate",
            "getUserPublicationCount" => "getUserPublicationCount",
            "user_lifetime_pubs_count" => "getUserLifetimePublicationCount",
            // ISAC
            "chair_id" => "getChairId",
            // Products
            "product_id" => "getProductId",
            "product_type" => "getProductType",
            "product_title" => "getProductTitle",
            "product_authors" => "getProductAuthors",
            "product_description" => "getProductDescription",
            "product_url" => "getProductUrl",
            "product_citation" => "getProductCitation",
            "product_qa_citation" => "getProductQACitation",
            "product_date" => "getProductDate",
            "product_acceptance_year" => "getProductAcceptanceYear",
            "product_year" => "getProductYear",
            "product_year_range" => "getProductYearRange",
            "product_date_range" => "getProductDateRange",
            "product_scope" => "getProductScope",
            "getProductData" => "getProductData",
            //Presentations
            "presentation_title" => "getPresentationTitle",
            "presentation_type" => "getPresentationType",
            "presentation_invited" => "getPresentationInvited",
            "presentation_organization" => "getPresentationOrganization",
            "presentation_country" => "getPresentationCountry",
            "presentation_length" => "getPresentationLength",
            "presentation_date" => "getProductDate",
            //Awards
            "award_scope" => "getAwardScope",
            "award_by" => "getAwardedBy",
            "award_year_range" => "getAwardYearRange",
            // Other
            "wgUserId" => "getWgUserId",
            "wgServer" => "getWgServer",
            "wgScriptPath" => "getWgScriptPath",
            "GET" => "getGet",
            "networkName" => "getNetworkName",
            "id" => "getId",
            "time2date" => "time2date",
            "name" => "getName",
            "index" => "getIndex",
            "value" => "getValue",
            "extraIndex" => "getExtraIndex",
            "getNProducts" => "getNProducts",
            "getBlobMD5" => "getBlobMD5",
            "getText" => "getText",
            "getNumber" => "getNumber",
            "getHTML" => "getHTML",
            "getArray" => "getArray",
            "getExtra" => "getExtra",
            "strip_html" => "strip_html",
            "count" => "count",
            "add" => "add",
            "subtract" => "subtract",
            "multiply" => "multiply",
            "divide" => "divide",
            "round" => "round",
            "set" => "set",
            "get" => "get",
            "and" => "andCond",
            "or" => "orCond",
            "contains" => "contains",
            "!contains" => "notContains",
            "==" => "eq",
            "!=" => "neq",
            ">" => "gt",
            "<" => "lt",
            ">=" => "gteq",
            "<=" => "lteq",
        );
    
    var $reportItem;
    
    // Constructor
    function ReportItemCallback($reportItem){
        $this->reportItem = $reportItem;
    }
    
    function get2YearsAgo(){
        return $this->reportItem->getReport()->year-2;
    }
    
    function getStartYear(){
        return $this->reportItem->getReport()->startYear;
    }
    
    function getLastYear(){
        return $this->reportItem->getReport()->year-1;
    }
    
    function getThisYear(){
        return $this->reportItem->getReport()->year;
    }
    
    function getNextYear(){
        return $this->reportItem->getReport()->year+1;
    }
    
    function getNextYear2(){
        return $this->reportItem->getReport()->year+2;
    }
    
    function getHqpName(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $hqp = $relation->getUser2();
        return $hqp->getNameForForms();
    }
    
    function getHqpReversedName(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $hqp = $relation->getUser2();
        return $hqp->getReversedName();
    }

    function getHqpPosition(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $hqp = $relation->getUser2();
        return $hqp->getPosition();
    }
    
    function getHqpAwards(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $hqp = $relation->getUser2();
        $award_names = array();
        $awards = $hqp->getPapers("Award", false, 'both', true, "Public");
        foreach($awards as $award){
            $award_names[] = str_replace("Misc: ", "", str_replace("Other: ", "", $award->type));
        }
        return implode(",",$award_names);
    }
    
    function getUserHqpRole(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        switch($relation->type){
            case SUPERVISES:
                return "Supervisor";
                break;
            case CO_SUPERVISES:
                return "Co-Supervisor";
                break;
        }
        return $relation->type;
    }
   
    function getHqpStartDate(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $array = explode(" ", $relation->getStartDate());
        return str_replace("0000-00-00", "", $array[0]);
    }
    
    function getHqpEndDate(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $array = explode(" ", $relation->getEndDate());
        return str_replace("0000-00-00", "", $array[0]);
    }
    
    function getHqpResearchArea(){
        $relation = Relationship::newFromId($this->reportItem->projectId);
        $hqp = $relation->getUser2();
        return $hqp->getResearchArea();
    }

    function getHqpStatus(){
        if($this->getHqpEndDate() == '0000-00-00' || $this->getHqpEndDate() == ''){
            return "Continuing";
        }
        else{
            $status = "Completed";
            $relation = Relationship::newFromId($this->reportItem->projectId);
            $hqp = $relation->getUser2()->getId();
            $sql = "SELECT status FROM grand_movedOn WHERE
                    user_id = '$hqp' AND 
                    effective_date LIKE '%{$this->getHqpEndDate()}%'";
            $data = DBFunctions::execSQL($sql);
            if(count($data)>0 && $data[0]['status'] != ""){
                 $status = $data[0]['status'];
            }
            return $status;
        }
    }

    function getCourseTerm(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->getTerm();
    }
    
    function getCourseStart(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->getStartDate();
    }
    
    function getCourseEnd(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->getEndDate();
    }

    function getCourseSubject(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->subject;
    }

    function getCourseNumber(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->catalog;
    }
    
    function getCourseTitle(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->descr;
    }

    function getCourseComp(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->component;
    }

    function getCourseSection(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->sect;
    }

    function getCourseEnroll(){
        $course = Course::newFromId($this->reportItem->projectId);
        return $course->totEnrl;
    }

    function getCourseEnrollPercent(){
        $course = Course::newFromId($this->reportItem->projectId);
        return ($course->totEnrl/max(1,$course->capEnrl))*100;
    }
    
    function getGrantId(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return $grant->getId();
    }
 
    function getGrantTitle(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return $grant->getTitle();
    }
    
    function getGrantDescription(){
        $grant = Grant::newFromId($this->reportItem->productId);
        $description = $grant->getDescription();
        if(trim($description) == ""){
            $description = $grant->getTitle();
        }
        return $description;
    }
    
    function getGrantProjectId(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return $grant->getProjectId();
    }
    
    function getGrantSponsor(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return $grant->getSponsor();
    }
    
    function getGrantStartDate(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return time2date($grant->getStartDate(), "Y-m-d");
    }
    
    function getGrantEndDate(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return time2date($grant->getEndDate(), "Y-m-d");
    }
    
    function getGrantTotal(){
        $grant = Grant::newFromId($this->reportItem->productId);
        return number_format($grant->getTotal());
    }
    
    function getOutputId(){
        return $this->reportItem->extra." ".$this->reportItem->productId;
    }
    
    function getOutputTitle(){
        $class = $this->reportItem->extra;
        $obj = $class::newFromId($this->reportItem->productId);
        return $obj->getTitle();
    }
    
    function getReportHasStarted(){
        $report = $this->reportItem->getReport();
        if($report->hasStarted()){
            return "<span style='font-weight:bold;color:#008800;'>Yes</span>";
        }
        return "<span>No</span>";
    }
    
    function getMyId(){
        $person = Person::newFromWgUser();
        return $person->getId();
    }
    
    function getMyName(){
        $person = Person::newFromWgUser();
        return $person->getNameForForms();
    }
    
    function getMyFirstName(){
        $person = Person::newFromWgUser();
        return $person->getFirstName();
    }
    
    function getMyLastName(){
        $person = Person::newFromWgUser();
        return $person->getLastName();
    }
    
    function getMyDept(){
        $person = Person::newFromWgUser();
        $university = $person->getUniversity();
        return $university['department'];
    }
    
    function getMyRoles(){
        $person = Person::newFromWgUser();
        $roles = $person->getRoles();
        $roleNames = array();
        foreach($roles as $role){
            $roleNames[$role->getRole()] = $role->getRole();
        }
        return implode(", ", $roleNames);
    }
    
    function getMySubRoles(){
        global $config;
        $person = Person::newFromWgUser();
        $roles = array();
        foreach(@$person->getSubRoles() as $subRole){
            $roles[] = $config->getValue('subRoles', $subRole);
        }
        return implode(", ", $roles);
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
        return $this->reportItem->getParent()->personId;
    }
    
    function getParentName(){
        $person = Person::newFromId($this->getParentId());
        return $person->getNameForForms();
    }
    
    function getParentUni(){
        $person = Person::newFromId($this->getParentId());
        return $person->getUni();
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
        $roles = $person->getRoles();
        $roleNames = array();
        foreach($roles as $role){
            $roleNames[$role->getRole()] = $role->getRole();
        }
        return implode(", ", $roleNames);
    }
    
    function getUserFullRoles(){
        $person = Person::newFromId($this->reportItem->personId);
        $project = Project::newFromId($this->reportItem->projectId);
        $roles = $this->getUserRoles();
        return $roles;
    }
    
    function getUserSubRoles(){
        global $config;
        $person = Person::newFromId($this->reportItem->personId);
        $roles = array();
        foreach(@$person->getSubRoles() as $subRole){
            $roles[] = $config->getValue('subRoles', $subRole);
        }
        return implode(", ", $roles);
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
    
    function getUserFEC(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getFECType($this->reportItem->getReport()->year.CYCLE_END_MONTH);
    }
    
    function getUserCaseNumber(){
        $person = Person::newFromId($this->reportItem->personId);
        return $person->getCaseNumber($this->reportItem->getReport()->year);
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
                    if((strcmp($start, REPORTING_CYCLE_START."00:00:00") <= 0 && (strcmp($end, REPORTING_CYCLE_START."00:00:00") >= 0 || strcmp($end, "0000-00-00") == 0)) ||
                       (strcmp($start, REPORTING_CYCLE_END."00:00:00") <= 0 && strcmp($start, REPORTING_CYCLE_START."00:00:00") >= 0) ||
                       (strcmp($end, REPORTING_CYCLE_END."00:00:00") <= 0 && strcmp($end, REPORTING_CYCLE_START."00:00:00") >= 0)){
                        $sup = $rel->getUser1();
                        $supervisors[$sup->getId()] = "<a target='_blank' href='{$sup->getUrl()}'>{$sup->getNameForForms()}</a>";
                    }
                }
            }
        }
        return implode("; ", $supervisors);
    }

    function getUserPublicationCount($start_date,$end_date,$case='Publication'){
        $year = substr($start_date, 0, 4);
        $person = Person::newFromId($this->reportItem->personId);
        $category = "";
        switch($case){
            default:
            case "Publication":
                $category = "Publication";
                $type = "Journal Paper|Conference Paper|Proceedings Paper|Workshop Paper|Book Chapter";
                $histories = $person->getProductHistories($year, "Refereed");
                break;
            case "Book":
                $category = "Publication";
                $type = "Book";
                $histories = $person->getProductHistories($year, "Book");
                break;
            case "Patent":
                $category = "Patent/Spin-Off";
                $type = "Patent";
                $histories = $person->getProductHistories($year, "Patent");
                break;
        }
        
        if(count($histories) > 0){
            return $histories[0]->getValue();
        }
        $products = $person->getPapersAuthored($category, $start_date, $end_date, false, true, true);
        $count = 0;
        $types = explode("|", $type);
        foreach($products as $product){
            if(in_array($product->getType(), $types)){
                $reportedYear = $product->getReportedForPerson($this->reportItem->personId);
                if($reportedYear == ""){
                    // Not reported yet, but do some checks to make sure we don't count it twice
                    if($product->getDate() > $end_date){
                        // Found one, set the reported year to next year as long as next year isn't greater than the current reporting year
                        $reportedYear = min($year+1, $this->reportItem->getReport()->year-1);
                    }
                }
                if($reportedYear == "" || $reportedYear == $year){
                    if($case == "Publication"){
                        if($product->getData('peer_reviewed') == "Yes"){
                            $count++;
                        }
                    }
                    else{
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    function getUserLifetimePublicationCount($type='all'){
        $phdYear = min(2006, max("1900", $this->getUserPhdYear()-10)); // Start at the phd year -10 so there is some flexibility, but also no later than 2006
        $year = $this->reportItem->getReport()->year;
        $person = Person::newFromId($this->reportItem->personId);
        $count = 0;
        for($y=$phdYear; $y <= $year; $y++){
            switch($type){
                default:
                case "Publication":
                    $previousCounts = $person->getProductHistories($y-1, "Previous Refereed");
                    break;
                case "Book":
                    $previousCounts = $person->getProductHistories($y-1, "Previous Book");
                    break;
                case "Patent":
                    $previousCounts = $person->getProductHistories($y-1, "Previous Patent");
                    break;
            }
            if(count($previousCounts) > 0){
                // Reset the count
                $count = $previousCounts[0]->getValue();
            }
            $count += $this->getUserPublicationCount(($y-1)."-07-01",($y)."-06-30",$type);
        }
        return $count;
    }

    function getChairId(){
        $user = Person::newFromId($this->reportItem->personId);
        $people = Person::getAllPeople(ISAC);
        foreach($people as $person){
            if($person->getDepartment() == $user->getDepartment()){
                return $person->getId();
            }
        }
        return 0;
    }
    
    function getProductId(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getId();
    }
    
    function getProductType(){
        $product = Paper::newFromId($this->reportItem->productId);
        return str_replace("Misc: ", "", str_replace("Other: ", "", $product->getType()));
    }
    
    function getProductTitle(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getTitle();
    }
    
    function getProductAuthors(){
        $product = Paper::newFromId($this->reportItem->productId);
        $authors = $product->getAuthors();
        $array = array();
        foreach($authors as $author){
            $array[] = $author->getReversedName();
        }
        return implode("; ", $array);
    }
    
    function getProductDescription(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getDescription();
    }
    
    function getProductUrl(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getUrl();
    }
   
    function getProductCitation(){
        $product = Paper::newFromId($this->reportItem->productId);
        if($this->reportItem->personId > 0){
            return $product->getCitation(true, true, false, false, $this->reportItem->personId);
        }
        return $product->getCitation(true, true, false, false, true);
    }
    
    function getProductQACitation(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getCitation(true, false, false, false, true);
    }
    
    function getProductScope(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getData('scope');
    }
    
    function getProductData($field){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getData($field);
    }

    function getPresentationTitle(){
        $product = Paper::newFromId($this->reportItem->productId);
        return $product->getTitle();
    }
    
    function getPresentationType(){
        $product = Paper::newFromId($this->reportItem->productId);
        return str_replace("Misc: ", "", str_replace("Other: ", "", $product->getType()));
    }

    function getPresentationInvited(){
        $product = Paper::newFromId($this->reportItem->productId);
        $status = $product->getStatus();
        if($status == "Invited"){
            return "Yes";
        }
        return "No";
    }
      
    function getPresentationOrganization(){
        $product = Paper::newFromId($this->reportItem->productId);
        $product = $product->getData();
        return @$product['organizing_body'];
    }
    
    function getPresentationCountry(){
        $product = Paper::newFromId($this->reportItem->productId);
        $product = $product->getData();
        return @$product['location'];
    }
    
    function getPresentationLength(){
        $product = Paper::newFromId($this->reportItem->productId);
        $product = $product->getData();
        return @$product['length'];
    }
    
    function getProductDate(){
        $product = Paper::newFromId($this->reportItem->productId);
        return @$product->getDate();
    }
    
    function getAwardScope(){
        $product = Paper::newFromId($this->reportItem->productId);
        $product = $product->getData();
        return @$product['scope'];
    }
    
    function getAwardedBy(){
        $product = Paper::newFromId($this->reportItem->productId);
        $product = $product->getData();
        return @$product['awarded_by'];
    }
    
    function getAwardYearRange(){
        $product = Paper::newFromId($this->reportItem->productId);
        $startYear = substr($product->getData('start_date'), 0, 4);
        $endYear = substr($product->getData('end_date'), 0, 4);
        if($startYear == $endYear){
            return $endYear;        
        }
        else if($startYear == "0000"){
            return $endYear;
        }
        else if($endYear == "0000"){
            return "{$startYear} - Present";
        }
        else{
            return "{$startYear} - {$endYear}";
        }
    }
    
    function getProductAcceptanceYear(){
        $product = Paper::newFromId($this->reportItem->productId);
        return @$product->getAcceptanceYear();
    }
    
    function getProductYear(){
        $product = Paper::newFromId($this->reportItem->productId);
        return @$product->getYear();
    }
    
    function getProductYearRange(){
        $product = Paper::newFromId($this->reportItem->productId);
        $startYear = $product->getAcceptanceYear();
        $endYear = $product->getYear();
        if($startYear == $endYear){
            return $endYear;        
        }
        else if($startYear == "0000"){
            return $endYear;
        }
        else if($endYear == "0000"){
            return "{$startYear} - Present";
        }
        else{
            return "{$startYear} - {$endYear}";
        }
    }
    
    function getProductDateRange(){
        $product = Paper::newFromId($this->reportItem->productId);
        $startYear = $product->getAcceptanceYear();
        $endYear = $product->getYear();
        $startDate = $product->getAcceptanceDate();
        $endDate = $product->getDate();
        if($startDate == $endDate){
            return $endDate;
        }
        else if($startYear == "0000"){
            return $endDate;
        }
        else if($endYear == "0000"){
            return "{$startDate} - Present";
        }
        else{
            return "{$startDate} - {$endDate}";
        }
    }

    function getWgUserId(){
        global $wgUser;
        return $wgUser->getId();
    }
    
    function getWgServer(){
        global $wgServer;
        return $wgServer;
    }
    
    function getWgScriptPath(){
        global $wgScriptPath;
        return $wgScriptPath;
    }

    function getGet($var1){
        if(isset($_GET[$var1])){
            return $_GET[$var1];
        }
        return "";
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
    
    function time2date($time, $format='F j, Y'){
        return time2date($time, $format);
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
        foreach($set->getCachedData() as $item){
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
    
    function getValue(){
        $default = $this->reportItem->getAttr('default', '');
		if($default != ''){
		    return $default;
		}
		else{
		    return $this->reportItem->getBlobValue();
		}
    }
    
    function getExtraIndex(){
        $set = $this->reportItem->getSet();
        while(!($set instanceof ArrayReportItemSet)){
            $set = $set->getParent();
            if($set instanceof AbstractReport){
                return 0;
            }
        }
        foreach($set->getData() as $index => $item){
            if($item['extra'] == $this->reportItem->extra){
                return $index;
            }
        }
        return 0;
    }
    
    function getNProducts($startDate = false, $endDate = false, $category="all", $type="all", $data="", $includeHQP="true", $peerReviewed="false"){
        $products = array();
        $includeHQP = (strtolower($includeHQP) == "true" || $includeHQP === true);
        $peerReviewed = (strtolower($peerReviewed) == "true" || $peerReviewed === true);
        if($this->reportItem->projectId != 0){
            // Project Products
            $project = Project::newFromId($this->reportItem->projectId);
            $products = $project->getPapers($category, $startDate, $endDate);
        }
        else if($this->reportItem->personId != 0){
            // Person Products
            $person = Person::newFromId($this->reportItem->personId);
            $products = $person->getPapersAuthored($category, $startDate, $endDate, $includeHQP, true);
        }
        if($type != "all"){
            $types = explode("|", $type);
            foreach($products as $key => $product){
                $type = explode(":", $product->getType());
                if(!in_array($type[0], $types)){
                    // Type doesn't match
                    unset($products[$key]);
                }
            }
        }
        if($data != ""){
            foreach($products as $key => $product){
                $productData = $product->getData();
                $datas = explode("=", $data);
                if(isset($productData[$datas[0]]) && $productData[$datas[0]] != $datas[1]){
                    // Data doesn't match
                    unset($products[$key]);
                }
            }
        }
        //var_dump($peerReviewed);
        if($peerReviewed){
            foreach($products as $key => $product){
                if($product->getData('peer_reviewed') != "Yes"){
                    // Not Peer Reviewed
                    unset($products[$key]);
                }
            }
        }
        return count($products);
    }
    
    function getBlobMD5($rp, $section, $blobId, $subId, $personId, $projectId){
        $addr = ReportBlob::create_address($rp, $section, $blobId, $subId);
        $blb = new ReportBlob(BLOB_PDF, $this->reportItem->getReport()->year, $personId, $projectId);
        $result = $blb->load($addr, true);
        return $blb->getMD5();
    }
    
    function getArray($rp, $section, $blobId, $subId, $personId, $projectId, $index=null, $delim=", "){
        $addr = ReportBlob::create_address($rp, $section, $blobId, $subId);
        $blb = new ReportBlob(BLOB_ARRAY, $this->reportItem->getReport()->year, $personId, $projectId);
        $result = $blb->load($addr);
        if($index == null){
            return $blb->getData();
        }
        else{
            $array = $blb->getData();
            $index = explode("|", $index);
            foreach($index as $i){
                $array = @$array[$i];
            }
            $value = $array;
            if(is_array($value) && $delim != ""){
                return str_replace(")", "&#41;", str_replace("(", "&#40;", @implode($delim, $value)));
            }
            return $value;
        }
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
    
    function count($val){
        return count($val);
    }
    
    function concat(){
        $args = func_get_args();
        $concat = "";
        foreach($args as $arg){
            $concat .= $arg;
        }
        return $concat;
    }
    
    function add(){
        $args = func_get_args();
        $sum = 0;
        foreach($args as $arg){
            $sum += $arg;
        }
        return $sum;
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
        return number_format(round($val, $dec), $dec, ".", "");
    }
    
    function set($key, $val){
        $this->reportItem->setVariable($key, $val);
    }
    
    function get($key){
        return $this->reportItem->getVariable($key);
    }
    
    function andCond(){
        $bool = true;
        $arg_list = func_get_args();
        foreach($arg_list as $arg){
            $bool = ($bool && $arg);
        }
        return $bool;
    }
    
    function orCond(){
        $bool = false;
        $arg_list = func_get_args();
        foreach($arg_list as $arg){
            $bool = ($bool || $arg);
        }
        return $bool;
    }
    
    function contains($val1, $val2){
        return (strstr($val1, $val2) !== false);
    }
    
    function notContains($val1, $val2){
        return !$this->contains($val1, $val2);
    }
    
    function eq($val1, $val2){
        return ($val1 == $val2);
    }
    
    function neq($val1, $val2){
        return ($val1 != $val2);
    }
    
    function gt($val1, $val2){
        return ($val1 > $val2);
    }
    
    function lt($val1, $val2){
        return ($val1 < $val2);
    }
    
    function gteq($val1, $val2){
        return ($val1 >= $val2);
    }
    
    function lteq($val1, $val2){
        return ($val1 <= $val2);
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
    
    function getExtra($index=null){
        $set = $this->reportItem->extra;
        if($index == null){
            return str_replace(")", "&#41;", 
                   str_replace("(", "&#40;", $set));
        }
        if(isset($set[$index])){
            return str_replace(")", "&#41;", 
                   str_replace("(", "&#40;", $set[$index]));
        }
        return "";
    }
    
    function strip_html($html){
        return strip_tags($html);
    }
    
    function getPostId(){
        return $this->reportItem->getPostId();
    }
    
    function getTimestamp(){
        $date = new DateTime("now", new DateTimeZone(date_default_timezone_get())); // USER's timezone
        return $date->format('Y-m-d H:i:s T');
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

    function getUserProductCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $products = $person->getPapersAuthored('all', ($this->reportItem->getReport()->startYear)."-07-01", ($this->reportItem->getReport()->year)."-06-30", true);
        $products = $person->getPapers("all", false, 'both', true, "Public");
        return count($products);
    }

    function getUserGradCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
           
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['grad']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserMscCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
           
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['msc']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserPhDCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
           
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['phd']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }

    function getUserFellowCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
            
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['pdf']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserTechCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
            
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['tech']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserUgradCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
            
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['ugrad']) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserOtherCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = array_merge(
            $person->getRelationsDuring(SUPERVISES, $startDate, $endDate),
            $person->getRelationsDuring(CO_SUPERVISES, $startDate, $endDate)
        );
        $count = 0;
        $hqpsDone = array();
        $merged = array();
        foreach(Person::$studentPositions as $array){
            $merged = array_merge($merged, $array);
        }
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
            
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            foreach($universities as $university){
                if(!in_array(strtolower($university['position']), $merged) &&
                   !($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserCommitteeCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $startDate = ($this->reportItem->getReport()->startYear)."-07-01";
        $endDate = ($this->reportItem->getReport()->year)."-06-30";
        $relations = $person->getRelationsDuring('all', $startDate, $endDate);
        $count = 0;
        $hqpsDone = array();
        $merged = array();
        foreach(Person::$studentPositions as $array){
            $merged = array_merge($merged, $array);
        }
        foreach($relations as $relation){
            $hqp = $relation->getUser2();
            $role = $relation->getType();
            
            if($role == SUPERVISES || $role == CO_SUPERVISES){
                continue;
            }
            
            if(isset($hqpsDone[$hqp->getId()])){
                continue;
            }
            
            if($relation->getEndDate() != "0000-00-00"){
                // Normal Date range
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), $relation->getEndDate());
            }
            else{
                // Person is still continuing
                $universities = $hqp->getUniversitiesDuring($relation->getStartDate(), "2100-00-00");
            }
            if(count($universities) == 0){
                // Nothing was found, just get everything
                $universities = $hqp->getUniversitiesDuring("0000-00-00", "2100-00-00");
            }
            if(count($universities) == 0){
                // Still Nothing was found, so skip this person
                continue;
            }
            
            $found = false;
            foreach($universities as $university){
                if(!($university['start'] < $startDate && $university['end'] < $startDate && $university['end'] != "0000-00-00 00:00:00")){
                    $count++;
                    $hqpsDone[$hqp->getId()] = true;
                    break;
                }
            }
        }
        return $count;
    }
    
    function getUserCoursesCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $courses = $person->getCoursesDuring(($this->reportItem->getReport()->startYear)."-07-01", ($this->reportItem->getReport()->year)."-06-30");
        $count = 0;
        foreach($courses as $course){
            if($course->totEnrl > 0){
                $count++;
            }
        }
        return $count;
    }
    
    function getUserGrantCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $grants = $person->getGrantsBetween(($this->reportItem->getReport()->startYear)."-07-01", ($this->reportItem->getReport()->year)."-06-30");
        return count($grants);
    }
    
    function getUserCVGrantCount(){
        $person = Person::newFromId($this->reportItem->personId);
        $grants = $person->getGrantsBetween(($this->reportItem->getReport()->startYear)."-07-01", ($this->reportItem->getReport()->year)."-06-30", true);
        return count($grants);
    }

    function getUserGrantTotal(){
        $person = Person::newFromId($this->reportItem->personId);
        $grants = $person->getGrantsBetween(($this->reportItem->getReport()->startYear)."-07-01", ($this->reportItem->getReport()->year)."-06-30");
        $total = 0;
        foreach($grants as $grant){
            $total += $grant->getTotal();
        }
        return number_format($total, 2);
    }

    function getUserPhdYear(){
        $person = Person::newFromId($this->reportItem->personId);
        $fecInfo = $person->getFecPersonalInfo();
        $phd_year_array = explode("-", $fecInfo->dateOfPhd);
        return $phd_year_array[0];
    }

    function getUserAppointmentYear(){
        $person = Person::newFromId($this->reportItem->personId);
        $fecInfo = $person->getFecPersonalInfo();
        $phd_year_array = explode("-", $fecInfo->dateOfAppointment);
        return $phd_year_array[0];
    }
    
    function getUserPhdDate(){
        $person = Person::newFromId($this->reportItem->personId);
        $fecInfo = $person->getFecPersonalInfo();
        return substr($fecInfo->dateOfPhd, 0, 10);
    }

    function getUserAppointmentDate(){
        $person = Person::newFromId($this->reportItem->personId);
        $fecInfo = $person->getFecPersonalInfo();
        return substr($fecInfo->dateOfAppointment, 0, 10);
    }
}

?>
