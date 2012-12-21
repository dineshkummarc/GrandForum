ProductView = Backbone.View.extend({

    initialize: function(){
        this.model.fetch();
        this.model.bind('change', this.render, this);
        this.template = _.template($('#product_template').html());
    },
    
    renderAuthors: function(){
        var views = Array();
        _.each(this.model.get('authors'), function(author, index){
            var link = new Link({id: author.id,
                                 text: author.name,
                                 url: author.url,
                                 target: '_blank'});
            views.push(new PersonLinkView({model: link}).render());
        });
        csv = new CSVView({el: this.$el.find('#productAuthors'), model: views}).render();
    },
    
    renderData: function(){
        
    },
    
    renderProjects: function(){
        var views = Array();
        _.each(this.model.get('projects'), function(project, index){
            var link = new Link({id: project.id,
                                 text: project.name,
                                 url: project.url,
                                 target: '_blank'});
            views.push(new ProjectLinkView({model: link}).render());
        });
        csv = new CSVView({el: this.$el.find('#productProjects'), model: views}).render();
    },
    
    render: function(){
        main.set('title', this.model.get('title'));
        this.$el.empty();
        this.$el.html(this.template(this.model.toJSON()));
        this.renderAuthors();
        this.renderData();
        this.renderProjects();
        return this.el;
    }

});
