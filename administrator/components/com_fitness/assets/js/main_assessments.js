require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/assessments/router_backend',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.status',
    'jquery.ajax_indicator',
    'jquery.cleditor'
    

], function($, _, Backbone, moment, app, Controller) {
    $.ajax_indicator({});
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
      
    Backbone.history.start();

});
