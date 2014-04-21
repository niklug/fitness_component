define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/exercise_library',
        'models/exercise_library/exercise_library_item',
        'models/exercise_library/request_params_items',
        'models/programs/exercises/item',
        'models/programs_templates/exercises/item',
        'views/exercise_library/backend/form_container',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/backend/menus/main_menu',
        'views/exercise_library/backend/exercise_details',
        'views/exercise_library/backend/exercise_video',
        'views/exercise_library/backend/business_permissions',
        'views/exercise_library/backend/list',
        'views/exercise_library/backend/list_header_container',
        'jwplayer', 
        'jwplayer_key',
], function (
        $,
        _,
        Backbone,
        app,
        Exercise_library_collection,
        Exercise_library_item_model,
        Request_params_items_model,
        Program_exercise_model,
        Pr_tepm_exercise_model,
        Form_container_view,
        Select_filter_block_view,
        Main_menu_view,
        Exercise_details_view,
        Exercise_video_view,
        Business_permissions_view,
        List_view,
        List_header_container_view
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
                        
            app.models.exercise_library_item = new Exercise_library_item_model();
            
            app.collections.items = new Exercise_library_collection();
            
            //business logic
            var business_profiles = null;
            if(!app.options.is_superuser) {
                business_profiles = app.options.business_profile_id;
            }
            //
            
            app.models.request_params = new Request_params_items_model({business_profiles : business_profiles});
            app.models.request_params.bind("change", this.get_items, this);
        },

        routes: {
            "": "list_view", 
            "!/form_view/:id": "form_view", 
            "!/list_view": "list_view", 
            "!/trash_list": "trash_list", 
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
                this.load_form_view(new Exercise_library_item_model({edit_allowed : true}));
                return;
            }
            var self = this;
            app.models.exercise_library_item.set({id : id});
            app.models.exercise_library_item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model)});
                    self.load_form_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        load_form_view : function(model) {
            $("#header_wrapper").html(new Main_menu_view({model : model}).render().el);

            $("#exercise_details_wrapper").html(new Exercise_details_view({model : model}).render().el);
            
            var element_disabled = false;
            if(!this.edit_allowed(model)) {
                var element_disabled = true;
            }

            new Select_filter_block_view({el : $("#select_filter_wrapper"), model : model, block_width : '140px', element_disabled : element_disabled});

            if(model.get('id')) {
                $("#exercise_video_wrapper").html(new Exercise_video_view({model : model}).render().el);
            }

            new Business_permissions_view({el : $("#permissions_wrapper"), model : model});
        },
     
        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        list_view : function() {
            app.models.request_params.set({page : 1, current_page : 'list',  state : 1, uid : app.getUniqueId()});
            
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
            var access = true;
            var created_by = model.get('created_by');
            var created_by_superuser = model.get('created_by_superuser');
            var is_client_of_trainer = model.get('is_client_of_trainer');
            var is_trainer = app.options.is_trainer;
            var user_id = app.options.user_id;
            
            //trainers not allowed edit items created by Super Users
            if(is_trainer && created_by_superuser) {
                access = false;
            }
            
            if(is_trainer && (user_id != created_by)) {
                access = false;
            }
            // logged trainer; item created by client
            if(is_trainer && is_client_of_trainer) {
                access = true;
            }
           
            return access;
        },
        
        copy_exercise : function(id) {
            var self = this;
            app.models.exercise_library_item.set({id : id});
            app.models.exercise_library_item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    model.set({
                        id : null, 
                        created_by : app.options.user_id,
                        created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
                        status : '1',
                        my_exercise_clients : null, 
                        assessed_by : null,
                        viewed : null
                    });
                    
                    model.save(null, {
                        success: function (model, response) {
                            self.set_params_model();
                        },
                        error: function (model, response) {
                            alert(response.responseText);
                        }
                    });
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        add_event_exercise : function(id) {
            var model = new Program_exercise_model();
            
            if((/programs_templates/g).test(app.options.back_url)) {
                model = new Pr_tepm_exercise_model();
            }

            var video_model = app.collections.items.get(id);
            
            model.clear();
            
            model.set({
                id : app.options.exercise_id,
                video_id : id,
                title : video_model.get('exercise_name')
            });
            var self = this;
            model.save(null, {
                success: function (model, response) {
                    self.route_program();
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        add_event_exercises : function(id) {
            var model = new Program_exercise_model();
            
            if((/programs_templates/g).test(app.options.back_url)) {
                model = new Pr_tepm_exercise_model();
            }
            
            var video_model = app.collections.items.get(id);
            
            model.clear();
            
            model.set({
                id : null,
                item_id : app.options.event_id,
                video_id : id,
                title : video_model.get('exercise_name')
            });
            var self = this;
            model.save(null, {
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        route_program : function() {
            var url = app.options.back_url;
            window.location = url;
        }
    });

    return Controller;
});