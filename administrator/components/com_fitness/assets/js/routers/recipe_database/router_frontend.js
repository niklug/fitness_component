define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/recipe_database/recipes',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/recipe_database/frontend/menus/submenu_my_recipes',
        'views/recipe_database/frontend/recipe_database_list',
        'views/recipe_database/frontend/latest_recipes/list'
], function (
        $,
        _,
        Backbone,
        app,
        Recipes_collection,
        Get_recipe_params_model,
        Submenu_my_recipes_view,
        Recipe_database_list_view,
        Latest_recipes_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            app.collections.recipes = new Recipes_collection();
            
            app.models.get_recipe_params = new Get_recipe_params_model();
            
            app.models.get_recipe_params.bind("change", this.get_database_recipes, this);
            
            //latest recipes
            app.collections.recipes_latest = new Recipes_collection();
            app.models.get_recipe_params_latest = new Get_recipe_params_model();
            //
            
        },

        routes: {
            "": "my_recipes", 
            "!/": "my_recipes", 
            "!/my_recipes": "my_recipes",
            "!/recipe_database": "recipe_database",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        get_database_recipes : function() {
            app.collections.recipes.reset();
            app.collections.recipes.fetch({
                data : app.models.get_recipe_params.toJSON(),
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        get_recipes_latest : function() {
            app.models.get_recipe_params_latest.set({sort_by : 'created', order_dirrection : 'DESC', limit : 15});
            app.collections.recipes_latest.reset();
            app.collections.recipes_latest.fetch({
                data : app.models.get_recipe_params_latest.toJSON(),
                success: function (collection, response) {
                    $("#recipes_latest_wrapper").html(new Latest_recipes_view({collection : collection}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
        },

        my_recipes : function () {
            this.common_actions();
            $("#recipe_submenu").html(new Submenu_my_recipes_view().render().el);
            
            app.models.get_recipe_params.set({page : 1, current_page : 'my_recipes', state : 1});
            
            $("#my_recipes_link").addClass("active_link");
            $("#recipe_main_container").html(new Recipe_database_list_view({collection : app.collections.recipes}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);
            
            app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            
            this.get_recipes_latest();
        },
        
        recipe_database : function () {
            this.common_actions();
            app.models.get_recipe_params.set({page : 1,current_page : 'recipe_database', state : 1});
            $("#recipe_database_link").addClass("active_link");
            
            $("#recipe_main_container").html(new Recipe_database_list_view({collection : app.collections.recipes}).render().el);

            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);

            app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            
            this.get_recipes_latest();
        },
        
        common_actions : function() {
            $("#recipe_submenu").empty();
            $(".block").hide();
            $(".plan_menu_link").removeClass("active_link");
        },
    
    });

    return Controller;
});