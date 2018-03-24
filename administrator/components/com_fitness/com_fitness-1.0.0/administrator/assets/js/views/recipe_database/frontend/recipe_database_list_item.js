define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/recipe_database/favourite_recipe',
	'text!templates/recipe_database/frontend/recipe_database_list_item.html'
], function ( $, _, Backbone, app, Favourite_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            this.favourite_recipe_model = new Favourite_recipe_model({id : this.model.get('id')})
            
            this.controller.connectStatus(this.model, $(this.el));
            
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
            this.controller.navigate("!/nutrition_recipe/" + id, true);
        },

        onClickCopyRecipe : function(event) {
            var recipe_id = $(event.target).attr('data-id');
            $.fitness_helper.copy_recipe(recipe_id);
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
            var self = this;
            this.favourite_recipe_model.destroy({
                success: function (model) {
                    model.trigger('detroy');
                    var current_page = app.models.get_recipe_params.get('current_page');
                    if(current_page == 'my_favourites') {
                        self.close();
                    }
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
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickDeleteRecipe : function() {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    app.collections.recipes.remove(model);
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickRestoreRecipe : function() {
            var self = this;
            this.model.save({state : 1}, {
                success: function (model) {
                    self.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickAddDiary : function(event) {
            var id = $(event.target).attr('data-id');
            this.controller.navigate("!/add_diary/" + id, true);
        },
        onClickShowRecipeVariations : function(event) {
            var id = $(event.target).attr('data-id');
            $('.show_recipe_variations[data-id="' + id + '"]').hide();
            $('.recipe_variations[data-id="' + id + '"]').show();
        },
        
        close : function() {
            this.$el.fadeOut();
        }
    });
            
    return view;
});