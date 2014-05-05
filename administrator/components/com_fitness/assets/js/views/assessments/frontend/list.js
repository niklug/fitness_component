define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/assessments/frontend/list_item',
	'text!templates/assessments/frontend/list.html'
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
            this.collection.off("add");
            this.collection.off("reset");
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
            "click .view_item" : "onClickViewItem",
            
            "click .trash" : "onClickTrash",
            "click .restore" : "onClickRestore",
            "click .delete" : "onClickDelete",
            
            "click #select_all" : "onClickSelectAll",
            
            "click #sort_starttime" : "sort_starttime",
            "click #sort_trainer" : "sort_trainer",
            "click #sort_session_focus" : "sort_session_focus",
            "click #sort_status" : "sort_status",
            "click #sort_weight" : "sort_weight",
            "click #sort_body_fat" : "sort_body_fat",
            "click #sort_lean_mass" : "sort_lean_mass",
            
            "click #trash_selected" : "onClickTrashSelected",
            "click #delete_selected" : "onClickDeleteSelected",
            
            "click .copy_item" : "onClickCopy",
            "click #copy_selected" : "onClickCopySelected",

        },
        
        addItem : function(model) {
            
            var edit_allowed = app.controller.edit_allowed(model);
            var delete_allowed = app.controller.delete_allowed(model);
            var status_change_allowed = app.controller.status_change_allowed(model);
           
            model.set({edit_allowed : edit_allowed, status_change_allowed : status_change_allowed, delete_allowed : delete_allowed});
            
            
            var current_page = this.model.get('current_page');

            this.item = new List_item_view({el : this.container_el, model : model}).render(); 

            app.models.pagination.set({'items_total' : model.get('items_total')});
            
            this.$el.find( "#items_container tr:odd" ).addClass('row1');
            this.$el.find( "#items_container tr:even" ).addClass('row0');
        },
        
        clearItems : function() {
            this.container_el.empty();
        },

        
        onClickTrash : function(event) {
            var id = $(event.target).attr('data-id');
            this.trash_item(id);
        },
        
        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            this.delete_item(id);
        },
        
        onClickRestore : function(event) {
            var id = $(event.target).attr('data-id');
            this.restore_item(id);
        },
        
        trash_item : function(id) {
            var model = app.collections.items.get(id);
            var self  = this;
            model.save({published : '-2'}, {
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        restore_item : function(id) {
            var model = app.collections.items.get(id);
            var self = this;
            model.save({published : '1'}, {
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        delete_item : function(id) {
            var model = app.collections.items.get(id);
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
                $(".item_row[data-id=" + item + "]").fadeOut();
            });
        },
        
        onClickTrashSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.trash_item(item);
                });
            }
            $("#select_all").prop("checked", false);
        },
                
        onClickDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.delete_item(item);
                });
            }
            $("#select_all").prop("checked", false);
        },
        
        sort_starttime : function() {
            this.model.set({sort_by : 'a.starttime', order_dirrection : 'DESC'});
        },

        sort_session_focus : function() {
            this.model.set({sort_by : 'session_focus_name', order_dirrection : 'ASC'});
        },
        
        sort_status : function() {
            this.model.set({sort_by : 'status', order_dirrection : 'ASC'});
        },
        
        sort_trainer : function() {
            this.model.set({sort_by : 'trainer_name', order_dirrection : 'ASC'});
        },
        
        sort_weight : function() {
            this.model.set({sort_by : 'a.weight', order_dirrection : 'DESC'});
        },
        
        sort_body_fat : function() {
            this.model.set({sort_by : 'a.body_fat', order_dirrection : 'DESC'});
        },
        
        sort_lean_mass : function() {
            this.model.set({sort_by : 'a.lean_mass', order_dirrection : 'DESC'});
        },
        
        onClickSelectAll : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
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
            $.AjaxCall(data, url, view, task, table, function(output){
                app.controller.update_list();
            });
        },
        
        onClickCopySelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.copy_item(item);
                });
            }
            $("#select_all,.trash_checkbox").prop("checked", false);
        },
        
        onClickViewItem : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/item_view/" + id, true);
        }
        
    });
            
    return view;
});