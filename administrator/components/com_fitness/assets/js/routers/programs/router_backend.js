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
        'views/programs/backend/form_details',
        'views/programs/backend/form_trainer',
        'views/programs/backend/form_clients',
        'views/programs/backend/list',
        'views/programs/backend/list_header_container'
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
        Form_details_view,
        Form_trainer_view,
        Form_event_clients_view,
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
            "!/unpublished_list": "unpublished_list", 
            "!/update_list": "update_list", 
            
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
                this.load_form_view(new Item_model(
                 /*      
                {
                    title : '5',
                    session_type : '26',
                    session_focus : '481',
                    starttime : '2014-03-13 04:30:00',
                    endtime : '2014-03-13 05:00:00',
                    location : '1',
                    frontend_published : '0',
                    published : '1',
                    trainer_id : '488'
                }
                */        
                ));
                return;
            }
            
            var self = this;
            app.models.item.set({id : id});
            app.models.item.fetch({
                data : {state : 1},
                success: function (model, response) {
                    self.load_form_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            })

        },
        
        load_form_view : function(model) {
            $("#header_wrapper").html(new Main_menu_view({model : model}).render().el);

            new Form_details_view({el : $("#details_wrapper"), model : model});
            
            new Form_trainer_view({el : $("#trainer_data_wrapper"), model : model});
            
            if(model.get('id')) {
                new Form_event_clients_view({el : $("#clients_data_wrapper"), model : model});
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
            app.models.request_params.set({page : 1, current_page : 'list',  published : 1, uid : app.getUniqueId()});
            
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

    });

    return Controller;
});