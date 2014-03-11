define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs&task=programs&id=',
        
        defaults : {
            id : null,
            published : 1
        },
        
        validate: function(attrs, options) {

        }
    });
    
    return model;
});