define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/backend/form_container.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            $(this.el).find("#program_form").validate();
            
            return this;
        },
    });
            
    return view;
});