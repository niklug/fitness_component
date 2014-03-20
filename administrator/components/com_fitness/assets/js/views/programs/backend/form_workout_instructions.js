define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/programs/backend/form_workout_instructions.html'
], function (
        $,
        _,
        Backbone,
        app,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
           this.render();
        },

        
        template:_.template(template),
        
        render: function(){
            var template = _.template(this.template(this.model.toJSON()));
            this.$el.html(template);
            return this;
        },
        
        events : {
            "click #pdf_button" : "onClickPdf",
            "click #email_button" : "onClickEmail"
        },
        
        onClickPdf : function() {
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_workout&event_id=' + this.model.get('id') + '&client_id=' + app.options.user_id;
            $.fitness_helper.printPage(htmlPage);
        },
        
        onClickEmail : function() {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.view = '';
            data.task = 'ajax_email';
            data.table = '';

            data.id =  this.model.get('id');
            data.view = 'Programs';
            data.method = 'Workout';
            $.fitness_helper.sendEmail(data);
        }

    });
            
    return view;
});