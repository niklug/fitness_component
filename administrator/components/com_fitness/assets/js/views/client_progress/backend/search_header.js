define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/select_filter',
        'collections/exercise_library/business_profiles',
        'collections/programs/trainers',
        'collections/programs/trainer_clients',
        'views/programs/select_element',
	'text!templates/client_progress/backend/search_header.html'

], function (
        $,
        _,
        Backbone,
        app,
        Select_filter_collection,
        Business_profiles_collection, 
        Trainers_collection,
        Trainer_clients_collection,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.business_profile_id = app.options.business_profile_id || localStorage.getItem('business_profile_id');
            
            if(app.collections.business_profiles && app.collections.trainers && app.collections.session_focuses) {
                this.render();
                return;
            }
            
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.trainers = new Trainers_collection();
            app.collections.session_focuses = new Select_filter_collection();
            
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
                }),
                
                app.collections.session_focuses.fetch({
                    data : {table : app.options.db_table_session_focuses, category_id : 5},
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
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.$el.find("#date_from, #date_to").datepicker({ dateFormat: "yy-mm-dd"});
                
                self.connectBusinessSelect();
                    
                var business_profile_id = self.business_profile_id;
                if(business_profile_id) {
                    self.loadTrainersSelect(business_profile_id);
                }

                var trainer_id = $(self.el).find("#trainer_id").val();
                if(trainer_id) {
                    self.loadClientsSelect(trainer_id);
                }
                
                self.loadSessionFocus('25');

            });
        },
        
        loadSessionFocus : function(id) {
            var session_focus_collection = new Backbone.Collection;
            
            session_focus_collection.add(app.collections.session_focuses.where({session_type_id : id}));

            new Select_element_view({
                model : '',
                el : this.$el.find("#session_focus_select"),
                collection : session_focus_collection,
                first_option_title : '-Assessment Type-',
                class_name : ' required ',
                id_name : 'session_focus',
                model_field : 'session_focus',
                element_disabled :  ''
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
            $("#main_container, #progress_graph_container, #sub_search_wrapper").empty();
            var date_from = this.$el.find("#date_from").val();
            var date_to = this.$el.find("#date_to").val();
            var client_id = this.$el.find("#client_id").val();
            var session_focus = this.$el.find("#session_focus").val();
            this.model.set({date_from : date_from, date_to : date_to, client_id : client_id, session_focus : session_focus, published : '1', frontend_published : '1', uid : app.getUniqueId()});
        },
        
        clearAll : function(){
            this.collection.reset();
            var form = $("#header_wrapper");
            form.find("#session_focus, #date_from, #date_to").val('');
            $("#main_container, #progress_graph_container, #sub_search_wrapper").empty();
        },
        
    });
            
    return view;
});