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
            equipment_type : null,
            difficulty : null,
            created : null,
            created_by : null,
            created_by_name : null,
            status : '1',
            global_business_permissions : null,
            user_view_permission : null,
            my_exercise_clients : null, 
            my_exercise_clients_names : null,
            video : null,
            state : 1,
            user_group_name : null,
            assessed_by_name : null,
            show_my_exercise : null,
            business_profiles : null
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