define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app, model) {
    var collection = Backbone.Collection.extend({
        initialize: function(models, options) {
            this.db_table = options.db_table;
        },
        
        url: function() {
            return app.options.ajax_call_url + '&db_table=' + this.db_table + '&format=text&view=nutrition_diaries&task=comments&id=';
        },
    });
    
    return collection;
});