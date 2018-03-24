define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/latest_recipes/item.html'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            this.container_el = this.$el.find("#latest_recipes_container");
            return this;
        },
        
        events: {
            "click .view_recipe" : "onClickViewRecipe",
        },

        onClickViewRecipe : function(event) {
            var id = $(event.target).attr("data-id");
            this.controller.navigate("!/recipe_database", true);
            this.controller.navigate("!/nutrition_recipe/" + id, true);
        }

    });
            
    return view;
});