define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/recipe_database/favourite_recipe',
	'text!templates/recipe_database/frontend/menus/submenu_recipe_database_item.html'
], function ( $, _, Backbone, app, Favourite_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
            app.views.main_menu.hide();
        },

        template:_.template(template),
        
        render: function(){
            var data  = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            this.favourite_recipe_model = new Favourite_recipe_model({id : this.model.get('id')})
            return this;
        },

        events: {
            "click #close_recipe" : "onClickCloseRecipe",
            "click #copy_recipe" : "onClickCopyRecipe",
            "click .add_favourite" : "onClickAddFavourite",
            "click .remove_favourites" : "onClickRemoveFavourites",
            "click .add_diary" : "onClickAddDiary",
        },

        onClickCloseRecipe : function() {
            this.controller.back();
        },

        onClickCopyRecipe : function() {
            $.fitness_helper.copy_recipe(this.model.get('id'));
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
                    self.controller.navigate("!/recipe_database", true);
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
    });
            
    return view;
});