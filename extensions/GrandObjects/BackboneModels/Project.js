/**
 * Project Model
 */
Project = Backbone.Model.extend({
    initialize: function(){
        this.products = new ProjectProducts();
        this.products.url = this.urlRoot + '/' + this.get('id') + '/products';
    },
    
    urlRoot: 'index.php?action=api.project',
    
    getProducts: function(){
        this.products.fetch();
        return this.products;
    },
    
    getLink: function(){
        return new Link({id: this.get('id'),
                         text: this.get('name'),
                         url: this.get('url'),
                         target: ''});
                               
    },
    
    defaults: {
        id: null,
        name: '',
        fullname: '',
        description: '',
        status: '',
        type: '',
        phase: '',
        url: '',
        deleted: ''
    },
});

/**
 * Projects Collection
 */
Projects = Backbone.Collection.extend({
    model: Project,
    
    url: 'index.php?action=api.project'
});

/**
 * ProjectProduct RelationModel
 */
ProjectProduct = RelationModel.extend({
    initialize: function(){
    
    },
    
    urlRoot: function(){
        return 'index.php?action=api.project/' + this.get('projectId') + '/products'
    },
    
    idAttribute: 'productId',
    
    getOwner: function(){
        project = new Project({id: this.get('projectId')});
        project.fetch();
        return person;
    },
    
    getTarget: function(){
        var product = new Product({id: this.get('productId')});
        product.fetch();
        return product;
    },
    
    defaults: {
        projectId: "",
        productId: "",
        startDate: "",
        endDate: "",
    }
});

/**
 * ProjectProducts RangeCollection
 */
ProjectProducts = RangeCollection.extend({
    model: ProjectProduct,
    
    newModel: function(){
        return new Products();
    },
});
