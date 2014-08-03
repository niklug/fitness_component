define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/archive_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template({'items' : this.collection.toJSON()}));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .preview" : "viewPlan",
        },

        viewPlan : function(event) {
            var id = $(event.target).attr('data-id');
            //console.log(id);
            app.controller.navigate("!/overview/" + id, true);
        },
    });
            
    return view;
});