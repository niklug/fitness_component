define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_list_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #create_menu" : "onClickCreateMenu",
        },

        onClickCreateMenu : function() {
            this.controller.navigate("!/menu_plan/0", true);
        },
    });
            
    return view;
});