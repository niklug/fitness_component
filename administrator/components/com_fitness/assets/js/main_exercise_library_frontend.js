require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/exercise_library/router_frontend',
    'views/exercise_library/frontend/menus/main_menu',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.status'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
    
    app.views.main_menu = new Main_menu_view();
    
    app.views.main_menu.render().el;
      
    Backbone.history.start();

});
