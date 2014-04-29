define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/programs/exercises/items',
        'models/programs/exercises/item', 
        'views/programs/exercises/list',
	'text!templates/programs/frontend/item.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Exercises_collection,
        Exercise_model,
        Exercises_list_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render : function () {
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));
            
            app.controller.connectStatus(this.model, this.$el);
            
            app.controller.connectComments(this.model, this.$el);
            
            this.connectExercises();
            
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
            data.task = 'ajax_email';

            data.id =  this.model.get('id');
            data.client_id =  app.options.user_id;
            data.view = 'Programs';
            data.method = 'Workout';
            $.fitness_helper.sendEmail(data);
        },

        connectExercises : function() {
            new Exercises_list_view({
                el : $(this.el).find("#exercises_list"),
                model : this.model,
                exercise_model : Exercise_model,
                exercises_collection : Exercises_collection,
                readonly : true,
                title : 'WORKOUT DETAILS'
            });
        }

    });
            
    return view;
});