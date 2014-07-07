define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/menu_plan_list_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #create_menu" : "onClickCreateMenu",
        },

        onClickCreateMenu : function() {
            app.controller.navigate("!/menu_plan/0/" + this.options.nutrition_plan_id, true);
        },
    });
            
    return view;
});