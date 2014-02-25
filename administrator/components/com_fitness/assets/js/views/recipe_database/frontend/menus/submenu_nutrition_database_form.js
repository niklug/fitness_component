define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/recipe_database/frontend/menus/submenu_nutrition_database_form.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
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
            this.saveItem();
        },

        onClickSaveClose : function() {
            this.save_method = 'save_close';
            this.saveItem();
        },

        onClickSaveNew : function() {
            this.save_method = 'save_new';
            this.saveItem();
        },

        onClickCancel : function() {
            this.controller.navigate("!/nutrition_database", true);
        },
        
        
        saveItem : function() {
            var self = this;
            $("#add_ingredient_form" ).die().live('submit', function(event) {
                event.preventDefault();
                var data = Backbone.Syphon.serialize(this);
                self.model.save(data, {
                    success: function (model, response) {
                        if(self.save_method == 'save_close') {
                            self.controller.navigate("!/nutrition_database", true);
                        } else if(self.save_method == 'save_new') {
                            self.controller.navigate("");
                            self.controller.navigate("!/add_ingredient", true);
                        }
                        
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            });

            $("#add_ingredient_form" ).submit();
        }
    });
            
    return view;
});