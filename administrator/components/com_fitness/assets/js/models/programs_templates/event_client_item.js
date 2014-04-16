define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs_templates&task=pr_temp_clients&id=',
        
        defaults : {
            id : null,
            event_id : null,
            client_id : null
        },
        
        validate: function(attrs, options) {

        }
    });
    
    return model;
});