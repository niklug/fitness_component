define([
    'jquery',
    'underscore',
    'backbone',
    'app'
], function ($, _, Backbone, app) {
    var model = Backbone.Model.extend({
        
        initialize: function(){
            this.bind("save", this.onAddFavourites, this);
            this.bind("detroy", this.onRemoveFavourites, this);
        },
           
        urlRoot : app.options.fitness_frontend_url + '&format=text&view=recipe_database&task=favourite_recipe&id=',
        
        onAddFavourites : function() {
            var recipe_id = this.get('id');
            $(".remove_favourites[data-id='" + recipe_id + "']").show();
            $(".add_favourite[data-id='" + recipe_id + "']").hide();
        },
        
        onRemoveFavourites : function() {
            var recipe_id = this.get('id');
            $(".remove_favourites[data-id='" + recipe_id + "']").hide();
            $(".add_favourite[data-id='" + recipe_id + "']").show();
        },
    });
    
    return model;
});