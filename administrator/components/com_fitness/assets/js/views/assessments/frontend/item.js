define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'collections/assessments/exercises/items',
        'models/programs/exercises/item', 
        'views/programs/exercises/list',
        'views/assessments/frontend/form_video',
        'views/assessments/frontend/photo_block/list',
	'text!templates/assessments/frontend/item.html'
], function (
        $,
        _, 
        Backbone, 
        app,
        Exercises_collection,
        Exercise_model,
        Exercises_list_view,
        Form_video_view,
        Photo_block_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        template:_.template(template),
        
        render : function () {
            var data = this.model.toJSON();
            data.$ = $;
            data.app = app;
            $(this.el).html(this.template(data));

            this.onRender();
            
            return this;
        },
        
        events : {
            "click #pdf_button_standard" : "onClickPdf",
            "click #email_button_standard" : "onClickEmail"
        },
        
        onRender : function() {
            var self = this;
            $(this.el).show('0', function() {
                app.controller.connectStatus(self.model, self.$el);
                
                var frontend_published = self.model.get('frontend_published');
                if(parseInt(frontend_published)) {

                    app.controller.loadAssessmentsForm(self.model.get('session_focus_name'), self.model, {readonly : true});

                    self.connectExercises();

                    new Form_video_view({el : $("#video_block"), model : self.model, readonly : true});

                    new Photo_block_view({el : $("#photo_block"), model : self.model, readonly : true});

                    app.controller.connectComments(self.model, self.$el);
                }
            });
        },
        
        onClickPdf : function() {
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_a_standard&event_id=' + this.model.get('id') + '&client_id=' + app.options.user_id;
            $.fitness_helper.printPage(htmlPage);
        },
        
        onClickEmail : function() {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.task = 'ajax_email';

            data.id =  this.model.get('id');
            data.client_id =  app.options.user_id;
            data.view = 'Programs';
            data.method = 'AssessmentStandard';
            $.fitness_helper.sendEmail(data);
        },

        connectExercises : function() {
            new Exercises_list_view({
                el : $(this.el).find("#exercises_list"),
                model : this.model,
                exercise_model : Exercise_model,
                exercises_collection : Exercises_collection,
                readonly : true,
                title : 'PHYSICAL ASSESSMENT DETAILS'
            });
        },

    });
            
    return view;
});