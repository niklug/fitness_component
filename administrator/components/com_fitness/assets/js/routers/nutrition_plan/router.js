define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/nutrition_plan/nutrition_plans',
        'collections/nutrition_plan/targets',
        'collections/nutrition_plan/supplements/protocols',
        'collections/nutrition_plan/nutrition_guide/menu_plans',
        'collections/nutrition_plan/nutrition_guide/example_day_meals',
        'collections/recipe_database/recipes',
        'collections/nutrition_plan/nutrition_guide/nutrition_database_categories',
        'collections/nutrition_plan/nutrition_guide/shopping_list_ingredients',
	'models/nutrition_plan/nutrition_plan',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/nutrition_guide/example_day_meal',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'models/nutrition_plan/supplements/protocol',
        'views/nutrition_plan/overview',
        'views/nutrition_plan/target_block',
        'views/nutrition_plan/macronutrients',
        'views/nutrition_plan/supplements/backend/protocols',
        'views/nutrition_plan/information',
        'views/nutrition_plan/archive_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_list_menu',
        'views/nutrition_plan/nutrition_guide/backend/menu_plan_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_header',
        'views/nutrition_plan/nutrition_guide/example_day_menu',
        'views/nutrition_plan/nutrition_guide/example_day',
        'views/nutrition_plan/nutrition_guide/example_day_meal',
        'views/nutrition_plan/nutrition_guide/add_recipe',
        'views/nutrition_plan/supplements/backend/protocols_wrapper',
        'views/nutrition_plan/supplements/backend/protocol',
        'views/nutrition_plan/nutrition_guide/shopping_list'
], function (
        $,
        _,
        Backbone,
        app, 
        Nutrition_plans_collection,
        Targets_collection,
        Protocols_collection,
        Menu_plans_collection,
        Example_day_meals_collection,
        Add_meal_recipes_collection,
        Nutrition_database_categories_collection,
        Shopping_list_ingredients_collection,
        Nutrition_plan_model,
        Target_model,
        Menu_plan_model,
        Example_day_meal_model,
        Get_recipe_params_model,
        Protocol_model,
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
        Protocols_wrapper_view,
        Protocol_view,
        Shopping_list_view
    ) {

    var Controller = Backbone.Router.extend({
        
            initialize: function(){
                app.models.nutrition_plan = new Nutrition_plan_model({'id' : app.options.item_id});
                
                app.collections.nutrition_plans = new Nutrition_plans_collection();
                
                app.collections.targets = new Targets_collection({'id' : app.options.item_id});
                //
                app.models.get_recipe_params = new Get_recipe_params_model();
                
                app.collections.add_meal_recipes = new Add_meal_recipes_collection(); 
                
                app.models.get_recipe_params.bind("change", this.get_database_recipes, this);
                
                // append targets fieldsets
                app.macronutrient_targets_heavy = $.macronutrientTargets(app.options, 'heavy', 'HEAVY TRAINING DAY');

                app.macronutrient_targets_light = $.macronutrientTargets(app.options, 'light', 'LIGHT TRAINING DAY');

                app.macronutrient_targets_rest = $.macronutrientTargets(app.options, 'rest', 'RECOVERY / REST DAY');
                
                app.macronutrient_targets_heavy.run();

                app.macronutrient_targets_light.run();

                app.macronutrient_targets_rest.run();
                //
                app.nutrition_plan = $.nutritionPlan(app.options);

            },
        
            routes: {
                "": "overview", 
                "!/": "overview", 
                "!/overview": "overview", 
                "!/targets": "targets", 
                "!/macronutrients": "macronutrients", 
                "!/supplements": "supplements", 
                "!/add_supplement_protocol": "add_supplement_protocol",
                "!/nutrition_guide": "nutrition_guide", 
                "!/menu_plan/:id": "menu_plan", 
                "!/example_day/:id": "example_day", 
                "!/add_example_day_meal/:id": "add_example_day_meal", 
                "!/shopping_list": "shopping_list", 
                "!/add_meal_recipe/:meal_id": "add_meal_recipe",
                "!/information": "information", 
                "!/close": "close", 
            },
            
            get_database_recipes : function() {
                app.collections.add_meal_recipes.reset();
                
                var params = app.models.get_recipe_params.toJSON();
                
                //console.log(app.nutrition_plan);
                
                params.client_id = app.nutrition_plan.options.client_selected;
                
                app.collections.add_meal_recipes.fetch({
                    data : params,
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });  
            },

            overview: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#overview_wrapper").show();
                 $("#overview_link").addClass("active_link");
                 app.nutrition_plan.run();
            },

            targets: function () {
                 this.no_active_plan_action();
                 this.common_actions();
                 $("#targets_wrapper").show();
                 $("#targets_link").addClass("active_link");
                 var id = app.models.nutrition_plan.get('id');

                // connect comments
                var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : app.options.fitness_frontend_url,
                    'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' :  '#__fitness_nutrition_plan_targets_comments',
                    'read_only' : true,
                    'anable_comment_email' : false
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
                 // connect comments
                 var comment_options = {
                    'item_id' :  id,
                    'fitness_administration_url' : app.options.fitness_frontend_url,
                    'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                    'db_table' : '#__fitness_nutrition_plan_macronutrients_comments',
                    'read_only' : true,
                    'anable_comment_email' : false
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
                 
                 $('#protocols_wrapper').html(new Protocols_wrapper_view().render().el);
                 
                 
                 app.collections.protocols = new Protocols_collection(); 

                 app.collections.protocols.fetch({
                    data: {nutrition_plan_id : app.options.nutrition_plan_id},
                    success: function (collection) {
                        //console.log(collection);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                 });
                 
                 app.views.protocols = new Protocols_view({el : $("#protocol_list"), collection : app.collections.protocols}); 
                 app.views.protocols.render();
                 
            },
            
            add_supplement_protocol : function() {
                app.views.protocol = new Protocol_view({model : new Protocol_model(), collection : app.collections.protocols}); 
                $("#protocol_list").append(app.views.protocol.render().el );
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
                        ingredients_collection : app.collections.shopping_list_ingredients
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
            },
                   
            close: function() {
                 this.no_active_plan_action();
                 $("#close_tab").hide();
                 app.models.nutrition_plan.set({id : app.options.item_id});
                 this.overview();
            },
            
            common_actions : function() {
                $(".block").hide();
                $(".plan_menu_link").removeClass("active_link")
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
                            status : '1',
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
             }

        });

    return Controller;
});