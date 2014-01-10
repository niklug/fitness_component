define([
	'jquery',
	'underscore',
	'backbone',
        'routers/nutrition_plan/router',
	'text!templates/nutrition_plan/main_menu.html'
], function ( $, _, Backbone, app, controller, template ) {

    var view = Backbone.View.extend({
            
        el: $("#plan_menu"), 

        initialize: function(){
            this.render();
        },

        render: function(){
            this.loadTemplate();
        },

        events: {
            "click #overview_link" : "onClickOverview",
            "click #targets_link" : "onClickTargets",
            "click #macronutrients_link" : "onClickMacronutrients",
            "click #supplements_link" : "onClickSupplements",
            "click #nutrition_guide_link" : "onClickNutrition_guide",
            "click #information_link" : "onClickInformation",
            "click #archive_focus_link" : "onClickArchive_focus",
            "click #close_tab" : "onClickClose",
        },

        loadTemplate : function(variables, target) {
            var template = _.template( template, variables );
            this.$el.html(template);
        },

        onClickOverview : function() {
            controller.navigate("!/overview", true);
        },

        onClickTargets : function() {
            controller.navigate("!/targets", true);
        },

        onClickMacronutrients : function() {
            controller.navigate("!/macronutrients", true);
        },

        onClickSupplements : function() {
            controller.navigate("!/supplements", true);
        },

        onClickNutrition_guide : function() {
            controller.navigate("!/nutrition_guide", true);
        },

        onClickInformation : function() {
            controller.navigate("!/information", true);
        },

        onClickArchive_focus : function() {
            controller.navigate("!/archive", true);
        },

        onClickClose : function() {
            controller.navigate("!/close", true);
        }

    });
            
    return view;
});