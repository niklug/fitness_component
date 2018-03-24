define([
    'underscore',
    'backbone',
    'app',
    'models/goals/mini_goal'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals&task=mini_goals&id=',
        model : model
    });
    
    return collection;
});