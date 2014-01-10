define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'routers/nutrition_plan/router',
        'collections/nutrition_plan/nutrition_plans',
	'models/nutrition_plan/overview',
	'text!templates/nutrition_plan/archive_list.html'
], function ( $, _, Backbone, app, controller, collection, model, template ) {

    var view = Backbone.View.extend({

        render: function(){
            var template = _.template(template, {'items' : collection.toJSON()});
            this.$el.html(template);
            return this;
        },

        events: {
            "click .preview" : "viewPlan",
        },

        viewPlan : function(event) {
            var id = $(event.target).attr('data-id');
            model.set({id : id});
            $("#close_tab").show();
            controller.navigate("!/overview", true);
        },
    });
            
    return view;
});