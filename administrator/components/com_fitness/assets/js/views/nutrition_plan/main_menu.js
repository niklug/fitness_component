define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        el: $("#plan_menu"), 

        initialize: function(){
            this.render();
            this.controller = app.routers.nutrition_plan;
        },

        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
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

        onClickOverview : function() {
            this.controller.navigate("!/overview", true);
        },

        onClickTargets : function() {
            this.controller.navigate("!/targets", true);
        },

        onClickMacronutrients : function() {
            this.controller.navigate("!/macronutrients", true);
        },

        onClickSupplements : function() {
            this.controller.navigate("!/supplements", true);
        },

        onClickNutrition_guide : function() {
            this.controller.navigate("!/nutrition_guide", true);
        },

        onClickInformation : function() {
            this.controller.navigate("!/information", true);
        },

        onClickArchive_focus : function() {
            this.controller.navigate("!/archive", true);
        },

        onClickClose : function() {
            this.controller.navigate("!/close", true);
        }

    });
            
    return view;
});