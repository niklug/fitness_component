define([
    'jquery',
    'underscore',
    'backbone',
    'app'
], function ($, _, Backbone, app) {
    var model = Backbone.Model.extend({
        
        initialize: function(){
            this.bind("save", this.onAddFavourite, this);
            this.bind("detroy", this.onRemoveFavourite, this);
        },
           
        urlRoot : app.options.fitness_frontend_url + '&format=text&view=programs&task=favourite_event&id=',
        
        onAddFavourite : function() {
            var recipe_id = this.get('id');
            $(".remove_favourite[data-id='" + recipe_id + "']").show();
            $(".add_favourite[data-id='" + recipe_id + "']").hide();
        },
        
        onRemoveFavourite : function() {
            var recipe_id = this.get('id');
            $(".remove_favourite[data-id='" + recipe_id + "']").hide();
            $(".add_favourite[data-id='" + recipe_id + "']").show();
        },
    });
    
    return model;
});