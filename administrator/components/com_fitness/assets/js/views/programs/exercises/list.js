define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/exercises/items',
        'models/programs/exercises/item',        
        'views/programs/exercises/list_item',
	'text!templates/programs/exercises/list.html',
        'jquery.tableDnD'
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
            this.readonly = this.options.readonly || false;

            app.collections.exercises = new Exercises_collection();
            var self = this;
            $.when (
                app.collections.exercises.fetch({
                    data : {event_id : this.model.get('id')},
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
            data.readonly = this.readonly;
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            if(app.collections.exercises.length) {
                _.each(app.collections.exercises.models, function(model) {
                    self.addItem(model);
                });
            }
            
            this.connectDragPlugin();
            
            this.setComments();
            
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
            "click #show_hide_comments" : "onClickShowComments",
            "click .search_video" : "onClickSearchVideo",
            "click .show_exercise_video" : "onClickShowVideo",
            
        },
        
        addItem : function(model) {
            this.item = new List_item_view({el : this.container_el, model : model, readonly : this.readonly}).render(); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        onClickAdd : function() {
            var model = new Item_model({
                event_id : this.model.get('id'),
                order : this.$el.find("#exercise_table tbody tr").length,
            });
            this.saveItem(model);
        },
        
        saveItem : function(model) {
            var self  = this;
            model.save(null, {
                success: function (model, response) {
                    self.addItem(model);
                    app.collections.exercises.add(model);
                    self.connectDragPlugin();
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
                    $(event.target).removeClass("focused_cell");
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onCellSetCursor : function(event) {
            $(event.target).addClass("focused_cell");
        },
        
        connectDragPlugin : function() {
            var self = this;
            this.$el.find("#exercise_table").die().tableDnD({
                onDrop: function(table, row) {
                    var rows = table.tBodies[0].rows;

                    var debugStr = "Row dropped was "+row.id+". New order: ";

                    for (var i=0; i<rows.length; i++) {
                        debugStr += rows[i].id+" ";
                        self.setEventExerciseOrder(rows[i].id.replace('exercise_row_', ''), i);
                    }
                    //console.log(debugStr);
                }
            });
        },
        
        setEventExerciseOrder : function(id, order) {
            var obj = {}
            
            obj.order = order;

            var model = app.collections.exercises.get(id);
            
            model.set(obj);
            
            model.save(null, {
                 error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickShowComments : function() {
            var exercise_comment_show =  localStorage.getItem("exercise_comment_show");
          
            if(parseInt(exercise_comment_show)) {
                localStorage.setItem("exercise_comment_show", "0");
            } else {
                localStorage.setItem("exercise_comment_show", "1");
            }
            
            this.setComments();
        },
        
        setComments : function() {
            var exercise_comment_show =  localStorage.getItem("exercise_comment_show");

            if(parseInt(exercise_comment_show)) {
                this.$el.find(".comments").show();
                this.$el.find("#show_hide_comments").html('[HIDE COMMENTS]');
            } else {
                this.$el.find(".comments").hide();
                this.$el.find("#show_hide_comments").html('[SHOW COMMENTS]');
            }
       },
       
       onClickSearchVideo : function(event) {
           var exercise_id = $(event.target).attr('data-id');
           
           var el_url = app.options.base_url_relative + 'index.php?option=com_fitness&view=exercise_library&event_id=' + this.model.get('id') + '&exercise_id=' + exercise_id;

           window.location = el_url;
       },
       
       onClickShowVideo : function(event) {
           var id = $(event.target).attr('data-id');
           var el_url = app.options.base_url_relative + 'index.php?option=com_fitness&view=exercise_library#!/form_view/' + id;

           window.open(el_url,'_blank');
       }
    });
            
    return view;
});