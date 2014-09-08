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
            app.collections.comments.fetch({
                success : function (collection, response) {
                    //console.log(collection.toJSON());
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
        
        addItem : function() {
            
        },
        
        onClickCreateConversation : function() {
            var data = {
                created_by : app.options.user_id,
                created_by_name : app.options.user_name,
                created : moment(new Date()).format("YYYY-MM-DD HH:mm:ss")
            };

            
            $(this.el).find(".conversations_container").append(
                new Conversation_view({
                    model : new Comment_model(data, {db_table : this.options.db_table})
                }).render().el
            );
        }
     
    });
            
    return view;

});