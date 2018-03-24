define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=meal_ingredients&id=',

    });
    
    return collection;
});