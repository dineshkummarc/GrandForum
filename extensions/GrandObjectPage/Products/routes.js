PageRouter = Backbone.Router.extend({
    
    currentView: null,
        
    initialize: function(){
        this.bind('all', function(event){
            $("#currentView").html("<div id='currentViewSpinner'></div>");
            spin = spinner("currentViewSpinner", 40, 75, 12, 10, '#888');
        });
    },
    
    closeCurrentView: function(){
        if(this.currentView != null){
            clearAllMessages();
            this.currentView.unbind();
            this.currentView.remove();
            $("div#backbone_main").append("<div id='currentView' />");
        }
    },

    routes: {
        ":category": "showGrandProducts",
        ":category/grand": "showGrandProducts",
        ":category/nonGrand": "showNonGrandProducts",
        ":category/new": "newProduct",
        ":category/:id": "showProduct",
        ":category/:id/edit": "editProduct"
    }
});

function pluralizeCategory(category){
    if(category == 'Press'){
        category = category;
    }
    else{
        category = category.pluralize();
    }
    return category;
}

// Initiate the router
var pageRouter = new PageRouter;

pageRouter.on('route:showGrandProducts', function(category){
    // Get All Products
    products = new Products();
    products.category = category;
    products.grand = 'grand';
    
    category = pluralizeCategory(category);
    main.set('title', 'GRAND ' + category);
    this.closeCurrentView();
    this.currentView = new ProductListView({el: $("#currentView"), model: products});
});

pageRouter.on('route:showNonGrandProducts', function(category){
    // Get All Products
    products = new Products();
    products.category = category;
    products.grand = 'nonGrand';
    
    category = pluralizeCategory(category);
    main.set('title', 'Non-GRAND ' + category);
    this.closeCurrentView();
    this.currentView = new ProductListView({el: $("#currentView"), model: products});
});

pageRouter.on('route:newProduct', function(category){
    // Create New Product
    products = new Product();
});

pageRouter.on('route:showProduct', function (category, id) {
    // Get A single product
    product = new Product({'id': id});
    this.closeCurrentView();
    this.currentView = new ProductView({el: $("#currentView"), model: product});
});

pageRouter.on('route:editProduct', function (category, id) {
    // Get A single product
    product = new Product({'id': id});
    this.closeCurrentView();
    this.currentView = new ProductEditView({el: $("#currentView"), model: product});
});

// Start Backbone history a necessary step for bookmarkable URL's
Backbone.history.start();
