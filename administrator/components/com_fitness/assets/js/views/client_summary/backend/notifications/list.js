define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/notifications/notifications',
        'collections/notifications/types',
        'models/notifications/request_params_notifications',
        'views/client_summary/backend/notifications/list_item',
	'text!templates/client_summary/backend/notifications/list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Notifications_collection,
        Template_types_collection,
        Request_params_notifications_model,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.notifications = new Notifications_collection();
            
            app.collections.notifications.bind("add", this.addItem, this);
            app.collections.notifications.bind("reset", this.clearItems, this);
            
            app.collections.notification_types = Template_types_collection;
            
            app.models.request_params_notifications = new Request_params_notifications_model();
                
            app.models.request_params_notifications.bind("change", this.get_items, this);

            this.item_views = [];
            
            this.render();
            
            app.models.request_params_notifications.set({page : 1,  uid : app.getUniqueId()});
            
            //setInterval(this.runList, 60000);
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            this.onRender();

            return this;
        },
        
        events: {
            "click #sort_created" : "sort_created",
            "click #sort_readed" : "sort_readed",
    
            "click .view" : "onClickView",
            
            "click #select_all_notifications" : "onClickSelectAll",
            "click #delete_selected_notifications" : "onClickDeleteSelected",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
                self.connectPagination();
            });
        },
        
        runList : function() {
            console.log(app.getUniqueId());
            app.models.request_params_notifications.set({uid : app.getUniqueId()});
        },
        
        connectPagination : function() {
            app.models.notifications_pagination = $.backbone_pagination({el : $(this.el).find(".pagination_container")});
            
            var self = this;
            app.collections.notifications.once("add", function(model) {
                app.models.notifications_pagination.set({'items_total' : model.get('items_total')});
            });
            
            if(app.collections.notifications.models.length){
                app.models.notifications_pagination.set({'items_total' : app.collections.notifications.models[0].get('items_total')});
            }

            app.models.notifications_pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.notifications_pagination.bind("change:items_number", this.set_params_model, this);
            
        },
        
        set_params_model : function() {
            app.collections.notifications.reset();
            app.models.request_params_notifications.set({"page" : app.models.notifications_pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
            
        
        get_items : function() {
            var params = app.models.request_params_notifications.toJSON();
            app.collections.notifications.reset();
            app.collections.notifications.fetch({
                data : params,
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        loadItems : function() {
            var self = this;
            _.each(app.collections.notifications.models, function(model) {
                self.addItem(model);
            });
        },
        
        addItem : function(model) {
            //console.log(model.toJSON());
            //var template_id = this.model.get('template_id');
            //var model = app.collections.notification_types.get(template_id);
            
            var item_view = new List_item_view({model : model, collection : app.collections.notification_types});
            
            this.item_views[model.get('id')] = item_view;
            
            this.container_el.append(item_view.render().el); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        sort_created : function() {
            app.models.request_params_notifications.set({sort_by : 'a.created', order_dirrection : 'DESC'});
        },
        
        sort_readed : function() {
            app.models.request_params_notifications.set({sort_by : 'a.readed', order_dirrection : 'ASC'});
        },
       
        onClickSelectAll : function(event) {
            $(".item_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".item_checkbox").prop("checked", true);
            }
        },

        onClickDeleteSelected : function() {
            var selected = new Array();
            $('.item_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.deleteItem(item);
                });
            }
            $("#select_all_notifications").prop("checked", false);
        },

        deleteItem : function(id) {
            var model = app.collections.notifications.get(id);
            var self = this;
            model.destroy({
                success: function (model, response) {
                    var view = self.item_views[model.get('id')];
                    view.close();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

    });
            
    return view;
});