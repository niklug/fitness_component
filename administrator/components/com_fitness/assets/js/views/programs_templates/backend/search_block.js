define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/business_profiles',
        'views/programs/select_element',
	'text!templates/programs_templates/backend/search_block.html'
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
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
            "click #copy_selected" : "onClickCopySelected",
            "click #back_program" : "onClickCopyBackProgram",
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
            var name = this.$el.find("#workout_name").val();
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            var client_name = this.$el.find("#client_name").val();
            var trainer_name = this.$el.find("#trainer_name").val();
            var created_by_name = this.$el.find("#created_by_name").val();
            this.model.set({name : name, date_from : date_from, date_to : date_to, client_name: client_name, trainer_name : trainer_name, created_by_name : created_by_name});
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            
            this.model.set(
                {
                    name : '',
                    date_from : '',
                    date_to : '',
                    client_name : '',
                    created_by_name : '',
                    title : '',
                    session_type : '',
                    session_focus : '',
                    business_profile_id : null
                }
            );
        },
        
        onChangeState : function(event) {
            var value = $(event.target).val();
            
            if(parseInt(value) == 1) {
                this.model.set({page : 1, current_page : 'list',  state : '1', uid : app.getUniqueId()});
            } else if(parseInt(value) == '-2') {
                this.model.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            } else if(parseInt(value) == '0') {
                this.model.set({page : 1, current_page : 'unpublished_list',  state : '0', uid : app.getUniqueId()});
            } else if(value == '*') {
                this.model.set({page : 1, current_page : 'all_list',  state : '*', uid : app.getUniqueId()});;
            }
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
            model.save({id : id, state : '-2'}, {
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
        
        onClickCopyBackProgram : function() {
            app.controller.route_program();
        }
    });
            
    return view;
});