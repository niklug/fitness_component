define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/items',
        'collections/nutrition_plan/supplements/protocols',
        'collections/nutrition_plan/nutrition_guide/menu_plans',
        'collections/nutrition_plan/nutrition_guide/nutrition_database_categories',
        'collections/nutrition_plan/nutrition_guide/shopping_list_ingredients',
	'models/nutrition_plan/item',
        'models/nutrition_plan/item_overview',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/request_params',
        'models/nutrition_plan/supplements/protocol',
        'views/nutrition_plan/backend/macronutrients',
        'views/nutrition_plan/supplements/backend/protocols',
        'views/nutrition_plan/backend/information',
        'views/nutrition_plan/nutrition_guide/menu_plan_list_menu',
        'views/nutrition_plan/nutrition_guide/backend/menu_plan_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_header',
        'views/nutrition_plan/nutrition_guide/example_day_menu',
        'views/nutrition_plan/nutrition_guide/example_day',
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/nutrition_guide/shopping_list',
        'views/nutrition_plan/supplements/backend/protocol',
        'views/nutrition_plan/backend/list',
        'views/nutrition_plan/backend/search_header',
        'views/nutrition_plan/backend/overview_container',
        'views/nutrition_plan/backend/menus/main_menu',
        'views/nutrition_plan/backend/targets/targets_container',
        'views/graph/graph',

], function (
        $,
        _,
        Backbone,
        app, 
        Items_collection,
        Protocols_collection,
        Menu_plans_collection,
        Nutrition_database_categories_collection,
        Shopping_list_ingredients_collection,
        Item_model,
        Item_overview_model,
        Target_model,
        Menu_plan_model,
        Request_params_model,
        Protocol_model,
        Macronutrients_view,
        Protocols_view,
        Information_view,
        Menu_plan_list_menu_view,
        Menu_plan_list_view,
        Menu_plan_header_view,
        Example_day_menu_view,
        Example_day_view,
        Example_day_add_recipe_view,
        Shopping_list_view,
        Protocol_view,
        List_view,
        Search_header_view,
        Overview_container_view,
        Main_menu_view,
        Targets_container_view,
        Graph_view
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
                
                app.models.request_params.bind("change", this.get_items, this);
                
                this.onClientChange();
 
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
                "!/example_day/:id/:menu_id/:nutrition_plan_id": "example_day", 
                "!/add_example_day_meal/:id/:nutrition_plan_id": "add_example_day_meal", 
                "!/shopping_list/:id/:menu_id": "shopping_list", 
                "!/information/:id": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
            },
            
            onClientChange : function() {
                var self = this;
                $("#graph_client_id").die().live('change', function() {
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
                this.common_actions();
                //show all
                app.models.request_params.set({page : 1,  state : '*', uid : app.getUniqueId()});

                this.list_actions();
            },

            list_actions : function () {
                this.connectGraph();
                
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
                this.common_actions();
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

               
            add_meal_recipe : function(meal_id, nutrition_plan_id) {
                this.get_database_recipes();

                $('#example_day_wrapper').html(new Example_day_add_recipe_view({nutrition_plan_id : nutrition_plan_id, collection : app.collections.add_meal_recipes}).render().el);
                
                app.models.pagination = $.backbone_pagination({});
                
                app.models.pagination.bind("change:currentPage", this.set_recipes_model, this);
                app.models.pagination.bind("change:items_number", this.set_recipes_model, this);
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
                            //console.log(model.toJSON());
                            self.loadTragers();
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
                    self.loadTragers();
                });
            },
            
            loadTragers : function() {
                $("#targets_link").addClass("active_link");
                
                $("#main_container").html(new Targets_container_view({model : app.models.target, item_model : app.models.item}).render().el);
            },
            
            common_actions : function() {
                $("#header_wrapper, #nutrition_guide_header, #graph_container").empty();
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link");
            },
            
            
            connectGraph : function() {
                this.graph = new Graph_view({
                    el : "#graph_container",
                    model : app.models.request_params,
                    show : {
                        primary_goals : true,
                        mini_goals : true,
                        personal_training : false,
                        semi_private : false,
                        resistance_workout : false,
                        cardio_workout : false,
                        assessment : false,
                        current_time : true,
                        client_select : true,
                        choices : true
                    },
                    style : '',
                    reloads : false,
                    list_type : ''
                });
            },
        });

    return Controller;
});