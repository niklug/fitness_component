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
        
        var add_diary_options = {
            'nutrition_plan_id' : '<?php echo JRequest::getVar('nutrition_plan_id'); ?>',
            'meal_id' : '<?php echo JRequest::getVar('meal_id'); ?>',
            'type' : '<?php echo JRequest::getVar('type'); ?>',
            'parent_view' : '<?php echo JRequest::getVar('parent_view');?>'
        };
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'base_url' : '<?php echo JURI::root();?>',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'recipes_db_table' : '#__fitness_nutrition_recipes',
            'ingredients_db_table' : '#__fitness_nutrition_database',
            'recipe_types_db_table' : '#__fitness_recipe_types',
            'recipe_comments_db_table' : '#__fitness_nutrition_recipes_comments',
            'recipes_favourites_db_table' : '#__fitness_nutrition_recipes_favourites',
            'default_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_image.png',
            'default_video_image' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_image.png',
            'no_video_image_big' : '<?php echo JURI::root();?>administrator/components/com_fitness/assets/images/no_video_big.png',
            'upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Images' . DS  ?>',
            'video_upload_folder' : '<?php echo JPATH_ROOT . DS . 'images' . DS . 'Recipe_Videos' . DS  ?>',
            'img_path' : 'images/Recipe_Images',
            'video_path' : 'images/Recipe_Videos',
            'add_diary_options' : add_diary_options
        };
        
        // connect helper class
        var helper_options = {
            'ajax_call_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'base_url' : '<?php echo JURI::root();?>',
        }
        window.fitness_helper = $.fitness_helper(helper_options);
        
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
                current_page: 'my_recipes',
                filter_options : "",
                sort_by : 'recipe_name',
                order_dirrection : 'ASC',
                state : '1'
            },
            initialize: function(){
                
                this.setListeners();
                this.connectPagination();
                this.nutrition_plan_id = this.attributes.add_diary_options.nutrition_plan_id;
           },
            
            setListeners : function() {
                this.bind("change:filter_options", this.resetCurrentPage, this);
                this.bind("change:recipe_variations_filter_options", this.resetCurrentPage, this);
            },
            
           
            resetFilter : function() {
                this.unbind("change:filter_options", this.resetCurrentPage);
                this.unbind("change:recipe_variations_filter_options", this.resetCurrentPage);
                this.set({'filter_options' : '', 'recipe_variations_filter_options' : ''});
                this.bind("change:filter_options", this.resetCurrentPage, this);
                this.bind("change:recipe_variations_filter_options", this.resetCurrentPage, this);
                
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
                
                data.state = this.get('state');
  
                data.filter_options = this.get('filter_options') || '';
                
                data.recipe_variations_filter_options = this.get('recipe_variations_filter_options') || '';
                
                data.current_page = this.get('current_page');

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
                if(!parseInt(id)) return;
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;
                
                data.state = this.get('state');
                
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe", output);
                    self.onChangeRecipe();
                    //console.log(output);
                });
            },
            
            onChangeRecipe : function() {
                var recipe = this.get('recipe');
                //console.log(recipe);            
                var current_page = this.get('current_page');


                if(current_page == 'my_recipes') {
                    new window.app.Submenu_myrecipe_view({ el: $("#submenu_container"), 'recipe_id' : recipe.id, 'is_favourite' : recipe.is_favourite, 'nutrition_plan_id' : this.nutrition_plan_id});
                } else if (current_page == 'recipe_database') {
                    new window.app.Submenu_recipe_database_view({ el: $("#submenu_container"), 'recipe_id' : recipe.id, 'is_favourite' : recipe.is_favourite, 'nutrition_plan_id' : this.nutrition_plan_id});
                } else if (current_page == 'my_favourites') {
                    new window.app.Submenu_my_favourites_view({ el: $("#submenu_container"), 'recipe_id' : recipe.id, 'nutrition_plan_id' : this.nutrition_plan_id});
                }  else if (current_page == 'trash_list') {
                    new window.app.Submenu_trash_form_view({ el: $("#submenu_container"), 'recipe_id' : recipe.id});
                } else if (current_page == 'edit_recipe') {
                    return false;
                } else if(current_page == 'add_diary') {
                    var nutrition_plan_id = window.app.recipe_items_model.get('nutrition_plan_id');
                    new window.app.Submenu_add_diary_view ({ el: $("#submenu_container"), 'recipe_id' : recipe.id, 'nutrition_plan_id' : nutrition_plan_id, 'number_serves_recipe' : recipe.number_serves});
                }
                this.populateRecipe(recipe);
            },
            
            populateRecipe : function(recipe) {
                var comment_options = {
                    'item_id' : recipe.id,
                    'fitness_administration_url' : this.attributes.fitness_frontend_url,
                    'comment_obj' : {'user_name' : this.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : this.attributes.recipe_comments_db_table,
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'RecipeComment'
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);
                
                this.recipe_item = new window.app.Recipe_view({model : this, 'comments' : comments});
   
                $("#recipe_main_container").html( this.recipe_item.render(recipe).el );
                
                this.recipe_item.loadComments();
                this.recipe_item.loadVideoPlayer();
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
            },
            
            add_favourite : function(recipe_id){
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'addFavourite';
                var table = this.get('recipes_favourites_db_table');
                
                data.recipe_id = recipe_id;

                var self = this;
                this.set("favourite_added", null);
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("favourite_added", recipe_id);
                    self.onAddFavourites();
                });
            },
            
            remove_favourite : function(recipe_id){
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'removeFavourite';
                var table = this.get('recipes_favourites_db_table');
                
                data.recipe_id = recipe_id;

                var self = this;
                this.set("favourite_removed", null);
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("favourite_removed", recipe_id);
                    self.onRemoveFavourites();
                });
            },
            
            hide_recipe_item : function(recipe_id) {
                $(".recipe_database_item_wrapper[data-id='" + recipe_id + "']").fadeOut();
            },
            
            onRemoveFavourites : function(){
                var recipe_id = this.get('favourite_removed');
                var current_page = this.get('current_page');
                
                if(current_page == 'my_favourites') {
                    this.hide_recipe_item(recipe_id);
                }
                
                if((current_page == 'my_recipes') || (current_page == 'recipe_database')) {
                   $(".remove_favourites[data-id='" + recipe_id + "']").hide();
                   $(".add_favourite[data-id='" + recipe_id + "']").show();
                }
                
            },
            
            onAddFavourites : function() {
                var recipe_id = this.get('favourite_added');
                $(".remove_favourites[data-id='" + recipe_id + "']").show();
                $(".add_favourite[data-id='" + recipe_id + "']").hide();
            },
            
            delete_recipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'deleteRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;

                var self = this;
                
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe_deleted", id);
                    self.hide_recipe_item(id);
                    self.onRecipeDeleted();
                });
            },
            
            restore_recipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'updateRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;
                
                data.state = '1';

                var self = this;
                
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe_restored", id);
                    self.hide_recipe_item(id);
                    self.onTrashRestored();
                });
            },
            
            trash_recipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'updateRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;
                
                data.state = '-2';

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipe_trashed", id);
                    self.hide_recipe_item(id);
                    self.onRecipeTrashed();
                });
            },
            
            onTrashRestored : function() {
                window.app.controller.navigate("!/trash_list", true);
            },
            
            onRecipeDeleted : function() {
                window.app.controller.navigate("!/trash_list", true);
            },
            
            onRecipeTrashed : function() {
                window.app.controller.navigate("!/my_recipes", true);
            },
            
            save_recipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'updateRecipe';
                var table = this.get('recipes_db_table');
                
                data.id = id;
                data.recipe_name = $("#recipe_name").val();
                if(!data.recipe_name) {
                    alert('Insert Recipe Name!')
                    return;
                }
                data.recipe_type = $("#recipe_type").find(':selected').map(function(){ return this.value }).get().join(",");
                data.recipe_variation = $("#recipe_variation").find(':selected').map(function(){ return this.value }).get().join(",");
                if(!data.recipe_type) {
                    alert('Select Recipe Type!')
                    return;
                }
                data.number_serves = $("#number_serves").val();
                data.image = $("#preview_image").attr('data-imagepath');
                data.video = $("#preview_video").attr('data-videopath');
                data.instructions = encodeURIComponent($("#instructions").html());
                data.created_by = this.get('user_id');
   
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss");  

               
                data.state = '1';
                //console.log(data);

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    if(parseInt(id) == 0) {
                        self.email_new_recipe(output);
                    }
                    self.set("recipe_saved", output);
                });
            },
            
            email_new_recipe : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = '';
                var task = 'ajax_email';
                var table = '';

                data.id = id;
                data.view = 'NutritionRecipe';
                data.method = 'NewRecipe';
                this.ajaxCall(data, url, view, task, table, function(output){
                    //console.log(output);
                    var emails = output.split(',');
                    var message = 'Emails were sent to: ' +  "</br>";
                    $.each(emails, function(index, email) { 
                        message += email +  "</br>";
                    });
                    $("#emais_sended").append(message);
               });
            },
            
            add_diary : function(data) {
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_plan';
                var task = 'importRecipe';
                                
                data.nutrition_plan_id = this.nutrition_plan_id;
                data.meal_id = this.attributes.add_diary_options.meal_id;
                data.type = this.attributes.add_diary_options.type;
                data.parent_view = this.attributes.add_diary_options.parent_view;

                if(data.parent_view == 'nutrition_diary_frontend'){
                    data.db_table =  '#__fitness_nutrition_diary_ingredients';
                }
                
                if(data.parent_view == 'nutrition_plan_backend'){
                    data.db_table = '#__fitness_nutrition_plan_ingredients';
                }
                
                var table = data.db_table;

                this.ajaxCall(data, url, view, task, table, function(output){

                    window.parent.nutrition_meal.run();
                    
                    var elem = window.parent.document.getElementById("recipes_list_wrapper");
                    elem.parentNode.removeChild(elem);

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
                
                data.state = '1';

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
        
        
        
        window.app.Nutrition_database_model = window.app.Recipe_database_model.extend({
            defaults: {

            },
            initialize: function(){
                this.setListeners();
                this.connectPagination();
                this.bind("change:ingredients", this.onGetIngredients, this);
            },
            
            setListeners : function() {

            },

            connectPagination : function() {
                this.pagination_app_model = $.backbone_pagination({});
                this.pagination_app_model.bind("change:currentPage", this.loadIngredients, this);
                this.pagination_app_model.bind("change:items_number", this.loadIngredients, this);
            },

            loadIngredients : function() {
                //pagination
                
                var page = this.pagination_app_model.getLocalStorageItem('currentPage');
                var limit = this.pagination_app_model.getLocalStorageItem('items_number');
                //
                this.set({'ingredients' : null});
                this.getIngredients(page, limit);
                
            },
            
            onGetIngredients : function() {
                if (this.has("ingredients")){
                    var ingredients_data = this.get("ingredients");
                    
                    //pagination
                    var items_total = ingredients_data.items_total;
                    
                    var ingredients = ingredients_data.ingredients;
                    
                    //if(items_total > 100) items_total = 100;
                    
                    this.pagination_app_model.set({'items_total' : items_total});
                    //
                    this.populateIngredients(ingredients);
                }
            },
            
            populateIngredients : function(ingredients) {
                
                new window.app.Nutrition_database_list_items_view({ el: $("#ingredients_items"), 'ingredients' : ingredients})
            },
            
            getIngredients : function(page, limit) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getIngredients';
                var table = this.get('ingredients_db_table');

                data.page = page || 1;
                
                                    
                data.limit = limit;
                
                data.table = table;
                
                data.search = $("#search_field").val() || '';

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("ingredients", output);
                });
            },
            
            
            saveIngredient  : function() {
                if(window.recipe_database.validate_form() != true) {
                    alert('Invalid form');
                    return false;
                }
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'updateIngredient';
                var table = this.get('ingredients_db_table');
                
                data.id = this.get('ingredient_id') || '';
                data.ingredient_name = $("#jform_ingredient_name").val();
                data.calories = $("#jform_calories").val();
                data.energy = $("#jform_energy").val();
                data.protein = $("#jform_protein").val();
                data.fats = $("#jform_fats").val();
                data.saturated_fat = $("#jform_saturated_fat").val();
                data.carbs = $("#jform_carbs").val();
                data.total_sugars = $("#jform_total_sugars").val();
                data.sodium = $("#jform_sodium").val();
                data.specific_gravity = $("#jform_specific_gravity").val();
                data.description = encodeURIComponent($("#jform_description").html());
                data.state = '1';
                //console.log(data);

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("ingredient_saved", output);
                    
                    self.onIngredientSave(output);
                });
            },
            
            onIngredientSave : function(ingredient_id) {
                this.set({'ingredient_id' : ingredient_id});
                var action = this.get('action');
                if(action == 'save_close') {
                    window.app.controller.navigate("!/nutrition_database", true);
                }
                
                if(action == 'save_new') {
                    this.set({'ingredient_id' : ''});
                    window.app.controller.navigate("!/nutrition_database", true)
                    window.app.controller.navigate("!/add_ingredient", true);
                }
            }
            
            
        });
        

                
        // VIEWS
        window.app.Views = { }; 
        
        window.app.Mainmenu_view = Backbone.View.extend({

            el: $("#recipe_mainmenu"), 

            events: {
                "click #my_favourites_link" : "onClickFavourites",
                "click #my_recipes_link" : "onClickMy_recipes",
                "click #recipe_database_link" : "onClickRecipe_database",
                "click #nutrition_database_link" : "onClickNutrition_database",
            },

            render : function(){
                var template = _.template($("#recipe_database_mainmenu_template").html());
                this.$el.html(template);
            },
            
            onClickFavourites : function() {
                window.app.controller.navigate("!/my_favourites", true);
                return false;
            },
            
            onClickMy_recipes : function() {
                window.app.controller.navigate("!/my_recipes", true);
                return false;
            },
            
            onClickRecipe_database : function() {
                window.app.controller.navigate("!/recipe_database", true);
                return false;
            },
            
            onClickNutrition_database : function() {
                window.app.controller.navigate("!/nutrition_database", true);
                return false;
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
            events: {
                "click #view_trash" : "onClickViewTrash",
                "click #new_recipe" : "onClickNewRecipe",
            },
            render : function(){
                var template = _.template($("#recipe_database_submenu_content_template").html());
                this.$el.html(template);
            },
             
            onClickViewTrash : function() {
                window.app.controller.navigate("!/trash_list", true);
            },
            
            onClickNewRecipe : function() {
                window.app.controller.navigate("!/edit_recipe/0", true);
            }
        });
        
        window.app.Submenu_myrecipe_view = Backbone.View.extend({
            initialize: function(){
                this.recipe_id = this.options.recipe_id;
                this.is_favourite = this.options.is_favourite;
                this.nutrition_plan_id = this.options.nutrition_plan_id;
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
                "click .add_favourite" : "onClickAddFavourite",
                "click .remove_favourites" : "onClickRemoveFavourites",
                "click .trash_recipe" : "onClickTrashRecipe",
                "click .edit_recipe" : "onClickEditRecipe",
                "click .add_diary" : "onClickAddDiary",
            },
            render : function(){
                var variables = {'recipe_id' : this.recipe_id, 'is_favourite' : this.is_favourite, 'nutrition_plan_id' : this.nutrition_plan_id };
                var template = _.template($("#submenu_my_recipes_template").html(), variables);
                this.$el.html(template);
            },
            onClickCloseRecipe : function() {
                window.app.controller.navigate("!/my_recipes", true);
            },
            
            onClickAddFavourite : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.add_favourite(recipe_id);
            },
            
            onClickRemoveFavourites : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.remove_favourite(recipe_id);
            },
            
            onClickTrashRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.trash_recipe(recipe_id);
            },
            
            onClickEditRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/edit_recipe/" + recipe_id, true);
            },
            
            onClickAddDiary : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/add_diary/" + id, true);
            },
        });
        
        
        window.app.Submenu_recipe_database_view = Backbone.View.extend({
            initialize: function(){
                this.recipe_id = this.options.recipe_id;
                this.is_favourite = this.options.is_favourite;
                this.nutrition_plan_id = this.options.nutrition_plan_id;
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
                "click #copy_recipe" : "onClickCopyRecipe",
                "click .add_favourite" : "onClickAddFavourite",
                "click .remove_favourites" : "onClickRemoveFavourites",
                "click .trash_recipe" : "onClickTrashRecipe",
                "click .add_diary" : "onClickAddDiary",
            },
            render : function(){
                var variables = {'recipe_id' : this.recipe_id, 'is_favourite' : this.is_favourite, 'nutrition_plan_id' : this.nutrition_plan_id};
                var template = _.template($("#submenu_recipe_database_template").html(), variables);
                this.$el.html(template);
            },

            onClickCloseRecipe : function() {
                window.app.controller.back();
            },
            
            onClickCopyRecipe : function() {
                window.app.recipe_items_model.copy_recipe(this.recipe_id);
            },
            
            onClickAddFavourite : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.add_favourite(recipe_id);
            },
            
            onClickRemoveFavourites : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.remove_favourite(recipe_id);
            },
            
            onClickTrashRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.trash_recipe(recipe_id);
            },
            
            onClickTrashRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.trash_recipe(recipe_id);
            },
            
            onClickAddDiary : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/add_diary/" + id, true);
            },
        });
        
        
        window.app.Submenu_my_favourites_view = Backbone.View.extend({
            initialize: function(){
                this.listenToOnce(window.app.recipe_items_model, "change:favourite_removed", this.redirectToFavourites);
                this.recipe_id = this.options.recipe_id;
                this.nutrition_plan_id = this.options.nutrition_plan_id;
                this.render();
            },
            events: {
                "click #close_recipe" : "onClickCloseRecipe",
                "click .remove_favourites" : "onClickRemoveFavourites",
                "click .add_diary" : "onClickAddDiary",
            },
            render : function(){
                var variables = {'recipe_id' : this.recipe_id, 'nutrition_plan_id' : this.nutrition_plan_id};
                var template = _.template($("#submenu_my_favourites_template").html(), variables);
                this.$el.html(template);
            },
            onClickCloseRecipe : function() {
                window.app.controller.back();
            },
            onClickRemoveFavourites : function(event) {
            
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.remove_favourite(recipe_id);
            },
            redirectToFavourites : function(){
                window.app.controller.navigate("!/my_favourites", true);
            },
            
            onClickAddDiary : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/add_diary/" + id, true);
            },
        });
        
        
        window.app.Submenu_trash_list_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #close_trash_list" : "onClickCloseTrashList",
            },
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_trash_list_template").html(), variables);
                this.$el.html(template);
            },

            onClickCloseTrashList : function(){
                window.app.controller.navigate("!/my_recipes", true);
            }
        });
        
        window.app.Submenu_trash_form_view = Backbone.View.extend({
            initialize: function(){
                this.recipe_id = this.options.recipe_id;
                this.render();
            },
            events: {
                "click .close_trash_form" : "onClickCloseTrashForm",
                "click .delete_recipe" : "onClickDeleteRecipe",
                "click .restore_recipe" : "onClickRestoreRecipe",
            },
            render : function(){
                var variables = {'recipe_id' : this.recipe_id};
                var template = _.template($("#submenu_trash_form_template").html(), variables);
                this.$el.html(template);
            },

            onClickCloseTrashForm : function(){
                window.app.controller.back();
            },
            
            onClickDeleteRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.delete_recipe(recipe_id);
            },
            
            onClickRestoreRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.restore_recipe(recipe_id);
            }
        });
        
        window.app.Submenu_edit_recipe_view = Backbone.View.extend({
            initialize: function(){
                this.render();
                
            },
            events: {
                "click #save" : "onClickSave",
                "click #save_close" : "onClickSaveClose",
                "click #cancel" : "onClickCancel",
            },
            render : function(){
                
                var variables = {'recipe_id' : this.options.recipe_id};
                var template = _.template($("#submenu_edit_recipe_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickSave : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                     
                this.stopListening(window.app.recipe_items_model, "change:recipe_saved");
                
                window.app.recipe_items_model.set({'recipe_saved' : '0'});
                
                this.listenToOnce(window.app.recipe_items_model, "change:recipe_saved", this.load_edit_recipe);
                
                window.app.recipe_items_model.save_recipe(recipe_id);
                
            },
            
            onClickSaveClose : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.save_recipe(recipe_id);
                window.app.Views.edit_recipe_container.close();
                window.app.recipe_items_model.set({current_page : 'my_recipes'});
                if(parseInt(recipe_id)) {
                    window.app.controller.navigate("!/nutrition_recipe/" + recipe_id, true);
                } else {
                    window.app.controller.navigate("!/my_recipes", true);
                }
            },
            
            onClickCancel : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.Views.edit_recipe_container.close();
                window.app.recipe_items_model.set({current_page : 'my_recipes'});
                
                if(parseInt(recipe_id)) {
                    window.app.controller.navigate("!/nutrition_recipe/" + recipe_id, true);
                } else {
                    window.app.controller.navigate("!/my_recipes", true);
                }
            },
            
            load_edit_recipe : function() {
                window.app.Views.edit_recipe_container.close();
                var recipe_id = window.app.recipe_items_model.get('recipe_saved');
                window.app.controller.navigate("!/edit_recipe/" + recipe_id, true);
            }

        });
        
        
        window.app.Submenu_nutrition_database_list_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #add_item" : "onClickAddItem",
            },
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_nutrition_database_list_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickAddItem : function() {
                window.app.controller.navigate("!/add_ingredient", true);
            },

        });
        
        
        window.app.Submenu_nutrition_database_item_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #save" : "onClickSave",
                "click #save_close" : "onClickSaveClose",
                "click #save_new" : "onClickSaveNew",
                "click #cancel" : "onClickCancel",
                
            },
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_nutrition_database_item_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickSave : function() {

                window.app.nutrition_database_model.set({'action' : 'save'});

                $( "#add_ingredient_form" ).submit();
            },
            
            onClickSaveClose : function() {
                               
                window.app.nutrition_database_model.set({'action' : 'save_close'});

                $( "#add_ingredient_form" ).submit();
            },
            
            onClickSaveNew : function() {
                               
                window.app.nutrition_database_model.set({'action' : 'save_new'});

                $( "#add_ingredient_form" ).submit();
            },
            
            onClickCancel : function() {
               window.app.controller.navigate("!/nutrition_database", true);
            },

        });
        
        
        window.app.Submenu_add_diary_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            events: {
                "click #add_diary" : "onClickAddDiary",
                "click #cancel" : "onClickCancel",
            },
            render : function(){
                var variables = {'recipe_id' : this.options.recipe_id, 'nutrition_plan_id' : this.options.nutrition_plan_id};
                var template = _.template($("#submenu_add_diary_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickAddDiary : function(event) {
                var recipe_id = $(event.target).attr("data-recipe_id");
                var number_serves = parseInt($("#number_serves").val());

                if(!number_serves) {
                    $("#number_serves").addClass("red_style_border");
                    return false;
                }
                
                var data = {};
                data.recipe_id = recipe_id;
                data.number_serves = number_serves;
 
                data.number_serves_recipe = this.options.number_serves_recipe;
                
                window.app.recipe_items_model.add_diary(data);
                
            },
            
            onClickCancel : function(event) {
                window.app.controller.back();
            },

        });
        
        
        // on open recipe
        window.app.Recipe_view = Backbone.View.extend({
            
             render : function(data){
                var data = data;
                //console.log(data);
                data.model = this.model;
                var template = _.template($("#recipe_database_view_recipe_template").html(), data);
                this.$el.html(template);
                return this;
            },
            
            events: {
                "click #pdf_button_recipe" : "onClickPdf",
                "click #email_button_recipe" : "onClickEmail",
            },
            
            onClickPdf : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                var user_id = this.model.get('user_id');
                var htmlPage = window.fitness_helper.get('base_url') + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_recipe&id=' + recipe_id + '&client_id=' + user_id;
                window.fitness_helper.printPage(htmlPage);
            },
            
            onClickEmail : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                var data = {};
                data.url = this.model.get('fitness_frontend_url');
                data.view = '';
                data.task = 'ajax_email';
                data.table = '';

                data.id = recipe_id;
                data.view = 'NutritionPlan';
                data.method = 'email_pdf_recipe';
                window.fitness_helper.sendEmail(data);
            },
                        
            loadComments : function(){
                var comments_html = this.options.comments.run();
                $("#comments_wrapper").html(comments_html);
            },
            
            loadVideoPlayer : function() {
                var recipe = this.model.get('recipe');
                
                var no_video_image_big = this.model.get('no_video_image_big');
                
                var video_path = recipe.video;

                var base_url = this.model.get('base_url');

                var imageType = /no_video_image.*/;  

		if (!video_path.match(imageType) && video_path) {  
            
                    jwplayer("recipe_video").setup({
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
            }
            
        });
        
        // list item
        window.app.Recipe_item_view = Backbone.View.extend({
            initialize: function(){

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
                "click .add_favourite" : "onClickAddFavourite",
                "click .remove_favourites" : "onClickRemoveFavourites",
                "click .trash_recipe" : "onClickTrashRecipe",
                "click .delete_recipe" : "onClickDeleteRecipe",
                "click .restore_recipe" : "onClickRestoreRecipe",
                "click .add_diary" : "onClickAddDiary",
                "click .show_recipe_variations" : "onClickShowRecipeVariations",
            },
            
            onClickViewRecipe : function(event) {
                var id = $(event.target).attr("data-id");

                window.app.controller.navigate("!/nutrition_recipe/" + id, true);
            },
            
            onClickCopyRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                this.model.copy_recipe(recipe_id);
            },
            
            onClickAddFavourite : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                this.model.add_favourite(recipe_id);
            },
            
           
            onClickRemoveFavourites : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                this.model.remove_favourite(recipe_id);
            },
            
            onClickTrashRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.trash_recipe(recipe_id);
            },
            
            onClickDeleteRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.delete_recipe(recipe_id);
            },
            
            onClickRestoreRecipe : function(event) {
                var recipe_id = $(event.target).attr('data-id');
                window.app.recipe_items_model.restore_recipe(recipe_id);
            },
            
            onClickAddDiary : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/add_diary/" + id, true);
            },
            onClickShowRecipeVariations : function(event) {
                var id = $(event.target).attr('data-id');
                
                $('.show_recipe_variations[data-id="' + id + '"]').hide();
                $('.recipe_variations[data-id="' + id + '"]').show();
            }
        });
        
        
        // NUTRITION DATABASE list core view
        window.app.Nutrition_database_list_view = Backbone.View.extend({
            initialize: function(){
  
            },
            render : function(){
                var template = _.template($("#nutrition_database_list_template").html());
                this.$el.html(template);
            },
            
            events: {
                "click .search_ingredients" : "onClickSearch",
                "click .clear" : "onClickClear",
            },

            onClickSearch : function() {
                window.app.nutrition_database_model.setLocalStorageItem('currentPage', 1);
                window.app.nutrition_database_model.set({'currentPage' : ""});
                window.app.nutrition_database_model.loadIngredients();
            },
            
            onClickClear : function(){
                $("#search_field").val('');
                window.app.nutrition_database_model.setLocalStorageItem('currentPage', 1);
                window.app.nutrition_database_model.set({'currentPage' : ""});
                window.app.nutrition_database_model.loadIngredients();
            }

        });
        
        // NUTRITION DATABASE list items view
        window.app.Nutrition_database_list_items_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            events: {
                "click .ingredient_name" : "onClickIngredient",
            },


            render : function(){
                var ingredients = this.options.ingredients;
                //console.log(ingredients);
                var data = {'items' : ingredients};
                var template = _.template($("#nutrition_database_list_items_template").html(), data);
                this.$el.html(template);
            },
            
            onClickIngredient : function(event) {
                var id = $(event.target).attr("data-id");
                $('.description').hide();
                $('.description[data-description="' + id + '"]').show();
            }

        });
        
        
         // NUTRITION DATABASE item view
        window.app.Nutrition_database_item_view = Backbone.View.extend({
            initialize: function(){
  
            },
            render : function(){
                var template = _.template($("#nutrition_database_item_template").html());
                this.$el.html(template);
            },
            
            events: {
                "submit #add_ingredient_form" : "onSubmit",
            },
            
            onSubmit : function(event) {
                event.preventDefault();
                window.app.nutrition_database_model.saveIngredient();
                return false;
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
                //console.log(ids);
            }
        });
        
        window.app.Recipe_variations_filter_view = Backbone.View.extend({

            render : function(){
                var template = _.template($("#recipe_variations_filter_template").html());
                this.$el.html(template);
                this.populateSelect();
                return this;
            },
            
            populateSelect : function() {
                
                var self = this;
                this.collection.on("add", function(model) {
                    self.$el.find("#recipe_variations_filter").append('<option value="' + model.get('id') + '">' + model.get('name') + '</option>');
		});
                
                _.each(this.collection.models, function (model) { 
                    self.$el.find("#recipe_variations_filter").append('<option value="' + model.get('id') + '">' + model.get('name') + '</option>');
                }, this);
  
            },
            
            events: {
                "change #recipe_variations_filter" : "onFilterSelect",
            },
            
        
            onFilterSelect : function(event){
                var ids = $(event.target).find(':selected').map(function(){ return this.value }).get().join(",");
                window.app.recipe_items_model.set({'recipe_variations_filter_options' : ids});
                //console.log(ids);
            },
            
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
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
            
            initialize: function(){
                
            },
            
            render : function(){
                var template = _.template($("#recipes_latest_wrapper_template").html());
                this.$el.html(template);
            }
        });



        window.app.MainRecipesContainer_view = Backbone.View.extend({
            
            el: $("#recipe_main_container"), 

            render : function(){
                var template = _.template($("#recipe_database_recipes_container_template").html());
                this.$el.html(template);
            },

        });
        
        window.app.EditRecipeContainer_view = Backbone.View.extend({
            initialize: function(){
                _.bindAll(this, 'render', 'loadTemplate', 'connect_file_upload', 'connect_video_upload');
            
                this.recipe_id = parseInt(this.options.recipe_id);
                
                if(!this.recipe_id) {
                    window.app.recipe_items_model.set({'recipe' : null});
                    this.get_recipe_types();
                }
                
                window.app.recipe_items_model.set({'recipe' : null});
                this.listenToOnce(window.app.recipe_items_model, "change:recipe", this.get_recipe_types);
                window.app.recipe_items_model.getRecipe(this.recipe_id);
              
            },
            
            el: $("#recipe_main_container"), 
            
            get_recipe_types : function() {
                
                this.recipe_types = this.options.filter_categories_model.get('recipe_types');
                
                if(this.recipe_types) this.render();

                this.listenToOnce(this.options.filter_categories_model, "change:recipe_types", this.render);
                this.options.filter_categories_model.getRecipeTypes();
            },

            render : function(){
                
                if (window.app.recipe_items_model.get('current_page') != 'edit_recipe') { 
                    return false;
                }
                
                this.recipe = window.app.recipe_items_model.get('recipe');
                
                this.recipe_types = this.options.filter_categories_model.get('recipe_types');
  
                var self = this;
                window.app.recipe_variations_collection.fetch({
                    success : function (collection, response) {
                        self.loadTemplate({
                           'recipe_types' : self.recipe_types,
                           'recipe_variations' : response,
                           'recipe' : self.recipe,
                           'recipe_items_model' : window.app.recipe_items_model,
                        });
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                });

            },
            
            loadTemplate : function(data) {
                var template = _.template($("#recipe_database_edit_recipe_container_template").html(), data);
                this.$el.html(template);

                this.connect_file_upload();
                this.connect_video_upload();
                if(data.recipe.id) {
                    this.connect_item_description();
                    this.connect_comments();
                }
            },
            
            connect_file_upload : function() {
                var imagepath = '';
                if(this.recipe) {
                    imagepath = this.recipe.image;
                }
                var filename = '';
                if(typeof imagepath !== 'undefined') {
                    var fileNameIndex = imagepath.lastIndexOf("/") + 1;
                    filename = imagepath.substr(fileNameIndex);
                }
                
                
                
                var image_upload_options = {
                    'url' : window.app.recipe_items_model.get('fitness_frontend_url') + '&view=recipe_database&task=uploadImage&format=text',
                    'picture' : filename,
                    'default_image' : window.app.recipe_items_model.get('default_image'),
                    'upload_folder' : window.app.recipe_items_model.get('upload_folder'),
                    'preview_height' : '180px',
                    'preview_width' : '200px',
                    'el' : $('#image_upload_content'),
                    'img_path' : window.app.recipe_items_model.get('img_path'),
                    'base_url' : window.app.recipe_items_model.get('base_url'),
                    'image_name' : this.recipe_id

                };

                var image_upload = $.backbone_image_upload(image_upload_options); 
                image_upload.render();
            },
            
            connect_video_upload : function() {
                
                var videopath = '';
                if(this.recipe) {
                    videopath = this.recipe.video;
                }
                var filename = '';
                if(typeof videopath !== 'undefined') {
                    var fileNameIndex = videopath.lastIndexOf("/") + 1;
                    filename = videopath.substr(fileNameIndex);
                }
                
                var video_upload_options = {
                    'url' : window.app.recipe_items_model.get('fitness_frontend_url') + '&view=recipe_database&task=uploadVideo&format=text',
                    'video' : filename,
                    'default_video_image' : window.app.recipe_items_model.get('default_video_image'),
                    'upload_folder' : window.app.recipe_items_model.get('video_upload_folder'),
                    'preview_height' : '180px',
                    'preview_width' : '250px',
                    'el' : $('#video_upload_content'),
                    'video_path' : window.app.recipe_items_model.get('video_path'),
                    'base_url' : window.app.recipe_items_model.get('base_url'),
                    'video_name' : this.recipe_id

                };

                var video_upload = $.backbone_video_upload(video_upload_options); 
                video_upload.render();
            },
            
            connect_item_description : function() {
                var item_description_options = {
                    'nutrition_plan_id' : this.recipe.id,
                    'fitness_administration_url' : window.app.recipe_items_model.get('fitness_frontend_url'),
                    'main_wrapper' : $("#item_descriptions"),
                    'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
                    'db_table' : '#__fitness_nutrition_recipes_meals',
                    'parent_view' : '',
                    'read_only' : false,
                    'ingredient_model' : 'recipe_database'
                }
                var item_description_html = $.itemDescription(item_description_options, 'meal', 'MEAL ITEM DESCRIPTION', 0).run();
                $("#item_descriptions").html(item_description_html);
            },
            
            connect_comments : function() {
                var comment_options = {
                    'item_id' : this.recipe.id,
                    'fitness_administration_url' : window.app.recipe_items_model.attributes.fitness_frontend_url,
                    'comment_obj' : {'user_name' : window.app.recipe_items_model.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : window.app.recipe_items_model.attributes.recipe_comments_db_table,
                    'read_only' : true,
                    'anable_comment_email' : false
                }
                var comments = $.comments(comment_options, comment_options.item_id, 0);

                var comments_html = comments.run();
                $("#comments_wrapper").html(comments_html);
            },
            
            close :function() {
                $(this.el).unbind();
		//$(this.el).remove();
            }

        });
        
        
        
        
        // COLLECTIONS
        window.app.Recipe_variations_collection = Backbone.Collection.extend({
            url : options.fitness_frontend_url + '&format=text&view=recipe_database&task=recipe_variations&'
        });
        

        
        //Creation global object
        window.app.recipe_items_model = new window.app.Recipe_items_model(options);
        
        window.app.recipes_latest_model = new window.app.Recipes_latest_model(options);
        var filter_options = options;
        filter_options.recipe_items_model = window.app.recipe_items_model;
        window.app.filter_categories_model = new window.app.Filter_categories_model(filter_options);
        
        window.app.Views = { 
            mainmenu: new window.app.Mainmenu_view(),
            submenu: new window.app.Submenu_view(),
            recipes_container : new window.app.MainRecipesContainer_view(),
            nutrition_database_container : new window.app.Nutrition_database_list_view({ el: $("#recipe_main_container")}),
            nutrition_database_item_container : new window.app.Nutrition_database_item_view({ el: $("#recipe_main_container")}),
           
        };
        
        // connect backend Nutrition Database class
        window.recipe_database = $.recipe_database({'specific_gravity' : '' });
        window.recipe_database.run();
        //
        
         // CONTROLLER
         window.app.Controller = Backbone.Router.extend({

            routes : {
                "": "my_recipes", 
                "!/": "my_recipes", 
                "!/my_recipes": "my_recipes", 
                "!/recipe_database": "recipe_database", 
                "!/nutrition_database": "nutrition_database", 
                "!/nutrition_recipe/:id" : "nutrition_recipe",
                "!/nutrition_database/nutrition_recipe/:id" : "nutrition_database_recipe",
                "!/my_favourites" : "my_favourites",
                "!/trash_list" : "trash_list",
                "!/edit_recipe/:id" : "edit_recipe",
                "!/add_ingredient" : "add_ingredient",
                "!/add_diary/:id" : "add_diary",
            },
            
            initialize: function(){
                window.app.recipe_items_model.set({current_page : 'recipe_database'});
                // history
                this.routesHit = 0;
                Backbone.history.on('route', function() { this.routesHit++; }, this);
                //
                window.app.recipe_variations_collection = new window.app.Recipe_variations_collection();
                window.app.recipe_variations_collection.fetch({
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },


            back: function() {
                if(this.routesHit > 1) {
                  window.history.back();
                } else {
                  this.navigate('', {trigger:true, replace:true});
                }
            },

            my_recipes : function () {
                window.app.recipe_items_model.set({state : '1'});
                this.common_actions();
                $("#my_recipes_link").addClass("active_link");
                
                this.load_submenu();
                // populate submenu
                new window.app.Submenu_myrecipes_view({ el: $("#submenu_container")});
            
                window.app.recipe_items_model.set({current_page : 'my_recipes'});
                
                this.recipe_pages_actions();
             },

            recipe_database : function () {
                window.app.recipe_items_model.set({state : '1'});
                this.common_actions();
                $("#recipe_database_link").addClass("active_link");
                
                this.hide_submenu();
                
                window.app.recipe_items_model.set({current_page : 'recipe_database'});
                
                this.recipe_pages_actions();
            },
            
            recipe_pages_actions : function () {
                window.app.recipe_items_model.resetFilter();

                window.app.recipe_items_model.loadRecipes();
                
                window.app.recipe_items_model.connectPagination();
   
                window.app.filter_categories_model.render();
                
                window.app.recipes_latest_model.render();
                
                if(typeof window.app.recipe_variations_filter_view !== 'undefined') {
                    window.app.recipe_variations_filter_view.close();
                }

                window.app.recipe_variations_filter_view = new window.app.Recipe_variations_filter_view({collection : window.app.recipe_variations_collection});

                $("#recipe_variations_filter_wrapper").html(window.app.recipe_variations_filter_view.render().el );
                
                
                
            },
            
            my_favourites : function () {
                window.app.recipe_items_model.set({state : '1'});
                this.common_actions();
                $("#my_favourites_link").addClass("active_link");
                
                this.hide_submenu();
                
                window.app.recipe_items_model.set({current_page : 'my_favourites'});
                
                this.recipe_pages_actions();
             },

            nutrition_database : function () {
                this.load_submenu();
                // populate submenu
                new window.app.Submenu_nutrition_database_list_view({ el: $("#submenu_container")});
                
                this.common_actions();
                $("#nutrition_database_link").addClass("active_link");
                
                window.app.Views.nutrition_database_container.render();
                
                window.app.nutrition_database_model = new window.app.Nutrition_database_model(options);
            },
            
            add_ingredient : function () {
                this.load_submenu();
                // populate submenu
                new window.app.Submenu_nutrition_database_item_view({ el: $("#submenu_container")});
                
                window.app.Views.nutrition_database_item_container.render();
                
                $("#add_ingredient_form").validate();
            },

            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
                
                this.load_mainmenu();
                
                window.app.Views.recipes_container.render();
            },
            
            trash_list : function() {
                this.common_actions();
                
                this.load_submenu();
            
                new window.app.Submenu_trash_list_view({ el: $("#submenu_container")});
            
                window.app.recipe_items_model.set({state : '-2'});
                
                window.app.recipe_items_model.set({current_page : 'trash_list'});
                
                this.recipe_pages_actions();
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
                var current_page = window.app.recipe_items_model.get('current_page');
             
                this.clear_main_ontainer();
                this.load_submenu();
                
                window.app.recipe_items_model.getRecipe(id);
           },
           
           nutrition_database_recipe : function(id) {

                this.clear_main_ontainer();
                
                window.app.recipe_items_model.getRecipe(id);

           },
           
           edit_recipe : function(id) {
               window.app.recipe_items_model.set({current_page : 'edit_recipe'});
               this.load_submenu();
               new window.app.Submenu_edit_recipe_view ({ el: $("#submenu_container"), 'recipe_id' : id});
               
               window.app.Views.edit_recipe_container = new window.app.EditRecipeContainer_view({'recipe_id' : id, 'filter_categories_model' : window.app.filter_categories_model});
           },
           
           add_diary : function(id) {
               var recipe_id = id;
               window.app.recipe_items_model.set({current_page : 'add_diary'});
               this.clear_main_ontainer();
               this.load_submenu();
               window.app.recipe_items_model.getRecipe(recipe_id);
           }
           
 
            
        });

        window.app.controller = new window.app.Controller(); 

        Backbone.history.start();  
        
        
        
        
        
        
        

        

    })($js);
    

        
</script>
