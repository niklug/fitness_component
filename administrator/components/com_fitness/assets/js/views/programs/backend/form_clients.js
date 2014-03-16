define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/event_clients',
        'views/programs/select_element',
	'text!templates/programs/backend/form_clients.html'
], function (
        $,
        _,
        Backbone,
        app,
        Event_clients_collection, 
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            if( 
                app.collections.event_clients 
            ) {
                this.render();
                return;
            } 
            
            app.collections.event_clients = new Event_clients_collection();
            
            var self = this;
            $.when (
                app.collections.event_clients.fetch({
                    data : {event_id : this.model.get('id')},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })

            ).then (function(response) {
                self.render();
            })
        },

        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.loadClients();
            
            console.log(app.collections.event_clients);

            return this;
        },
        
        events : {

        },
        
        loadClients : function() {
            
        }

    });
            
    return view;
});