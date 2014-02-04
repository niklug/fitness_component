define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/recipe_database/favourite_recipe',
	'text!templates/recipe_database/frontend/menus/submenu_my_favoirites_item.html'
], function ( $, _, Backbone, app, Favourite_recipe_model, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var data  = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.favourite_recipe_model = new Favourite_recipe_model({id : this.model.get('id')})
            this.$el.html(template);
            return this;
        },

        events: {
            "click #close_recipe" : "onClickCloseRecipe",
            "click .remove_favourites" : "onClickRemoveFavourites",
            "click .add_diary" : "onClickAddDiary",
        },

        onClickCloseRecipe : function() {
            this.controller.back();
        },
        
        onClickRemoveFavourites : function(event) {
            var self = this;
            this.favourite_recipe_model.destroy({
                success: function (model) {
                    self.controller.navigate("!/my_favourites", true);
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