define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/items',
        'models/programs/item',
        'models/programs/request_params_items',
        'models/programs/favourite',
        'views/programs/frontend/menus/submenu_list',
        'views/programs/frontend/menus/submenu_item',
        'views/programs/frontend/menus/submenu_form',
        'views/programs/frontend/list',
        'views/programs/select_filter_block',
        'views/programs/frontend/item',
        'views/programs/frontend/form',
        'views/programs/backend/comments_block'
        
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Item_model,
        Request_params_items_model,
        Favourite_model,
        Submenu_list_view,
        Submenu_item_view,
        Submenu_form_view,
        List_view,
        Select_filter_block_view,
        Item_view,
        Form_view,
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
                        
            app.models.item = new Item_model({});
            
            app.collections.items = new Items_collection();
            
            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //
            
            app.models.request_params = new Request_params_items_model({business_profile_id : business_profile_id, current_page : 'my_workouts'});
            app.models.request_params.bind("change", this.get_items, this);
        },

        routes: {
            "": "my_workouts", 
            "!/my_workouts": "my_workouts", 
            "!/workout_programs": "workout_programs", 
            "!/my_favourites": "my_favourites", 
            "!/trash_list": "trash_list", 
            "!/item_view/:id": "item_view",
            "!/form_view/:id": "form_view",
            "!/my_favourites" : "my_favourites",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
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
        
        my_workouts : function() {
            app.models.request_params.set({page : 1, current_page : 'my_workouts', published : '1', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_workouts_link").addClass("active_link");
        },
        
        workout_programs : function() {
            app.models.request_params.set({page : 1, current_page : 'workout_programs', published : '1', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#workout_programs_link").addClass("active_link");
        },
        
        trash_list : function() {
            app.models.request_params.set({page : 1, current_page : 'trash_list', published : '-2', frontend_published : '2',  uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_workouts_link").addClass("active_link");
        },
        
        my_favourites : function () {
            app.models.request_params.set({page : 1, current_page : 'my_favourites', published : '1', uid : app.getUniqueId()});
            
            this.list_actions();
            
            $("#my_favourites_link").addClass("active_link");
        },
        
        
        list_actions : function () {
            $("#submenu_container").html(new Submenu_list_view({model : app.models.request_params}).render().el);
            
            this.connectSelectFilter();
            
            $(".menu_link").removeClass("active_link");
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        set_params_model : function() {
            app.collections.items.reset();
            app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
       
        update_list : function() {
            app.models.request_params.set({ uid : app.getUniqueId()});
        },
        
        connectSelectFilter : function() {
            new Select_filter_block_view({el : $("#filters_container"), model : app.models.request_params, block_width : '250px', not_show : ['locations']});
        },
        
        
        
        edit_allowed : function(model) {
            var access = false;

            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            var appointment = model.get('title');
            var status = model.get('status');
            
            // if ‘COMPLETE', 'INCOMPLETE' or 'NOT ATTEMPTED’,
            if(status == '6' || status == '7' || status == '8'  || status == '10') {
                return false;
            }
            
            if(user_id == created_by) {
                access = true;
            }
            
            //'Resistance Workout', 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
                access = true;
            }
            
            return access;
        },
        
        show_allowed : function(model) {
            var access = false;

            var appointment = model.get('title');
  
            
            //'Resistance Workout', 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
                access = true;
            }
            
            return access;
        },
        
        is_item_owner : function(model) {
            var access = false;
            
            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            
            if(user_id == created_by) {
                access = true;
            }
            return access;
        },
        
        delete_allowed : function(model) {
            var access = false;

            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            
            if(user_id == created_by) {
                access = true;
            }

            return access;
        },
        
        status_change_allowed : function(model) {
            var access = false;
            
            var user_id = app.options.user_id;
            var created_by = model.get('owner');
            var appointment = model.get('title');
            var status = model.get('status');
            
            // if ‘COMPLETE', 'INCOMPLETE' or 'NOT ATTEMPTED’,
            if(status == '6' || status == '7' || status == '8' || status == '10') {
                return false;
            }
            
            if(user_id == created_by) {
                return true;
            }

            //'Resistance Workout' and 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
                return true;
            }
           
            return access;
        },
        
        item_view : function(id) {
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model), show_allowed : self.show_allowed(model), status_change_allowed : self.status_change_allowed(model), delete_allowed : self.delete_allowed(model)});
            
                    $("#submenu_container").html(new Submenu_item_view({model : model, request_params_model : app.models.request_params}).render().el);

                    $("#main_container").html(new Item_view({model : model, request_params_model : app.models.request_params}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
            
            this.hideFilters();
        },
        
        hideFilters : function() {
            $("#filters_container").empty();
        },
        
        connectStatus : function(model, view) {
            var id = model.get('client_item_id');
            if(!id) {
                return;
            }
            var status = model.get('status');

            var options = _.extend({}, app.options.status_options);
            if(id) {
                var status_change_allowed = model.get('status_change_allowed');
                
                if(!parseInt(model.get('frontend_published'))) {
                    options.status_button = 'status_button_not_active';
                }
                
                if(status_change_allowed == false) {
                    options.status_button = 'status_button_not_active';
                }
                
                options.model = model;
                
                var status_obj = $.status(options);

                view.find("#status_button_place_" + id).html(status_obj.statusButtonHtml(id, status));

                status_obj.run();
            }
        },
        
        connectComments : function(model, view) {
            if(model.get('id')) {
                new Comments_block_view({el : view.find("#comments_block"), model : model, read_only : true});
            }
        },
        
        add_favourite : function(id) {
            var favourite_model = new Favourite_model({id : id})
            favourite_model.save(null, {
                success: function (model) {
                    model.trigger('save');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

        remove_favourite : function(id) {
            var favourite_model = new Favourite_model({id : id})
            var self = this;
            favourite_model.destroy({
                success: function (model) {
                    model.trigger('detroy');
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        form_view : function(id) {
            $("#filters_container").empty();
            if(!parseInt(id)) {
                this.load_form_view(new Item_model());
                return;
            }
            
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                wait : true,
                success: function (model, response) {
                    if(self.edit_allowed(model)) {
                        self.load_form_view(model);
                    } else {
                        self.navigate("!/workout_programs", true);
                    }
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })
        },
        
        load_form_view : function(model) {

            $("#submenu_container").html(new Submenu_form_view({model : model, request_params_model : app.models.request_params}).render().el);
            
            new Form_view({el : $("#main_container"), model : model});
        },

    });

    return Controller;
});