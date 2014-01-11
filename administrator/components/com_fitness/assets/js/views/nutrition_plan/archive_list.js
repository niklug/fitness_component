define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'routers/nutrition_plan/router',
	'text!templates/nutrition_plan/archive_list.html'
], function ( $, _, Backbone, app, controller, template ) {

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
            this.model.set({id : id});
            $("#close_tab").show();
            console.log(app.routers.nutrition_plan);
            app.routers.nutrition_plan.navigate("!/overview", true);
        },
    });
            
    return view;
});