define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=notifications&id=',
        
        defaults : {
            id : null,
            created_by : app.options.user_id,
            created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
        },
        
        validate: function(attrs, options) {
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }

            if (!attrs.client_id) {
              return 'No client_id';
            }
        }
    });
    
    
    
    return model;
});