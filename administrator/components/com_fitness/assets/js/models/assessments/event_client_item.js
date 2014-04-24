define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs&task=event_clients&id=',
        
        defaults : {
            id : null,
            event_id : null,
            client_id : null,
            status : '1'
        },
        
        validate: function(attrs, options) {

        }
    });
    
    return model;
});