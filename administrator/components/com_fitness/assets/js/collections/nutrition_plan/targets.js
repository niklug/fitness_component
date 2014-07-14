define([
    'underscore',
    'backbone',
    'app',
    'models/nutrition_plan/target'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_plans&task=nutrition_plan_targets&id=',
        model: model 
    });
    
    return collection;
});