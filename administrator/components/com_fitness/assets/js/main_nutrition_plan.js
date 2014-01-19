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
    'jquery.macronutrientTargets'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.routers.nutrition_plan = new Controller();
  
    app.views.main_menu = new Main_menu_view();
    
    Backbone.history.start();

});
