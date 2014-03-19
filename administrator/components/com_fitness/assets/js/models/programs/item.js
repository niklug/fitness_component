define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs&task=programs&id=',
        
        defaults : {
            id : null,
            title : null,
            description : null,
            comments : null,
            session_type : null,
            session_focus : null,
            starttime : null,
            endtime : null,
            location : null,
            client_id : null,
            trainer_id : null,
            frontend_published : null,
            owner : app.options.user_id,
            business_profile_id : app.options.business_profile_id,
            status : '1',
            published : '1',
            calid : '0',
            auto_publish_workout : null,
            auto_publish_event : null
        },
        
        validate: function(attrs, options) {
            if (!attrs.title || attrs.title == '0') {
              return 'title';
            }
            
            if (!attrs.session_type || attrs.session_type == '0') {
              return 'session_type';
            }
            
            if (!attrs.session_focus || attrs.session_focus == '0') {
              return 'session_focus';
            }
            
            if (!attrs.starttime || attrs.starttime == '0') {
              return 'starttime';
            }
            
            if (!attrs.endtime || attrs.endtime == '0') {
              return 'endtime';
            }
            
            if (!attrs.location || attrs.location == '0') {
              return 'location';
            }
            
            if (!attrs.owner) {
              return 'Error: no owner id';
            }
        }
    });
    
    return model;
});