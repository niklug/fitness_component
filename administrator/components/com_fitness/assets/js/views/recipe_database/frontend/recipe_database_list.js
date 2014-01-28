define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/recipe_database/frontend/recipe_database_list_item',
        'views/nutrition_plan/nutrition_guide/recipe_types_filter',
        'views/nutrition_plan/nutrition_guide/recipe_variations_filter',
	'text!templates/recipe_database/frontend/recipe_database_list.html'
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Recipe_database_list_item_view,
        Recipe_types_filter_view,
        Recipe_variations_filter_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            this.controller = app.routers.nutrition_plan;
            this.collection.bind("add", this.addRecipeItem, this);
            this.collection.bind("reset", this.clearRecipeItems, this);
        },

        render:function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find(".recipes_list");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addRecipeItem(model);
                });
            }

            this.connectFilter();

            this.connectRecipeVariationsFilter();

            return this;
        },
        
        addRecipeItem : function(model) {
            this.recipe_database_list_item = new Recipe_database_list_item_view({model : model}); 
            
            this.container_el.append( this.recipe_database_list_item.render().el );

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },

        clearRecipeItems : function() {
            this.container_el.empty();
        },

        connectFilter : function() {
            this.filter_container = this.$el.find("#recipe_database_filter_wrapper");

            app.collections.recipe_types = new Recipe_types_collection();

            var self = this;

            app.collections.recipe_types.fetch({
                wait : true,
                success : function(collection, response) {
                    self.filter_container.html(new Recipe_types_filter_view({model : response}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },

        connectRecipeVariationsFilter : function() {
            this.recipe_variations_filter_container = this.$el.find("#recipe_variations_filter_wrapper");

            app.collections.recipe_variations = new Recipe_variations_collection();

            var self = this;

            app.collections.recipe_variations.fetch({
                wait : true,
                success : function(collection, response) {
                    self.recipe_variations_filter_container.html(new Recipe_variations_filter_view({collection : collection}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        }

    });
            
    return view;
});