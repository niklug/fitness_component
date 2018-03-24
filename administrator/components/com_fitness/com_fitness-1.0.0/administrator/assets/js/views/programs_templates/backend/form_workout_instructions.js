define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs_templates/backend/form_workout_instructions.html'
], function (
        $,
        _,
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
           this.render();
        },

        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            $(this.el).find("#description").cleditor({width:'100%', height:150, useCSS:true})[0];
            
            if(!this.model.get('view_allowed')) {
               $(this.el).find("#description").cleditor()[0].disable(true);
            } 
            
            return this;
        },
        


    });
            
    return view;
});