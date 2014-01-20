define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/nutrition_plan/nutrition_guide/add_recipe_item',
        'views/nutrition_plan/nutrition_guide/recipe_types_filter',
        'views/nutrition_plan/nutrition_guide/recipe_variations_filter',
	'text!templates/nutrition_plan/nutrition_guide/add_recipe.html',
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Add_recipe_item_view,
        Recipe_types_filter_view,
        Recipe_variations_filter_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            this.controller = app.routers.nutrition_plan;
            _.bindAll(this, 'render',  'addRecipeItem', 'clearRecipeItems');
            this.collection.bind("reset", this.clearRecipeItems, this);
            this.collection.bind("add", this.addRecipeItem, this);
        },

        render:function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find(".example_day_meal_recipes_list");

            this.connectFilter();

            this.connectRecipeVariationsFilter();

            return this;
        },


        addRecipeItem : function(model) {
            var meal_id = this.model.get('id');
            model.set({'meal_id' : meal_id});
            app.views.recipe_item_view = new Add_recipe_item_view({collection : this.collection, model : model}); 
            this.container_el.append( app.views.recipe_item_view.render().el );

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },

        clearRecipeItems : function() {
            this.container_el.empty();
        },

        events:{
            "click .cancel_add_recipe": "onCancelViewRecipe"
        },

        onCancelViewRecipe :function (event) {
            this.controller.navigate("!/example_day/" + this.model.get('example_day_id'), true);
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