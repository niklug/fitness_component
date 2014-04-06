define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/programs/backend/event_client_item.html'
], function ( $, _, Backbone, app, Select_element_view, template ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),
        
        render : function(){
            var data = this.model.toJSON();
            //console.log(data);
            data.app = app;
            data.$ = $;
            data.appointment_id = this.options.item_model.get('title');
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.loadClientSelect();
            
            this.connectStatus();
            
            return this;
        },
        
        events : {
            "click .client_id" : "onClientSelect",
            "click .delete_event_client" : "delete",
        },
        
        loadClientSelect : function() {
            var element_disabled = '';
            
            if(this.model.get('id')) {
                element_disabled = 'disabled';
            }
            
            new Select_element_view({
                model : this.model,
                el : this.$el.find(".event_client_select"),
                collection : this.collection,
                first_option_title : '-Select-',
                class_name : 'client_id',
                id_name : '',
                model_field : 'client_id',
                element_disabled : element_disabled,
                value_field : 'client_id',
                text_field : 'name'
            }).render();
        },
        
        connectStatus : function() {
            var id = this.model.get('id');

            var status = this.model.get('status');

            var status_obj = $.status(app.options.status_options);

            this.$el.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

            status_obj.run();
        },
        
        onClientSelect : function(event) {
            var client_id = $(event.target).val();
            
            if(!parseInt(client_id)) return;
            
            this.model.set({client_id : client_id});
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    self.render().el;
                    if(app.collections.clients_rest.length - 1) {
                        $("#add_client").show();
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        delete : function(event) {
            var self = this;
            this.model.destroy({
                success: function (model) {
                    self.close();
                    $("#add_client").show();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
         
        close : function() {
            $(this.el).unbind();
            $(this.el).remove();
        },
        

    });
            
    return view;
});