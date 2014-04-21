define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs_templates/backend/menus/main_menu.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            
        },

        template:_.template(template),
        
        render: function(){
            var data  = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            return this;
        },

        events: {
            "click #save" : "onClickSave",
            "click #save_close" : "onClickSaveClose",
            "click #save_new" : "onClickSaveNew",
            "click #save_copy" : "onClickSaveCopy",
            "click #cancel" : "onClickCancel",
            "click #add_template" : "onClickAddTemplate",
            "click #search_program" : "onClickSaerchProgram",
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
        
        onClickSaveCopy : function() {
            var id = this.model.get('id');
            var data = {};
            var url = app.options.ajax_call_url;
            var view = 'programs_templates';
            var task = 'copyProgramTemplate';
            var table = '';
            data.id = id;
            $.AjaxCall(data, url, view, task, table, function(output){
                app.controller.navigate("!/form_view/" + output, true);
            });
        },

        onClickCancel : function() {
            app.controller.navigate("!/list_view", true);
        },
        
        
        saveItem : function() {
            var data = {};
            
            var appointment_field = $('#appointment_id');
            
            var session_type_field = $('#session_type');
            
            var session_focus_field = $('#session_focus');
            
            var workout_name_field = $('#workout_name');
            
            data.appointment_id = appointment_field.val();
            
            data.session_type = session_type_field.val();
            
            data.session_focus = session_focus_field.val();
            
            data.name = workout_name_field.val();
            
            data.state = '1';
            
            data.business_profile_id = $('#business_profile_select').val();

            var description = $('#description').val();
             
            if(typeof description !== 'undefined') {
                description = encodeURIComponent(description);
            } else {
                description = '';
            }
            
            data.description = description;
            
            data.trainer_id = $('#trainer_id').val();
            
            data.access = $('#access').val();
            
            if(!this.model.get('id')) {
                data.created_by = app.options.user_id;
                data.created = moment(new Date()).format("YYYY-MM-DD");
            }
            
            this.model.set(data);

            
            console.log(this.model.toJSON());
            
            $('#title, #session_type, #session_focus, #created, #workout_name').removeClass("red_style_border");

            
            if (!this.model.isValid()) {
                var validate_error = this.model.validationError;
                if(validate_error == 'appointment_id') {
                    appointment_field.addClass("red_style_border");
                    return false;
                } else if(validate_error == 'name') {
                    workout_name_field.addClass("red_style_border");
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
        
        onClickAddTemplate : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.add_template(id);
        },
        
        onClickSaerchProgram : function(event) {
            var id = $(event.target).attr('data-id');
            app.controller.search_program(id);
        }

    });
            
    return view;
});