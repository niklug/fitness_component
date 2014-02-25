define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/exercise_library/business_profiles',
        'collections/exercise_library/clients',
        'views/exercise_library/backend/business_profile_block',
	'text!templates/exercise_library/backend/business_permissions.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Business_profiles_collection, 
        Clients_collection,
        Business_profile_block_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            app.collections.business_profiles = new Business_profiles_collection();
            app.collections.clients = new Clients_collection();

            var self = this;
            $.when (
                app.collections.business_profiles.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                }),

                app.collections.clients.fetch({
                    error: function (collection, response) {
                        alert(response.responseText);
                    }
                })
 
            ).then (function(response) {
                self.render();
            })
            
        },
        
        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            var self = this;
            _.each(app.collections.business_profiles.models, function (model) { 
                self.addItem(model);
            }, this);
            
            var value = this.$el.find("#global_view_access").val();
            
            if(app.options.is_superuser) {
                this.setGlobalViewAccess(value);
            }
            
            if(this.model.get('id')) {
                this.connectComments();
            }
            
            return this;
        },
        
        events : {
            "change #global_view_access" : "onChangeGlobalViewAccess",
        },
        
        addItem : function(model) {
            var user_business_profile_id = app.options.business_profile_id;
            var business_profile_id = model.get('id');
            
            if((user_business_profile_id == business_profile_id) || app.options.is_superuser) {
                var container = this.$el.find("#business_profiles_list");
                container.append(new Business_profile_block_view({model : model, item_model : this.model, collection : app.collections.clients}).render().el);
            }
            
            
            if(user_business_profile_id == business_profile_id) {
                var checkbox = this.$el.find(".bisiness_profile_item");
                checkbox.attr('checked', true);
                checkbox.attr('disabled', true);
            }
        },
        
        onChangeGlobalViewAccess : function(event) {
            var value = $(event.target).val();
            this.setGlobalViewAccess(value);
        },
        
        setGlobalViewAccess : function(value) {
            var checkboxes = this.$el.find(".bisiness_profile_item");
            
            if(parseInt(value)) {
                checkboxes.attr('checked', true);
                checkboxes.attr('disabled', true);
            } else {
                checkboxes.attr('disabled', false);
            }
        },
        
        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_exercise_library_comments',
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'ExerciseLibraryComment'
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $("#comments_wrapper").html(comments_html);
        },
    });
            
    return view;
});