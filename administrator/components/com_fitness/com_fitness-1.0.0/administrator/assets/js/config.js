require.config({
    //baseUrl: set up in view,
    
    map: {
      // '*' means all modules will get 'jquery-private'
      // for their 'jquery' dependency.
      '*': { 'jquery': 'jquery_private' },

      // 'jquery-private' wants the real jQuery module
      // though. If this line was not here, there would
      // be an unresolvable cyclic dependency.
      'jquery_private': { 'jquery': 'jquery' }
    },
    
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
        jwplayer: {
            exports: 'jwplayer'
        },
        jwplayer_key: {
            deps: ['jwplayer']
        },
        'jquery.tableDnD': {
            deps: ['jquery'],
            exports: 'jquery.tableDnD'
        },
        
        'jquery.scrollTo': {
            deps: ['jquery'],
            exports: 'jquery.scrollTo'
        },
        
    },
    paths: {
        jquery: 'lib/jquery',
        jquery_private: 'lib/jquery_private',
        'jqueryui': 'lib/jquery-ui',
        'jquery.validate' : 'lib/jquery.validate.min',
        underscore: 'lib/underscore-min',
        backbone: 'lib/backbone-min',
        'backbone.syphon': 'lib/backbone.syphon.min',
        'jquery.AjaxCall': 'lib/ajax_call_function',
        'jquery.goals_frontend': 'lib/goals_frontend',
        'jquery.fitness_helper': 'lib/fitness_helper',
        text: 'lib/text',
        'jquery.flot': 'lib/jquery.flot',
        'jquery.flot.time': 'lib/jquery.flot.time',
        'jquery.flot.pie': 'lib/jquery.flot.pie',
        'jquery.drawPie': 'lib/flot_pie_class',
        'moment': 'lib/moment.min',
        'jquery.timepicker': 'lib/jquery.timepicker.min',
        'jquery.backbone_pagination': 'lib/backbone_pagination',
        'jquery.nutritionPlan': 'lib/nutrition_plan_class',
        'jquery.macronutrientTargets': 'lib/dayly_targets_class',
        'jquery.status': 'lib/status_class',
        jwplayer : 'lib/jwplayer/jwplayer',
        jwplayer_key : 'lib/jwplayer/jwplayer_key',
        'jquery.itemDescription': 'lib/meal_description_class',
        'jquery.backbone_image_upload' : 'lib/backbone_image_upload',
        'jquery.backbone_video_upload' : 'lib/backbone_video_upload',
        'jquery.recipe_database' : 'lib/recipe_database_class',
        'jquery.nutritionMeal' : 'lib/nutrition_meal_class',
        'jquery.calculateSummary' : 'lib/plan_summary_class',
        'jquery.gredient_graph' : 'lib/gredient_graph',
        'jquery.tableDnD' : 'lib/jquery.tablednd',
        'jquery.ajax_indicator' : 'lib/ajax_indicator',
        'jquery.cleditor' : 'lib/jquery.cleditor',
        'jquery.scrollTo' : 'lib/jquery.scrollTo.min'

    },

    waitSeconds: 30
});

