define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/comments/comments',
        'models/comments/comment',
        'views/comments/item',
	'text!templates/comments/index.html'
        
], function (
        $,
        _,
        Backbone,
        app,
        Comments_collection,
        Comment_model,
        Comment_view,
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
            "click .create_comment" : "onClickCreateComment",
        },
        
        addItem : function() {
            
        },
        
        onClickCreateComment : function() {
            $(this.el).find(".comments_wrapper").append(
                new Comment_view({
                    model : new Comment_model({}, {db_table : this.options.db_table})
                }).render().el
            );
        }
     
    });
            
    return view;

});