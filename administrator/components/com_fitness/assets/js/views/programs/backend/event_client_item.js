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
            $(this.el).html(template);
            
            this.loadClientSelect();
            
            this.connectStatus();
            
            return this;
        },
        
        events : {
            "change .client_id" : "onClientSelect",
            "click .delete_event_client" : "delete",
            "click .send_workout_email" : "onClickSendEmail",
            "click .send_to_client" : "onClickSendToClient",
            "click .pdf_button" : "onClickPdf",
        },
        
        connectStatus : function(model) {
            var id = this.model.get('id');

            var status = this.model.get('status');

            var status_obj = $.status(app.options.status_options);
             
            $(this.el).find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

            status_obj.run();
        },
        
        loadClientSelect : function() {
            var element_disabled = '';
            
            if(this.model.get('id')) {
                element_disabled = 'disabled';
            }
            
            new Select_element_view({
                model : this.model,
                el : $(this.el).find(".event_client_select"),
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

        onClientSelect : function(event) {
            var client_id = $(event.target).val();

            var group_clients_data = app.models.item.get('group_clients_data');
            
            if(!parseInt(client_id)) return;
            
            this.model.set({client_id : client_id});
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    group_clients_data.push(model.toJSON());
                    
                    app.models.item.set({group_clients_data : group_clients_data});
                    
                    //console.log(app.models.item.get('group_clients_data'));
                    
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
            var group_clients_data = app.models.item.get('group_clients_data');
            var self = this;
            this.model.destroy({
                success: function (model) {
                    var id = model.get('id');
                    
                    group_clients_data = _.without(group_clients_data, _.findWhere(group_clients_data, {id : id}));
                    
                    app.models.item.set({group_clients_data : group_clients_data});
                    
                    //console.log(app.models.item.get('group_clients_data'));
                    
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
        
        onClickSendEmail :function(event) {
            var id = $(event.target).attr('data-id');
            var client_id = this.model.get('client_id');
            app.controller.sendWorkoutEmail(id, client_id, 'Workout', false);
        },
        
        onClickSendToClient : function(event) {
            var id = $(event.target).attr('data-id');
            var client_id = this.model.get('client_id');
            app.controller.sendWorkoutEmail(id, client_id, 'Workout', true);
        },
        
        onClickPdf : function(event) {
            var id = $(event.target).attr('data-id');
            var client_id = this.model.get('client_id');
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_workout&event_id=' + id + '&client_id=' + client_id;
            $.fitness_helper.printPage(htmlPage);
        },

    });
            
    return view;
});