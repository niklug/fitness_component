define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/nutrition_plan/macronutrients.html'
], function ( $, _, Backbone, app, template ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render: function(){
            var template = _.template( this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
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
            data.url = app.options.fitness_frontend_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id = $(event.target).attr('data-id');
            data.view = 'NutritionPlan';
            data.method = 'email_pdf_nutrition_plan_macros';
            $.fitness_helper.sendEmail(data);
        },
        
    });
            
    return view;
});