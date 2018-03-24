define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/diary/backend/list_item',
        'views/diary/backend/batch_process',
	'text!templates/diary/backend/list.html'
], function (
        $,
        _,
        Backbone,
        app,
        List_item_view,
        Batch_process_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.collection.bind("add", this.addItem, this);
            this.collection.bind("reset", this.clearItems, this);
        },
        
        template : _.template(template),

        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#diary_items");
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            this.connectBatchProcess();
            
            return this;
        },
        
        events: {
            "click #sort_entry_date" : "onClickSortEnryDate",
            "click #sort_submit_date" : "onClickSortSubmitDate",
            "click #sort_client_name" : "onClickSortClientName",
            "click #sort_trainer_name" : "onClickSortTrainerName",
            "click #sort_assessed_by" : "onClickSortAssessedByName",
            "click #sort_primary_goal" : "onClickSortPrimaryGoal",
            "click #sort_mini_goal" : "onClickSortMiniGoal",
            "click #sort_nutrition_focus" : "onClickSortNutritionFocus",
            "click #sort_status" : "onClickSortStatus",
            "click #sort_state" : "onClickSortState",
            "click #sort_score" : "onClickSortScore",
            
            "click #sort_target_calories" : "onClickSortTargetCalories",
            "click #sort_target_protein" : "onClickSortTargetProtein",
            "click #sort_target_fats" : "onClickSortTargetFats",
            "click #sort_target_carbs" : "onClickSortTargetCarbs",
            
            "click .publish" : "onClickPublish",            
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .preview" : "onClickPreview",
            "click .view" : "onClickPreview",
            "click #select_trashed" : "onClickSelectTrashed",
        },

        addItem : function(model) {
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
        },
        
        clearItems : function() {
            this.container_el.empty();
        },

        onClickSortEnryDate : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.entry_date', order_dirrection : "DESC"});
        },
        
        onClickSortSubmitDate : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.submit_date', order_dirrection : "DESC"});
        },
        
        onClickSortClientName : function() {
            app.models.request_params_diaries.set({'sort_by' : 'client_name', order_dirrection : "ASC"});
        },
        
        onClickSortTrainerName : function() {
            app.models.request_params_diaries.set({'sort_by' : 'trainer_name', order_dirrection : "ASC"});
        },
        
        onClickSortAssessedByName : function() {
            app.models.request_params_diaries.set({'sort_by' : 'assessed_by_name', order_dirrection : "ASC"});
        },
        
        onClickSortPrimaryGoal : function() {
            app.models.request_params_diaries.set({'sort_by' : 'primary_goal_name', order_dirrection : "ASC"});
        },
        
        onClickSortMiniGoal : function() {
            app.models.request_params_diaries.set({'sort_by' : 'mini_goal_name', order_dirrection : "ASC"});
        },
        
        onClickSortNutritionFocus : function() {
            app.models.request_params_diaries.set({'sort_by' : 'nutrition_focus_name', order_dirrection : "ASC"});
        },


        onClickSortStatus : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.status'});
        },
        
        onClickSortState : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.state'});
        },

        onClickSortScore : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.score',  order_dirrection : "DESC"});
        },
        
        onClickSortTargetCalories : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.target_calories',  order_dirrection : "DESC"});
        },
        
        onClickSortTargetProtein : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.target_protein',  order_dirrection : "DESC"});
        },

        onClickSortTargetFats : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.target_fats',  order_dirrection : "DESC"});
        },
        
        onClickSortTargetCarbs : function() {
            app.models.request_params_diaries.set({'sort_by' : 'a.target_carbs',  order_dirrection : "DESC"});
        },

        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            this.model = this.collection.get(id);
            var self  = this;
            this.model.save({state : '-2'}, {
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
            this.model = this.collection.get(id);
            var self = this;
            this.model.save({state : '1'}, {
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
            this.model = this.collection.get(id);
            var self = this;
            this.model.destroy({
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

        onClickPreview : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/item_view/" + id, true);
        },
        
        hide_items : function(items) {
            var self = this;
            var items = items.split(",");
            _.each(items, function(item, key){ 
                self.container_el.find(".diary_row[data-id=" + item + "]").fadeOut();
            });
        },
        
        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
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
        
        connectBatchProcess : function() {
            $(this.el).find("#batch_process_wrapper").html(new Batch_process_view({
                collection : this.collection,
                title : 'Choose Status to apply to selected nutrition diary entries',
                email_title : 'Send notification email to all clients',
                statuses : app.options.statuses,
                status_options : app.options.status_options,
                checkbox_element : ".trash_checkbox",
                checkbox_element_multiple : "#select_trashed"
            }).render().el);
        },

        close :function() {
            $(this.el).empty();
        }

    });
            
    return view;
});