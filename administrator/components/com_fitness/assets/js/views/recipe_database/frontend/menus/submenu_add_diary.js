define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_add_diary.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #add_diary" : "onClickAddDiary",
            "click #cancel" : "onClickCancel",
        },

        onClickAddDiary : function() {
            var number_serves = parseInt($("#number_serves").val());

            if(!number_serves) {
                $("#number_serves").addClass("red_style_border");
                return false;
            }

            var data = {};
            data.recipe_id = this.model.get('id');
            data.number_serves = number_serves;

            data.number_serves_recipe = this.model.get('number_serves');

            $.fitness_helper.add_diary(data, app);

        },

        onClickCancel : function(event) {
            this.controller.back();
        },
    });
            
    return view;
});