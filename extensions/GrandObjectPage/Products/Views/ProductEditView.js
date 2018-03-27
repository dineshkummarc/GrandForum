ProductEditView = Backbone.View.extend({

    isDialog: false,
    parent: null,

    initialize: function(options){
        this.parent = this;
        this.listenTo(this.model, "sync", this.render);
        this.listenTo(this.model, "change:projects", this.render);
        this.listenTo(this.model, "change:category", this.render);
        this.listenTo(this.model, "change:type", this.render);
        this.listenTo(this.model, "change:access", this.render);
        this.listenTo(this.model, "change:title", function(){
            if(!this.isDialog){
                main.set('title', this.model.get('title'));
            }
        });
        if(options.isDialog != undefined){
            this.isDialog = options.isDialog;
        }
        this.template = _.template($('#product_edit_template').html());

        if(!this.model.isNew() && !this.isDialog){
            this.model.fetch();
        }
        else{
            _.defer(this.render);
        }
    },
    
    // Sets the end date to infinite (0000-00-00)
    setInfinite: function(){
        this.$("input[name=date]").val('0000-00-00').change();
        //this.model.set('date', '0000-00-00');
    },
    
    events: {
        "click #infinity": "setInfinite",
        "click #saveProduct": "saveProduct",
        "click #cancel": "cancel",
        "change #acceptance_date": "changeStart",
        "change #date": "changeEnd"
    },
    
    changeStart: function(){
        var start_date = this.$("#acceptance_date").val();
        var end_date = this.$("#date").val();
        if(start_date != "" && start_date != "0000-00-00"){
            if(end_date != "" && end_date != "0000-00-00"){
                this.$("#date").datepicker("option", "minDate", start_date);
            }
            else{
                this.$("#date").datepicker("option", "minDate", "");
                this.$("#date").val(end_date).change();
            }
        }
    },
    
    changeEnd: function(){
        var start_date = this.$("#acceptance_date").val();
        var end_date = this.$("#date").val();
        if(end_date != "" && end_date != "0000-00-00"){
            this.$("#acceptance_date").datepicker("option", "maxDate", end_date);
        }
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
            error: $.proxy(function(){
                this.$(".throbber").hide();
                this.$("#saveProduct").prop('disabled', false);
                clearAllMessages();
                addError("There was a problem saving the Product", true);
            }, this)
        });
    },
    
    cancel: function(){
        document.location = this.model.get('url');
    },
    
    renderAuthorsWidget: function(){
        var objs = [];
        this.allPeople.each(function(p){
            objs[p.get('fullName')] = {id: p.get('id'),
                                       name: p.get('name'),
                                       fullname: p.get('fullName')};
        });

        var delimiter = ';';
        var html = HTML.TagIt(this, 'authors.fullname', {
            values: _.pluck(this.model.get('authors'), 'fullname'),
            strictValues: false, 
            objs: objs,
            options: {
                placeholderText: 'Enter ' + this.model.getAuthorsLabel().pluralize().toLowerCase() + ' here...',
                allowSpaces: true,
                allowDuplicates: false,
                removeConfirmation: false,
                singleFieldDelimiter: delimiter,
                splitOn: delimiter,
                availableTags: this.allPeople.pluck('fullName'),
                afterTagAdded: $.proxy(function(event, ui){
                    if(this.allPeople.pluck('fullName').indexOf(ui.tagLabel) >= 0){
                        ui.tag[0].style.setProperty('background', highlightColor, 'important');
                        ui.tag.children("a").children("span")[0].style.setProperty("color", "white", 'important');
                        ui.tag.children("span")[0].style.setProperty("color", "white", 'important');
                    }
                }, this),
                tagSource: function(search, showChoices) {
                    if(search.term.length < 2){ showChoices(); return; }
                    var filter = search.term.toLowerCase();
                    var choices = $.grep(this.options.availableTags, function(element) {
                        return (element.toLowerCase().match(filter) !== null);
                    });
                    showChoices(this._subtractArray(choices, this.assignedTags()));
                }
            }
        });
        this.$("#productAuthors").html(html);
        this.$("#productAuthors").append("<p><i>Drag to re-order each " + this.model.getAuthorsLabel().toLowerCase() + "</i></p>");
        this.$("#productAuthors .tagit").sortable({
            stop: function(event,ui) {
                $('input[name=authors_fullname]').val(
                    $(".tagit-label",$(this))
                        .clone()
                        .text(function(index,text){ return (index == 0) ? text : delimiter + text; })
                        .text()
                ).change();
            }
        });
        this.$el.on('mouseover', 'div[name=authors_fullname] li.tagit-choice', function(){
            $(this).css('cursor', 'move');
        });
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
    
    renderJournalsAutocomplete: function(){
        if(this.$("input[name=data_published_in]").length > 0){
            var autoComplete = {
                source: $.proxy(function(request, response){
                    var journals = new Journals();
                    journals.search = request.term;
                    journals.fetch({success: function(collection){
                        var data = _.map(collection.toJSON(), function(journal){
                            return {id: journal.id, 
                                    label: journal.title + " " + journal.year + " (" + journal.description + ")", 
                                    value: journal.title,
                                    journal: journal.title,
                                    impact_factor: journal.impact_factor,
                                    category_ranking: journal.category_ranking,
                                    eigen_factor: journal.eigenfactor,
                                    issn: journal.issn
                            };
                        });
                        response(data);
                    }});
                }, this),
                minLength: 2,
                select: $.proxy(function(event, ui){
                    _.defer($.proxy(function(){
                        this.$("input[name=data_published_in]").val(ui.item.journal).change();
                        this.$("input[name=data_impact_factor]").val(ui.item.impact_factor).change();
                        this.$("input[name=data_category_ranking]").val(ui.item.category_ranking).change();
                        this.$("input[name=data_eigen_factor]").val(ui.item.eigen_factor).change();
                        this.$("input[name=data_issn]").val(ui.item.issn).change();
                    }, this));
                }, this)
            };
            
            this.$("input[name=data_issn]").autocomplete(autoComplete);
            this.$("input[name=data_published_in]").autocomplete(autoComplete);
        }
    },
    
    render: function(){
        this.$el.html(this.template(this.model.toJSON()));
        this.renderAuthors();
        this.renderJournalsAutocomplete();
        
        if(productStructure.categories[this.model.get('category')] != undefined &&
           (productStructure.categories[this.model.get('category')].types[this.model.get('type')] != undefined &&
            _.size(productStructure.categories[this.model.get('category')].types[this.model.get('type')].titles) > 0) ||
            (_.size(_.first(_.values(productStructure.categories[this.model.get('category')].types)).titles) > 0)){
            this.$("select[name=title]").combobox();
        }
        this.$("input[name=data_category_ranking]").prop('disabled', true);
        this.$("input[name=data_impact_factor]").prop('disabled', true);
        this.$("input[name=data_eigen_factor]").prop('disabled', true);
        this.$("input[name=data_eigen_factor]").after("<div>The IFs reported are based on the data available on July 1, " + (YEAR - 1) + "</div>");

        _.defer($.proxy(function(){
            this.$("#acceptance_date").change();
            this.$("#date").change();
        }, this));
        return this.$el;
    }

});
