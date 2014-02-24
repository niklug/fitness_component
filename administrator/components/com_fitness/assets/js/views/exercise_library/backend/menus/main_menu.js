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
            app.controller.navigate("!/list_view", true);
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
            
            data.video = $("#preview_video").attr('data-videopath');
            
            data.global_business_permissions = $("#global_view_access").val();

            data.user_view_permission = this.getUserViewPermission();
            
            data.show_my_exercise = this.getShowMyExercise();
            
            data.my_exercise_clients = this.getMyExerciseList();
            
            data.business_profiles = this.getBusinessProfiles();
            
            data.status = $(".status_button").attr('data-status_id');

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
        
        getUserViewPermission : function() {
            var show_public_database_list = $(".show_public_database").map(function(){ return this.value }).get();
            
            var business_profile_list = $(".show_public_database").map(function(){ return this.getAttribute("data-business_profile_id") }).get();

            var obj;
            
            _.zip(business_profile_list, show_public_database_list).map(function(v){this[v[0]]=v[1];}, obj = {});
            
            var serialised = JSON.stringify(obj);
            
            return serialised;
        },
        
        getShowMyExercise : function(){
            var show_my_exercise_list = $(".show_my_exercise").map(function(){ return this.value }).get();
            
            var business_profile_list = $(".show_my_exercise").map(function(){ return this.getAttribute("data-business_profile_id") }).get();

            var obj;
            
            _.zip(business_profile_list, show_my_exercise_list).map(function(v){this[v[0]]=v[1];}, obj = {});
            
            var serialised = JSON.stringify(obj);
            
            return serialised;
        },
        
        getMyExerciseList : function(){
            var client_ids = $(".bisiness_client:checked").map(function(){ return this.getAttribute("data-client_id") }).get().join(",");
            return client_ids;
        },
        
        getBusinessProfiles : function() {
            var ids = $(".bisiness_profile_item:checked").map(function(){ return this.getAttribute("data-business_profile_id") }).get().join(",");
            return ids;
        }
    });
            
    return view;
});