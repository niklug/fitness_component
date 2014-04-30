define([
    'underscore',
    'backbone',
    'app',
    'models/rest_data',
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=assessments&task=assessment_photos&id='
    });
    
    return collection;
});