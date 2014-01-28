define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var collection = Backbone.Collection.extend({
        url : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_database_categories&id=',
    });
    
    return collection;
});