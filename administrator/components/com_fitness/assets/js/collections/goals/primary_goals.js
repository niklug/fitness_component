define([
    'underscore',
    'backbone',
    'app',
    'models/goals/primary_goal'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals&task=primary_goals&id=',
        model : model
    });
    
    return collection;
});