define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/programs/trainers',
        'views/programs/select_element',
	'text!templates/programs/backend/form_trainer.html'
], function (
        $,
        _,
        Backbone,
        app,
        Business_profiles_collection, 
        Trainers_collection, 
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            if( 
                app.collections.business_profiles 
                && app.collections.trainers
            ) {
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

        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            
            this.connectBusinessSelect();
            
            var business_profile_id = this.model.get('business_profile_id');
        
            if(business_profile_id) {
                this.loadTrainersSelect(business_profile_id);
            }
            
            return this;
        },
        
        events : {
            "change #business_profile_select" : "onChangeBusinessName",
            "change #trainer_id" : "onChangeTrainer",
        },
        
        connectBusinessSelect : function() {
            var business_name_collection = new Backbone.Collection;
            
            var element_disabled = '';
            
            if(app.options.is_trainer) {
                business_name_collection.add(app.collections.business_profiles.where({id : app.options.business_profile_id}));
                element_disabled = 'disabled';
            }
            
            if(app.options.is_superuser) {
                business_name_collection = app.collections.business_profiles;
            }
            
             new Select_element_view({
                model : this.model,
                el : $("#business_name_select"),
                collection : business_name_collection,
                first_option_title : '-Global Business Permission-',
                class_name : '',
                id_name : 'business_profile_select',
                model_field : 'business_profile_id',
                element_disabled : element_disabled
            }).render();
        },
        
        onChangeBusinessName : function(event) {
            var business_profile_id = $(event.target).val();
            this.loadTrainersSelect(business_profile_id);
            app.controller.deleteClients(this.model);
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
            
            if(app.options.is_trainer && !app.options.is_trainer_administrator && !this.model.get('id')) {
                
                this.model.set({trainer_id : app.options.user_id});
            }
            new Select_element_view({
                model : this.model,
                el : $("#trainer_select"),
                collection : trainers_collection,
                first_option_title : '-Select-',
                class_name : '',
                id_name : 'trainer_id',
                model_field : 'trainer_id',
                element_disabled : element_disabled
            }).render();
        },
        
        onChangeTrainer : function() {
            app.controller.deleteClients(this.model);
        },

    });
            
    return view;
});