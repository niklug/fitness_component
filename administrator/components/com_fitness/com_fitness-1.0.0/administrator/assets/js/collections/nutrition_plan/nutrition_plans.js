define([
    'underscore',
    'backbone',
    'app',
    'models/nutrition_plan/nutrition_plan'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals_periods&task=nutrition_plan&',
        model: model 
    });
    
    return collection;
});