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
        
        connectComments :function() {
            this.model.set({created_by : this.model.get('owner')});
            var comment_options = {
                'item_id' :  this.model.get('id'),
                'item_model' : this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_program_comments',
                'read_only' : this.options.read_only || false,
                'anable_comment_email' : true,
                'comment_method' : 'ProgramComment'
            }
            
            if(app.options.is_backend) {
                comment_options.read_only = false;
            }
            
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#comments_wrapper").html(comments_html);
        },
    });
            
    return view;
});