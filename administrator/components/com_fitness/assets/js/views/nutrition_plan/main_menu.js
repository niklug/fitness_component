define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.id = this.options.nutrition_plan_id;
        },

        render: function(){
            var data = {};
            data.app = app;
            var template = _.template(this.template(data));
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
            "click #back_to_diary_link" : "onClickBackToDiary",
        },

        onClickOverview : function() {
            app.controller.navigate("!/overview/" + this.id, true);
        },

        onClickTargets : function() {
            app.controller.navigate("!/targets/" + this.id, true);
        },

        onClickMacronutrients : function() {
            app.controller.navigate("!/macronutrients/" + this.id, true);
        },

        onClickSupplements : function() {
            app.controller.navigate("!/supplements/" + this.id, true);
        },

        onClickNutrition_guide : function() {
            var id = app.models.nutrition_plan.get('id');
            app.controller.navigate("!/nutrition_guide/" + id, true);
        },

        onClickInformation : function() {
            app.controller.navigate("!/information/" + this.id, true);
        },

        onClickArchive_focus : function() {
            app.controller.navigate("!/archive/" + this.id, true);
        },

        onClickClose : function() {
            app.controller.navigate("!/close", true);
        },
        
        onClickBackToDiary : function() {
            window.location = decodeURIComponent(app.options.add_diary_options.back_url);
        },

    });
            
    return view;
});