<?php

require_once("GradChairTable/GradChairTable.php");

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['GradDB'] = 'GradDB'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['GradDB'] = $dir . 'GradDB.i18n.php';
$wgSpecialPageGroups['GradDB'] = 'network-tools';

class GradDB extends SpecialPage{

    static function mail($to, $subject, $message, $pdf, $fileName){
        global $config;

        $attachment = chunk_split(base64_encode($pdf));
        $eol = PHP_EOL;
        $separator = md5(time());

        $headers = "From: {$config->getValue('networkName')} <{$config->getValue('supportEmail')}>$eol";
        $headers .= 'MIME-Version: 1.0' .$eol;
        $headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

        $body = "--".$separator.$eol;
        $body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
        $body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
        $body .= "{$message}".$eol;

        $body .= "--".$separator.$eol;
        $body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
        $body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;

        $body .= "--".$separator.$eol;
        $body .= "Content-Type: application/pdf; name=\"".$fileName."\"".$eol; 
        $body .= "Content-Transfer-Encoding: base64".$eol;
        $body .= "Content-Disposition: attachment".$eol.$eol;
        $body .= $attachment.$eol;
        $body .= "--".$separator."--";
        
        mail($to, $subject, $body, $headers);
    }

    function GradDB() {
        parent::__construct("GradDB", HQP.'+', true);
    }

    function execute($par){
        global $wgOut;
        $me = Person::newFromWgUser();
        
        if(isset($_GET['pdf'])){
            $this->downloadPDF();
        }
        else if(isset($_GET['accept'])){
            $this->accept();
        }
        else if($me->isRole(NI)){
            if(isset($_GET['hqp']) && isset($_GET['term'])){
                $this->supervisorForm($_GET['hqp'], $_GET['term']);
            }
            else{
                $this->supervisorTable();
           }
        }
        else if($me->isRole(HQP)){
            $this->hqpTable();
        }
    }
    
    // View of the table of HQP for the supervisor
    function supervisorTable(){
        global $wgOut, $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        $terms = array();

        $year = date('Y');
        $month = date('n');
        if($month == 1){
            $term = "Winter{$year}";
        }
        else if($month >= 2 && $month < 5){
            $term = "Spring/Summer{$year}";
        }
        else if($month >= 5 && $month < 9){
            $term = "Fall{$year}";
        }
        else if($month >= 9){
            $term = "Winter".($year+1);
        }
        $terms[] = GradDBFinancial::prevTerm($term);
        $terms = array_merge($terms, GradDBFinancial::yearTerms($term));
        
        
        if(isset($_GET['term']) && in_array($_GET['term'], $terms)){
            $term = $_GET['term'];
        }
        
        $date = GradDBFinancial::term2Date($term);
        
        $termSelect = new SelectBox("term", "Term", $term, $terms);
        $wgOut->addHTML("<div><span class='label'>Term:</span> {$termSelect->render()}</div><br />
            <p>If the HQP not in the table you can <a class='button' href='{$wgServer}{$wgScriptPath}/index.php/Special:GradDB?hqp=0&term={$term}'>Make a Contract</a> for any eligible HQP.</p>
            <table id='hqpTable' class='wikitable' frame='box' rules='all'>
                <thead>
                    <tr>
                        <th>HQP</th>
                        <th>Program</th>
                        <th style='width:1%;'>TA Eligible</th>
                        <th style='width:1%;'>HQP Accepted</th>
                        <th style='width:1%;'>Supervisor Accepted</th>
                        <th>Financial Form</th>
                    </tr>
                </thead>
                <tbody>");
        foreach(array_merge($me->getHQP(true), GradDBFinancial::getAttachedHQP($me->getId(), $term)) as $hqp){
            $universities = $hqp->getUniversitiesDuring($date, $date);
            foreach($universities as $university){
                if(in_array(strtolower($university['position']), Person::$studentPositions['grad'])){
                    $gradDBFinancial = GradDBFinancial::newFromTuple($hqp->getId(), $me->getId(), $term);
                    $button = (!$gradDBFinancial->exists()) ? "<a class='button' href='{$wgServer}{$wgScriptPath}/index.php/Special:GradDB?hqp={$hqp->getId()}&term={$term}'>Make a Contract</a>" : "<a class='button' target='_blank' href='{$wgServer}{$wgScriptPath}/index.php/Special:GradDB?pdf={$gradDBFinancial->getMD5()}'>View Contract</a>";
                    $eligible = ($hqp->isTAEligible($date)) ? "<span style='font-size:2em;'>&#10003;</span>" : "";
                    $hqpAccepted = ($gradDBFinancial->hasHQPAccepted()) ? "<span style='font-size:2em;'>&#10003;</span>" : "";
                    $hasSupAccepted = ($gradDBFinancial->hasSupAccepted()) ? "<span style='font-size:2em;'>&#10003;</span>" : "";
                    $wgOut->addHTML("<tr>
                        <td><a href='{$hqp->getUrl()}'>{$hqp->getReversedName()}</a></td>
                        <td>{$university['position']}</td>
                        <td align='center'>{$eligible}</td>
                        <td align='center'>{$hqpAccepted}</td>
                        <td align='center'>{$hasSupAccepted}</td>
                        <td align='center'>{$button}</td>
                    </tr>");
                    break;
                }
            }
        }
        $wgOut->addHTML("</tbody></table>
        <script type='text/javascript'>
            $('#hqpTable').DataTable({
                aLengthMenu: [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, 'All']
                ],
                iDisplayLength: -1
            });
            $('select[name=term]').change(function(){
                document.location = wgServer + wgScriptPath + '/index.php/Special:GradDB?term=' + $('select[name=term]').val();
            });
        </script>");
    }
    
    function hqpTable(){
        global $wgOut, $wgServer, $wgScriptPath, $wgMessage, $config;
    }
    
    // View of the supervisor form
    function supervisorForm($hqpId, $term){
        global $wgOut, $wgServer, $wgScriptPath, $wgMessage, $config;
        $me = Person::newFromWgUser();
        if(isset($_POST['hqp'])){
            $hqp = Person::newFromId($_POST['hqp']);
        }
        else{
            $hqp = Person::newFromId($hqpId);
        }
        $terms = (isset($_POST['terms'])) ? $_POST['terms'] : array($term);
        foreach($terms as $t){
            $gradDBFinancial = GradDBFinancial::newFromTuple($hqp->getId(), $me->getId(), $t);
            if($gradDBFinancial->exists()){
                $wgOut->addHTML("This entry already exists and cannot be edited");
                return;
            }
        }
        if(isset($_POST['submit'])){
            // Handle Form Submit
            $error = "";
            $gradDBFinancial->hqpId = $hqp->getId();
            $gradDBFinancial->supId = $me->getId();
            $gradDBFinancial->term = implode(",", $_POST['terms']);
            
            $gradDBFinancial->lines = array();
            $scale = GradDBFinancial::getScale($hqp, 2020);
            foreach($_POST['type'] as $key => $sup){
                $award = $scale['award']*$_POST['hours'][$key]/GradDBFinancial::$HOURS;
                $salary = $scale['salary']*$_POST['hours'][$key]/GradDBFinancial::$HOURS;
                $stipend = $award + $salary;
                $gradDBFinancial->lines[] = $gradDBFinancial->emptyLine($_POST['type'][$key], 
                                                                        str_replace("_", "", $_POST['account'][$key]),
                                                                        $_POST['hours'][$key],
                                                                        $award,
                                                                        $salary,
                                                                        $stipend);
            }

            if(!$gradDBFinancial->exists()){
                $gradDBFinancial->create();
            }
            
            if($error == ""){
                $gradDBFinancial->generatePDF();
                $wgMessage->addSuccess("Financial Information updated");

                // Supervisor Email
                self::mail("dwt@ualberta.ca", "Contract for {$gradDBFinancial->getTerm()}", $gradDBFinancial->getEmail(), $gradDBFinancial->getPDF(), "Contract.pdf");
                // Student Email
                self::mail("dwt@ualberta.ca", "Contract for {$gradDBFinancial->getTerm()}", $gradDBFinancial->getEmail(), $gradDBFinancial->getPDF(), "Contract.pdf");

                redirect("{$wgServer}{$wgScriptPath}/index.php/Special:GradDB?term={$term}");
            }
        }
        
        // Form
        $date = GradDBFinancial::term2Date($term);
        $hqpNames = array();
        if($gradDBFinancial->hqpId != 0){
            $hqp = $gradDBFinancial->getHQP();
            $email = str_replace("@ualberta.ca", "", $hqp->getEmail());
            if($email != ""){
                $email = "($email)";
            }
            $hqpNames[$hqp->getId()] = "{$hqp->getNameForForms()} {$email}";
        }
        $students = new SelectBox("hqp", "Student", $gradDBFinancial->hqpId, $hqpNames);
        $students->forceKey = true;
        $students->attr("data-placeholder", "Choose a student...");
        $terms = new VerticalCheckBox("terms", "Terms", $gradDBFinancial->getTerms(), GradDBFinancial::yearTerms($term));
        $wgOut->addHTML("<form method='POST'>
                            <table>
                                <tr>
                                    <td><b>Student:</b></td>
                                    <td>{$students->render()}</td>
                                </tr>
                                <tr>
                                    <td><b>Term(s):</b></td>
                                    <td>{$terms->render()}</td>
                                </tr>
                            </table><div id='supervisors'>");
        $names = array("");
        foreach(Person::getAllPeople(NI) as $faculty){
            $names[$faculty->getId()] = $faculty->getNameForForms();
        }
        $wgOut->addHTML("<table class='wikitable'>
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Hours/Week</th>
                                    <th>Award</th>
                                    <th>Salary</th>
                                    <th>Total Stipend</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>");
        $speedCodes = array("");
        foreach($me->getSpeedCodes() as $code){
            $speedCodes["_{$code['speedcode']}"] = "{$code['speedcode']} - {$code['title']}";
        }
        foreach(array_merge(array($gradDBFinancial->emptyLine()), $gradDBFinancial->getLines()) as $line){
            $account = new SelectBox("account[]", "Account", $line['account'], $speedCodes);
            $type = new SelectBox("type[]", "Type", $line['type'], array("GTA" => "GTA", 
                                                                         "GRA" => "GRA", 
                                                                         "GRAF" => "GRAF",
                                                                         "Fee Differential" => "Fee Differential",
                                                                         "Top Up" => "Top Up"));
            $hours = new SelectBox("hours[]", "Hours/Week", $line['hours'], array("12" => "12",
                                                                                  "11" => "11",
                                                                                  "10" => "10",
                                                                                  "9" => "9",
                                                                                  "8" => "8",
                                                                                  "7" => "7",
                                                                                  "6" => "6",
                                                                                  "5" => "5",
                                                                                  "4" => "4",
                                                                                  "3" => "3",
                                                                                  "2" => "2",
                                                                                  "1" => "1"));
            
            $wgOut->addHTML("
                <tr>
                    <td>{$account->render()}</td>
                    <td>{$type->render()}</td>
                    <td>{$hours->render()}</td>
                    <td align='right'><span class='award'></span></td>
                    <td align='right'><span class='salary'></span></td>
                    <td align='right'><span class='total'></span></td>
                    <td><button class='removeSupervisor' type='button'>Remove Line Item</button></td>
                </tr>");
            }
            $wgOut->addHTML("</tbody></table></div><button class='addSupervisor' type='button'>Add Line Item</button><br /><br /><input type='submit' name='submit' value='Submit' />
            </form>
            <script type='text/javascript' src='{$wgServer}{$wgScriptPath}/extensions/GradDB/graddb.js'></script>");
    }
    
    function downloadPDF(){
        global $wgOut;
        $gradDBFinancial = GradDBFinancial::newFromMD5($_GET['pdf']);
        $gradDBTimeUse = GradDBTimeUse::newFromMD5($_GET['pdf']);
        if($gradDBFinancial->exists()){
            if($gradDBFinancial->isAllowedToView()){
                header("Content-Type: application/pdf");
                header("Content-Disposition:filename=\"{$gradDBFinancial->getHQP()->getName()}_{$gradDBFinancial->getTerm()}_Appointment.pdf\"");
                echo $gradDBFinancial->getPDF();
                exit;
            }
            else{
                permissionError();
            }
        }
        else if($gradDBTimeUse->exists()){
            if($gradDBTimeUse->isAllowedToView()){
                header("Content-Type: application/pdf");
                header("Content-Disposition:filename=\"{$gradDBTimeUse->getHQP()->getName()}_{$gradDBTimeUse->getTerm()}_TimeUse.pdf\"");
                echo $gradDBTimeUse->getPDF();
                exit;
            }
            else{
                permissionError();
            }
        }
        else{
            $wgOut->addHTML("This PDF doesn't exist.");
        }
    }
    
    function accept(){
        global $wgMessage, $wgServer, $wgScriptPath, $config;
        $me = Person::newFromWgUser();
        $gradDBFinancial = GradDBFinancial::newFromMD5($_GET['accept']);
        if($gradDBFinancial->exists() && $gradDBFinancial->isAllowedToView() && ($gradDBFinancial->getHQP()->getId() == $me->getId() || 
                                                                                 $gradDBFinancial->isSupervisor($me->getId()))){
            if($gradDBFinancial->getHQP()->getId() == $me->getId() && !$gradDBFinancial->hasHQPAccepted()){
                $gradDBFinancial->hqpAccepted = currentTimeStamp();
            }
            else if($gradDBFinancial->isSupervisor($me->getId()) && !$gradDBFinancial->hasSupAccepted()){
                $gradDBFinancial->supAccepted = currentTimeStamp();
            }
            else{
                $wgMessage->addError("You have already accepted this contract.");
                return;
            }
            
            $gradDBFinancial->update();
            $gradDBFinancial->generatePDF();
            $message = "<p>{$me->getFullName()} has accepted the contract appointment for {$gradDBFinancial->getTerm()}.
                        <p> - {$config->getValue('networkName')}</p>";
            self::mail("dwt@ualberta.ca", "Contract for {$gradDBFinancial->getTerm()} Accepted", $message, $gradDBFinancial->getPDF(), "Contract.pdf");
            $wgMessage->addSuccess("Thank you for accepting this contract.");
        }
        else{
            $wgMessage->addError("This contract doesn't exist.");
        }
    }

}

?>
