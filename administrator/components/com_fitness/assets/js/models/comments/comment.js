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
        
        validate: function(attrs, options) {
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }
            
            if (!attrs.item_id || attrs.item_id == '0') {
              return 'Error: no item_id';
            }
            
            if (!attrs.sub_item_id) {
              return 'Error: no sub_item_id';
            }
        }

    });
    
    
    
    return model;
});