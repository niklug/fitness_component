define([
    'underscore',
    'backbone',
    'app'
], function ( _, Backbone, app) {
    var model = Backbone.Model.extend({
        urlRoot : app.options.ajax_call_url + '&format=text&view=programs&task=event_exercises&id=',
        
        defaults : {
            id : null,
            event_id : null,
            order : 0,
            sequence : null,
            title : null,
            speed : null,
            weight : null,
            reps : null,
            time : null,
            sets : null,
            rest : null,
            comments : null,
            video_id : null
        },
    });
    
    return model;
});