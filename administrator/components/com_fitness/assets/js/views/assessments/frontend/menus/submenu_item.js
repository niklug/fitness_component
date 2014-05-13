define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/assessments/item',
        'models/assessments/event_client_item',
	'text!templates/assessments/frontend/menus/submenu_item.html'
], function ( $, _, Backbone, app, Item_model, Event_client_item_model, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data  = this.options.request_params_model.toJSON();
            data.app = app;
            _.extend(data, this.model.toJSON());
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .close_item" : "onClickClose",
            "click .edit_item" : "onClickEditItem",
            "click .trash_item" : "onClickTrashItem",
            "click .copy_item" : "onClickCopy",
            "click .submit_to_trainer" : "onClickSubmit",
        },

        onClickClose : function() {
            var current_page = this.options.request_params_model.get('current_page');
            if(!current_page) {
                current_page = 'my_progress';
            }
            app.controller.navigate("!/" + current_page, true);
        },


        
        onClickTrashItem: function(event) {
            var id = $(event.target).attr('data-id');
            var model = app.collections.items.get(id);
            var self  = this;
            model.save({published : '-2'}, {
                success: function (model, response) {
                    app.controller.navigate("!/my_progress", true);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickEditItem : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            this.copy_item(id);
        },
        
        copy_item : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'Programs';
            var task = 'copyEvent';
            var table = '';
            data.id = id;
            data.client_id = app.options.user_id;
            $.AjaxCall(data, url, view, task, table, function(output){
                
            });
        },
        
        onClickSubmit : function(event) {
            var id = $(event.target).attr('data-id');
            app.models.item = new Item_model({id : id});
            var self = this;
            app.models.item.fetch({
                wait : true,
                success: function (model, response) {
                    var client_item_id = model.get('client_item_id');
                    var event_client_item_model = new Event_client_item_model({id : client_item_id});
                    
                    event_client_item_model.save({status : '10'}, {
                        success: function (model, response) {
                            var id = model.get('id');
                            self.sendAssessingEmail(id);
                            
                            var current_page = app.models.request_params.get('current_page');
                            
                            if(!current_page) {
                                current_page = 'my_progress';
                            }
                            
                            app.controller.navigate("!/" + current_page, true);
                            
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        sendAssessingEmail : function(id) {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = id;
            data.view = 'Programs';
            data.method = 'AsAssessing';
            $.fitness_helper.sendEmail(data);
        },
    });
            
    return view;
});