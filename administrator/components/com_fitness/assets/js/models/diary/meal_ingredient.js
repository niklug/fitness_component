define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=meal_ingredients&id=',

        validate: function(attrs, options) {
            if (!attrs.nutrition_plan_id) {
              return 'No nutrition_plan_id';
            }
            
            if (!attrs.diary_id) {
              return 'No diary_id';
            }
            
            if (!attrs.meal_entry_id) {
              return 'No meal_entry_id';
            }
            
            if (!attrs.menu_id) {
              return 'No menu_id';
            }

        }
    });
    
    
    
    return model;
});