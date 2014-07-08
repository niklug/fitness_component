define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/backend/menus/form_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({

        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #cancel" : "onClickCancel",
        },

        onClickSave : function() {
            this.save_method = 'save';
            this.saveItem();
        },

        onClickSaveClose : function() {
            this.save_method = 'save_close';
            this.saveItem();
        },

        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            var data = {};
            
            //
            var information = $("#information").val();
            if(typeof information !== 'undefined') {
                information = encodeURIComponent(information);
            } else {
                information = '';
            }
            data.information = information;
            
            console.log(data);
              
            this.model.set(data);
            

            var id = this.model.get('id');
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    if(self.save_method == 'save') {
                        app.controller.navigate("");
                        app.controller.navigate("!/information/" + model.get('id'), true);
                    } else if(self.save_method == 'save_close') {
                        app.controller.navigate("!/list_view", true);
                    } else {
                        app.controller.navigate("!/list_view", true);
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },

    });
            
    return view;
});