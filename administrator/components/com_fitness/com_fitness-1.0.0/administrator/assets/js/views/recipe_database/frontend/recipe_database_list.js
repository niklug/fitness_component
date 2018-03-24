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
            this.collection.bind("add", this.addRecipeItem, this);
            this.collection.bind("reset", this.clearRecipeItems, this);
        },

        render:function () {
            $(this.el).html(this.template());
            this.container_el = this.$el.find(".recipes_list");

            this.onRender();

            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectRecipeTypesFilter();
                self.connectRecipeVariationsFilter();
                self.loadListItems();
            });
        },
        
        loadListItems : function() {
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addRecipeItem(model);
                });
            }
        },
        
        addRecipeItem : function(model) {
            this.recipe_database_list_item = new Recipe_database_list_item_view({model : model}); 
            
            this.container_el.append( this.recipe_database_list_item.render().el );

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },

        clearRecipeItems : function() {
            this.container_el.empty();
        },

        connectRecipeTypesFilter : function() {
            if(app.collections.recipe_types) {
                this.loadRecipeTypesSelect(app.collections.recipe_types );
                return;
            }
            var self = this;
            app.collections.recipe_types = new Recipe_types_collection();
            app.collections.recipe_types.fetch({
                success : function (collection, response) {
                    self.loadRecipeTypesSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeTypesSelect : function(collection) {
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_database_filter_wrapper"),
                collection : collection,
                title : 'RECIPE TYPE',
                first_option_title : 'ALL TYPES',
                class_name : ' dark_input_style ',
                id_name : 'recipe_type',
                select_size : 17,
                model_field : 'filter_options',
                element_disabled : ''
            }).render();  
        },
        
        connectRecipeVariationsFilter : function() {
            if(app.collections.recipe_variations) {
                this.loadRecipeVariationsSelect(app.collections.recipe_variations );
                return;
            }
            var self = this;
            app.collections.recipe_variations = new Recipe_variations_collection();
            app.collections.recipe_variations.fetch({
                success : function (collection, response) {
                    self.loadRecipeVariationsSelect(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadRecipeVariationsSelect : function(collection) {
            new Select_filter_fiew({
                model : this.model,
                el : this.$el.find("#recipe_variations_filter_wrapper"),
                collection : collection,
                title : 'RECIPE VARIATION',
                first_option_title : 'ALL VARIATIONS',
                class_name : ' dark_input_style ',
                id_name : 'recipe_variation',
                select_size : 12,
                model_field : 'recipe_variations_filter_options',
                element_disabled : ''
            }).render(); 
        },

    });
            
    return view;
});