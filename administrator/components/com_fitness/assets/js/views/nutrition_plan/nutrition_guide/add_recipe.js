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
            
            this.connectPagination();
            
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
        
        events:{
            "click .search_recipes": "search",
            "click .clear_search": "clear",
            'keypress input[type=text]': 'filterOnEnter',
        },
        
        loadItems : function() {
            //console.log(this.collection.toJSON());
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
                model : model,
                keep_open : this.options.keep_open
            }); 
            this.container_el.append( app.views.recipe_item_view.render().el );
            
            
         },

        clearRecipeItems : function() {
            this.container_el.empty();
        },
        
        connectFilters : function() {
            new Select_filter_fiew({
                model : this.options.recipe_params_model,
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
                model : this.options.recipe_params_model,
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
        
        search : function() {
            var recipe_name = this.$el.find(".recipe_name").val();
            this.options.recipe_params_model.set({
                recipe_name : recipe_name,
                page : "1"
            });
        },

        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
        
        clear : function() {
            $(this.el).find(".recipe_name").val('');
            $(this.el).find(".filter_select").val(0);
            this.options.recipe_params_model.set(
                {
                    recipe_name : '',
                    filter_options : '0',
                    recipe_variations_filter_options : '0',
                    status : '', 
                    page : '1',
                    state : '1',
                    sort_by : 'recipe_name',
                    uid : app.getUniqueId() 
                }
            );
        },
        
        connectPagination : function() {
            this.pagination_model = $.backbone_pagination({el : $(this.el).find(".pagination_container")});
            var self = this;
            this.collection.once("add", function(model) {
                self.pagination_model.set({'items_total' : model.get('items_total')});
            });
            
            if(this.collection.models.length){
                this.pagination_model.set({'items_total' : this.collection.models[0].get('items_total')});
            }
            
            this.pagination_model.bind("change:currentPage", this.set_recipes_model, this);
            this.pagination_model.bind("change:items_number", this.set_recipes_model, this);
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            this.options.recipe_params_model.set({"page" : this.pagination_model.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
        },
  
        
    });
            
    return view;
});