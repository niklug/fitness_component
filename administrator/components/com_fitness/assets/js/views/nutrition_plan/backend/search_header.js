define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/programs/trainers',
        'collections/programs/trainer_clients',
        'views/programs/select_element',
	'text!templates/nutrition_plan/backend/search_header.html'

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
        
        initialize : function() {
            this.business_profile_id = app.options.business_profile_id || localStorage.getItem('business_profile_id');
            
            if(app.collections.business_profiles && app.collections.trainers) {
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
            var data = {item : {}};
            //console.log(data);
            data.app = app;
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        events : {
            "change #business_profile_id " : "onChangeBusinessName",
            "change #trainer_id" : "onChangeTrainer",
            "change #client_id" : "onChangeClient",
            "click #search" : "search",
            "click #clear_all" : "clearAll",
            "click #publish_selected" : "onClickPublishAll",
            "click #unpublish_selected" : "onClickUnpublishAll",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#active_start_from, #active_start_to, #active_finish_from, #active_finish_to").datepicker({ dateFormat: "yy-mm-dd"});
                
                self.connectBusinessSelect();
                    
                var business_profile_id = self.business_profile_id;
                if(business_profile_id) {
                    self.loadTrainersSelect(business_profile_id);
                }

                var trainer_id = $(self.el).find("#trainer_id").val();
                if(trainer_id) {
                    self.loadClientsSelect(trainer_id);
                }
                
                //self.connectActivePlanFilter();
                
                self.connectForceActiveFilter();
                
                self.connectPublishedFilter();
                
            });
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
                el : $(this.el).find("#business_name_select"),
                collection : business_name_collection,
                first_option_title : '- Business Profile-',
                id_name : 'business_profile_id',
                model_field : 'business_profile_id',
                element_disabled : element_disabled

            }).render();
        },
        
        onChangeBusinessName : function(event) {
            var business_profile_id = $(event.target).val();
            
            this.business_profile_id = business_profile_id;
            
            localStorage.setItem('business_profile_id', business_profile_id);
   
            this.loadTrainersSelect(business_profile_id);
            $("#client_id").val('');
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
                this.model.set({trainer_id : trainer_id});
            }
            
            new Select_element_view({
                model : new Backbone.Model({trainer_id : trainer_id}),
                el : $(this.el).find("#trainer_select"),
                collection : trainers_collection,
                first_option_title : '-Trainer-',
                class_name : '',
                id_name : 'trainer_id',
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
                        el : $(self.el).find("#client_select"),
                        collection : collection,
                        value_field : 'client_id',
                        first_option_title : '-Client-',
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
            localStorage.setItem('client_id', client_id);
        },
        
        search : function() {
            var active_start_from = this.$el.find("#active_start_from").val();
            var active_start_to = this.$el.find("#active_start_to").val();
            var active_finish_from = this.$el.find("#active_finish_from").val();
            var active_finish_to = this.$el.find("#active_finish_to").val();
            var business_profile_id = this.$el.find("#business_profile_id").val();
            var trainer_id = this.$el.find("#trainer_id").val();
            var client_id = this.$el.find("#client_id").val();
            var force_active = this.$el.find("#force_active_select").val();

            this.model.set({
                active_start_from : active_start_from,
                active_start_to : active_start_to,
                active_finish_from : active_finish_from,
                active_finish_to : active_finish_to,
                business_profile_id : business_profile_id,
                trainer_id : trainer_id,
                client_id : client_id,
                force_active : force_active
            });
        },
        
        clearAll : function(){
            $(this.el).find("#business_profile_id, #trainer_id, #client_id, #state_select, #force_active_select").val('');
            $(this.el).find("#active_start_from, #active_start_to, #active_finish_from, #active_finish_to").val('');
            this.model.set(
                {
                    active_start_from : '',
                    active_start_to : '',
                    active_finish_from : '',
                    active_finish_to : '',
                    business_profile_id : '',
                    trainer_id : '',
                    client_id : '',
                    status : '',
                    force_active : ''
                }
            );
        },
        
        connectPublishedFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Published'},
                {id : '0', name : 'Unpublished'},
            ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#state_wrapper"),
                collection : collection,
                first_option_title : '-Published-',
                class_name : 'filter_select',
                id_name : 'state_select',
                model_field : 'state'
            }).render();
        },
        
        connectActivePlanFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Active Plan'},
                {id : '0', name : 'Inactive Plan '},
             ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#active_plan_wrapper"),
                collection : collection,
                first_option_title : '-Active Plan-',
                class_name : 'filter_select',
                id_name : 'active_plan_select',
                model_field : 'active_plan'
            }).render();
        },
        
        connectForceActiveFilter : function() {
            var collection = new Backbone.Collection();
            
            collection.add([
                {id : '1', name : 'Force Active'},
                {id : '0', name : 'Force Inactive'},
             ]);           
          
            new Select_element_view({
                model : this.model,
                el : $(this.el).find("#force_active_wrapper"),
                collection : collection,
                first_option_title : '-Force Active-',
                class_name : 'filter_select',
                id_name : 'force_active_select',
                model_field : 'force_active'
            }).render();
        },
        
        onClickPublishAll: function() {
            var selected = new Array();
            $('.item_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publishUnpublishItem(item, '1');
                });
            }
            $("#select_all_checkbox").prop("checked", false);
        },
        
        onClickUnpublishAll: function() {
            var selected = new Array();
            $('.item_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publishUnpublishItem(item, '0');
                });
            }
            $("#select_all_checkbox").prop("checked", false);
        },
        
         
        publishUnpublishItem : function(id, state) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({state : state}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
        
    });
            
    return view;
});