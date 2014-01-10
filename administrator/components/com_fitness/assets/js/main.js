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
        
    },
    paths: {
        jquery: 'lib/jquery',
        underscore: 'lib/underscore-min',
        backbone: 'lib/backbone-min',
        text: 'lib/text'
    }
});


require([
    'jquery',
    'underscore',
    'backbone',
    'app',
    'collections/nutrition_plan/targets'

], function($, _, Backbone, app, model) {

    console.log(new model());
    
    Backbone.emulateHTTP = true ;
    Backbone.emulateJSON = true;

});
