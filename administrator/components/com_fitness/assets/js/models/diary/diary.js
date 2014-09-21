define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=diaries&id=',
        
        defaults : {
            id : null,
            nutrition_plan_id : null,
            client_id : null,
            trainer_id : null,
            goal_category_id : null,
            nutrition_focus : null,
            created : null, 
            state : 1
        },
        
        validate: function(attrs, options) {
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }
            
            if (!attrs.nutrition_plan_id) {
              return 'No nutrition_plan_id';
            }
            if (!attrs.client_id) {
              return 'No client_id';
            }
            if (!attrs.trainer_id) {
              return 'No trainer_id';
            }
            if (!attrs.goal_category_id) {
              return 'No goal_category_id';
            }
            if (!attrs.nutrition_focus) {
              return 'No nutrition_focus';
            }
            if (!attrs.created) {
              return 'No Date created';
            }
            
            if (!attrs.entry_date) {
              return 'entry_date';
            }
        }
    });
    
    
    
    return model;
});