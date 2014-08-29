define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/programs/trainers',
        'collections/programs/trainer_clients',
        'views/programs/select_element',
	'text!templates/diary/backend/search_block.html'
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
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.$el.find("#entry_date_from, #entry_date_to, #submit_date_from, #submit_date_to").datepicker({ dateFormat: "yy-mm-dd"});
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectStatusFilter();
                self.connectPublishedFilter();
            
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
            "click #add_item" : "onClickAddItem",
            "click #trash_delete_selected" : "onClickTrashDeleteSelected",
            "click #publish_selected" : "onClickPublish",
            "click #unpublish_selected" : "onClickUnpublish",
            "click #search" : "search",
            'keypress input[type=text]': 'filterOnEnter',
            "click #clear_all" : "clearAll",
            "change #state_filter" : "onChangeState",
            "change #business_profile_select " : "onChangeBusinessName",
            "change #trainer_select" : "onChangeTrainer",
            "change #client_id" : "onChangeClient",
        },

        filterOnEnter : function(event) { 
          if(event.which === 13) {
            this.search();
          }
        },
 
        search : function() {
            var client_name = this.$el.find("#client_name").val();
            var trainer_name = this.$el.find("#trainer_name").val();
            var assessed_by_name = this.$el.find("#assessed_by_name").val();
            var final_score_from = this.$el.find("#final_score_from").val();
            var final_score_to = this.$el.find("#final_score_to").val();
            var entry_date_from = this.$el.find("#entry_date_from").val();
            var entry_date_to = this.$el.find("#entry_date_to").val();
            var submit_date_from = this.$el.find("#submit_date_from").val();
            var submit_date_to = this.$el.find("#submit_date_to").val();
            

            this.model.set({
                client_name : client_name, 
                trainer_name : trainer_name,
                assessed_by_name : assessed_by_name,
                final_score_from : final_score_from,
                final_score_to : final_score_to,
                entry_date_from : entry_date_from, 
                entry_date_to : entry_date_to,
                submit_date_from : submit_date_from,
                submit_date_to : submit_date_to
            });
        },
        
        clearAll : function(){
            var form = $("#header_wrapper");
            form.find(".filter_select").val(0);
            form.find("input[type=text]").val('');
            form.find("#state_select").val('*');
            this.model.set(
                {
                    client_name : '',
                    trainer_name : '',
                    assessed_by_name : '',
                    final_score_from : '',
                    final_score_to : '',
                    entry_date_from : '',
                    entry_date_to : '',
                    submit_date_from : '',
                    submit_date_to : '',
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
                first_option_title : '-Status-',
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
                this.model.set({trainer_id : trainer_id});
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
        
        onClickPublish : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.publish(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        publish : function(id) {
            var model = this.collection.get(id);
       
            var self  = this;
            model.save({id : id, state : '1'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickUnpublish : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });

            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.unpublish(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        unpublish : function(id) {
            var model = this.collection.get(id);
            var self  = this;
            model.save({id : id, state : '0'}, {
                success: function (model, response) {
                    app.controller.update_list();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        onClickTrashDeleteSelected : function() {
            var selected = new Array();
            $('.trash_checkbox:checked').each(function() {
                selected.push($(this).attr('data-id'));
            });
            
            var self = this;
            
            if(selected.length > 0) {
                _.each(selected, function(item, key){ 
                    self.trash_delete(item);
                });
            }
            $("#select_trashed").prop("checked", false);
        },
        
        trash_delete : function(id) {
            this.model = this.collection.get(id);
            var self  = this;
            if(this.model.get('state') == '-2') {
                this.model.destroy({
                    success: function (model) {
                        app.controller.update_list();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            } else {
                this.model.save({state : '-2'}, {
                    success: function (model, response) {
                        app.controller.update_list();
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            }
            
        },


    });
            
    return view;
});