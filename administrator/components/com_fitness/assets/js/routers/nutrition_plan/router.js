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
        'collections/nutrition_plan/nutrition_guide/add_meal_recipes',
	'models/nutrition_plan/nutrition_plan',
        'models/nutrition_plan/target',
        'models/nutrition_plan/nutrition_guide/menu_plan',
        'models/nutrition_plan/nutrition_guide/example_day_meal',
        'models/nutrition_plan/nutrition_guide/get_recipe_params',
        'views/nutrition_plan/overview',
        'views/nutrition_plan/target_block',
        'views/nutrition_plan/macronutrients',
        'views/nutrition_plan/supplements/frontend/protocols',
        'views/nutrition_plan/information',
        'views/nutrition_plan/archive_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_list_menu',
        'views/nutrition_plan/nutrition_guide/menu_plan_list',
        'views/nutrition_plan/nutrition_guide/menu_plan_header',
        'views/nutrition_plan/nutrition_guide/example_day_menu',
        'views/nutrition_plan/nutrition_guide/example_day',
        'views/nutrition_plan/nutrition_guide/example_day_meal'
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
        Nutrition_plan_model,
        Target_model,
        Menu_plan_model,
        Example_day_meal_model,
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
        Example_day_meal_view
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

            },
        
            routes: {
                "": "overview", 
                "!/": "overview", 
                "!/overview": "overview", 
                "!/targets": "targets", 
                "!/macronutrients": "macronutrients", 
                "!/supplements": "supplements", 
                "!/nutrition_guide": "nutrition_guide", 
                "!/menu_plan/:id": "menu_plan", 
                "!/example_day/:id": "example_day", 
                "!/add_example_day_meal/:id": "add_example_day_meal", 
                "!/add_meal_recipe/:meal_id": "add_meal_recipe",
                "!/information": "information", 
                "!/archive": "archive", 
                "!/close": "close", 
            },
            
            get_database_recipes : function() {
                app.collections.add_meal_recipes.fetch({
                    data : app.models.get_recipe_params.toJSON(),
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
                 // connect Graph from Goals frontend logic
                 $.goals_frontend(app.options);
                 var id = app.models.nutrition_plan.get('id');
                 app.models.nutrition_plan.fetch({
                    data: {id : id},
                    wait : true,
                    success : function(model, response) {
                        var overview_view = new Overview_view({model : model});
                        $("#nutrition_focus_wrapper").html(overview_view.render().el);
                    },
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
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
                    app.views.example_day_menu = new Example_day_menu_view();
                    $("#nutrition_guide_container").html(app.views.example_day_menu.render().el);
                }

            },
            
            example_day : function(example_day_id) {
                app.collections.example_day_meals = new Example_day_meals_collection(); 
                var id = app.models.nutrition_plan.get('id');
                app.collections.example_day_meals.fetch({
                    data: {
                        nutrition_plan_id : id,
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
                app.views.example_day_meal = new Example_day_meal_view({model : new 
                    ({'example_day_id' : example_day_id}), collection : app.collections.example_day_meals}); 
                $("#example_day_meal_list").append(app.views.example_day_meal.render().el );
            },
            
            add_meal_recipe : function(meal_id) {
                this.get_database_recipes();
                
                //var meal_model = window.app.example_day_meal_collection.get({id : meal_id});
                
                //$('#example_day_wrapper').html(new window.app.Example_day_add_recipe_view({collection : window.app.nutrition_guide_add_recipe_collection, model : meal_model}).render().el);

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
                $(".plan_menu_link").removeClass("active_link")
            },
            
            no_active_plan_action : function() {
                if(!app.options.item_id) {
                    alert('Please contact your trainer immediately regarding your current Nutrition Plan!');
                    return false;
                }
           }

        });

    return Controller;
});