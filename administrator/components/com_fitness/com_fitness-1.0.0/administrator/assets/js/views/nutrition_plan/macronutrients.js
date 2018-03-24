define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/comments/index',
	'text!templates/nutrition_plan/macronutrients.html'
], function ( $, _, Backbone, app, Comments_view, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            this.onRender();
            return this;
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectComments();
            });
        },

        events: {
            "click #pdf_button_macros" : "onClickPdf",
            "click #email_button_macros" : "onClickEmail",
        },

        onClickPdf : function(event) {
            var id = $(event.target).attr('data-id');
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_plan_macros&id=' + id + '&client_id=' + app.options.client_id;
            $.fitness_helper.printPage(htmlPage);
        },

        onClickEmail : function(event) {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = $(event.target).attr('data-id');
            data.view = 'NutritionPlan';
            data.method = 'email_pdf_nutrition_plan_macros';
            $.fitness_helper.sendEmail(data);
        },
        
        connectComments :function() {
            
            var comment_options = {
                'item_id' :  this.model.get('id'),
                'item_model' : this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_nutrition_plan_macronutrients_comments',
                'read_only' : true,
                'anable_comment_email' : true,
                'comment_method' : 'MacrosComment'
            }
            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#macronutrients_comments_wrapper").html(comments_html);
        },
        
    });
            
    return view;
});