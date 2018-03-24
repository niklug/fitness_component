define([
    'underscore',
    'backbone',
    'app',
    'models/goals/periodization/session'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals&task=training_sessions&id=',
        model : model
    });
    
    return collection;
});