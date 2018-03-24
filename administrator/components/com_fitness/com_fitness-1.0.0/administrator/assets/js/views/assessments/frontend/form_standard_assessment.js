define([
	'jquery',
	'underscore',
	'backbone',
        'app',
	'text!templates/assessments/frontend/form_standard_assessment.html',
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
            
            data.$ = $;
            var template = _.template(this.template(data));
            this.$el.html(template);
            
            var readonly = this.options.readonly || false;
            
            if(readonly) {
                $(this.el).find("input[type='text']").attr('disabled', true);
            }
            
            return this;
        },

    });
            
    return view;
});