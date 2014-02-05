define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/diaries',
        'collections/nutrition_plan/targets',
        'models/diary/request_params_diaries',
        'models/diary/diary',
        'models/diary/active_plan_data',
        'models/diary/diary_days',
        'views/diary/frontend/menus/submenu_list',
        'views/diary/frontend/menus/submenu_trash_list',
        'views/diary/frontend/menus/submenu_form',
        'views/diary/frontend/menus/submenu_item',
        'views/diary/frontend/list',
        'views/diary/frontend/form',
        'views/diary/frontend/item'

  
], function (
        $,
        _,
        Backbone,
        app,
        Diaries_collection,
        Targets_collection,
        Request_params_diaries_model,
        Diary_model,
        Active_plan_data_model,
        Diary_days_model,
        Submenu_list_view,
        Submenu_trash_list_view,
        Submenu_form_view,
        Submenu_item_view,
        List_view,
        Form_view,
        Item_view
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
            //
            app.collections.diaries = new Diaries_collection();
            app.models.request_params_diaries = new Request_params_diaries_model();
            app.models.request_params_diaries.bind("change", this.get_diaries, this);
            //
            app.collections.targets = new Targets_collection();
            
            app.models.active_plan_data = new Active_plan_data_model();
            app.models.diary_days = new Diary_days_model();
        },

        routes: {
            "": "list_view", 
            "!/list_view": "list_view", 
            "!/trash_list" : "trash_list",
            "!/create_item" : "create_item",
            "!/item_view/:id" : "item_view"
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        get_diaries : function() {
            app.collections.diaries.reset();
            app.collections.diaries.fetch({
                data : app.models.request_params_diaries.toJSON(),
                success: function (collection, response) {
                    //console.log(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        
        list_view : function() {
            $("#submenu_container").html(new Submenu_list_view().render().el);
            
            app.models.request_params_diaries.set({page : 1, current_page : 'list',  state : 1, uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        set_diaries_model : function() {
            app.collections.diaries.reset();
            app.models.request_params_diaries.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        trash_list : function() {
            $("#submenu_container").html(new Submenu_trash_list_view().render().el);
            
            app.models.request_params_diaries.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        list_actions : function () {
            $("#main_container").html(new List_view({collection : app.collections.diaries}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_diaries_model, this);

            app.models.pagination.bind("change:items_number", this.set_diaries_model, this);
        },
        
        create_item : function() {
            $.when (
                app.models.active_plan_data.fetch({
                    data: {client_id : app.options.client_id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.models.diary_days.fetch({
                    data: {client_id : app.options.client_id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
                    
            ).then (function() {
                var id = app.models.active_plan_data.get('id');
                app.collections.targets.fetch({
                    data: {id : id, client_id : app.options.client_id},
                    success : function(collection, response) {
                        $("#submenu_container").html(new Submenu_form_view({model : new Diary_model(), collection : collection}).render().el);
                        new Form_view({el : $("#main_container"), collection : collection}).render();
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            })
        },
        
        item_view : function(id) {
            app.models.diary = new Diary_model({id : id});
            
            $.when (
                app.models.active_plan_data.fetch({
                    data: {client_id : app.options.client_id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.models.diary_days.fetch({
                    data: {client_id : app.options.client_id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),
                
                app.models.diary.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
                    
            ).then (function() {
                var id = app.models.active_plan_data.get('id');
                app.collections.targets.fetch({
                    data: {id : id, client_id : app.options.client_id},
                    success : function(collection, response) {
                        app.views.diary = new Item_view({el : $("#main_container"), model : app.models.diary, collection : collection}).render();
                        $("#submenu_container").html(new Submenu_item_view({model : app.models.diary}).render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            })


            /*

            window.app.item_view = new window.app.Item_view({model : window.app.item_model, 'item_id' : id});
            */

        },
            
    
    });

    return Controller;
});