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
        underscore: 'lib/underscore-min',
        backbone: 'lib/backbone-min',
        'jquery.AjaxCall': 'lib/ajax_call_function',
        'jquery.comments': 'lib/comments_class',
        'jquery.goals_frontend': 'lib/goals_frontend',
        'jquery.fitness_helper': 'lib/fitness_helper',
        text: 'lib/text'
    },
    waitSeconds: 5
});


require([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'routers/nutrition_plan/router',
    'views/nutrition_plan/main_menu',
    'jquery.AjaxCall',
    'jquery.goals_frontend',
    'jquery.comments',
    'jquery.fitness_helper'

], function($, _, Backbone, app, Controller, Main_menu_view) {
    console.log(app);
    $.fitness_helper = $.fitness_helper(app.options);
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

    
    app.routers.nutrition_plan = new Controller();
    
    app.views.main_menu = new Main_menu_view();
    
    Backbone.history.start();

});
