define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/nutrition_plan/supplements/frontend/protocol',
	'text!templates/nutrition_plan/supplements/frontend/supplements.html'
], function ( $, _, Backbone, app, Protocol_view, template ) {

     var view = Backbone.View.extend({
         
        template:_.template(template),
           
        render: function(){
            this.nutrition_plan_id = this.model.get('id');
            var template = _.template(this.template({'id' : this.nutrition_plan_id}));
            this.$el.html(template);

            var self = this;

            this.protocolListItemViews = {};

            this.collection.on("add", function(protocol) {
                app.views.protocol = new Protocol_view({collection : this,  model : protocol}); 
                self.$el.find(".protocols_wrapper").append( app.views.protocol.render().el );
            });

            return this;
        },

        events: {
            "click #pdf_button_supplements" : "onClickPdf",
            "click #email_button_supplements" : "onClickEmail",
        },
        
        onClickPdf : function(event) {
            var id = $(event.target).attr('data-id');
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_nutrition_plan_supplements&id=' + id + '&client_id=' + this.model.get('client_id');
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
            data.method = 'email_pdf_nutrition_plan_supplements';
            $.fitness_helper.sendEmail(data);
        },
    });
            
    return view;
});