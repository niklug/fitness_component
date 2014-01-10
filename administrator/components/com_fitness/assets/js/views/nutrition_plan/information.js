define([
	'jquery',
	'underscore',
	'backbone',
	'models/nutrition_plan/target',
	'text!templates/nutrition_plan/information.html'
], function ( $, _, Backbone, app, model, template ) {

    var view = Backbone.View.extend({
        render: function(){
            var template = _.template(template, model.toJSON());
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});