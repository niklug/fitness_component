define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/comments/index',
	'text!templates/exercise_library/frontend/item.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Comments_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render : function () {
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            this.connectComments();
            
            return this;
        },

        connectComments :function() {
            var comment_options = {
                'item_id' :  this.model.get('id'),
                'item_model' :  this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_exercise_library_comments',
                'read_only' : true,
                'anable_comment_email' : true,
                'comment_method' : 'ExerciseLibraryComment'
            }

            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#comments_wrapper").html(comments_html);
        },

    });
            
    return view;
});