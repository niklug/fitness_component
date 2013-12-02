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
    
    <div style="padding: 2px;" id="submenu"></div>
    
    <div id="main_container"></div>
    
</div>



<script type="text/javascript">
    
    (function($) {
        
        var options = {
            'fitness_frontend_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
            'calendar_frontend_url' : '<?php echo JURI::root()?>index.php?option=com_multicalendar&task=load&calid=0',
            'fitness_administration_url' : '<?php echo JURI::root();?>index.php?option=com_fitness&tmpl=component&<?php echo JSession::getFormToken(); ?>=1',
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
            
            status_html_stamp : function(status) {
                var class_name, text;
                switch(status) {
                    case '2' :
                        class_name = 'status_pass_stamp';
                        break;
                    case '3' :
                        class_name = 'status_fail_stamp';

                        break;
                    case '4' :
                        class_name = 'status_distinction_stamp';
                        break;
                    case '5' :
                        class_name = 'status_submitted_stamp';
                        break;
                    default :
                        break;
                }

                var html = '<div class=" status_button_stamp ' + class_name + '"></div>';

                return html;
            }
             
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
                });
            },
            
            hide_items : function(items) {
                var items = items.split(",");
                _.each(items, function(item, key){ 
                    $("#diary_item_" + item).fadeOut();
                });
            },

        });

        
        window.app.Item_model = window.app.Diary_model.extend({
            
            initialize: function(){
                _.bindAll(this, 'getDiaryDays', 'disableDays', 'getActivePlanData', 'getNutritionTarget', 'setTargetData', 'sendSubmitEmail');
                this.bind("change:disabled_days", this.getActivePlanData, this);
                this.getDiaryDays();
            },
            
            getDiaryDays : function() {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'getDiaryDays';
                var table = this.get('diary_db_table');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("disabled_days", output);
                });
            },

            disableDays : function(date) {
                
                var disabledDays = this.get('disabled_days');
                    
                var calendar_date = moment(date).format("YYYY-MM-DD");
                var result =  [true];
                if(_.contains(disabledDays, calendar_date)) {
                    result =  [false];
                }
                return result
            },
            
            getActivePlanData : function() {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'getActivePlanData';
                var table = this.get('diary_db_table');

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("active_plan_data", output);
                });
            },
            
            getNutritionTarget : function(type, active_plan_data) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'getNutritionTarget';
                var table = this.get('diary_db_table');
                
                data.type = type;
                
                data.nutrition_plan_id = active_plan_data.id;
                        
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set(type + "_target", output);
                    //console.log(output);
                });
            },
            
            setTargetData : function(activity_level) {
                var activity_data;
                if(activity_level == '1') activity_data = this.get('heavy_target');
                if(activity_level == '2') activity_data = this.get('light_target');
                if(activity_level == '3') activity_data = this.get('rest_target');

                var calories = activity_data.calories;
                var water = activity_data.water;

                $("#calories_value").html(calories);
                $("#water_value").html(water);

                $("#pie_td, .calories_td").css('visibility', 'visible');


                //console.log(activity_data);
                var data = [
                    {label: "Protein:", data: [[1, activity_data.protein]]},
                    {label: "Carbs:", data: [[1, activity_data.carbs]]},
                    {label: "Fat:", data: [[1, activity_data.fats]]}
                ];

                var container = $("#placeholder_targets");

                var targets_pie = $.drawPie(data, container, {'no_percent_label' : false});

                targets_pie.draw(); 
            },
            
            save_item : function(data) {
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'updateDiaryItem';
                var table = this.get('diary_db_table');
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("item_saved", output);
                });
            },
            
                               
            getItem : function(id) {
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = 'nutrition_diaries';
                var task = 'getDiaryItem';
                var table = this.get('diary_db_table');
                data.id = id;
                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    self.set("item", output);
                    //console.log(output);
                });
            },
            
            sendSubmitEmail : function(){
                var id = this.get('item_saved');
                var data = {};
                var url = this.get('fitness_frontend_url');
                var view = '';
                var task = 'ajax_email';
                var table = '';
 
                data.id = id;
                data.view = 'NutritionDiary';
                data.method = 'DiarySubmitted';

                var self = this;
                this.ajaxCall(data, url, view, task, table, function(output) {
                    //console.log(output);
                });
            }
            
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
                $(this.el).empty();
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
                window.app.controller.navigate("!/create_item", true);
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
                $(this.el).empty();
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
                $(this.el).empty();
            }
        });
        
        
        window.app.Submenu_create_item_view = Backbone.View.extend({
            initialize: function(){
                this.render();
            },
            
            events: {
                "click #next" : "onClickNext",
                "click #cancel" : "onClickCancel",
            },
            
            render : function(){
                var variables = {};
                var template = _.template($("#submenu_diary_create_item_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickNext : function() {
                $( "#create_item_form" ).submit();
            },
            
            onClickCancel : function() {
                window.app.controller.navigate("!/list_view", true);
            },

            close : function() {
                $(this.el).unbind();
                $(this.el).empty();
            }
        });
        
        
        window.app.Submenu_item_view = Backbone.View.extend({
            initialize: function(){
                _.bindAll(this, 'render', 'onClickSave', 'prepareSaveData', 'onClickDelete');
            },
            
            events: {
                "click #save" : "onClickSave",
                "click #save_close" : "onClickSaveClose",
                "click #close" : "onClickClose",
                "click #submit" : "onClickSubmit",
                "click #delete" : "onClickDelete",
            },
            
            render : function(){
                this.model = window.app.item_model;
                var variables = {'item_id' : this.options.item_id, 'model' : this.model};
                var template = _.template($("#submenu_diary_item_template").html(), variables);
                this.$el.html(template);
            },
            
            onClickSave : function(event) {
                var id =  $(event.target).attr('data-id');
                var data = this.prepareSaveData(id);
                
                this.model.set({'item_saved' : null});
                this.listenToOnce(this.model, "change:item_saved", this.redirectToItem);
                this.model.save_item(data);
      
            },
            
            onClickSaveClose : function(event) {
                var id =  $(event.target).attr('data-id');
                var data = this.prepareSaveData(id);
                
                this.model.set({'item_saved' : null});
                this.listenToOnce(this.model, "change:item_saved", this.redirectToList);
                this.model.save_item(data);
            },
            
            onClickSubmit : function(event) {
                var id =  $(event.target).attr('data-id');
                var data = this.prepareSaveData(id);
                
                data.submit_date = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
                data.status = '5';
                
                this.model.set({'item_saved' : null});
                this.listenToOnce(this.model, "change:item_saved", this.onSubmit);
                this.model.save_item(data);
      
            },
            
            onSubmit : function() {
                this.model.sendSubmitEmail()
                this.redirectToItem();
            },
            
            redirectToItem : function() {
                var id = this.model.get('item_saved');
                window.app.controller.navigate("!/list_view/", true);
                window.app.controller.navigate("!/item_view/" + id, true);
            },
            
            redirectToList : function() {
                window.app.controller.navigate("!/list_view", true);
            },            
            
            prepareSaveData : function(id) {
                this.model = window.app.item_model;
                this.active_plan_data = this.model.get('active_plan_data');
                this.item = this.model.get('item');
                var data = {};
                data.id = this.item.id;
                data.entry_date = $("#entry_date").val();
                data.activity_level = this.model.get('activity_level');
                data.nutrition_plan_id = this.active_plan_data.id;
                data.client_id = this.active_plan_data.client_id;
                data.trainer_id = this.active_plan_data.trainer_id;
                data.goal_category_id = this.active_plan_data.mini_goal;
                data.nutrition_focus = this.active_plan_data.nutrition_focus;
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
                data.state = '1';
                return data;
            },
            
            onClickClose : function() {
                window.app.controller.navigate("!/list_view", true);
            },
            
            onClickDelete : function(event) {
                var id =  $(event.target).attr('data-id');
                var model = window.app.items_model;
                this.listenToOnce(model, "change:item_deleted", this.redirectToList);
                model.delete_item(id);
            },

            close : function() {
                $(this.el).unbind();
                $(this.el).empty();
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
                "click .preview" : "onClickPreview",
                
            },
            
            el: $("#main_container"), 
            
            render : function() {
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
            
            onClickPreview : function(event) {
                var id = $(event.target).attr('data-id');
                window.app.controller.navigate("!/item_view/" + id, true);
            },
            
            close :function() {
                $(this.el).unbind();
                window.app.items_model.set({'items' : null});
                $(this.el).empty();
                
	    }
          
        });
        
        
        
         window.app.Create_item_view = Backbone.View.extend({
            
            initialize: function(){
                _.bindAll(this, 'render', 'setVariables', 'onSubmit', 'onChooseTrainingDay');
                this.listenTo(this.model, "change:active_plan_data", this.onActivePlanDataChange); 
            },
            
            onActivePlanDataChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:heavy_target", this.onHeavyTargetChange); 
                this.model.getNutritionTarget('heavy', active_plan_data);
            },
            
            onHeavyTargetChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:light_target", this.onLightTargetChange); 
                this.model.getNutritionTarget('light', active_plan_data);
            },
            
            onLightTargetChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:rest_target", this.render); 
                this.model.getNutritionTarget('rest', active_plan_data);
            },
                    
            events: {
                "submit #create_item_form" : "onSubmit",
                "click .activity_level" : "onChooseTrainingDay"
            },
            
            el: $("#main_container"), 
            
            render : function() {
                this.setVariables();
                //console.log(this.model.get('active_plan_data'));
                var data = {'model' : this.model};
                var template = _.template($("#diary_create_item_template").html(), data);
                this.$el.html(template);
                this.loadPlugins();
            },
            
            setVariables : function() {
                this.active_plan_data = this.model.get('active_plan_data');
                this.heavy_target = this.model.get('heavy_target');
                this.light_target = this.model.get('light_target');
                this.rest_target = this.model.get('rest_target');
            },

            loadPlugins : function() {
                $("#create_item_form").validate();
                this.setCalendar();
            },
            
            onSubmit : function(event) {
                event.preventDefault();
                var data = {};
                data.id = '';
                data.entry_date = $("#entry_date").val();
                data.activity_level = this.activity_level;
                data.nutrition_plan_id = this.active_plan_data.id;
                data.client_id = this.active_plan_data.client_id;
                data.trainer_id = this.active_plan_data.trainer_id;
                data.goal_category_id = this.active_plan_data.mini_goal;
                data.nutrition_focus = this.active_plan_data.nutrition_focus;
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
                data.state = '1';
                //console.log(data);
                this.model.set({'item_saved' : null});
                this.listenToOnce(this.model, "change:item_saved", this.redirect);
                this.model.save_item(data);
                return false;
            },
            
            redirect : function() {
                var id = this.model.get('item_saved');
                window.app.controller.navigate("!/item_view/" + id, true);
            },
            
            setCalendar : function() {
                var self = this;
                $( "#entry_date" ).datepicker({ dateFormat: "yy-mm-dd",  minDate : 0, beforeShowDay: self.model.disableDays});
            },
            
            onChooseTrainingDay : function(event) {
                this.activity_level = $(event.target).val();
                this.model.setTargetData(this.activity_level);
                $("#next").show();
            },
            
            close :function() {
                $(this.el).unbind();
                $(this.el).empty();
                
	    }
          
        });
        
        
        window.app.Item_view = Backbone.View.extend({
            
            initialize: function(){
                _.bindAll(this, 'render', 'setTarget', 'connectMealsBlock', 'close');
                                
                this.listenTo(this.model, "change:active_plan_data", this.onActivePlanDataChange);
            },
            
            onActivePlanDataChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:heavy_target", this.onHeavyTargetChange); 
                this.model.getNutritionTarget('heavy', active_plan_data);
            },
            
            onHeavyTargetChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:light_target", this.onLightTargetChange); 
                this.model.getNutritionTarget('light', active_plan_data);
            },
            
            onLightTargetChange : function() {
                var active_plan_data = this.model.get('active_plan_data');
                this.listenTo(this.model, "change:rest_target", this.onRestTargetChange); 
                this.model.getNutritionTarget('rest', active_plan_data);
            },

            
            onRestTargetChange : function() {
                var item_id = this.options.item_id;
                this.listenTo(this.model, "change:item", this.render); 
                this.model.getItem(item_id)
            },
            
            events: {
                "click .activity_level" : "onChooseTrainingDay"
            },
            
            el: $("#main_container"), 
            
            render : function() {
                window.app.submenu_item_view.render();
                 
                this.item = this.model.get('item');
                this.active_plan_data = this.model.get('active_plan_data');
                var data = {'model' : this.model};
                var template = _.template($("#diary_item_template").html(), data);
                this.$el.html(template);
                this.loadPlugins();
                
                this.setTarget();
                this.connectMealsBlock();
            },
            
            
            setTarget : function() {
                this.activity_level = this.item.activity_level;

                $('#jform_activity_level' + (parseInt(this.activity_level) - 1)).prop('checked',true);
                
                this.model.setTargetData(this.activity_level);
                
                this.model.set({'activity_level' : this.activity_level});
            },
            
            loadPlugins : function() {
                $("#create_item_form").validate();
                this.setCalendar();
            },
            
            setCalendar : function() {
                var self = this;
                $( "#entry_date" ).datepicker({ dateFormat: "yy-mm-dd",  minDate : 0, beforeShowDay: self.model.disableDays});
            },
            
            onChooseTrainingDay : function(event) {
                this.activity_level = $(event.target).val();
                this.model.setTargetData(this.activity_level);
                
                this.model.set({'activity_level' : this.activity_level});
            },
            
            connectMealsBlock : function() {
                
                var submitted = false;
                if (this.item.submit_date && (this.item.submit_date != '0000-00-00 00:00:00')) {
                    submitted = true;
                }
                
                var scored = false;
                if(_.contains(['2', '3', '4'], this.item.status)) {
                    scored = true;
                }
    
                var item_description_options = {
                    'nutrition_plan_id' : this.item.id,
                    'logged_in_admin' : false,
                    'fitness_frontend_url' : this.model.attributes.fitness_frontend_url,
                    'fitness_administration_url' : this.model.attributes.fitness_administration_url,
                    'main_wrapper' : $("#diary_guide"),
                    'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
                    'db_table' : '#__fitness_nutrition_diary_ingredients',
                    'parent_view' : 'nutrition_diary_frontend',
                    'read_only' : submitted
                }
                
                
                var nutrition_meal_options = {
                    'main_wrapper' : $("#meals_wrapper"),
                    'nutrition_plan_id' :  this.item.id,
                    'fitness_administration_url' : this.model.attributes.fitness_administration_url,
                    'add_meal_button' : $("#add_plan_meal"),
                    'activity_level' : "input[name='jform[activity_level]']",
                    'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""},
                    'db_table' : '#__fitness_nutrition_diary_meals',
                    'read_only' : submitted,
                    'import_date' : true,
                    'import_date_source' : '#jform_entry_date'
                }
                
                var nutrition_comment_options = {
                    'item_id' : this.item.id,
                    'fitness_administration_url' : this.model.attributes.fitness_administration_url,
                    'comment_obj' : {'user_name' : this.model.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_diary_meal_comments',
                    'read_only' : scored
                }
                
                var nutrition_bottom_comment_options = {
                    'item_id' : this.item.id,
                    'fitness_administration_url' : this.model.attributes.fitness_administration_url,
                    'comment_obj' : {'user_name' : this.model.attributes.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_diary_comments',
                    'read_only' : scored
                }
                
                var calculate_summary_options = {
                    'activity_level' : "input[name='jform[activity_level]']",
                    'chart_container' : $("#placeholder_scope"),
                    'draw_chart' : true
                }
                
                var macronutrient_targets_options = {
                    'main_wrapper' : $("#daily_micronutrient"),
                    'fitness_administration_url' : this.model.attributes.fitness_administration_url,
                    'protein_grams_coefficient' : 4,
                    'fats_grams_coefficient' : 9,
                    'carbs_grams_coefficient' : 4,
                    'nutrition_plan_id' : this.item.nutrition_plan_id,
                    'empty_html_data' : {'calories' : "", 'water' : "", 'protein' : "", 'fats' : "", 'carbs' : ""}
                }
              
    
                this.nutrition_meal = $.nutritionMeal(nutrition_meal_options, item_description_options, nutrition_comment_options);
                this.calculateSummary =  $.calculateSummary(calculate_summary_options);

                    // append targets fieldsets
                this.macronutrient_targets_heavy = $.macronutrientTargets(macronutrient_targets_options, 'heavy', 'HEAVY TRAINING DAY');

                this.macronutrient_targets_light = $.macronutrientTargets(macronutrient_targets_options, 'light', 'LIGHT TRAINING DAY');

                this.macronutrient_targets_rest = $.macronutrientTargets(macronutrient_targets_options, 'rest', 'RECOVERY / REST DAY');
                //bottom comments
                this.plan_comments = $.comments(nutrition_bottom_comment_options, nutrition_comment_options.item_id, 0);

                this.nutrition_meal.run();
                
                
                this.calculateSummary.run();

                this.macronutrient_targets_heavy.run();
                this.macronutrient_targets_light.run();
                this.macronutrient_targets_rest.run();

                var plan_comments_html = this.plan_comments.run();
                $("#plan_comments_wrapper").html(plan_comments_html);

            },            
            
            close :function() {
                $(this.el).unbind();
                $(this.el).empty();

                if(typeof this.calculateSummary !== 'undefined') {
                    clearInterval(this.calculateSummary.interval);
                }
                
                delete this.calculateSummary;
                delete this.nutrition_meal;
                delete this.macronutrient_targets_heavy;
                delete this.macronutrient_targets_light;
                delete this.macronutrient_targets_rest;
                delete this.plan_comments;

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
                "!/create_item" : "create_item",
                "!/item_view/:id" : "item_view",
            },
            
            list_view : function() {
                
                this.hide_submenu();
                this.load_submenu('list');
                window.app.items_model.set({'state' : '1'});
                this.reset_main_container();
                window.app.list_view = new window.app.List_view();
            },
            
            create_item : function() {
                this.hide_submenu();
                this.reset_main_container();
                this.load_submenu('create_item');
                window.app.item_model = new window.app.Item_model(options);
                window.app.item_model.unset('disabled_days');
                window.app.item_model.unset('active_plan_data');
                window.app.item_model.unset('heavy_target');
                window.app.item_model.unset('light_target');
                window.app.item_model.unset('rest_target');
                window.app.item_model.unset('item');
                
                window.app.create_item_view = new window.app.Create_item_view({model : window.app.item_model});
            },
            
            
            load_submenu : function(type, id) {
                window.app.submenu_view = new window.app.Submenu_view()
                
                if(type == 'list') {
                    window.app.submenu_list_view = new window.app.Submenu_list_view({el : $("#submenu_container")})
                } else if(type == 'trash_list') {
                    window.app.submenu_trash_view = new window.app.Submenu_trash_list_view({el : $("#submenu_container")})
                } else if(type == 'create_item') {
                    window.app.submenu_create_item_view = new window.app.Submenu_create_item_view({el : $("#submenu_container")})
                } else if(type == 'item') {
                    window.app.submenu_item_view = new window.app.Submenu_item_view({el : $("#submenu_container"), 'item_id' : id})
                }
            },
            
            hide_submenu : function() {
                if (typeof window.app.submenu_list_view !== 'undefined') {
                    window.app.submenu_list_view.close();
                }
                if (typeof window.app.submenu_trash_view !== 'undefined') {
                    window.app.submenu_trash_view.close();
                }
                if (typeof window.app.submenu_view !== 'undefined') {
                    window.app.submenu_view.close();
                }
                if (typeof window.app.submenu_create_item_view !== 'undefined') {
                    window.app.submenu_create_item_view.close();
                }
            },
            
            reset_main_container : function() {
                if (typeof window.app.list_view !== 'undefined') {
                    window.app.list_view.close();
                }
                if (typeof window.app.create_item_view !== 'undefined') {
                    window.app.create_item_view.close();
                }
                if (typeof window.app.item_view !== 'undefined') {
                    window.app.item_view.close();
                }
                
                if (typeof window.app.item_model !== 'undefined') {
                    window.app.item_model.unset();
                }
                
                
            },
            
            trash_list : function() {
                this.hide_submenu();
                this.load_submenu('trash_list');
                window.app.items_model.set({'state' : '-2'});
                this.reset_main_container();
                window.app.list_view = new window.app.List_view();
            },
            
            item_view : function(id) {

                this.hide_submenu();
                this.reset_main_container();
                this.load_submenu('item', id);
                window.app.item_model = new window.app.Item_model(options);
                window.app.item_model.unset('disabled_days');
                window.app.item_model.unset('active_plan_data');
                window.app.item_model.unset('heavy_target');
                window.app.item_model.unset('light_target');
                window.app.item_model.unset('rest_target');
                window.app.item_model.unset('item');
                window.app.item_view = new window.app.Item_view({model : window.app.item_model, 'item_id' : id});
            },
            
            
        });

        window.app.controller = new window.app.Controller(); 

        Backbone.history.start();  
        
        
    })($js);
    
</script>
