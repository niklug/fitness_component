define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/nutrition_database/list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render : function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.append(template);
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

