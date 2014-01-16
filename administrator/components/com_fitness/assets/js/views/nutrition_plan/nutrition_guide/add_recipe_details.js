define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/add_recipe_details.html',
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render : function(){
            $(this.el).html(this.template(this.model));
            return this;
        },
    });
            
    return view;
});