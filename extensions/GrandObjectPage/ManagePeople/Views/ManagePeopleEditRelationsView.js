ManagePeopleEditRelationsView = Backbone.View.extend({

    relations: null,
    person: null,
    university: null,
    relationViews: null,

    initialize: function(options){
        this.person = options.person;
        this.university = options.university;
        this.model.fetch();
        this.relationViews = new Array();
        this.template = _.template($('#edit_relations_template').html());
        this.model.ready().then(function(){
            this.relations = this.model;
            this.listenTo(this.relations, "add", this.addRows);
            this.model.ready().then(function(){
                this.render();
            }.bind(this));
        }.bind(this));
        
        var dims = {w:0, h:0};
        // Reposition the dialog when the window is resized or the dialog is resized
        setInterval(function(){
	        if(this.$el.width() != dims.w || this.$el.height() != dims.h){
	            this.$el.dialog("option","position", {
                    my: "center center",
                    at: "center center",
                    offset: "0 -75%"
                });
	            dims.w = this.$el.width();
	            dims.h = this.$el.height();
	        }
	    }.bind(this), 100);
	    $(window).resize(function(){
	        this.$el.dialog("option","position", {
                my: "center center",
                at: "center center",
                offset: "0 -75%"
            });
	    }.bind(this));
    },
    
    clean: function(){
        _.each(this.relationViews, function(relView){
            relView.stopListening();
            relView.undelegateEvents();
        });
        this.stopListening();
        this.undelegateEvents();
    },
    
    getRelations: function(){
        var relations = new Backbone.Collection(this.relations.where({user2: this.person.get('id')}));
        if(this.university != null){
            var tmpRelations = new Backbone.Collection(this.relations.where({university: this.university.get('id')}));
            tmpRelations.each(function(relation){
                relation.set('personUniversity', this.university)
            }.bind(this));
        }
        relations = new Backbone.Collection(relations.filter(function(rel){
            return (rel.get('personUniversity') == this.university);
        }.bind(this)));
        return relations;
    },
    
    saveAll: function(){
        var person = this.person;
        var relations = this.getRelations();
        relations.each(function(relation){
            if(this.university != null){
                relation.set('university', this.university.get('id'));
            }
            if(relation.get('deleted') != "true"){
                if(!relation.save(null, {
                    success: function(){
                        
                    },
                    error: function(){
                        addError("Relation could not be saved");
                    }
                })){
                    addError(relation.validationError);
                };
            }
            else {
                relation.destroy({
                    success: function(){

                    },
                    error: function(){
                        addError("Relation could not be saved");
                    }
                });
            }
        }.bind(this));
    },
    
    addRelation: function(){
        this.relations.add(new PersonRelation({type: 'Supervises', 
                                               user1: me.get('id'), 
                                               user2: this.person.get('id'),
                                               startDate: this.university.get('startDate'),
                                               endDate: this.university.get('endDate'),
                                               university: this.university.get('id'),
                                               personUniversity: this.university}));
        this.$el.scrollTop(this.el.scrollHeight);
    },
    
    addRows: function(){
        var relations = this.getRelations();
        relations.each(function(relation, i){
            if(this.relationViews[i] == null){
                var view = new ManagePeopleEditRelationsRowView({model: relation});
                this.$("#relation_rows").append(view.render());
                this.relationViews[i] = view;
            }
        }.bind(this));
    },
    
    render: function(){
        this.$el.empty();
        this.$el.html(this.template());
        this.addRows();
        return this.$el;
    }

});

ManagePeopleEditRelationsRowView = Backbone.View.extend({
    
    tagName: 'tr',
    
    initialize: function(){
        this.listenTo(this.model, "change", this.update);
        this.template = _.template($('#edit_relations_row_template').html());
    },
    
    delete: function(){
        this.model.delete = true;
    },
    
    // Sets the end date to infinite (0000-00-00)
    setInfinite: function(){
        this.$("input[name=endDate]").val('').change();
        this.model.set('endDate', '');
        this.changeEnd();
    },
    
    events: {
        "click #infinity": "setInfinite",
        "change [name=startDate]": "changeStart",
        "change [name=endDate]": "changeEnd"
    },
    
    changeStart: function(){
        // These probably won't exist in most cases, but if they do, then yay
        var start_date = this.$("[name=startDate]").val();
        var end_date = this.$("[name=endDate]").val();
        if(start_date != "" && start_date != "0000-00-00"){
            this.$("[name=endDate]").datepicker("option", "minDate", start_date);
        }
    },
    
    changeEnd: function(){
        // These probably won't exist in most cases, but if they do, then yay
        var start_date = this.$("[name=startDate]").val();
        var end_date = this.$("[name=endDate]").val()
        if(end_date != "" && end_date != "0000-00-00"){
            this.$("[name=startDate]").datepicker("option", "maxDate", end_date);
        }
        else{
            this.$("[name=startDate]").datepicker("option", "maxDate", null);
        }
    },
    
    update: function(){
        if(this.model.get('deleted') == "true"){
            this.$el.addClass('deleted');
        }
        else{
            this.$el.removeClass('deleted');
        }
        if((this.model.get('status') == "Completed" ||
            this.model.get('status') == "Withdrew" ||
            this.model.get('status') == "Changed Supervisor") &&
           (this.model.get('endDate') == "" ||
            this.model.get('endDate') == "0000-00-00")){
            this.$(".endDateCell").css("background", "#FF8800");
            this.$(".relError").text("There should be an end date when status is '" + this.model.get('status') + "'").show();
        }
        else if((this.model.get('status') == "Continuing") &&
           (this.model.get('endDate') != "" &
            this.model.get('endDate') != "0000-00-00")){
            this.$(".endDateCell").css("background", "#FF8800");
            this.$(".relError").text("There should be no end date when status is '" + this.model.get('status') + "'").show();
        }
        else{
            this.$(".endDateCell").css("background", "");
            this.$(".relError").text("").hide();
        }
    },
   
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        this.update();
        _.defer(function(){
            this.$("[name=startDate]").change();
            this.$("[name=endDate]").change();
        }.bind(this));
        return this.$el;
    }, 
    
});
