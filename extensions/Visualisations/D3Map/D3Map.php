<?php

require_once("SpecialD3Map.php");

class D3Map extends Visualisation {
    
    static $a = 0;
    var $url = "";
    var $width = "500";
    var $height = "500";
    
    function D3Map($url){
        $this->url = $url;
        self::Visualisation();
    }
    
    static function init(){
        global $wgOut, $wgServer, $wgScriptPath, $visualisations;
        $wgOut->addScript('<style rel="stylesheet" type="text/css">
.container, .container svg {
  position: absolute;
}

.container .edge {
    fill: none;
}

.container .edgeStroke {
    stroke: black;
    fill: none;
}

.container svg {
  width: 75px;
  height: 20px;
  padding-right: 100px;
  font: 11px sans-serif;
  font-weight: bold;
}

.container circle {
  stroke: black;
  stroke-width: 1.5px;
}

.container text {
    fill: black;
    text-shadow: 0 0 1.5px #ffffff;
}
</style>');
        $wgOut->addScript('<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>');
        $wgOut->addScript('<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>');
    }

    function show(){
        global $wgOut, $wgServer, $wgScriptPath;
        $string = "<div style='height:".($this->height)."px;width:".($this->width)."px;float:left;' id='vis{$this->index}'>
                   </div>
                   <div style='margin-top:25px;margin-left:25px;' id='visOptions{$this->index}'></div>";
        $string .= <<<EOF
<script type='text/javascript'>
    var params = Array();
    var showHide = Array();
    var lastMapRequest;
    function onLoad{$this->index}(){
        // Create the Google Map…
        var map = new google.maps.Map(d3.select("#vis{$this->index}").node(), {
            zoom: 4,
            center: new google.maps.LatLng(49, -100),
            mapTypeId: google.maps.MapTypeId.TERRAIN
        });

        // Load the station data. When the data comes back, create an overlay.
        function loadData(){
            lastMapRequest = d3.json("{$this->url}" + params.join(''), function(data){
                var locIndex = _.indexBy(data.locations, function(d){
                    return d.name;
                });
                var overlay = new google.maps.OverlayView();
                
                var locations = data.locations;
                var edges = data.edges;
                var arcs = {};
                _.each(edges, function(edge){
                    var source = edge.source;
                    var target = edge.target;
                    if(arcs[source] == undefined){
                        arcs[source] = {};
                    }
                    if(arcs[source][target] == undefined){
                        arcs[source][target] = 0;
                    }
                    arcs[source][target] += 1;
                });
                
                overlay.onAdd = function() {
                    d3.select(this.getPanes().overlayLayer).selectAll("div").remove();
                    var layer = d3.select(this.getPanes().overlayLayer).append("div")
                        .attr("class", "container");
                        
                    var edges = layer.append("div")
                        .attr("class", "edges");
                    
                    // Draw each marker as a separate SVG element.
                    // We could use a single SVG, but what size would it have?
                    overlay.draw = function() {
                        var projection = this.getProjection(),
                            padding = 10;
                        edges.selectAll("svg").remove();
                        
                        _.each(arcs, function(s, source){
                            _.each(s, function(t, target){
                                var locSource = locIndex[source];
                                var locTarget = locIndex[target];
                                
                                var sourceTransform = [transformX(locSource),transformY(locSource)];
                                var targetTransform = [transformX(locTarget),transformY(locTarget)];
                                
                                var dx = targetTransform[0] - sourceTransform[0];
                                var dy = targetTransform[1] - sourceTransform[1];
                                var dr = Math.sqrt(dx * dx + dy * dy);
                                
                                var colorFn = d3.interpolateRgb(locSource.color, locTarget.color);
                                edges.append("svg")
                                    .style("left", Math.min(sourceTransform[0], targetTransform[0]) - dr)
                                    .style("top", Math.min(sourceTransform[1], targetTransform[1]) - dr)
                                    .style("width", Math.abs(dx) + dr*2)
                                    .style("height", Math.abs(dy) + dr*2)
                                    .append("path")
                                    .attr("class", "edgeStroke")
                                    .attr("d", function(){
                                        var startX = dr;
                                        var startY = dr;
                                        if(sourceTransform[0] > targetTransform[0]){
                                            startX = Math.abs(dx) + dr;
                                        }
                                        if(sourceTransform[1] > targetTransform[1]){
                                            startY = Math.abs(dy) + dr;
                                        }
                                        return "M" + startX + "," + startY + "A" + dr + "," + dr + " 0 0,1 " + (dx + startX) + "," + (dy + startY);
                                    })
                                    .attr("stroke-width", Math.sqrt(t) + 2)
                                    .attr("opacity", function(d){if(showHide[locSource.name] == true || showHide[locTarget.name] == true){ return 1;} return 0;});
                                
                                edges.append("svg")
                                    .style("left", Math.min(sourceTransform[0], targetTransform[0]) - dr)
                                    .style("top", Math.min(sourceTransform[1], targetTransform[1]) - dr)
                                    .style("width", Math.abs(dx) + dr*2)
                                    .style("height", Math.abs(dy) + dr*2)
                                    .append("path")
                                    .attr("class", "edge")
                                    .attr("d", function(){
                                        var startX = dr;
                                        var startY = dr;
                                        if(sourceTransform[0] > targetTransform[0]){
                                            startX = Math.abs(dx) + dr;
                                        }
                                        if(sourceTransform[1] > targetTransform[1]){
                                            startY = Math.abs(dy) + dr;
                                        }
                                        return "M" + startX + "," + startY + "A" + dr + "," + dr + " 0 0,1 " + (dx + startX) + "," + (dy + startY);
                                    })
                                    .attr("stroke-width", Math.sqrt(t) + 1)
                                    .attr("stroke", colorFn(0.5))
                                    .attr("opacity", function(d){if(showHide[locSource.name] == true || showHide[locTarget.name] == true){ return 1;} return 0.1;});
                            });
                        });

                        var marker = layer.selectAll("svg.marker")
                            .data(d3.entries(data.locations))
                            .each(transform) // update existing markers
                            .enter().append("svg:svg")
                            .each(transform)
                            .attr("class", "marker");

                        // Add a circle.
                        marker.append("svg:circle")
                            .attr("r", 5)
                            .attr("cx", padding)
                            .attr("cy", padding)
                            .attr("fill", function(d){return d.value.color;});

                        // Add a label.
                      
                        marker.append("svg:text")
                            .attr("x", padding + 7)
                            .attr("y", padding)
                            .attr("dy", ".31em")
                            .text(function(d) { return d.value.name; });

                        function transformX(d){
                            d = new google.maps.LatLng(d.latitude, d.longitude);
                            d = projection.fromLatLngToDivPixel(d);
                            return d.x;
                        }
                        
                        function transformY(d){
                            d = new google.maps.LatLng(d.latitude, d.longitude);
                            d = projection.fromLatLngToDivPixel(d);
                            return d.y;
                        }

                        function transform(d) {
                            d = new google.maps.LatLng(d.value.latitude, d.value.longitude);
                            d = projection.fromLatLngToDivPixel(d);
                            return d3.select(this)
                                .style("left", (d.x - padding) + "px")
                                .style("top", (d.y - padding) + "px");
                        }
                    };
                };
                // Bind our overlay to the map…
                overlay.setMap(map);
                
                if($("#visOptions{$this->index}").html().trim() == '' && typeof data.filterOptions != 'undefined'){
                    $("#visOptions{$this->index}").append("<h3>Filter Options</h3><table>");
                    for(oId in data.filterOptions){
                        var option = data.filterOptions[oId];
                        $("#visOptions{$this->index} table").append("<tr><td><input type='checkbox' value='" + option.param + "' " + option.checked + " /></td><td valign='top'><b>" + option.name + "</b></td></tr>");
                        if(option.inverted){
                            $("#visOptions{$this->index} input[value=" + option.param + "]").addClass('inverted');
                        }
                    }
                    $("#visOptions{$this->index} input").change(function(){
                        if((!$(this).hasClass('inverted') && !$(this).is(':checked')) || ($(this).hasClass('inverted') && $(this).is(':checked'))) {
                            params.push('&' + $(this).val());
                        }
                        else{
                            var index = params.indexOf('&' + $(this).val());
                            params[index] = null;
                            delete params[index];
                        }
                        lastMapRequest.abort();
                        loadData();
                    });
                    
                    if(data.dateOptions != undefined){
                        $("#visOptions{$this->index}").append("<tr><td><b>Date Range:</b><br /><div style='margin-top:5px;margin-left:1px;width:200px;' id='visDateSlider{$this->index}'></div><div id='visDateSliderLabels{$this->index}' class='steps'></td></tr>");
                        var dateOptions = data.dateOptions;
                        $("#visDateSlider{$this->index}").slider({
                            min: dateOptions[0].date,
                            max: dateOptions[dateOptions.length-1].date,
                            step: 1,
                            slide: function( event, ui ) {
                                for(pId in params){
                                    var param = params[pId];
                                    if(param.indexOf('&date=') !== -1){
                                        params[pId] = null;
                                        delete params[pId];
                                    }
                                }
                                params.push('&date=' + ui.value);
                                
                                lastMapRequest.abort();
                                loadData();
                            }
                        });
                        for(dId in dateOptions){
                            var dOption = dateOptions[dId];
                            if(dOption.checked == 'checked'){
                                $("#visDateSlider{$this->index}").slider("option", "value", dOption.date);
                            }
                            var perc = Math.floor((dId/Math.max(1, (dateOptions.length-1)))*100);
                            $("#visDateSliderLabels{$this->index}").append("<span style='left: " + perc + "%;' class='tick'>|<br />" + dOption.date + "</span>");
                        }
                    }
                }
                $("#visOptions{$this->index} .showHide").remove();
                $("#visOptions{$this->index}").append("<tr class='showHide'><td><b>Highlight:</b><br /><div id='visShowHide{$this->index}'></div></td></tr>");
                _.each(locations, function(loc){
                    $("#visShowHide{$this->index}").append("<input value='" + loc.name.replace("'", "") + "' type='checkbox' />&nbsp" + loc.name + "<br />");
                    if(showHide[loc] == true){
                        
                    }
                });
                $("#visShowHide{$this->index} input").change(function(e){
                    var checked = $(e.currentTarget).is(":checked");
                    var loc = $(e.currentTarget).val();
                    showHide[loc] = checked;
                    overlay.onAdd();
                    overlay.draw();
                });
            });
        }
        loadData();
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
