define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs_templates&task=programs_templates&id=',
        
        defaults : {
            id : null,
            name : '',
            appointment_id : '',
            session_type : '',
            session_focus : '',
            description : '',
            created_by : app.options.user_id,
            state : '1',
            business_profile_id : app.options.business_profile_id,
            trainer_id : '',
            created : '',
            access : ''
        },
        
        validate: function(attrs, options) {
            if (!attrs.name || attrs.name == '0') {
              return 'name';
            }
            
            if (!attrs.appointment_id || attrs.appointment_id == '0') {
              return 'appointment_id';
            }
            
            if (!attrs.session_type || attrs.session_type == '0') {
              return 'session_type';
            }
            
            if (!attrs.session_focus || attrs.session_focus == '0') {
              return 'session_focus';
            }

        }
    });
    
    return model;
});