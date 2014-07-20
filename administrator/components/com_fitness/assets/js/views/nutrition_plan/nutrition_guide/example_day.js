define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/recipe_database/recipes',
        'collections/nutrition_plan/nutrition_guide/nutrition_guide_recipes',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/nutrition_guide/example_day_recipe',
	'text!templates/nutrition_plan/nutrition_guide/example_day.html',
        'jquery.timepicker'
], function ( 
        $,
        _,
        Backbone,
        app,
        Recipes_collection,
        Example_day_recipes_collection,
        Get_recipe_params_model,
        Example_day_add_recipe_view,
        Example_day_recipe_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.models.get_recipe_params = new Get_recipe_params_model();
                
            app.collections.recipes = new Recipes_collection(); 

            app.models.get_recipe_params.bind("change", this.get_database_recipes, this);
            
            app.collections.example_day_recipes = new Example_day_recipes_collection();
            
            app.collections.example_day_recipes.bind("add", this.addItem, this);
            
            this.getExampleDayRecipes();
        },
        
        template : _.template(template),

        render: function(){
            $(this.el).html(this.template({ }));
            return this;
        },

        events:{
            "click .add_recipe" : "onClickAddRecipe",
            "click .search_recipes": "search",
            "click .clear_search": "clear",
            'keypress input[type=text]': 'filterOnEnter',
        },
        
        onClickAddRecipe : function() {
            $(this.el).find(".add_recipe").hide();
            $(this.el).find(".add_recipe_container").show();
            
            if(parseInt(app.collections.recipes.length)) {
                return;
            }
            
            this.get_database_recipes();
            
            $(this.el).find(".add_recipe_container").html(new Example_day_add_recipe_view({
                example_day_id : this.options.example_day_id,
                menu_id : this.options.menu_id,
                nutrition_plan_id : this.options.nutrition_plan_id,
                collection : app.collections.recipes
            }).render().el);
            
            app.models.pagination = $.backbone_pagination({});
            app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);
            app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
        },
        
        get_database_recipes : function() {
            app.collections.recipes.reset();
            //console.log(app.models.get_recipe_params.toJSON());
            app.collections.recipes.fetch({
                data : app.models.get_recipe_params.toJSON(),
                success : function(collection, response) {
                    console.log(collection.toJSON());
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
        },
        
        getExampleDayRecipes : function() {
            var self = this;
            app.collections.example_day_recipes.fetch({
                data: {
                    nutrition_plan_id : this.options.nutrition_plan_id,
                    example_day_id : this.options.example_day_id
                },
                success : function(collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadItems : function(collection) {
            var self = this;
            _.each(collection.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            $(this.el).find("#recipes_list_container").append(new Example_day_recipe_view({
                menu_id : this.options.menu_id,
                model : model,
                collection : app.collections.example_day_recipes
            }).render().el);
        },
        
        search : function() {
            var recipe_name = this.$el.find(".recipe_name").val();
            app.models.get_recipe_params.set({recipe_name : recipe_name});
        },

        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
        
        clear : function() {
            $(this.el).find(".recipe_name").val('');
            app.models.get_recipe_params.set(
                {
                    recipe_name : '',
                }
            );
        }

            
    });
            
    return view;
});