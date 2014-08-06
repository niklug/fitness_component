define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'models/nutrition_plan/target',
        'views/diary/frontend/target_block',
        'views/diary/frontend/meal_entries_block',
	'text!templates/diary/frontend/item.html',
        'jquery.validate',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.time',
        'jquery.flot.pie',
        'jquery.drawPie',
        'jquery.nutritionMeal',
        //'jquery.calculateSummary',
        'jquery.macronutrientTargets',
        'jquery.timepicker',
        'jquery.gredient_graph',
        'jquery.itemDescription'
], function (
        $,
        _,
        Backbone,
        app,
        Target_model,
        Target_block_view,
        Meal_entries_block,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
        },
        
        template : _.template(template),

        render : function () {
            var data = {item : this.model.toJSON()};
            data.active_plan_data = this.active_plan_data;
            data.$ = $;
            $(this.el).html(this.template(data));
            
            this.connectTargets(this.active_plan_data.id);
            
            this.connectMealEntries();
            
            //this.connectMealsBlock();
            
            return this;
        },
        
        connectTargets : function(id) {
            if(app.models.target) {
                this.loadTargets(id);
                return;
            }
            
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
            $(this.el).find("#targets_wrapper").html(new Target_block_view({model : app.models.target, item_model : app.models.active_plan_data}).render().el);
        },

        connectMealsBlock : function() {
            return;
            this.item = this.model.toJSON();
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
                'fitness_frontend_url' : app.options.fitness_frontend_url,
                'fitness_administration_url' : app.options.fitness_frontend_url,
                'main_wrapper' : $("#diary_guide"),
                'ingredient_obj' : {id : "", meal_name : "", quantity : "", measurement : "", protein : "", fats : "", carbs : "", calories : "", energy : "", saturated_fat : "", total_sugars : "", sodium : ""},
                'db_table' : '#__fitness_nutrition_diary_ingredients',
                'parent_view' : 'nutrition_diary_frontend',
                'read_only' : submitted
            }


            var nutrition_meal_options = {
                'main_wrapper' : $("#meals_wrapper"),
                'nutrition_plan_id' :  this.item.id,
                'fitness_administration_url' : app.options.fitness_frontend_url,
                'add_meal_button' : $("#add_plan_meal"),
                'activity_level' : "input[name='jform[activity_level]']",
                'meal_obj' : {id : "", 'nutrition_plan_id' : "", 'meal_time' : "", 'water' : "", 'previous_water' : ""},
                'db_table' : '#__fitness_nutrition_diary_meals',
                'read_only' : submitted,
                'import_date' : true,
                'import_date_source' : '#entry_date'
            }

            var nutrition_comment_options = {
                'item_id' : this.item.id,
                'fitness_administration_url' : app.options.fitness_frontend_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_nutrition_diary_comments',
                'read_only' : scored,
                'anable_comment_email' : true,
                'comment_method' : 'DiaryComment'
            }

            var nutrition_bottom_comment_options = {
                'item_id' : this.item.id,
                'fitness_administration_url' : app.options.fitness_frontend_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_nutrition_diary_comments',
                'read_only' : scored,
                'anable_comment_email' : true,
                'comment_method' : 'DiaryComment'
            }

            var calculate_summary_options = {
                'activity_level' : "input[name='jform[activity_level]']",
                'chart_container' : $("#placeholder_scope"),
                'draw_chart' : true
            }

            var macronutrient_targets_options = {
                'targets_main_wrapper' : "#daily_micronutrient",
                'fitness_administration_url' : app.options.fitness_frontend_url,
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
            
            window.nutrition_meal = this.nutrition_meal;
            
            window.resetBody = function() {
                $("body").css('overflow', 'auto');
            }
            

            this.calculateSummary.run();

            this.macronutrient_targets_heavy.run();
            this.macronutrient_targets_light.run();
            this.macronutrient_targets_rest.run();

            var plan_comments_html = this.plan_comments.run();
            $("#plan_comments_wrapper").html(plan_comments_html);
            
        }, 
        
        close : function() {
            if(typeof this.calculateSummary !== 'undefined') {
                clearInterval(this.calculateSummary.interval);
            }

            delete this.calculateSummary;
            delete this.nutrition_meal;
            delete this.macronutrient_targets_heavy;
            delete this.macronutrient_targets_light;
            delete this.macronutrient_targets_rest;
            delete this.plan_comments;

        },
        
        connectMealEntries : function() {
            this.loadMealEntries();
        },
        
        loadMealEntries : function() {
            
            $(this.el).find("#meal_entries_wrapper").html(new Meal_entries_block({model : this.model, plan_model : app.models.active_plan_data}).render().el);
        },

 
    });
            
    return view;

});