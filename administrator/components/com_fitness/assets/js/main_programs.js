require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/programs/router_backend',
    'jquery.AjaxCall',
    'jquery.comments',
    'jquery.fitness_helper',
    'jqueryui',
    'backbone.syphon',
    'jquery.backbone_pagination',
    'jquery.status'
    

], function($, _, Backbone, moment, app, Controller) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.controller = new Controller();
      
    Backbone.history.start();

});
