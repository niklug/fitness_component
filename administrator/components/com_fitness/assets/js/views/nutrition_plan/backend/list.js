define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/list_item',
	'text!templates/nutrition_plan/backend/list.html'
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
            
            "click #select_all_checkbox" : "onClickSelectAll",
            
            "click .publish" : "onClickPublish",
            "click .email_button" : "onClickSendEmail",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadItems();
            });
        },
        
        loadItems : function() {
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
        },
        
        addItem : function(model) {
          
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
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
        
        onClickPublish: function(event) {
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
        
        onClickSendEmail : function(event) {
            var id = $(event.target).attr('data-id');
            
            var data = {};
            var url = app.options.ajax_call_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'NutritionPlan';
            data.method = 'Notify';

            $.AjaxCall(data, url, view, task, table, function(output){
                console.log(output);
            });
        },
        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/overview/" + id, true);
        }
    });
            
    return view;
});