define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
            urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_plan_menu&id=',
            
            defaults : {
                id : null,
                nutrition_plan_id : app.options.item_id,
                name : null,
                start_date : null,
                created_by : null,
                status : 4,
                assessed_by : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
                if (!attrs.name) {
                  return 'menu_name';
                }
                if (!attrs.start_date) {
                  return 'start_date';
                }
                if (!attrs.created_by) {
                  return 'Created By value is empty!';
                }
            }
        });
    return model;
});