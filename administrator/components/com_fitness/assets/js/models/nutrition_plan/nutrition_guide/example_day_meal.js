define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_plan_example_day_meal&id=',

        defaults : {
            id : null,
            description : null,
            nutrition_plan_id : app.options.item_id,
            example_day_id : null,
            menu_id : null,
            meal_time : null,
        },

        validate: function(attrs, options) {
            if (!attrs.description) {
              return 'description';
            }
            if (!attrs.nutrition_plan_id) {
              return 'Nurtition Plan Id is not valid';
            }
            if (!attrs.example_day_id) {
              return 'error: No example_day_id';
            }
            if (!attrs.menu_id) {
              return 'error: No menu_id';
            }
            var result = false, m;
            var re = /^\s*([01]?\d|2[0-3]):?([0-5]\d)\s*$/;
            if ((m = attrs.meal_time.match(re))) {
                result = (m[1].length == 2 ? "" : "0") + m[1] + ":" + m[2];
            }
            if (!attrs.meal_time || !result) {
              return 'meal_time';
            }
        }
    });
    return model;
});