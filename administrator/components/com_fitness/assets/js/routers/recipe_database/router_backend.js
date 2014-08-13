define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/recipe_database/recipes',
        'models/recipe_database/recipe',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/recipe_database/backend/search_block',
        'views/recipe_database/backend/list',
        'views/recipe_database/backend/form',
        'views/recipe_database/backend/menus/form_menu',
        'jwplayer', 
        'jwplayer_key',
        'jquery.validate'
  
], function (
        $,
        _,
        Backbone,
        app,
        Items_collection,
        Item_model,
        Request_params_items_model,
        Search_block_view,
        List_view,
        Form_view,
        Form_menu_view,
        jwplayer,
        jwplayer_key       
        
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
            
            app.collections.items = new Items_collection();
            
            app.models.item = new Item_model();
            
            //business logic
            var business_profile_id = null;
            if(!app.options.is_superuser) {
                business_profile_id = app.options.business_profile_id;
            }
            //
            
            app.models.request_params = new Request_params_items_model({});
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
        
        list_view : function() {
            //show all
            app.models.request_params.set({page : 1, current_page : 'all_list', state : '*', uid : app.getUniqueId()});
            
            this.list_actions();
        },

        get_items : function() {
            var params = app.models.request_params.toJSON();
            app.collections.items.reset();
            app.collections.items.fetch({
                data : params,
                success : function (collection, response) {
                    console.log(collection.toJSON());
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        connectStatus : function(model, el) {
            var id = model.get('id');
            var status = model.get('status');
            var options = _.extend({}, app.options.status_options);
            
            if(!model.get('edit_allowed')) {
                options.status_button = 'status_button_not_active';
            }
            
            var target = "#status_button_place_" + id;

            var status_obj = $.status(options);

            el.find(target).html(status_obj.statusButtonHtml(id, status));
            
            
            status_obj.run();
        },

        update_list : function() {
            app.models.request_params.set({ uid : app.getUniqueId()});
        },
        
        
        list_actions : function () {
            $("#search_block").html(new Search_block_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_params_model, this);

            app.models.pagination.bind("change:items_number", this.set_params_model, this);
        },
        
        copy_recipe : function(recipe_id, update_list){
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'recipe_database';
            var task = 'copyRecipe';
            var table = '';

            data.id = recipe_id;
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                if(update_list) {
                    self.update_list();
                }
            });
        },
        
        form_view : function(id) {
            if(!parseInt(id)) {
                this.load_form_view(new Item_model({edit_allowed : true}));
                return;
            }

            var model = new Item_model({id : id});
            var self = this;
            model.fetch({
                wait : true,
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model)});
                    app.collections.items.add(model);
                    self.load_form_view(model);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });
        },
        
        load_form_view : function(model) {
            $("#search_block").html(new Form_menu_view({model : model}).render().el);
            $("#main_container").html(new Form_view({collection : app.collections.items, model : model}).render().el);
        },
        
        edit_allowed : function(model) {
            var access = true;
            var is_simple_trainer = app.options.is_simple_trainer;
            var is_trainer_administrator = app.options.is_trainer_administrator;
            var is_superuser = app.options.is_superuser;
            var created_by_superuser = model.get('created_by_superuser');
            var is_associated_trainer = model.get('is_associated_trainer');
            var created_by = model.get('created_by');
            var user_id = app.options.user_id;
            
            if(!is_superuser && parseInt(created_by_superuser)) {
                access = false;
            }
            
            if(is_simple_trainer && (created_by != user_id)) {
                access = false;
            }
            
            if(is_associated_trainer) {
                access = true;
            }
            
            if(model.isNew()) {
                access = true;
            }
           
            return access;
        }

        
    
    });

    return Controller;
});