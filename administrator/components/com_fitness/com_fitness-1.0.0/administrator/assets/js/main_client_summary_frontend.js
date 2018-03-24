require([
    'jquery',
    'underscore',
    'backbone',
    'moment',
    'app',
    'routers/client_summary/router_frontend',
    'jquery.AjaxCall',
    'jquery.fitness_helper',
    'jquery.flot',
    'jquery.flot.time',
    'jquery.flot.pie',
    'jquery.drawPie',
    'jqueryui',
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
