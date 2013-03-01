(function( $ ){

    var convertURL = '';
    var delay = 10*1000;
    var maxSize = 5*1000*1000;
    var convertSVG = false;
    var interval = null;
    var recordInterval = null;
    var mouseInterval = null;
    var el;
    var selectable = false;
    var onCapture = undefined;
    var onFinishedRecord = undefined;
    var story = Array();
    var target = '';
    var oldWindowOnBeforeUnload = undefined;
    var currentSize = 0;
    
    var recordButton;
    var pickButton;
    var screenshotButton;
    var timeLeft;
    var sizeLeft;
    
    var timeTillNext = parseInt(delay/1000);

    var methods = {
        /**
         * Initializes the recording, to make all the buttons etc.
         */
        init : function() {
            var that = this;
            var recordDiv = $("<div class='record'>");
            recordDiv.css('padding', '2px');
            recordButton = $('<button onClick="return false;" style="padding:3px 10px !important;font-size:10px !important;"><span class="recordText">Record</span> <span class="record" style="font-size:12px;">●</span></button>');
            pickButton = $('<button onClick="return false;" style="padding:3px 10px !important;font-size:10px !important;">Select Element</button>');
            screenshotButton = $('<button style="padding:3px 10px !important;font-size:10px !important;" onClick="return false;">Capture (Shift+c)</button>');
            sizeLeft = $('<span style="margin-left:20px;font-size:10px;"></span><br />');
            timeLeft = $('<span style="margin-left:20px;font-size:10px;"></span>');
            
            $(window).keydown(function(e){
                if(e.shiftKey && e.keyCode == 67 && e.target.nodeName.toLowerCase() != 'input' && 
                                                    e.target.nodeName.toLowerCase() != 'textarea' &&
                                                    e.target.nodeName.toLowerCase() != 'select' &&
                                                    e.target.nodeName.toLowerCase() != 'option'){ // Alt + c
                    methods['takeScreenshot'].apply(that);
                }
            });
            recordButton.click(function(e){
                if(interval == null){
                    methods['start'].apply(that);
                    e.stopPropagation();
                }
                else{
                    screenshotButton.hide();
                    pickButton.hide();
                    sizeLeft.hide();
                    timeLeft.hide();
                    methods['stop'].apply(that);
                    if(onFinishedRecord != undefined){
                        onFinishedRecord(story.slice(0));
                    }
                    story = Array();
                    window.onbeforeunload = oldWindowOnBeforeUnload;
                    oldWindowOnBeforeUnload = undefined;
                }
            });
            pickButton.click(function(e){
                methods['start'].apply(that);
            });
            screenshotButton.click(function(){
                methods['takeScreenshot'].apply(that);
            });
            
            pickButton.hide();
            screenshotButton.hide();
            sizeLeft.hide();
            timeLeft.hide();
            
            recordButton.appendTo(recordDiv);
            pickButton.appendTo(recordDiv);
            screenshotButton.appendTo(recordDiv);
            sizeLeft.appendTo(recordDiv);
            timeLeft.appendTo(recordDiv);
            
            recordDiv.appendTo($(el));
        },
        /**
         * Takes a screenshot of the selected element
         */
        takeScreenshot : function(){
            var that = this;
            if(interval != null){
                if(delay > 0){
                    clearInterval(interval);
                }
                clearInterval(recordInterval);
                timeLeft.html('Capturing...');
                if(convertSVG && $($("svg:visible"), that).length > 0){
                    var converted = Array();
                    var deferreds = Array();
                    $.each($($("svg:visible"), that), function(index, value){
                        var svg = $(value).clone().wrapAll("<div/>").parent().html();
                        deferreds.push($.post(convertURL, {'svg': svg}, function(response){
                            var img = $("<img id='img" + index + "' src='data:image/png;base64, " + response + "' />");
                            var oldValue = $(value).replaceWith(img);
                            converted[index] = oldValue;
                        }));
                    });
                    $.when.apply(null, deferreds).done(function(){
                        methods['html2canvas'].apply(this, function(canvas){
                            converted.forEach(function(c, cId){
                                $("#img" + cId).replaceWith(c);
                            });
                            if(onCapture != undefined){
                                onCapture(canvas);
                            }
                        });
                    });
                }
                else{
                    methods['html2canvas'].apply(this, function(canvas){
                        if(onCapture != undefined){
                            onCapture(canvas);
                        }
                    });
                }
                if(delay > 0){
                    interval = setInterval(function(){methods['takeScreenshot'].apply(that);}, delay);
                    timeTillNext = parseInt(delay/1000);
                }
                else{
                    interval = 0;
                }
                recordInterval = setInterval(function(){methods['recordBlink'].apply(that);}, 1000);
            }
        },
        /**
         * Changes the 'record' button so that the circle blinks.
         */
        recordBlink : function(){
            var that = this;
            if($("span.record", $(that).parent()).css('color') == "rgb(255, 0, 0)"){
                $("span.record", $(that).parent()).css('color', '');
            }
            else{
                $("span.record", $(that).parent()).css('color', '#FF0000');
            }
            if(delay > 0){
                timeTillNext--;
                timeLeft.html('Next Screenshot in ' + timeTillNext + ' s');
            }
            else{
                timeLeft.empty();
            }
        },
        /**
         * Shows the current size of the recording
         */
        showSize : function(over){
            var that = this;
            if(parseFloat(currentSize) <= maxSize && over == false){
                sizeLeft.html(currentSize + '/' + Math.round(maxSize/1000/1000) + 'MB');
                sizeLeft.css('color', '');
            }
            else{
                sizeLeft.html(currentSize + '/' + Math.round(maxSize/1000/1000) + 'MB<br />The last screenshot exceeded the size limit.  Please stop recording to start a new session.');
                sizeLeft.css('color', '#FF0000');
            }
        },
        /**
         * Runs after the dom is selected, sets up some of the intervals and listeners
         */
        afterStart : function(dom){
            var that = this;
            sizeLeft.show();
            timeLeft.show();
            screenshotButton.show();
            screenshotButton.css('display', 'inline-block');
            if(selectable){
                pickButton.show();
                pickButton.css('display', 'inline-block');
            }
            target = dom;
            methods['stop'].apply(that);
            $("span.recordText", $(that).parent()).html('Stop');
            if(delay > 0){
                interval = setInterval(function(){methods['takeScreenshot'].apply(that);}, delay);
            }
            else{
                interval = 0;
            }
            methods['showSize'].apply(that, Array(false));
            recordInterval = setInterval(function(){methods['recordBlink'].apply(that);}, 1000);
            if(typeof oldWindowOnBeforeUnload == 'undefined'){
                oldWindowOnBeforeUnload = window.onbeforeunload;
                window.onbeforeunload = function(){ return "You are currently recording a screen capture session.  Leaving this page will cause the session to be lost.  To save the session, press the 'Stop' button."};
            }
            var mX = 0;
            var mY = 0;
            $(target).mousemove(function(e){
                mX = e.pageX - $(target).position().left;
                mY = e.pageY - $(target).position().top;
            });
            $(target).click(function(e){
                var data = {event: 'click',
                            x: e.pageX - $(target).position().left,
                            y: e.pageY - $(target).position().top,
                            date: new Date().toJSON()
                           };
            });
            mouseInterval = setInterval(function(){
                var data = {event: 'mousemove',
                            x: mX,
                            y: mY,
                            date: new Date().toJSON()
                           };
                var size = JSON.stringify(story).length;
                var sizeAfter = size + JSON.stringify(data).length;
                if(sizeAfter <= maxSize){
                    story.push(data);
                    currentSize = (sizeAfter/1000/1000).toFixed(2);
                }
                else{
                    currentSize = (size/1000/1000).toFixed(2);
                    methods['showSize'].apply(that, Array(true));
                }
            }, 100);
        },
        /**
         * If applicable, starts the dom selection process, then calls the afterStart method
         */
        start : function(){
            var that = this;
            currentSize = 0;
            story = Array();
            if(selectable){
                var outline = DomOutline({onClick: function(){methods['afterStart'].apply(that);}});
                outline.start();
            }
            else{
                methods['afterStart'].apply(this, Array($(that)));
            }
        },
        /**
         * Stops the recording
         */
        stop : function(){
            var that = this;
            timeLeft.empty();
            sizeLeft.empty();
            if(delay > 0){
                clearInterval(interval);
            }
            interval = null;
            clearInterval(recordInterval);
            clearInterval(mouseInterval);
            recordInterval = null;
            mouseInterval = null;
            $(target).unbind('click');
            $(target).unbind('mousemove');
            $("span.record", $(that).parent()).css('color', '');
            $("span.recordText", $(that).parent()).html('Record');
        },
        /**
         * Calls the html2canvas library.  Converts any svg if there are any
         */
        html2canvas : function(callback){
            var that = this;
            html2canvas(target, {
                onrendered: function(canvas) {
                    var img = canvas.toDataURL().replace('data:image/png;base64,', '');
                    if(img != ''){
                        var data = {
                                    'event' : 'screen',
                                    'url' : document.location.toString() + document.location.hash,
                                    'img' : canvas.toDataURL().replace('data:image/png;base64,', ''),
                                    'date': new Date().toJSON(),
                                    'descriptions': Array(),
                                    'transition': ''
                                   };
                        var size = JSON.stringify(story).length;
                        var sizeAfter = size + JSON.stringify(data).length;
                        
                        if(sizeAfter <= maxSize){
                            story.push(data);
                            currentSize = (sizeAfter/1000/1000).toFixed(2);
                            methods['showSize'].apply(that, Array(false));
                        }
                        else{
                            currentSize = (size/1000/1000).toFixed(2);
                            methods['showSize'].apply(that, Array(false));
                        }
                    }
                    if(callback != undefined){
                        callback(canvas);
                    }
                }
            });
        }
    };

    $.fn.record = function(options) {
        var browserVersion = parseFloat($.browser.fullVersion);
        if(($.browser.msie && browserVersion < 9) ||
           ($.browser.mozilla && browserVersion < 3.5) ||
           ($.browser.opera && browserVersion < 12)){
            return;
        }
        
        if(options.convertSVG == true && options.convertURL != undefined){
            convertURL = options.convertURL;
            convertSVG = true;
        }
        if(options.delay != undefined){
            delay = options.delay;
        }
        if(options.maxSize != undefined){
            maxSize = options.maxSize;
        }
        if(options.onCapture != undefined){
            onCapture = options.onCapture;
        }
        if(options.onFinishedRecord != undefined){
            onFinishedRecord = options.onFinishedRecord;
        }
        if(options.selectable != undefined){
            selectable = options.selectable;
        }
        if(options.el != undefined){
            el = $(options.el);
        }
        else{
            el = $(this).parent();
        }
        
        methods['init'].apply(this);
        
    };
})( jQuery );
