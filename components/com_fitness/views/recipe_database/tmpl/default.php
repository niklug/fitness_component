<?php
/**
 * @version     1.0.0
 * @package     com_fitness
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Nikolay Korban <niklug@ukr.net> - http://
 */


// no direct access
defined('_JEXEC') or die;
?>

<div style="opacity: 1;" class="fitness_wrapper">
    <h2>RECIPE DATABASE</h2>
    
    <div id="recipe_mainmenu"></div>
    
    <div id="recipe_submenu"></div>
    
    <div id="recipe_main_container"></div>
    
</div>



<script type="text/javascript">
    
    (function($) {
       
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'recipes_db_table' : '#__fitness_nutrition_recipes',
            'recipe_types_db_table' : '#__fitness_recipe_types',
            'recipe_comments_db_table' : '#__fitness_nutrition_recipes_comments',
            'default_image' : 'administrator/components/com_fitness/assets/images/no_image.png'
        };
        
        // MODELS 
        Recipe_database_model = Backbone.Model.extend({


            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
            },
            
            checkLocalStorage : function() {
                if(typeof(Storage)==="undefined") {
                   return false;
                }
                return true;
            },
          
            setLocalStorageItem : function(name, value) {
                if(!this.checkLocalStorage) return;
                localStorage.setItem(name, value);
            },
            
            getLocalStorageItem : function(name) {
                var value = this.get(name);
                if(!this.checkLocalStorage) {
                    return value;
                }
                var store_value =  localStorage.getItem(name);
                if(!store_value) return value;
                return store_value;
            },
          
            setStatus : function(status) {
                var style_class;
                var text;
                switch(status) {
                    case '1' :
                        style_class = 'recipe_status_pending';
                        text = 'PENDING';
                        break;
                    case '2' :
                        style_class = 'recipe_status_approved';
                        text = 'APPROVED';
                        break;
                    case '3' :
                        style_class = 'recipe_status_notapproved';
                        text = 'NOT APPROVED';
                        break;
                   
                    default :
                        style_class = 'recipe_status_pending';
                        text = 'PENDING';
                        break;
                }
                var html = '<a style="cursor:default;" href="javascript:void(0)"  class="status_button ' + style_class + '">' + text + '</a>';
                return html;
            },
             
        });
        
        
        var Recipe_items_model = Recipe_database_model.extend({
            defaults: {
                filter_options : "",
                sort_by : 'recipe_name',
                order_dirrection : 'ASC',
            },
            initialize: function(){
                this.connectPagination();
                this.setListeners();
            },
            
            setListeners : function() {
                this.bind("change:filter_options", this.resetCurrentPage, this);
                this.bind("change:recipe", this.onChangeRecipe, this);
            },
            
            resetFilter : function() {
                this.unbind("change:filter_options", this.resetCurrentPage);
                this.set({'filter_options' : ''});
                this.bind("change:filter_options", this.resetCurrentPage, this);
                
                this.pagination_app_model.off("change:currentPage", this.loadRecipes);
                this.pagination_app_model.set({'currentPage' : ""});
                this.pagination_app_model.bind("change:currentPage", this.loadRecipes, this);
                this.pagination_app_model.setLocalStorageItem('currentPage', 1);
            },
 
            connectPagination : function() {
                this.pagination_app_model = $.backbone_pagination({});
                this.pagination_app_model.bind("change:currentPage", this.loadRecipes, this);
                this.pagination_app_model.bind("change:items_number", this.loadRecipes, this);
            },
            // on change filter options, reset pagination to 1 page
            resetCurrentPage : function() {
                this.pagination_app_model.setLocalStorageItem('currentPage', 1);
                this.pagination_app_model.off("change:currentPage", this.loadRecipes);
                this.pagination_app_model.set({'currentPage' : ""});
                this.pagination_app_model.bind("change:currentPage", this.loadRecipes, this);
                this.pagination_app_model.set({'currentPage' : 1});
            },
            

            getRecipes : function(page, limit) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipes';
                var table = this.get('recipes_db_table');
                
                data.sort_by = this.get('sort_by');
                data.order_dirrection = this.get('order_dirrection');

                data.page = page || 1;
                data.limit = limit;
                
                var filter_options = this.get('filter_options') || '';
                
                data.filter_options = filter_options;
                
                data.my_recipes = this.get('my_recipes');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipes", output);
                    
                });
            },
            
            
            loadRecipes : function() {
                
                //pagination
                var page = this.pagination_app_model.getLocalStorageItem('currentPage');
                var limit = this.pagination_app_model.getLocalStorageItem('items_number');
                //
                this.getRecipes(page, limit);
                this.listenToOnce(this, "change:recipes", this.onGetRecipes);
            },
            
            onGetRecipes : function() {
                if (this.has("recipes")){
                    var recipes = this.get("recipes");
                    
                    //pagination
                    var item = recipes[0];
                    var items_total = 0;
                    if (typeof item !== "undefined") {
                        items_total = item.items_total;
                    }
                    this.pagination_app_model.set({'items_total' : items_total});
                    //
                    
                    this.populateRecipes(recipes);
                }
            },
            populateRecipes : function(recipes) {
                $("#recipe_database_items_wrapper").html('');
                
                if(recipes.length == 0) {
                    $("#recipe_database_items_wrapper").html('<div style="text-align:center;">No Recipes Found.</div>');
                }
                var recipe_item = new Recipe_item_view({ el: $("#recipe_database_items_wrapper"), model : this});
                _.each(recipes, function(item){
                    recipe_item.render(item);
                });
            },
            
            getRecipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe", output);
                    //console.log(output);
                });
            },
            
            onChangeRecipe : function() {
                var recipe = this.get('recipe');
                this.populateRecipe(recipe);
            },
            
            populateRecipe : function(recipe) {
                var comment_options = {
                    'item_id' : recipe.id,
                    'fitness_administration_url' : this.attributes.fitness_frontend_url,
                    'comment_obj' : {'user_name' : this.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : this.attributes.recipe_comments_db_table,
                    'read_only' : true,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);
                var recipe_item = new Recipe_view({ el: $("#recipe_main_container"), model : this, 'comments' : comments});
                recipe_item.render(recipe);
            },
            
            
            
        });
        
        
        
        var Recipes_latest_model = Recipe_database_model.extend({
            initialize: function(){
                this.listenToOnce(this, "change:recipes_latest", this.populateRecipesLatest);
            },
            
            defaults: {
                limit : 2,
            },
            
            render : function(){
                this.populateRecipesLatest();
            },
            
            getRecipesLatest : function() {
                
                if(this.has("recipes_latest")) return;
                
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipes';
                var table = this.get('recipes_db_table');
                
                data.sort_by = 'created';
                data.order_dirrection = 'DESC';

                data.page = 1;
                data.limit = this.get('limit');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipes_latest", output);
                    //console.log(output);
                });
            },
            
          
            populateRecipesLatest : function() {
                
                if(!this.has("recipes_latest")) {
                    this.getRecipesLatest();
                    return;
                }
                
                var recipes_latest = this.get('recipes_latest');
                
                var latest_recipes_wrapper_view = new Latest_recipes_wrapper_view({ el: $("#recipes_latest_wrapper")});
                latest_recipes_wrapper_view.render();
                
                $("#latest_recipes_container").html('');
                
                if(recipes_latest.length == 0) {
                    $("#latest_recipes_container").html('<div style="text-align:center;">No Recipes Found.</div>');
                }
                var recipe_item = new Latest_recipes_item_view({ el: $("#latest_recipes_container"), model : this});
                _.each(recipes_latest, function(item){
                    recipe_item.render(item);
                });

            },

        });
        
        
        var Filter_categories_model = Recipe_database_model.extend({
            initialize: function() {
                this.recipe_items_model = this.attributes.recipe_items_model;
                this.listenToOnce(this, "change:recipe_types", this.populateRecipeTypes);
                
            },

            render : function(){
                
                this.populateRecipeTypes();
            },
            
            getRecipeTypes : function() {
                
                if(this.has("recipe_types")) return;
                
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipeTypes';
                var table = this.get('recipe_types_db_table');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe_types", output);
                    //console.log(output);
                });
            },

            populateRecipeTypes : function() {
                
                if(!this.has("recipe_types")) {
                    this.getRecipeTypes();
                    return;
                }
                
                var recipe_types = this.get('recipe_types');
                
                var categories_filter = new Filter_view({
                    el: $("#recipe_database_filter_wrapper"),
                    'recipe_types' : recipe_types,
                    'recipe_items_model' : this.recipe_items_model
                });
                categories_filter.render();

            },
        });
        

                
        // VIEWS
        var Views = { }; 
        
        var Mainmenu_view = Backbone.View.extend({

            el: $("#recipe_mainmenu"), 

            render : function(){
                var template = _.template($("#recipe_database_mainmenu_template").html());
                this.$el.html(template);
            }
        });
        
        var Submenu_view = Backbone.View.extend({
            el: $("#recipe_submenu"), 
            render : function(){
                var template = _.template($("#recipe_database_submenu_template").html());
                this.$el.html(template);
            }
        });
        
        var Submenu_myrecipes_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render : function(){
                var template = _.template($("#recipe_database_submenu_content_template").html());
                this.$el.html(template);
            }
        });
        
        var Submenu_myrecipe_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
            },
            render : function(){
                var template = _.template($("#recipe_database_submenu_recipe_content_template").html());
                this.$el.html(template);
            },
            onClickCloseRecipe : function() {
                window.history.back();
            }
        });
        
        // on open recipe
        var Recipe_view = Backbone.View.extend({
             render : function(data){
                var data = data
                data.model = this.model;
                var template = _.template($("#recipe_database_view_recipe_template").html(), data);
                this.$el.html(template);
                this.loadComments();
            },
                        
            loadComments : function(){
                var comments_html = this.options.comments.run();
                $("#comments_wrapper").html(comments_html)
            },
            
        });
        
        // list item
        var Recipe_item_view = Backbone.View.extend({
            render : function(data){
                var data = data
                data.model = this.model;
                var template = _.template($("#recipe_database_item_template").html(), data);
                this.$el.append(template);
            },
            
            events: {
                "click .view_recipe" : "onClickViewRecipe",
            },
            
            onClickViewRecipe : function(event) {
                var id = $(event.target).attr("data-id");
                var controller = new Controller();
                controller.navigate("!/nutrition_recipe/" + id, true);
            }
        });
        
        
        var Filter_view = Backbone.View.extend({
            initialize: function(){
                this.recipe_items_model = this.options.recipe_items_model;
            },
            
            render : function(){
                var recipe_types = this.options.recipe_types;
                this.loadTemplate(recipe_types);
            },
            
            events: {
                "change #categories_filter" : "onFilterSelect",
            },
            
            loadTemplate : function(recipe_types) {
                var data = {'items' : recipe_types};
                var template = _.template($("#recipe_database_filter_template").html(), data);
                this.$el.html(template);
            },
            
            onFilterSelect : function(event){
                var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
                this.recipe_items_model.set({'filter_options' : ids});
                console.log(ids);
            }
        });
        
        var Latest_recipes_item_view = Backbone.View.extend({

            render : function(data){
                var data = data
                data.model = this.model;
                var template = _.template($("#recipes_latest_item_template").html(), data);
                this.$el.append(template);
            },
            
            events: {
                "click .view_recipe" : "onClickViewRecipe",
            },
            
            onClickViewRecipe : function(event) {
                var id = $(event.target).attr("data-id");
                var controller = new Controller();
                controller.navigate("!/nutrition_recipe/" + id, true);
            }
        });
        
        var Latest_recipes_wrapper_view = Backbone.View.extend({
            render : function(){
                var template = _.template($("#recipes_latest_wrapper_template").html());
                this.$el.html(template);
            }
        });



        var MainContainer_view = Backbone.View.extend({
            
            initialize: function(){
                this.render();
            },
            
            el: $("#recipe_main_container"), 

            render : function(){
                
                var variables = {

                };
                
                var template = _.template($("#recipe_database_main_container_template").html(), variables);
                this.$el.html(template);
            },
            
           
        });
        
        
        

        Views = { 
            mainmenu: new Mainmenu_view(),
            submenu: new Submenu_view(),
            main_container : new MainContainer_view(),
        };
        
        var recipe_items_model = new Recipe_items_model(options);
        
        var recipes_latest_model = new Recipes_latest_model(options);
        
        var filter_options = options;
        filter_options.recipe_items_model = recipe_items_model;
        
        var filter_categories_model = new Filter_categories_model(filter_options);
        
        
         // CONTROLLER
         var Controller = Backbone.Router.extend({

            routes : {
                "": "my_recipes", 
                "!/": "my_recipes", 
                "!/my_recipes": "my_recipes", 
                "!/recipe_database": "recipe_database", 
                "!/nutrition_database": "nutrition_database", 
                "!/nutrition_recipe/:id" : "nutrition_recipe"
            },

            my_recipes : function () {
                this.common_actions();
                $("#my_recipes_link").addClass("active_link");
                
                this.load_submenu();
                // populate submenu
                new Submenu_myrecipes_view({ el: $("#submenu_container"), recipe_items_model : recipe_items_model});
                
                recipe_items_model.set({my_recipes : true});
                
                recipe_items_model.resetFilter();
                
                recipe_items_model.loadRecipes();
                
                recipe_items_model.connectPagination();

                filter_categories_model.render();
                
                recipes_latest_model.render();
             },

            recipe_database : function () {
                this.common_actions();
                $("#recipe_database_link").addClass("active_link");
                
                this.hide_submenu();
                
                recipe_items_model.set({my_recipes : false});
                
                recipe_items_model.resetFilter();

                recipe_items_model.loadRecipes();
                
                recipe_items_model.connectPagination();
   
                filter_categories_model.render();
                
                recipes_latest_model.render();
              },

            nutrition_database : function () {
                this.common_actions();
                $("#nutrition_database_link").addClass("active_link");
            },

            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
                
                this.load_mainmenu();
                
                if (Views.main_container != null) {
                    Views.main_container.render();
                }
            },
            
            load_mainmenu : function() {
                if (Views.mainmenu != null) {
                    Views.mainmenu.render();
                }
            },
            
            load_submenu : function() {
                if (Views.submenu != null) {
                    Views.submenu.render();
                }
            },
            
            hide_submenu : function() {
                $(Views.submenu.$el).html('');
            },
            
            clear_main_ontainer : function() {
                $("#recipe_main_container").html('');
            },

            nutrition_recipe : function(id) {
                this.clear_main_ontainer();
                this.load_submenu();
                
                recipe_items_model.getRecipe(id);
            
                new Submenu_myrecipe_view({ el: $("#submenu_container"), recipe_items_model : recipe_items_model});
            }
 
            
        });

        var controller = new Controller(); 

        Backbone.history.start();  
        
        
        
        
        
        
        

        

    })($js);
    

        
</script>