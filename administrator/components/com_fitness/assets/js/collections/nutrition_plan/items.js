define([
    'underscore',
    'backbone',
    'app',
    'models/nutrition_plan/item'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_plans&task=nutrition_plans&',
        model: model 
    });
    
    return collection;
});