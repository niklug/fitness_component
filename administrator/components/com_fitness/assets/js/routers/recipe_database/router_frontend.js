define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/recipe_database/recipes',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/recipe_database/frontend/recipe_database_list'
], function (
        $,
        _,
        Backbone,
        app,
        Recipes_collection,
        Get_recipe_params_model,
        Recipe_database_list_view
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


        },

        routes: {
            "": "my_recipes", 
            "!/": "my_recipes", 
            "!/my_recipes": "my_recipes",
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
                error: function (model, response) {
                    alert(response.responseText);
                }
            });  
        },

        my_recipes : function () {
            app.models.get_recipe_params.set({current_page : 'my_recipes', state : 1});
            $("#my_recipes_link").addClass("active_link");
            $("#recipe_main_container").html(new Recipe_database_list_view({collection : app.collections.recipes}).render().el);
        },
        
        common_actions : function() {
            $(".block").hide();
            $(".plan_menu_link").removeClass("active_link");
        },
    
    });

    return Controller;
});