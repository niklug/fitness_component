define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.fitness_frontend_url + '&format=text&view=goals_periods&task=nutrition_plan&',
        defaults : {
            id : app.options.item_id,
            client_id : app.options.client_id
        },
    });
    
    return model;
});