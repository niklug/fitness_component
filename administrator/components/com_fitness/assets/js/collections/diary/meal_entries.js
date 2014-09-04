define([
    'underscore',
    'backbone',
    'app',
    'models/diary/meal_entry'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=meal_entries&id=',
        
        comparator: function(model) {
          return new Date(Date.parse(model.get('meal_time'))).getTime();
        },
        
        model : model
    });
    
    return collection;
});