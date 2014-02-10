define([
    'underscore',
    'backbone',
    'app',
    'models/exercise_library/exercise_library_item',
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=exercise_library&task=exercise_library&id=',
        model : model
  
    });
    
    return collection;
});