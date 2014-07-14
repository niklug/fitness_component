define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/items',
        'collections/nutrition_plan/supplements/protocols',
        'collections/nutrition_plan/nutrition_guide/menu_plans',
        'collections/nutrition_plan/nutrition_guide/example_day_meals',
        'collections/recipe_database/recipes',
        'collections/nutrition_plan/nutrition_guide/nutrition_database_categories',
        'collections/nutrition_plan/nutrition_guide/shopping_list_ingredients',
	'models/nutrition_plan/item',
        'models/nutrition_plan/item_overview',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/nutrition_guide/example_day_meal',
        'models/nutrition_plan/request_params',
        'models/nutrition_plan/supplements/protocol',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/nutrition_plan/overview',
        'views/nutrition_plan/backend/macronutrients',
        'views/nutrition_plan/supplements/backend/protocols',
        'views/nutrition_plan/backend/information',
        'views/nutrition_plan/nutrition_guide/menu_plan_list_menu',
        'views/nutrition_plan/nutrition_guide/backend/menu_plan_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_header',
        'views/nutrition_plan/nutrition_guide/example_day_menu',
        'views/nutrition_plan/nutrition_guide/example_day',
        'views/nutrition_plan/nutrition_guide/example_day_meal',
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/nutrition_guide/shopping_list',
        'views/nutrition_plan/supplements/backend/protocols_wrapper',
        'views/nutrition_plan/supplements/backend/protocol',
        'views/graph/graph',
        'views/nutrition_plan/backend/list',
        'views/nutrition_plan/backend/search_header',
        'views/nutrition_plan/backend/overview_container',
        'views/nutrition_plan/backend/menus/main_menu',
        'views/nutrition_plan/backend/targets/targets_container'

], function (
        $,
        _,
        Backbone,
        app, 
        Items_collection,
        Protocols_collection,
        Menu_plans_collection,
        Example_day_meals_collection,
        Add_meal_recipes_collection,
        Nutrition_database_categories_collection,
        Shopping_list_ingredients_collection,
        Item_model,
        Item_overview_model,
        Target_model,
        Menu_plan_model,
        Example_day_meal_model,
        Request_params_model,
        Protocol_model,
        Get_recipe_params_model,
        Overview_view,
        Macronutrients_view,
        Protocols_view,
        Information_view,
        Menu_plan_list_menu_view,
        Menu_plan_list_view,
        Menu_plan_header_view,
        Example_day_menu_view,
        Example_day_view,
        Example_day_meal_view,
        Example_day_add_recipe_view,
        Shopping_list_view,
        Protocols_wrapper_view,
        Protocol_view,
        Graph_view,
        List_view,
        Search_header_view,
        Overview_container_view,
        Main_menu_view,
        Targets_container_view
    ) {


    var Controller = Backbone.Router.extend({
        
            initialize: function(){
                //unique id
                app.getUniqueId = function() {
                    return new Date().getUTCMilliseconds();
                }

                app.models.item = new Item_model({});
                
                app.collections.items = new Items_collection();
                
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
                
                //
                app.models.get_recipe_params = new Get_recipe_params_model();
                
                app.collections.add_meal_recipes = new Add_meal_recipes_collection(); 
                
                app.models.get_recipe_params.bind("change", this.get_database_recipes, this);

            },
        
            routes: {
                "": "list_view", 
                "!/": "list_view", 
                "!/list_view": "list_view", 
                "!/overview/:id": "overview", 
                "!/targets/:id": "targets", 
                "!/macronutrients/:id": "macronutrients", 
                "!/supplements/:id": "supplements", 
                "!/add_supplement_protocol/:id": "add_supplement_protocol",
                "!/nutrition_guide/:id": "nutrition_guide", 
                "!/menu_plan/:id/:nutrition_plan_id": "menu_plan", 
                "!/example_day/:id/:nutrition_plan_id": "example_day", 
                "!/add_example_day_meal/:id/:nutrition_plan_id": "add_example_day_meal", 
                "!/shopping_list/:id": "shopping_list", 
                "!/add_meal_recipe/:meal_id/:nutrition_plan_id": "add_meal_recipe",
                "!/information/:id": "information", 
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
                        //console.log(collection.toJSON());
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
                $(".plan_menu_link").removeClass("active_link");
                
                var model = app.collections.items.get(id);
                
                if(model) {
                    this.load_overview(model);
                    return;
                }
                model = new Item_overview_model({id : id});
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
                this.loadMainMenu(model.get('id'));
                $("#main_container").html(new Overview_container_view({model : model}).render().el);
            },
            
            loadMainMenu : function(id) {
                $("#header_wrapper").html(new Main_menu_view({nutrition_plan_id : id}).render().el);
            },

            update_list : function() {
                app.models.request_params.set({ uid : app.getUniqueId()});
            },
        
            supplements: function (id) {
                $("#header_wrapper").empty();
                this.common_actions();
                this.loadMainMenu(id);
                $("#supplements_link").addClass("active_link");

                app.collections.protocols = new Protocols_collection();
                var self = this;
                app.collections.protocols.fetch({
                    data: {nutrition_plan_id : id},
                    success: function(collection) {
                        $("#main_container").html(new Protocols_view({collection : collection, nutrition_plan_id : id}).render().el);
                    },
                    error: function(collection, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            add_supplement_protocol : function(id) {
                app.views.protocol = new Protocol_view({model : new Protocol_model({nutrition_plan_id : id}), collection : app.collections.protocols}); 
                   $("#protocol_list").append(app.views.protocol.render().el );
            },
            
            nutrition_guide: function (id) {
                this.common_actions();
                this.loadMainMenu(id);
                $("#nutrition_guide_link").addClass("active_link");
                
                app.collections.menu_plans = new Menu_plans_collection(); 
                app.collections.menu_plans.fetch({
                    data: {nutrition_plan_id : id},
                    success: function (collection, response) {
                        app.views.menu_plan_list = new Menu_plan_list_view({collection : collection, nutrition_plan_id : id});
                        $("#main_container").html(app.views.menu_plan_list.render().el);
                    },
                    
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 app.views.menu_plan_list_menu = new Menu_plan_list_menu_view({nutrition_plan_id : id});
                 
                 $("#nutrition_guide_header").html(app.views.menu_plan_list_menu.render().el);
            },
            
            menu_plan: function (id, nutrition_plan_id) {
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
                //on default
                this.example_day(1, nutrition_plan_id);
                $(".example_day_link").first().addClass("active");

            },
            
            example_day : function(example_day_id, nutrition_plan_id) {
                app.collections.example_day_meals = new Example_day_meals_collection(); 
                var menu_id = app.models.menu_plan.get('id');
                 app.collections.example_day_meals.fetch({
                    data: {
                        nutrition_plan_id : nutrition_plan_id,
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
                $('#example_day_wrapper').html(new Example_day_view({collection : app.collections.example_day_meals, 'example_day_id' : example_day_id, nutrition_plan_id : nutrition_plan_id}).render().el);
            },
            
            
            add_example_day_meal : function(example_day_id, nutrition_plan_id) {
                var menu_id = app.models.menu_plan.get('id');
                app.views.example_day_meal = new Example_day_meal_view({nutrition_plan_id : nutrition_plan_id, model : new Example_day_meal_model({'example_day_id' : example_day_id, 'menu_id' : menu_id, nutrition_plan_id : nutrition_plan_id}), collection : app.collections.example_day_meals}); 
                $("#example_day_meal_list").append(app.views.example_day_meal.render().el );
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
             
             macronutrients : function (id) {
                this.common_actions();

                var model = app.collections.items.get(id);
                
                if(model) {
                    this.load_macronutrients(model);
                    return;
                }
                model = new Item_model({id : id});
                var self = this;
                model.fetch({
                    success: function (model, response) {
                        app.collections.items.add(model);
                        self.load_macronutrients(model);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },

            load_macronutrients : function(model) {
                this.loadMainMenu(model.get('id'));
                
                $("#macronutrients_link").addClass("active_link");
                
                var macronutrients_view = new Macronutrients_view({model : model});
                     
                $("#main_container").html(macronutrients_view.render().el);
            },
            
            information: function (id) {
                this.common_actions();
                var model = app.collections.items.get(id);
                
                if(model) {
                    this.load_information(model);
                    return;
                }
                model = new Item_model({id : id});
                var self = this;
                model.fetch({
                    success: function (model, response) {
                        app.collections.items.add(model);
                        self.load_information(model);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                });
            },
            
            load_information: function(model) {
                this.loadMainMenu(model.get('id'));
                
                $("#information_link").addClass("active_link");
                
                var information_view = new Information_view({model : model});
                        
                $("#main_container").html(information_view.render().el);
            },

            shopping_list : function(id) {
                var menu_id = app.models.menu_plan.get('id');
                app.collections.nutrition_database_categories = new Nutrition_database_categories_collection();
                app.collections.shopping_list_ingredients = new Shopping_list_ingredients_collection();
                
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
            
            targets: function (id) {
                this.common_actions();
                
                this.loadMainMenu(id);
                
                var self = this;
                
                app.models.target = new Target_model({nutrition_plan_id : id});
     
                app.models.item = app.collections.items.get(id);
                
                if(app.models.item) {
                    app.models.target.fetch({
                        data : {nutrition_plan_id : id},
                        success : function (model, response) {
                            self.loadTragers(app.models.item);
                        },
                        error : function (collection, response) {
                            alert(response.responseText);
                        }
                    })

                    return;
                }
                
                app.models.item = new Item_model({id : id});
  
                $.when(
                
                    app.models.item.fetch({
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                
                ,
                
                    app.models.target.fetch({
                        data : {nutrition_plan_id : id},
                        error: function (collection, response) {
                            alert(response.responseText);
                        }
                    })
                
                ).then(function() {
                    app.collections.items.add(app.models.item);
                    self.loadTragers(app.models.target);
                });
            },
            
            loadTragers : function(model) {
                $("#targets_link").addClass("active_link");
                
                $("#main_container").html(new Targets_container_view({model : app.models.target, item_model : app.models.item}).render().el);
            },
            
            common_actions : function() {
                $("#header_wrapper, #nutrition_guide_header").empty();
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
            },
            
            
        
        


        });

    return Controller;
});