define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs_templates/backend/form_container.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});