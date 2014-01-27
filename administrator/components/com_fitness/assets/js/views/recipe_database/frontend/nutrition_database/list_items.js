define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/nutrition_database/list_items.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize: function(){
            this.render();
        },
        
        template:_.template(template),

        render : function(){
            var ingredients = this.options.ingredients;
            //console.log(ingredients);
            var template = _.template(this.template(ingredients));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .ingredient_name" : "onClickIngredient",
        },

        onClickIngredient : function(event) {
            var id = $(event.target).attr("data-id");
            $('.description').hide();
            $('.description[data-description="' + id + '"]').show();
        }
    });
            
    return view;
});