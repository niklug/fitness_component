define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/frontend/menus/main_menu.html'
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
            "click #my_favourites_link" : "onClickFavourites",
            "click #my_workouts_link" : "onClickMyWorkouts",
            "click #workout_programs_link" : "onClickWorkoutPrograms",
        },

        onClickFavourites : function() {
            app.controller.navigate("!/my_favourites", true);
            return false;
        },

        onClickMyWorkouts : function() {
            app.controller.navigate("!/my_workouts", true);
            return false;
        },

        onClickWorkoutPrograms : function() {
            app.controller.navigate("!/workout_programs", true);
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