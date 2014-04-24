define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/exercise_library',
        'models/exercise_library/exercise_library_item',
        'models/exercise_library/request_params_items',
        'models/exercise_library/favourite_exercise',
        'models/programs/exercises/item',
        'views/exercise_library/select_filter_block',
        'views/exercise_library/frontend/list',
        'views/exercise_library/frontend/menus/submenu_exercise_database',
        'views/exercise_library/frontend/menus/submenu_item',
        'views/exercise_library/frontend/menus/submenu_form',
        'views/exercise_library/frontend/popular_exercises/list',
        'views/exercise_library/frontend/item',
        'views/exercise_library/frontend/form',
        
        'jquery.backbone_video_upload',
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
        Favourite_exercise_model,
        Exercise_model,
        Select_filter_block_view,
        List_view,
        Submenu_exercise_database_view,
        Submenu_item_view,
        Submenu_form_view,
        Popular_exercises_view,
        Item_view,
        Form_view
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
            
            app.models.request_params = new Request_params_items_model();
            app.models.request_params.bind("change", this.get_items, this);
            
            //popolar exercises
            app.collections.popular_items = new Exercise_library_collection();
            app.models.request_params_popular = new Request_params_items_model();
            
            
        },

        routes: {
            "": "my_exercises", 
            "!/exercise_database": "exercise_database", 
            "!/my_exercises": "my_exercises",
            "!/my_favourites" : "my_favourites",
            "!/trash_list" : "trash_list",
            "!/add_favourite/:id" : "add_favourite",
            "!/remove_favourite/:id" : "remove_favourite",
            "!/item_view/:id" : "item_view",
            "!/form_view/:id": "form_view", 
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate("", {trigger:true, replace:true});
            }
        },

        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                success: function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        

        exercise_database : function() {
            app.models.request_params.set({page : 1, current_page : 'exercise_database',  state : 1, uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#exercise_database_link").addClass("active_link");
        },
        
        my_exercises : function() {
            app.models.request_params.set({page : 1, current_page : 'my_exercises',  state : 1, uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_exercises_link").addClass("active_link");
        },
        
        my_favourites : function () {
            app.models.request_params.set({page : 1, current_page : 'my_favourites', state : 1, uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_favourites_link").addClass("active_link");
        },
        
        trash_list : function() {
            app.models.request_params.set({page : 1, current_page : 'trash_list', state : '-2', uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_exercises_link").addClass("active_link");
        },
        
        list_actions : function () {
            $("#submenu_container").html(new Submenu_exercise_database_view({model : app.models.request_params}).render().el);
            
            $(".plan_menu_link").removeClass("active_link");
            
            this.connectSelectFilter();
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);

            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
            
            this.connecPopular();
        },
        
        connectSelectFilter : function() {
            new Select_filter_block_view({el : $("#filters_container"), model : app.models.request_params, block_width : '152.5px', not_show : ['mechanics_type']});
        },
        
        connecPopular : function() {
            $("#right_side").html(new Popular_exercises_view({collection : app.collections.popular_items, model : app.models.request_params_popular}).render().el);
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        add_favourite : function(id) {
            var favourite_exercise_model = new Favourite_exercise_model({id : id})
            favourite_exercise_model.save(null, {
                success: function (model) {
                    model.trigger('save');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        remove_favourite : function(id) {
            var favourite_exercise_model = new Favourite_exercise_model({id : id})
            var self = this;
            favourite_exercise_model.destroy({
                success: function (model) {
                    model.trigger('detroy');
                    var current_page = app.models.request_params.get('current_page');
                    if(current_page == 'my_favourites') {
                        self.remove_list_item(id);
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        remove_list_item : function(id) {
            $(".exercise_list_item_wrapper[data-id=" + id + "]").fadeOut();
        },
        
        trash_exercise : function(id) {
            var model = app.collections.items.get(id);
            var self = this;
            model.save({state : '-2'}, {
                success: function (model) {
                    self.remove_list_item(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        delete_exercise : function(id) {
            var model = app.collections.items.get(id);
            var self = this;
            model.destroy({
                success: function (model) {
                    self.remove_list_item(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        restore_exercise : function(id) {
            var model = app.collections.items.get(id);
            var self = this;
            model.save({state : '1'}, {
                success: function (model) {
                    self.remove_list_item(id);
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        item_view : function(id) {
            $("#filters_container").empty();
            var self = this;
            app.models.exercise_library_item.set({id : id});
            app.models.exercise_library_item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model)});
                    $("#submenu_container").html(new Submenu_item_view({model : model, request_params_model : app.models.request_params}).render().el);
                    
                    $("#main_container").html(new Item_view({model : model, request_params_model : app.models.request_params}).render().el);
                    
                    self.connecPopular();
                    
                    var video_path = app.models.exercise_library_item.get('video');
                    $.fitness_helper.loadVideoPlayer(video_path, app, 400, 700, 'exercise_video');
                    
                    self.increaseViewed(model);
                    
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        increaseViewed : function(model) {
            var viewed =  parseInt(model.get('viewed')) + 1;
            model.save({viewed : viewed}, {
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
               
        form_view : function(id) {
            $("#filters_container").empty();
            if(!parseInt(id)) {
                this.load_form_view(new Exercise_library_item_model());
                return;
            }
            
            var self = this;
            app.models.exercise_library_item.set({id : id});
            app.models.exercise_library_item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    if(self.edit_allowed(model)) {
                        self.load_form_view(model);
                    } else {
                        self.navigate("!/my_exercises", true);
                    }
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        load_form_view : function(model) {
            $("#submenu_container").html(new Submenu_form_view({model : model, request_params_model : app.models.request_params}).render().el);
            
            $("#main_container").html(new Form_view({model : model}).render().el);
            
            new Select_filter_block_view({el : $("#select_filter_wrapper"), model : model, block_width : '140px'});
        },
        
        edit_allowed : function(model) {
            var access = false;
            var id = model.get('id');
            var client_id = app.options.client_id;
            var created_by = model.get('created_by');
            
            if(client_id == created_by) {
                access = true;
            }
           
            return access;
        },
        
        add_event_exercise : function(id) {
            var model = new Exercise_model();
            
            var video_model = new Exercise_library_item_model({id : id});
            var self = this;
            video_model.fetch({
                data : {state : 1},
                success: function (video_model, response) {
                    model.clear();

                    model.set({
                        id : app.options.exercise_id || null,
                        item_id : app.options.event_id,
                        video_id : id,
                        title : video_model.get('exercise_name')
                    });
                    model.save(null, {
                        success: function (model, response) {
                            if(app.options.exercise_id ) {
                                self.route_program();
                            }
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
        
        route_program : function() {
            var url = app.options.back_url;
            window.location = url;
        }
        
    });

    return Controller;
});