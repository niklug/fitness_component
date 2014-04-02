require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/programs/router_frontend',
    'views/programs/frontend/menus/main_menu',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.status',
    'jquery.ajax_indicator'
    

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