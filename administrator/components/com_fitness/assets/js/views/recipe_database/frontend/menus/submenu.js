define([
	'jquery',
	'underscore',
	'backbone',
	'text!templates/recipe_database/frontend/menus/submenu.html'
], function ( $, _, Backbone, template ) {

    var view = Backbone.View.extend({

        el: $("#recipe_submenu"), 

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },
    });
            
    return view;
});