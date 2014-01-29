define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.fitness_frontend_url + '&format=text&view=recipe_database&task=recipes&id=',
        
        defaults : {
            id : null,
            recipe_name : null,
            recipe_type : null,
            recipe_variation : null,
            number_serves : null,
            author : null,
            status : null, 
            trainer : null,
            instructions : null
        },
        
        validate: function(attrs, options) {
            if (!attrs.recipe_name) {
              return 'recipe_name';
            }
            if (!attrs.recipe_type) {
              return 'recipe_type';
            }
            if (!attrs.recipe_variation) {
              return 'recipe_variation';
            }
            if (!attrs.number_serves || !parseInt(attrs.number_serves)) {
              return 'number_serves';
            }
        }
    });
    
    return model;
});