define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/backend/form_standard_assessment.html',
        'jquery.timepicker'
], function (
        $,
        _,
        Backbone,
        app,
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
            
            return this;
        },

    });
            
    return view;
});