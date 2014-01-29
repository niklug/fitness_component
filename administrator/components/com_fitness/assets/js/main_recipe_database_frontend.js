require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/recipe_database/router_frontend',
    'views/recipe_database/frontend/menus/main_menu',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.routers.recipe_database = new Controller();
    
    app.views.main_menu = new Main_menu_view();
    
    app.views.main_menu.render();
      
    Backbone.history.start();

});
