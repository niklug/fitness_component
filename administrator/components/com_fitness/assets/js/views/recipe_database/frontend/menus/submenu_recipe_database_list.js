define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_recipe_database_list.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var variables = {'recipe_id' : this.options.recipe_id};
            var template = _.template(this.template(variables));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #add_item" : "onClickAddItem",
        },

        onClickAddItem : function() {
            this.controller.navigate("!/add_ingredient", true);
        },
    });
            
    return view;
});