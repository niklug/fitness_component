define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=goals&task=training_periods&id=',
        
        validate: function(attrs, options) {
            
            if (!attrs.period_focus || attrs.period_focus == '0') {
              return 'period_focus';
            }


        }
    });
    
    return model;
});