Product = Backbone.Model.extend({
    initialize: function(){
        
    },
    
    urlRoot: 'index.php?action=api.product',
    
    defaults: {
        id : null,

    },
});

Products = Backbone.Collection.extend({
    initialize: function(){
        this.on('all', function(event){
            console.log(event);
        });
    },

    model: Product,
    
    url: 'index.php?action=api.product'
});
