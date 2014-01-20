define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/supplements/backend/protocols_wrapper.html'
], function ( $, _, Backbone, app, template ) {

     var view = Backbone.View.extend({

        template:_.template(template),
        
        initialize:function () {
            this.controller = app.routers.nutrition_plan;
            this.render();
        },

        render: function(){
            $(this.el).html(this.template());
            return this;
        },

        events:{
            "click #add_protocol": "onAddProtocol"
        },

        onAddProtocol:function () {
            this.controller.navigate("!/supplements");
            this.controller.navigate("!/add_supplement_protocol", true);
        }
    });
            
    return view;
});