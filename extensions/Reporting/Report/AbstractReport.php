<?php

/**
 * @package Report
 * @abstract
 */

$wgHooks['CheckImpersonationPermissions'][] = 'AbstractReport::checkImpersonationPermissions';
$wgHooks['ImpersonationMessage'][] = 'AbstractReport::impersonationMessage';
$wgHooks['CanUserReadPDF'][] = 'AbstractReport::canUserReadPDF';
$wgHooks['UnknownAction'][] = 'AbstractReport::downloadBlob';
$wgHooks['UnknownAction'][] = 'AbstractReport::tinyMCEUpload';

require_once("ReportConstants.php");
require_once("ReportDashboardTableTypes.php");
require_once("SpecialPages/{$config->getValue('networkName')}/Report.php");
require_once("SpecialPages/{$config->getValue('networkName')}/DummyReport.php");
if(file_exists("SpecialPages/{$config->getValue('networkName')}/ReportPDFs.php")){
    require_once("SpecialPages/{$config->getValue('networkName')}/ReportPDFs.php");
}
if(file_exists("SpecialPages/{$config->getValue('networkName')}/ReportSurvey.php")){
    require_once("SpecialPages/{$config->getValue('networkName')}/ReportSurvey.php");
}

autoload_register('Reporting/Report');
autoload_register('Reporting/Report/ReportSections');
autoload_register('Reporting/Report/ReportItems');
autoload_register('Reporting/Report/ReportItems/StaticReportItems');
autoload_register('Reporting/Report/ReportItems/ReportItemSets');

abstract class AbstractReport extends SpecialPage {
    
    var $name;
    var $year;
    var $xmlName;
    var $extends;
    var $reportType;
    var $ajax;
    var $header;
    var $headerName;
    var $sections;
    var $currentSection;
    var $permissions;
    var $sectionPermissions;
    var $person;
    var $project;
    var $readOnly = false;
    var $topProjectOnly;
    var $generatePDF;
    var $pdfType;
    var $pdfFiles;
    var $pdfAllProjects;
    var $showInstructions = true;
    var $variables = array();
    
    /**
     * @param string $tok
     * @return DummyReport Returns the DummyReport associated with the given $tok
     */
    static function newFromToken($tok){
        global $wgUser;
        $person = Person::newFromId($wgUser->getId());
        $sto = new ReportStorage($person);
        $sto->select_report($tok, false);
        
        $pers = Person::newFromId($sto->metadata('user_id'));
        $pers->id = $sto->metadata('user_id');
        $type = $sto->metadata('type');
        $year = $sto->metadata('year');
        
        $type = ReportXMLParser::findPDFReport($type, true);
        
        $proj = null;
        $rp_index = new ReportIndex($pers);
        $projects = $rp_index->list_projects();
        foreach($projects as $project){
            $reports = $rp_index->list_reports($project, 0, 0);
            foreach($reports as $report){
                if($report['token'] == $tok){
                    $proj = Project::newFromId($project);
                    break;
                }
            }
            if($proj != null){
                break;
            }
        }
        return new DummyReport($type, $pers, $proj, $year);
    }
    
    // Creates a new AbstractReport from the given $xmlFileName
    // $personId forces the report to use a specific user id as the owner of this Report
    // $projectName is the name of the Project this Report belongs to
    // $topProjectOnly means that the Report should override all ReportItemSets which use Projects as their data with the Project belonging to $projectName
    function AbstractReport($xmlFileName, $personId=-1, $projectName=false, $topProjectOnly=false, $year=REPORTING_YEAR){
        global $wgUser, $wgMessage, $config;
        $this->name = "";
        $this->extends = "";
        $this->year = $year;
        $this->reportType = RP_RESEARCHER;
        $this->disabled = false;
        $this->ajax = false;
        $this->generatePDF = false;
        $this->pdfType = RPTP_NORMAL;
        $this->pdfFiles = array();
        $this->header = null;
        $this->sections = array();
        $this->permissions = array();
        $this->sectionPermissions = array();
        $this->topProjectOnly = $topProjectOnly;
        $this->pdfAllProjects = false;
        if($personId == -1){
            $this->person = Person::newFromId($wgUser->getId());
        }
        else{
            $this->person = Person::newFromId($personId);
        }
        if($projectName === false && isset($_GET['project'])){
            $projectName = $_GET['project'];
        }
        if($projectName != null){
            $this->project = Project::newFromName($projectName);
        }
        if(isset($_GET['generatePDF'])){
            $this->generatePDF = true;
        }
        if(file_exists($xmlFileName)){
            $exploded = explode(".", $xmlFileName);
            $exploded = explode("/ReportXML/{$config->getValue('networkName')}/", $exploded[count($exploded)-2]);
            $this->xmlName = $exploded[count($exploded)-1];
            $xml = file_get_contents($xmlFileName);
            $parser = new ReportXMLParser($xml, $this);
            if(isset($_COOKIE['showSuccess'])){
                unset($_COOKIE['showSuccess']);
                setcookie('showSuccess', 'true', time()-(60*60), '/');
                $wgMessage->addSuccess("Report Loaded Successfully.");
            }
            if(isset($_GET['saveBackup']) || isset($_GET['saveBackup'])){
                ini_set("memory_limit","1024M");
            }
            if(isset($_POST['loadBackup']) && !$this->readOnly){
                $status = $parser->loadBackup();
                if($status){
                    $parser->parse();
                    setcookie('showSuccess', 'true', time()+(60), '/');
                    redirect("{$wgServer}{$_SERVER["REQUEST_URI"]}");
                }
            }
            $parser->parse();
            if(isset($_GET['saveBackup'])){
                $parser->saveBackup();
            }
            
            $currentSection = @$_GET['section'];
            foreach($this->sections as $section){
                if($section->name == $currentSection && $currentSection != ""){
                    $this->currentSection = $section;
                    break;
                }
            }
            if($this->currentSection == null){
                $i = 0;
                if(isset($this->sections[$i])){
                    $permissions = $this->getSectionPermissions($this->sections[$i]);
                    while(isset($this->sections[$i]) && 
                          (
                            (($this->sections[$i] instanceof HeaderReportSection) || !isset($permissions['r'])) ||
                            ($this->topProjectOnly && $this->sections[$i]->private))
                          ){
                        $i++;
                        if(isset($this->sections[$i])){
                            $permissions = $this->getSectionPermissions($this->sections[$i]);
                        }
                    }
                    $this->currentSection = @$this->sections[$i];
                }
            }
            if($this->currentSection == null){
                // If this gets run, it will probably result in a permissions error, but atleast it error out later
                $this->currentSection = @$this->sections[0];
            }
            $this->currentSection->selected = true;
            SpecialPage::__construct("Report", '', false);
        }
        else{
            SpecialPage::__construct("Report", '', false);
        }
    }
    
    function execute(){
        global $wgOut, $wgServer, $wgScriptPath, $wgUser, $wgImpersonating, $wgRealUser;
        if($this->name != ""){
            if((isset($_POST['submit']) && $_POST['submit'] == "Save") || isset($_GET['showInstructions'])){
                $managerImpersonating = false;
                if($wgImpersonating){
                    $realPerson = Person::newFromUser($wgRealUser);
                    $managerImpersonating = $realPerson->isRoleAtLeast(MANAGER);
                }
                if(!$managerImpersonating && (!$wgUser->isLoggedIn() || ($wgImpersonating && !$this->checkPermissions()) || !DBFunctions::DBWritable() || (isset($_POST['user']) && $_POST['user'] != $wgUser->getName()))){
                    header('HTTP/1.1 403 Authentication Required');
                    exit;
                }
            }
            if(!$this->checkPermissions()){
                $wgOut->setPageTitle("Permission error");
                $wgOut->addHTML("<p>You are not allowed to execute the action you have requested.</p>
                                 <p>Return to <a href='$wgServer$wgScriptPath/index.php/Main_Page'>Main Page</a>.</p>");
                return;
            }
            if(isset($_POST['submit']) && ($_POST['submit'] == "Save" || $_POST['submit'] == "Next")){
                $oldData = array();
                parse_str(@$_POST['oldData'], $oldData);
                $_POST['oldData'] = $oldData;
                $json = array();
                if($this->currentSection instanceof EditableReportSection){
                    $json = $this->currentSection->saveBlobs();
                }
                if($this->ajax){
                    header('Content-Type: text/json');
                    echo json_encode($json);
                    exit;
                }
            }
            if(isset($_GET['showSection'])){
                header("Expires: ".gmdate("D, d M Y H:i:s")." GMT"); // Always expired 
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified 
                header("Cache-Control: no-cache, must-revalidate");// HTTP/1.1 
                header("Pragma: nocache");// HTTP/1.0
                session_write_close();
                $this->currentSection->render();
                echo $wgOut->getHTML();
                exit;
            }
            else if(isset($_GET['showInstructions'])){
                session_write_close();
                echo $this->currentSection->getInstructions();
                exit;
            }
            else if(isset($_GET['getProgress'])){
                session_write_close();
                $prog = array();
                foreach($this->sections as $section){
                    if($section instanceof EditableReportSection){
                        $prog[str_replace(" ", "", $section->name)] = $section->getPercentComplete();
                    }
                }
                header('Content-Type: text/json');
                echo json_encode($prog);
                exit;
            }
            else if(isset($_GET['submitReport'])){
                $me = Person::newFromId($wgUser->getId());
                foreach($this->pdfFiles as $file){
                    if($this->pdfAllProjects){
                        foreach($this->person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
                            if(!$project->isSubProject()){
                                $report = new DummyReport($file, $this->person, $project, $this->year);
                                $report->submitReport();
                            }
                        }
                    }
                    $report = new DummyReport($file, $this->person, $this->project, $this->year);
                    $report->submitReport();
                    break; //Temporary solution to not submitting NI Report Comments PDF (2nd PDF and only 1 2nd PDF among all reports)
                }
                exit;
            }
            else if(isset($_GET['getPDF'])){
                header('Content-Type: application/json');
                echo json_encode($this->getPDF());
                exit;
            }
            if(!$this->generatePDF){
                $wgOut->setPageTitle($this->name);
                $this->render();
            }
            else{
                $this->generatePDF();
            }
        }
        else{
            // File not found
            $wgOut->setPageTitle("Report not Found");
            $wgOut->addHTML("The report specified does not exist");
        }
    }
    
    function notifySupervisors($tok){
        global $wgServer, $wgScriptPath;
        $alreadySeen = array();
        $supervisors = $this->person->getSupervisors(true);
        $realSupervisors = array();
        foreach($supervisors as $supervisor){
            if(isset($alreadySeen[$supervisor->getId()])){
                continue;
            }
            $alreadySeen[$supervisor->getId()] = true;
            $hqps = $supervisor->getHQPDuring($this->year.REPORTING_CYCLE_START_MONTH, $this->year.REPORTING_CYCLE_END_MONTH);
            foreach($hqps as $hqp){
                if($hqp->getId() == $this->person->getId()){
                    $realSupervisors[] = $supervisor;
                    break;
                }
            }
        }
        foreach($realSupervisors as $supervisor){
            $alreadySeen[$supervisor->getId()] = true;
            Notification::addNotification($this->person, $supervisor, "HQP Report Complete", "{$this->person->getReversedName()} completed their HQP Report.", "$wgServer$wgScriptPath/index.php/Special:ReportArchive?getpdf=$tok");
        }
    }
    
    function getLatestPDF(){
        if(isset($this->pdfFiles[0]) && $this->pdfFiles[0] != $this->xmlName){
            $file = $this->pdfFiles[0];
            $report = new DummyReport($file, $this->person, $this->project);
            return $report->getLatestPDF();
        }
        $sto = new ReportStorage($this->person);
        if($this->project != null){
            if($this->pdfAllProjects){
                $check = $sto->list_user_project_reports($this->project->getId(), $this->person->getId(), 0, 0, $this->pdfType);
            }
            else{
                $check = $sto->list_project_reports($this->project->getId(), 0, 0, $this->pdfType, $this->year);
            }
        }
        else{
            $check = array_merge($sto->list_reports($this->person->getId(), SUBM, 0, 0, $this->pdfType, $this->year), 
                                 $sto->list_reports($this->person->getId(), NOTSUBM, 0, 0, $this->pdfType, $this->year));
        }
        $largestDate = "0000-00-00 00:00:00";
        $return = array();
        foreach($check as $c){
            $tst = $c['timestamp'];
            if(strcmp($tst, $largestDate) > 0){
                $largestDate = $tst;
                $return = array($c);
            }
        }
        return $return;
    }
    
    function getPDF($submittedByOwner=false){
        if(isset($this->pdfFiles[0]) && $this->pdfFiles[0] != $this->xmlName){
            $file = $this->pdfFiles[0];
            $report = new DummyReport($file, $this->person, $this->project);
            return $report->getPDF();
        }
        $sto = new ReportStorage($this->person);
        $foundSameUser = false;
        $foundSubmitted = false;
        if($this->project != null){
            if($this->pdfAllProjects){
                $check = $sto->list_user_project_reports($this->project->getId(), $this->person->getId(), 0, 0, $this->pdfType);
            }
            else{
                $check = $sto->list_project_reports($this->project->getId(), 0, 0, $this->pdfType, $this->year);
                $check2 = array();
                foreach($check as $c){
                    if($c['submitted'] == 1){
                        $foundSubmitted = true;
                    }
                    else{
                        $check2[] = $c;
                    }
                }
            }
        }
        else{
            // First check submitted
            $check = $sto->list_reports($this->person->getId(), SUBM, 0, 0, $this->pdfType, $this->year);
            $check2 = $sto->list_reports($this->person->getId(), NOTSUBM, 0, 0, $this->pdfType, $this->year);
            if(count($check) == 0){
                // If found none, then look for any generated PDF
                $check = $check2;
            }
            foreach($check as $c){
                if($c['generation_user_id'] == $c['user_id']){
                   $foundSameUser = true;
                   break;
                }
            }
        }
        foreach($check as $key => $c){
            if($foundSameUser && $c['generation_user_id'] != $c['user_id']){
                unset($check[$key]);
            }
            if($foundSubmitted && $c['submitted'] != 1){
                unset($check[$key]);
            }
        }
        $largestDate = "0000-00-00 00:00:00";
        $return = array();
        foreach($check as $c){
            $tst = $c['timestamp'];
            if($c['submitted'] == 1){
                $c['status'] = "Generated/Submitted";
            }
            else if($foundSameUser){
                $c['status'] = "Generated/Not Submitted";
            }
            else if(!$foundSameUser){
                $c['status'] = "Generated/Not Submitted";
            }
            else{
                $c['status'] = "Generated/Not Submitted";
            }
            $c['name'] = $this->name;
            if(strcmp($tst, $largestDate) > 0){
                $largestDate = $tst;
                $return = array($c);
            }
        }
        if(isset($check2) && count($check2) > 0){
            foreach($check2 as $chk){
                if($chk['timestamp'] > $largestDate){
                    $return[0]['status'] = "Submitted/Re-Generated";
                }
            }
        }
        return $return;
    }
    
    // Sets the name of this Report
    function setName($name){
        if($this->project != null){
            $this->name = $name.": {$this->project->getName()}";
        }
        else{
            $this->name = $name;
        }
    }
    
    function setHeaderName($name){
        $section = new ReportSection();
        $item = new StaticReportItem();
        $section->parent = $this;
        $item->parent = $section;
        if($this->person != null){
            $item->setPersonId($this->person->getId());
        }
        if($this->project != null){
            $item->setProjectId($this->project->getId());
        }
        $this->headerName = $item->varSubstitute($name);
    }
    
    // Specifies which report this one inherits from
    function setExtends($extends){
        $this->extends = $extends;
    }
    
    // Sets whether or not this Report should be disabled or not
    function setDisabled($disabled){
        $this->disabled = $disabled;
    }
    
    // Sets the type of Report
    function setReportType($type){
        $this->reportType = $type;
    }
    
    // Sets the type of PDF to generate when generatePDF is called
    function setPDFType($type){
        $this->pdfType = $type;
    }
    
    // Sets the PDF Files that this Report will generate
    function setPDFFiles($files){
        $this->pdfFiles = explode(",", $files);
    }
    
    // Sets whether or not this Report should generate a different PDF for every Project this user is a member of
    function setPDFAllProjects($allProjects){
        $this->pdfAllProjects = $allProjects;
    }
    
    function setHeader($header){
        $header->setParent($this);
        $this->header = $header;
    }
    
    // Adds a new section to this Report
    function addSection($section, $position=null){
        $section->setParent($this);
        if($position == null){
            $this->sections[] = $section;
        }
        else{
            array_splice($this->sections, $position, 0, array($section));
        }
    }
    
    // Deleted the given ReportItem from this AbstractReport
    function deleteSection($section){
        foreach($this->sections as $key => $sec){
            if($section->id == $sec->id){
                unset($this->sections[$key]);
                return;
            }
        }
    }
    
    // Returns the section with the given id, or null if there is no such section
    function getSectionById($sectionId){
        foreach($this->sections as $section){
            if($section->id == $sectionId){
                return $section;
            }
        }
        return null;
    }
    
    // Returns whether or not this AbstractReport has a section of type SubReportSection
    function hasSubReport(){
        foreach($this->sections as $section){
            if(get_class($section) == "SubReportSection"){
                return true;
            }
        }
        return false;
    }
    
    // Adds a new Permission to this Report
    function addPermission($type, $permission, $start=null, $end=null){
        if($start == null){
            $start = "0000-00-00";
        }
        if($end == null){
            $end = "2100-12-31";
        }
        $this->permissions[$type][] = array('perm' => $permission, 'start' => $start, 'end' => $end);
    }
    
    function addSectionPermission($sectionId, $role, $permissions){
        $permissions = str_split($permissions);
        if(count($permissions) > 0){
            foreach($permissions as $permission){
                $this->sectionPermissions[$role][$sectionId][$permission] = true;
            }
        }
        else{
            // No permissions were explicitly defined, so add 'empty' permission (signals that the user has no permissions)
            $this->sectionPermissions[$role][$sectionId][""] = true;
        }
    }
    
    // Returns the percent of the number of chars used in all the limited textareas
    function getPercentChars(){
        $percents = array();
        foreach($this->sections as $section){
            $percents["{$section->name}"] = $section->getPercentChars();
        }
        return $percents;
    }
    
    // Returns an array of the ReportSections which are to be rendered when generating a PDF
    function getPDFRenderableSections(){
        $sections = array();
        foreach($this->sections as $section){
            if($section->renderPDF){
                $sections[] = $section;
            }
        }
        return $sections;
    }
    
    /**
     * Returns whether or not the Person has started the report yet
     * @return boolean Whether or not the Person has started the report yet
     */
    function hasStarted(){
        $personId = $this->person->getId();
        $projectId = ($this->project != null) ? $this->project->getId() : 0;
        $data = DBFunctions::select(array('grand_report_blobs'),
                                    array('*'),
                                    array('user_id' => EQ($personId),
                                          'proj_id' => EQ($projectId),
                                          'rp_type' => EQ($this->reportType),
                                          'year' => EQ($this->year)));
        return (count($data) > 0);
    }
    
    // Checks the permissions of the Person with the required Permissions of the Report
    function checkPermissions(){
        global $wgUser;
        $me = Person::newFromId($wgUser->getId());
        if(isset($_GET['reportingYear']) && !$me->isRoleAtLeast(MANAGER)){
            // Check that the user has a ticket for the specified report year
            $year = $_GET['reportingYear'];
            $ticket = @$_GET['ticket'];
            if(!$me->hasReportingTicket($this->project, $year, $this->reportType, $ticket)){
                return false;
            }
        }
        $me = Person::newFromId($wgUser->getId());
        $rResult = $me->isRoleAtLeast(MANAGER);
        $pResult = false;
        $nProjectTags = 0;
        foreach($this->permissions as $type => $perms){
            foreach($perms as $perm){
                switch($type){
                    case "Role":
                        if($perm['perm']['role'] == INACTIVE && !$me->isActive()){
                            $rResult = true;
                        }
                        else if($this->project != null && $perm['perm']['role'] == CHAMP && $me->isRole(CHAMP)){
                            if($me->isChampionOfOn($this->project, $perm['end']) && !$this->project->isSubProject()){
                                $rResult = true;
                            }
                            else {
                                foreach($this->project->getSubProjects() as $sub){
                                    if($me->isChampionOfOn($sub, $perm['end'])){
                                        $rResult = true;
                                    }
                                }
                            }
                        }
                        else if($this->project != null && ($perm['perm']['role'] == PL || $perm['perm']['role'] == "Leadership")){
                            $project_objs = $me->leadershipDuring($perm['start'], $perm['end']);
                            if(count($project_objs) > 0){
                                foreach($project_objs as $project){
                                    if(!$project->isSubProject() && $project->getId() == $this->project->getId()){
                                        $rResult = true;
                                    }
                                }
                            }
                        }
                        else if($this->project != null && ($perm['perm']['role'] == "SUB-PL")){
                            $project_objs = $me->leadershipDuring($perm['start'], $perm['end']);
                            if(count($project_objs) > 0){
                                foreach($project_objs as $project){
                                    if($project->isSubProject() && $project->getId() == $this->project->getId()){
                                        $rResult = true;
                                    }
                                }
                            }
                        }
                        else if($this->project != null && ($perm['perm']['role'] == TC || $perm['perm']['role'] == TL)){
                            $project_objs = $me->getThemeProjects();
                            if(count($project_objs) > 0){
                                foreach($project_objs as $project){
                                    if($project->getId() == $this->project->getId()){
                                        $rResult = true;
                                    }
                                }
                            }
                        }
                        else{
                            if(strstr($perm['perm']['role'], "+") !== false){
                                $rResult = ($rResult || $me->isRoleAtLeastDuring(constant(str_replace("+", "", $perm['perm']['role'])), $perm['start'], $perm['end']));
                            }
                            else{
                                $isMember = true;
                                if($this->project != null && $this->project->getId() != 0){
                                    $isMember = $me->isRoleDuring($perm['perm']['role'], $perm['start'], $perm['end'], $this->project);
                                }
                                $rResult = ($rResult || ($me->isRoleDuring($perm['perm']['role'], $perm['start'], $perm['end']) && $isMember));
                            }
                        }
                        if($perm['perm']['subType'] != ""){
                            $rResult = ($rResult && ($me->isSubRole($perm['perm']['subType'])));
                        }
                        break;
                    case "Project":
                        $nProjectTags++;
                        if($this->project != null){
                            if(isset($perm['perm']['deleted']) && $perm['perm']['deleted'] !== null){
                                $pResult = ($pResult || (($perm['perm']['deleted'] && 
                                           $this->project->isDeleted() && 
                                           substr($this->project->getEffectiveDate(), 0, 4) >= substr($perm['start'], 0, 4) && 
                                           substr($this->project->getEffectiveDate(), 0, 4) <= substr($perm['end'], 0, 4)) || 
                                          (!$perm['perm']['deleted'] && 
                                           !$this->project->isDeleted())));
                            }
                            else if(isset($perm['perm']['project'])){
                                $pResult = ($pResult || $this->project->getName() == $perm['perm']['project']);
                            }
                        }
                        break;
                    case "Person":
                        if($me->getId() == $perm['perm']['id']){
                            $rResult = true;
                        }
                        break;
                }
            }
        }
        if($nProjectTags == 0){
            $pResult = true;
        }
        return ($pResult && $rResult);
    }
    
    function getSectionPermissions($section){
        global $wgUser;
        $me = Person::newFromId($wgUser->getId());
        if($me->isRoleAtLeast(MANAGER)){
            return array('r' => true, 'w' => true);
        }
        $found = false;
        $roles = $me->getRights();
        $roleObjs = $me->getRolesDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END);
        foreach($roleObjs as $role){
            $roles[] = $role->getRole();
        }
        $permissions = array();
        foreach($roles as $role){
            if(isset($this->sectionPermissions[$role][$section->id])){
                $found = true;
                foreach($this->sectionPermissions[$role][$section->id] as $key => $perm){
                    $permissions[$key] = $perm;
                }
            }
        }
        if($this->person->getId() == 0 &&
           $this->project != null){
            if(isset($this->sectionPermissions[$this->project->getName()][$section->id])){
                $found = true;
                foreach($this->sectionPermissions[$this->project->getName()][$section->id] as $key => $perm){
                    $permissions[$key] = $perm;
                }
            }
        }
        if(isset($this->sectionPermissions[$me->getId()])){
            $found = true;
            foreach($this->sectionPermissions[$me->getId()][$section->id] as $key => $perm){
                $permissions[$key] = $perm;
            }
        }
        if(!$found){
            // If neither the section permissions were never defined, initialize them here as true
            $permissions['r'] = true;
            $permissions['w'] = true;
        }
        return $permissions;
    }
    
    // Generates the PDF for the report, and saves it to the Database
    function generatePDF($person=null, $submit=false){
        global $wgOut, $wgUser;
        session_write_close();
        $me = $person;
        if($person == null){
            $me = Person::newFromId($wgUser->getId());
        }
        $json = array();
        $preview = isset($_GET['preview']);
        if($this->pdfAllProjects && !$preview){
            foreach($this->person->getProjectsDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END) as $project){
                if(!$project->isSubProject()){
                    foreach($this->pdfFiles as $pdfFile){
                        set_time_limit(120); // Renew the execution timer
                        $wgOut->clearHTML();
                        $report = new DummyReport($pdfFile, $this->person, $project, $this->year);
                        $report->renderForPDF();
                        $data = "";
                        $pdf = PDFGenerator::generate("{$report->person->getNameForForms()}_{$report->name}", $wgOut->getHTML(), "", $me, null, false, $report);
                        $sto = new ReportStorage($this->person);
                        $sto->store_report($data, $pdf['html'], $pdf['pdf'], 0, 0, $report->pdfType, $this->year);
                        if($project != null){
                            $ind = new ReportIndex($this->person);
                            $rid = $sto->metadata('report_id');
                            $ind->insert_report($rid, $report->project);
                        }
                        if($submit){
                            $report->submitReport($person);
                        }
                    }
                }
            }
        }
        foreach($this->pdfFiles as $pdfFile){
            set_time_limit(120); // Renew the execution timer
            $wgOut->clearHTML();
            $report = new DummyReport($pdfFile, $this->person, $this->project, $this->year);
            $report->renderForPDF();
            $data = "";
            $pdf = PDFGenerator::generate("{$report->person->getNameForForms()}_{$report->name}", $wgOut->getHTML(), "", $me, $this->project, false, $report);
            if($preview){
                exit;
            }
            $sto = new ReportStorage($this->person);
            $sto->store_report($data, $pdf['html'],$pdf['pdf'], 0, 0, $report->pdfType, $this->year);
            if($report->project != null){
                $ind = new ReportIndex($this->person);
                $rid = $sto->metadata('report_id');
                $ind->insert_report($rid, $report->project);
            }
            $tok = $sto->metadata('token');
            $tst = $sto->metadata('timestamp');
            $len = $sto->metadata('pdf_len');
            $json[$pdfFile] = array('tok'=>$tok, 'time'=>$tst, 'len'=>$len, 'name'=>"{$report->name}");
        }
        if($submit){
            $this->submitReport($person);
        }
        header('Content-Type: application/json');
        header('Content-Length: '.strlen(json_encode($json)));
        echo json_encode($json);
        ob_flush();
        flush();
        exit;
    }
    
    // Marks the report as submitted
    function submitReport($person=null){
        global $wgUser;
        if($person == null){
            $me = Person::newFromId($wgUser->getId());
        }
        else{
            $me = $person;
        }
        $sto = new ReportStorage($me);
        $check = $this->getLatestPDF();
        if(count($check) > 0){
            $sto->mark_submitted_ns($check[0]['token']);
            if(($this->xmlName == "HQPReport" || $this->xmlName == "HQPReportPDF") && $this->project == null){
                $this->notifySupervisors($check[0]['token']);
            }
        }
    }
    
    // Checks whether or not this report has been submitted or not
    function isSubmitted(){
        //$sto = new ReportStorage($sto = new ReportStorage($this->person));
        $check = $this->getPDF();
        if(isset($check[0])){
            return (isset($check[0]['submitted']) && $check[0]['submitted'] == 1);
        }
    }
    
    // Renders the Report to the browser
    function render(){
        global $wgOut, $wgServer, $wgScriptPath, $wgArticle, $wgImpersonating, $wgMessage;
        FootnoteReportItem::$nFootnotes = 0;
        if($this->disabled && !$wgImpersonating){
            $wgOut->addHTML("<div id='outerReport'>This report is currently disabled until futher notice.</div>");
            return;
        }
        $writable = "true";
        if(!DBFunctions::DBWritable()){
            $writable = "false";
        }
        $wgOut->addStyle("../extensions/Reporting/Report/style/report.css");
        $wgOut->addScript("<script type='text/javascript'>
            var dbWritable = {$writable};
        </script>");
        $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/extensions/Reporting/Report/scripts/report.js'></script>");
        $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/extensions/Reporting/Report/scripts/instructions.js'></script>");
        $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/extensions/Reporting/Report/scripts/progress.js'></script>");
        if($this->ajax){
            $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/extensions/Reporting/Report/scripts/ajax.js'></script>");
        }
        else{
            $wgOut->addScript("<script type='text/javascript' src='$wgServer$wgScriptPath/extensions/Reporting/Report/scripts/noAjax.js'></script>");
        }
        $wgOut->addHTML("<div id='outerReport'>
                            <div class='displayTableCell'><div id='aboveTabs'></div>
                                <div id='reportTabs'>\n");
        $this->renderTabs();
        $wgOut->addHTML("<div id='autosaveDiv'><span style='float:left;width:100%;text-align:left'><span style='float:right;' class='autosaveSpan'></span></span></div>
                            <div id='optionsDiv'>");
        $this->renderOptions();
        if($this->extends == "" && !$this->hasSubReport()){
            $this->renderBackup();  
        }
        $wgOut->addHTML("</div></div>
                            </div>");
        
        $wgOut->addHTML("   <div id='reportMain' class='displayTableCell'><div>");
        if(!$this->topProjectOnly || ($this->topProjectOnly && !$this->currentSection->private)){
            $this->currentSection->render();
        }
        $wgOut->addHTML("   </div></div>\n");
        $instructionsHide = "";
        if(!$this->showInstructions){
            $instructionsHide = "style='display:none;'";
        }
        $instructions = trim($this->currentSection->getInstructions());
        if($instructions == ""){
            $instructionsHide = "style='display:none;'";
        }
        $wgOut->addHTML("   <div $instructionsHide id='instructionsToggle' class='highlights-text'>.<br />.<br />.</div>\n");
        $wgOut->addHTML("   <div $instructionsHide id='reportInstructions' class='displayTableCell'><div><div>
                                <span id='instructionsHeader'>Instructions</span>
                                {$instructions}
                            </div></div></div>\n");
        
        $wgOut->addHTML("</div>\n");
        $wgOut->addHTML("<script type='text/javascript'>
            autosaveDiv = $('.autosaveSpan');
        </script>");
    }
    
    function renderTabs(){
        foreach($this->sections as $section){
            $permissions = $this->getSectionPermissions($section);
            if(!isset($permissions['r'])){
                continue;
            }
            if($this->topProjectOnly && $section->private){
                continue;
            }
            $section->renderTab();
        }
    }
    
    function renderOptions(){
        global $wgOut;
        $wgOut->addHTML("<hr />
                            <h3>Options</h3>
                            <table>
                                <tr id='fullScreenRow'>
                                    <td width='50%' align='right' valign='top' style='white-space:nowrap;'>Full-Window&nbsp;Mode:</td><td width='50%' valign='middle'><input type='checkbox' name='toggleFullscreen'></td>
                                </tr>
                                <tr id='autosaveRow'>
                                    <td width='50%' align='right' valign='top'>Autosave:</td><td width='50%' valign='middle'><input name='autosave' autosave='on' type='radio' checked>On<br /><input name='autosave' value='off' type='radio'>Off</td>
                                </tr>
                            </table>");
    }
    
    function renderBackup(){
        global $wgOut, $wgServer, $wgScriptPath, $wgTitle;
        $getParams = "";
        $i = 0;
        foreach($_GET as $key => $get){
            if($key == "title"){
                continue;
            }
            $delim = "&";
            if($i == 0) $delim = "?";
            $getParams .= "{$delim}{$key}={$get}";
            $i++;
        }
        $wgOut->addHTML("<hr />
                            <h3><a id='backupLink' style='cursor:pointer;' onClick='toggleBackup();' title='A backup can be used to Save the current state of your report to your computer, and Load it later in case you wanted to revert to a previous version.  After Loading a file, the report will be saved using the data from the backup.'>Backup</a></h3>
                            <div id='backupTable' style='display:none;'><table style='width:100%;'>
                                <tr>
                                    <td><a style='overflow: hidden;' href='javascript:saveBackup();' class='button' id='saveBackup'>Save</a></td>
                                    <td><form id='backupForm' method='post' action='{$wgTitle->getFullUrl()}{$getParams}' enctype='multipart/form-data'><input type='hidden' name='loadBackup' value='true' /><a style='overflow: hidden; position: relative;' class='button' id='downloadBackup'>Load<input class='hiddenFile' name='backup' type='file' /></a><input id='resetBackup' type='reset' style='position:absolute; left:-1000px;' /></form>
                                    <div style='display:none;' id='dialog-confirm' title='Load Report Confirmation'>
    <p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>Are you sure you want to upload the file: <p nowrap='nowrap' style='font-style:italic;white-space:nowrap;' id='fileName'></p>Uploading this file will replace the current report data with the data from the backup.  The file you should be uploading should be using the file extension <b>'.report'</b>.</p>
</div></td>
                                </tr>
                            </table></div>
                            <script type='text/javascript'>
                                $('#backupLink').qtip();
                            </script>");
    }
    
    // Renders the Report for use in a PDF
    function renderForPDF(){
        global $wgOut;
        FootnoteReportItem::$nFootnotes = 0;
        $sections = $this->getPDFRenderableSections();
        
        $count = count($sections);
        $i = 0;
        if($this->header != null){
            $this->header->render();
        }
        foreach($sections as $section){
            $permissions = $this->getSectionPermissions($section);
            if(!isset($permissions['r'])){
                continue;
            }
            if(isset($_GET['preview']) || (!isset($_GET['preview']) && !$section->previewOnly)){
                if(!$this->topProjectOnly || ($this->topProjectOnly && !$section->private)){
                    if(!($section instanceof HeaderReportSection)){
                        $number = "";
                        if(count($section->number) > 0){
                            $numbers = array();
                            foreach($section->number as $n){
                                $numbers[] = AbstractReport::rome($n);
                            }
                            $number = implode(', ', $numbers).'. ';
                        }
                        $name = $section->varSubstitute($section->name);
                        $title = $section->getAttr("title", $number.$name, true);
                        if(strtolower($section->getAttr("subBookmark", "false")) == "true"){
                            PDFGenerator::addSubChapter($title);
                        }
                        else{
                            PDFGenerator::addChapter($title);
                        }
                    }
                    $section->renderForPDF();
                    if(!($section instanceof HeaderReportSection)){
                        PDFGenerator::changeSection();
                    }
                    $i++;
                    if($count >= $i && $section->pageBreak){
                        $wgOut->addHTML("<div class='pagebreak'></div>");
                    }
                }
            }
        }
    }
    
    // A function to return the Roman Numeral, given an integer
    function rome($num){
        // Make sure that we only use the integer portion of the value
        $n = intval($num);
        $result = '';
    
        // Declare a lookup array that we will use to traverse the number:
        $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
        'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
        'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    
        foreach ($lookup as $roman => $value){
            // Determine the number of matches
            $matches = intval($n / $value);
    
            // Store that many characters
            $result .= str_repeat($roman, $matches);
    
            // Substract that from the number
            $n = $n % $value;
        }
    
        // The Roman numeral should be built, return it
        return $result;
    }
    
    static function checkImpersonationPermissions($person, $realPerson, $ns, $title, $pageAllowed){
        if($person->isRoleDuring(HQP, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
            $hqps = $realPerson->getHQPDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END);
            foreach($hqps as $hqp){
                if($hqp->getId() == $person->getId()){
                    if(("$ns:$title" == "Special:Report" &&
                       @$_GET['report'] == "HQPReport") || ("$ns:$title" == "Special:ReportArchive" && checkSupervisesImpersonee())){
                        $pageAllowed = true;
                    }
                    break;
                }
            }
        }
        
        if(!$pageAllowed){
            $leadership = $realPerson->leadershipDuring(REPORTING_CYCLE_START, REPORTING_CYCLE_END);
            if(count($leadership) > 0){
                foreach($leadership as $proj){
                    if($person->isRoleDuring(NI, REPORTING_CYCLE_START, REPORTING_CYCLE_END) &&
                       $person->isMemberOfDuring($proj, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
                        if("$ns:$title" == "Special:Report" &&
                           @$_GET['report'] == "NIReport" &&
                           @$_GET['project'] == $proj->getName()){
                            $pageAllowed = true;
                        }
                    }
                }
            }
        }
        return true;
    }
    
    static function impersonationMessage($person, $realPerson, $ns, $title, $message){
        $isSupervisor = false;
        if($person->isRoleDuring(HQP, REPORTING_CYCLE_START, REPORTING_CYCLE_END)){
            if(checkSupervisesImpersonee()){
                $message = str_replace(" in read-only mode", "", $message);
            }
            $isSupervisor = false;
        }
        if($isSupervisor){
            $message .= "<br />As a supervisor, you are able to edit, generate and submit the report of your HQP.  The user who edits, generates and submits the report is recorded.";
        }
        return true;
    }
    
    static function canUserReadPDF($me, $pdf, $result){
        $start = $pdf->getYear().REPORTING_CYCLE_START_MONTH;
        $end = ($pdf->getYear()+1).REPORTING_CYCLE_END_MONTH;
        
        if($pdf->getType() == RPTP_HQP ||
           $pdf->getType() == RPTP_EXIT_HQP ||
           $pdf->getType() == RPTP_HQP_COMMENTS){
            $hqps = $me->getHQPDuring($start, $end);
            foreach($hqps as $hqp){
                if($hqp->getId() == $pdf->userId){
                    // I should be able to read any pdf which was created by my hqp (for that year)
                    $result = true;
                    return true;
                }
            }
        }
        else if($pdf->getType() == RPTP_LEADER ||
                $pdf->getType() == RPTP_LEADER_COMMENTS ||
                $pdf->getType() == RPTP_LEADER_MILESTONES){
            if($pdf->getProjectId() != ""){
                $leads = $me->leadershipDuring($start, $end);
                foreach($leads as $project){
                    if($project->getId() == $pdf->getProjectId()){
                        // I should be able to read any pdf for a Project that I was a project leader to (for that year)
                        $result = true;
                        return true;
                    }
                }
            }
        }
        if($pdf->getType() == RPTP_LEADER ||
           $pdf->getType() == RPTP_NORMAL){
            if($me->isEvaluator($pdf->getYear())){
                $evals = $me->getEvaluateSubs($pdf->getYear());
                foreach($evals as $eval){
                    if($eval instanceof Project && 
                       $pdf->getType() == RPTP_LEADER){
                        if($pdf->getProjectId() == $eval->getId()){
                            // I should be able to read any pdf for the Projects that I am evaluating (for that year)
                            $result = true;
                            return true;
                        }
                    }
                    else if($eval instanceof Person &&
                            $pdf->getType() == RPTP_NORMAL){
                        if($pdf->getPerson()->getId() == $eval->getId()){
                            // I should be able to read any pdf for the People that I am evaluating (for that year)
                            $result = true;
                            return true;
                        }
                    }
                }
            }
        }
        $result = false;
        return true;
    }
    
    static function downloadBlob($action){
        $me = Person::newFromWgUser();
        if($action == "downloadBlob" && isset($_GET['id'])){
            if(!$me->isLoggedIn()){
                permissionError();
            }
            $blob = new ReportBlob();
            $blob->loadFromMD5($_GET['id']);
            $data = $blob->getData();
            if($data != null){
                $fileName = $_GET['id'];
                $json = json_decode($data);
                if(isset($_GET['mime'])){
                    header("Content-Type: {$_GET['mime']}");
                }
                if($json != null){
                    $fileName = $json->name;
                    header("Content-disposition: attachment; filename=\"".addslashes($fileName)."\"");
                    echo base64_decode($json->file);
                    exit;
                }
                else{
                    if(isset($_GET['fileName'])){
                        $fileName = $_GET['fileName'];
                    }
                    header("Content-disposition: attachment; filename=\"".addslashes($fileName)."\"");
                    echo $data;
                    exit;
                }
            }
            exit;
        }
        return true;
    }
    
    static function blobConstant($constant){
        if(defined("{$constant}")){
            return constant("{$constant}");
        }
        return "{$constant}";
    }
    
    static function tinyMCEUpload($action){
        $me = Person::newFromWgUser();
        if($action == "tinyMCEUpload"){
            if(!$me->isLoggedIn() || 
                $_FILES['image']['size'] >= 512*1024){
                echo "alert('There was a problem with the uploaded file.  Make sure that it is a valid image and under 512KB.');";
                exit;
            }
            $src = $_FILES['image']['tmp_name'];
            $hash = md5($src);
            system("convert +antialias -background transparent $src /tmp/$hash.png");
            list($width, $height) = getimagesize("/tmp/$hash.png");
            $imgConst = DPI_CONSTANT*72/96;
            $width = $width/$imgConst;
            $height = $height/$imgConst;
            $png = file_get_contents("/tmp/$hash.png");
            unlink("/tmp/$hash.png");
            $str = "top.$('input[aria-label=Width]').val($width);
                    top.$('input[aria-label=Height]').val($height);
                    top.$('.mce-btn.mce-open').parent().find('.mce-textbox').val('data:image/png;base64,".base64_encode($png)."').closest('.mce-window').find('.mce-primary').click();";
            echo $str;
            exit;
        }
        return true;
    }
    
    /**
     * Returns the value of the variable with the given key
     * @param string $key The key of the variable
     * @return string The value of the variable if found
     */
    function getVariable($key){
        if(isset($this->variables[$key])){
            return $this->variables[$key];
        }
        return "";
    }
    
    /**
     * Sets the value of the variable with the given key to the given value
     * @param string $key The key of the variable
     * @param string $value The value of the variable
     * @param integer $depth The depth of the function call (should not need to ever pass this)
     * @return boolean Whether or not the variable was found
     */
    function setVariable($key, $value, $depth=0){
        if(isset($this->variables[$key])){
            $this->variables[$key] = $value;
            return true;
        }
        return false;
    }
}

?>
