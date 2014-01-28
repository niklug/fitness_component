define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'jwplayer', 
        'jwplayer_key',
        'collections/recipe_database/recipes',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'models/recipe_database/recipe',
        'views/recipe_database/frontend/menus/submenu_my_recipes',
        'views/recipe_database/frontend/menus/submenu_trash_list',
        'views/recipe_database/frontend/menus/submenu_my_recipe_item',
        'views/recipe_database/frontend/recipe_database_list',
        'views/recipe_database/frontend/latest_recipes/list',
        'views/recipe_database/frontend/recipe_database_item'
], function (
        $,
        _,
        Backbone,
        app,
        jwplayer,
        jwplayer_key,
        Recipes_collection,
        Get_recipe_params_model,
        Recipe_model,
        Submenu_my_recipes_view,
        Submenu_trash_list_view,
        Submenu_my_recipe_item_view,
        Recipe_database_list_view,
        Latest_recipes_view,
        Recipe_item_view
    ) {

    var Controller = Backbone.Router.extend({
        
        initialize: function(){
            // history
            this.routesHit = 0;
            Backbone.history.on('route', function() { this.routesHit++; }, this);
            //
            
            app.collections.recipes = new Recipes_collection();
            
            app.models.get_recipe_params = new Get_recipe_params_model();
            
            app.models.get_recipe_params.bind("change", this.get_database_recipes, this);
            
            //latest recipes
            app.collections.recipes_latest = new Recipes_collection();
            app.models.get_recipe_params_latest = new Get_recipe_params_model();
            //
            app.models.recipe = new Recipe_model();
            
        },

        routes: {
            "": "my_recipes", 
            "!/": "my_recipes", 
            "!/my_recipes": "my_recipes",
            "!/recipe_database": "recipe_database",
            "!/my_favourites" : "my_favourites",
            "!/trash_list" : "trash_list",
            "!/nutrition_recipe/:id" : "nutrition_recipe",
            "!/nutrition_database/nutrition_recipe/:id" : "nutrition_database_recipe",
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
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
        
        get_recipes_latest : function() {
            app.models.get_recipe_params_latest.set({sort_by : 'created', order_dirrection : 'DESC', limit : 15});
            app.collections.recipes_latest.reset();
            app.collections.recipes_latest.fetch({
                data : app.models.get_recipe_params_latest.toJSON(),
                success: function (collection, response) {
                    $("#recipes_latest_wrapper").html(new Latest_recipes_view({collection : collection}).render().el);
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        set_recipes_model : function() {
            app.collections.recipes.reset();
            app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
        },
        
        my_favourites : function () {
            this.recipe_pages_actions();
            
            $("#my_favourites_link").addClass("active_link");

            app.models.get_recipe_params.set({page : 1, current_page : 'my_favourites', state : 1});
         },

        my_recipes : function () {
            this.recipe_pages_actions();
            
            $("#recipe_submenu").html(new Submenu_my_recipes_view().render().el);
            
            app.models.get_recipe_params.set({page : 1, current_page : 'my_recipes', state : 1});
            
            $("#my_recipes_link").addClass("active_link");
        },
        
        recipe_database : function () {
            this.recipe_pages_actions();
            
            app.models.get_recipe_params.set({page : 1,current_page : 'recipe_database', state : 1});
            
            $("#recipe_database_link").addClass("active_link");
        },
        
        trash_list : function() {
            this.recipe_pages_actions();
            
            $("#recipe_submenu").html(new Submenu_trash_list_view().render().el);
            
            app.models.get_recipe_params.set({page : 1, current_page : 'trash_list', state : '-2'});
            
            $("#my_recipes_link").addClass("active_link");
        },
        
        recipe_pages_actions : function () {
            this.common_actions();

            $("#recipe_main_container").html(new Recipe_database_list_view({collection : app.collections.recipes}).render().el);

            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);

            app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            
            this.get_recipes_latest();
        },

        nutrition_recipe : function(id) {
            var self = this;
            app.models.recipe.fetch({
                wait : true,
                data : {id : id},
                success: function (model, response) {
                    $("#recipe_main_container").html(new Recipe_item_view({model : model}).render().el);
                    self.load_recipe_submenu();
                    self.loadVideoPlayer();
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
            }
        },
        
        loadVideoPlayer : function() {
            var no_video_image_big = app.options.no_video_image_big;

            var video_path = app.models.recipe.get('video');

            var base_url = app.options.base_url;

            var imageType = /no_video_image.*/;  

            if (!video_path.match(imageType) && video_path) {  

                jwplayer('recipe_video').setup({
                    file: base_url + video_path,
                    image: "",
                    height: 340,
                    width: 640,
                    autostart: true,
                    mute: true,
                    controls: false,
                    events: {
                        onReady: function () { 
                            var self = this;
                            setTimeout(function(){
                                self.pause();
                                self.setMute(false);
                                self.setControls(true);
                            },3000);
                        }
                    }
                });
            } else {
                $("#recipe_video").css('background-image', 'url(' +  no_video_image_big + ')');
            }
        },
        
       
        common_actions : function() {
            $("#recipe_submenu").empty();
            $(".block").hide();
            $(".plan_menu_link").removeClass("active_link");
        }
    
    });

    return Controller;
});