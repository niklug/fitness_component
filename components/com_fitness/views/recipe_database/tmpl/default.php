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

<style>

            nav {
                padding: 0;
                width: 600px;
                margin: auto;
                position:relative; 
                text-align: center;
            }

            nav a {
                display: inline-block;
           }

            nav ul {
                display: inline;
            }	

            nav li {
                display: inline;
                list-style-type: none;
            }

        </style>

<div style="opacity: 1;" class="fitness_wrapper">
    <h2>RECIPE DATABASE</h2>
    
    <div id="recipe_mainmenu"></div>
    
    <div id="recipe_submenu"></div>
    
    <div id="recipe_main_container"></div>
    
</div>



<script type="text/javascript">
    
    (function($) {
    
    
        <!-- START Pagination -->
        var Pagination_item = Backbone.Model.extend({});

        var Pagination_items = Backbone.Collection.extend({
            model: Pagination_item,
        });

        var Pagination_app_model = Backbone.Model.extend({
            defaults: {
                currentPage : "",
                items_number : 10
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
            }
        });

        var Pagination_page_view = Backbone.View.extend({
            tagName: "li",
            template: _.template($("#template-page").html()),
            events: {
                "click #a-page-item": "onPageClick",
                
            },
            onPageClick: function() {
                var currentPage = this.options.pageIndex;
                this.model.setLocalStorageItem('currentPage',  currentPage);
                this.model.set({currentPage: currentPage});

            },
       
            render: function() {
                $(this.el).html(this.template({pageNumber: this.options.pageIndex}));
                return this;
            },
        });


         var Pagination_view = Backbone.View.extend({

            initialize: function(){

                this.render();
                this.itemsCollection = new Pagination_items();
                this.itemsCollection.bind("add", this.renderPages, this);
                this.addItems();
            },

            render : function(){
                var template = _.template($("#backbone_pagination_template").html());
                this.$el.html(template);
                var items_number = this.model.getLocalStorageItem('items_number');
                $("#items_number").val(items_number);

            },

            events: {
                "change #items_number": "onLimitChange"
            },
            
            onLimitChange: function(event) {
                var items_number = $(event.target).val();
                this.model.setLocalStorageItem('currentPage',  1);
                this.model.setLocalStorageItem('items_number', items_number);
                this.model.set({'items_number' : items_number});
                
             
            },

            addItems: function() {
                var items_total = this.model.get('items_total');
                var self = this;
                for (var i = 0; i < items_total; i++) {
                    self.itemsCollection.add(new Pagination_item());
                }
            },

            renderPages: function() {
                var length = this.itemsCollection.length;
                var items_number = this.model.getLocalStorageItem('items_number');
                var pages = length / items_number | 0;
                if (pages > 0) {
                    $("#ul-pagination li").remove();
                    for (i = 1; i <= pages; i++) {
                        var pageItem = new Pagination_page_view({pageIndex: i, model : this.model});
                        $("#ul-pagination").append(pageItem.render().el);
                    }
                }
            }
        });
        
        <!-- END Pagination -->
        
        
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'recipes_db_table' : '#__fitness_nutrition_recipes',
        };
        
        // MODELS 
        Recipe_database_model = Backbone.Model.extend({
            defaults: {
                'page' : 1,
                'limit' : 10
            },

            initialize: function(){
                this.pagination_app_model = new Pagination_app_model();

                this.pagination_app_model.bind("change:currentPage", this.loadRecipes, this);
                this.pagination_app_model.bind("change:items_number", this.loadRecipes, this);
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
            
            getRecipes : function(page, limit) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'recipe_database';
                var task = 'getRecipes';
                var table = this.get('recipes_db_table');

                data.page = page || 1;
                data.limit = limit;

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("recipes", output);
                });
            },
            
            loadRecipes : function() {

                var page = this.pagination_app_model.getLocalStorageItem('currentPage');
                var limit = this.pagination_app_model.getLocalStorageItem('items_number');
                
                console.log('page ' + page);
                console.log('limit ' + limit);
            
                this.getRecipes(page, limit);
                this.listenToOnce(this, "change:recipes", this.onGetRecipes);
            },
            
            onGetRecipes : function() {
                if (this.has("recipes")){
                    var recipes = this.get("recipes");
                    var item = recipes[0];
                    var items_total = item.items_total; 
                    this.pagination_app_model.set({'items_total' : items_total});
                    var pagination_view = new Pagination_view({el: $("#pagination_container"), model : this.pagination_app_model});
                    
                    this.populateRecipes(recipes);
                }
            },
            populateRecipes : function(recipes) {
                $("#recipe_database_items_wrapper").html('');
                _.each(recipes, function(item){
                    var recipe_item = new Recipe_item_view({ el: $("#recipe_database_items_wrapper"), 'data' : item});
                    recipe_item.render();
                });
            }
            
            
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
                var data = this.options.data;

                data.model = new Recipe_database_model(options);
                
                var template = _.template($("#recipe_database_item_template").html(), data);
                this.$el.append(template);
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
                
                if (Views.main_container != null) {
                   var model = new Recipe_database_model(options);
                   model.loadRecipes();
                }
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
            }
            
        });

        var controller = new Controller(); 

        Backbone.history.start();  
        
        
        
        
        
        
        

        

    })($js);
    

        
</script>