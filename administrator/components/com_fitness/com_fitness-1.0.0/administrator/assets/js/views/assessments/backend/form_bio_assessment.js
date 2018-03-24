define([
	'jquery',
	'underscore',
	'backbone',
        'app',
        'views/programs/select_element',
	'text!templates/assessments/backend/form_bio_assessment.html',
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

            
            return this;
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
            
            new Select_element_view({
                model : this.model,
                el : target,
                collection : collection,
                first_option_title : '-Select-',
                class_name : ' required',
                id_name : name,
                model_field : name
            }).render();
        }

    });
            
    return view;
});