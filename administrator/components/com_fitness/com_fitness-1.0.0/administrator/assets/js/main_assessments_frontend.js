require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/assessments/router_frontend',
    'views/assessments/frontend/menus/main_menu',
    'jquery.AjaxCall',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.status',
    'jquery.ajax_indicator',
    'jquery.cleditor'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {
    //console.log($.fn.jquery);

    $.ajax_indicator({});
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
    
    app.views.main_menu = new Main_menu_view();

    Backbone.history.start();

});
