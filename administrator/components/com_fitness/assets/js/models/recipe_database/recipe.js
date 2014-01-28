define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        url : app.options.fitness_frontend_url + '&format=text&view=nutrition_plan&task=recipes',
    });
    
    return model;
});