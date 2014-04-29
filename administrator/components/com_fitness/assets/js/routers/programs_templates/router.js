define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs_templates/items',
        'collections/programs_templates/template_clients',
        'collections/programs_templates/exercises/items',
        'models/programs_templates/item',
        'models/programs_templates/request_params_items',
        'models/programs_templates/exercises/item', 
        'views/programs_templates/backend/form_container',
        'views/programs_templates/backend/menus/main_menu',
        'views/programs_templates/backend/form_details',
        'views/programs_templates/backend/form_trainer',
        'views/programs_templates/backend/form_clients',
        'views/programs_templates/backend/form_workout_instructions',
        'views/programs_templates/backend/list',
        'views/programs_templates/backend/list_header_container',
        'views/programs/exercises/list',
        'views/programs_templates/backend/comments_block'
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Template_clients_collection, 
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
        Comments_block_view
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
            
            app.models.request_params = new Request_params_items_model({business_profile_id : business_profile_id});
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
              this.navigate('', {trigger:true, replace:true});t
            }
        },

        form_view : function(id) {
            $("#main_container").html(new Form_container_view().render().el);
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
            model.set({edit_allowed : this.edit_allowed(model)});
            model.set({view_allowed : this.view_allowed(model)});
            model.set({is_owner : this.is_owner(model)});
            $("#header_wrapper").html(new Main_menu_view({model : model}).render().el);

            new Form_details_view({el : $("#details_wrapper"), model : model});
            
            new Form_trainer_view({el : $("#trainer_data_wrapper"), model : model});
            
            if(model.get('id')) {
                new Form_event_clients_view({el : $("#clients_data_wrapper"), model : model});
                
                new Form_event_workout_instructions({el : $("#workout_instuctions_wrapper"), model : model});
                
                var readonly_exercises = false;
                
                if(!model.get('view_allowed')) {
                    readonly_exercises = true;
                }
                
                new Exercises_list_view({
                    el : $("#exercises_list"),
                    model : model,
                    exercise_model : Exercise_model,
                    exercises_collection : Exercises_collection,
                    readonly : readonly_exercises,
                    title : 'WORKOUT DETAILS'
                });
                
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
            app.models.request_params.set({page : 1, current_page : 'all_list', state : '*',  uid : app.getUniqueId()});
            
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
            app.models.request_params.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            this.list_actions();
        },
        
        edit_allowed : function(model) {
            var access = false;
            
            var created_by = model.get('created_by');

            var business_profile_id = model.get('business_profile_id');

            var is_superuser = app.options.is_superuser;
            
            var user_id = app.options.user_id;
            
            var author_only = parseInt(model.get('access'));
            
            var is_trainer_administrator = app.options.is_trainer_administrator;
            

            // if trainer administrator //
            if(business_profile_id == app.options.business_profile_id) {
                access = true;
            }
            // //
            
            // if superuser
            if(is_superuser) {
                access = true;
            }
            
            
            //author access
            if(author_only && (user_id != created_by) && !is_superuser && !is_trainer_administrator) {
                access = false;
            }
           
            return access;
        },
        
        is_owner : function(model) {
            var access = false;
            var created_by = model.get('created_by');
            var user_id = app.options.user_id;
            if(created_by == user_id) {
                access = true;
            }
            return access;
        },
        
        view_allowed : function(model) {
            var access = false;
            if(this.is_owner(model) || app.options.is_trainer_administrator || app.options.is_superuser) {
                access = true;
            }
            return access;
        },
        
        copy_item : function(id) {
            var self = this;
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'programs_templates';
            var task = 'copyProgramTemplate';
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
            var template_clients_collection = new Template_clients_collection();
            var self = this;
            var template_id = model.get('id');
            template_clients_collection.fetch({
                data : {item_id : template_id},
                success : function (collection, response) {
                    if(!template_id) return;
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

        route_program : function() {
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
            data.id = id;
            data.item_id = app.options.event_id;
            $.AjaxCall(data, url, view, task, table, function(output){
                self.route_program();
            });
        },
        
        search_program : function(id) {
            var url = app.options.base_url_relative + 'index.php?option=com_fitness&view=programs';
            url += '&pr_temp_id=' + id;
            url += '&back_url=' + encodeURIComponent(app.options.base_url_relative + 'index.php?option=com_fitness&view=programs_templates');
            window.location = url;
        }

    });

    return Controller;
});