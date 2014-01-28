define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/recipe_database/frontend/latest_recipes/item',
	'text!templates/recipe_database/frontend/latest_recipes/list.html'
], function (
        $,
        _,
        Backbone,
        app,
        Item_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            this.collection.bind("reset", this.clearItems, this);
        },

        render : function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find("#latest_recipes_container");
            var self = this;
            _.each(this.collection.models, function(model){
                self.container_el.append( new Item_view({model : model}).render().el);
            });
                
            return this;
        },

        clearItems : function() {
            this.container_el.empty();
        },

    });
            
    return view;
});