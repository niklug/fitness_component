define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_guide/recipe_types',
        'collections/nutrition_plan/nutrition_guide/recipe_variations',
        'views/nutrition_plan/nutrition_guide/add_recipe_item',
        'views/exercise_library/select_filter',
	'text!templates/nutrition_plan/nutrition_guide/add_recipe.html',
], function (
        $,
        _,
        Backbone,
        app,
        Recipe_types_collection,
        Recipe_variations_collection, 
        Add_recipe_item_view,
        Select_filter_fiew,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            _.bindAll(this, 'render',  'addItem', 'clearRecipeItems');
            this.collection.bind("reset", this.clearRecipeItems, this);
            this.collection.bind("add", this.addItem, this);
        },

        render:function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find(".example_day_meal_recipes_list");
            
            this.loadItems();

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
        
        loadItems : function() {
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
        },


        addItem : function(model) {
            app.views.recipe_item_view = new Add_recipe_item_view({
                example_day_id : this.options.example_day_id,
                nutrition_plan_id : this.options.nutrition_plan_id,
                collection : this.collection,
                model : model
            }); 
            this.container_el.append( app.views.recipe_item_view.render().el );

            app.models.pagination.set({'items_total' : model.get('items_total')});
            console.log(app.models.pagination);
        },

        clearRecipeItems : function() {
            this.container_el.empty();
        },

        events:{
            "click .cancel_add_recipe": "onCancelViewRecipe"
        },

        onCancelViewRecipe :function (event) {
            $(".add_recipe").show();
            $(".add_recipe_container").hide();
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