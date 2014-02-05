require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/diary/router_frontend',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination'
    

], function($, _, Backbone, moment, app, Controller) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
      
    Backbone.history.start();

});
