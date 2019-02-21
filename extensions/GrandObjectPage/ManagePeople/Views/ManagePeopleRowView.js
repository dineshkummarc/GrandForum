ManagePeopleRowView = Backbone.View.extend({
    
    tagName: 'tr',
    parent: null,
    row: null,
    // Views
    editRoles: null,
    editProjects: null,
    editRelations: null,
    editUniversities: null,
    editSubRoles: null,
    editProjectLeaders: null,
    editThemeLeaders: null,
    // Dialogs
    rolesDialog: null,
    projectsDialog: null,
    relationsDialog: null,
    universitiesDialog: null,
    subRolesDialog: null,
    projectLeadersDialog: null,
    themeLeadersDialog: null,
    template: _.template($('#manage_people_row_template').html()),
    
    initialize: function(options){
        this.parent = options.parent;
        this.listenTo(this.model, "change", this.render);
        this.listenTo(this.model, "change:candidate", this.save);
    },
    
    openRolesDialog: function(){
        if(this.rolesDialog == null){
            this.rolesDialog = this.$("#rolesDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editRoles.stopListening();
	                this.editRoles.undelegateEvents();
	                clearInterval(this.editRoles.interval);
	                this.editRoles.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Role", 
	                    click: $.proxy(function(e){
	                        this.editRoles.addRole();
	                    }, this), 
	                    disabled: (allowedRoles.length == 0 || (this.model.get('candidate') && _.intersection(_.pluck(me.get('roles'), 'role'), [STAFF,MANAGER,ADMIN]).length == 0)),
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editRoles.saveAll();
                        this.rolesDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.rolesDialog.dialog('close');
	                }, this)
	            }
	        });
	    }
        this.rolesDialog.empty();
        this.rolesDialog.dialog('open');
        this.editRoles = new ManagePeopleEditRolesView({model: this.model.roles, 
                                                        person:this.model, 
                                                        el: this.rolesDialog});
    },
    
    openProjectsDialog: function(){
        if(this.projectsDialog == null){
            this.projectsDialog = this.$("#projectsDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editProjects.stopListening();
	                this.editProjects.undelegateEvents();
	                clearInterval(this.editProjects.interval);
	                this.editProjects.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Project", 
	                    click: $.proxy(function(e){
	                        this.editProjects.addProject();
	                    }, this),
	                    disabled: (allowedProjects.length == 0),
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editProjects.saveAll();
                        this.projectsDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.projectsDialog.dialog('close');
	                }, this)
	            }
	        });
        }
        this.projectsDialog.empty();
        this.projectsDialog.dialog('open');
        this.editProjects = new ManagePeopleEditProjectsView({model: this.model.projects, 
                                                              person:this.model, 
                                                              el: this.projectsDialog});
    },
    
    openRelationsDialog: function(){
        if(this.relationsDialog == null){
            this.relationsDialog = this.$("#relationsDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editRelations.stopListening();
	                this.editRelations.undelegateEvents();
	                clearInterval(this.editRelations.interval);
	                this.editRelations.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Relationship", 
	                    click: $.proxy(function(e){
	                        this.editRelations.addRelation();
	                    }, this), 
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editRelations.saveAll();
                        this.relationsDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.relationsDialog.dialog('close');
	                }, this)
	            }
	        });
        }
        this.relationsDialog.empty();
        this.relationsDialog.dialog('open');
        this.editRelations = new ManagePeopleEditRelationsView({model: me.relations, 
                                                                person:this.model, 
                                                                el: this.relationsDialog});
        this.editRelations.allPeople = this.parent.allPeople;
    },
    
    openUniversitiesDialog: function(){
        if(this.universitiesDialog == null){
            this.universitiesDialog = this.$("#universitiesDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editUniversities.stopListening();
	                this.editUniversities.undelegateEvents();
	                clearInterval(this.editUniversities.interval);
	                this.editUniversities.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Institution", 
	                    click: $.proxy(function(e){
	                        this.editUniversities.addUniversity();
	                    }, this), 
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editUniversities.saveAll();
                        this.universitiesDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.universitiesDialog.dialog('close');
	                }, this)
	            }
	        });
        }
        this.universitiesDialog.empty();
        this.universitiesDialog.dialog('open');
        this.editUniversities = new ManagePeopleEditUniversitiesView({model: this.model.universities, 
                                                                      person:this.model,
                                                                      el: this.universitiesDialog});
    },
    
    openSubRolesDialog: function(){
        if(this.subRolesDialog == null){
            this.subRolesDialog = this.$("#subRolesDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editSubRoles.stopListening();
	                this.editSubRoles.undelegateEvents();
	                clearInterval(this.editSubRoles.interval);
	                this.editSubRoles.interval = null;
	            }, this),
	            buttons: {
	                "Save": $.proxy(function(e){
	                    this.editSubRoles.model.save(null, {
	                        success: function(){
	                            clearAllMessages();
	                            addSuccess("Sub-Roles saved");
	                        },
	                        error: function(){
	                            clearAllMessages();
	                            addError("Could not modify Sub-Roles");
	                        }
	                    });
                        this.subRolesDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.subRolesDialog.dialog('close');
	                }, this)
	            }
	        });
	    }
        this.subRolesDialog.empty();
        this.subRolesDialog.dialog('open');
        this.editSubRoles = new ManagePeopleEditSubRolesView({model: this.model.subRoles,
                                                              el: this.subRolesDialog});
    },
    
    openProjectLeadersDialog: function(){
        if(this.projectLeadersDialog == null){
            this.projectLeadersDialog = this.$("#projectLeadershipDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editProjectLeaders.stopListening();
	                this.editProjectLeaders.undelegateEvents();
	                clearInterval(this.editProjectLeaders.interval);
	                this.editProjectLeaders.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Project", 
	                    click: $.proxy(function(e){
	                        this.editProjectLeaders.addProject();
	                    }, this),
	                    disabled: (allowedProjects.length == 0),
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editProjectLeaders.saveAll();
                        this.projectLeadersDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.projectLeadersDialog.dialog('close');
	                }, this)
	            }
	        });
        }
        this.projectLeadersDialog.empty();
        this.projectLeadersDialog.dialog('open');
        this.editProjectLeaders = new ManagePeopleEditProjectLeadersView({model: this.model.leaderships, 
                                                                          person:this.model, 
                                                                          el: this.projectLeadersDialog});
    },
    
    openThemeLeadersDialog: function(){
        if(this.themeLeadersDialog == null){
            this.themeLeadersDialog = this.$("#themeLeadershipDialog").dialog({
	            autoOpen: false,
	            modal: true,
	            show: 'fade',
	            resizable: false,
	            draggable: false,
	            width: 800,
	            position: {
                    my: "center bottom",
                    at: "center center"
                },
	            open: function(){
	                $("html").css("overflow", "hidden");
	            },
	            beforeClose: $.proxy(function(){
	                $("html").css("overflow", "auto");
	                this.editThemeLeaders.stopListening();
	                this.editThemeLeaders.undelegateEvents();
	                clearInterval(this.editThemeLeaders.interval);
	                this.editThemeLeaders.interval = null;
	            }, this),
	            buttons: {
	                "+": { 
	                    text: "Add Theme", 
	                    click: $.proxy(function(e){
	                        this.editThemeLeaders.addTheme();
	                    }, this),
	                    disabled: (allowedThemes.length == 0),
	                    style: "float: left;"
	                },
	                "Save": $.proxy(function(e){
	                    this.editThemeLeaders.saveAll();
                        this.themeLeadersDialog.dialog('close');
	                }, this),
	                "Cancel": $.proxy(function(){
	                    this.themeLeadersDialog.dialog('close');
	                }, this)
	            }
	        });
        }
        this.themeLeadersDialog.empty();
        this.themeLeadersDialog.dialog('open');
        this.editThemeLeaders = new ManagePeopleEditThemeLeadersView({model: this.model.themes, 
                                                                      person:this.model, 
                                                                      el: this.themeLeadersDialog});
    },
    
    save: function(){
        _.defer($.proxy(function(){
            this.$(".throbber").show();
        }, this));
        this.model.save(null, {
            success: $.proxy(function(){
                this.$(".throbber").hide();
            }, this),
            error: $.proxy(function(){
                this.$(".throbber").hide();
            }, this),
            silent: true
        });
    },
    
    events: {
        "click #editRoles": "openRolesDialog",
        "click #editProjects": "openProjectsDialog",
        "click #editRelations": "openRelationsDialog",
        "click #editUniversities": "openUniversitiesDialog",
        "click #editSubRoles": "openSubRolesDialog",
        "click #editProjectLeadership": "openProjectLeadersDialog",
        "click #editThemeLeadership": "openThemeLeadersDialog"
    },
    
    render: function(){
        var classes = new Array();
        this.$("td").each(function(i, val){
            classes.push($(val).attr("class"));
        });
        this.el.innerHTML = this.template(this.model.toJSON());
        if(this.parent.table != null){
            var data = new Array();
            this.$("td").each(function(i, val){
                data.push($(val).htmlClean().html());
            });
            if(this.row != null){
                this.row.data(data);
            }
        }
        if(classes.length > 0){
            this.$("td").each(function(i, val){
                $(val).addClass(classes[i]);
            });
        }
        return this.$el;
    }
    
});
