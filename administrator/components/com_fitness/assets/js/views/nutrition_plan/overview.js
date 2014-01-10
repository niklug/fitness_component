define([
	'jquery',
	'underscore',
	'backbone',
	'models/nutrition_plan/overview',
	'text!templates/nutrition_plan/overview.html'
], function ( $, _, Backbone, model, template ) {

    var view = Backbone.View.extend({

        render: function(){
            var template = _.template(template, model.toJSON());
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});