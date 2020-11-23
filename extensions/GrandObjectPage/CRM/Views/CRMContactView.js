CRMContactView = Backbone.View.extend({

    initialize: function(){
        this.model.fetch();
        this.listenTo(this.model, "sync", this.render);
        this.template = _.template($('#crm_contact_template').html());
    },
       
    events: {
        "click #edit": "edit"
    },
    
    edit: function(){
        document.location = document.location + '/edit';
    },
    
    render: function(){
        main.set('title', this.model.get('title'));
        this.$el.html(this.template(this.model.toJSON()));
        return this.$el;
    }

});
