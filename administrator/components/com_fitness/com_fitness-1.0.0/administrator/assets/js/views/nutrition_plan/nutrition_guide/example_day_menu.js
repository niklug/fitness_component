define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/example_day_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },

        events:{
            "click .menu_day": "onChooseDay",
            "click .shopping_list": "onChooseShoopingList"
        },

        onChooseDay:function (event) {
            $(".example_day_link").removeClass("active");
            var day = $(event.target).attr('data-id');
            $(event.target).addClass("active");
            app.controller.navigate("!/example_day/" + day + "/" + this.model.get('id') + '/' + this.options.nutrition_plan_id, true);
        },
        
        onChooseShoopingList:function (event) {
            $(".example_day_link").removeClass("active");
            $(event.target).addClass("active");
            app.controller.navigate("!/shopping_list/" + this.options.nutrition_plan_id + "/" + this.model.get('id'), true);
        }

    });
            
    return view;
});