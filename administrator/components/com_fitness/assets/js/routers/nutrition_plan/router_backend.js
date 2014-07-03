define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/items',
        'collections/nutrition_plan/targets',
        'collections/nutrition_plan/supplements/protocols',
        'collections/nutrition_plan/nutrition_guide/menu_plans',
        'collections/nutrition_plan/nutrition_guide/example_day_meals',
        'collections/recipe_database/recipes',
        'collections/nutrition_plan/nutrition_guide/nutrition_database_categories',
        'collections/nutrition_plan/nutrition_guide/shopping_list_ingredients',
	'models/nutrition_plan/item',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/nutrition_guide/example_day_meal',
        'models/nutrition_plan/request_params',
        'views/nutrition_plan/overview',
        'views/nutrition_plan/target_block',
        'views/nutrition_plan/macronutrients',
        'views/nutrition_plan/supplements/frontend/protocols',
        'views/nutrition_plan/information',
        'views/nutrition_plan/archive_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_list_menu',
        'views/nutrition_plan/nutrition_guide/frontend/menu_plan_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_header',
        'views/nutrition_plan/nutrition_guide/example_day_menu',
        'views/nutrition_plan/nutrition_guide/example_day',
        'views/nutrition_plan/nutrition_guide/example_day_meal',
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/nutrition_guide/shopping_list',
        'views/graph/graph',
        'views/nutrition_plan/backend/list',
        'views/nutrition_plan/backend/search_header',
        'views/nutrition_plan/backend/overview_container'

], function (
        $,
        _,
        Backbone,
        app, 
        Items_collection,
        Targets_collection,
        Protocols_collection,
        Menu_plans_collection,
        Example_day_meals_collection,
        Add_meal_recipes_collection,
        Nutrition_database_categories_collection,
        Shopping_list_ingredients_collection,
        Item_model,
        Target_model,
        Menu_plan_model,
        Example_day_meal_model,
        Request_params_model,
        Overview_view,
        Target_block_view,
        Macronutrients_view,
        Protocols_view,
        Information_view,
        Archive_list_view,
        Menu_plan_list_menu_view,
        Menu_plan_list_view,
        Menu_plan_header_view,
        Example_day_menu_view,
        Example_day_view,
        Example_day_meal_view,
        Example_day_add_recipe_view,
        Shopping_list_view,
        Graph_view,
        List_view,
        Search_header_view,
        Overview_container_view
    ) {


    var Controller = Backbone.Router.extend({
        
            initialize: function(){
                //unique id
                app.getUniqueId = function() {
                    return new Date().getUTCMilliseconds();
                }

                app.models.item = new Item_model({});
                
                app.collections.items = new Items_collection();
                
                app.collections.targets = new Targets_collection({'id' : app.options.item_id});
                
                //business logic
                var business_profile_id = null;
                if(!app.options.is_superuser) {
                    business_profile_id = app.options.business_profile_id;
                }
                app.options.client_id = localStorage.getItem('client_id');
                //
                app.models.request_params = new Request_params_model({client_id : app.options.client_id, business_profile_id : business_profile_id});
                
                app.collections.add_meal_recipes = new Add_meal_recipes_collection(); 
                
                app.models.request_params.bind("change", this.get_items, this);
                
                this.onClientChange();

            },
        
            routes: {
                "": "list_view", 
                "!/": "list_view", 
                "!/list_view": "list_view", 
                "!/overview/:id": "overview", 
                "!/targets": "targets", 
                "!/macronutrients": "macronutrients", 
                "!/supplements": "supplements", 
                "!/nutrition_guide": "nutrition_guide", 
                "!/menu_plan/:id": "menu_plan", 
                "!/example_day/:id": "example_day", 
                "!/add_example_day_meal/:id": "add_example_day_meal", 
                "!/shopping_list": "shopping_list", 
                "!/add_meal_recipe/:meal_id": "add_meal_recipe",
                "!/information": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
            },
            
            onClientChange : function() {
                var self = this;
                $("#client_id").die().live('change', function() {
                    var client_id = $(this).val();
                    app.options.client_id = client_id;
                    localStorage.setItem('client_id', client_id);
                    app.models.request_params.set({client_id : client_id});
                    self.navigate("!/list_view", true);
                });
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

            list_view : function() {
                //show all
                app.models.request_params.set({page : 1,  state : '*', uid : app.getUniqueId()});

                this.list_actions();
            },

            list_actions : function () {
                $("#header_wrapper").html(new Search_header_view({model : app.models.request_params, collection : app.collections.items}).render().el);

                $("#main_container").html(new List_view({model : app.models.request_params, collection : app.collections.items}).render().el);

                app.models.pagination = $.backbone_pagination({});

                app.models.pagination.bind("change:currentPage", this.set_params_model, this);

                app.models.pagination.bind("change:items_number", this.set_params_model, this);
            },

            set_params_model : function() {
                app.collections.items.reset();
                app.models.request_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10, uid : app.getUniqueId()});
            },
            
            overview : function(id) {
                $("#header_wrapper").empty();
                
                $(".plan_menu_link").removeClass("active_link");
                
                var model = app.collections.items.get(id);
                
                if(model) {
                    this.load_overview(model);
                    return;
                }
                model = new Item_model({id : id});
                var self = this;
                model.fetch({
                    success: function (model, response) {
                        app.collections.items.add(model);
                        self.load_overview(model);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            
 
                
            },
            
            load_overview : function(model) {
                $("#main_container").html(new Overview_container_view({model : model}).render().el);
            },

        
        
        
        
        
            ///
            get_database_recipes : function() {
                app.collections.add_meal_recipes.reset();
                //console.log(app.models.get_recipe_params.toJSON());
                app.collections.add_meal_recipes.fetch({
                    data : app.models.get_recipe_params.toJSON(),
                    success : function(collection, response) {
                        //console.log(collection.models.length);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });  
            },

            overview_old: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#overview_wrapper").show();
                 $("#overview_link").addClass("active_link");
                 // connect Graph from Goals frontend logic
                 this.connectGraph();
                 var id = app.models.nutrition_plan.get('id');
                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var overview_view = new Overview_view({model : model});
                        $("#nutrition_focus_wrapper").html(overview_view.render().el);
                    },
                    error: function (collection, response) {
                        //alert(response.responseText);
                    }
                 });
                 
            },
            
            connectGraph : function() {
                this.graph = new Graph_view({
                    el : "#graph_container",
                    model : '',
                    show : {
                        primary_goals : true,
                        mini_goals : true,
                        personal_training : false,
                        semi_private : false,
                        resistance_workout : false,
                        cardio_workout : false,
                        assessment : false,
                        current_time : true,
                        client_select : false,
                        choices : false
                    },
                    style : 'dark',
                    reloads : true,
                    list_type : false,
                    head_title : 'MY GOALS & TRAINING PERIODS'
                });
            },
        
            targets: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#targets_wrapper").show();
                 $("#targets_link").addClass("active_link");
                 var id = app.models.nutrition_plan.get('id');
                 
                 app.collections.targets.fetch({
                    data: {id : id, client_id : app.options.client_id},
                    wait : true,
                    success : function(collection, response) {
                        $("#targets_container").empty();
                        _.each(collection.models, function(model) {
                            var target_block_view = new Target_block_view({model : model});
                            $("#targets_container").append(target_block_view.render().el);
                        });
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });

                 // connect comments
                 var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : app.options.fitness_frontend_url,
                    'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' :  '#__fitness_nutrition_plan_targets_comments',
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'TargetsComment'
                }
                var comments =  $.comments(comment_options, comment_options.item_id, 0);

                var comments_html = comments.run();
                $("#targets_comments_wrapper").html(comments_html);

            },
            
            macronutrients: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#macronutrients_wrapper").show();
                 $("#macronutrients_link").addClass("active_link");
               
                 var id = app.models.nutrition_plan.get('id');
                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var macronutrients_view = new Macronutrients_view({model : model});
                        
                        $("#macronutrients_container").html(macronutrients_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 // connect comments
                 var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : app.options.fitness_frontend_url,
                    'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'MacrosComment'
                }
                var comments = $.comments(comment_options, comment_options.item_id, 1);

                var comments_html = comments.run();
                $("#macronutrients_comments_wrapper").html(comments_html);
            },
            
            supplements: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#supplements_wrapper").show();
                 $("#supplements_link").addClass("active_link");

                 app.collections.protocols = new Protocols_collection(); 
                 var id = app.models.nutrition_plan.get('id');
                 app.collections.protocols.fetch({
                    data: {nutrition_plan_id : id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 app.views.protocols = new Protocols_view({model : app.models.nutrition_plan, collection : app.collections.protocols}); 
                 $("#supplements_wrapper").html(app.views.protocols.render().el);
            },
            
            nutrition_guide: function () {
                this.no_active_plan_action();
                this.common_actions();
                $("#nutrition_guide_wrapper").show();
                $("#nutrition_guide_link").addClass("active_link");
                
                app.collections.menu_plans = new Menu_plans_collection(); 
                var id = app.models.nutrition_plan.get('id');
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : id},
                    
                    success: function (collection, response) {
                        app.views.menu_plan_list = new Menu_plan_list_view({collection : collection});
                        $("#nutrition_guide_container").html(app.views.menu_plan_list.render().el);
                    },
                    
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 app.views.menu_plan_list_menu = new Menu_plan_list_menu_view();
                 
                 $("#nutrition_guide_header").html(app.views.menu_plan_list_menu.render().el);
            },
            
            menu_plan: function (id) {
                app.models.menu_plan = new Menu_plan_model();
                
                if(parseInt(id)) {
                    app.models.menu_plan = app.collections.menu_plans.get(id);
                }
                   
                app.views.menu_plan_header = new Menu_plan_header_view({model : app.models.menu_plan, collection : app.collections.menu_plans});
                 
                $("#nutrition_guide_header").html(app.views.menu_plan_header.render().el);
                
                
                
                $( "#start_date" ).datepicker({ dateFormat: "yy-mm-dd",  minDate : -5});
                
                if(parseInt(id)) {
                    app.views.example_day_menu = new Example_day_menu_view({model : app.models.menu_plan});
                    $("#nutrition_guide_container").html(app.views.example_day_menu.render().el);
                }
                //on default
                this.example_day(1);
                $(".example_day_link").first().addClass("active");

            },
            
            example_day : function(example_day_id) {
                app.collections.example_day_meals = new Example_day_meals_collection(); 
                var id = app.models.nutrition_plan.get('id');
                var menu_id = app.models.menu_plan.get('id');
                app.collections.example_day_meals.fetch({
                    data: {
                        nutrition_plan_id : id,
                        menu_id : menu_id,
                        example_day_id : example_day_id
                    },
                    success: function (collection, response) {
                        //console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
                
                $('#example_day_wrapper').html(new Example_day_view({collection : app.collections.example_day_meals, 'example_day_id' : example_day_id}).render().el);
            },
            
            add_example_day_meal : function(example_day_id) {
                var menu_id = app.models.menu_plan.get('id');
                app.views.example_day_meal = new Example_day_meal_view({model : new Example_day_meal_model({'example_day_id' : example_day_id, 'menu_id' : menu_id}), collection : app.collections.example_day_meals}); 
                $("#example_day_meal_list").append(app.views.example_day_meal.render().el );
            },
            
            shopping_list : function() {
                var menu_id = app.models.menu_plan.get('id');
                app.collections.nutrition_database_categories = new Nutrition_database_categories_collection();
                app.collections.shopping_list_ingredients = new Shopping_list_ingredients_collection();
                var id = app.models.nutrition_plan.get('id');
                
                $.when(
                
                app.collections.nutrition_database_categories.fetch({
                    wait : true,
                    success: function (collection, response) {
                        //console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
                
                ,
                
                app.collections.shopping_list_ingredients.fetch({
                    wait : true,
                    data: {
                        nutrition_plan_id : id,
                        menu_id : menu_id
                    },
                    success: function (collection, response) {
                        //console.log(response);
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                })
                
                ).then(function() {
                    $('#example_day_wrapper').html(new Shopping_list_view({
                        categories_collection : app.collections.nutrition_database_categories, 
                        ingredients_collection : app.collections.shopping_list_ingredients,
                        model : app.models.menu_plan
                    }).render().el);
                });
            },
            
            add_meal_recipe : function(meal_id) {
                this.get_database_recipes();
                
                var meal_model = app.collections.example_day_meals.get({id : meal_id});

                $('#example_day_wrapper').html(new Example_day_add_recipe_view({collection : app.collections.add_meal_recipes, model : meal_model}).render().el);
                
                app.models.pagination = $.backbone_pagination({});
                
                app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);
                app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            },
            
            set_recipes_model : function() {
                app.collections.add_meal_recipes.reset();
                app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
            },
     
            information: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#information_wrapper").show();
                 $("#information_link").addClass("active_link");
                 var id = app.models.nutrition_plan.get('id');
                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var information_view = new Information_view({model : model});
                        
                        $("#information_wrapper").html(information_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            archive: function () {
                 this.common_actions();
                 $("#archive_wrapper").show();
                 $("#archive_focus_link").addClass("active_link");

                 app.collections.nutrition_plans.fetch({
                    data: {id : app.options.item_id, client_id : app.options.client_id},
                    wait : true,
                    success : function(collection, response) {
                        var archive_list_view = new Archive_list_view({model : app.models.nutrition_plan, collection : collection});
                        
                        $("#archive_wrapper").html(archive_list_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            close: function() {
                 this.no_active_plan_action();
                 $("#close_tab").hide();
                 app.models.nutrition_plan.set({id : app.options.item_id});
                 this.overview();
            },
            
            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
            },
            
            no_active_plan_action : function() {
                if(!app.options.item_id) {
                    alert('Please contact your trainer immediately regarding your current Nutrition Plan!');
                    return false;
                }
           },
           
           
           copy_menu_plan : function(id) {
                app.models.menu_plan = new Menu_plan_model();
                var self = this;
                app.models.menu_plan.set({id : id});
                app.models.menu_plan.fetch({
                    wait : true,
                    success: function (model, response) {
                        model.set({
                            id : null, 
                            created_by : app.options.user_id,
                            submit_date : null,
                            status : '4',
                            assessed_by : null,
                        });
                        model.save(null, {
                            success: function (model, response) {
                                self.nutrition_guide();
                            },
                            error: function (model, response) {
                                alert(response.responseText);
                            }
                        });
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
             },
             
            update_list : function() {
                app.models.request_params.set({ uid : app.getUniqueId()});
            },

        });

    return Controller;
});