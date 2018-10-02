ManageProductsViewRow = Backbone.View.extend({
    
    tagName: 'tr',
    parent: null,
    row: null,
    duplicating: false,
    template: _.template($('#manage_products_row_template').html()),
    otherPopupTemplate: _.template($('#manage_products_other_popup_template').html()),
    projectsPopupTemplate: _.template($('#manage_products_projects_popup_template').html()),
    
    initialize: function(options){
        this.parent = options.parent;
        this.listenTo(this.model, "change", this.render);
        this.listenTo(this.model, "change:projects", this.render);
        this.listenTo(this.model, "change:access_id", this.setDirty, true);
    },
    
    setDirty: function(trigger){
        this.model.dirty = true;
        if(trigger){
            this.model.trigger("dirty");
        }
    },
    
    select: function(projectId){
        var projects = this.model.get('projects');
        if(_.where(projects, {id: projectId}).length == 0){
            projects.push({id: projectId});
        }
        // Only trigger an event if this is a parent
        if(this.$("input[data-project=" + projectId + "]").attr('name') == 'project'){
            this.model.trigger("change", this.model);
        }
        this.setDirty(false);
    },
    
    unselect: function(projectId){
        var project = _.findWhere(this.parent.projects.models
                                      .concat(this.parent.otherProjects.models)
                                      .concat(this.parent.oldProjects.models), {id: projectId});
        var projects = this.model.get('projects');

        // Unselect all subprojects as well
        if(project != undefined){
            _.each(project.get('subprojects'), $.proxy(function(sub){
                var index = _.indexOf(projects, _.findWhere(projects, {id: sub.id}));
                if(index != -1){
                    projects.splice(index, 1);
                    this.$("input[data-project=" + sub.id + "]").prop('checked', false);
                }
            }, this));
        }
        projects.splice(_.indexOf(projects, _.findWhere(projects, {id: projectId})), 1);
        // Only trigger an event if this is a parent
        if(this.$("input[data-project=" + projectId + "]").attr('name') == 'project'){
            this.model.trigger("change", this.model);
        }
        this.setDirty(false);
    },
    
    toggleSelect: function(e){
        var target = $(e.currentTarget);
        var projectId = target.attr('data-project');
        if(projectId != undefined){
            if(target.is(":checked")){
                // 'Check' Project
                this.select(projectId);
                if(target.attr('name') == "subproject"){
                    var parentId = target.attr('data-parent');
                    this.$("div[data-project=" + parentId + "] div.subprojectPopup").show();
                }
                else if(target.attr('name') == "otherproject"){
                    $("div.otherSubProjects", target.parent()).slideDown();
                }
            }
            else{
                // 'Uncheck' Project
                this.unselect(projectId);
                if(target.attr('name') == "project"){
                    // Do nothing
                }
                else if(target.attr('name') == "subproject"){
                    var parentId = target.attr('data-parent');
                    this.$("div[data-project=" + parentId + "] div.subprojectPopup").show();
                }
                else if(target.attr('name') == "otherproject"){
                    $("div.otherSubProjects", target.parent()).slideUp();
                }
            }
        }
        
        this.setDirty(true);
    },
    
    showSubprojects: function(e){
        var target = $(e.currentTarget);
        var projectId = target.attr('data-project');
        var project = _.findWhere(this.parent.projects.models, {id: projectId});
        this.$("div[data-project=" + projectId + "] div.subprojectPopup").html(this.projectsPopupTemplate(_.extend(project.toJSON(), {projects: this.model.get('projects')})));
        this.$("div[data-project=" + projectId + "] div.subprojectPopup").slideDown();
    },
    
    showOther: function(e){
        this.$("div.otherPopup").html(this.otherPopupTemplate(this.model.toJSON()));
        this.$("div.otherPopup").slideDown();
    },
    
    filterSearch: function(e){
        var target = $(e.currentTarget);
        var value = target.val();
        var block = target.parent();
        var options = $("div.popupMainProject", block);
        options.each(function(i, el){
            var text = $(el).text();
            if(unaccentChars(text).indexOf(unaccentChars(value)) == -1){
                $(el).slideUp(150);
            }
            else{
                $(el).slideDown(150);
            }
        });
    },
    
    editProduct: function(){
        var view = new ProductEditView({el: this.parent.editDialog, model: this.model, isDialog: true});
        this.parent.editDialog.view = view;
        this.parent.editDialog.dialog({
            height: $(window).height()*0.75, 
            width: 800,
            title: "Edit " + productsTerm
        });
        this.parent.editDialog.dialog('open');
    },
    
    deleteProduct: function(){
        this.parent.deleteDialog.model = this.model;
        this.parent.deleteDialog.dialog('open');
    },
    
    duplicateProduct: function(){
        if(!this.duplicating){
            // Only duplicate if there isn't already a pending one happening
            this.$(".copy-icon").css('background', 'none');
            this.$(".copy-icon .throbber").show();
            this.duplicating = true;
            var product = new Product(this.model.toJSON());
            product.set('id', null);
            product.save(null, {
                success: $.proxy(function(){
                    clearSuccess();
                    clearError();
                    addSuccess('The ' + product.get('category') + ' <i>' + product.get('title') + '</i> was duplicated');
                    this.parent.products.add(product);
                    this.duplicating = false;
                    this.$(".copy-icon").css('background', '');
                    this.$(".copy-icon .throbber").hide();
                }, this),
                error: $.proxy(function(){
                    clearSuccess();
                    clearError();
                    addError('There was a problem duplicating the ' + product.get('category') + ' <i>' + product.get('title') + '</i>');
                    this.duplicating = false;
                    this.$(".copy-icon").css('background', '');
                    this.$(".copy-icon .throbber").hide();
                }, this)
            });
        }
    },
    
    events: {
        "change input[type=checkbox]": "toggleSelect",
        "click div.showSubprojects": "showSubprojects",
        "click div.showOther": "showOther",
        "change input.popupBlockSearch": "filterSearch",
        "keyup input.popupBlockSearch": "filterSearch",
        "click .edit-icon": "editProduct",
        "click .copy-icon": "duplicateProduct",
        "click .delete-icon": "deleteProduct"
    },
    
    render: function(){
        var classes = new Array();
        this.$("td").each(function(i, val){
            classes.push($(val).attr("class"));
        });
        var isMine = {isMine: false};
        if(_.contains(_.pluck(this.model.get('authors'), 'id'), me.get('id')) ||
           _.contains(_.pluck(this.model.get('authors'), 'name'), me.get('name')) ||
           _.contains(_.pluck(this.model.get('authors'), 'fullname'), me.get('fullName')) ||
           _.intersection(_.pluck(this.model.get('authors'), 'id'), students).length > 0 ||
           _.intersection(_.pluck(this.model.get('authors'), 'name'), studentNames).length > 0 ||
           _.intersection(_.pluck(this.model.get('authors'), 'fullname'), studentFullNames).length > 0){
            isMine.isMine = true;
        }
        
        //Sanity Check: If there is ONLY title and year (no data), set incomplete == true;
        var incomplete = {incomplete: true};
        
        // $.proxy rebinds this to val to this.model.get('cat') instead of 'type'
        if(productStructure.categories[this.model.get('category')].
           types[this.model.getType()].data.length == 0){
            incomplete.incomplete = false;
        }
        else{
            _.each(productStructure.categories[this.model.get('category')].
                   types[this.model.getType()].data, $.proxy(function(val, key){        
                        if(this.model.get('data')[key] != undefined &&  this.model.get('data')[key].trim() != ""){
                            incomplete.incomplete = false;
                        }
                }, this)
            );
        }
        
        /*
        // This version of the code does NOT highlight if type of publication is changed.
        
        if ( _.size( _.filter(this.model.get('data').trim(), function(val){return val != ""})) >= 1){
            console.log(productStructure.categories[this.model.get('category')].types[this.model.get('type')].data)  
            //console.log(_.size( _.filter(this.model.get('data'), function(val){return val != ""})));
            incomplete.incomplete = false;
        } 
        */  
            
        this.el.innerHTML = this.template(_.extend(this.model.toJSON(), isMine, incomplete));
        if(this.parent.table != null){
            // Need this so that the search functionality is updated
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
