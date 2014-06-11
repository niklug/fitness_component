define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=goals&task=training_sessions&id=',
        
        defaults : {
            id : null,
            period_id : '',
            starttime : '',
            endtime : '',
            appointment_type_id : '',
            session_type : '',
            session_focus : '',
            location : '',
            pr_temp_id : ''
        },
        
        validate: function(attrs, options) {
            if (!attrs.period_id || attrs.period_id == '0') {
              return 'Error: No period_id';
            }
            
            if (!attrs.starttime || attrs.starttime == '0') {
              return 'starttime';
            }
            
            if (!attrs.endtime || attrs.endtime == '0') {
              return 'Error: No endtime';
            }
            
            if (!attrs.appointment_type_id || attrs.appointment_type_id == '0') {
              return 'appointment_type_id';
            }
            
            if (!attrs.session_type || attrs.session_type == '0') {
              return 'session_type';
            }
            
            if (!attrs.session_focus || attrs.session_focus == '0') {
              return 'session_focus';
            }
            
            if (!attrs.location || attrs.location == '0') {
              return 'location';
            }
        }
    });
    
    return model;
});