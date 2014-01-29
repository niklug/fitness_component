define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/recipe_database/favourite_recipe',
	'text!templates/recipe_database/frontend/menus/submenu_trash_item.html'
], function ( $, _, Backbone, app, Favourite_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            this.favourite_recipe_model = new Favourite_recipe_model({id : this.model.get('id')})
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

        onClickDeleteRecipe : function() {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.controller.navigate("!/trash_list", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickRestoreRecipe : function(event) {
            var self = this;
            this.model.save({state : 1}, {
                success: function (model) {
                    self.controller.navigate("!/trash_list", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
    });
            
    return view;
});