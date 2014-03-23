define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/exercises/items',
        'models/programs/exercises/item',        
        'views/programs/exercises/list_item',
	'text!templates/programs/exercises/list.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Exercises_collection,
        Item_model,
        List_item_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.exercises = new Exercises_collection();
            var self = this;
            $.when (
                app.collections.exercises.fetch({
                    data : {event_id : this.model.get('id')},
                    success : function (collection, response) {
                        console.log(collection.toJSON());
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                })

            ).then (function(response) {
                self.render();
            })
        },
        
        template:_.template(template),
        
        render : function () {
            var data = {};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            if(app.collections.exercises.length) {
                _.each(app.collections.exercises.models, function(model) {
                    self.addItem(model);
                });
            }
            return this;
        },
        
        events: {
            "click #add_exercise" : "onClickAdd",
            "click .delete" : "onClickDelete",
            "click #delete_selected" : "onClickDeleteSelected",
            "click #copy_selected" : "onClickCopySelected",
            "click #select_all" : "onClickSelectAll",
            "click .copy_item" : "onClickCopy",
            "focusout .data_cell" : "onCellEdit",
            "focusin .data_cell" : "onCellSetCursor",
        },
        
        addItem : function(model) {
            this.item = new List_item_view({el : this.container_el, model : model}).render(); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        onClickAdd : function() {
            var model = new Item_model({event_id : this.model.get('id')});
            this.saveItem(model);
        },
        
        saveItem : function(model) {
            var self  = this;
            model.save(null, {
                success: function (model, response) {
                    self.addItem(model);
                    app.collections.exercises.add(model);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            var model = new Item_model({id : id});
            var self = this;
            model.destroy({
                success: function (model) {
                    app.collections.exercises.remove(model);
                    self.hide_items(model.get('id'));
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
                $(".item_row[data-id=" + item + "]").remove();
            });
        },
        
        onClickDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.deleteItem(item);
                });
            }
            $("#select_all").prop("checked", false);
        },
        
        deleteItem : function(id) {
            var model = new Item_model();
            model.set({id : id});
            var self = this;
            model.destroy({
                success: function (model, response) {
                    self.hide_items(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickSelectAll : function(event) {
            $(".trash_checkbox").prop("checked", false);

            if($(event.target).attr("checked")) {
                $(".trash_checkbox").prop("checked", true);
            }
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            this.copyExercise(id);
        },
        
        copyExercise : function(id) {
            var model = app.collections.exercises.get(id);
            
            var new_model = model.clone() 
  
            new_model.set({id : null});
    
            this.saveItem(new_model);
        },
        
        onClickCopySelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.copyExercise(item);
                });
            }
            $("#select_all,.trash_checkbox").prop("checked", false);
        },
        
        onCellEdit : function(event) {
            var id = $(event.target).parent().attr('data-id');
            
            var field = $(event.target).attr('data-name');
            
            var value = $(event.target).text();
            
            var obj = {}
            
            obj[field ] = value;

            var model = app.collections.exercises.get(id);
            
            model.set(obj);
            
            model.save(null, {
                success: function (model, response) {
                    console.log(model);
                    $(event.target).removeClass("focused_cell");
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onCellSetCursor : function(event) {
            $(event.target).addClass("focused_cell");
        }
    });
            
    return view;
});