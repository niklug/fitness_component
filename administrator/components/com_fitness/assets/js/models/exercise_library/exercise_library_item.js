define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=exercise_library&task=exercise_library&id=',
        
        defaults : {
            id : null,
            exercise_name : null,
            exercise_type : null,
            force_type : null,
            mechanics_type : null,
            body_part : null,
            target_muscles : null, 
            equipment : null,
            difficulty : null,
            created : null,
            created_by : null,
            status : null,
            global_business_permission : null,
            user_view_permission : null,
            my_exercise_list : null, 
            video : null,
            assessed_by : null,
            state : null
        },
        
        validate: function(attrs, options) {
            if (!attrs.exercise_name) {
              return 'exercise_name';
            }
            if (!attrs.created) {
              return 'created';
            }
            if (!attrs.created_by) {
              return 'created_by';
            }
        }
    });
    
    return model;
});