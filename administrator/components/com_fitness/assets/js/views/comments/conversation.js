define([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/exercise_library/business_profiles',
    'collections/programs/trainers',
    'collections/programs/trainer_clients',
    'views/programs/select_element',
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
        template
        ) {

    var view = Backbone.View.extend({

        initialize : function() {
            if (!app.collections.business_profiles) {
                app.collections.business_profiles = new Business_profiles_collection();
                app.collections.business_profiles.fetch({
                    error: function(collection, response) {
                        alert(response.responseText);
                    }
                });
            }

            if (!app.collections.trainers) {
                app.collections.trainers = new Trainers_collection();
                app.collections.trainers.fetch({
                    error: function(collection, response) {
                        alert(response.responseText);
                    }
                });
            }
            
            var trainer_id = app.options.user_id;
            if (!app.collections.trainer_clients) {
                app.collections.trainer_clients = new Trainer_clients_collection();
                app.collections.trainer_clients.fetch({
                    data : {trainer_id : trainer_id},
                    success : function(collection, response) {
                        console.log(collection.toJSON());
                    },
                    error : function(collection, response) {
                        alert(response.responseText);
                    }
                });
            }

            
        },
        template : _.template(template),

        render : function() {
            //console.log(this.model.toJSON());
            var data = {item: this.model.toJSON()};
            data.$ = $;
            $(this.el).html(this.template(data));

            this.connectConversationPermissions();

            return this;
        },
        
        events: {
            "change .conversation_permissions" : "onChangeConversationPermissions",
        },
        
        connectConversationPermissions : function() {
            var collection = new Backbone.Collection();

            collection.add([
                {id: 'all_clients', name: 'All My Clients'},
                {id: 'selected_clients', name: 'Only Selected Clients'},
                {id: 'all_trainers', name: 'All Trainers'},
                {id: 'selected_trainers', name: 'Only Selected Trainers'},
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

        onChangeConversationPermissions : function(event) {
            var conversation_permissions = $(event.target).find("option:selected").val();
            this.model.set({conversation_permissions: conversation_permissions});

            console.log(this.model.toJSON());

            switch(conversation_permissions) {
                case 'all_clients':
                    this.showAllClients();
                    break;
                case 'selected_clients':
                    this.showSelectedClients();
                    break;
                case 'all_clients':
                    this.showAllTrainers();
                    break;
                case 'all_clients':
                    this.showSelectedTrainers();
                    break;
            }
        },
        
        showAllClients : function() {
            
            
        },
        
        showSelectedClients : function() {
            
        },
        
        showAllTrainers : function() {
            
        },
        
        showSelectedTrainers : function() {
            
        },


    });
            return view;

});