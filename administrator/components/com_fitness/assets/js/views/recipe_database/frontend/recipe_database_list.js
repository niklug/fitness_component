define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/recipe_database/frontend/recipe_database_list_item',
        'views/exercise_library/select_filter',
	'text!templates/recipe_database/frontend/recipe_database_list.html'
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Recipe_database_list_item_view,
        Select_filter_fiew,
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
           
            
            if(app.collections.recipe_types && app.collections.recipe_variations) {
                this.connectFilters();
                return this;
            }

                
            app.collections.recipe_types = new Recipe_types_collection();
            app.collections.recipe_variations = new Recipe_variations_collection();
            
            var self = this;
            $.when (
                app.collections.recipe_types.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.collections.recipe_variations.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
 
            ).then (function(response) {
                self.connectFilters();
            })

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
        
        connectFilters : function() {
            new Select_filter_fiew({
                model : app.models.get_recipe_params,
                el : this.$el.find("#recipe_database_filter_wrapper"),
                collection : app.collections.recipe_types,
                title : 'FILTER CATEGORIES',
                first_option_title : 'ALL CATEGORIES',
                class_name : 'dark_input_style',
                id_name : '',
                select_size : 17,
                model_field : 'filter_options'
            }).render();

            new Select_filter_fiew({
                model : app.models.get_recipe_params,
                el : this.$el.find("#recipe_variations_filter_wrapper"),
                collection : app.collections.recipe_variations,
                title : 'RECIPE VARIATIONS',
                first_option_title : 'ALL VARIATIONS',
                class_name : 'dark_input_style',
                id_name : '',
                select_size : 12,
                model_field : 'recipe_variations_filter_options'
            }).render();
        },

    });
            
    return view;
});