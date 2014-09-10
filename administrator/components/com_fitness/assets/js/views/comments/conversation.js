define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/exercise_library/business_profiles',
    'collections/programs/trainers',
    'collections/programs/trainer_clients',
    'views/programs/select_element',
    'views/diary/checkbox_item',
    'text!templates/comments/conversation.html'

], function(
        $,
        _,
        Backbone,
        app,
        Business_profiles_collection,
        Trainers_collection,
        Trainer_clients_collection,
        Select_element_view,
        Checkbox_item_view,
        template
        ) {

    var view = Backbone.View.extend({
        initialize: function() {
            /*
             if (!app.collections.business_profiles) {
             app.collections.business_profiles = new Business_profiles_collection();
             app.collections.business_profiles.fetch({
             error: function(collection, response) {
             alert(response.responseText);
             }
             });
             }
             */
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
                $(this.el).find(".save_conversation, .toogle_checkboxes_wrapper").show();
            } else {
                $(this.el).find(".edit_conversation").show();
                $(this.el).find(".conversation_permissions").attr('disabled', true);
            }

            return this;
        },
        events: {
            "change .conversation_permissions": "onChangeConversationPermissions",
            "click .save_conversation": "onClickSaveConversation",
            "click .edit_conversation": "onClickEditConversation",
            "click .close_conversation": "onClickCloseConversation",
            "click .toogle_checkboxes": "onClickToogleCheckboxes",
            "click .select_all_checkboxes": "onClickSelectAll",
            "click .select_none_checkboxes": "onClickSelectNone",
            "click .delete_conversation": "onClickDeleteConversation",
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
            $(this.el).find(".close_conversation, .save_conversation, .toogle_checkboxes_wrapper").show();
            $(this.el).find(".conversation_permissions").attr('disabled', false);
            this.loadAllowedUsers();
        },
        onClickCloseConversation: function() {
            $(this.el).find(".edit_conversation").show();
            $(this.el).find(".close_conversation, .save_conversation, .toogle_checkboxes_wrapper").hide();
            $(this.el).find(".conversation_permissions").attr('disabled', true);
            this.clearUsersContainer();
        },
        connectConversationPermissions: function() {
            var collection = new Backbone.Collection();

            collection.add([
                {id: 'all_trainers', name: 'All Trainers'},
                {id: 'selected_trainers', name: 'Only Selected Trainers'},
            ]);

            if (app.options.is_backend) {
                collection.add([
                    {id: 'all_clients', name: 'All My Clients'},
                    {id: 'selected_clients', name: 'Only Selected Clients'},
                ]);
            }

            new Select_element_view({
                model: this.model,
                el: $(this.el).find(".conversation_permissions_select"),
                collection: collection,
                first_option_title: '-Select-',
                class_name: 'conversation_permissions',
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
            $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toogle_checkboxes").show();
            switch (conversation_permissions) {
                case 'all_clients':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toogle_checkboxes").hide();
                    this.showAllClients();
                    break;
                case 'selected_clients':
                    this.showSelectedClients();
                    break;
                case 'all_trainers':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toogle_checkboxes").hide();
                    this.showAllTrainers();
                    break;
                case 'selected_trainers':
                    this.showSelectedTrainers();
                    break;
                case 'all_business':
                    $(this.el).find(".select_all_checkboxes, .select_none_checkboxes, .toogle_checkboxes").hide();
                    this.showAllBusiness();
                    break;
                case 'selected_business':
                    this.showSelectedBusiness();
                    break;
            }
        },
        getTrainerClients: function(type) {
            if (app.collections.trainer_clients) {
                this.populateClients(app.collections.trainer_clients, type);
                return;
            }

            var self = this;
            var trainer_id = app.options.user_id;
            app.collections.trainer_clients = new Trainer_clients_collection();
            app.collections.trainer_clients.fetch({
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
            if (app.collections.trainers) {
                this.populateTrainers(app.collections.trainers, type);
                return;
            }

            var self = this;
            app.collections.trainers = new Trainers_collection();
            app.collections.trainers.fetch({
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
            var allowed_users = $(this.el).find(".checkbox_item:checked").map(function() {
                return $(this).val();
            }).get().join(",");

            this.model.set({
                item_id: this.options.comment_options.item_id,
                sub_item_id: this.options.comment_options.sub_item_id,
            });
            
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
                {id: 'all_business', name: 'All Businesses'},
                {id: 'selected_business', name: 'Only Selected Businesses'},
            ]);


            new Select_element_view({
                model: this.model,
                el: $(this.el).find(".conversation_permissions_select"),
                collection: collection,
                first_option_title: '-Select-',
                class_name: 'conversation_permissions',
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
        
        close : function() {
            $(this.el).remove();
        }


    });

    return view;

});