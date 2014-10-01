define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/notifications/notifications',
        'views/client_summary/backend/notifications/list_item',
	'text!templates/client_summary/backend/notifications/list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Notifications_collection,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.notifications = new Notifications_collection();
            
            app.collections.notifications.bind("add", this.addItem, this);
            app.collections.notifications.bind("reset", this.clearItems, this);
            
            this.item_views = [];
            
            this.render();
            
            this.get_items();
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
            "click #sort_client_name" : "sort_client_name",
            "click #sort_trainer" : "sort_trainer",
            "click #sort_active_start" : "sort_active_start",
            "click #sort_active_finish" : "sort_active_finish",
            "click #sort_active_plan" : "sort_active_plan",
            "click #sort_force_active" : "sort_force_active",
            "click #sort_primary_goal" : "sort_primary_goal",
            "click #sort_mini_goal" : "sort_mini_goal",
            "click #sort_nutrition_focus" : "sort_nutrition_focus",
            "click #sort_state" : "sort_state",

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
        
        connectPagination : function() {
            app.models.notifications_pagination = $.backbone_pagination({el : $(this.el).find(".pagination_container")});
            
            var self = this;
            app.collections.notifications.once("add", function(model) {
                app.models.notifications_pagination.set({'items_total' : model.get('items_total')});
            });
            
            if(app.collections.notifications.models.length){
                app.models.notifications_pagination.set({'items_total' : app.collections.notifications.models[0].get('items_total')});
            }

            app.models.notifications_pagination.bind("change:currentPage", this.get_items, this);

            app.models.notifications_pagination.bind("change:items_number", this.get_items, this);
            
        },
        
        get_items : function() {
            var params = {};
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
            
            var item_view = new List_item_view({model : model});
            
            this.item_views[model.get('id')] = item_view;
            
            this.container_el.append(item_view.render().el); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        sort_client_name : function() {
            this.model.set({sort_by : 'client_name', order_dirrection : 'ASC'});
        },
        
        sort_trainer : function() {
            this.model.set({sort_by : 'trainer_name', order_dirrection : 'ASC'});
        },
        
        sort_active_start : function() {
            this.model.set({sort_by : 'a.active_start', order_dirrection : 'DESC'});
        },
        
        sort_active_finish : function() {
            this.model.set({sort_by : 'active_finish', order_dirrection : 'DESC'});
        },
        
        sort_active_plan : function() {
            this.model.set({sort_by : 'a.active_plan', order_dirrection : 'DESC'});
        },
        
        sort_force_active : function() {
            this.model.set({sort_by : 'a.force_active', order_dirrection : 'DESC'});
        },
        
        sort_primary_goal : function() {
            this.model.set({sort_by : 'primary_goal_name', order_dirrection : 'ASC'});
        },
        
        sort_mini_goal : function() {
            this.model.set({sort_by : 'mini_goal_name', order_dirrection : 'ASC'});
        },
        
        sort_nutrition_focus : function() {
            this.model.set({sort_by : 'nutrition_focus_name', order_dirrection : 'ASC'});
        },
        
        sort_state : function() {
            this.model.set({sort_by : 'a.state', order_dirrection : 'DESC'});
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