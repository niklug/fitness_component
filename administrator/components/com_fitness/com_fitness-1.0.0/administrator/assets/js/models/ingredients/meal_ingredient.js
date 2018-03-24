define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=meal_ingredients&id=',
        
        defaults : {
            protein : '0',
            fats : '0',
            carbs : '0',
            calories : '0',
            energy : '0',
            saturated_fat : '0',
            total_sugars : '0',
            sodium: '0',
        },
        
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
            
            if (!attrs.meal_id) {
              return 'No meal_id';
            }

        }
    });
    
    
    
    return model;
});