define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/nutrition_plan/overview.html'
], function ( $, _, Backbone, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});