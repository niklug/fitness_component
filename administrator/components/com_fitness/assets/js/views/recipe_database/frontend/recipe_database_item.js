define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/comments/index',
	'text!templates/recipe_database/frontend/recipe_database_item.html',
        'jqueryui',
        'jquery.flot',
        'jquery.flot.pie',
        'jquery.drawPie'
        
], function ( $, _, Backbone, app, Comments_view, template ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            this.controller = app.routers.recipe_database;
        },

        template:_.template(template),
        
        render: function(){
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.onRender();
            
            return this;
        },

        events: {
            "click #pdf_button_recipe" : "onClickPdf",
            "click #email_button_recipe" : "onClickEmail",
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                self.connectComments();
                self.controller.connectStatus(self.model, $(self.el));
                self.setPieGraph();
            });
        },

        onClickPdf : function(event) {
            var id = $(event.target).attr('data-id');
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_recipe&id=' + id + '&client_id=' + app.options.client_id;
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
            data.method = 'email_pdf_recipe';
            $.fitness_helper.sendEmail(data);
        },
        
        connectComments :function() {
            var comment_options = {
                'item_id' :  this.model.get('id'),
                'item_model' :  this.model,
                'sub_item_id' :  '0',
                'db_table' : 'fitness_nutrition_recipes_comments',
                'read_only' : true,
                'anable_comment_email' : true,
                'comment_method' : 'RecipeComment'
            }

            var comments_html = new Comments_view(comment_options).render().el;
            $(this.el).find("#comments_wrapper").html(comments_html);
        },

        
        setPieGraph : function() {
            var data = [
                {label: "Protein:", data: [[1, this.model.get('protein')]]},
                {label: "Carbs:", data: [[1, this.model.get('carbs')]]},
                {label: "Fat:", data: [[1, this.model.get('fats')]]}
            ];

            var container = $(this.el).find("#placeholder_targets");

            var pie_graph = $.drawPie(data, container, {'no_percent_label' : false});
            
            pie_graph.draw(); 
        },

        
    });
            
    return view;
});