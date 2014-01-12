define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
            urlRoot : app.options.ajax_call_url + '&format=text&view=nutrition_plan&task=nutrition_plan_supplement&',
            
            defaults : {
                id : null,
                nutrition_plan_id : app.options.item_id,
                protocol_id : null,
                name : null,
                description : null,
                comments : null,
                url : null,
            },
        });
    return model;
});