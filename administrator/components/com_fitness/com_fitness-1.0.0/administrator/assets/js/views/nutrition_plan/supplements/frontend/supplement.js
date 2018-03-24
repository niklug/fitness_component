define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/nutrition_plan/supplements/frontend/supplement.html'
], function ( $, _, Backbone, template ) {

     var view = Backbone.View.extend({
         
        template:_.template(template),

        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click .view_product": "onClickViewProduct"
        },

        onClickViewProduct : function(event) {
            var url = $(event.target).attr('data-url');
            window.open(url);
        },

        close :function() {
            $(this.el).unbind();
            $(this.el).remove();
        },

    });

    return view;
});