define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=goals&task=primary_goals&id=',
        
        defaults : {
            id : null,
            user_id : app.options.user_id,
            status : '1',
            state : '1'
         },
        
        validate: function(attrs, options) {
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }
            
            if (!attrs.start_date) {
              return 'start_date';
            }
            
            if (!attrs.deadline) {
              return 'deadline';
            }
            
            if (!attrs.user_id) {
              return 'Error: no user_id';
            }

        }
    });
    
    return model;
});