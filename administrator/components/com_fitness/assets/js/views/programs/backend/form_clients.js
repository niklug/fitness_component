define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/event_clients',
        'collections/programs/trainer_clients',
        'models/programs/event_client_item',
        'views/programs/backend/event_client_item',
	'text!templates/programs/backend/form_clients.html'
], function (
        $,
        _,
        Backbone,
        app,
        Event_clients_collection, 
        Trainer_clients_collection, 
        Event_client_item_model,
        Event_client_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.trainer_clients = new Trainer_clients_collection();
            
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
            "click #add_client" : "onClickAddClient",
        },
        
        loadClientsFields : function() {
            if(app.collections.event_clients.length) {
                var self = this;
                _.each(app.collections.event_clients.models, function(model) {
                    self.addItem(model, app.collections.event_clients); 
                });
            }
        },
        
        addItem : function(model, collection) {
            this.container_el.append(new Event_client_item_view({model : model, collection : collection}).render().el); 
        },
        
        onClickAddClient : function() {
            var added_clients = $(".client_id").map(function() { return this.value; }).get();

            var trainer_id = $("#trainer_id").val();
            var self = this;
            app.collections.trainer_clients.fetch({
                data : {trainer_id : trainer_id},
                success : function (collection, response) {
                    
                    _.each(added_clients, function(item){
                        var model = collection.findWhere({client_id : item});
                        collection.remove(model);
                    });
                    
                    if(!collection.length) {return;}
 
                    var model = new Event_client_item_model({event_id : self.model.get('id')});
                    self.addItem(model, collection); 
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })

        }

    });
            
    return view;
});