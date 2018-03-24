define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=recipe_database&task=recipes&id=',
        
        defaults : {
            id : null,
            recipe_name : '',
            recipe_type : '',
            recipe_variation : '',
            number_serves : '',
            author : '',
            status : '1', 
            state : '1',
            trainer : '',
            instructions : '',
            business_profile_id : ''
        },
        
        validate: function(attrs, options) {
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }
            
            if (!attrs.recipe_name) {
              return 'recipe_name';
            }
            if (!attrs.recipe_type || !parseInt(attrs.recipe_type)) {
              return 'recipe_type';
            }
            if (!attrs.recipe_variation || !parseInt(attrs.recipe_variation)) {
              return 'recipe_variation';
            }
            if (!attrs.number_serves || !parseInt(attrs.number_serves)) {
              return 'number_serves';
            }
        }
    });
    
    return model;
});