define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/items',
        'models/programs/item',
        'models/programs/request_params_items',
        'views/programs/backend/form_container',
        'views/programs/backend/menus/main_menu',
        'views/programs/frontend/menus/submenu_list',
        'views/programs/backend/form_workout_instructions',
        'views/programs/frontend/list',
        'views/programs/select_filter_block'
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Item_model,
        Request_params_items_model,
        Form_container_view,
        Main_menu_view,
        Submenu_list_view,
        Form_event_workout_instructions,
        List_view,

        Select_filter_block_view
        
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
            "": "my_workouts", 
            "!/my_workouts": "my_workouts", 
            "!/workout_programs": "workout_programs", 
            "!/my_favourites": "my_favourites", 
            "!/trash_list": "trash_list", 
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
            
            if(user_id == created_by) {
                access = true;
            }
            
            //'Resistance Workout' and 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
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
            
            if(user_id == created_by) {
                return true;
            }

            //'Resistance Workout' and 'Cardio Workout'
            if(appointment == '3' || appointment == '4') {
                return true;
            }
           
            return access;
        }
        

    });

    return Controller;
});