define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=goals&task=program_templates&id='
    });
    
    return collection;
});