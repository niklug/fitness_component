define([
    'underscore',
    'backbone',
    'app',
    'models/programs/exercises/item',
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=programs&task=event_exercises&id=',
        model : model
    });
    
    return collection;
});