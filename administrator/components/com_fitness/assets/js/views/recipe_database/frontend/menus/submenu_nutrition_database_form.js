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
                var item_id = self.model.get('id');
                
                data.category = data.category[0];

                self.model.save(data, {
                    success: function (model, response) {
                        if(self.save_method == 'save_close') {
                            self.controller.navigate("!/nutrition_database", true);
                        } else if(self.save_method == 'save_new') {
                            self.controller.navigate("");
                            self.controller.navigate("!/add_ingredient", true);
                        }
                        
                        if(!item_id) {
                            self.email_new_item(model.get('id'));
                        }
                    },
                    error: function (model, response) {
                        alert(response.responseText);
                    }
                });
            });

            $("#add_ingredient_form" ).submit();
        },
        
        
        email_new_item : function(id) {
            var data = {};
            var url = app.options.fitness_frontend_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'NutritionDatabase';

            $.AjaxCall(data, url, view, task, table, function(output){
                //console.log(output);
                var emails = output.split(',');
                var message = 'Emails were sent to: ' +  "</br>";
                $.each(emails, function(index, email) { 
                    message += email +  "</br>";
                });
                $("#emais_sended").append(message);
           });
        },
    });
            
    return view;
});