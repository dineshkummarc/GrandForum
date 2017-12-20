<?php

class ApplicationTab extends AbstractTab {

    var $rp;
    var $people;
    var $year;
    var $extraCols;
    var $showAllWithPDFs;

    function ApplicationTab($rp, $people, $year=REPORTING_YEAR, $title=null, $extraCols=array()){
        $me = Person::newFromWgUser();
        $this->rp = $rp;
        $this->year = $year;
        $this->extraCols = $extraCols;
        $this->showAllWithPDFs = false;
        $newPeople = array();
        foreach($people as $person){
            $newPeople[$person->getId()] = $person;
        }
        $this->people = $newPeople;
        if(is_array($this->rp)){
            $report = new DummyReport($this->rp[0], $me, null, $year);
        }
        else{
            $report = new DummyReport($this->rp, $me, null, $year);
        }
        if($title == null){
            parent::AbstractTab($report->name);
        }
        else{
            parent::AbstractTab($title);
        }
        $this->html = "";
    }

    function generateBody(){
        global $wgServer, $wgScriptPath;
        $me = Person::newFromWgUser();
        $rpId = (is_array($this->rp)) ? $this->rp[0] : $this->rp;
        $rpId .= $this->year;
        
        $isPerson = true;
        foreach($this->people as $person){
            if(!($person instanceof Person)){
                $isPerson = false;
            }
            break;
        }
        
        if(is_array($this->rp)){
            $report = array();
            foreach($this->rp as $rp){
                $report[] = new DummyReport($rp, $me, null, $this->year);
            }
        }
        else{
            $report = new DummyReport($this->rp, $me, null, $this->year);
            if($report->allowIdProjects){
                $report = array();
                $proj0 = new Project(array());
                $proj1 = new Project(array());
                $proj0->id = 0;
                $proj1->id = 1;
                $report[] = new DummyReport($this->rp, $me, $proj0, $this->year);
                $report[] = new DummyReport($this->rp, $me, $proj1, $this->year);
            }
        }

        // Set year of Report
        if(is_array($report)){
            foreach($report as $rep){
                $rep->year = $this->year;
            }
        }
        else{
            $report->year = $this->year;
        }
        
        $this->html .= "<table id='application_{$rpId}' frame='box' rules='all'>";
        $this->html .= "<thead>";
        if(is_array($report)){
            $colspan = ($isPerson) ? "2" : "2";
            $this->html .= "<tr><th width='50%' colspan='$colspan'></th>";
            foreach($report as $rep){
                $colspan = 2 + count($this->extraCols);
                $this->html .= "<th colspan='$colspan'>{$rep->name}</th>";
            }
            $this->html .= "</tr>";
        }
        $this->html .= "<tr>
                            <th>Name</th>";
        if(!$isPerson){
            $this->html .= "<th>Leader</th>";
        }
        else{
            $this->html .= "<th>Email</th>";
        }
        if(is_array($report)){
            foreach($report as $rep){
                $this->html .= "<th>Generation Date</th>
                                <th width='1%'>PDF&nbsp;Download</th>";
                if(count($this->extraCols) > 0){
                    $this->html .= "<th>Extra</th>";
                }
            }
        }
        else{
            $this->html .= "<th>Generation Date</th>
                            <th width='1%'>PDF&nbsp;Download</th>";
            if(count($this->extraCols) > 0){
                $this->html .= "<th>Extra</th>";
            }
        }
        $this->html .= "</tr>
                        </thead>
                        <tbody>";
        
        foreach($this->people as $person){
            if(is_array($report)){
                foreach($report as $rep){
                    if($person instanceof Project ||
                       $person instanceof Theme){
                        $rep->project = $person;
                        $rep->person = Person::newFromId(0);
                    }
                    else{
                        $rep->person = $person;
                    }
                }
                $first = $report[0];
            }
            else{
                if($person instanceof Project ||
                   $person instanceof Theme){
                    $report->project = $person;
                    $report->person = Person::newFromId(0);
                }
                else{
                    $report->person = $person;
                }
                $first = $report;
            }
            if($first->hasStarted() || ($this->showAllWithPDFs && count($first->getPDF()) > 0)){
                $pName = $person->getName();
                if($person instanceof Theme){
                    $pName = "{$person->getAcronym()}: {$person->getName()}";
                }
                $this->html .= "<tr>
                    <td>{$pName}</td>";
                if($isPerson){
                    $this->html .= "<td>{$person->getEmail()}</td>";
                }
                if($person instanceof Project || $person instanceof Theme){
                    $leader = array_values($person->getLeaders());
                    $leader = (isset($leader[0])) ? $leader[0] : null;
                    if($leader != null){
                        $this->html .= "<td>{$leader->getNameForForms()}</td>";
                    }
                    else{
                        $this->html .= "<td></td>";
                    }
                }
                if(is_array($report)){
                    foreach($report as $rep){
                        $pdf = $rep->getPDF();
                        $pdfButton = (count($pdf) > 0) ? "<a class='button' href='$wgServer$wgScriptPath/index.php/Special:ReportArchive?getpdf={$pdf[0]['token']}'>Download</a>" : "";
                        $pdfDate = (count($pdf) > 0) ? "{$pdf[0]['timestamp']}" : "";
                        $this->html .= "<td align='center'>{$pdfDate}</td>
                                        <td>{$pdfButton}</td>";
                        foreach($this->extraCols as $extra){
                            $section = new EditableReportSection();
                            $section->setParent($rep);
                            $extra->setParent($section);
                            $extra->setPersonId($rep->person->getId());
                            if($rep->project != null){
                                $extra->setProjectId($rep->project->getId());
                            }
                            else{
                                $extra->setProjectId(0);
                            }
                            $this->html .= "<td>{$extra->getText()}</td>";
                        }
                    }
                }
                else{
                    $pdf = $first->getPDF();
                    $pdfButton = (count($pdf) > 0) ? "<a class='button' href='$wgServer$wgScriptPath/index.php/Special:ReportArchive?getpdf={$pdf[0]['token']}'>Download</a>" : "";
                    $pdfDate = (count($pdf) > 0) ? "{$pdf[0]['timestamp']}" : "";
                    $this->html .= "<td align='center'>{$pdfDate}</td>
                                    <td>{$pdfButton}</td>";
                }  
                $this->html .= "</tr>";
            }
        }
        $this->html .= "</tbody>
                        </table>";
        $this->html .= "<script type='text/javascript'>
            $('#application_{$rpId}').dataTable({
                autoWidth: false,
                'columnDefs': [
                    {'type': 'string', 'targets': 0 }
                ]
            });
        </script>";
    }
}
?>