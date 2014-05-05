define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/frontend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
        },
        
        el : $("#mainmenu"),

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #my_progress_link" : "onClickMyProgress",
            "click #self_assessments_link" : "onClickSelfAssessments",
            "click #assessments_link" : "onClickAssessments",
        },

        onClickMyProgress : function() {
            app.controller.navigate("!/my_progress", true);
            return false;
        },

        onClickSelfAssessments : function() {
            app.controller.navigate("!/self_assessments", true);
            return false;
        },

        onClickAssessments : function() {
            app.controller.navigate("!/assessments", true);
            return false;
        },
        
        hide : function() {
            this.$el.hide();
        },
        
        show : function() {
            this.$el.show();
        }
    });
            
    return view;
});