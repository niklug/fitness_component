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
            app.collections.business_profiles = new Business_profiles_collection();
            var self = this;
            app.collections.business_profiles.fetch({
                success : function (collection, response) {
                    self.connectBusinessFilter(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            this.$el.find("#date_from").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.$el.find("#date_to").datepicker({ dateFormat: "yy-mm-dd"});
           
            return this;
        },
        
        events : {
            "click #search" : "search",
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
            "change #workout_filter" : "onChangePublishedWorkout",
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
        },
        
        connectBusinessFilter : function(collection) {
            if(!app.options.is_superuser) {
                return false;
            }
             new Select_element_view({
                model : this.model,
                el : $("#business_profile_filter"),
                collection : collection,
                first_option_title : '-Global Business Permission-',
                class_name : '',
                id_name : 'business_profile_select',
                model_field : 'business_profiles'
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
                    date_from : null,
                    date_to : null,
                    client_name : null,
                    trainer_name : null,
                    created_by_name : null,
                    title : null,
                    location : null,
                    session_type : null,
                    session_focus :null
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
        
        onClickAddItem : function() {
            app.controller.navigate("!/form_view/0", true);
        }
    });
            
    return view;
});