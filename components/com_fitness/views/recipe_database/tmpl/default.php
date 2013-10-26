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
        
        // MODELS 
        Recipe_database_model = Backbone.Model.extend({
            defaults: {
                'pages_number' : 10,
                'list_type' : '0'
            },

            initialize: function(){

            },

            ajaxCall : function(data, url, view, task, table, handleData) {
                return $.AjaxCall(data, url, view, task, table, handleData);
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
        
        
        var Recipe_item_view = Backbone.View.extend({
            render : function(){
                var variables = {
                    'id' : '1',
                    'name' : 'Paleo Chicken Soup',
                    'type' : 'Chicken & Poultry, Soups',
                    'serves' : '4',
                    'author' : 'Jodi Boam',
                    'status' : '1',
                    'trainer' : 'Paul Meier',
                    'image' : 'images/images.jpeg',
                    'model' : new Recipe_database_model()
                };
                
                var template = _.template($("#recipe_database_item_template").html(), variables);
                this.$el.html(template);
            }
        });
        
        
        var MainContainer_view = Backbone.View.extend({
            
            el: $("#recipe_main_container"), 

            render : function(){
                
                var variables = {

                };
                
                var template = _.template($("#recipe_database_main_container_template").html(), variables);
                this.$el.html(template);
                
                // load recipe item
                var recipe_item = new Recipe_item_view({ el: $("#recipe_database_items_wrapper")});
                recipe_item.render();
            }
        });
        
        
        

        Views = { 
            mainmenu: new Mainmenu_view(),
            submenu: new Submenu_view(),
            main_container : new MainContainer_view(),
   
        };
        
         // CONTROLLER
         var Controller = Backbone.Router.extend({

            routes : {
                "": "my_recipes", 
                "!/": "my_recipes", 
                "!/my_recipes": "my_recipes", 
                "!/recipe_database": "recipe_database", 
                "!/nutrition_database": "nutrition_database", 
            },

            my_recipes : function () {
                this.common_actions();
                $("#my_recipes_link").addClass("active_link");
                this.load_submenu();
                
       
            },

            recipe_database : function () {
                this.common_actions();
                $("#recipe_database_link").addClass("active_link");
                this.hide_submenu()
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
            
        });

        var controller = new Controller(); 

        Backbone.history.start();  
        
        
        
        

    })($js);
    

        
</script>