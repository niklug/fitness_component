define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/recipe_database/favourite_recipe',
	'text!templates/recipe_database/frontend/menus/submenu_my_recipe_item.html'
], function ( $, _, Backbone, app, Favourite_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
            app.views.main_menu.hide();
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.favourite_recipe_model = new Favourite_recipe_model({id : this.model.get('id')})
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
            this.favourite_recipe_model.save(null, {
                success: function (model) {
                    model.trigger('save');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });

        },

        onClickRemoveFavourites : function(event) {
            this.favourite_recipe_model.destroy({
                success: function (model) {
                    model.trigger('detroy');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickTrashRecipe : function() {
            var self = this;
            this.model.save({state : '-2'}, {
                success: function (model) {
                    self.controller.navigate("!/my_recipes", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
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