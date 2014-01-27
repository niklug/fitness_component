define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/menus/frontend/submenu_recipe_database_item.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.render();
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #save_new" : "onClickSaveNew",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function() {

            window.app.nutrition_database_model.set({'action' : 'save'});

            $( "#add_ingredient_form" ).submit();
        },

        onClickSaveClose : function() {

            window.app.nutrition_database_model.set({'action' : 'save_close'});

            $( "#add_ingredient_form" ).submit();
        },

        onClickSaveNew : function() {

            window.app.nutrition_database_model.set({'action' : 'save_new'});

            $( "#add_ingredient_form" ).submit();
        },

        onClickCancel : function() {
           this.controller.navigate("!/nutrition_database", true);
        },
    });
            
    return view;
});