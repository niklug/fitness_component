define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/diaries',
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
            app.collections.items = new Diaries_collection();
            app.models.request_params_diaries = new Request_params_diaries_model();
            app.models.request_params_diaries.bind("change", this.get_diaries, this);

            //active plan data
            app.models.active_plan_data = new Active_plan_data_model();
            app.models.active_plan_data.fetch({
                data : {client_id : app.options.client_id},
                success : function (model, response) {
                    //console.log(model.toJSON());
                },
                error : function (model, response) {
                    alert(response.responseText);
                }
            });
            
            
            //diary days
            app.models.diary_days = new Diary_days_model();
            app.models.diary_days.fetch({
                data : {client_id : app.options.client_id},
                success : function (model, response) {
                    //console.log(model.toJSON());
                },
                error : function (model, response) {
                    alert(response.responseText);
                }
            })

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
            app.collections.items.reset();
            app.collections.items.fetch({
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
            
            app.models.request_params_diaries.set({page : 1, current_page : 'list',  state : 1});
            
            this.list_actions();
        },
        
        set_diaries_model : function() {
            app.collections.items.reset();
            app.models.request_params_diaries.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        trash_list : function() {
            $("#submenu_container").html(new Submenu_trash_list_view().render().el);
            
            app.models.request_params_diaries.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        list_actions : function () {
            $("#main_container").html(new List_view({collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_diaries_model, this);

            app.models.pagination.bind("change:items_number", this.set_diaries_model, this);
        },
        
        create_item : function() {
            $("#submenu_container").html(new Submenu_form_view({model : new Diary_model()}).render().el);
            $("#main_container").empty();
        },
        
        item_view : function(id) {
            var model = app.collections.items.get(id);
            
            if(model) {
                this.load_item_view(model);
                return;
            }
            
            app.models.diary = new Diary_model({id : id});
            var self = this;
            app.models.diary.fetch({
                success : function (model, response) {
                    app.collections.items.add(model);
                    self.load_item_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        load_item_view : function(model) {
            $("#main_container").html(new Item_view({model : model}).render().el);
            $("#submenu_container").html(new Submenu_item_view({model : model}).render().el);
        }
            
    
    });

    return Controller;
});