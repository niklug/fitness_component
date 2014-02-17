define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/popular_exercises/item.html'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
        
        events: {
            "click .view_recipe" : "onClickViewRecipe",
        },

        onClickViewRecipe : function(event) {
            var id = $(event.target).attr("data-id");
            app.controller.navigate("!/recipe_database", true);
            app.controller.navigate("!/nutrition_recipe/" + id, true);
        }

    });
            
    return view;
});