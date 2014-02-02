define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.fitness_frontend_url + '&format=text&view=nutrition_diaries&task=diaries&id=',
    });
    
    return model;
});