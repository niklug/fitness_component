define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_trash_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.recipe_id = this.options.recipe_id;
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var variables = {'recipe_id' : this.recipe_id};
            var template = _.template(this.template(variables));
            this.$el.html(template);
            return this;
        },


        events: {
            "click .close_trash_form" : "onClickCloseTrashForm",
            "click .delete_recipe" : "onClickDeleteRecipe",
            "click .restore_recipe" : "onClickRestoreRecipe",
        },
        
        onClickCloseTrashForm : function(){
            this.controller.back();
        },

        onClickDeleteRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.delete_recipe(recipe_id);
        },

        onClickRestoreRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.restore_recipe(recipe_id);
        }
    });
            
    return view;
});