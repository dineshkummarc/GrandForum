ManagePeopleEditRolesView = Backbone.View.extend({

    roles: null,
    person: null,
    roleViews: null,

    initialize: function(options){
        this.person = options.person;
        this.model.fetch();
        this.roleViews = new Array();
        this.listenTo(this.model, "change", this.render);
        this.template = _.template($('#edit_roles_template').html());
        this.model.ready().then($.proxy(function(){
            this.roles = this.model.getAll();
            this.listenTo(this.roles, "add", this.addRows);
            this.model.ready().then($.proxy(function(){
                this.render();
            }, this));
        }, this));
        
        var dims = {w:0, h:0};
        // Reposition the dialog when the window is resized or the dialog is resized
        setInterval($.proxy(function(){
	        if(this.$el.width() != dims.w || this.$el.height() != dims.h){
	            this.$el.dialog("option","position", {
                    my: "center center",
                    at: "center center",
                    offset: "0 -75%"
                });
	            dims.w = this.$el.width();
	            dims.h = this.$el.height();
	        }
	    }, this), 100);
	    $(window).resize($.proxy(function(){
	        this.$el.dialog("option","position", {
                my: "center center",
                at: "center center",
                offset: "0 -75%"
            });
	    }, this));
    },
    
    saveAll: function(){
        var copy = this.roles.toArray();
        clearAllMessages();
        _.each(copy, $.proxy(function(role){
            if(_.contains(allowedRoles, role.get('name'))){
                if(role.get('deleted') != "true"){
                    role.save(null, {
                        success: function(){
                            addSuccess("Roles saved");
                        },
                        error: function(){
                            addError("Roles could not be saved");
                        }
                    });
                }
                else {
                    role.destroy(null, {
                        success: function(){
                            addSuccess("Roles saved");
                        },
                        error: function(){
                            addError("Roles could not be saved");
                        }
                    });
                }
            }
        }, this));
    },
    
    addRole: function(){
        this.roles.add(new Role({name: "HQP", userId: this.person.get('id')}));
        this.$el.scrollTop(this.el.scrollHeight);
    },
    
    addRows: function(){
        this.roles.each($.proxy(function(role, i){
            if(this.roleViews[i] == null){
                var view = new ManagePeopleEditRolesRowView({model: role});
                this.$("#role_rows").append(view.render());
                if(i % 2 == 0){
                    view.$el.addClass('even');
                }
                else{
                    view.$el.addClass('odd');
                }
                this.roleViews[i] = view;
            }
        }, this));
    },
    
    render: function(){
        this.$el.empty();
        this.$el.html(this.template());
        this.addRows();
        return this.$el;
    }

});

ManagePeopleEditRolesRowView = Backbone.View.extend({
    
    tagName: 'tr',
    
    initialize: function(){
        this.listenTo(this.model, "change", this.update);
        this.listenTo(this.model, "change:projects", this.renderProjects);
        this.template = _.template($('#edit_roles_row_template').html());
    },
    
    delete: function(){
        this.model.delete = true;
    },
    
    // Sets the end date to infinite (0000-00-00)
    setInfinite: function(){
        this.$("input[name=endDate]").val('0000-00-00');
        this.model.set('endDate', '0000-00-00');
    },
    
    addProject: function(event){
        var selectedProject = this.$("#selectedProject option:selected");
        var name = selectedProject.text();
        var projects = this.model.get('projects');
        projects.push({id: null, name: name});
        this.model.trigger('change:projects');
    },
    
    deleteProject: function(event){
        var el = $(event.currentTarget);
        var projects = _.filter(this.model.get('projects'), function(project){
            return project.name != el.attr('data-project-id');
        });
        this.model.set('projects', projects);
    },
    
    events: {
        "click #infinity": "setInfinite",
        "click .roleProject": "deleteProject",
        "click #addProject": "addProject"
    },
    
    update: function(){
        if(this.model.get('deleted') == "true"){
            this.$el.addClass('deleted');
        }
        else{
            this.$el.removeClass('deleted');
        }
    },
    
    renderProjects: function(){
        this.$("#projects").empty();
        var template = _.template($("#edit_role_projects_template").html());
        _.each(this.model.get('projects'), $.proxy(function(proj){
            this.$("#projects").append(template(proj));
        }, this));
        if(this.$("#projects tr").length == 0){
            this.$("#projects").append("<tr><td align='center' colspan='2'>No Projects</td></tr>");
        }
    },
   
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        this.renderProjects();
        return this.$el;
    }, 
    
});
