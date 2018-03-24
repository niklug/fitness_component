define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/exercise_library/backend/business_client_item',
	'text!templates/exercise_library/backend/business_profile_block.html'
], function ( $, _, Backbone, app, Business_client_item_view,template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            $(this.el).html(template);
            
            this.business_profile_id = this.model.get('id');
            var clients_models =  this.collection.where({business_profile_id : this.business_profile_id});
            var self = this;
            _.each(clients_models, function (model) { 
                self.addItem(model);
            }, this);
            
            this.setShowPublicDatabase();
            
            this.setMyExerciseList();
            
            this.setShowMyExercise($(this.el).find(".show_my_exercise").val());
            
            this.setBusinessProfiles();
            
            this.setPermissions();
            
            return this;
        },
        
        events : {
            "change .show_my_exercise" : "onChangeShowMyExercise",
            
        },
        
        addItem : function(model) {
           var container = $(this.el).find(".business_clients_wrapper");
           container.append(new Business_client_item_view({model : model, item_model : this.options.item_model}).render().el);
        },

        setShowPublicDatabase : function(){
            var item_id = this.options.item_model.get('id');
            if(!item_id && !app.options.is_superuser) {
                $(this.el).find(".show_public_database").val('0');
                return;
            }
            
            var user_view_permission = this.options.item_model.get('user_view_permission');
            
            if(!user_view_permission) {
                return;
            }
            
            user_view_permission = JSON.parse(user_view_permission);
            
            if(user_view_permission && this.business_profile_id) {
                var show_public_database = user_view_permission[this.business_profile_id];
                $(this.el).find(".show_public_database").val(show_public_database);
            }
        },
        
        setMyExerciseList : function() {
            var show_my_exercise_items = JSON.parse(this.options.item_model.get('show_my_exercise'));
            
            if(show_my_exercise_items && this.business_profile_id) {
                var show_my_exercise = show_my_exercise_items[this.business_profile_id];
                $(this.el).find(".show_my_exercise").val(show_my_exercise);
            }
        },
        
        onChangeShowMyExercise : function(event) {
            var value = $(event.target).val();
            this.setShowMyExercise(value);
        },
        
        setShowMyExercise : function(value) {
            $(this.el).find(".business_clients_wrapper").show();
            var checkboxes = $(this.el).find(".bisiness_client");
            if(value == '0') {
                checkboxes.attr('checked', false);
                checkboxes.attr('disabled', true);
                $(this.el).find(".business_clients_wrapper").hide();
            } else if(value == '1') {
                checkboxes.attr('checked', true);
                checkboxes.attr('disabled', true);
            } else {
                 checkboxes.attr('disabled', false);
            }
        },
        
        setBusinessProfiles : function() {
            var business_profiles = this.options.item_model.get('business_profiles');
            
            if((typeof business_profiles === 'undefined') || business_profiles == null) {
                return;
            }

            if(_.include(business_profiles.split(","), this.business_profile_id)) {
                $(this.el).find(".bisiness_profile_item").attr('checked', true);
            }
        },
        
        setPermissions : function() {
            var edit_allowed = app.controller.edit_allowed(this.options.item_model);

            if(edit_allowed == false) {
                $(this.el).find(".show_public_database").attr('disabled', true);
            }
            
        }
        
    });
            
    return view;
});