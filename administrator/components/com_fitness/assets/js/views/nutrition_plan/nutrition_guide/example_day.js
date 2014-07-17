define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/example_day.html',
        'jquery.timepicker'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            $(this.el).html(this.template({ }));
            return this;
        },

        events:{
            "click .add_recipe": "add_recipe"
        },
    });
            
    return view;
});