OISView = Backbone.View.extend({

    oisId: "",
    ois: null,
    template: _.template($('#ois_view_template').html()),

    initialize: function(){
        this.oisId = this.model.get('ois_id');
        $.post("https://gars.ualberta.ca/ois/getinterview/", JSON.stringify({token: this.oisId}), function(response){
            this.ois = response;
            this.render();
        }.bind(this));
    },
    
    events: {
        "click .videoPrev:not(.disabled)": "prevVideo",
        "click .videoNext:not(.disabled)": "nextVideo"
    },
    
    prevVideo: function(e){
        this.$("video").each(function(i, video){
            video.pause();
            video.currentTime = 0;
        });
        if($(e.currentTarget).parents(".video").prev().length > 0){
            $(e.currentTarget).parents(".video").hide();
            $(e.currentTarget).parents(".video").prev().show();
            $("video", $(e.currentTarget).parents(".video").prev())[0].play();
        }
    },
    
    nextVideo: function(e){
        this.$("video").each(function(i, video){
            video.pause();
            video.currentTime = 0;
        });
        if($(e.currentTarget).parents(".video").next().length > 0){
            $(e.currentTarget).parents(".video").hide();
            $(e.currentTarget).parents(".video").next().show();
            $("video", $(e.currentTarget).parents(".video").next())[0].play();
        }
    },
    
    render: function(){
        if(this.ois != null){
            this.$el.html(this.template(this.ois));
            this.$(".video video").each(function(i, el){
                if(i == 0){
                    $(el).parents(".video").show();
                    $(".videoPrev", $(el).parents(".video")).addClass("disabled");
                }
                else if(i == this.$(".video video").length - 1){
                    $(".videoNext", $(el).parents(".video")).addClass("disabled");
                }
                $(el).bind('ended', this.nextVideo.bind(this));
            }.bind(this));
        }
        return this.$el;
    }
    
});
