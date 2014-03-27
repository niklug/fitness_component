require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/nutrition_plan/router',
    'views/nutrition_plan/main_menu',
    'jquery.AjaxCall',
    'jquery.goals_frontend',
    'jquery.comments',
    'jquery.fitness_helper',
    'jquery.flot',
    'jquery.flot.time',
    'jquery.flot.pie',
    'jquery.drawPie',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.nutritionPlan',
    'jquery.macronutrientTargets',
    'jquery.status',
    'jquery.ajax_indicator'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {
    $.ajax_indicator({});
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.routers.nutrition_plan = new Controller();
  
    app.views.main_menu = new Main_menu_view();
    
    $("#archive_focus_link").parent().hide();
    
    Backbone.history.start();
    
    // joomla form codding
     Joomla.submitbutton = function(task)  {
        if (task == 'nutrition_plan.cancel') {
            Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
        }
        else{

            if (task != 'nutrition_plan.cancel' && document.formvalidator.isValid(document.id('nutrition_plan-form'))) {

                if(app.options.nutrition_plan_id) {
                    // Targets
                    var heavy_validation = app.macronutrient_targets_heavy.validateSum100();
                    if(heavy_validation == false) {
                        alert('Protein, Fats and Carbs MUST equal (=) 100%');
                        return;
                    }

                    var light_validation = app.macronutrient_targets_light.validateSum100();
                    if(light_validation == false) {
                        alert('Protein, Fats and Carbs MUST equal (=) 100%');
                        return;
                    }

                    var rest_validation = app.macronutrient_targets_rest.validateSum100();
                    if(rest_validation == false) {
                        alert('Protein, Fats and Carbs MUST equal (=) 100%');
                        return;
                    }
                }

                //save targets data
                if(app.options.nutrition_plan_id) {  

                    app.macronutrient_targets_heavy.saveTargetsData(function(output) {
                        app.macronutrient_targets_light.saveTargetsData(function(output) {
                            app.macronutrient_targets_rest.saveTargetsData(function(output) {
                                //reset force active fields in database by ajax
                                var force_active = $("#jform_force_active0").is(":checked");
                                if(force_active) {
                                    app.nutrition_plan.resetAllForceActive(function() {
                                        Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                                    });
                                } else {
                                    Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                                }
                            });
                        });

                      });

                } else {
                    //reset force active fields in database by ajax
                    var force_active = $("#jform_override_dates0").is(":checked");

                    if(force_active) {
                        app.nutrition_plan.resetAllForceActive(function() {
                            Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                        });
                    } else {
                        Joomla.submitform(task, document.getElementById('nutrition_plan-form'));
                    }

                }
            }
            else {
                alert('Form Validation Failed');
            }
        }
    }

});
