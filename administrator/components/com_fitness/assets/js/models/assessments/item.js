define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs&task=programs&id=',
        
        defaults : {
            id : null,
            title : '',
            description : '',
            comments : '',
            session_type : '',
            session_focus : '',
            starttime : '',
            endtime : '',
            location : '',
            client_id : '',
            trainer_id : '',
            frontend_published : '0',
            owner : app.options.user_id,
            business_profile_id : app.options.business_profile_id,
            published : '1',
            calid : '0',
            auto_publish_workout : '',
            auto_publish_event : '',
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
     
            if (new Date(Date.parse(attrs.endtime)).getTime() <=  new Date(Date.parse(attrs.starttime)).getTime()) {
              return "end_date_time";
            }
            
            if (!attrs.owner) {
              return 'Error: no owner id';
            }
        }
    });
    
    return model;
});