define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var collection = Backbone.Collection.extend({
        url : app.options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=recipes&id=',
    });
    
    return collection;
});