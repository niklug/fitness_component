define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/assessments/frontend/form_bio_assessment.html',
        'jquery.timepicker'
], function (
        $,
        _,
        Backbone,
        app,
        Select_element_view,
        template
    ) {

    var view = Backbone.View.extend({
        
        initialize : function() {
            
        },

        
        template:_.template(template),
        
        render: function(){
            var data = {data : this.model.toJSON()};
            //console.log(data);
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            this.prioritySelect('priority_1');
            this.prioritySelect('priority_2');
            this.prioritySelect('priority_3');
            
            var readonly = this.options.readonly || false;
            
            if(readonly) {
                $(this.el).find("input[type='text']").attr('readonly', true);
                $(this.el).find("select").attr('disabled', true);
            }

            
            return this;
        },
        
        events : {
            "click #pdf_button_bio" : "onClickPdf",
            "click #email_button_bio" : "onClickEmail"
        },
        
        prioritySelect : function(name) {
            var target = $(this.el).find("#" + name);
            
            var collection = new Backbone.Collection;
            
            collection.add([
              {id : '1', name: "CHIN"},
              {id : '2', name: "CHEEK"},
              {id : '3', name: "PECTORAL"},
              {id : '4', name: "TRICEPS"},
              {id : '5', name: "SUB-SCAPULARIS"},
              {id : '6', name: "MID-AXILLARY"},
              {id : '7', name: "SUPRAILLIAC"},
              {id : '8', name: "UMBILICAL"},
              {id : '9', name: "KNEE"},
              {id : '10', name: "CALF"},
              {id : '11', name: "QUADRICEPS"},
              {id : '12', name: "HAMSTRINGS"},
              
            ]);
            
            var id = this.model.get(name);
            
            var model = collection.get(id);
            
            var name = model.get('name');
            
            target.html('<span style="color:red;">' + name + '</span>');
        },
        
        onClickPdf : function() {
            var htmlPage = app.options.base_url + 'index.php?option=com_multicalendar&view=pdf&tpml=component&layout=email_pdf_a_bio&event_id=' + this.model.get('id') + '&client_id=' + app.options.user_id;
            $.fitness_helper.printPage(htmlPage);
        },
        
        onClickEmail : function() {
            var data = {};
            data.url = app.options.ajax_call_url;
            data.task = 'ajax_email';

            data.id =  this.model.get('id');
            data.client_id =  app.options.user_id;
            data.view = 'Programs';
            data.method = 'AssessmentBio';
            $.fitness_helper.sendEmail(data);
        },


    });
            
    return view;
});