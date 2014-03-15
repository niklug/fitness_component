define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/backend/menus/main_menu.html'
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
            app.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            var data = {};
            
            var appointment_field = $('#title');
            
            var session_type_field = $('#session_type');
            
            var session_focus_field = $('#session_focus');
            
            var start_date_field = $('#start_date');
            
            var finish_date_field = $('#finish_date');
            
            var start_time_field = $('#start_time');
            
            var finish_time_field = $('#finish_time');
            
            
            data.title = appointment_field.val();
            
            data.session_type = session_type_field.val();
            
            data.session_focus = session_focus_field.val();
            
            data.starttime  = start_date_field.val() + ' ' + start_time_field.val();
            
            data.endtime = finish_date_field.val() + ' ' + finish_time_field.val();

            data.owner = app.options.user_id;
            
            data.frontend_published = $('#frontend_published:checked').val() || '0';
            
            data.published = $('#published:checked').val() || '0';
            
            this.model.set(data);

            
            console.log(this.model.toJSON());
            
            $('#title, #session_type, #session_focus, #start_date, #finish_date, #start_time, #finish_time').removeClass("red_style_border");
            
            //validation          
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                
                if(validate_error == 'title') {
                    appointment_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'session_type') {
                    session_type_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'session_focus') {
                    session_focus_field.addClass("red_style_border");
                    return false;
                } else {
                    alert(this.model.validationError);
                    return false;
                }
            }
            
            if(!start_date_field.val()) {
                start_date_field.addClass("red_style_border");
                return false;
            }
            
            if(!finish_date_field.val()) {
                finish_date_field.addClass("red_style_border");
                return false;
            }
            
            if(!start_time_field.val()) {
                start_time_field.addClass("red_style_border");
                return false;
            }
            
            if(!finish_time_field.val()) {
                finish_time_field.addClass("red_style_border");
                return false;
            }
            //end validation

            
            var self = this;
            this.model.save(null, {
                success: function (model, response) {
                    var id = model.get('id');
                    if(self.save_method == 'save') {
                        app.controller.navigate("!/form_view/" + id, true);
                    } else if(self.save_method == 'save_close') {
                        app.controller.navigate("!/list_view", true);
                    } else if(self.save_method == 'save_new') {
                        app.controller.navigate("!/form_view/0", true);
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