define([
    'underscore',
    'backbone',
    'app',
    'models/nutrition_plan/nutrition_guide/menu_plan'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_plan_menu&',
        model: model 
    });
    
    return collection;
});