define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.fitness_frontend_url + '&format=text&view=recipe_database&task=ingredients&id=',
  
    });
    
    return collection;
});