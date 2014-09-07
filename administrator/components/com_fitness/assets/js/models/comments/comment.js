define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        initialize: function(data, options) {
            this.db_table = options.db_table;
        },
        
        defaults : {
            id : null,
        },
        
        urlRoot : function() {
            return app.options.ajax_call_url + '&db_table=' + this.db_table + '&format=text&view=nutrition_diaries&task=comments&id=';
        },

    });
    
    
    
    return model;
});