define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/nutrition_guide/frontend/menu_plan_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),

        render: function(){
            var template = _.template(this.template({items : this.collection.toJSON(), $ : $}));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .preview" : "onClickPreview",
            "click .copy_menu_plan" : "onClickCopy",
        },

        onClickPreview : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.navigate("!/menu_plan/" + id + "/" + this.options.nutrition_plan_id, true);
        },
        
        onClickCopy : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.copy_menu_plan(id);
        }
    });
            
    return view;
});