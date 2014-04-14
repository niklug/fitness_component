define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=programs&task=event_clients&id=',
    });
    
    return collection;
});