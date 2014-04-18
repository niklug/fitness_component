define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/backend/comments_block.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        initialize : function() {
            this.render();
        },
        
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            this.connectComments();
            return this;
        },
        
        connectComments : function() {
            var comment_options = {
                'item_id' : this.model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' : '#__fitness_pr_temp_comments',
                'read_only' : this.options.read_only || false,
                'anable_comment_email' : true,
                'comment_method' : 'ProgramTemplateComment'
            }
            var comments = $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            this.$el.find("#comments_wrapper").html(comments_html);
        },
    });
            
    return view;
});