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
            var active_start_field = $("#active_start");
            var active_finish_field = $("#active_finish");
            var nutrition_focus_field = $("#nutrition_focus");
            
            var data = {};
            
            data.active_start = active_start_field.val();

            data.active_finish = active_finish_field.val();
            
            data.nutrition_focus = nutrition_focus_field.val();
            
            data.force_active = $("#force_active").is(":checked");
            

            var trainer_comments = $("#trainer_comments").val();
             
            if(typeof trainer_comments !== 'undefined') {
                trainer_comments = encodeURIComponent(trainer_comments);
            } else {
                trainer_comments = '';
            }
            data.trainer_comments = trainer_comments;

            console.log(data);
              
            this.model.set(data);
            
            //validation
            active_start_field.removeClass("red_style_border");
            active_finish_field.removeClass("red_style_border");
            nutrition_focus_field.removeClass("red_style_border");
            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;

                if(validate_error == 'active_start') {
                    active_start_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'active_finish') {
                    active_finish_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'nutrition_focus') {
                    nutrition_focus_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            
            var id = this.model.get('id');
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    if(self.save_method == 'save') {
                        app.controller.navigate("");
                        app.controller.navigate("!/overview/" + model.get('id'), true);
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