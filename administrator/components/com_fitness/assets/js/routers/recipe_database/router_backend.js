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
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        list_view : function() {
            //show all
            app.models.request_params.set({page : 1, current_page : 'all_list', published : '*', uid : app.getUniqueId()});
            
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
        
        get_database_recipes : function() {
            app.collections.recipes.reset();
            app.collections.recipes.fetch({
                data : app.models.get_recipe_params.toJSON(),
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        get_ingredients : function() {
            app.collections.ingredients.reset();
            app.collections.ingredients.fetch({
                data : app.models.request_params_ingredients.toJSON(),
                success: function (collection, response) {
                    //console.log(collection);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        my_favourites : function () {
            this.recipe_pages_actions();
            
            $("#my_favourites_link").addClass("active_link");

            app.models.get_recipe_params.set({page : 1, current_page : 'my_favourites', state : 1, uid : app.getUniqueId()});
        },

        my_recipes : function () {
            this.recipe_pages_actions();
            
            $("#recipe_submenu").html(new Submenu_my_recipes_view().render().el);
            
            app.models.get_recipe_params.set({page : 1, current_page : 'my_recipes', filter_options : '',  recipe_variations_filter_options : '', state : 1, uid : app.getUniqueId()});
            
            $("#my_recipes_link").addClass("active_link");
        },
        
        recipe_database : function () {
            this.recipe_pages_actions();
            
            app.models.get_recipe_params.set({page : 1,current_page : 'recipe_database', state : 1, filter_options : '',  recipe_variations_filter_options : '', uid : app.getUniqueId()});
            
            $("#recipe_database_link").addClass("active_link");
        },
        
        trash_list : function() {
            this.recipe_pages_actions();
            
            $("#recipe_submenu").html(new Submenu_trash_list_view().render().el);
            
            app.models.get_recipe_params.set({page : 1, current_page : 'trash_list', state : '-2', filter_options : '',  recipe_variations_filter_options : '', uid : app.getUniqueId()});
            
            $("#my_recipes_link").addClass("active_link");
        },
        
        recipe_pages_actions : function () {
            this.common_actions();

            $("#recipe_main_container").html(new Recipe_database_list_view({collection : app.collections.recipes}).render().el);

            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);

            app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            
            this.connecLatest();
        },
        
        connecLatest : function() {
            $("#recipes_latest_wrapper").html(new Latest_recipes_view({collection : app.collections.recipes_latest, model : app.models.get_recipe_params_latest}).render().el);
        },

        nutrition_recipe : function(id) {
            var self = this;
            app.models.recipe.fetch({
                wait : true,
                data : {id : id},
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model)});
                    $("#recipe_main_container").html(new Recipe_item_view({model : model}).render().el);
                    self.load_recipe_submenu();
                    var video_path = model.get('video');
                    $.fitness_helper.loadVideoPlayer(video_path, app, 340, 640, 'recipe_video');
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },
        
        nutrition_database_recipe : function(id) {
            var self = this;
            app.models.recipe.fetch({
                wait : true,
                data : {id : id},
                success: function (model, response) {
                    model.set({edit_allowed : self.edit_allowed(model)});
                    $("#recipe_main_container").html(new Recipe_item_view({model : model}).render().el);
                    var video_path = model.get('video');
                    $.fitness_helper.loadVideoPlayer(video_path, app, 340, 640, 'recipe_video');
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            }); 
       },

        load_recipe_submenu : function() {
            var current_page = app.models.get_recipe_params.get('current_page');
            if(current_page == 'my_recipes') {
                $("#recipe_submenu").html(new Submenu_my_recipe_item_view({model : app.models.recipe}).render().el);
            } else if(current_page == 'recipe_database') {
                $("#recipe_submenu").html(new Submenu_recipe_database_item_view({model : app.models.recipe}).render().el);
            } else if (current_page == 'my_favourites') {
                $("#recipe_submenu").html(new Submenu_my_favourites_view({model : app.models.recipe}).render().el);
            } else if (current_page == 'trash_list') {
                $("#recipe_submenu").html(new Submenu_trash_item_view({model : app.models.recipe}).render().el);
            } else if (current_page == 'add_diary') {
                $("#recipe_submenu").html(new Submenu_add_diary_view({model : app.models.recipe}).render().el);
            } 
        },
               
        common_actions : function() {
            app.views.main_menu.show();
            $("#recipe_submenu").empty();
            $(".block").hide();
            $(".plan_menu_link").removeClass("active_link");
        },
        
        edit_recipe : function(id) {
            $("#recipe_submenu").html(new Submenu_edit_recipe_view({model : app.models.recipe}).render().el);
            
            if(!parseInt(id)) {
                new EditRecipeContainer_view({el : $("#recipe_main_container"), model : new Item_model()});
                return;
            }
            
            var self = this;
            app.models.recipe.fetch({
                wait : true,
                data : {id : id},
                success: function (model, response) {
                    if(self.edit_allowed(model)) {
                        new EditRecipeContainer_view({el : $("#recipe_main_container"), model : model});
                    } else {
                        self.navigate("!/my_recipes", true);
                    }
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },
        
        nutrition_database : function () {
            this.common_actions();

            $("#recipe_submenu").html(new Submenu_nutrition_database_list_view().render().el);
   
            $("#nutrition_database_link").addClass("active_link");
            
            $("#recipe_main_container").html(new Nutrition_database_list_view({collection : app.collections.ingredients}).render().el);
            
            app.models.request_params_ingredients.set({page : 1, search : '', state : 1, uid : app.getUniqueId()});
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_ingredients_model, this);

            app.models.pagination.bind("change:items_number", this.set_ingredients_model, this);
        },
        
        set_ingredients_model : function() {
            app.collections.ingredients.reset();
            app.models.request_params_ingredients.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        add_ingredient : function () {
            app.models.ingredient = new Ingredient_model();
  
            $("#recipe_submenu").html(new Submenu_nutrition_database_form_view({model : app.models.ingredient}).render().el);
            
            $("#recipe_main_container").html(new Nutrition_database_form_view({model : app.models.ingredient}).render().el);

            $("#add_ingredient_form").validate();
        },
        
        add_diary : function(id) {
           app.models.get_recipe_params.set({current_page : 'add_diary'});
           var self = this;
            app.models.recipe.fetch({
                data : {id : id},
                success: function (model, response) {
                    $("#recipe_main_container").html(new Recipe_item_view({model : model}).render().el);
                    self.load_recipe_submenu();
                    var video_path = model.get('video');
                    $.fitness_helper.loadVideoPlayer(video_path, app, 340, 640, 'recipe_video');
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            }); 
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
    
    });

    return Controller;
});