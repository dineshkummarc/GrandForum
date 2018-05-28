<?php

$dir = dirname(__FILE__) . '/';
$wgSpecialPages['GraduateStudents'] = 'GraduateStudents'; # Let MediaWiki know about the special page.
$wgExtensionMessagesFiles['GraduateStudents'] = $dir . 'GraduateStudents.i18n.php';
$wgSpecialPageGroups['GraduateStudents'] = 'reporting-tools';

$wgHooks['SubLevelTabs'][] = 'GraduateStudents::createSubTabs';

class GraduateStudents extends SpecialPage {
    
    function GraduateStudents(){
        parent::__construct("GraduateStudents", ISAC, true);
    }
    
    function execute($par){
        global $wgOut;
        $me = Person::newFromWgUser();
        $dept = $me->getDepartment();
        $start = REPORTING_CYCLE_START;
        $end   = REPORTING_CYCLE_END;
        $hqps = Person::getAllPeopleDuring(HQP, $start, $end);
        $table = (isset($_GET['table'])) ? $_GET['table'] : "grad";
        
        $wgOut->addHTML("<table id='graduateStudents' frame='box' rules='all'>");
        $wgOut->addHTML("<thead>
                            <tr>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Employee Id</th>
                                <th>Position</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Supervisor(s)</th>
                                <th>Co-Supervisor(s)</th>
                            </tr>
                        </thead>
                        <tbody>");
        foreach($hqps as $hqp){
            $universities = $hqp->getUniversitiesDuring($start, $end);
            $university = null;
            foreach($universities as $uni){
                if($uni['department'] == $dept && in_array(strtolower($uni['position']), @Person::$studentPositions[$table])){
                    $university = $uni;
                    break;
                }
            }
            if($university != null){
                $supervisors = $hqp->getSupervisorsDuring($start, $end);
                $sups = array();
                $cosups = array();
                foreach($supervisors as $supervisor){
                    if($supervisor->isRelatedToDuring($hqp, SUPERVISES, $start, $end)){
                        $sups[] = $supervisor->getNameForForms();
                    }
                    else if($supervisor->isRelatedToDuring($hqp, CO_SUPERVISES, $start, $end)){
                        $cosups[] = $supervisor->getNameForForms();
                    }
                }
                $wgOut->addHTML("<tr>");
                $wgOut->addHTML("<td>{$hqp->getFirstName()}</td>");
                $wgOut->addHTML("<td>{$hqp->getMiddleName()}</td>");
                $wgOut->addHTML("<td>{$hqp->getLastName()}</td>");
                $wgOut->addHTML("<td>{$hqp->getEmail()}</td>");
                $wgOut->addHTML("<td>{$hqp->getEmployeeId()}</td>");
                $wgOut->addHTML("<td>{$uni['position']}</td>");
                $wgOut->addHTML("<td align='center'>".substr($uni['start'], 0, 10)."</td>");
                $wgOut->addHTML("<td align='center'>".substr($uni['end'], 0, 10)."</td>");
                $wgOut->addHTML("<td>".implode("; ", $sups)."</td>");
                $wgOut->addHTML("<td>".implode("; ", $cosups)."</td>");
                $wgOut->addHTML("</tr>");
            }
        }
        $wgOut->addHTML("</tbody></table>");
        $wgOut->addHTML("<script type='text/javascript'>
            $('#graduateStudents').DataTable({
                'iDisplayLength': 100, 
                'autoWidth':false,
                'dom': 'Blfrtip',
                'buttons': [
                    'excel'
                ]
            });
        </script>");
    }
    
    static function createSubTabs(&$tabs){
        global $wgServer, $wgScriptPath, $wgUser, $wgTitle;
        $person = Person::newFromWgUser();
        if($person->isRole(ISAC)){
            $selected = @($wgTitle->getText() == "GraduateStudents" && $_GET['table'] == "grad") ? "selected" : false;
            $tabs["Chair"]['subtabs'][] = TabUtils::createSubTab("Graduate Students", "$wgServer$wgScriptPath/index.php/Special:GraduateStudents?table=grad", $selected);
            
            $selected = @($wgTitle->getText() == "GraduateStudents" && $_GET['table'] == "ugrad") ? "selected" : false;
            $tabs["Chair"]['subtabs'][] = TabUtils::createSubTab("Undergraduate Students", "$wgServer$wgScriptPath/index.php/Special:GraduateStudents?table=ugrad", $selected);
        }
        return true;
    }
}

?>
