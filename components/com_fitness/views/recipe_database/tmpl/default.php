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

        window.app = {};
        
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
        window.app.Recipe_database_model = Backbone.Model.extend({


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
        
        
        window.app.Recipe_items_model = window.app.Recipe_database_model.extend({
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
                this.set({'recipes' : null});
                this.listenToOnce(this, "change:recipes", this.onGetRecipes);
                this.getRecipes(page, limit);
                
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
                
                var recipe_item = new window.app.Recipe_item_view({ el: $("#recipe_database_items_wrapper"), model : this});
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
                var recipe_item = new window.app.Recipe_view({ el: $("#recipe_main_container"), model : this, 'comments' : comments});
                recipe_item.render(recipe);
            },
            
            copy_recipe : function(recipe_id){
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'copyRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = recipe_id;

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe_copied", output);
                    //console.log(output);
                });
            }

        });
        
        
        
        window.app.Recipes_latest_model = window.app.Recipe_database_model.extend({
            initialize: function(){
                this.listenToOnce(this, "change:recipes_latest", this.populateRecipesLatest);
            },
            
            defaults: {
                limit : 15,
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
                
                var latest_recipes_wrapper_view = new window.app.Latest_recipes_wrapper_view({ el: $("#recipes_latest_wrapper")});
                latest_recipes_wrapper_view.render();
                
                $("#latest_recipes_container").html('');
                
                if(recipes_latest.length == 0) {
                    $("#latest_recipes_container").html('<div style="text-align:center;">No Recipes Found.</div>');
                }
                var recipe_item = new window.app.Latest_recipes_item_view({ el: $("#latest_recipes_container"), model : this});
                _.each(recipes_latest, function(item){
                    recipe_item.render(item);
                });

            },

        });
        
        
        window.app.Filter_categories_model = window.app.Recipe_database_model.extend({
            initialize: function() {
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
                
                var categories_filter = new window.app.Filter_view({
                    el: $("#recipe_database_filter_wrapper"),
                    'recipe_types' : recipe_types
                });
                categories_filter.render();
            },
        });
        

                
        // VIEWS
        window.app.Views = { }; 
        
        window.app.Mainmenu_view = Backbone.View.extend({

            el: $("#recipe_mainmenu"), 

            render : function(){
                var template = _.template($("#recipe_database_mainmenu_template").html());
                this.$el.html(template);
            }
        });
        
        window.app.Submenu_view = Backbone.View.extend({
            el: $("#recipe_submenu"), 
            render : function(){
                var template = _.template($("#recipe_database_submenu_template").html());
                this.$el.html(template);
            }
        });
        
        window.app.Submenu_myrecipes_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            render : function(){
                var template = _.template($("#recipe_database_submenu_content_template").html());
                this.$el.html(template);
            }
        });
        
        window.app.Submenu_myrecipe_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
            },
            render : function(){
                var template = _.template($("#submenu_my_recipes_template").html());
                this.$el.html(template);
            },
            onClickCloseRecipe : function() {
                window.history.back();
            }
        });
        
        
        window.app.Submenu_recipe_database_view = Backbone.View.extend({
            initialize: function(){
                this.listenToOnce(window.app.recipe_items_model, "change:recipe_copied", this.redirectToRecipe);
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
                "click #copy_recipe" : "onClickCopyRecipe",
            },
            render : function(){
                var template = _.template($("#submenu_recipe_database_template").html());
                this.$el.html(template);
            },

            onClickCloseRecipe : function() {
                window.history.back();
            },
            
            onClickCopyRecipe : function() {
                var recipe_id = this.options.recipe_id;
                window.app.recipe_items_model.copy_recipe(recipe_id);
            },
            
            redirectToRecipe : function() {
                var new_recipe_id = window.app.recipe_items_model.get('recipe_copied').id;

                window.app.controller.navigate("!/my_recipes", true);
                window.app.controller.navigate("!/nutrition_recipe/" + new_recipe_id, true);
            }
        });
        
        // on open recipe
        window.app.Recipe_view = Backbone.View.extend({
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
        window.app.Recipe_item_view = Backbone.View.extend({
            initialize: function(){
                this.listenToOnce(this.model, "change:recipe_copied", this.redirectToRecipe);
            },
            render : function(data){
                var data = data
                data.model = this.model;
                var template = _.template($("#recipe_database_item_template").html(), data);
                this.$el.append(template);
            },
            
            events: {
                "click .view_recipe" : "onClickViewRecipe",
                "click #copy_recipe" : "onClickCopyRecipe",
            },
            
            onClickViewRecipe : function(event) {
                var id = $(event.target).attr("data-id");

                window.app.controller.navigate("!/nutrition_recipe/" + id, true);
            },
            
            onClickCopyRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                this.model.copy_recipe(recipe_id);
            },
            
            redirectToRecipe : function() {
                var new_recipe_id = this.model.get('recipe_copied').id;

                window.app.controller.navigate("!/my_recipes", true);
                window.app.controller.navigate("!/nutrition_recipe/" + new_recipe_id, true);
            }
        });
        
        
        window.app.Filter_view = Backbone.View.extend({
            initialize: function(){
                
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
                window.app.recipe_items_model.set({'filter_options' : ids});
            }
        });
        
        window.app.Latest_recipes_item_view = Backbone.View.extend({

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

                window.app.controller.navigate("!/recipe_database", true);
                window.app.controller.navigate("!/nutrition_recipe/" + id, true);
            }
        });
        
        window.app.Latest_recipes_wrapper_view = Backbone.View.extend({
            render : function(){
                var template = _.template($("#recipes_latest_wrapper_template").html());
                this.$el.html(template);
            }
        });



        window.app.MainContainer_view = Backbone.View.extend({
            
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
        
        
        
        //Creation global objects
        window.app.Views = { 
            mainmenu: new window.app.Mainmenu_view(),
            submenu: new window.app.Submenu_view(),
            main_container : new window.app.MainContainer_view(),
        };
        
        
        window.app.recipe_items_model = new window.app.Recipe_items_model(options);
        window.app.recipes_latest_model = new window.app.Recipes_latest_model(options);
        var filter_options = options;
        filter_options.recipe_items_model = window.app.recipe_items_model;
        window.app.filter_categories_model = new window.app.Filter_categories_model(filter_options);
        //
        
         // CONTROLLER
         window.app.Controller = Backbone.Router.extend({

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
                new window.app.Submenu_myrecipes_view({ el: $("#submenu_container")});
                
                window.app.recipe_items_model.set({my_recipes : true});
                
                window.app.recipe_items_model.resetFilter();
                
                window.app.recipe_items_model.loadRecipes();
                
                window.app.recipe_items_model.connectPagination();

                window.app.filter_categories_model.render();
                
                window.app.recipes_latest_model.render();
             },

            recipe_database : function () {
                this.common_actions();
                $("#recipe_database_link").addClass("active_link");
                
                this.hide_submenu();
                
                window.app.recipe_items_model.set({my_recipes : false});
                
                window.app.recipe_items_model.resetFilter();

                window.app.recipe_items_model.loadRecipes();
                
                window.app.recipe_items_model.connectPagination();
   
                window.app.filter_categories_model.render();
                
                window.app.recipes_latest_model.render();
              },

            nutrition_database : function () {
                this.common_actions();
                $("#nutrition_database_link").addClass("active_link");
            },

            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
                
                this.load_mainmenu();
                
                if (window.app.Views.main_container != null) {
                    window.app.Views.main_container.render();
                }
            },
            
            load_mainmenu : function() {
                if (window.app.Views.mainmenu != null) {
                    window.app.Views.mainmenu.render();
                }
            },
            
            load_submenu : function() {
                if (window.app.Views.submenu != null) {
                    window.app.Views.submenu.render();
                }
            },
            
            hide_submenu : function() {
                $(window.app.Views.submenu.$el).html('');
            },
            
            clear_main_ontainer : function() {
                $("#recipe_main_container").html('');
            },

            nutrition_recipe : function(id) {
                this.clear_main_ontainer();
                this.load_submenu();
                
                window.app.recipe_items_model.getRecipe(id);
                
                var my_recipes = window.app.recipe_items_model.get('my_recipes');
                
                if(my_recipes) {
                    new window.app.Submenu_myrecipe_view({ el: $("#submenu_container")});
                } else {
                    new window.app.Submenu_recipe_database_view({ el: $("#submenu_container"), recipe_id : id});
                }
            }
 
            
        });

        window.app.controller = new window.app.Controller(); 

        Backbone.history.start();  
        
        
        
        
        
        
        

        

    })($js);
    

        
</script>