define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/client_summary/backend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

        },
        
        template:_.template(template),
            
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #overview_link" : "onClickOverview",
            "click #notifications_link" : "onClickNotifications",
            "click #message_centre_link" : "onClickMessageCentre",
            "click #trainings_link" : "onClickTrainings",
            "click #nutrition_link" : "onClickNutrition",
            "click .plan_menu_link" : "onClickNutritionMenuItem"
        },
        
        onClickNutritionMenuItem : function(event) {
            $(".plan_menu_link").removeClass("active_link");
            $(event.target).addClass("active_link");
        },

        onClickOverview : function() {
            app.controller.navigate("!/overview", true);
        },

        onClickNotifications : function() {
            app.controller.navigate("!/notifications", true);
        },

        onClickMessageCentre : function() {
            app.controller.navigate("!/message_centre", true);
        },

        onClickTrainings : function() {
            app.controller.navigate("!/trainings", true);
        },

        onClickNutrition : function() {
            app.controller.navigate("!/nutrition", true);
        },

    });
            
    return view;
});