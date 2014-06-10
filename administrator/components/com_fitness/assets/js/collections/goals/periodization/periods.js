define([
    'underscore',
    'backbone',
    'app',
    'models/goals/periodization/period'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals&task=training_periods&id=',
        model : model
    });
    
    return collection;
});