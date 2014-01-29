define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },
        
        el: $("#recipe_mainmenu"), 

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #my_favourites_link" : "onClickFavourites",
            "click #my_recipes_link" : "onClickMy_recipes",
            "click #recipe_database_link" : "onClickRecipe_database",
            "click #nutrition_database_link" : "onClickNutrition_database",
        },

        onClickFavourites : function() {
            this.controller.navigate("!/my_favourites", true);
            return false;
        },

        onClickMy_recipes : function() {
            this.controller.navigate("!/my_recipes", true);
            return false;
        },

        onClickRecipe_database : function() {
            this.controller.navigate("!/recipe_database", true);
            return false;
        },

        onClickNutrition_database : function() {
            this.controller.navigate("!/nutrition_database", true);
            return false;
        },
        
        hide : function() {
            this.$el.hide();
        },
        
        show : function() {
            this.$el.show();
        }
    });
            
    return view;
});