define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=recipe_database&task=recipe_ingredients&id=',
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
            if (!attrs.recipe_id) {
              return 'No recipe_id';
            }
        }

    });
    
    return model;
});