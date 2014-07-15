define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/backend/targets/step1',
	'text!templates/nutrition_plan/backend/targets/targets_container.html'
], function (
        $,
        _, 
        Backbone,
        app,
        Step1_view,
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
            app.views.targets_step1 = new Step1_view({model : this.model});
            $(this.el).find("#step1_wrapper").html(app.views.targets_step1.render().el);
        },
        
        connectComments : function() {
            // connect comments
             var comment_options = {
                'item_id' :  this.options.item_model.get('id'),
                'fitness_administration_url' : app.options.ajax_call_url,
                'comment_obj' : {'user_name' : app.options.user_name, 'created' : "", 'comment' : ""},
                'db_table' :  '#__fitness_nutrition_plan_targets_comments',
                'read_only' : false,
                'anable_comment_email' : true,
                'comment_method' : 'TargetsComment'
            }
            var comments =  $.comments(comment_options, comment_options.item_id, 0);

            var comments_html = comments.run();
            $(this.el).find("#targets_comments_wrapper").html(comments_html);
        }

   
    });
            
    return view;
});