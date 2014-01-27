define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_edit_recipe.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var variables = {'recipe_id' : this.options.recipe_id};
            var template = _.template(this.template(variables));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function(event) {
            var recipe_id = $(event.target).attr('data-id');

            this.stopListening(window.app.recipe_items_model, "change:recipe_saved");

            window.app.recipe_items_model.set({'recipe_saved' : '0'});

            this.listenToOnce(window.app.recipe_items_model, "change:recipe_saved", this.load_edit_recipe);

            window.app.recipe_items_model.save_recipe(recipe_id);

        },

        onClickSaveClose : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.save_recipe(recipe_id);
            window.app.Views.edit_recipe_container.close();
            window.app.recipe_items_model.set({current_page : 'my_recipes'});
            if(parseInt(recipe_id)) {
                this.controller.navigate("!/nutrition_recipe/" + recipe_id, true);
            } else {
                this.controller.navigate("!/my_recipes", true);
            }
        },

        onClickCancel : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.Views.edit_recipe_container.close();
            window.app.recipe_items_model.set({current_page : 'my_recipes'});

            if(parseInt(recipe_id)) {
               this.controller.navigate("!/nutrition_recipe/" + recipe_id, true);
            } else {
                this.controller.navigate("!/my_recipes", true);
            }
        },

        load_edit_recipe : function() {
            window.app.Views.edit_recipe_container.close();
            var recipe_id = window.app.recipe_items_model.get('recipe_saved');
            this.controller.navigate("!/edit_recipe/" + recipe_id, true);
        }
    });
            
    return view;
});