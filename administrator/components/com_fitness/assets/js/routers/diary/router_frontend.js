define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/diaries',
        'models/diary/request_params_diaries',
        'views/diary/frontend/menus/submenu_list',
        'views/diary/frontend/menus/submenu_trash_list',
        'views/diary/frontend/list'
  
], function (
        $,
        _,
        Backbone,
        app,
        Diaries_collection,
        Request_params_diaries_model,
        Submenu_list_view,
        Submenu_trash_list_view,
        List_view
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
        }
            
    
    });

    return Controller;
});