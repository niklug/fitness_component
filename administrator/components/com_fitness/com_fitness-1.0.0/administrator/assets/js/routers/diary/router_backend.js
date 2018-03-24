define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/diary/diaries',
        'collections/diary/meal_entries',
        'collections/diary/diary_meals',
        'collections/diary/meal_ingredients',
        'models/diary/request_params_diaries',
        'models/diary/diary',
        'models/diary/plan_data',
        'views/diary/backend/menus/submenu_item',
        'views/diary/backend/list',
        'views/diary/backend/item',
        'views/graph/progress_graph',
        'views/diary/backend/search_block'
        
], function (
        $,
        _,
        Backbone,
        app,
        Diaries_collection,
        Meal_entries_collection,
        Diary_meals_collection,
        Meal_ingredients_collection,
        Request_params_diaries_model,
        Diary_model,
        Plan_data_model,
        Submenu_item_view,
        List_view,
        Item_view,
        Progress_graph_view,
        Search_block_view
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
            app.collections.items = new Diaries_collection();
            
            app.collections.items.bind("sync", this.connectGraph, this);
            
            this.onClientChange();

            app.options.client_id = localStorage.getItem('client_id');
            
            app.models.request_params_diaries = new Request_params_diaries_model({client_id : app.options.client_id});
            app.models.request_params_diaries.bind("change", this.get_diaries, this);

            //active plan data
            app.models.active_plan_data = new Plan_data_model();
            
            
            app.collections.meal_entries = new Meal_entries_collection();
            app.collections.diary_meals = new Diary_meals_collection();
            app.collections.meal_ingredients = new Meal_ingredients_collection();
        },

        routes: {
            "": "list_view", 
            "!/list_view": "list_view", 
            "!/trash_list" : "trash_list",
            "!/create_item" : "create_item",
            "!/item_view/:id" : "item_view"
        },
        
        back: function() {
            if(this.routesHit > 1) {
              window.history.back();
            } else {
              this.navigate('', {trigger:true, replace:true});
            }
        },
        
        onClientChange : function() {
            var self = this;
            $("#client_id").die().live('change', function() {
                var client_id = $(this).val();
                app.options.client_id = client_id;
                localStorage.setItem('client_id', client_id);
                app.models.request_params_diaries.set({client_id : client_id});
                self.navigate("!/list_view", true);
            });
        },
        
        get_diaries : function() {
            app.collections.items.reset();
            app.collections.items.fetch({
                data : app.models.request_params_diaries.toJSON(),
                success: function (collection, response) {
                    //console.log(collection.toJSON());
                },
                error: function (collection, response) {
                    alert(response.responseText);
                }
            });  
        },
        
        
        list_view : function() {
            app.models.request_params_diaries.set({page : 1,  state : '*', uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        set_diaries_model : function() {
            app.collections.items.reset();
            app.models.request_params_diaries.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
        },
        
        trash_list : function() {
            $("#submenu_container").html(new Submenu_trash_list_view().render().el);
            
            app.models.request_params_diaries.set({page : 1, current_page : 'trash_list',  state : '-2', uid : app.getUniqueId()});
            
            this.list_actions();
        },
        
        list_actions : function () {
            $("#progress_graph_container, #header_wrapper, #submenu_container").empty();
            this.connectGraph();
            
            new Search_block_view({el : $("#header_wrapper"), model : app.models.request_params_diaries, collection : app.collections.items});
            
            $("#main_container").html(new List_view({collection : app.collections.items}).render().el);
            
            app.models.pagination = $.backbone_pagination({});

            app.models.pagination.bind("change:currentPage", this.set_diaries_model, this);

            app.models.pagination.bind("change:items_number", this.set_diaries_model, this);
        },

        item_view : function(id) {
            
            app.models.diary = new Diary_model({id : id});
            var self = this;
            $.when(

                    app.models.diary.fetch({
                        success : function (model, response) {
                            app.collections.items.add(model);
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                    
                    ,

                    app.models.active_plan_data.fetch({
                        data : {diary_id : id},
                        success : function (model, response) {
                            //console.log(model.toJSON());
                        },
                        error : function (model, response) {
                            alert(response.responseText);
                        }
                    })
                            
                    ,
                    
                    app.collections.meal_entries.fetch({
                        data : {diary_id : id},
                        success: function (collection, response) {
                            //console.log(collection.toJSON());
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                    
                    ,
                    
                    app.collections.diary_meals.fetch({
                        data : {diary_id : id},
                        success: function (collection, response) {
                            //console.log(collection.toJSON());
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                            
                    ,
                    
                    app.collections.meal_ingredients.fetch({
                        data : {diary_id : id},
                        success: function (collection, response) {
                            //console.log(collection.toJSON());
                        },
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                
                ).then(function() {
                    $("#progress_graph_container, #header_wrapper, #submenu_container").empty();
                    var edit_allowed = self.edit_allowed(app.models.diary);
                    app.models.diary.set({edit_allowed : edit_allowed});
                    self.load_item_view(app.models.diary);
                });
        },
        
        load_item_view : function(model) {
            $("#main_container").html(new Item_view({model : model}).render().el);
            $("#submenu_container").html(new Submenu_item_view({model : model}).render().el);
        },
        
        copy_meal_entry : function(id, diary_id){
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'nutrition_diaries';
            var task = 'copyMealEntry';
            var table = '';

            data.id = id;
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                
                self.navigate("");
                self.navigate("!/item_view/" + diary_id, true);
            });
        },

        copy_diary_meal : function(id, diary_id){
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'nutrition_diaries';
            var task = 'copyDiaryMeal';
            var table = '';

            data.id = id;
            var self = this;
            $.AjaxCall(data, url, view, task, table, function(output) {
                
                self.navigate("");
                self.navigate("!/item_view/" + diary_id, true);
            });
        },    
        
        connectGraph : function() {
            var collection = app.collections.items;
            
            this.progress_graph = new Progress_graph_view({
                head_title : 'NUTRITION DIARY FINAL SCORES',
                el : "#progress_graph_container",
                collection : collection,
                style : '',
                color : "#287725",
                data_field_x : 'entry_date',
                data_field_y : 'score',
                y_title : 'Final Score (%)',
                tooltip : true,
                setTooltipHtml : this.setTooltipHtml
            });
        },
        
        setTooltipHtml : function(html, model) {
            html +=  "Entry Date: " +  moment(new Date(Date.parse(model.get('entry_date')))).format("ddd, D MMM  YYYY") + "</br>";
            html +=  "Final Score: " +  model.get('score') + '%';
            return html;
        },
        
        update_list : function() {
            app.models.request_params_diaries.set({ uid : app.getUniqueId()});
        },
        
        edit_allowed : function(model) {
            var access = false;
            
            var status = model.get('status');
            
            if(status ==  app.options.statuses.INPROGRESS_DIARY_STATUS.id) {
                access = true;
            }

            return access;
        },
    
    });

    return Controller;
});