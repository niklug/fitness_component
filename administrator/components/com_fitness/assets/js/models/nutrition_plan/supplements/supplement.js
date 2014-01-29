define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
            urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : app.options.item_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },
            
            validate: function(attrs, options) {
                if (!attrs.name) {
                  return 'name';
                }
                if (!attrs.nutrition_plan_id) {
                  return 'Nurtition Plan Id is not valid';
                }
                if (!attrs.protocol_id) {
                  return 'error: No Protocol Id';
                }
            }
        });
    return model;
});