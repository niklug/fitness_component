define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/exercise_library/backend/list_item',
	'text!templates/exercise_library/backend/list.html'
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
            
            var self = this;
            if(this.collection.length) {
                _.each(this.collection.models, function(model) {
                    self.addItem(model);
                });
            }
            
            
            return this;
        },
        
        events: {
            "click #sort_exercise_name" : "onClickSortExerciseName",
            "click #sort_created" : "onClickSortCreated",
            "click #sort_created_by" : "onClickSortCreatedBy",
            "click #sort_status" : "onClickSortStatus",
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            "click .view" : "onClickView",
            "click .copy_exercise" : "onClickCopyExercise",
            "click #select_trashed" : "onClickSelectTrashed",
            "click .add_exercise" : "onClickAddExercise",
        },
        
        addItem : function(model) {
            
            model.set({edit_allowed : app.controller.edit_allowed(model)});
            
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        onClickSortExerciseName : function() {
            this.model.set({sort_by : 'a.exercise_name', order_dirrection : 'ASC'});
        },
        
        onClickSortCreated : function() {
            this.model.set({sort_by : 'a.created', order_dirrection : 'DESC'});
        },
        
        onClickSortCreatedBy : function() {
            this.model.set({sort_by : 'created_by_name', order_dirrection : 'ASC'});
        },
        
        onClickSortStatus : function() {
            this.model.set({sort_by : 'a.status', order_dirrection : 'ASC'});
        },
        
        onClickView : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/form_view/" + id, true);
        },
        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : '-2'}, {
                success: function (model, response) {
                    self.hide_items(id);
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
                    self.hide_items(id);
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
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        hide_items : function(items) {
            var self = this;
            var items = items.split(",");
            _.each(items, function(item, key){ 
                self.container_el.find(".item_row[data-id=" + item + "]").fadeOut();
            });
        },
        
        onClickSelectTrashed : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
        },
        
        onClickCopyExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_exercise(id);
        },
        
        onClickAddExercise : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_event_exercise(id);
        }
    });
            
    return view;
});