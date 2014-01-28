define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_my_favourites_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.listenToOnce(window.app.recipe_items_model, "change:favourite_removed", this.redirectToFavourites);
            this.recipe_id = this.options.recipe_id;
            this.nutrition_plan_id = this.options.nutrition_plan_id;
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var variables = {'recipe_id' : this.recipe_id, 'nutrition_plan_id' : this.nutrition_plan_id};
            var template = _.template(this.template(variables));
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

            var recipe_id = $(event.target).attr('data-id');
            window.app.recipe_items_model.remove_favourite(recipe_id);
        },
        
        redirectToFavourites : function(){
            this.controller.navigate("!/my_favourites", true);
        },

        onClickAddDiary : function(event) {
            var id = $(event.target).attr('data-id');
            this.controller.navigate("!/add_diary/" + id, true);
        },
    });
            
    return view;
});