define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_plans&task=nutrition_plans&id=',
        
        validate: function(attrs, options) {
            if (!attrs.client_id) {
              return 'Error: no client_id';
            }
            
            if (!attrs.trainer_id) {
              return 'Error: no trainer_id';
            }
            
            if (!attrs.active_start) {
              return 'active_start';
            }
            
            if (!attrs.active_finish) {
              return 'active_finish';
            }
            
            if (!attrs.nutrition_focus) {
              return 'nutrition_focus';
            }
        }
    });
    return model;
});