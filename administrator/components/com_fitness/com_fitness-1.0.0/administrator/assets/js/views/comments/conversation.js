define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/exercise_library/business_profiles',
    'collections/programs/trainers',
    'collections/programs/trainer_clients',
    'collections/diary/users_names',
    'models/comments/comment',
    'views/programs/select_element',
    'views/diary/checkbox_item',
    'views/comments/comment',
    'text!templates/comments/conversation.html'

], function(
        $,
        _,
        Backbone,
        app,
        Business_profiles_collection,
        Trainers_collection,
        Trainer_clients_collection,
        Users_names_collection,
        Comment_model,
        Select_element_view,
        Checkbox_item_view,
        Comment_view,
        template
        ) {

    var view = Backbone.View.extend({
        initialize: function() {
            this.parent_comment_edit_mode = false;
        
            this.conversation_permissions_object = {
                all_clients : 'All My Clients',
                selected_clients : 'Only Selected Clients',
                all_trainers : 'All Trainers',
                all_my_trainers : 'All My Trainers',
                selected_trainers : 'Only Selected Trainers',
                all_business : 'All Businesses',
                selected_business : 'Only Selected Businesses'
            };
            
            app.collections.users_names = new Users_names_collection();
        },
        
        template: _.template(template),
        
        render: function() {
            //console.log(this.model.toJSON());
            var data = {item: this.model.toJSON()};
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            if(!app.options.is_superuser) {
                this.connectConversationPermissions();
            }
            
            if(app.options.is_superuser) {
                this.connectConversationPermissionsBusiness();
            }

            this.users_container = $(this.el).find(".users_container");

            if (this.model.isNew()) {
                $(this.el).find(".save_conversation, .toggle_checkboxes_wrapper").show();
            } else {
                this.editAllowLoggic();
                
                $(this.el).find(".conversation_permissions").attr('disabled', true);
                
                this.setConversationPermissionsText();
                
                this.connectParentComment();
                
                this.populateClildComments();
            }

            return this;
        },
        
        
        events: {
            "change .conversation_permissions": "onChangeConversationPermissions",
            "click .save_conversation": "onClickSaveConversation",
            "click .edit_conversation": "onClickEditConversation",
            "click .close_conversation": "onClickCloseConversation",
            "click .toggle_checkboxes": "onClickToogleCheckboxes",
            "click .select_all_checkboxes": "onClickSelectAll",
            "click .select_none_checkboxes": "onClickSelectNone",
            "click .delete_conversation": "onClickDeleteConversation",
            "click .show_users_list" : "showUsersPopup",
            "click .close_users_list" : "hideUsersPopup",
            "click .reply_parent_comment": "onClickReplyComment",
        },
        
        editAllowLoggic : function() {
            var conversation_permissions = this.model.get('conversation_permissions');
            var created_by_client = this.model.get('created_by_client');
            
            var logged_business_profile_id = app.options.business_profile_id;
            var business_profile_id = this.model.get('business_profile_id');

            var allowed_users = this.model.get('allowed_users');
            if(allowed_users) {
                allowed_users = allowed_users.split(",");
            }

            var allowed_business = this.model.get('allowed_business');
            if(allowed_business) {
                allowed_business = allowed_business.split(",");
            }


            var user_id = app.options.user_id;
            var created_by = this.model.get('created_by');


            if(app.options.is_superuser) {
                this.showEditButton();
            }

            if(app.options.is_trainer_administrator) {
                if((user_id == created_by) || created_by_client) {
                    this.showEditButton();
                }
            }
            
            if(app.options.is_simple_trainer) {
                if(user_id == created_by) {
                    this.showEditButton();
                }
            }

            if(app.options.is_client) {
                if(user_id == created_by) {
                    this.showEditButton();
                }
            }  
        },
        
        showEditButton : function() {
            $(this.el).find(".edit_conversation, .delete_conversation").show();
        },

        showUsersPopup : function() {
            $(this.el).find(".show_users_list").hide();
            $(this.el).find(".close_users_list").show();
            var ids  = this.model.get('allowed_users');
            
            if(!ids) {
                return;
            }
            var self = this;
            app.collections.users_names.fetch({
                data: {ids: ids},
                success: function(collection, response) {
                    self.populateUserspopup(collection);
                    //console.log(collection.toJSON());
                },
                error: function(collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateUserspopup : function(collection) {

            var html = '<div class="users_popup" style="border: 1px solid #ccc; padding:2px;">';
            _.each(collection.models, function(model) {
                html += '<div style="font-size:13px;color:#8a8a8a;font-style:italic;display:inline-block;">';
                html += model.get('name') + "&nbsp; &nbsp;&nbsp;";
                html += "</div>";
            }, this);
            html += "</div>";
            $(this.el).find(".users_popup_container").html(html);
        },
        
        hideUsersPopup : function() {
            $(this.el).find(".show_users_list").show();
            $(this.el).find(".close_users_list").hide();
            $(this.el).find(".users_popup_container").empty();
        },
        
        setConversationPermissionsText : function() {
            var conversation_permissions = this.model.get('conversation_permissions');
            var conversation_permissions_text = this.conversation_permissions_object[conversation_permissions];
            $(this.el).find(".conversation_permissions_text").html(conversation_permissions_text);
        },
        
        onClickToogleCheckboxes: function() {
            var checkBoxes = $(this.el).find(".checkbox_item");
            // Invert selection
            checkBoxes.each(function() {
                $(this).attr('checked', !$(this).attr('checked'));
            });
        },
        
        onClickSelectAll: function() {
            var checkBoxes = $(this.el).find(".checkbox_item");
            // Invert selection
            checkBoxes.each(function() {
                $(this).attr('checked', true);
            });
        },
        
        onClickSelectNone: function() {
            var checkBoxes = $(this.el).find(".checkbox_item");
            // Invert selection
            checkBoxes.each(function() {
                $(this).attr('checked', false);
            });
        },
        
        onClickEditConversation: function() {
            $(this.el).find(".edit_conversation").hide();
            $(this.el).find(".close_conversation, .save_conversation, .toggle_checkboxes_wrapper").show();
            $(this.el).find(".conversation_permissions").attr('disabled', false);
            
            var conversation_permissions = this.model.get('conversation_permissions');

            //console.log(this.model.toJSON());
            this.loadUsersLogic(conversation_permissions);
        },
        onClickCloseConversation: function() {
            $(this.el).find(".edit_conversation").show();
            $(this.el).find(".close_conversation, .save_conversation, .toggle_checkboxes_wrapper").hide();
            $(this.el).find(".conversation_permissions").attr('disabled', true);
            this.clearUsersContainer();
        },
        connectConversationPermissions: function() {
            var collection = new Backbone.Collection();
            
            var conversation_permissions = this.model.get('conversation_permissions');
           
            //if created by Super User
            if(app.options.is_trainer && (conversation_permissions == 'all_business' || conversation_permissions == 'selected_business')) {
                collection.add([
                    {id: 'all_business', name : 'Only Trainers'},
                    {id: 'selected_business', name : 'Only Trainers'},
                ]);
            }

            if (app.options.is_backend) {
                collection.add([
                    {id: 'all_clients', name: this.conversation_permissions_object['all_clients']},
                    {id: 'selected_clients', name: this.conversation_permissions_object['selected_clients']},
                    {id: 'all_trainers', name: this.conversation_permissions_object['all_trainers']},
                ]);
            }
            
            if (!app.options.is_backend) {
                collection.add([
                    {id: 'all_my_trainers', name: this.conversation_permissions_object['all_my_trainers']},
                ]);
            }
            
            collection.add([
                {id: 'selected_trainers', name: this.conversation_permissions_object['selected_trainers']},
            ]);

            new Select_element_view({
                model: this.model,
                el: $(this.el).find(".conversation_permissions_select"),
                collection: collection,
                first_option_title: '-Select-',
                class_name: 'conversation_permissions dark_input_style',
                id_name: '',
                model_field: 'conversation_permissions'
            }).render();
        },
        onChangeConversationPermissions: function(event) {
            $(this.el).find(".edit_conversation").hide();
            $(this.el).find(".close_conversation, .save_conversation").show();
            $(event.target).removeClass("red_style_border");
            var conversation_permissions = $(event.target).find("option:selected").val();
            this.model.set({conversation_permissions: conversation_permissions});

            //console.log(this.model.toJSON());
            this.loadUsersLogic(conversation_permissions);
        },
        loadUsersLogic: function(conversation_permissions) {
            $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toggle_checkboxes").show();
            switch (conversation_permissions) {
                case 'all_clients':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toggle_checkboxes").hide();
                    this.showAllClients();
                    break;
                case 'selected_clients':
                    this.showSelectedClients();
                    break;
                case 'all_trainers':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toggle_checkboxes").hide();
                    this.showAllTrainers();
                    break;
                case 'all_my_trainers':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toggle_checkboxes").hide();
                    this.showAllTrainers();
                    break;
                case 'selected_trainers':
                    this.showSelectedTrainers();
                    break;
                case 'all_business':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toggle_checkboxes").hide();
                    this.showAllBusiness();
                    break;
                case 'selected_business':
                    this.showSelectedBusiness();
                    break;
            }
        },
        getTrainerClients: function(type) {
            if (app.collections.trainer_clients1) {
                this.populateClients(app.collections.trainer_clients1, type);
                return;
            }

            var self = this;
            var trainer_id = app.options.user_id;
            app.collections.trainer_clients1 = new Trainer_clients_collection();
            app.collections.trainer_clients1.fetch({
                data: {trainer_id: trainer_id},
                success: function(collection, response) {
                    self.populateClients(collection, type);
                },
                error: function(collection, response) {
                    alert(response.responseText);
                }
            });
        },
        getTrainers: function(type) {
            var data = {};
            
            if(app.options.is_client) {
                data.client_id = app.options.user_id;
            }
            
            var created_by_client = this.options.comment_options.item_model.get('created_by_client');
            //console.log(this.options.comment_options);
            if(created_by_client) {
                data.client_id = created_by_client;
            }
            
            if(app.options.is_trainer) {
                data.business_profile_id = app.options.business_profile_id;
            }

            var self = this;
            app.collections.trainers_1 = new Trainers_collection();
            app.collections.trainers_1.fetch({
                data : data,
                success: function(collection, response) {
                    self.populateTrainers(collection, type);
                },
                error: function(collection, response) {
                    alert(response.responseText);
                }
            });
        },
        showAllClients: function() {
            this.getTrainerClients(true);
        },
        showSelectedClients: function() {
            this.getTrainerClients(false);
        },
        showAllTrainers: function() {
            this.getTrainers(true);
        },
        showSelectedTrainers: function() {
            this.getTrainers(false);
        },
        
        showAllBusiness: function() {
            this.getBusiness(true);
        },
        
        showSelectedBusiness: function() {
            this.getBusiness(false);
        },
        
        populateClients: function(collection, type) {
            this.clearUsersContainer();
            var self = this;
            _.each(collection.models, function(model) {
                this.addClientItem(model, type);
            }, this);
        },
        
        addClientItem: function(model, type) {
            var created_by_client = this.options.comment_options.item_model.get('created_by_client');
            
            console.log(this.options.comment_options.item_model);

            if(created_by_client && created_by_client != model.get('client_id')) {
                return;
            }
            
            //for appointments 
            var group_clients_data = this.options.comment_options.item_model.get('group_clients_data');
            
            var item_data = this.options.comment_options.item_model.toJSON();
            
            if(item_data.hasOwnProperty("group_clients_data")) {
                var group_clients = [];
                _.each(group_clients_data, function(o) {
                     group_clients.push(o.client_id);
                });
                
       
                if(group_clients.indexOf(model.get('client_id')) == '-1') {
                    return;
                }
            }
            //
            
            var id = model.get('client_id');

            var checked = type;

            model.set({id: id});

            var allowed_users = this.model.get('allowed_users');

            if (allowed_users) {
                allowed_users = allowed_users.split(",");

                if (allowed_users.indexOf(id) != '-1') {
                    checked = true;
                }
            }

            this.users_container.append(new Checkbox_item_view({disabled: type, checked: checked, model: model}).render().el);
        },
        populateTrainers: function(collection, type) {
            this.clearUsersContainer();
            var self = this;
            _.each(collection.models, function(model) {
                this.addTrainerItem(model, type);
            }, this);
        },
        addTrainerItem: function(model, type) {
            var id = model.get('id');

            var checked = type;

            model.set({id: id});

            var allowed_users = this.model.get('allowed_users');

            if (allowed_users) {
                allowed_users = allowed_users.split(",");

                if (allowed_users.indexOf(id) != '-1') {
                    checked = true;
                }
            }

            this.users_container.append(new Checkbox_item_view({disabled: type, checked: checked, model: model}).render().el);
        },
        clearUsersContainer: function() {
            this.users_container.empty();
        },
        onClickSaveConversation: function() {
            
            if(this.model.isNew()) {
                this.parent_comment_edit_mode = true;
                this.model.set({comment : ''});
            }
            
            var allowed_users = $(this.el).find(".checkbox_item:checked").map(function() {
                return $(this).val();
            }).get().join(",");

            if(!app.options.is_superuser) {
                this.model.set({allowed_users: allowed_users});
            }
            
            if(app.options.is_superuser) {
                this.model.set({allowed_business : allowed_users});
            }

            var conversation_permissions_field = $(this.el).find(".conversation_permissions");

            var conversation_permissions = conversation_permissions_field.find("option:selected").val();

            conversation_permissions_field.removeClass("red_style_border");

            if (!conversation_permissions && !app.options.is_superuser) {
                conversation_permissions_field.addClass("red_style_border");
                return;
            }
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                alert(this.model.validationError);
                return;
            }
            var self = this;
            this.model.save(null, {
                success: function(model, response) {
                    console.log(model.toJSON());
                    self.render();
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        loadAllowedUsers: function() {
            var conversation_permissions = this.model.get('conversation_permissions');
            this.loadUsersLogic(conversation_permissions);
        },
        
        onClickDeleteConversation : function() {
            var self = this;
            this.model.destroy({
                success: function(model, response) {
                    self.close();
                },
                error: function(model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        connectConversationPermissionsBusiness : function() {
            var collection = new Backbone.Collection();

            collection.add([
                {id: 'all_business', name: this.conversation_permissions_object['all_business']},
                {id: 'selected_business', name: this.conversation_permissions_object['selected_business']},
            ]);


            new Select_element_view({
                model: this.model,
                el: $(this.el).find(".conversation_permissions_select"),
                collection: collection,
                first_option_title: '-Select-',
                class_name: 'conversation_permissions dark_input_style',
                id_name: '',
                model_field: 'conversation_permissions'
            }).render();
        },
        
        getBusiness : function(type) {
            if (app.collections.business_profiles) {
                this.populateBusiness(app.collections.business_profiles, type);
                return;
            }

            var self = this;
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.business_profiles.fetch({
                success: function(collection, response) {
                    //console.log(collection.toJSON());
                    self.populateBusiness(collection, type);
                },
                error: function(collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        populateBusiness : function(collection, type) {
            this.clearUsersContainer();
            var self = this;
            _.each(collection.models, function(model) {
                this.addBusinessItem(model, type);
            }, this);
        },
        addBusinessItem: function(model, type) {
            var id = model.get('id');

            var checked = type;

            model.set({id: id});

            var allowed_users = this.model.get('allowed_business');

            if (allowed_users) {
                allowed_users = allowed_users.split(",");

                if (allowed_users.indexOf(id) != '-1') {
                    checked = true;
                }
            }

            this.users_container.append(new Checkbox_item_view({disabled: type, checked: checked, model: model}).render().el);
        },
        
        connectParentComment : function() {
            var target = $(this.el).find(".parent_comment_wrapper");
            
            target.html(new Comment_view({
                model : this.model,
                comment_options : this.options.comment_options,
                show_delete : false,
                show_arrow : false,
                show_editor : false,
                edit_mode : this.parent_comment_edit_mode
            }).render().el);
        },
        
        onClickReplyComment : function() {
            var target = $(this.el).find(".child_comments_wrapper");
            
            var data = {
                business_profile_id : app.options.business_profile_id,
                created_by : app.options.user_id,
                created_by_name : app.options.user_name,
                created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
                parent_id : this.model.get('id'),
                comment : '',
                item_id: this.options.comment_options.item_id,
                sub_item_id: this.options.comment_options.sub_item_id,
                allowed_users : this.model.get('allowed_users')
            };
            
            var model = new Comment_model(data, {db_table : this.options.comment_options.db_table});
            
            target.append(new Comment_view({
                model : model,
                comment_options : this.options.comment_options,
                show_delete : true,
                show_arrow : true,
                show_editor : false,
                edit_mode : true
            }).render().el);
        },
        
        populateClildComments : function() {
            var self = this;
            _.each(this.collection.models, function (model) { 
                this.addChildItem(model);
            }, this);
        },
        
        addChildItem : function(model) {
            if(model.get('parent_id') == '0') {
                return;
            }
            
            if(model.get('parent_id') != this.model.get('id')) {
                return;
            }
            
            var target = $(this.el).find(".child_comments_wrapper");
    
            target.append(new Comment_view({
                model : model,
                comment_options : this.options.comment_options,
                show_delete : true,
                show_arrow : true,
                show_editor : false,
                edit_mode : false
            }).render().el);
        },
        
        close : function() {
            $(this.el).remove();
        }


    });

    return view;

});