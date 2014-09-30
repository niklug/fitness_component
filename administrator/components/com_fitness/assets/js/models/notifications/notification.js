define([
    'underscore',
    'backbone',
    'app',
    'collections/notifications/types'
], function ( 
        _,
        Backbone,
        app,
        Types_collection
    ) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_diaries&task=notifications&id=',
        
        initialize : function(attr) {
            this.setItemData(attr);
        },
        
        defaults : {
            id : null,
            created_by : app.options.user_id,
            created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
        },
        
        validate: function(attrs, options) {
            /*
            if (!attrs.created_by || attrs.created_by == '0') {
              return 'Error: no created_by';
            }

            if (!attrs.client_id) {
              return 'No client_id';
            }
            */
        },
        
        setItemData : function(attr) {
            if(attr.db_table == 'fitness_nutrition_diary_comments') {
                this.set({template_id : '6'});
            }
            
            this.save(null, {
                success: function(model, response) {
                    console.log(model.toJSON());
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        }
    });
    
    
    
    return model;
});