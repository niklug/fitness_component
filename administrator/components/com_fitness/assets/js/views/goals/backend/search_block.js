define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/goals/backend/search_block.html'
], function (
        $,
        _,
        Backbone,
        app, 
        Select_element_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {

        },
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.$el.find("#start_from, #start_to, #end_from, #end_to").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.connectStatusFilter();
            this.connectPublishedFilter();

            return this;
        },
        
        events : {
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
            "click #publish_workout_selected" : "onClickPublishWorkout",
            "click #unpublish_workout_selected" : "onClickUnpublishWorkout",
            "click #search" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
        },
        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
 
        search : function() {
            var start_from = this.$el.find("#start_from").val();
            var start_to = this.$el.find("#start_to").val();
            var end_from = this.$el.find("#end_from").val();
            var end_to = this.$el.find("#end_to").val();

            this.model.set({start_from : start_from, start_to : start_to, end_from : end_from, end_to : end_to});
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            form.find("#state_select").val('*');
            this.model.set(
                {
                    start_from : '',
                    start_to : '',
                    end_from : '',
                    end_to : '',
                    status : ''
                }
            );
        },

        onClickAddItem : function() {
            app.controller.navigate("!/form_primary/0", true);
        },
        
        connectStatusFilter : function() {
            var collection = new Backbone.Collection();
            
            _.each(app.options.statuses, function(status) {
                var model = new Backbone.Model(status);
                collection.add(model);
            });
          
             new Select_element_view({
                model : this.model,
                el : $(this.el).find("#status_wrapper"),
                collection : collection,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'status_select',
                model_field : 'status'
            }).render();
        },
        
        connectPublishedFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Published'},
                {id : '0', name : 'Unpublished'},
                {id : '-2', name : 'Trashed'},
                {id : '*', name : 'All Goals'}
            ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#state_wrapper"),
                collection : collection,
                first_option_title : '-Select-',
                class_name : 'filter_select',
                id_name : 'state_select',
                model_field : 'state'
            }).render();
        },
        
        onClickTrashDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var current_page = this.model.get('current_page');
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    if(current_page == 'trash_list') {
                        self.deleteItem(item);
                    } else {
                       self.trashItem(item); 
                    }
                });
            }
            $("#select_trashed").prop("checked", false);
        },

        
        trashItem : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, published : '-2'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        deleteItem : function(id) {
            var model = this.collection.get(id);
            var self = this;
            model.destroy({
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
                
        onClickPublishWorkout : function() {
            
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publishWorkout(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        publishWorkout : function(id) {
            var model = this.collection.get(id);
            
            var frontend_published = model.get('frontend_published');
            
            var self  = this;
            model.save({id : id, frontend_published : '1'}, {
                success: function (model, response) {
                    app.controller.update_list();
                    if(!parseInt(frontend_published)) {
                        self.sendNotifyEmail(id);
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

    });
            
    return view;
});