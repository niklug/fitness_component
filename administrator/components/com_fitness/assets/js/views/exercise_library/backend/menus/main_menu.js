define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/backend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            
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
            this.save_method = 'save';
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
            this.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            var data = {};
            
            var exercise_name_field = $('#exercise_name');
            
            data.exercise_name = exercise_name_field.val();
            
            var created = this.model.get('created');
            
            var id = this.model.get('id');

            if(!id) {
                data.created = moment(new Date()).format("YYYY-MM-DD HH:mm:ss"); 
                data.created_by = app.options.user_id; 
            }

            this.model.set(data);
            
            console.log(this.model.toJSON());
            
            exercise_name_field.removeClass("red_style_border");
            
                       
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                
                if(validate_error == 'exercise_name') {
                    exercise_name_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    if(self.save_method == 'save') {
            
                    } else if(self.save_method == 'save_close') {
                        
                    } else if(self.save_method == 'save_new') {
                        
                    } else {
                        
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        }
    });
            
    return view;
});