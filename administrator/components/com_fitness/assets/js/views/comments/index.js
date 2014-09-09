define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/comments/comments',
        'models/comments/comment',
        'views/comments/item',
        'views/comments/conversation',
	'text!templates/comments/index.html'
        
], function (
        $,
        _,
        Backbone,
        app,
        Comments_collection,
        Comment_model,
        Comment_view,
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
            $(this.el).find(".conversations_container").append(
                new Conversation_view({
                    comment_options : this.options,
                    model : model
                }).render().el
            );
        },
        
        onClickCreateConversation : function() {
            var data = {
                business_profile_id : app.options.business_profile_id,
                created_by : app.options.user_id,
                created_by_name : app.options.user_name,
                created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss")
            };
            
            var model = new Comment_model(data, {db_table : this.options.db_table});
            
            this.addItem(model);
        }
     
    });
            
    return view;

});