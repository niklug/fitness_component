require.config({
    //baseUrl: set up in view,
    shim: {
        underscore: {
            exports: '_'
        },
        backbone: {
            deps: [
                'underscore',
                'jquery'
            ],
            exports: 'Backbone'
        },
        
        'backbone.syphon': {
            deps: ['jquery', 'backbone'],

        },

        'jquery.flot': {
            deps: ['jquery'],
            exports: 'jquery.flot'
        },
        'jquery.flot.time': {
            deps: ['jquery', 'jquery.flot'],
        },
        'jquery.flot.pie': {
            deps: ['jquery', 'jquery.flot', 'jquery.flot.time'],
        },
        'jquery.drawPie': {
            deps: ['jquery', 'jquery.flot', 'jquery.flot.time', 'jquery.flot.pie'],
        },
        
        moment: {
            exports: 'moment'
        },

    },
    paths: {
        jquery: 'lib/jquery',
        'jqueryui': 'lib/jquery-ui',
        underscore: 'lib/underscore-min',
        backbone: 'lib/backbone-min',
        'backbone.syphon': 'lib/backbone.syphon.min',
        'jquery.AjaxCall': 'lib/ajax_call_function',
        'jquery.comments': 'lib/comments_class',
        'jquery.goals_frontend': 'lib/goals_frontend',
        'jquery.fitness_helper': 'lib/fitness_helper',
        text: 'lib/text',
        'jquery.flot': 'lib/jquery.flot',
        'jquery.flot.time': 'lib/jquery.flot.time',
        'jquery.flot.pie': 'lib/jquery.flot.pie',
        'jquery.drawPie': 'lib/flot_pie_class',
        'moment': 'lib/moment.min',
        'jquery.timepicker': 'lib/jquery.timepicker.min'
    },

    waitSeconds: 5
});


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
    'backbone.syphon'
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {

    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    app.routers.nutrition_plan = new Controller();
  
    app.views.main_menu = new Main_menu_view();
    
    Backbone.history.start();

});
