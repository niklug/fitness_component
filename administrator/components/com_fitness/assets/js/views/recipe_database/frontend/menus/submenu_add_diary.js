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
            var variables = {'recipe_id' : this.options.recipe_id, 'nutrition_plan_id' : this.options.nutrition_plan_id};
            var template = _.template(this.template(variables));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #add_diary" : "onClickAddDiary",
            "click #cancel" : "onClickCancel",
        },

        onClickAddDiary : function(event) {
            var recipe_id = $(event.target).attr("data-recipe_id");
            var number_serves = parseInt($("#number_serves").val());

            if(!number_serves) {
                $("#number_serves").addClass("red_style_border");
                return false;
            }

            var data = {};
            data.recipe_id = recipe_id;
            data.number_serves = number_serves;

            data.number_serves_recipe = this.options.number_serves_recipe;

            window.app.recipe_items_model.add_diary(data);

        },

        onClickCancel : function(event) {
            this.controller.back();
        },
    });
            
    return view;
});