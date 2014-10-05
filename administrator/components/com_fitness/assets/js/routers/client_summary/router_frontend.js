define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/client_summary/backend/menus/main_menu',
        'views/client_summary/frontend/notifications/list'
], function (
        $,
        _,
        Backbone,
        app,
        Main_menu_view,
        Notifications_list_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            //unique id
            app.getUniqueId = function() {
                return new Date().getUTCMilliseconds();
            }
            
            app.options.client_id = localStorage.getItem('client_id');

            this.loadMainMenu();
        },

        routes: {
            "" : "notifications", 
            "!/notifications" : "notifications", 
        },
        
        notifications : function() {
            this.common_actions();
            $("#notifications_link").addClass("active_link");
            
            new Notifications_list_view({el : $("#main_container")});
        },
        
        loadMainMenu : function() {
            $("#header_wrapper").html(new Main_menu_view({}).render().el);
        },
        
        common_actions : function() {
            $(".block").hide();
            $(".plan_menu_link").removeClass("active_link");
        },
        
    });

    return Controller;
});