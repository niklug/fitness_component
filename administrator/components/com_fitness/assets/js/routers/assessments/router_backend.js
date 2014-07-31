define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/items',
        'collections/assessments/event_clients',
        'collections/assessments/exercises/items',
        'models/assessments/item',
        'models/assessments/request_params_items',
        'models/assessments/exercises/item', 
        'views/assessments/backend/form_container',
        'views/assessments/backend/menus/main_menu',
        'views/assessments/backend/form_details',
        'views/assessments/backend/form_trainer',
        'views/assessments/backend/form_clients',
        'views/assessments/backend/form_workout_instructions',
        'views/assessments/backend/list',
        'views/assessments/backend/list_header_container',
        'views/programs/exercises/list',
        'views/programs/backend/comments_block',
        'views/assessments/backend/form_video',
        'views/assessments/backend/photo_block/list',
        'jquery.validate'
        
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Event_clients_collection, 
        Exercises_collection,
        Item_model,
        Request_params_items_model,
        Exercise_model,
        Form_container_view,
        Main_menu_view,
        Form_details_view,
        Form_trainer_view,
        Form_event_clients_view,
        Form_event_workout_instructions,
        List_view,
        List_header_container_view,
        Exercises_list_view,
        Comments_block_view,
        Form_video_view,
        Photo_block_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            //unique id
            app.getUniqueId = function() {
                return new Date().getUTCMilliseconds();
            }
                        
            app.models.item = new Item_model();
            
            app.collections.items = new Items_collection();
            
            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //
            this.onClientChange();

            app.options.client_id = localStorage.getItem('client_id');
            
            app.models.request_params = new Request_params_items_model({business_profile_id : business_profile_id, client_id : app.options.client_id});
            app.models.request_params.bind("change", this.get_items, this);
        },

        routes: {
            "": "list_view", 
            "!/form_view/:id": "form_view", 
            "!/list_view": "list_view", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        onClientChange : function() {
            var self = this;
            $("#graph_client_id").die().live('change', function() {
                var client_id = $(this).val();
                app.options.client_id = client_id;
                localStorage.setItem('client_id', client_id);
                app.models.request_params.set({client_id : client_id});
                self.navigate("!/list_view", true);
            });
        },

        form_view : function(id) {
            
            if(!parseInt(id)) {
                this.load_form_view(new Item_model());
                return;
            }
            
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    var edit_allowed = self.edit_allowed(model);

                    if(!edit_allowed) {
                        self.navigate("!/list_view", true);
                        return;
                    }
                    
                    self.load_form_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })

        },
        
        load_form_view : function(model) {
            $("#header_wrapper").html(new Main_menu_view({model : model}).render().el);
            
            $("#main_container").html(new Form_container_view({model : model}).render().el);

            new Form_details_view({el : $("#details_wrapper"), model : model});
            
            new Form_trainer_view({el : $("#trainer_data_wrapper"), model : model});
            
            if(model.get('id')) {
                new Form_event_clients_view({el : $("#clients_data_wrapper"), model : model});
                
                new Form_event_workout_instructions({el : $("#workout_instuctions_wrapper"), model : model});
                
                new Exercises_list_view({
                    el : $("#exercises_list"),
                    model : model,
                    exercise_model : Exercise_model,
                    exercises_collection : Exercises_collection,
                    choose_template : true,
                    title : 'PHYSICAL ASSESSMENT DETAILS'
                });
                
                new Form_video_view({el : $("#video_block"), model : model});
                
                new Photo_block_view({el : $("#photo_block"), model : model});
                
                new Comments_block_view({el : $("#comments_block"), model : model});
            }
        },
     
        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        list_view : function() {
            //show all
            app.models.request_params.set({page : 1, current_page : 'all_list', published : '*', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        list_actions : function () {
            $("#header_wrapper").html(new List_header_container_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        trash_list : function() {
            app.models.request_params.set({page : 1, current_page : 'trash_list',  published : '-2', uid : app.getUniqueId()});
            this.list_actions();
        },
        
        edit_allowed : function(model) {
            var access = false;
            
            var created_by = model.get('owner');
            
            var trainer_id = model.get('trainer_id');
            
            var is_associated_trainer = model.get('is_associated_trainer');
            
            var is_simple_trainer = app.options.is_simple_trainer;
            
            var business_profile_id = model.get('business_profile_id');
            
            var is_trainer_administrator = app.options.is_trainer_administrator;
            
            var is_superuser = app.options.is_superuser;
            
            var user_id = app.options.user_id;
            
            
            //if simple trainer//
            
            // if event created by logged simple trainer
            if(is_simple_trainer && (user_id == created_by)) {
                var access = true;
            }
            //if logged simple trainer is event's trainer 
            if(is_simple_trainer && (user_id == trainer_id)) {
                var access = true;
            }
            //if logged simple trainer associated to the event's clients 
            if(is_simple_trainer && is_associated_trainer) {
                var access = true;
            }
            // //
            
            // if trainer administrator //
            if(is_trainer_administrator && (business_profile_id == app.options.business_profile_id)) {
                var access = true;
            }
            // //
            
            // if superuser
            if(is_superuser) {
                var access = true;
            }
           
            return access;
        },
        
        copy_item : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'Programs';
            var task = 'copyEvent';
            var table = '';
            data.id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                self.update_list();
            });
        },
        
        update_list : function() {
            app.models.request_params.set({ uid : app.getUniqueId()});
        },
        
        deleteClients : function(model) {
            var event_clients_collection = new Event_clients_collection();
            var self = this;
            var event_id = model.get('id');
            event_clients_collection.fetch({
                data : {event_id : event_id},
                success : function (collection, response) {
                    if(!event_id) return;
                    _.each(collection.models, function(model) {
                        self.deleteClientEvent(model);
                    });
                    $("#clients_data").empty();
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        deleteClientEvent : function(model) {
            $("#add_client").show();
            model.destroy({
                wait :true,
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        sendWorkoutEmail : function(id, client_id, method, to_client_only) {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id =  id;
            data.client_id = client_id || app.options.user_id;
            data.view = 'Programs';
            data.method = method;
            data.to_client_only = to_client_only;
            $.fitness_helper.sendEmail(data);
        },
        
        route_back_url : function() {
            var url = app.options.back_url;
            window.location = url;
        },
        
        add_template : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'programs_templates';
            var task = 'import_pr_temp';
            var table = '';
            data.id = app.options.pr_temp_id;
            data.item_id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                app.controller.navigate("!/form_view/" + id, true);
            });
        },
        
        add_templates : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'programs_templates';
            var task = 'import_pr_temp';
            var table = '';
            data.id = app.options.pr_temp_id;
            data.item_id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                
            });
        },
        
        is_bio_assessment : function(name) {
            var result = false;
            if(name && (name.toLowerCase().indexOf("bio") > -1)) {
                result = true;
            }
            return result;
        }
    });

    return Controller;
});