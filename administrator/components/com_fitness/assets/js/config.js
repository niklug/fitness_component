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
        'jquery.timepicker': 'lib/jquery.timepicker.min',
        'jquery.backbone_pagination': 'lib/backbone_pagination'
    },

    waitSeconds: 5
});

