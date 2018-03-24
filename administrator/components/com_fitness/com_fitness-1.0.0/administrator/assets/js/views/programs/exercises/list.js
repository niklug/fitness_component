define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/exercises/list_item',
	'text!templates/programs/exercises/list.html',
        'jquery.tableDnD'
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
            this.readonly = this.options.readonly || false;

            app.collections.exercises = new this.options.exercises_collection();
            
            app.models.exercise = this.options.exercise_model;

            var self = this;
            $.when (
                app.collections.exercises.fetch({
                    data : {item_id : this.model.get('id')},
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
            data.choose_template = this.options.choose_template || false;
            data.search_videos = this.options.search_videos || false;
            data.title = this.options.title || false;
            
            $(this.el).html(this.template(data));
            
            this.container_el = this.$el.find("#items_container");
            
            var self = this;
            _.each(app.collections.exercises.models, function(model) {
                  self.addItem(model);
            });
            
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
            "click #choose_template" : "onClickChooseTemplate",
            "click #search_videos" : "onClickSearchVideo",
            
        },
        
       
        
        addItem : function(model) {
            this.item = new List_item_view({el : this.container_el, model : model, readonly : this.readonly}).render(); 
        },
        
        clearItems : function() {
            this.container_el.empty();
        },
        
        onClickAdd : function() {
            var model = new app.models.exercise({
                item_id : this.model.get('id'),
                order : this.$el.find("#exercise_table tbody tr").length,
            });
            this.saveItem(model);
        },
        
        saveItem : function(model) {
            var self  = this;
            model.save(null, {
                wait : true,
                success: function (model, response) {
                    app.collections.exercises.add(model);
                    self.render();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickDelete : function(event) {
            var id = $(event.target).attr('data-id');
            this.deleteItem(id);
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
            var model = new app.models.exercise();
            model.set({id : id});
            var self = this;
            app.collections.exercises.remove(model);
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
        
        copyExercise : function(id, order) {
            var model = app.collections.exercises.get(id);
            
            var new_model = model.clone() 

            new_model.set({id : null, order : order});
    
            this.saveItem(new_model);
        },
        
        copyExercises : function(items) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'programs';
            var task = 'copyProgramExercises';
            var table = '';
            data.item_id = this.model.get('id');
            data.db_table = app.options.db_table_exercises;
            data.items = items;
            $.AjaxCall(data, url, view, task, table, function(output){
                app.collections.exercises.fetch({
                    data : {item_id : self.model.get('id')},
                    success : function (collection, response) {
                        self.render();
                    }
                })
                
            });
        },
        
        onClickCopySelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var items  = selected.join(",");
                    
            this.copyExercises(items);
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
           var exercise_id = $(event.target).attr('data-id') || '';
           
           var el_url = app.options.base_url_relative + 'index.php?option=com_fitness&view=exercise_library';
           
           el_url += '&event_id=' + this.model.get('id');
           
           el_url += '&exercise_id=' + exercise_id;
           
           el_url += '&back_url=' + encodeURIComponent(app.options.base_url_relative + 'index.php?option=com_fitness&view=' + app.options.current_view + '#!/form_view/' + this.model.get('id'));

           window.location = el_url;
       },
       
       onClickShowVideo : function(event) {
           var id = $(event.target).attr('data-id');
     
           var view = 'item_view';
           
           if(app.options.is_backend) {
               view = 'form_view';
           }
           
           var el_url = app.options.base_url_relative + 'index.php?option=com_fitness&view=exercise_library#!/' + view + '/' + id;

           window.open(el_url,'_blank');
       },
       
       onClickChooseTemplate : function() {
           var el_url = app.options.base_url_relative + 'index.php?option=com_fitness&view=programs_templates';
           
           el_url += '&event_id=' + this.model.get('id');
           
           el_url += '&back_url=' + encodeURIComponent(app.options.base_url_relative + 'index.php?option=com_fitness&view=' + app.options.current_view + '#!/form_view/' + this.model.get('id'));

           window.location = el_url;
       },
    });
            
    return view;
});