define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/event_clients',
        'views/programs/select_element',
        'views/programs/backend/event_client_item',
	'text!templates/programs/backend/form_clients.html'
], function (
        $,
        _,
        Backbone,
        app,
        Event_clients_collection, 
        Select_element_view,
        Event_client_item_view,
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
            
            this.container_el = this.$el.find("#clients_data");
            
            this.loadClientsFields();
           
            return this;
        },
        
        events : {

        },
        
        loadClientsFields : function() {
            if(app.collections.event_clients.length) {
                var self = this;
                _.each(app.collections.event_clients.models, function(model) {
                    //console.log(model);
                    self.container_el.append(new Event_client_item_view({model : model, collection : app.collections.event_clients}).render().el); 
                });
            }
        }

    });
            
    return view;
});