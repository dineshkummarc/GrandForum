EditBioView = Backbone.View.extend({
    template: _.template($("#edit_bio_template").html()),

    initialize: function(options){
        this.parent = options.parent;
    },

    upload: function(){
        var button = $("#editGsmsUpload");
        button.prop("disabled", true);
        this.$(".throbber").show();
        ccvUploaded = function(success, errors){
            // Purposefully global so that iframe can access
                clearAllMessages();
                if(success != ""){
                    addSuccess(success);
                }
                if(errors != ""){
                    addError(errors);
                }
                button.prop("disabled", false);
            this.$(".throbber").hide();
        }.bind(this);
        var form = this.$("form");
        form.submit();
    },

    events: {
        "click #uploadUserOTBio": "upload"
    },

    render: function(){
        this.$el.html(this.template());
        this.$("#coursemember-select").chosen();
        return this.$el;
    }

});
