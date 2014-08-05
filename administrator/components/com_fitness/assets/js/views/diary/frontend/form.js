define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/diary/frontend/form.html',
        'jquery.drawPie'
], function (
        $,
        _,
        Backbone,
        app,
        template 
    ) {

    var view = Backbone.View.extend({
        
        template : _.template(template),

        render : function () {
            $(this.el).html(this.template());
            return this;
        },

    });
            
    return view;
});