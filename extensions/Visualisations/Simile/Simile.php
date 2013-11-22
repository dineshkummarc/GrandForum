<?php

//require_once("SpecialSimile.php");

class Simile extends Visualisation {
    
    static $a = 0;
    var $url = "";
    var $year = REPORTING_YEAR;
    var $width = "70";
    var $interval = "50";
    var $popupWidth = 300;
    var $popupHeight = 175;
    
    function Simile($url){
        $this->url = $url;
        self::Visualisation();
    }
    
    static function init(){
        global $wgOut, $wgServer, $wgScriptPath;
        $wgOut->addStyle($wgServer.$wgScriptPath.'/extensions/Visualisations/Simile/simile.css');
        $wgOut->addScript('<script src="'.$wgServer.$wgScriptPath.'/extensions/Visualisations/Simile/Simile.js" type="text/javascript" charset="utf-8"></script>');
    }

    function show(){
        global $wgOut, $wgServer, $wgScriptPath;
        $string = "<div style='height:600px;' class='simile' id='vis{$this->index}'></div>";
        $string .= "<script type='text/javascript'>
            var tl;
            function onLoad{$this->index}() {
                _.defer(function(){
	                var eventSource = new Timeline.DefaultEventSource();
	                var theme = Timeline.ClassicTheme.create();
	                theme.event.bubble.width = {$this->popupWidth};
                    theme.event.bubble.height = {$this->popupHeight}; 
                    var bandInfos = [
                    Timeline.createBandInfo({
                        timeZone:       0,
                        eventSource:    eventSource,
                        date:           'Jul 01 {$this->year} 00:00:00 GMT',
                        width:          '{$this->width}%', 
                        intervalUnit:   Timeline.DateTime.MONTH, 
                        intervalPixels: {$this->interval},
                        theme: theme
                    }),
                    Timeline.createBandInfo({
		                showEventText:  false,
                        trackHeight:    0.5,
                        trackGap:       0.2,
                        timeZone:       0,
                        eventSource:    eventSource,
                        date:           'Jul 01 {$this->year} 00:00:00 GMT',
                        width:          '".($this->width*0.4)."%', 
                        intervalUnit:   Timeline.DateTime.YEAR, 
                        intervalPixels: ".($this->interval*2)."
                    })
                ];
              
                  bandInfos[1].syncWith = 0;
                  bandInfos[1].highlight = true;
                  bandInfos[1].eventPainter.setLayout(bandInfos[0].eventPainter.getLayout());
                  
                  tl = Timeline.create(document.getElementById('vis{$this->index}'), bandInfos);
                  Timeline.loadJSON('{$this->url}', function(json, url){
		                eventSource.loadJSON({
			                'events' : json,
			                'dateTimeFormat' : 'iso8601'
		                }, url);
	              });
	           });
            }

            var resizeTimerID = null;
            function onResize() {
                if (resizeTimerID == null) {
                    resizeTimerID = window.setTimeout(function() {
                        resizeTimerID = null;
                        tl.layout();
                    }, 500);
                }
            }
            
            Timeline.urlPrefix = '$wgServer$wgScriptPath/extensions/Visualisations/Simile/';
            
            $(document).ready(function(){
                if($('#vis{$this->index}:visible').length > 0){
                    onLoad{$this->index}();
                }
            });
        </script>";
        
        return $string;
    }
}


?>
