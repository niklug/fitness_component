define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_my_recipes.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.recipe_database;
        },
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },
        
        events: {
            "click #view_trash" : "onClickViewTrash",
            "click #new_recipe" : "onClickNewRecipe",
        },

        onClickViewTrash : function() {
            this.controller.navigate("!/trash_list", true);
        },

        onClickNewRecipe : function() {
            this.controller.navigate("!/edit_recipe/0", true);
        }
    });
            
    return view;
});