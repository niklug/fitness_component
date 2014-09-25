define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step1',
        'views/comments/index',
	'text!templates/nutrition_plan/backend/targets/targets_container.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step1_view,
        Comments_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
            
        render: function(){
            var template = _.template(this.template());
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.loadStep1();
                self.connectComments();
            });
        },
        
        loadStep1 : function() {
            //console.log(this.options.item_model);
            app.views.targets_step1 = new Step1_view({model : this.model, item_model : this.options.item_model});
            $(this.el).find("#step1_wrapper").html(app.views.targets_step1.render().el);
        },
        
        connectComments :function() {
            this.model.set({created_by_client : this.options.item_model.get('client_id')});
            var comment_options = {
                'item_id' :  this.options.item_model.get('id'),
                'item_model' :  this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_nutrition_plan_targets_comments',
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'TargetsComment'
            }
            
            if(app.options.is_backend) {
                comment_options.read_only = false;
            }
            
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#targets_comments_wrapper").html(comments_html);
        },

   
    });
            
    return view;
});