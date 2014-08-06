define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=meal_entries&id=',

        validate: function(attrs, options) {
            if (!attrs.nutrition_plan_id) {
              return 'No nutrition_plan_id';
            }
            
            if (!attrs.diary_id) {
              return 'No diary_id';
            }
            
            var result = false, m;
            var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
            if ((m = attrs.meal_time.match(re))) {
                result = (m[1].length == 2 ? "" : "0") + m[1] + ":" + m[2];
            }
            if (!attrs.meal_time || !result) {
              return 'meal_time';
            }
            
            if (!attrs.water) {
              return 'water';
            }
            
        }
    });
    
    
    
    return model;
});