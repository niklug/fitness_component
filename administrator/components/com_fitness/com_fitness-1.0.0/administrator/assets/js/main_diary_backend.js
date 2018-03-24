require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/diary/router_backend',
    'jquery.AjaxCall',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.ajax_indicator'
    

], function($, _, Backbone, moment, app, Controller) {
    $.ajax_indicator({});
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
      
    Backbone.history.start();

});
