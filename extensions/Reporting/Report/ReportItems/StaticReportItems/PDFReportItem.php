<?php

class PDFReportItem extends StaticReportItem {

    function render(){
        global $wgServer, $wgScriptPath, $wgOut;
        $me = $this->getReport()->person;
        $reportType = $this->getAttr("reportType", 'HQPReport');
        $useProject = $this->getAttr("project", false);
        $class = $this->getAttr("class", "button");
        $showIfNull = $this->getAttr("showIfNull", "false");
        $buttonName = $this->getAttr("buttonName", "Report PDF");
        $noRenderIfNull = $this->getAttr("noRenderIfNull", "false");
        $year = $this->getAttr("year", $this->getReport()->year);
        $width = $this->getAttr("width", 'auto');
        $embed = (strtolower($this->getAttr("embed", "false")) == "true");
        if(strstr($width, "%") !== false){
            $width = $width.";-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box";
        }
        $project = null;
        if($useProject){
            $project = Project::newFromId($this->projectId);
        }
        $person = Person::newFromId($this->personId);
        $report = new DummyReport($reportType, $person, $project, $year);
        $tok = false;
        $check = $report->getPDF();
        if (count($check) > 0 && ($reportType != "ProjectNIComments" || $person->getId() != $me->getId())) {
            $tok = $check[0]['token'];
            if($embed){
                $item = "<iframe src='$wgServer$wgScriptPath/index.php/Special:ReportArchive?getpdf={$tok}' width='100%' height='700px' frameborder='0'></iframe>";
            }
            else {
                $item = "<a class='$class' style='width:{$width};' target='_blank' href='$wgServer$wgScriptPath/index.php/Special:ReportArchive?getpdf={$tok}'>{$buttonName}</a>";
            }
            $item = $this->processCData($item);
            $wgOut->addHTML($item);
        }
        else{
            if($noRenderIfNull == "true"){
                return;
            }
            else if($showIfNull == "true"){
                $wgOut->addHTML($this->processCData("{$buttonName}"));
            }
            else{
                $wgOut->addHTML($this->processCData(""));
            }
        }
    }

    function renderForPDF(){
        global $wgOut;
        $wgOut->addHTML($this->processCData(""));
    }
}

?>
