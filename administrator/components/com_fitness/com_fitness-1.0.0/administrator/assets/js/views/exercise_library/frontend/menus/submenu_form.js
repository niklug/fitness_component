define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/menus/submenu_form.html'
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
            app.controller.navigate("!/my_exercises", true);
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
            
            data.video = $("#video_container").attr('data-videopath');
            
            data.global_business_permissions = $("#global_view_access").val();

            data.user_view_permission = '{"' + app.options.business_profile_id + '":"0"}';;
            
            data.show_my_exercise = '{"' + app.options.business_profile_id + '":"2"}';
            
            data.my_exercise_clients = app.options.client_id;
            
            data.business_profiles = app.options.business_profile_id;

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
            var item_id = this.model.get('id');
            this.model.save(null, {
                success: function (model, response) {
                    var id = model.get('id');
                    if(self.save_method == 'save') {
                        app.controller.navigate("!/form_view/" + id, true);
                    } else if(self.save_method == 'save_close') {
                        app.controller.navigate("!/item_view/" + id, true);
                    } else if(self.save_method == 'save_new') {
                        app.controller.navigate("!/form_view/0", true);
                    } else {
                        app.controller.navigate("!/my_exercises", true);
                    }
                    
                    if(!item_id) {
                        self.email_new_item(model.get('id'));
                    }
                },
                error: function (model, response) {
                    alert(response.responseText);
                }
            });
        },
        
        email_new_item : function(id) {
            var data = {};
            var url = app.options.fitness_frontend_url;
            var view = '';
            var task = 'ajax_email';
            var table = '';

            data.id = id;
            data.view = 'ExerciseLibrary';
            data.method = 'NewExercise';
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