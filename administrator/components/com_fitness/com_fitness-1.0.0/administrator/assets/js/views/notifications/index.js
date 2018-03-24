define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/notifications/notifications',
        'models/notifications/notification',
	'text!templates/notifications/index.html'
        
], function (
        $,
        _,
        Backbone,
        app,
        Notifications_collection,
        Notifications_model,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
           
        },
        
        template : _.template(template),

        render : function () {
            var data = {};
            data.$ = $;
            $(this.el).html(this.template(data));
      
            return this;
        },

     
    });
            
    return view;

});