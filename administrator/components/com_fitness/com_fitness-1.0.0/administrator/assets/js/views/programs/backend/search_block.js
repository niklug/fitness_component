define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/business_profiles',
        'views/programs/select_element',
	'text!templates/programs/backend/search_block.html'
], function (
        $,
        _,
        Backbone,
        app, 
        Business_profiles_collection, 
        Select_element_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            if(app.collections.business_profiles) {
                this.render();
            } 
            
            app.collections.business_profiles = new Business_profiles_collection();
            var self = this;
            app.collections.business_profiles.fetch({
                success : function (collection, response) {
                    self.render();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });
            
            this.status_obj = $.status(app.options.status_options);
        },
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
               
            this.connectBusinessFilter();
           
            return this;
        },
        
        events : {
            "click #search" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
            "change #workout_filter" : "onChangePublishedWorkout",
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
            "click #publish_workout_selected" : "onClickPublishWorkout",
            "click #unpublish_workout_selected" : "onClickUnpublishWorkout",
            "click #copy_selected" : "onClickCopySelected",
            "click #go_back" : "onClickGoBack",
            "click #add_pr_tepm_multiple" : "onClickAddTemplateMultiple",
            
        },
        
        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
        
        connectBusinessFilter : function() {
            if(!app.options.is_superuser) {
                return false;
            }
             new Select_element_view({
                model : this.model,
                el : $("#business_profile_filter"),
                collection : app.collections.business_profiles,
                first_option_title : '-Global Business Permission-',
                class_name : 'filter_select',
                id_name : 'business_profile_select',
                model_field : 'business_profile_id'
            }).render();
        },
        
        search : function() {
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            var client_name = this.$el.find("#client_name").val();
            var trainer_name = this.$el.find("#trainer_name").val();
            var created_by_name = this.$el.find("#created_by_name").val();
            this.model.set({date_from : date_from, date_to : date_to, client_name: client_name, trainer_name : trainer_name, created_by_name : created_by_name});
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            
            this.model.set(
                {
                    date_from : '',
                    date_to : '',
                    client_name : '',
                    trainer_name : '',
                    created_by_name : '',
                    title : '',
                    location : '',
                    session_type : '',
                    session_focus : '',
                    business_profile_id : null
                }
            );
        },
        
        onChangeState : function(event) {
            var value = $(event.target).val();
            
            if(parseInt(value) == 1) {
                this.model.set({page : 1, current_page : 'list',  published : '1', uid : app.getUniqueId()});
            } else if(parseInt(value) == '-2') {
                this.model.set({page : 1, current_page : 'trash_list',  published : '-2', uid : app.getUniqueId()});
            } else if(parseInt(value) == '0') {
                this.model.set({page : 1, current_page : 'unpublished_list',  published : '0', uid : app.getUniqueId()});
            } else if(value == '*') {
                this.model.set({page : 1, current_page : 'all_list',  published : '*', uid : app.getUniqueId()});;
            }
        },
        
        
        onChangePublishedWorkout : function(event) {
            var value = $(event.target).val();
            
            this.model.set({frontend_published :  value});
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
        
        sendNotifyEmail : function(id) {
            this.status_obj.sendEmail(id, 'Notify');
        },
        
        onClickUnpublishWorkout : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.unpublishWorkout(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        unpublishWorkout : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, frontend_published : '0'}, {
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
        
        onClickCopySelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    app.controller.copy_item(item);
                });
            }
            $("#select_trashed,.trash_checkbox").prop("checked", false);
        },
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        },
         
        onClickGoBack : function() {
            app.controller.route_back_url();
        },
        
        onClickAddTemplateMultiple : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            var self = this;
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    app.controller.add_templates(item);
                });
            }
            $("#select_trashed,.trash_checkbox").prop("checked", false);
        }
    });
            
    return view;
});