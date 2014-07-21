ManageProductsView = Backbone.View.extend({

    allProjects: null,
    otherProjects: null,
    oldProjects: null,
    products: null,
    projects: null,
    table: null,
    nProjects: 0,
    subViews: new Array(),
    dialog: null,

    initialize: function(){
        this.allProjects = new Projects();
        this.allProjects.fetch();
        this.template = _.template($('#manage_products_template').html());
        me.getProjects();
        this.listenTo(this.model, "sync", function(){
            this.products = this.model.getAll();
            this.listenTo(this.products, "add", this.addRows);
            me.projects.ready().then($.proxy(function(){
                this.projects = me.projects.getCurrent();
                this.model.ready().then($.proxy(function(){
                    this.allProjects.ready().then($.proxy(function(){
                        this.otherProjects = this.allProjects.getCurrent();
                        this.oldProjects = this.allProjects.getOld();
                        this.otherProjects.remove(this.projects.models);
                        this.oldProjects.remove(this.projects.models);
                        me.projects.ready().then($.proxy(function(){
                            this.render();
                        }, this));
                    }, this));
                }, this));
            }, this));
        }, this);
    },
    
    addProduct: function(){
        var model = new Product({authors: [me.toJSON()]});
        var view = new ProductEditView({el: this.dialog, model: model, isDialog: true});
        this.dialog.view = view;
        this.dialog.dialog({
            height: $(window).height()*0.75, 
            width: 800,
            title: "Create Product"
        });
        this.dialog.dialog('open');
    },
    
    productChanged: function(){
        // Count how many products there are dirty
        var sum = 0;
        this.products.each(function(product){
            if(product.dirty){
                sum++;
            }
        });
        this.$("#saveN").html("(" + sum + ")");
        if(sum > 0){
            window.onbeforeunload = function(){
                return "You have unsaved Products";
            }
        }
        else{
            window.onbeforeunload = null;
        }
        
        // Change the state of the 'selectAll' checkbox
        this.projects.each(function(project){
            var allFound = true;
            this.products.each(function(product){
                if(allFound && _.where(product.get('projects'), {id: project.get('id')}).length == 0){
                    allFound = false;
                }
            }, this);
            if(allFound){
                this.$("input.selectAll[data-project=" + project.get('id') + "]").prop('checked', true);
            }
            else{
                this.$("input.selectAll[data-project=" + project.get('id') + "]").prop('checked', false);
            }
        }, this);
    },
    
    addRows: function(){
        if(this.table != undefined){
            this.table.destroy();
            this.table = null;
        }
        var models = _.pluck(_.pluck(this.subViews, 'model'), 'id');
        this.products.each($.proxy(function(p, i){
            if(!_.contains(models, p.id)){
                this.listenTo(p, "dirty", this.productChanged);
                if(p.dirty == undefined){
                    p.dirty = false;
                }
                var row = new ManageProductsViewRow({model: p, parent: this});
                this.subViews.push(row);
                this.$("#productRows").append(row.$el);
            }
        }, this));
        _.each(this.subViews, $.proxy(function(row){
            row.render();
        }, this));
        this.createDataTable();
        this.productChanged();
    },
    
    cacheRows: function(){
        if(this.table != null){
            var rows = this.table.rows().indexes();
            var table = this.table;
            rows.each($.proxy(function(i, val){
                this.subViews[i].row = this.table.row(i);
            }, this));
        }
    },
    
    createDataTable: function(){
        this.table = this.$('#listTable').DataTable({'bPaginate': false,
                                                     'autoWidth': false,
                                                     'aoColumnDefs': [
                                                        {'bSortable': false, 'aTargets': _.range(0, this.projects.length + 2) }
                                                     ],
	                                                 'aLengthMenu': [[-1], ['All']]});
	    this.cacheRows();
	    this.table.order([this.projects.length + 2,'desc']).draw();
	    table = this.table;
	    this.$('#listTable_wrapper').prepend("<div id='listTable_length' class='dataTables_length'></div>");
	    this.$("#listTable_length").html('<button id="saveProducts">Save All <span id="saveN">(0)</span></button><span style="display:none;" class="throbber"></span>');
    },
    
    toggleSelect: function(e){
        var target = $(e.currentTarget);
        var projectId = target.attr('data-project');
        var checked = target.is(":checked");
        _.each(this.subViews, function(view){
            if(checked){
                view.select(projectId);
            }
            else{
                view.unselect(projectId);
            }
        });
        this.productChanged();
    },
    
    saveProducts: function(){
        var error = false;
        this.products.each(function(product){
            if(product.get('title').trim() == ""){
                error = true;
            }
        });
        if(error){
            addError("There is a product without a title");
            return;
        }
        this.$("#saveProducts").prop('disabled', true);
        this.$(".throbber").show();
        var xhrs = new Array();
        this.products.each(function(product){
            if(product.dirty){
                // Save all Dirty Products
                xhrs.push(product.save({}, {
                    success: function(){
                        // Save was successful, mark it as 'clean'
                        product.dirty = false;
                    }
                }));
            }
        });
        $.when.apply(null, xhrs).done($.proxy(function(){
            // Success
            clearAllMessages();
            addSuccess("All products have been successfully saved");
            this.$("#saveProducts").prop('disabled', false);
            this.$(".throbber").hide();
            this.productChanged();
        }, this)).fail($.proxy(function(e){
            // Failure
            clearAllMessages();
            var list = new Array();
            list.push("There was a problem saving the following products:<ul>");
            this.products.each(function(product){
                if(product.dirty){
                    list.push("<li>" + product.get('title') + "</li>");
                }
            });
            list.push("</ul>");
            addError(list.join(''));
            this.$("#saveProducts").prop('disabled', false);
            this.$(".throbber").hide();
            this.productChanged();
        }, this));
    },
    
    events: {
        "click .selectAll": "toggleSelect",
        "click #saveProducts": "saveProducts",
        "click #addProductButton": "addProduct"
    },
    
    render: function(){
        this.$el.empty();
        $(document).click($.proxy(function(e){
            var popup = $("div.popupBox:visible").not(":animated").first();
            if(popup.length > 0 && !$.contains(popup[0], e.target)){
                _.each(this.subViews, function(view){
                    if(view.$("div.popupBox").is(":visible")){
                        // Need to defer the event so that unchecking a project is not in conflict
                        _.defer(function(){
                            view.model.trigger("change");
                        });
                    }
                });
            }
        }, this));
        this.$el.html(this.template());
        this.addRows();
	    var maxWidth = 50;
	    this.$('.angledTableText').each(function(i, e){
	        maxWidth = Math.max(maxWidth, $(e).width());
	    });
	    this.$('.angledTableHead').height(maxWidth +"px");
	    this.$('.angledTableHead').width('40px');
	    this.productChanged();
	    this.dialog = this.$("#editDialog").dialog({
	        autoOpen: false,
	        modal: true,
	        show: 'fade',
	        resizable: false,
	        draggable: false,
	        open: function(){
	            $("html").css("overflow", "hidden");
	        },
	        beforeClose: $.proxy(function(){
	            this.dialog.view.stopListening();
	            this.dialog.view.undelegateEvents();
	            $("html").css("overflow", "auto");
	        }, this),
	        buttons: {
                "Save Product": $.proxy(function(){
                    var validation = this.dialog.view.validate();
                    if(validation != ""){
                        clearAllMessages("#dialogMessages");
                        addError(validation, true, "#dialogMessages");
                        return "";
                    }
                    this.dialog.view.model.save(null, {
                        success: $.proxy(function(){
                            clearAllMessages();
                            this.dialog.view.model.dirty = false;
                            this.dialog.dialog("close");
                            addSuccess("The Product has been saved sucessfully");
                            if(this.products.indexOf(this.dialog.view.model) == -1){
                                this.products.add(this.dialog.view.model);
                            }
                        }, this),
                        error: $.proxy(function(){
                            clearAllMessages("#dialogMessages");
                            addError("There was an error saving Product", true, "#dialogMessages");
                        }, this)
                    });
                }, this)
            }
	    });
	    $(window).resize($.proxy(function(){
	        this.dialog.dialog({height: $(window).height()*0.75});
	    }, this));
        return this.$el;
    }

});

ManageProductsViewRow = Backbone.View.extend({
    
    tagName: 'tr',
    parent: null,
    row: null,
    
    initialize: function(options){
        this.parent = options.parent;
        this.listenTo(this.model, "change", this.render);
        this.template = _.template($('#manage_products_row_template').html());
        this.otherPopupTemplate = _.template($('#manage_products_other_popup_template').html());
        this.projectsPopupTemplate = _.template($('#manage_products_projects_popup_template').html());
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
            this.model.trigger("change");
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
            this.model.trigger("change");
        }
        this.setDirty(false);
    },
    
    toggleSelect: function(e){
        var target = $(e.currentTarget);
        var projectId = target.attr('data-project');
        if(target.is(":checked")){
            // 'Check' Project
            this.select(projectId);
            if(target.attr('name') == "project"){
                //this.$("div[data-project=" + projectId + "] div.subprojectPopup").slideDown();
            }
            else if(target.attr('name') == "subproject"){
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
        var options = $("div", block).not(".subproject");
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
        var view = new ProductEditView({el: this.parent.dialog, model: this.model, isDialog: true});
        this.parent.dialog.view = view;
        this.parent.dialog.dialog({
            height: $(window).height()*0.75, 
            width: 800,
            title: "Edit Product"
        });
        this.parent.dialog.dialog('open');
    },
    
    events: {
        "change input[type=checkbox]": "toggleSelect",
        "click div.showSubprojects": "showSubprojects",
        "click div.showOther": "showOther",
        "change input.popupBlockSearch": "filterSearch",
        "keyup input.popupBlockSearch": "filterSearch",
        "click .edit-icon": "editProduct"
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
