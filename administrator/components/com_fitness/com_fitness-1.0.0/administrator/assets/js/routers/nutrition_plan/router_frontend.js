define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_plans',
        'collections/nutrition_plan/supplements/protocols',
        'collections/nutrition_plan/nutrition_guide/menu_plans',
        'collections/recipe_database/recipes',
        'collections/nutrition_plan/nutrition_guide/nutrition_database_categories',
        'collections/nutrition_plan/nutrition_guide/shopping_list_ingredients',
	'models/nutrition_plan/nutrition_plan',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
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
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/nutrition_guide/shopping_list',
        'views/graph/graph',
        'views/nutrition_plan/main_menu',
        'views/comments/index'

], function (
        $,
        _,
        Backbone,
        app, 
        Nutrition_plans_collection,
        Protocols_collection,
        Menu_plans_collection,
        Add_meal_recipes_collection,
        Nutrition_database_categories_collection,
        Shopping_list_ingredients_collection,
        Nutrition_plan_model,
        Target_model,
        Menu_plan_model,
        Get_recipe_params_model,
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
        Example_day_add_recipe_view,
        Shopping_list_view,
        Graph_view,
        Main_menu_view,
        Comments_view
    ) {


    var Controller = Backbone.Router.extend({
        
            initialize: function(){

                app.models.nutrition_plan = new Nutrition_plan_model({'id' : app.options.item_id});
                
                app.collections.nutrition_plans = new Nutrition_plans_collection();
                //
                app.models.get_recipe_params = new Get_recipe_params_model();
                
                app.collections.add_meal_recipes = new Add_meal_recipes_collection(); 
                
                app.models.get_recipe_params.bind("change", this.get_database_recipes, this);

            },
        
            routes: {
                "": "overview", 
                "!/": "overview", 
                "!/overview/:id": "overview", 
                "!/targets/:id": "targets", 
                "!/macronutrients/:id": "macronutrients", 
                "!/supplements/:id": "supplements", 
                "!/nutrition_guide/:id": "nutrition_guide", 
                "!/menu_plan/:id/:nutrition_plan_id": "menu_plan", 
                "!/example_day/:id/:menu_id/:nutrition_plan_id": "example_day", 
                "!/add_example_day_meal/:id/:nutrition_plan_id": "add_example_day_meal", 
                "!/shopping_list/:id/:menu_id": "shopping_list", 
                "!/add_meal_recipe/:meal_id/:nutrition_plan_id": "add_meal_recipe",
                "!/information/:id": "information", 
                "!/archive/:id": "archive", 
                "!/close": "close", 
            },
            
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

            overview: function (id) {
                if(!id) {
                    id = app.options.item_id;
                }
                this.common_actions(id);
                if(!this.no_active_plan_action(id)) return;
                

                $("#overview_link").addClass("active_link");
                // connect Graph from Goals frontend logic
                this.connectGraph();
                app.models.nutrition_plan.fetch({
                    data: {id: id},
                    wait: true,
                    success: function(model, response) {
                        var overview_view = new Overview_view({model: model});
                        $("#main_container").html(overview_view.render().el);
                    },
                    error: function(collection, response) {
                        //alert(response.responseText);
                    }
                });
                 
            },
            
            loadMainMenu : function(id) {
                $("#header_wrapper").html(new Main_menu_view({nutrition_plan_id : id}).render().el);
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
        
            targets: function (id) {
                this.common_actions(id);
                if(!this.no_active_plan_action(id)) return;
                
                $("#targets_link").addClass("active_link");
                
                app.models.target = new Target_model({nutrition_plan_id : id});
                var self = this;
                app.models.target.fetch({
                    data : {nutrition_plan_id : id},
                    success : function (model, response) {
                        self.loadTargets(id);
                    },
                    error : function (collection, response) {
                        alert(response.responseText);
                    }
                })
            },
            
            loadTargets : function(id) {
                $("#main_container").html(new Target_block_view({model : app.models.target, item_model : app.models.nutrition_plan}).render().el);
                
                // connect comments
                var comment_options = {
                    'item_id' :  id,
                    'item_model' :  app.models.target,
                    'sub_item_id' :  '0',
                    'db_table' : 'fitness_nutrition_plan_targets_comments',
                    'read_only' : true,
                    'anable_comment_email' : true,
                    'comment_method' : 'TargetsComment'
                }
                var comments_html = new Comments_view(comment_options).render().el;
                $("#targets_comments_wrapper").html(comments_html);
            },
            
            macronutrients: function (id) {
                this.common_actions(id);
                if(!this.no_active_plan_action(id)) return;
                 
                 $("#macronutrients_link").addClass("active_link");

                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var macronutrients_view = new Macronutrients_view({model : model});
                        
                        $("#main_container").html(macronutrients_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
            },
            
            supplements: function (id) {
                this.common_actions(id);
                 if(!this.no_active_plan_action(id)) return;
                 
                 $("#supplements_link").addClass("active_link");

                 app.collections.protocols = new Protocols_collection(); 
  
                 app.collections.protocols.fetch({
                    data: {nutrition_plan_id : id},
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 app.views.protocols = new Protocols_view({model : app.models.nutrition_plan, collection : app.collections.protocols}); 
                 $("#main_container").html(app.views.protocols.render().el);
            },
            
            nutrition_guide: function (id) {
                this.common_actions(id);
                if(!this.no_active_plan_action(id)) return;
                
                $("#nutrition_guide_link").addClass("active_link");
                
                if (app.collections.menu_plans
                        && app.collections.menu_plans.models[0]
                        && app.collections.menu_plans.models[0].get('nutrition_plan_id') == id
                    ) {
                    this.load_nutrition_guide(id);
                    return;
                }
                
                var self = this;
                app.collections.menu_plans = new Menu_plans_collection(); 
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : id},
                    success: function (collection, response) {
                        self.load_nutrition_guide(id);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            load_nutrition_guide : function(id) {
                app.views.menu_plan_list_menu = new Menu_plan_list_menu_view({nutrition_plan_id : id});
                $("#nutrition_guide_header").html(app.views.menu_plan_list_menu.render().el);
                app.views.menu_plan_list = new Menu_plan_list_view({collection : app.collections.menu_plans, nutrition_plan_id : id});
                $("#main_container").html(app.views.menu_plan_list.render().el);
            },
            
            menu_plan: function (id, nutrition_plan_id) {
                if (app.collections.menu_plans
                        && app.collections.menu_plans.models[0]
                        && app.collections.menu_plans.models[0].get('nutrition_plan_id') == nutrition_plan_id
                    ) {
                    this.load_menu_plan(id, nutrition_plan_id);
                    return;
                }
                
                var self = this;
                app.collections.menu_plans = new Menu_plans_collection(); 
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : nutrition_plan_id},
                    success: function (collection, response) {
                        self.load_menu_plan(id, nutrition_plan_id);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
            
            load_menu_plan: function (id, nutrition_plan_id) {
                this.loadMainMenu(nutrition_plan_id);
                $("#nutrition_guide_link").addClass("active_link");
                $("#main_container").empty();
                if(parseInt(id)) {
                    this.example_day(1, id, nutrition_plan_id);
                    $(".example_day_link").first().addClass("active");
                } else {
                    this.load_menu_plan_content(id, nutrition_plan_id);
                }
            },
            
            load_menu_plan_content : function (id, nutrition_plan_id) {
                app.models.menu_plan = new Menu_plan_model();
                if(parseInt(id)) {
                    app.models.menu_plan = app.collections.menu_plans.get(id);
                }
                
                app.views.menu_plan_header = new Menu_plan_header_view({model : app.models.menu_plan, collection : app.collections.menu_plans, nutrition_plan_id : nutrition_plan_id});
                 
                $("#nutrition_guide_header").html(app.views.menu_plan_header.render().el);

                $( "#start_date" ).datepicker({ dateFormat: "yy-mm-dd",  minDate : -5});
                
                if(parseInt(id)) {
                    app.views.example_day_menu = new Example_day_menu_view({model : app.models.menu_plan, nutrition_plan_id : nutrition_plan_id});
                    $("#main_container").html(app.views.example_day_menu.render().el);
                }
            },
            
            example_day : function(example_day_id, menu_id, nutrition_plan_id) {
                if (app.collections.menu_plans
                        && app.collections.menu_plans.models[0]
                        && app.collections.menu_plans.models[0].get('nutrition_plan_id') == nutrition_plan_id
                    ) {
                    this.load_example_day(example_day_id, menu_id, nutrition_plan_id);
                    return;
                }
                
                var self = this;
                app.collections.menu_plans = new Menu_plans_collection(); 
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : nutrition_plan_id},
                    success: function (collection, response) {
                        self.load_example_day(example_day_id, menu_id, nutrition_plan_id);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            load_example_day : function(example_day_id, menu_id, nutrition_plan_id) {
                this.loadMainMenu(nutrition_plan_id);
                $("#nutrition_guide_link").addClass("active_link");

                this.load_menu_plan_content(menu_id, nutrition_plan_id);
                
                app.views.example_day = new Example_day_view({model : app.models.menu_plan, 'example_day_id' : example_day_id, menu_id : menu_id,  nutrition_plan_id : nutrition_plan_id});
                
                $('#example_day_wrapper').html(app.views.example_day.render().el);
                
                $('.example_day_link[data-id=' + example_day_id + ']').addClass("active");
            },
            
            add_example_day_meal : function(example_day_id, nutrition_plan_id) {
                var menu_id = app.models.menu_plan.get('id');
                app.views.example_day_meal = new Example_day_meal_view({nutrition_plan_id : nutrition_plan_id, model : new Example_day_meal_model({'example_day_id' : example_day_id, 'menu_id' : menu_id}), collection : app.collections.example_day_meals}); 
                $("#example_day_meal_list").append(app.views.example_day_meal.render().el );
            },
            
            shopping_list : function(id, menu_id) {
                this.loadMainMenu(id);
                $("#nutrition_guide_link").addClass("active_link");
                
                app.collections.nutrition_database_categories = new Nutrition_database_categories_collection();
                app.collections.shopping_list_ingredients = new Shopping_list_ingredients_collection();

                app.collections.menu_plans = new Menu_plans_collection(); 

                var self = this;
                
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
                    },
                }),
                
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : id},
     
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
                
                ).then(function() {
                    self.load_menu_plan_content(menu_id, id);
                    
                    $('#example_day_wrapper').html(new Shopping_list_view({
                        categories_collection : app.collections.nutrition_database_categories, 
                        ingredients_collection : app.collections.shopping_list_ingredients,
                        model : app.models.menu_plan
                    }).render().el);
                    
                    $(".shopping_list").addClass("active");
                });
            },
            
            add_meal_recipe : function(meal_id, nutrition_plan_id) {
                this.get_database_recipes();
                
                var meal_model = app.collections.example_day_meals.get({id : meal_id});

                $('#example_day_wrapper').html(new Example_day_add_recipe_view({nutrition_plan_id : nutrition_plan_id, collection : app.collections.add_meal_recipes, model : meal_model}).render().el);
                
                app.models.pagination = $.backbone_pagination({});
                
                app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);
                app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
            },
            
            set_recipes_model : function() {
                app.collections.add_meal_recipes.reset();
                app.models.get_recipe_params.set({"page" : app.models.pagination.get('currentPage') || 1, "limit" : localStorage.getItem('items_number') || 10});
            },
     
            information: function (id) {
                this.common_actions(id);
                 if(!this.no_active_plan_action(id)) return;

                 $("#information_link").addClass("active_link");
                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var information_view = new Information_view({model : model});
                        
                        $("#main_container").html(information_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            archive: function (id) {
                this.common_actions(id);
                 $("#archive_focus_link").addClass("active_link");

                 app.collections.nutrition_plans.fetch({
                    data: {id : app.options.item_id, client_id : app.options.client_id},
                    wait : true,
                    success : function(collection, response) {
                        var archive_list_view = new Archive_list_view({model : app.models.nutrition_plan, collection : collection});
                        
                        $("#main_container").html(archive_list_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
            },
                    
            close: function() {
                 if(!this.no_active_plan_action(app.options.item_id)) return;
                 app.models.nutrition_plan.set({id : app.options.item_id});
                 app.controller.navigate("!/overview/" + app.options.item_id, true);
            },
            
            common_actions : function(id) {
                $("#main_container, #header_wrapper, #nutrition_guide_header, #graph_container").empty();
                this.loadMainMenu(id);
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
                
                $("#close_tab").show();

                if(id == app.options.item_id) {
                    $("#close_tab").hide();
                }
            },
            
            no_active_plan_action : function(id) {
                if(!app.options.item_id && id) {
                    return true;
                }
                if(!app.options.item_id) {
                    alert('Please contact your trainer immediately regarding your current Nutrition Plan!');
                    return false;
                }
                return true;
           },
           
        });

    return Controller;
});