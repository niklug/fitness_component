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
        
        
        
        
        
        
        'jquery.AjaxCall': {
            deps: ['jquery']
        },

        'jquery.comments': {
            deps: ['jquery']
        },
        
        'jquery.goals_frontend': {
            deps: ['jquery']
        },
        
        'jquery.fitness_helper': {
            deps: ['jquery']
        },
    },
    paths: {
        jquery: 'lib/jquery',
        'jquery-ui': 'lib/jquery-ui',
        underscore: 'lib/underscore-min',
        backbone: 'lib/backbone-min',
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
    

], function($, _, Backbone, moment, app, Controller, Main_menu_view) {
    console.log(moment);
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;


    app.routers.nutrition_plan = new Controller();
  
    app.views.main_menu = new Main_menu_view();
    
    Backbone.history.start();

});
