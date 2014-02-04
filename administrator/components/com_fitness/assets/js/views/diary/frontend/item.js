define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/item.html',
        'jquery.validate',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.time',
        'jquery.flot.pie',
        'jquery.drawPie',
        'jquery.nutritionMeal',
        'jquery.calculateSummary',
        'jquery.macronutrientTargets',
        'jquery.timepicker',
        'jquery.gredient_graph',
        'jquery.itemDescription'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.active_plan_data = app.models.active_plan_data.toJSON();
            this.heavy_target = this.collection.findWhere({type : 'heavy'}).toJSON();
            this.light_target = this.collection.findWhere({type : 'light'}).toJSON();
            this.rest_target = this.collection.findWhere({type : 'rest'}).toJSON();
        },
        
        template : _.template(template),

        render : function () {
            var data = this.model.toJSON();
            data.active_plan_data = this.active_plan_data;
            data.heavy_target = this.heavy_target;
            data.light_target = this.light_target;
            data.rest_target = this.rest_target;
            data.$ = $;
            $(this.el).html(this.template(data));
            
            this.setTarget();
            
            this.connectMealsBlock();
            
            return this;
        },
        
        setTarget : function() {
            this.activity_level = this.model.get('activity_level');

            $('#jform_activity_level' + (parseInt(this.activity_level) - 1)).prop('checked',true);

            this.setTargetData(this.activity_level);
        },

        setTargetData : function(activity_level) {
            var activity_data;
            if(activity_level == '1') activity_data = this.heavy_target;
            if(activity_level == '2') activity_data = this.light_target;
            if(activity_level == '3') activity_data = this.rest_target;

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
        
        connectMealsBlock : function() {
            
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

        }
 
    });
            
    return view;

});