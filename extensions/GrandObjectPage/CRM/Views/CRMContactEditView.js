CRMContactEditView = Backbone.View.extend({

    initialize: function(){
        this.model.fetch();
        this.listenTo(this.model, "sync", this.render);
        this.listenTo(this.model, "change:title", function(){
            main.set('title', this.model.get('title'));
        });
        this.template = _.template($('#crm_contact_edit_template').html());
    },
    
    save: function(){
        this.$(".throbber").show();
        this.$("#save").prop('disabled', true);
        this.model.save(null, {
            success: function(){
                this.$(".throbber").hide();
                this.$("#save").prop('disabled', false);
                clearAllMessages();
                document.location = this.model.get('url');
            }.bind(this),
            error: function(o, e){
                this.$(".throbber").hide();
                this.$("#save").prop('disabled', false);
                clearAllMessages();
                if(e.responseText != ""){
                    addError(e.responseText, true);
                }
                else{
                    addError("There was a problem saving the Contact", true);
                }
            }.bind(this)
        });
    },
       
    events: {
        "click #save": "save",
        "click #cancel": "cancel"
    },
    
    cancel: function(){
        document.location = this.model.get('url');
    },
    
    render: function(){
        main.set('title', this.model.get('title'));
        this.$el.html(this.template(this.model.toJSON()));
        return this.$el;
    }

});
