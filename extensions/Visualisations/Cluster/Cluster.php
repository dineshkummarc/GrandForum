<?php

class Cluster extends Visualisation {
    
    static $a = 0;
    var $url = "";
    var $width = "800";
    var $height = "800";
    
    function Cluster($url, $sizeLabel="Size", $countLabel="Count"){
        $this->url = $url;
        $this->sizeLabel = $sizeLabel;
        $this->countLabel = $countLabel;
        self::Visualisation();
    }
    
    static function init(){
        global $wgOut, $wgServer, $wgScriptPath, $visualisations;
        $wgOut->addScript("<script src='$wgServer$wgScriptPath/extensions/Visualisations/Cluster/js/cluster.js' type='text/javascript'></script>");
    }

    function show(){
        global $wgOut, $wgServer, $wgScriptPath;
        $string = "<div style='height:".($this->height)."px;width:".($this->width)."px;' class='Cluster' id='vis{$this->index}'></div></div>";
        $string .= <<<EOF
<script type='text/javascript'>
    
    function onLoad{$this->index}(){
        $('#vis{$this->index}').cluster({url: '{$this->url}'});
    }
            
    $(document).ready(function(){
        if($('#vis{$this->index}:visible').length > 0){
            onLoad{$this->index}();
        }
    });

</script>
EOF;
        return $string;
    }
}


?>
