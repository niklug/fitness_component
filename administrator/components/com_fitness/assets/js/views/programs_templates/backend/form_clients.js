define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs_templates/template_clients',
        'collections/programs_templates/trainer_clients',
        'models/programs_templates/client_item',
        'views/programs_templates/backend/client_item',
	'text!templates/programs_templates/backend/form_clients.html'
], function (
        $,
        _,
        Backbone,
        app,
        Template_clients_collection, 
        Trainer_clients_collection, 
        Client_item_model,
        Client_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.trainer_clients = new Trainer_clients_collection();

            app.collections.template_clients = new Template_clients_collection();
            
            var self = this;
            
            app.collections.template_clients.fetch({
                data : {item_id : this.model.get('id')},
                success : function (collection, response) {
                    self.render();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            $(this.el).html(template);
            
            this.container_el = $(this.el).find("#clients_data");
            
            this.loadClientsFields();
           
            return this;
        },
        
        events : {
            "click #add_client" : "onClickAddClient",
        },
        
        loadClientsFields : function() {
            if(app.collections.template_clients.length) {
                var self = this;
                _.each(app.collections.template_clients.models, function(model) {
                    self.addItem(model, app.collections.template_clients); 
                });
            }
        },
        
        addItem : function(model, collection) {
            this.container_el.append(new Client_item_view({model : model, item_model : this.model, collection : collection}).render().el); 
        },
        
        
        onClickAddClient : function() {
            var added_clients = $(".client_id").map(function() { return this.value; }).get();

            var trainer_id = $("#trainer_id").val();
            var self = this;
            app.collections.trainer_clients.fetch({
                data : {trainer_id : trainer_id},
                success : function (collection, response) {
                    self.$el.find("#add_client").hide();
                    
                    app.collections.clients_rest = collection;
                    
                    _.each(added_clients, function(item){
                        var model = app.collections.clients_rest.findWhere({client_id : item});
                        app.collections.clients_rest.remove(model);
                    });
                    
                    if(!app.collections.clients_rest.length) {
                        return;
                    }
 
                    var model = new Client_item_model({item_id : self.model.get('id')});
                    self.addItem(model, app.collections.clients_rest); 
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })

        }

    });
            
    return view;
});