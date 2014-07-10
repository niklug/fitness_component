define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/client_trainers_block.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            var data = {item : this.model.toJSON()};
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});