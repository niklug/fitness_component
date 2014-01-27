define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/nutrition_database/item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),

        render : function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "submit #add_ingredient_form" : "onSubmit",
        },

        onSubmit : function(event) {
            event.preventDefault();
            window.app.nutrition_database_model.saveIngredient();
            return false;
        }
    });
            
    return view;
});