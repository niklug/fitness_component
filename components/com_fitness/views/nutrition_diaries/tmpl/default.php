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

    <h2>NUTRITION DIARY</h2>
    
    <div id="submenu"></div>
    
    <div id="main_container"></div>
    
</div>



<script type="text/javascript">
    
    (function($) {
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'user_name' : '<?php echo JFactory::getUser()->name;?>',
            'user_id' : '<?php echo JFactory::getUser()->id;?>',
            'diary_db_table' : '#__fitness_nutrition_diary',

        };

        window.app = {};
        
        // MODELS 
        window.app.Diary_model = Backbone.Model.extend({

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
                        style_class = 'status_inprogress';
                        text = 'IN PROGRESS';
                        break;
                    case '2' :
                        style_class = 'status_pass';
                        text = 'PASS';
                        break;
                    case '3' :
                        style_class = 'status_fail';
                        text = 'FAIL';
                        break;
                    case '4' :
                        style_class = 'status_distinction';
                        text = 'DISTINCTION';
                        break;
                    case '5' :
                        style_class = 'status_submitted';
                        text = 'SUBMITTED';
                        break;
                    default :
                        style_class = 'status_inprogress';
                        text = 'IN PROGRESS';
                        break;
                }
                var html = '<a style="cursor:default;" href="javascript:void(0)"  class="status_button ' + style_class + '">' + text + '</a>';
                return html;
            },
             
        });
        
        
        window.app.Items_model = window.app.Diary_model.extend({
            
            defaults: {
                current_page: 'list',
                sort_by : 'entry_date',
                order_dirrection : 'DESC',
                state : '1'
            },
            
            initialize: function(){

                this.connectPagination();
                this.pagination_app_model.setLocalStorageItem('currentPage', 1);
            },
            
            connectPagination : function() {
                this.pagination_app_model = $.backbone_pagination({});
                this.pagination_app_model.bind("change:currentPage", this.loadItems, this);
                this.pagination_app_model.bind("change:items_number", this.loadItems, this);
            },
            
            getItems : function(page, limit) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'getDiaries';
                var table = this.get('diary_db_table');
                
                data.sort_by = this.get('sort_by');
                data.order_dirrection = this.get('order_dirrection');

                data.page = page || 1;
                data.limit = limit;
                
               
                data.state = this.get('state');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                    self.set("items", output);
                    self.onGetItems();
                    //console.log(output.length);
                    
                }); 
            },
            
            loadItems : function() {
                //pagination
                var page = this.pagination_app_model.getLocalStorageItem('currentPage');
                var limit = this.pagination_app_model.getLocalStorageItem('items_number');
                this.getItems(page, limit);
            },
            
            onGetItems : function() {
                
                if (this.has("items")){
                    var items = this.get("items");
                    
                    //pagination
                    var item = items[0];
                    var items_total = 0;
                    if (typeof item !== "undefined") {
                        items_total = item.items_total;
                    }
                    this.pagination_app_model.set({'items_total' : items_total});
                }             
            }, 
            
            
            
            trash_item : function(ids) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'updateDiary';
                var table = this.get('diary_db_table');
                
                data.ids = ids;
                
                data.state = '-2';

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("item_trashed", output);
                    self.hide_items(output);
                    console.log(output);
                });
            },
            
            restore_item : function(ids) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'updateDiary';
                var table = this.get('diary_db_table');
                
                data.ids = ids;
                
                data.state = '1';

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("item_restored", output);
                    self.hide_items(output);
                    console.log(output);
                });
            },
            
            delete_item : function(ids) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'deleteDiary';
                var table = this.get('diary_db_table');
                
                data.ids = ids;

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("item_deleted", output);
                    self.hide_items(output);
                    console.log(output);
                });
            },
            
            hide_items : function(items) {
                
                var items = items.split(",");
                
                console.log(items);
                
                _.each(items, function(item, key){ 
                    $("#diary_item_" + item).fadeOut();
                });
            },

        });
        
        // VIEWS

        window.app.Submenu_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            el: $("#submenu"), 
            
            render : function(){
                var template = _.template($("#diary_list_submenu_template").html());
                this.$el.html(template);
            },
            
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            }
        });
        
        window.app.Submenu_list_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            events: {
                "click #add" : "onClickAdd",
                "click #view_trash" : "onClickViewTrash",
                "click #trash_selected" : "onClickTrashSelected",
            },
            
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_diary_list_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickAdd : function() {
                
            },
            
            onClickViewTrash : function() {
                window.app.controller.navigate("!/trash_list", true);
            },
            
            onClickTrashSelected : function() {
                var selected = new Array();
                $('.trash_checkbox:checked').each(function() {
                    selected.push($(this).attr('data-id'));
                });
                var items = selected.join(",");
                
                if(items) {
                    window.app.items_model.trash_item(items);
                }
            },
                        
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            }
        });
        
        window.app.Submenu_trash_list_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            events: {
                "click #close_trash" : "onClickCloseTrash",
                "click #trash" : "onClickTrash",
                "click #delete_selected" : "onClickDeleteSelected",
            },
            
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_diary_trash_list_template").html(), variables);
                this.$el.html(template);
            },
            
           
            onClickCloseTrash : function() {
                window.app.controller.navigate("!/list_view", true);
            },
            
            onClickDeleteSelected : function() {
                var selected = new Array();
                $('.trash_checkbox:checked').each(function() {
                    selected.push($(this).attr('data-id'));
                });
                
                var items = selected.join(",");
                
                if(items) {
                    window.app.items_model.delete_item(items);
                }
                //console.log(selected.join(","));
            },
            
            close :function() {
                $(this.el).unbind();
		$(this.el).remove();
            }
        });
        
        
        window.app.List_view = Backbone.View.extend({
            
            initialize: function(){
                this.listenTo(window.app.items_model, "change:items", this.render);
                window.app.items_model.loadItems();
            },
            
            events: {
                "click #sort_entry_date" : "onClickSortEnryDate",
                "click #sort_status" : "onClickSortStatus",
                "click #sort_score" : "onClickSortScore",
                "click #sort_assessed_by" : "onClickAssessedBy",
                "click #sort_submit_date" : "onClickSubmitDate",
                "click .trash" : "onClickTrash",
                "click #select_trashed" : "onClickSelectTrashed",
                "click .restore" : "onClickRestore",
                "click .delete" : "onClickDelete",
                
            },
            
            el: $("#main_container"), 
            
            render : function(){
                var data = {'model' : window.app.items_model};
                var template = _.template($("#diary_list_template").html(), data);
                this.$el.html(template);
                window.app.items_model.connectPagination();
            },
            
            onClickSortEnryDate : function() {
                window.app.items_model.set({'sort_by' : 'a.entry_date'});
                window.app.items_model.loadItems();
            },
            
            onClickSortStatus : function() {
                window.app.items_model.set({'sort_by' : 'a.status'});
                window.app.items_model.loadItems();
            },
            
            onClickSortScore : function() {
                window.app.items_model.set({'sort_by' : 'a.score'});
                window.app.items_model.loadItems();
            },
            
            onClickAssessedBy : function() {
                window.app.items_model.set({'sort_by' : 'assessed_by_name'});
                window.app.items_model.loadItems();
            },
            
            onClickSubmitDate : function() {
                window.app.items_model.set({'sort_by' : 'a.submit_date'});
                window.app.items_model.loadItems();
            },
            
            onClickTrash : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.items_model.trash_item(id);
            },
            
            onClickRestore : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.items_model.restore_item(id);
            },
            
            onClickDelete : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.items_model.delete_item(id);
            },
            
            onClickSelectTrashed : function(event) {
                $(".trash_checkbox").prop("checked", false);
                
                if($(event.target).attr("checked")) {
                    $(".trash_checkbox").prop("checked", true);
                }
            },
            
            close :function() {
                $(this.el).unbind();
		//$(this.el).remove();
            }
          
        });
        
        // init items model
        window.app.items_model = new window.app.Items_model(options);
        
        // CONTROLLER
        window.app.Controller = Backbone.Router.extend({

            routes : {
                "": "list_view", 
                "!/list_view": "list_view", 
                "!/trash_list" : "trash_list",
            },
            
            list_view : function() {
                this.hide_submenu();
                this.load_submenu('list');
                window.app.items_model.set({'state' : '1'});
                if(typeof window.app.list_view !== 'undefined') {
                    this.reset_main_container();
                }
                window.app.list_view = new window.app.List_view({el : $("#main_container")});
                
            },
            
            load_submenu : function(type) {
                window.app.submenu_view = new window.app.Submenu_view()
                
                if(type == 'list') {
                    window.app.submenu_list_view = new window.app.Submenu_list_view({el : $("#submenu_container")})
                } else if(type == 'trash_list') {
                    window.app.submenu_trash_view = new window.app.Submenu_trash_list_view({el : $("#submenu_container")})
                }
            },
            
            hide_submenu : function() {
                if (window.app.submenu_list_view != null) {
                    window.app.submenu_list_view.close();
                }
                if (window.app.submenu_trash_view != null) {
                    window.app.submenu_trash_view.close();
                }

            },
            
            reset_main_container : function() {
                window.app.list_view.close();
            },
            
            trash_list : function() {
                this.hide_submenu();
                this.load_submenu('trash_list');
                window.app.items_model.set({'state' : '-2'});
                if(typeof window.app.list_view !== 'undefined') {
                    this.reset_main_container();
                }
                window.app.list_view = new window.app.List_view({el : $("#main_container")});
                
             
            }
            
        });

        window.app.controller = new window.app.Controller(); 

        Backbone.history.start();  
        
        
    })($js);
    
</script>
