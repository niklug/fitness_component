define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/backend/list_item',
	'text!templates/programs/backend/list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
            this.status_obj = $.status(app.options.status_options);
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            
            return this;
        },
        
        events: {
            "click #sort_starttime" : "sort_starttime",
            "click #sort_trainer" : "sort_trainer",
            "click #sort_author" : "sort_author",
            "click #sort_location" : "sort_location",
            "click #sort_appointment_type" : "sort_appointment_type",
            "click #sort_session_type" : "sort_session_type",
            "click #sort_session_focus" : "sort_session_focus",
   
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .view" : "onClickView",
            "click .copy_exercise" : "onClickCopyExercise",
            "click #select_trashed" : "onClickSelectTrashed",
            
            "click .publish_event" : "onClickPublishEvent",
            "click .publish_workout" : "onClickPublishWorkout",
            
            "click .copy_item" : "onClickCopy",
            
            "click .appointment_email" : "sendAppointmentEmail",
            "click .add_template" : "onClickAddTemplate",
            
        },
        
        addItem : function(model) {
            var edit_allowed = app.controller.edit_allowed(model);

            if(!edit_allowed) return;
            
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        sort_starttime : function() {
            this.model.set({sort_by : 'a.starttime', order_dirrection : 'DESC'});
        },
        
        sort_trainer : function() {
            this.model.set({sort_by : 'trainer_name', order_dirrection : 'ASC'});
        },
        
        sort_author : function() {
            this.model.set({sort_by : 'created_by_name', order_dirrection : 'ASC'});
        },
        
        sort_location : function() {
            this.model.set({sort_by : 'location_name', order_dirrection : 'ASC'});
        },
        
        sort_appointment_type : function() {
            this.model.set({sort_by : 'appointment_name', order_dirrection : 'ASC'});
        },
        
        sort_session_type : function() {
            this.model.set({sort_by : 'session_type_name', order_dirrection : 'ASC'});
        },
        
        sort_session_focus : function() {
            this.model.set({sort_by : 'session_focus_name', order_dirrection : 'ASC'});
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);

            console.log(model.toJSON());
            var self  = this;
            model.save({published : '-2'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickRestore : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.save({published : '1'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        
        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
        },
        
        onClickPublishEvent : function(event) {
            var id = $(event.target).attr('data-id');
            var state = $(event.target).attr('data-published');
            
            var published = 1;
            
            if(parseInt(state) == '1') {
                published = 0;
            }
            
            var model = this.collection.get(id);
            var self  = this;
            model.save({published : published}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickPublishWorkout : function(event) {
            var id = $(event.target).attr('data-id');
            var state = $(event.target).attr('data-frontend_published');

            var frontend_published = 1;
            
            if(parseInt(state) == '1') {
                frontend_published = 0;
            }
            
            var model = this.collection.get(id);
            var self  = this;
            model.save({frontend_published : frontend_published}, {
                success: function (model, response) {
                    app.controller.update_list();
                    if(parseInt(frontend_published)) {
                        self.sendNotifyEmail(id);
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_item(id);
        },
        
        sendAppointmentEmail : function(event) {
            var id = $(event.target).attr('data-id');
            this.status_obj.sendEmail(id, 'Appointment');
        },
        
        sendNotifyEmail : function(id) {
            this.status_obj.sendEmail(id, 'Notify');
        },
        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickAddTemplate : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_template(id);
        }
        
    });
            
    return view;
});