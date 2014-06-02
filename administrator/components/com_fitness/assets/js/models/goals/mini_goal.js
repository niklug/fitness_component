define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=goals&task=mini_goals&id=',
        
        defaults : {
            id : null,
            status : '1',
            state : '1'
         },
        
        validate: function(attrs, options) {
            
            if (!attrs.primary_goal_id || attrs.primary_goal_id == '0') {
              return 'primary_goal_id';
            }

            if (!attrs.start_date || attrs.start_date == '0') {
              return 'start_date';
            }
            
            if (!attrs.deadline || attrs.deadline == '0') {
              return 'deadline';
            }

        }
    });
    
    return model;
});