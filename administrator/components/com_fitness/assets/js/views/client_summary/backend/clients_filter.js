define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/programs/trainers',
        'collections/programs/trainer_clients',
        'views/programs/select_element',
	'text!templates/client_summary/backend/clients_filter.html'
], function (
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
        template:_.template(template),
         
        initialize : function() {
            this.business_profile_id = app.options.business_profile_id || localStorage.getItem('business_profile_id');
            
            if(app.collections.business_profiles  && app.collections.trainers) {
                this.render();
                return;
            }
            
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.trainers = new Trainers_collection();
            
            var self = this;
            $.when (
                app.collections.business_profiles.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),

                app.collections.trainers.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
            ).then (function(response) {
                self.render();
            })
        },
        
        render: function(){
            var data = {};
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
                   
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectBusinessSelect();

                var business_profile_id = self.business_profile_id;
                if(business_profile_id) {
                    self.loadTrainersSelect(business_profile_id);
                }

                var trainer_id = $(self.el).find("#trainer_select").val();
                if(trainer_id) {
                    self.loadClientsSelect(trainer_id);
                }

            });
        },
        
        events : {
            "change #business_profile_select " : "onChangeBusinessName",
            "change #trainer_select" : "onChangeTrainer",
            "change #client_id" : "onChangeClient",
        },
        
        connectBusinessSelect : function() {
            var business_name_collection = new Backbone.Collection;
            
            var element_disabled = '';
            
            if(app.options.is_trainer) {
                business_name_collection.add(app.collections.business_profiles.where({id : this.business_profile_id}));
                element_disabled = 'disabled';
            }
            
            if(app.options.is_superuser) {
                business_name_collection = app.collections.business_profiles;
            }
            
             new Select_element_view({
                model : new Backbone.Model({business_profile_id : this.business_profile_id}),
                el : $(this.el).find("#business_name_select_wrapper"),
                collection : business_name_collection,
                first_option_title : '- Business profile-',
                id_name : 'business_profile_select',
                model_field : 'business_profile_id',
                element_disabled : element_disabled

            }).render();
        },
        
        onChangeBusinessName : function(event) {
            var business_profile_id = $(event.target).val();
            
            this.business_profile_id = business_profile_id;
            
            localStorage.setItem('business_profile_id', business_profile_id);
   
            this.loadTrainersSelect(business_profile_id);
        },
        
        loadTrainersSelect : function(business_profile_id) {
            var trainers_collection = new Backbone.Collection;
            
            trainers_collection.add(app.collections.trainers.where({business_profile_id : business_profile_id}));
            
            //console.log(trainers_collection);
            
            var element_disabled = '';
            
            //allow select only for trainer administrator
            
            if(app.options.is_simple_trainer) {
                element_disabled = 'disabled';
            }
            
            var trainer_id = localStorage.getItem('trainer_id');
            
            if(app.options.is_trainer && !app.options.is_trainer_administrator) {
                trainer_id = app.options.user_id 
            }
            
            new Select_element_view({
                model : new Backbone.Model({trainer_id : trainer_id}),
                el : $(this.el).find("#trainer_select_wrapper"),
                collection : trainers_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'trainer_select',
                model_field : 'trainer_id',
                element_disabled : element_disabled
            }).render();
        },
        
        onChangeTrainer : function(event) {
            var trainer_id = $(event.target).val();
            
            this.trainer_id = trainer_id;
            
            localStorage.setItem('trainer_id', trainer_id);
            
            this.loadClientsSelect(trainer_id);
        },
        
        loadClientsSelect : function(trainer_id) {
            var self = this;
            var trainer_clients_collection = new Trainer_clients_collection();
            trainer_clients_collection.fetch({
                data : {trainer_id : trainer_id},
                success : function (collection, response) {
                    new Select_element_view({
                        model : new Backbone.Model({client_id : localStorage.getItem('client_id')}),
                        el : $(self.el).find("#client_select_wrapper"),
                        collection : collection,
                        value_field : 'client_id',
                        first_option_title : '-Select-',
                        class_name : '',
                        model_field : 'client_id',
                        id_name : 'client_id',
                    }).render();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        onChangeClient : function(event) {
            var client_id = $(event.target).val();
            //console.log(client_id);
            localStorage.setItem('client_id', client_id);
        },
        
       
    });
            
    return view;
});