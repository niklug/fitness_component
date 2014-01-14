define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            var template = _.template(this.template({items : this.collection.toJSON(), $ : $}));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .preview" : "onClickPreview",
        },

        onClickPreview : function(event) {
            var id = $(event.target).attr('data-id');
            this.controller.navigate("!/menu_plan/" + id, true);
        },
    });
            
    return view;
});