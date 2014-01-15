define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/example_day_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        initialize: function(){
            this.controller = app.routers.nutrition_plan;
        },

        render:function () {
            $(this.el).html(this.template());
            return this;
        },

        events:{
            "click .example_day_link": "onChooseDay"
        },

        onChooseDay:function (event) {
            $(".example_day_link").removeClass("active");
            var day = $(event.target).attr('data-id');
            $(event.target).addClass("active");
            this.controller.navigate("!/example_day/" + day, true);
        }

    });
            
    return view;
});