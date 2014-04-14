define([
    'underscore',
    'backbone',
    'app',
    'models/programs_templates/item',
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=programs_templates&task=programs_templates&id=',
        model : model
  
    });
    
    return collection;
});