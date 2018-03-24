define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs_templates/backend/list_item',
	'text!templates/programs_templates/backend/list.html'
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
            "click #sort_name" : "sort_name",
            "click #sort_created" : "sort_created",
            "click #sort_author" : "sort_author",
            "click #sort_appointment_type" : "sort_appointment_type",
            "click #sort_session_type" : "sort_session_type",
            "click #sort_session_focus" : "sort_session_focus",
            "click #sort_business_name" : "sort_business_name",
   
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .view" : "onClickView",
            "click .copy_item" : "onClickCopyItem",
            "click #select_trashed" : "onClickSelectTrashed",
            
            "click .publish_item" : "onClickPublishItem",
            "click .add_template" : "onClickAddTemplate",
            "click .search_program" : "onClickSearchProgram",
        },
        
        addItem : function(model) {
            var edit_allowed = app.controller.edit_allowed(model);
            model.set({is_owner : app.controller.is_owner(model)});
            model.set({edit_allowed : app.controller.edit_allowed(model)});
            model.set({view_allowed : app.controller.view_allowed(model)});
            if(!edit_allowed) return;
            
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        sort_name : function() {
            this.model.set({sort_by : 'a.name', order_dirrection : 'ASC'});
        },
        
        sort_created : function() {
            this.model.set({sort_by : 'a.created', order_dirrection : 'DESC'});
        },

        
        sort_author : function() {
            this.model.set({sort_by : 'created_by_name', order_dirrection : 'ASC'});
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
        
        sort_business_name : function() {
            this.model.set({sort_by : 'business_profile_name', order_dirrection : 'ASC'});
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : '-2'}, {
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
            model.save({state : '1'}, {
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
        
        onClickPublishItem : function(event) {
            var id = $(event.target).attr('data-id');
            var state = $(event.target).attr('data-state');
            
            var published = 1;
            
            if(parseInt(state) == '1') {
                published = 0;
            }
            
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : published}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickCopyItem : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_item(id);
        },

        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickAddTemplate : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_template(id);
        },
        
        onClickSearchProgram : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.search_program(id);
        }
    });
            
    return view;
});