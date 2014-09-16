<?php
$dir = dirname(__FILE__);
if(isset($_GET['generatePDF'])){
    require_once($dir . '/../../../Classes/SmartDomDocument/SmartDomDocument.php');
}
require_once('PDFParams.php');
require_once('ReportIndex.php');
require_once('ReportStorage.php');
$GLOBALS['chapters'] = array();
$GLOBALS['footnotes'] = array();
$GLOBALS['nFootnotes'] = 0;
$GLOBALS['nFootnotesProcessed'] = 0;
$GLOBALS['section'] = 0;
if(isset($_GET['dpi'])){
    define('DPI', $_GET['dpi']);
}
else if(isset($_GET['preview'])){
    define('DPI', 150);
}
else{
    define('DPI', 300);
}
define('DPI_CONSTANT', DPI/72);

PDFGenerator::$preview = isset($_GET['preview']);

/**
 * This class helps with the generation of a PDF document.
 * @package PDFGenerator
 */
abstract class PDFGenerator {

    static $preview = false;
    
    /**
     * Generates a PDF based on html input
     * @param string $name The name of the PDF File
     * @param string $html The html input string
     * @param Person $person The Person that this Report is being generated by
     * @param Project $project The Project that this Report belongs for (generally only for Project Reports)
     * @param boolean $preview Whether or not this should be a preview
     * @returns array Returns an array containing the final html, as well as the pdf string
     */
    function generate($name, $html, $head, $person=null, $project=null, $preview=false){
        global $wgServer, $wgScriptPath, $wgUser;
        
        if(self::$preview){
            $preview = true;
        }
        $dom = new SmartDomDocument();
        $dom->loadHTML($html);
        $as = $dom->getElementsByTagName("a");
        for($i=0; $i<$as->length; $i++){
            $a = $as->item($i);
            if($a->getAttribute('class') != 'anchor'){
                $i--;
                DOMRemove($a);
            }
        }
        
        $divs = $dom->getElementsByTagName('div');
        $nInfo = 0;
        foreach($divs as $div){
            if($div->getAttribute('class') == 'report_info'){
                $value = explode("\n", $div->nodeValue);
                $nInfo = count($value);
                break;
            }
        }
        $nInfo = max(5, $nInfo);
        $html = "$dom";
        if($person == null){
            $person = Person::newFromId($wgUser->getId());
        }
        ini_set("max_execution_time","500");
        ini_set("memory_limit","1024M");
        
        $previewScript = "";
        if($preview){
            $previewScript = "
            <script type='text/javascript' src='$wgServer$wgScriptPath/scripts/jquery.min.js'></script>
            <script type='text/javascript' src='$wgServer$wgScriptPath/scripts/jquery-ui.min.js'></script>
            <script type='text/javascript' src='$wgServer$wgScriptPath/scripts/jquery.qtip.min.js'></script>
            <link type='text/css' href='$wgServer$wgScriptPath/skins/cavendish/jquery.qtip.min.css' rel='Stylesheet' />
            <script type='text/javascript'>
                function hideProgress(){
                    if(parent.location == window.location){
                        return;
                    }
                    parent.hideProgress();
                    load_page();
                }
                
                function load_page() {
                    $(\"body\").width($(\"body\").width() - 50);
                    parent.alertsize($(\"body\").height() + 50 + 38);
                    $(\"body\").width('auto');
                }
                
                
            </script>
            <script type='text/javascript'>
		        $(document).ready(function(){
		            $('.tooltip').qtip();
		            hideProgress();
		        });
		    </script>";
        }
        else{
            require_once(dirname(__FILE__) . '/../../../Classes/dompdf/dompdf_config.inc.php');
            $dompdf = new DOMPDF();
        }
        
        $header = <<<EOF
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>$name</title>
        <style type='text/css'>
EOF;
        $fontSize = (10*DPI_CONSTANT);
        if($preview){
            $header .= "
            #pdfBody .pagebreak {
		        border-width: 0 0 ".max(1, (0.5*DPI_CONSTANT))."px 0;
		        border-style: dashed;
		        border-color: #000000;
		        margin-bottom: ".(5*DPI_CONSTANT)."px;
		        margin-top:".(5*DPI_CONSTANT)."px;
		    }
		    
		    #pdfBody .logo {
		        background-image: url('../skins/Grand_Logo.png');
		        background-size: 100% Auto;
		        background-repeat: no-repeat;
		    }
		    
		    #pdfBody .report_name {
		        margin-top:".(17*DPI_CONSTANT)."px;
		        margin-right: 100px;
		    }
		    
		    #pdfBody .report_info {
		        margin-top:".(12*DPI_CONSTANT)."px;
		        margin-right: 100px;
		    }
		    
		    #pdfBody img.logo {
		        display: none;
		    }
		    
		    #pdfBody #page_header {
		        width:100%;
		        text-align:right;
		        margin-top:-".(15*DPI_CONSTANT)."px;
		        font-size:smaller;
		    }
		    
		    #pdfBody {
		        margin: ".(20*DPI_CONSTANT)."px ".(20*DPI_CONSTANT)."px !important;
		        position: relative;
		        white-space: normal !important;
		    }
		    
		    .ui-dialog-content {
		        white-space: normal !important;
		    }";
        }
        else{
            $header .= "
            #pdfBody .pagebreak {
		        page-break-after:always;
		    }
		    
		    #pdfBody .logo {
		        background-image: url('skins/Grand_Logo.png');
		        background-repeat: no-repeat;
		    }";
        }
        
		$header .= "
		    @page {
                margin-top: 0.75cm;
                margin-left: 0.75cm;
                margin-right: 0.75cm;
                margin-bottom: 0.50cm;
            }
		
		    #pdfBody  {
		        margin: 0.5cm 0cm;
		        margin-bottom:1cm;
		        font-family: helvetica !important;
		        font-size: {$fontSize}px;
		    }
		    
		    /* Messages */
		    
            #pdfBody .inlineError, #pdfBody .inlineWarning, #pdfBody .inlineSuccess, #pdfBody .inlineInfo {
                margin: 0 0;
                padding: 0 ".(2*DPI_CONSTANT)."px;
            }

            #pdfBody .inlineError {
                color: #D50013;
                background: #FEB8B8;
                border-radius: 3px;
                -moz-border-radius: 3px;
	            -webkit-border-radius: 3px;
            }

            #pdfBody .inlineWarning {
                color: #9C600D;
                background: #FDEEB2;
                border-radius: 3px;
                -moz-border-radius: 3px;
	            -webkit-border-radius: 3px;
            }

            #pdfBody .inlineSuccess {
                color: #51881D;
                background: #DEF1BE;
                border-radius: 3px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
            }

            #pdfBody .inlineInfo {
                color: #0A5398;
                background: #BCE4F7;
                border-radius: 3px;
                -moz-border-radius: 3px;
                -webkit-border-radius: 3px;
            }
            
            #pdfBody .budgetError {
                color: #D50013;
                background: #FEB8B8;
            }
		    
		    #pdfBody td, #pdfBody th {
		        font-family: helvetica !important;
		        background-color: #FFFFFF;
		    }
		    
		    #pdfBody .report_name {
		        position:absolute;
		        right: 0;
		        top: 0;
		        margin-right:0 !important;
		    }
		    
		    #pdfBody .report_info {
		        width: 100%;
		        height: ".(($fontSize+4)*($nInfo) + (20*DPI_CONSTANT))."px;
		        font-size: ".($fontSize+(-3*DPI_CONSTANT))."px;
		        top:0;
		        margin-right:0 !important;
		    }
		    
		    #pdfBody .report_info > tbody > tr > td {
		        vertical-align: top;
		    }
		    
		    #pdfBody .progress_table {
		        white-space: nowrap;
		        font-size: ".($fontSize+(-3*DPI_CONSTANT))."px;
		        border-spacing:".max(1, (0.5*DPI_CONSTANT))."px;
		        border-width:".max(1, (0.5*DPI_CONSTANT))."px;
		        border-color: #000000;
		        margin-top:".(5*DPI_CONSTANT)."px;
		    }
		    
		    #pdfBody .report_info > table {
		        height: ".(($fontSize+4)*($nInfo) + (20*DPI_CONSTANT))."px;
		    }
		    
		    #pdfBody hr {
		        border-width: ".max(1, (0.5*DPI_CONSTANT))."1px 0 0 0;
		        border-style: solid;
		        border-color: #000000;
		    }
		    
		    #pdfBody h1 {
		        background-color: #333333;
		        color: #FFFFFF;
		        margin-top:0;
		        margin-bottom: 0.25em;
		        font-size: ".($fontSize+(4*DPI_CONSTANT))."px;
		        font-weight:normal;
		        border: ".max(1, (0.5*DPI_CONSTANT))."px solid #000000;
		        padding: ".max(1, (0.5*DPI_CONSTANT))."px ".(3*DPI_CONSTANT)."px ".(2*DPI_CONSTANT)."px ".(3*DPI_CONSTANT)."px;
		    }
		    
		    #pdfBody h2 {
		        background-color: #666666;
		        color: #FFFFFF;
		        font-size: ".($fontSize+(2*DPI_CONSTANT))."px;
		        font-weight:normal;
		        border: ".max(1, (0.5*DPI_CONSTANT))."px solid #000000;
		        padding: ".max(1, (0.5*DPI_CONSTANT))."px ".(3*DPI_CONSTANT)."px ".(2*DPI_CONSTANT)."px ".(3*DPI_CONSTANT)."px;
		        margin-bottom: ".(2*DPI_CONSTANT)."px;
		        margin-top: ".(2*DPI_CONSTANT)."px;
		    }
		    
		    #pdfBody h3 {
		        background-color: #999999;
		        color: #FFFFFF;
		        font-size: ".($fontSize+(1*DPI_CONSTANT))."px;
		        font-weight:normal;
		        border: ".max(1, (0.5*DPI_CONSTANT))."px solid #000000;
		        padding: ".max(1, (0.5*DPI_CONSTANT))."px ".(3*DPI_CONSTANT)."px ".(2*DPI_CONSTANT)."px ".(3*DPI_CONSTANT)."px;
		        margin-bottom: ".(2*DPI_CONSTANT)."px;
		        margin-top: ".(10*DPI_CONSTANT)."px;
		    }
		    
		    #pdfBody h4 {
		        margin-top:0;
		        margin-bottom:0;
		        font-size: ".($fontSize+(1*DPI_CONSTANT))."px;
		    }
		    
		    #pdfBody #ni_report_wrapper, #pdfBody #hqp_report_wrapper, #pdfBody #ldr_report_wrapper, #pdfBody #ldr_comments_wrapper, #pdfBody #ldr_budget_wrapper {
		        width: 100%;
		    }
		    
		    #pdfBody #ni_budget_wrapper {
		        width:100%;
		    }
		    
		    .pdfnodisplay {
		        display:none;
		    }
		    
		    #pdfBody p {
		        margin: 0;
		    }
		    
		    #pdfBody small, #pdfBody .small {
		        font-size: ".max(10, ($fontSize+(-3*DPI_CONSTANT)))."px;
		        display:inline;
		    }
		    
		    #pdfBody .smaller {
		        font-size: ".max(9, ($fontSize+(-4*DPI_CONSTANT)))."px;
		    }
		    
		    #pdfBody ul {
		        margin-top: ".max(9, ($fontSize+(-4*DPI_CONSTANT)))."px;
		        margin-bottom: ".max(9, ($fontSize+(-4*DPI_CONSTANT)))."px;
		    }
		    
		    #pdfBody li {
		        font-weight: normal !important;
		        margin-bottom: ".max(9, ($fontSize+(-4*DPI_CONSTANT)))."px;
		    }
		    
		    #pdfBody b, #pdfBody strong {
                font-weight: bold !important;
            }
		    
		    #pdfBody .label {
		        font-weight: bold;
		    }
		    
		    #pdfBody ins {
                background: #AAFFAA;
                display: inline;
                text-decoration: none;
                vertical-align:top;
                position:relative;
            }

            #pdfBody del {
                background: #FFAAAA;
                display: inline;
                text-decoration: none;
                vertical-align:top;
                position:relative;
            }
            
            #pdfBody .logo {
                width:".(203*DPI_CONSTANT)."px;
                height:".(68*DPI_CONSTANT)."px;
                position:absolute;
                margin-top: ".(10*DPI_CONSTANT)."px;
            }
            
            #pdfBody .logo_div {
                margin-bottom: 0;
                height: ".(($fontSize+4)*$nInfo + (20*DPI_CONSTANT))."px;
            }
            
            #pdfBody br {
                font-size: 0.5em;
            }
		    
		</style>
		<!--[if lt IE 9]>
		    <style type='text/css'>
		        #pdfBody .logo {
		            background-image: none;
		            filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../skins/Grand_Logo.png',sizingMethod='scale');
                    -ms-filter: \"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='../skins/Grand_Logo.png', sizingMethod='scale')\";
                }
            </style>
        <![endif]-->
		$head
		$previewScript
		</head>";
		$headerName = "";
        if($project != null){
            $headerName = "{$project->getName()}: {$project->getFullName()}";
        }
        else {
            $headerName = "{$person->getReversedName()}";
        }
        $pages = '
        <script type="text/php">

if ( isset($pdf) ) {

  $font = Font_Metrics::get_font("verdana");
  $size = 8;
  $size2 = 6;
  $color = array(0,0,0);
  $text_height = Font_Metrics::get_font_height($font, $size);
  $text_height2 = Font_Metrics::get_font_height($font, $size2);
  
  $foot = $pdf->open_object();
  
  $w = $pdf->get_width();
  $h = $pdf->get_height();

  // Draw a line along the bottom
  $y = $h - $text_height2 - 24;
  $pdf->line(16, $y - $text_height2, $w - 16, $y - $text_height2, $color, 0.5);
  $pdf->line(16, 22 + $text_height, $w - 16, 22 + $text_height, $color, 0.5);
  $pdf->close_object();
  $pdf->add_object($foot, "all");
  $text = "Page {PAGE_NUM} of {PAGE_COUNT}";

  // Center the text
  $width = Font_Metrics::get_text_width("Page 1 of 50", $font, $size2);
  
  $nameWidth = Font_Metrics::get_text_width("'.$headerName.'", $font, $size);
  
  $pdf->page_text($w - $width - 22, $y+4 - $text_height2, $text, $font, $size2, $color);
  $pdf->page_text($w - $nameWidth - 22, 20, "'.$headerName.'", $font, $size, $color);
}
</script>';
        $dateStr = date("Y-m-d H:i:s T", time());
        if($preview){
            echo $header."<body><div id='pdfBody'><div id='page_header'>{$headerName}</div><hr style='border-width:1px 0 0 0;position:absolute;left:".(0*DPI_CONSTANT)."px;right:".(0*DPI_CONSTANT)."px;top:".(10*DPI_CONSTANT)."px;' /><div style='position:absolute;top:0;font-size:smaller;'><i>Generated: $dateStr</i></div>$html</div></body></html>";
            return;
        }
        
        $html = str_replace("–", '-', $html);
        $html = str_replace("’", '\'', $html);
        $html = str_replace("“", '"', $html);
        $html = str_replace("”", '"', $html);
        //$html = utf8_encode($html);
        $html = preg_replace('/\cP/', '', $html);
        $finalHTML = utf8_decode($header."<body id='pdfBody'><div style='margin-top:-".($fontSize*1.6)."px;font-size:smaller;'><i>Generated: $dateStr</i></div>$pages$html</body></html>");
        $dompdf->load_html($finalHTML);
        $dompdf->render();
        //$pdfStr = $dompdf->output();
        $pdfStr = PDFGenerator::processChapters($dompdf, $name);
        unset($dompdf);
        $GLOBALS['footnotes'] = array();
        $GLOBALS["nFootnotesProcessed"] = 0;
        return array('html' => $finalHTML, 'pdf' => $pdfStr);
        PDFGenerator::stream($pdfStr);
    }
    
    /**
     * Concatenates the chapters to the pdf document using GhostScript
     * @param DOMPDF $dompdf The instantiated DOMPDF object
     * @param string $name The name of the PDF document
     * @returns string Returns the pdf string
     */
    function processChapters($dompdf, $name){
        $str = "";
        foreach($GLOBALS['chapters'] as $chapter){
            if(count($chapter['subs']) > 0){
                $str .= "[/Count ".count($chapter['subs'])." /Title ({$chapter['title']}) /Page {$chapter['page']} /OUT pdfmark\n";
                foreach($chapter['subs'] as $sub){
                    $str .= "[/Title ({$sub['title']}) /Page {$sub['page']} /OUT pdfmark\n";
                }
            }
            else{
                $str .= "[/Title ({$chapter['title']}) /Page {$chapter['page']} /OUT pdfmark\n";
            }
        }
        $rand = rand(0, 100000000);
        $nRetries = 0;
        while(file_exists("/tmp/{$name}{$rand}pdfmarks") && $nRetries < 5){
            // File is already in use, wait one second and try again, but don't try more than 5 times
            $nRetries++;
            sleep(1);
        }
        file_put_contents("/tmp/{$name}{$rand}pdfmarks", $str);
        file_put_contents("/tmp/{$name}{$rand}pdf", $dompdf->output());
        exec("gs -q -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=\"/tmp/{$name}{$rand}withmarks\" \"/tmp/{$name}{$rand}pdf\" \"/tmp/{$name}{$rand}pdfmarks\""); // Add Bookmarks
        $pdfStr = file_get_contents("/tmp/{$name}{$rand}withmarks");
        unlink("/tmp/{$name}{$rand}pdfmarks");
        unlink("/tmp/{$name}{$rand}pdf");
        unlink("/tmp/{$name}{$rand}withmarks");
        $GLOBALS['chapters'] = array();
        $GLOBALS['nFootnotes'] = 0;
        $GLOBALS['section'] = 0;
        return $pdfStr;
    }
    
    /**
     * Adds a chapter bookmark to the document
     * @param string $title The title of the bookmark
     */
    function addChapter($title){
        global $wgOut;
        $title = strip_tags($title);
        $wgOut->addHTML("<div></div>
                        <script type='text/php'>
                            \$GLOBALS['chapters'][] = array('title' => \"{$title}\", 
                                                            'page' => \$pdf->get_page_number(),
                                                            'subs' => array());
                        </script>");
    }
    
    /**
     * Adds a sub-Chapter bookmark to the document
     * @param string $title The title of the sub-bookmark
     */
    function addSubChapter($title){
        global $wgOut;
        $title = strip_tags($title);
        $wgOut->addHTML("<div></div>
                        <script type='text/php'>
                            \$GLOBALS['chapters'][count(\$GLOBALS['chapters'])-1]['subs'][] = array('title' => \"{$title}\", 
                                                            'page' => \$pdf->get_page_number(),
                                                            'subs' => array());
                        </script>");
    }
    
    /**
     * Adds a footnote to the PDF
     * @param string $note The text for the footnote
     */
    function addFootNote($note){
        global $wgOut;
        $wgOut->addHTML("<script type='text/php'>
                            if(!isset(\$GLOBALS[\"nFootnotes\"])){
                                \$GLOBALS[\"nFootnotes\"] = 0;
                                \$GLOBALS[\"nFootnotesProcessed\"] = 0;
                            }
                            \$GLOBALS[\"nFootnotes\"]++;
                            \$GLOBALS[\"footnotes\"][\$PAGE_NUM][".(FootnoteReportItem::$nFootnotes-1)."] = array(\"id\" => ".FootnoteReportItem::$nFootnotes.", \"note\" => \"{$note}\", \"processed\" => false);
                            \$php_code = '
                                if(isset(\$GLOBALS[\"footnotes\"][\$PAGE_NUM])){
                                    \$font = Font_Metrics::get_font(\"verdana\");
                                    \$size = 6;
                                    \$text_height = Font_Metrics::get_font_height(\$font, \$size);
                                    \$color = array(0,0,0);
                                    \$w = \$pdf->get_width();
                                    \$h = \$pdf->get_height();
                                    \$y = \$h - \$text_height - 24;
                                    
                                    \$maxX = array();
                                    ksort(\$GLOBALS[\"footnotes\"][\$PAGE_NUM]);
                                    foreach(\$GLOBALS[\"footnotes\"][\$PAGE_NUM] as \$key => \$footnote){
                                        \$key -= \$GLOBALS[\"nFootnotesProcessed\"];
                                        \$id = \$footnote[\"id\"];
                                        \$note = \$footnote[\"note\"];
                                        \$xOffset = floor(\$key / 3);
                                        \$text_width = Font_Metrics::get_text_width(\"[\$id] \$note\", \$font, \$size);
                                        if(!isset(\$maxX[\$xOffset])){
                                            \$maxX[\$xOffset] = 0;
                                        }
                                        \$xOffsetAlready = 0;
                                        if(\$xOffset > 0){
                                            \$xOffsetAlready = \$maxX[\$xOffset-1];
                                        }
                                        \$maxX[\$xOffset] = max(\$maxX[\$xOffset], \$xOffsetAlready+\$text_width);
                                    }
                                    \$i = 0;
                                    foreach(\$GLOBALS[\"footnotes\"][\$PAGE_NUM] as \$key => \$footnote){
                                        if(!\$footnote[\"processed\"]){
                                            \$GLOBALS[\"footnotes\"][\$PAGE_NUM][\$key][\"processed\"] = true;
                                            \$key -= \$GLOBALS[\"nFootnotesProcessed\"];
                                            \$id = \$footnote[\"id\"];
                                            \$note = \$footnote[\"note\"];
                                            \$xOffset = floor(\$key / 3);
                                            \$x = 0;
                                            if(isset(\$maxX[\$xOffset-1])){
                                                \$x = \$maxX[\$xOffset-1];
                                            }
                                            \$extraHeight = 0;
                                            if((\$key + 1) > 6){
                                                \$extraHeight = \$text_height;
                                            }
                                            \$pdf->text(22 + \$x + 8*\$xOffset, \$y+(\$extraHeight + \$text_height*(\$key - (\$xOffset)*3)) - \$text_height + 4, \"[\$id] \$note\", \$font, \$size, \$color);
                                            \$i++;
                                        }
                                    }
                                    \$GLOBALS[\"nFootnotesProcessed\"] += \$i;
                                }
                                ';
                             \$pdf->page_script(\$php_code);
                        </script>");
    }
    
    function changeSection(){
        global $wgOut;
        // It doesn't look like dompdf supports this yet.  We want to display the page numbers like {section#} - {page#}
        $wgOut->addHTML("<script type='text/php'>
            \$php_code = '\$GLOBALS[\"section\"]++;';
            \$pdf->page_script(\$php_code);
        </script>");  
    }
    
    /**
     * Streams the pdf to the browser
     * @param string $pdfStr The pdf string
     */
    function stream($pdfStr){
        header("Content-Type: application/pdf");
        echo $pdfStr;
        exit;
    }
}

?>
