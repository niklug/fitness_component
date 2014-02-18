define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/exercise_library/frontend/item.html'
], function (
        $,
        _, 
        Backbone, 
        app,
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
        
        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_exercise_library_comments',
                'read_only' : true,
                'anable_comment_email' : false
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $(this.el).find("#comments_wrapper").html(comments_html);
        },

    });
            
    return view;
});