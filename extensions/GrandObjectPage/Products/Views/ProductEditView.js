ProductEditView = Backbone.View.extend({

    isDialog: false,
    projectWarning: null,

    initialize: function(options){
        this.parent = this;
        this.listenTo(this.model, "sync", this.render);
        this.listenTo(this.model, "change:category", this.render);
        this.listenTo(this.model, "change:type", this.render);
        this.listenTo(this.model, "change:projects", this.updateProjectWarning);
        this.listenTo(this.model, "change:title", function(){
            if(!this.isDialog){
                main.set('title', this.model.get('title'));
            }
        });
        if(options.isDialog != undefined){
            this.isDialog = options.isDialog;
        }
        this.template = _.template($('#product_edit_template').html());
        
        var tagsGet = $.get(wgServer + wgScriptPath + '/index.php/index.php?action=api.product/tags');
        tagsGet.then($.proxy(function(availableTags){
            this.availableTags = availableTags;
            if(!this.model.isNew() && !this.isDialog){
                this.model.fetch({silent: true});
            }
            else{
                _.defer(this.render);
            }
        }, this));
    },
    
    events: {
        "click #saveProduct": "saveProduct",
        "click #cancel": "cancel",
        "click #btnViewAvailableTags": "toggleTagsList"
    },
    
    validate: function(){
        if(this.model.get('title').trim() == ""){
            return "The Product must have a title";
        }
        else if(this.model.get('category').trim() == ""){
            return "The Product must have a category";
        }
        else if(this.model.get('type').trim() == ""){
            return "The Product must have a type";
        }
        return "";
    },
    
    saveProduct: function(){
        var validation = this.validate();
        if(validation != ""){
            clearAllMessages();
            addError(validation, true);
            return;
        }
        this.$(".throbber").show();
        this.$("#saveProduct").prop('disabled', true);
        this.model.save(null, {
            success: $.proxy(function(){
                this.$(".throbber").hide();
                this.$("#saveProduct").prop('disabled', false);
                clearAllMessages();
                document.location = this.model.get('url');
            }, this),
            error: $.proxy(function(o, e){
                this.$(".throbber").hide();
                this.$("#saveProduct").prop('disabled', false);
                clearAllMessages();
                if(e.responseText != ""){
                    addError(e.responseText, true);
                }
                else{
                    addError("There was a problem saving the Product", true);
                }
            }, this)
        });
    },
    
    cancel: function(){
        document.location = this.model.get('url');
    },

    toggleTagsList: function() {
        var tagsDiv = $('#availableTagsDiv');
        if (tagsDiv.css('display') == 'none') {
            tagsDiv.slideDown(300);
            $('#btnViewAvailableTags').text('Hide Tags');
        } else {
            tagsDiv.slideUp(300);
            $('#btnViewAvailableTags').text('View Tags');
        }
    },
    
    renderAuthorsWidget: function(){
        var left = _.pluck(this.model.get('authors'), 'fullname');
        var right = _.difference(this.allPeople.pluck('fullName'), left);
        var objs = [];
        this.allPeople.each(function(p){
            objs[p.get('fullName')] = {id: p.get('id'),
                                       name: p.get('name'),
                                       fullname: p.get('fullName')};
        });
        var html = HTML.Switcheroo(this, 'authors.fullname', {name: this.model.getAuthorsLabel().toLowerCase(),
                                                          'left': left,
                                                          'right': right,
                                                          'objs': objs
                                                          });
        this.$("#productAuthors").html(html);
        createSwitcheroos();
    },
    
    renderAuthors: function(){
        if(this.allPeople != null && this.allPeople.length > 0){
            this.renderAuthorsWidget();
        }
        else{
            this.allPeople = new People();
            this.allPeople.simple = true;
            this.allPeople.fetch();
            var spin = spinner("productAuthors", 10, 20, 10, 3, '#888');
            this.allPeople.bind('sync', function(){
                if(this.allPeople.length > 0){
                    this.renderAuthorsWidget();
                }
            }, this);
        }
    },
    
    renderTagsWidget: function(){
        var html = HTML.TagIt(this, 'tags', {
            strictValues: false, 
            values: this.model.get('tags'),
            options: {
                removeConfirmation: false,
                availableTags: this.availableTags
            }
        });
        this.$("#productTags").html(html);
    },
    
    updateProjectWarning: function(){
        if(projectsEnabled && this.model.get('projects').length == 0){
            this.projectsWarning.show();
        } else {
            this.projectsWarning.hide();
        }
    },
    
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        this.projectsWarning = this.$("#projectsWarning");
        if(this.isDialog){
            $(".ui-dialog-buttonset #projectsWarning", this.$el.parent()).remove();
            this.projectsWarning.css('display', 'inline-block')
                                .css('margin', 0)
                                .css('margin-top', '2px')
                                .css('font-size', '1em')
                                .css('float', 'left')
                                .css('padding-right', '15px');
            this.projectsWarning.detach();
            $(".ui-dialog-buttonset", this.$el.parent()).prepend(this.projectsWarning);
        }
        this.renderAuthors();
        this.renderTagsWidget();
        this.updateProjectWarning();
        this.$(".integer").forceNumeric({min: 0, max: 99999999999});
        return this.$el;
    }

});
