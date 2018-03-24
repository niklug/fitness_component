define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        el : $("#exercise_mainmenu"),

        template:_.template(template),
        
        render: function(){
            var data = {};
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #my_favourites_link" : "onClickFavourites",
            "click #my_exercises_link" : "onClickMyExercises",
            "click #exercise_database_link" : "onClickExerciseDatabase",
            "click #back_program_link" : "onClickBackProgram",
        },

        onClickFavourites : function() {
            app.controller.navigate("!/my_favourites", true);
            return false;
        },

        onClickMyExercises : function() {
            app.controller.navigate("!/my_exercises", true);
            return false;
        },

        onClickExerciseDatabase : function() {
            app.controller.navigate("!/exercise_database", true);
            return false;
        },
        
        hide : function() {
            this.$el.hide();
        },
        
        show : function() {
            this.$el.show();
        },
        
        onClickBackProgram : function() {
            app.controller.route_program();
        }
    });
            
    return view;
});