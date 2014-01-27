define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/recipe_database_list_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(data){
            var data = data;
            //console.log(data);
            data.model = this.model;
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },


        events: {
            "click .view_recipe" : "onClickViewRecipe",
            "click #copy_recipe" : "onClickCopyRecipe",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourites" : "onClickRemoveFavourites",
            "click .trash_recipe" : "onClickTrashRecipe",
            "click .delete_recipe" : "onClickDeleteRecipe",
            "click .restore_recipe" : "onClickRestoreRecipe",
            "click .add_diary" : "onClickAddDiary",
            "click .show_recipe_variations" : "onClickShowRecipeVariations",
        },

        onClickViewRecipe : function(event) {
            var id = $(event.target).attr("data-id");

            window.app.controller.navigate("!/nutrition_recipe/" + id, true);
        },

        onClickCopyRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            this.model.copy_recipe(recipe_id);
        },

        onClickAddFavourite : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            this.model.add_favourite(recipe_id);
        },


        onClickRemoveFavourites : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            this.model.remove_favourite(recipe_id);
        },

        onClickTrashRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.trash_recipe(recipe_id);
        },

        onClickDeleteRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.delete_recipe(recipe_id);
        },

        onClickRestoreRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.restore_recipe(recipe_id);
        },

        onClickAddDiary : function(event) {
            var id = $(event.target).attr('data-id');
            window.app.controller.navigate("!/add_diary/" + id, true);
        },
        onClickShowRecipeVariations : function(event) {
            var id = $(event.target).attr('data-id');

            $('.show_recipe_variations[data-id="' + id + '"]').hide();
            $('.recipe_variations[data-id="' + id + '"]').show();
        }
    });
            
    return view;
});