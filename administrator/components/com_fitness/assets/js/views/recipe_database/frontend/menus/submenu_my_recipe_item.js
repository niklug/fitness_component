define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_my_recipe_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },
        
        el: $("#recipe_mainmenu"), 

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },


        events: {
            "click #close_recipe" : "onClickCloseRecipe",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourites" : "onClickRemoveFavourites",
            "click .trash_recipe" : "onClickTrashRecipe",
            "click .edit_recipe" : "onClickEditRecipe",
            "click .add_diary" : "onClickAddDiary",
        },

        onClickCloseRecipe : function() {
            this.controller.navigate("!/my_recipes", true);
        },

        onClickAddFavourite : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.add_favourite(recipe_id);
        },

        onClickRemoveFavourites : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.remove_favourite(recipe_id);
        },

        onClickTrashRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.trash_recipe(recipe_id);
        },

        onClickEditRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            this.controller.navigate("!/edit_recipe/" + recipe_id, true);
        },

        onClickAddDiary : function(event) {
            var id = $(event.target).attr('data-id');
            this.controller.navigate("!/add_diary/" + id, true);
        },
    });
            
    return view;
});