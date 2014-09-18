define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/comments/comments',
        'models/comments/comment',
        'views/comments/conversation',
	'text!templates/comments/index.html'
        
], function (
        $,
        _,
        Backbone,
        app,
        Comments_collection,
        Comment_model,
        Conversation_view,
        template 
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            //console.log(this.options);
            app.collections.comments = new Comments_collection([], {db_table : this.options.db_table});
            //console.log( app.collections.comments);
            var self = this;
            app.collections.comments.fetch({
                success : function (collection, response) {
                    //console.log(collection.toJSON());
                    self.populateItems(collection);
                },
                error : function (collection, response) {
                    alert(response.responseText);
                }
            }); 
        },
        
        template : _.template(template),

        render : function () {
            var data = {};
            data.$ = $;
            $(this.el).html(this.template(data));
      
            return this;
        },
        
        events: {
            "click .create_conversation" : "onClickCreateConversation",
        },
        
        populateItems : function(collection) {
            var self = this;
            _.each(collection.models, function (model) { 
                this.addItem(model);
            }, this);
        },
        
        addItem : function(model) {
            if(this.options.item_id != model.get('item_id')) {
                return;
            }
            
            if(!model.isNew() && model.get('parent_id') != '0') {
                return;
            }
            var conversation_permissions = model.get('conversation_permissions');
            
            var logged_business_profile_id = app.options.business_profile_id;
            var business_profile_id = model.get('business_profile_id');
            
            var allowed_users = model.get('allowed_users');
            if(allowed_users) {
                allowed_users = allowed_users.split(",");
            }
            
            var allowed_business = model.get('allowed_business');
            if(allowed_business) {
                allowed_business = allowed_business.split(",");
            }
            
            var user_id = app.options.user_id;
            var created_by = model.get('created_by');
            
          
            // filter for trainers by business
            if(conversation_permissions == 'all_business' || conversation_permissions == 'selected_business') {
                if(app.options.is_superuser) {
                    if(created_by != user_id) {
                        return;
                    }
                }
                
                if(app.options.is_trainer) {
                    if(allowed_business.indexOf(logged_business_profile_id) == '-1') {
                        return;
                    }
                }
                
                if(app.options.is_client) {
                    return;
                }
                
            }
            
            // filter for trainers by user_id
            if(conversation_permissions == 'all_trainers' || conversation_permissions == 'all_my_trainers' || conversation_permissions == 'selected_trainers') {
                if(app.options.is_superuser) {
                    if(created_by != user_id) {
                        return;
                    }
                }
                
                if(app.options.is_trainer && (created_by != user_id)) {
                    if(allowed_users.indexOf(user_id) == '-1') {
                        return;
                    }
                }
                
                if(app.options.is_client && (created_by != user_id)) {
                    return;
                }
            }
            
            // filter for clients by user_id
            if(conversation_permissions == 'all_clients' || conversation_permissions == 'selected_clients') {
                if(app.options.is_superuser) {
                    if(created_by != user_id) {
                        return;
                    }
                }
                
                if(app.options.is_trainer) {
                    
                }
                
                if(app.options.is_client && (created_by != user_id)) {
                    if(allowed_users.indexOf(user_id) == '-1') {
                        return;
                    }
                }
            }
            
            $(this.el).find(".conversations_container").append(
                new Conversation_view({
                    comment_options : this.options,
                    model : model,
                    collection : app.collections.comments
                }).render().el
            );
        },
        
        onClickCreateConversation : function() {
            var data = {
                business_profile_id : app.options.business_profile_id,
                created_by : app.options.user_id,
                created_by_name : app.options.user_name,
                created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss"),
                item_id: this.options.item_id,
                sub_item_id: this.options.sub_item_id,
            };
            var model = new Comment_model(data, {db_table : this.options.db_table});
            this.addItem(model);
        }
     
    });
            
    return view;

});